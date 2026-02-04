<?php
declare (strict_types=1);

namespace woo\common\builder;

use Countable;
use IteratorAggregate;
use think\facade\Db;
use woo\common\builder\form\Collection;
use woo\common\facade\Auth;
use think\facade\Cache;
use woo\common\helper\Arr;
use woo\common\helper\Str;
use think\Exception;

class Form implements Countable, IteratorAggregate
{
    use \woo\common\builder\form\traits\AttrValues;
    use \woo\common\builder\form\traits\ItemAttrs;
    use \woo\common\builder\form\traits\AfterParse;


    protected $request;

    protected $view;

    /**
     * 表单集合
     */
    protected $collection;

    /**
     * 当前表单名称
     */
    protected $name;

    /**
     * 对应模型名称
     */
    protected $model;

    protected $parentField;

    /**
     * 表单值存储
     */
    protected $data = [];

    protected $dataKey = null;

    /**
     * 模板对象
     */
    private $template;

    /**
     * 当前操作的表单项
     */
    protected $currentItem;

    /**
     * 不可见字段
     */
    protected $hiddenItems = [];

    /**
     * 可见字段
     */
    protected $visibleItems = [];

    protected $labelClass = '';

    protected $csrfName = '__token__';

    /**
     * 错误信息
     * @var array
     */
    protected $error = [];
    /**
     * 错误信息划分为可见字段错误信息和不可见
     * @var array
     */
    protected $errorDivide = [];

    protected $groupFields = [];

    protected $together = [];

    protected $config = [];

    /**
     * @var bool 是否临时保存 默认false 如果为true将不进行数据验证 直接保存数据
     */
    protected $draftSave = false;

    /**
     * Form constructor.
     * @param array $data
     * @param null $model
     * @throws Exception
     */
    public function __construct($data = [], $model = null, $parentField = null)
    {
        if (empty($this->name)) {
            $name = str_replace('\\', '/', static::class);
            $this->name = basename($name);
        }
        // 对象
        $this->request = app()->request;
        $this->view = app(\woo\common\View::class);
        $this->template = app('\think\Template');
        $this->template->config([
            'view_path' => '',
            'cache_path' => app()->getRuntimePath() . 'form' . DIRECTORY_SEPARATOR,
            'view_suffix' => ''
        ]);
        $this->collection = new Collection();
        if ($model) {
            $this->setModel($model);
        }
        if ($parentField) {
            $this->parentField = $parentField;
        }

        //$this->setElementListValue();
        $this->setParseMethodValue();
        $this->setDefaultOptionsValue();
        $this->setHiddenElementListValue();
        $this->setDefaultAttrs();
        $this->initialize();

        if ($this->request->isPost() || $this->request->isPut()) {
            $this->data = array_merge($this->getDataCache() ??[], $this->data, $this->request->post());
        } else {
            $this->setData($data);
        }
    }

    /**
     * 初始化方法
     */
    protected function initialize()
    {
    }

    /**
     * 设置当前表单操作的模型
     * @param $model
     * @return $this
     * @throws Exception
     */
    public function setModel($model)
    {
        if (empty($model)) {
            $params = $this->request->getParams();
            $model =  $params['addon_name']
                ? $params['addon_name'] . '.' . $this->request->getParams()['controller']
                : $this->request->getParams()['controller'];
        }
        if ($model && is_string($model)) {
            $model = model($model);
        }
        if ($model && !($model instanceof \think\Model)) {
            $this->forceError('错误的模型对象');
            return $this;
        }
        if ($model) {
            $this->model = $model;
        }
        return $this;
    }

    /**
     * 设置临时保存
     * @param bool $is
     * @return $this
     */
    public function setDraftSave(bool $is)
    {
        $this->draftSave = $is;
        return $this;
    }

    /**
     * 获取模型
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    public function setConfig(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 自动保存保存
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        // 额外添加或覆盖部分数据
        if (!empty($options['forceData'])) {
            $this->data = array_merge($this->data, (array) $options['forceData']);
        }
        // 可以通过subversionData 完全重置数据 具体保存什么数据你说了算
        if (!empty($options['subversionData'])) {
            $this->data = $options['subversionData'];
        }
        // 强制添加错误信息
        if (!empty($options['forceError'])) {
            $this->forceError($options['forceError']);
            return false;
        }

        if (!$this->csrfCheck()) {
            $this->forceError('CSRF验证失败，请重新提交');
            return false;
        }

        if (empty($options['id'])) {
            $sence = 'add';
        } else {
            $sence = 'edit';
        }

        $validate = [];
        $fieldsName = [];
        $is_validate = !($this->draftSave && !empty($this->data['is_draft_save']));

        foreach ($this->collection as $field => $item88) {
            if (!empty($item88['label'])) {
                $fieldsName[$field] = $item88['label'];
            }
            if (!empty($item88['validate'])) {
                $validate[$field] = $item88['validate'];
            }
        }
        // 关联写入数据
        $togetherSave = [];
        $notSave = false;
        if ($this->together) {
            $login = Auth::user();
            foreach ($this->together as $field => $info) {
                if (!is_array($this->data[$field]) && is_json($this->data[$field])) {
                    $this->data[$field] = json_decode($this->data[$field], true);
                }
                if (empty($this->data[$field])) {
                    continue;
                }
                $togetherData = [];
                // 数据处理
                $checkError = [];

                foreach ($this->data[$field] as $index => $item) {
                    // 添加的时候必须去主键字段 否则TP底层会认为是修改
                    $s = $sence;
                    $foreignModel = model($info['foreign'], '', true);
                    if (isset($item[$info['pk']]) && empty($item[$info['pk']])) {
                        unset($item[$info['pk']]);
                    }
                    if ($sence == 'edit' && empty($item[$info['pk']])) {
                        // 修改的时候新增关联数据
                        $s = 'add';
                    }
                    if ($s == 'edit') {
                        $foreignModel = $foreignModel->find($item[$info['pk']]);
                        if (!$foreignModel) {
                            continue;
                        }
                        $item = array_merge($foreignModel->getData(), $item);
                    }
                    //添加的时候 会自动加上admin_id的值
                    if ($login &&  isset($login['login_foreign_key']) && $sence == 'add') {
                        $login_foreign_key = $login['login_foreign_key'];
                        if (isset($foreignModel->form[$login_foreign_key])) {
                            $item[$login_foreign_key] = $login['login_foreign_value'];
                        }
                    }
                    // 关联写入的值 没法单独传入 所以添加之前有值需要处理的 在 模型方法setTogetherItem中自行处理以后并返回
                    if (method_exists($foreignModel, 'setTogetherItem')) {
                        $item = $foreignModel->setTogetherItem($item, $sence);
                    }
                    if (!empty($item)) {
                        // 提前手动验证  不然必须就只能主表验证通过以后 并插入数据成功以后才会自动验证子表数据
                        $is = $foreignModel->isValidate($is_validate)->setValidate('scene', $s)->validate($item);
                        if (!$is) {
                            $checkError[$index] = $foreignModel->getError();
                        }
                        // 20230412 给模型设置数据并保存
                        $foreignModel->setAttrs($item);
                        $togetherData[$index] = $foreignModel;
                    }
                }
                if ($togetherData) {
                    $togetherSave[$field] = $togetherData;
                }
                if ($checkError) {
                    $this->forceError($field, $checkError);
                    $notSave = true;
                }
            }
        }

        if (empty($options['id'])) {
            if (false === $notSave) {
                Db::startTrans();
                $result = $this->model
                    ->setValidate('mergeRule', $validate)
                    ->setValidate('fieldsName', $fieldsName)
                    ->isValidate($is_validate)
                    ->createData($this->data, $options);
            } else {
                $result = false;
                $is = $this->model
                    ->setValidate('mergeRule', $validate)
                    ->setValidate('fieldsName', $fieldsName)
                    ->setValidate('scene', $sence)
                    ->validate($this->data);
                if (!$is) {
                    $this->forceError($this->model->getError());
                }
            }
        } else {
            if (!empty($this->data[$this->model->getPk()]) && !empty($options['id']) && $this->data[$this->model->getPk()] != $options['id']) {
                $this->forceError('主键值不一致，操作失败，请刷新以后再试');
                return false;
            }
            $id = !empty($options['id']) ? intval($options['id']) : intval($this->data[$this->model->getPk()]);
            $this->model = $this->model->find($id);

            if (empty($this->model)) {
                $this->forceError('ID：' . $id .'的数据已经不存在，修改失败');
                return false;
            }

            if (false === $notSave) {
                Db::startTrans();
                $result = $this->model
                    ->setValidate('mergeRule', $validate)
                    ->setValidate('fieldsName', $fieldsName)
                    ->isValidate($is_validate)
                    ->modifyData($this->data, $options);
            } else {
                $result = false;
                $is = $this->model
                    ->setValidate('mergeRule', $validate)
                    ->setValidate('fieldsName', $fieldsName)
                    ->setValidate('scene', $sence)
                    ->validate(array_merge($this->model->getData(), $this->data));
                if (!$is) {
                    $this->forceError($this->model->getError());
                }
            }
        }

        // 关联写入
        if ($result && $togetherSave) {
            foreach ($togetherSave as $field => $saveData) {
                $info = $this->together[$field];
                $errors = [];
                foreach ($saveData as $index => $itemModel) {
                    //添加关联字段值
                    if ($itemModel->getAttr($info['foreign_key']) && $itemModel->getAttr($info['foreign_key']) != $result) {
                        continue;
                    }
                    $itemModel->setAttr($info['foreign_key'], $result);
                    $re = $itemModel->isValidate(false)->save();
                    if (!$re) {
                        $errors[$index] = $itemModel->getError();
                    }
                }
                if ($errors) {
                    $this->forceError($field, $errors);
                }
            }
            if ($this->getError()) {
                Db::rollback();
                $this->assignError();
                return false;
            }
        }
        if (false === $notSave) {
            Db::commit();
        }
        if (!$result) {
            $this->forceError($this->model->getError());
            $this->assignError();
            return false;
        }
        $this->data = !empty($this->model->getData()) ? $this->model->getData(): $this->data;
        $this->removeDataCache();
        return $this->model;
    }

    // 强制更新html 传入错误信息
    protected function assignError()
    {
        foreach ($this->getError() as $field => $error) {
            if ($this->collection->getItemAttr($field))
                $this->collection->setItemAttr($field, 'html', $this->getHtmlAttr($this->collection->getItemAttr($field), true));
        }
    }


    /**
     * CSRF验证
     * @return bool
     */
    public function csrfCheck()
    {
        if (false === $this->csrfName) {
            return true;
        }
        $name = is_string($this->csrfName) ? $this->csrfName : '__token__';
        return $this->request->checkToken($name);
    }

    /**
     * 设置CSRF 令牌的name值  默认__token__ 如果不需要验证 设置为false
     * @param $name string|bool
     */
    public function setCsrf($name)
    {
        $this->csrfName = $name;
    }

    public function setName($name)
    {
        $this->name = trim(Str::studly($name));
    }

    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * 切换当前操作的表单项
     * @param string $item
     * @return $this
     */
    public function switchItem(string $field)
    {
        $this->currentItem = $field;
        return $this;
    }


    /**
     * 添加一个表单项
     * @param string $field
     * @param string $element
     * @param array $options
     * @return $this
     * @throws Exception
     */
    public function addFormItem(string $field, $element = 'text', array $options = [])
    {
        $element = trim(strval($element));

        if ($this->collection->itemExists($field)) {
            throw new Exception('item exists:' . $field);
        }
        $this->switchItem($field);
        if ($this->collection->itemExists($field)) {
            return $this;
        }
        $options['attrs'] = Arr::deepMerge(
            $this->defaultAttrs['_'] ?? [],
            $this->defaultAttrs[$element] ?? [],
            $options['attrs'] ?? []
        );

        $options = array_merge(
            [
                'field_name' => $field,
                'real_field_name' => $field,
                'element' => $element,
                'current_model' => $this->model ? get_base_class($this->model) : '',
                'current_model_pk' => $this->model ? $this->model->getPk() : 'id',
            ],
            isset($this->defaultOptions['_']) ? $this->defaultOptions['_'] : [],
            !empty($this->defaultOptions[$element]) ? $this->defaultOptions[$element] : [],
            $options
        );
        $this->collection->push($options, $field);
        // 初始值
        if (isset($options['attrs']['value'])) {
            $this->setItemValue($field, $options['attrs']['value']);
        }
        if (in_array($element, ['together', 'orderitem', 'together2'])) {
            $this->getTogetherInfo($options);
        }
        return $this;
    }

    /**
     * 删除一个表单项
     * @param string $field
     * @return $this|array
     */
    public function removeFormItem(string $field)
    {
        $this->collection->remove($field);
        return $this;
    }

    /**
     * 表单项赋值
     * @param string $field
     * @param string $value
     * @return $this|bool
     */
    public function setItemValue($field = '', $value = '')
    {
        if (count(func_get_args()) <= 1) {
            $value = $field;
            $field = $this->currentItem;
        }
        if (empty($field)) {
            $field = $this->currentItem;
        }
        if (!$this->collection->itemExists($field)) {
            return false;
        }
        $key = $field;
        if (($this->request->isPost() || $this->request->isPut()) && isset($this->data[$key])) {
            $value = $this->data[$key];
        }
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 删除某个表单项的值
     * @param $field
     * @return $this
     */
    public function removeItemValue($field)
    {
        if (isset($this->data[$field])) {
            unset($this->data[$field]);
        }
        return $this;
    }

    /**
     * 批量赋值
     * @param array $data
     * @return $this
     */
    public function setData(array $data = [])
    {
        $this->data = array_merge($data, $this->data);
        foreach ($data as $field => $value) {
            $this->setItemValue($field, $value);
        }
        if (!empty($this->data) && $this->request->isGet()) {
            $this->setDataCache();
        }
        return $this;
    }

    public function setDataKey(string $key)
    {
        $this->dataKey = $key;
        return $this;
    }

    protected function setDataCache()
    {
        if (empty($this->data)) {
            return $this;
        }
        $this->dataKey = $this->dataKey ?? $this->request->url();
        Cache::set($this->dataKey, $this->data, 3600);
        return $this;
    }

    protected function getDataCache()
    {
        $this->dataKey = $this->dataKey ?? $this->request->url();
        return Cache::get($this->dataKey);
    }

    protected function removeDataCache()
    {
        $this->dataKey = $this->dataKey ?? $this->request->url();
        Cache::delete($this->dataKey);
        return $this;
    }

    /**
     * 添加CSRF表单项
     * @throws Exception
     */
    protected function addCsrf()
    {
        if (false === $this->csrfName) {
            return;
        }
        $name = is_string($this->csrfName) ? $this->csrfName : '__token__';
        if (!$this->collection->itemExists($name)) {
            $this->addFormItem($name, 'csrf');
        }
    }

    /**
     * 获取模型form属性自动创建表单项目
     * @param array $form
     * @param array $fields_list
     * @return $this
     * @throws Exception
     */
    public function createFormItem(array $form = [], array $fields_list = [])
    {
        if ($this->collection->count()) {
            return $this;
        }
        if (empty($form) && $this->model) {
            $form = $this->model->form;
        }
        if (!empty($form)) {
            $fields = empty($fields_list) ? array_keys($form) : $fields_list;
            foreach ($fields as $field) {
                $item = $form[$field];
                $this->groupFields[$item['form_group'] ?? 'basic'][] = $field;
                $options = array_diff_key($item, Arr::normalize([
                    'elem', 'name', 'type', 'list', 'detail', 'form_group', 'filter', 'modify_elem', 'join', 'rsa'
                ]));
                if (!in_array(app('http')->getName(), ['admin', 'business']) && isset($options['is_contribute']) && !$options['is_contribute']) {
                    continue;
                }
                $this->addFormItem($field, $item['elem'] ?? 0, $options)->setLabelAttr($item['label'] ?? ($item['name'] ?? $field));
            }
        }
        return $this;
    }


    /**
     * 解析处理每个表单项
     * @return $this
     * @throws Exception
     */
    public function parseItems()
    {
        $this->createFormItem();
        $this->addCsrf();
        $items = $this->collection->keys()->toArray();
        foreach ($items as $field) {
            $this->parseItem($field);
        }
        return $this;
    }

    /**
     * 解析处理指定字段名的表单项
     * @param string $field
     * @param bool $force
     * @return array|null
     */
    public function parseItem(string $field, bool $force = false)
    {
        if (false === $force && true === $this->collection->getItemAttr($field, 'is_parsed')) {
            return $this->collection->getItemAttr($field);
        }
        $element = $this->collection->getItemAttr($field, 'element');
        if (method_exists($this, $element . 'BeforeParse')) {
            $options = $this->collection->getItemAttr($field);
            $result = $this->{$element . 'BeforeParse'}($options);
            $this->collection->setItemAttr($field, array_merge($options, $result));
        }

        $methods = array_merge(
            Arr::normalize($this->parseMethod['_']),
            Arr::normalize(isset($this->parseMethod[$element]) ? $this->parseMethod[$element] : [])
        );
        foreach ($methods as $attr => $info) {
            if (false === $info) {
                continue;
            }
            if (is_array($info)) {
                if (array_key_exists('only', $info) && !in_array($element, (array)$info['only'])) {
                    continue;
                }
                if (array_key_exists('except', $info) && in_array($element, (array)$info['except'])) {
                    continue;
                }
                $info = null;
            }
            if (is_null($info) && method_exists($this, 'get' . Str::studly($attr) . 'Attr')) {
                $this->collection->setItemAttr(
                    $field,
                    $attr,
                    $this->{'get' . Str::studly($attr) . 'Attr'}($this->collection->getItemAttr($field))
                );
                continue;
            }
            if (is_string($info)) {
                if (method_exists($this, $info)) {
                    $this->collection->setItemAttr(
                        $field,
                        $attr,
                        $this->$info($this->collection->getItemAttr($field))
                    );
                } else {
                    $this->collection->setItemAttr($field, $attr, $info);
                }
                continue;
            }
            if (is_callable($info)) {
                $this->collection->setItemAttr(
                    $field,
                    $attr,
                    call_user_func_array($info, [$this->collection->getItemAttr($field)])
                );
                continue;
            }
        }

        // 每种表单类型都可以有直接的parse回调 来最终处理该类型的特殊情况 命名：类型AfterParse 参数为当前表单项目的相关信息
        if (method_exists($this, $element . 'AfterParse')) {
            $options = $this->collection->getItemAttr($field);
            $result = $this->{$element . 'AfterParse'}($options);
            $this->collection->setItemAttr($field, array_merge($options, $result));
        }

        $callback = $this->collection->getItemAttr($field, 'callable');
        if (is_callable($callback)) {
            $this->collection->setItemAttr($field, call_user_func_array($callback, [$this->collection->getItemAttr($field)]));
        }

        // 标注为已解析 防止重复解析
        $this->collection->setItemAttr($field, 'is_parsed', true);

        // 最后获取表单HTML结构
        $this->collection->setItemAttr($field, 'html', $this->getHtmlAttr($this->collection->getItemAttr($field)));

        return $this->collection->getItemAttr($field);
    }

    /**
     * 获取隐藏的表单项目
     */
    public function getHiddenItems()
    {
        $this->parseItems();
        $data = [];
        foreach ($this->hiddenItems as $item) {
            $data[$item] = $this->collection->getItemAttr($item);
        }
        return $data;
    }

    /**
     * 获取可见的表单项目
     */
    public function getVisibleItems()
    {
        $this->parseItems();
        $data = [];
        foreach ($this->visibleItems as $item) {
            $data[$item] = $this->collection->getItemAttr($item);
        }
        return $data;
    }

    protected function divideError()
    {
        if (!empty($this->errorDivide) || empty($this->error)) {
            return;
        }
        $visible = array_keys($this->getVisibleItems());
        foreach ($this->error as $field => $error) {
            if (in_array($field, $visible, true)) {
                $this->errorDivide['visible'][$field] = $error;
            } else {
                $this->errorDivide['hidden'][$field] = $error;
            }
        }
    }

    /**
     * 获取可见字段的错误信息
     * @param string $field  指定具体字段的错误信息
     * @return mixed|string
     */
    public function getVisibleError($field = '')
    {
        $this->divideError();
        if ($field) {
            return $this->errorDivide['visible'][$field] ?? '';
        }
        return $this->errorDivide['visible'] ?? [];
    }

    /**
     *  获取不可见字段的错误信息
     * @param string $field
     * @return array|mixed|string
     */
    public function getHiddenError($field = '')
    {
        $this->divideError();
        if ($field) {
            return $this->errorDivide['hidden'][$field] ?? '';
        }
        return $this->errorDivide['hidden'] ?? [];
    }


    /**
     * 加设错误信息
     * @param $field
     * @param string $error
     * @return $this
     */
    public function forceError($field, $error = '')
    {
        if (is_string($field)) {
            if (!empty($error)) {
                $this->error[$field] = $error;
            } else {
                $this->error[] = $field;
            }
        } elseif (is_array($field)) {
            $this->error = array_merge($this->error, $field);
        }
        return $this;
    }

    /**
     * 获取错误信息
     * @param string $field
     * @return array|mixed|string
     */
    public function getError(string $field = '')
    {
        if ($field) {
            return $this->error[$field] ?? '';
        }
        return $this->error;

    }

    public function fetch($template, $data = [], $trim = true)
    {
        ob_start();
        $assign = get_object_vars($this->view);
        $this->template->fetch($template, array_merge((array)$assign, ['data' => $data, 'values' => $this->data]));
        $content = ob_get_contents();
        if ($trim) {
            $content = preg_replace_callback("/<[^<]*?>/is", function ($matched) {
                return preg_replace("/\s*" . PHP_EOL . "\s*/is", " ", $matched[0]);
            }, $content);
        }
        ob_end_clean();
        return $content;
    }

    public function __call($name, $args)
    {
        if (substr($name, 0, 3) == 'set' && substr($name, -4) == 'Attr' && $name != 'setItemAttr') {
            $name = Str::snake(substr($name, 3, -4));
            if (in_array($name, ['field_name', 'element'])) {
                return $this;
            }
            $this->collection->setItemAttr($this->currentItem, $name, ...$args);
            return $this;
        } elseif (method_exists($this->collection, $name)) {
            var_dump($name);
            $result = call_user_func_array([$this->collection, $name], $args);
            if ($result instanceof Collection) {
                $this->collection = $result;
                return $this;
            }
            return $result;
        }
    }

    /**
     * 获取内置表单渲染
     * @param string $layout
     * @return string|string[]|null
     */
    public function getLayout(string $layout = '')
    {
        $this->parseItems();
        if (empty($layout)) {
            $layout = 'simple';
        }
        $origin = $layout;

        if (!is_file($layout)) {
            $layout = app()->getAppPath() . 'view' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . $origin . '.html';
        }

        if (!is_file($layout)) {
            $layout = app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . $origin . '.html';
        }

        $default = woo_path() . 'common' . DIRECTORY_SEPARATOR . 'builder' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . $origin . '.html';
        if (!is_file($layout) && is_file($default)) {
            $layout = $default;
        }
        return $this->fetch($layout, $this, false);
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
    }

    public function __toString()
    {
        return $this->getLayout();
    }

    public function __invoke(string $layout = '')
    {
        return $this->getLayout($layout);
    }

//    public function __debugInfo()
//    {
//        return $this->collection->toArray();
//    }

    //Countable
    #[\ReturnTypeWillChange]
    public function count()
    {
        return $this->collection->count();
    }

    //IteratorAggregate
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    //JsonSerializable
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->parseItems()->toArray();
    }
}