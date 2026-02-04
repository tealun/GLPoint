<?php
declare (strict_types=1);

namespace woo\common;

use think\Exception;
// use think\Model as ThinkModel;
use think\model\Pivot;
use woo\common\helper\Arr;
use woo\common\helper\Str;
use think\facade\Db;

class Model extends Pivot
{
    use \woo\common\model\traits\Relation;
    use \woo\common\model\traits\Event;
    use \woo\common\model\traits\Curd;
    use \woo\common\model\traits\Validate;
    use \woo\common\model\traits\Tree;
    use \woo\common\model\traits\Scope;
    use \think\model\concern\SoftDelete;
    use \woo\common\model\traits\DataAllow;

    /**
     * 模型id
     * @var int
     */
    protected $modelId = 0;

    /**
     * 定义模型关联
     */
    public $relationLink = [];

    /**
     * 定义模型表单项目 要动态操作都操作form接口
     * @var array
     */
    public $form = [];
    public $businessForm = [];
    public $formScene = [];
    /**
     * 需要rsa加密字段
     * @var array
     */
    public $rsaFields = [];
    /**
     * 模型字段管理中设置的字段结构数据
     * @var array
     */
    public $tableColumns = [];

    /**
     * 模型表单场景配置 20230124新增
     * @var array
     */
    public $FormScene = [];

    /**
     *  定义模型表单分组 暂时没有分中台、后台 以后考虑
     * @var array
     */
    public  $formGroup = [];

    /**
     * 定义模型表单触发器 用于定义根据某个字段的值不同 切换不同表单项的显示与否
     * @var array
     */
    public $formTrigger = [];
    public $businessFormTrigger = [];
    /**
     * 表格
     * @var array
     */
    public $tableTab = [];

    /**
     * 后台临时 tableTab属性
     * @var array
     */
    public $adminCustomTab = [];
    public $businessCustomTab = [];

    /**
     * 父模型名
     */
    public $parentModel = null;

    /**
     * 模型中文名
     */
    public $cname = '';

    /**
     * 主要展示字段
     */
    public $display = 'id';

    /**
     * 默认的排序方式
     * @var string|array
     */
    public $orderType;

    /** 列表是否开启拖拽排序 需要有list_order字段有效 */
    public $sortable = false;
    public $businessSortable = false;

    /**
     * 错误信息
     * @var array
     */
    protected $error = [];

    /**
     * 开启自动时间戳
     * @var string
     */
    protected $autoWriteTimestamp = 'int';
    /**
     * 创建日期时间戳字段  默认false 系统自动根据form属性识别create_time并开启
     * @var bool
     */
    protected $createTime = false;
    /**
     * 修改日期时间戳字段  默认false 系统自动根据form属性识别update_time并开启
     * @var bool
     */
    protected $updateTime = false;
    /**
     * 删除日期时间戳字段  默认false 系统自动根据form属性识别delete_time并开启
     * @var bool
     */
    protected $deleteTime = false;
    /**
     * 软删除默认值
     * @var int
     */
    protected $defaultSoftDelete = 0;

    /**
     * @var array 自定义数据
     */
    public $customData = [];

    /**
     * 是否是添加操作
     * @var bool
     */
    protected $isInsert = false;
    /**
     * 是否是修改操作
     * @var bool
     */
    protected $isUpdate = false;

    /**
     * 多对多 非真实字段
     * @var array
     */
    protected $belongsToManyForeign = [];

    /**
     * 父模型 --TP多对多要用 不用动
     * @var Model
     */
    public $parent;

    /**
     * @var array 定义全局的查询范围
     */
    protected $globalScope = ['business'];


    public function __construct(array $data = [], $parent = null, string $table = '')
    {
        $this->parent = $parent;

        if (is_null($this->name)) {
            $this->name = $table;
        }
        parent::__construct($data);

        if (!empty($this->relationLink)) {
            foreach ($this->relationLink as $relationModel => &$relationMsg) {
                if (is_string($relationMsg)) {
                    $relationMsg = ['type' => $relationMsg];
                }
                if (!isset($relationMsg['foreign'])) {
                    $relationMsg['foreign'] = Str::studly($relationModel);
                }
                $relationMsg['localName'] = $this->name;
                if ($relationModel != Str::studly($relationModel)) {
                    $this->relationLink[Str::studly($relationModel)] = $relationMsg;
                    unset($this->relationLink[$relationModel]);
                }
                $relationMsg['key'] = Str::studly($relationModel);
                // 识别多对多 需要关联写入的非数据字段
                if ($relationMsg['type'] === 'belongsToMany') {
                    $relationMsg['middle'] = $relationMsg['middle'] ?? Str::studly($this->name) . Str::studly($relationModel);
                    $relationMsg['foreignKey'] = $relationMsg['foreignKey'] ?? Str::snake($relationMsg['key']) . '_id';
                    $relationMsg['localKey'] = $relationMsg['localKey'] ?? Str::snake($this->name) . '_id';
                    $this->belongsToManyForeign[$relationMsg['foreignKey']] = $relationMsg;
                }
            }
        }

        if ($this->display === 'id') {
            $this->display = $this->getPk();
        }

        /*
        if (!isset($this->orderType)) {
            $this->orderType = $this->treeLevel <= 0 ? 'desc' : 'asc';
        }
        */

        // 模型初始化方法  不要使用TP的initialize方法  已经被TP特殊使用  为了避免不必要麻烦 自己加初始化方法
        $this->start();

        // 中台应用
        if (app('http')->getName() == 'business') {
            if (!empty($this->businessForm)) {
                $this->form = $this->businessForm;
            }
            if (!empty($this->businessFormTrigger)) {
                $this->formTrigger = $this->businessFormTrigger;
            }
            if (!empty($this->businessValidate)) {
                $this->validate = $this->businessValidate;
            }
            $this->sortable = $this->businessSortable;
            /*
            if (isset($this->form['business_id']) || isset($this->form['business_member_id']) || $this->name == 'Business') {
                array_push($this->globalScope, 'business');
            }
            */
        }
        if (!empty($this->treeLevel) && isset($this->form['parent_id']) && empty($this->parentModel)) {
            $this->parentModel = 'parent';
        }

        $this->afterStart();

        // 字段处理
        foreach ($this->form as $field => &$item) {
            if (!empty($item['rsa'])) {
                array_push($this->rsaFields, $field);
            }
            // 字段选项（options自动处理）
            if (!empty($item['options']) && is_array($item['options'])) {
                $from = trim((string) array_keys($item['options'])[0]);
                if (0 === strpos($from, 'model.')) {
                    $value = trim((string) array_values($item['options'])[0]);
                    $getModel = Str::studly(substr($from, 6));
                    if (!get_model_name($getModel) && isset($this->relationLink[$getModel])) {
                        $getModel = $this->relationLink[$getModel]['foreign'];
                    }
                    if (get_model_name($getModel)) {
                        $getModel = model($getModel);
                        if (empty($value)) {
                            $value =  $getModel->display;
                        }
                        if (false === strpos($value, ':')) {
                            $key = $getModel->getPk();
                        } else {
                            list($key, $value) = explode(':', $value);
                        }
                        $where = [];

                        if (isset($item['options']['where']) && is_array($item['options']['where'])) {
                            $where = $item['options']['where'];
                        }
                        if (!empty(app('request')->business_id) && isset($getModel->form['business_id'])) {
                            $where[] = ['business_id', '=', (int) app('request')->business_id];
                        }
                        if (isset($getModel->form['delete_time'])) {
                            $where[] = ['delete_time', '=', 0];
                        }
                        $order = $getModel->getDefaultOrder();
                        if (isset($item['options']['order']) && is_array($item['options']['order'])) {
                            $order = $item['options']['order'];
                        }
                        $limit = 1000;
                        if (isset($item['options']['limit']) && is_numeric($item['options']['limit'])) {
                            $limit = intval($item['options']['limit']);
                        }
                        try {
                            $cacheKey = md5(implode('_', ['modelAutoOptions', $getModel->getName(),$this->name, $field, json_encode($where), json_encode($order), $limit, app('request')->business_id ?? 0]));
                            $list = Db::table($getModel->getTable())
                                ->field(array_unique([$key, $value]))
                                ->where($getModel->getCheckAdminWhere())
                                ->where($where)
                                ->order($order)
                                ->limit($limit)
                                ->cache($cacheKey, 3600, model_cache_tag(get_base_class($getModel)), true)
                                ->select()
                                ->toArray();
                            $item['options'] = Arr::combine($list, $key, $value);
                        } catch (\Exception $e) {
                            $item['options'] = [];
                        }
                    }
                } elseif (0 === strpos($from, 'dict.')) {
                    $value = trim((string) array_values($item['options'])[0]);
                    $getDict = substr($from, 5);
                    $value = $value ?: $field;
                    $item['options'] = dict($getDict, $value);
                } elseif (get_app('business') &&  0 === strpos($from, 'option') && count($item['options']) == 1) {
                    $value = trim((string) array_values($item['options'])[0]);
                    $item['options'] = business_option($value);
                }
            }
        }

        if ($this->belongsToManyForeign) {
            foreach ($this->belongsToManyForeign as $field => $info) {
                if (!array_key_exists($field, $this->form)) {
                    unset($this->belongsToManyForeign[$field]);
                }
            }
        }

        // 表单分组 默认一定会有一个叫 basic => '基本信息' 的分组
        if (!isset($this->formGroup['basic'])) {
            $this->formGroup = array_merge([
                'basic' => __('Basic')
            ], $this->formGroup);
        }
        if (!isset($this->tableTab['basic'])) {
            $this->tableTab = array_merge([
                'basic' => [
                    'title' => __('Basic')
                ]
            ], $this->tableTab);
        }
        if (!isset($this->tableTab['basic']['title'])) {
            $this->tableTab['basic']['title'] = __('Basic');
        }

        if (app('http')->getName() == 'admin' && !empty($this->adminCustomTab)) {
            // 后台请求 合并自定义列表属性
            $this->tableTab['basic'] = Arr::deepMerge($this->adminCustomTab, $this->tableTab['basic']);
        }

        if (app('http')->getName() == 'business') {
            // 中台请求 合并自定义列表属性
            $this->tableTab['basic'] = Arr::deepMerge($this->businessCustomTab ? $this->businessCustomTab :$this->adminCustomTab, $this->tableTab['basic']);
        }
        if (!isset($this->tableTab['basic']['table']['title'])) {
            $this->tableTab['basic']['table']['title'] = date('Y-m-d') . $this->cname;
        }

        // 自动识别时间戳字段
        if (isset($this->form['create_time'])) {
            $this->createTime = 'create_time';
        }
        if (isset($this->form['update_time'])) {
            $this->updateTime = 'update_time';
        }
        if (isset($this->form['delete_time'])) {
            $this->deleteTime = 'delete_time';
        }
    }

    /**
     * 初始化  重写以后 一定要回调父级方法 parent::start()
     */
    protected function start()
    {}
    protected function afterStart()
    {}

    /**
     * 获取模型Id编号
     * @return int
     */
    public function getModelId(): int
    {
        return $this->modelId;
    }

    /**
     * 查询多对多的关联id 并返回到数据对象中
     * @return $this
     */
    public function getBelongsToManyFieldsValue()
    {
        if (empty($this->belongsToManyForeign)) {
            return $this;
        }
        $pk = $this[$this->getPk()] ?? 0;
        if (empty($pk)) {
            return $this;
        };
        foreach ($this->belongsToManyForeign as $field => $info) {
            try {
                $list = model($info['middle'])->where($info['localKey'], $pk)->column($info['foreignKey']);
                $this[$field] = $list;
            } catch (\Exception $e) {}
        }
        return $this;
    }

    /**
     * 获取当前模型数据的默认排序方式
     * @return array|string
     */
    public function getDefaultOrder()
    {
        if (empty($this->orderType)) {
            return [];
        }
        $order = [];
        if (is_string($this->orderType) && in_array(strtolower($this->orderType), ['desc', 'asc'])) {
            if (isset($this->form['list_order'])) {
                $order['list_order'] = $this->orderType;
            }
            $order[$this->getPk()] = $this->orderType;
        } elseif (is_array($this->orderType)) {
            $order = $this->orderType;
        } else {
            $order[$this->getPk()] = 'desc';
        }
        return $order;
    }

    public function getParentId()
    {
        if (!$this->getParentModel()) {
            return null;
        }
        if ($this->parentModel === 'parent' || $this->parentModel === $this->name) {
            $parent_id = 'parent_id';
        } elseif (isset($this->relationLink[$this->parentModel])) {
            $parent_id = $this->relationLink[$this->parentModel]['foreignKey'] ?? Str::snake((string) $this->parentModel) . '_id';
        }
        return $parent_id ?? null;
    }

    public function getParentModel()
    {
        if (empty($this->parentModel)) {
            return false;
        }
        if ($this->parentModel == 'parent') {
            return get_base_class($this);
        }
        return $this->relationLink[$this->parentModel]['foreign'] ?? false;
    }


    /**
     * 加设错误信息
     * @param $field
     * @param string $error
     * @return bool
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
        return true;
    }

    /**
     * 选择查询的字段列表
     * @param bool $field true 全部字段  数组 自定字段
     * @param array $except 排除的字段  如(true, ['a']) 表示除了a字段都要
     * @return array
     */
    public function selectField($field = true, array $except = [])
    {
        if ($field === true) {
            $field = $this->getTableFields();
        }
        if (is_string($field)) {
            $field = explode(',', $field);
        }
        return array_diff($field, $except);
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
        return $this->error ?: [];
    }

    public function isSoftDelete()
    {
        return !!$this->deleteTime;
    }

    public function getCascaderText($field, $value, $separator = null)
    {
        if (empty($value)) {
            return '';
        }
        $info = $this->form[$field] ?? [];
        if (empty($info) || empty($info['foreign'])) {
            return '';
        }
        $foreign = get_relation($info['foreign'], $this)[0];

        if (!isset($info['attrs']['data-texttype'])) {
            $sep = $separator ?? ($info['attrs']['data-textseparator'] ?? '/');
            return implode($sep, get_cascader_value($foreign, $value));
        } else {
            return array_pop(get_cascader_value($foreign, $value));
        }
    }

    /**
     * 允许为空，但如果不为空就要唯一 的自定义验证方法
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    public function uniqueWithoutEmpty($value, $rule, $data)
    {
        if (empty($value)) {
            return true;
        }
        if (!$rule) {
            return 'uniqueWithoutEmpty自定义验证缺少[字段]参数';
        }
        $where[] = [$rule, '=', $value];
        if (!empty($data[$this->getPk()])) {
            $where[] = [$this->getPk(), '<>', $data[$this->getPk()]];
        }
        try {
            $count = $this->withTrashed()->where($where)->count();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        if ($count == 0) {
            return true;
        }
        return ($this->form[$rule]['name'] ?? $rule) . '已存在';
    }

    /**
     * 获取允许投稿的字段列表
     * @is_contribute true 表示获取允许投稿的字段列表  false 表示获取不允许投稿的字段列表
     */
    public function getContributeFields(bool $is_contribute = true) :array
    {
        $fields = [];
        foreach ($this->form as $field => $info) {
            if (isset($info['is_contribute']) && $info['is_contribute'] === $is_contribute) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    public function getRsaFields(array $form = [])
    {
        if (empty($form)) {
            return $this->rsaFields;
        }
        foreach ($form as $field => $item) {
            if (isset($this->form[$field])) {
                continue;
            }
            if (!empty($item['rsa'])) {
                array_push($this->rsaFields, $field);
            }
        }
        return $this->rsaFields;
    }


    public function __get(string $name)
    {
        if (array_key_exists(Str::studly($name), $this->relationLink)) {
            $modelRelation = $this->$name();
            $value = $this->getRelationData($modelRelation);
            $this[Str::studly($name)] = $value;
            return $value;
        }
        return $this->getAttr($name);
    }

    public function __call($method, $args)
    {
        if (array_key_exists(Str::studly($method), $this->relationLink)) {
            $method = Str::studly($method);
            if (!isset($this->relationLink[$method]['type'])) {
                throw  new Exception('未定义关联类型');
            }

            $callMethod = $this->relationLink[$method]['type'] . 'Call';

            if (method_exists($this, $callMethod)) {
                $relationResult = $this->$callMethod($method, $this->relationLink[$method]);
                return $relationResult;
            } else {
                throw  new Exception('关联类型：' . $this->relationLink[$method]['type'] . '没有配置对应方法');
            }
        }
        return parent::__call($method, $args);
    }
}