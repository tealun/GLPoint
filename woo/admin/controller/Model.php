<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use app\common\controller\Admin;
use think\facade\Config;
use think\facade\Db;
use think\facade\Session;
use woo\common\annotation\Forbid;
use woo\common\annotation\Ps;
use woo\common\helper\Arr;
use woo\common\helper\CreateFile;
use woo\common\helper\Str;
use think\facade\Env;
use woo\common\annotation\Except;
use woo\common\Upload;

class Model extends Admin
{
    public function index()
    {
        $more_btns = [
            [
                'name' => 'model_to_list',
                'class' => 'new_tab',
                'templet' => '<a href="' . str_replace('MODEL_ID', '{{d.id}}', (string) url('redirectIndex',['id' => 'MODEL_ID'])) .'" class="layui-btn layui-btn-sm" data-title="{{d.cname}}列表">{{d.cname}}列表</a>',
            ],
            [
                'name' => 'delete_index',
                'class' => 'new_tab',
                'templet' => '<a href="' . str_replace('MODEL_ID', '{{d.id}}', (string) url('redirectDeleteIndex',['id' => 'MODEL_ID'])) .'" class="layui-btn layui-btn-sm" data-title="{{d.cname}}回收站">{{d.cname}}回收站</a>',
                'where' => '{{d.is_delete_time}} == 1',
                'where_type' => 'disabled'
            ],
            [
                'name' => 'form_scence',
                'class' => 'new_tab',
                'templet' => '<a href="' . str_replace('MODEL_ID', '{{d.id}}', (string) url('form_scene/index',['parent_id' => 'MODEL_ID'])) .'" class="layui-btn layui-btn-sm" data-title="表单场景列表">表单场景列表</a>',
            ],
        ];

        $this->mdl->tableTab['basic']['item_tool_bar'] = [
            [
                'name' => 'more',
                'title' => '更多',
                'sort' => 0,
                'class' => 'btn-35',
                'icon' => '',
                'children' => $more_btns,
                'length' => 3
            ],
        ];



        if (Env::get('APP_DEBUG')) {
            $this->mdl->tableTab['basic']['item_tool_bar'][] = [
                'name' => 'field',
                'title' => '',
                'sort' => 90,
                'js_func' => '',
                'icon' => 'woo-icon-shuxing',
                'class' => 'btn-23 new_tab model-list-field',
                'url' => (string) url('field/index', ['parent_id' => "{{d.id}}"]),
                'hover' => '模型字段列表',
                'power' => 'Field/index'
            ];

            $this->mdl->tableTab['basic']['item_tool_bar'][] = [
                'name' => 'create_model',
                'title' => '',
                'sort' => 100,
                'js_func' => '',
                'icon' => 'woo-icon woo-icon-program-full',
                'class' => 'btn-39',
                'url' => (string) url('createModel', ['id' => "{{d.id}}"]),
                'hover' => '重新生成模型文件',
                'power' => 'createModel'
            ];

            if (class_exists("woo\\common\\helper\\ApiGenerate")) {
                $api = Db::name('Application')
                    ->where('is_api', '=', 1)
                    ->select()
                    ->toArray();

                $btns = [];
                foreach ($api as $item) {
                    array_push($btns, [
                        'name' => 'api_' . $item['name'],
                        'title' => $item['title'],
                        'url' => (string)url('ApiDevelopment/create', ['application_id' => $item['id'], 'model_id' => 'MODELID']),
                        'js_func' => 'api_development',
                        'power' => 'ApiDevelopment/create'
                    ]);
                }
                if ($btns) {
                    $this->mdl->tableTab['basic']['tool_bar'][] = [
                        'name' => 'api_development',
                        'title' => 'API开发',
                        'sort' => 9.9,
                        'js_func' => '',
                        'icon' => 'woo-icon-api',
                        'class' => 'btn-21',
                        'url' => 'javascript:;',
                        'children' => $btns,
                        'check' => true
                    ];
                }
            }

            $this->mdl->tableTab['basic']['tool_bar'][] = [
                'name' => 'create_from_table',
                'title' => '从数据表生成',
                'sort' => 10,
                'js_func' => '',
                'icon' => '',
                'class' => 'btn-13 new_tab',
                'url' => (string) url('selectTable'),
                'power' => 'selectTable'
            ];

            $this->mdl->tableTab['basic']['tool_bar'][] = [
                'name' => 'copy_model',
                'title' => '复制模型',
                'sort' => 9.5,
                'js_func' => 'copy_model',
                'icon' => 'woo-icon-fuzhi',
                'class' => 'btn-15',
                'url' => (string) url('copy',['model_id' => 'MODELID']),
                'check' => true,
                'power' => 'copy'
            ];

            $this->mdl->tableTab['basic']['tool_bar'][] = [
                'name' => 'model_export',
                'title' => '升级导出',
                'sort' => 9.4,
                'class' => 'btn-2',
                'check' => true,
                'icon' => 'woo-icon-daochu',
                'children' => [
                    [
                        'name' => 'model_export_data',
                        'title' => '模型导出(不含数据)',
                        'sort' => 0,
                        'js_func' => 'model_export_data',
                        'icon' => '',
                        'class' => 'btn-15',
                        'url' => (string) url('exportData'),
                        'check' => true,
                        'power' => 'exportData'
                    ],
                    [
                        'name' => 'model_export_data_list',
                        'title' => '模型导出(含数据)',
                        'sort' => 0,
                        'js_func' => 'model_export_data',
                        'icon' => '',
                        'class' => 'btn-15',
                        'url' => (string) url('exportData', ['list' => true]),
                        'check' => true,
                        'power' => 'exportData'
                    ],
                    [
                        'name' => 'model_export_zip',
                        'title' => '下载升级包',
                        'sort' => 0,
                        'js_func' => 'model_export_zip',
                        'icon' => '',
                        'class' => 'btn-15',
                        'url' => (string) url('exportZip'),
                        'check' => true,
                        'power' => 'exportZip'
                    ]
                ]
            ];
            $this->mdl->tableTab['basic']['tool_bar'][] = [
                'name' => 'model_import',
                'title' => '升级导入',
                'sort' => 9.3,
                'class' => 'btn-21',
                'icon' => 'woo-icon-daoru',
                'children' => [
                    [
                        'name' => 'model_import_data',
                        'title' => '模型导入',
                        'sort' => 0,
                        'js_func' => 'model_import_data',
                        'icon' => '',
                        'class' => 'btn-15',
                        'url' => (string) url('importData'),
                        'power' => 'importData'
                    ],
                    [
                        'name' => 'model_import_dzip',
                        'title' => '上传升级包',
                        'sort' => 0,
                        'js_func' => 'model_import_zip',
                        'icon' => '',
                        'class' => 'btn-15',
                        'url' => (string) url('importZip'),
                        'power' => 'importZip'
                    ]
                ]
            ];
        }
        $this->local['tool_bar']['batch_delete'] = false;
        $this->local['load_type'] = 'load-default';
        //2021-05-22:自定义删除按钮的js方法 然后你自己去写这个js函数
        $this->local['item_tool_bar']['delete']['js_func'] = 'model_delete';
        // $this->local['tool_bar']['batch_delete']['js_func'] = ''; 批量删除的用这个定义


        $this->mdl->tableTab['basic']['list_filters'] = [
            'cname',
            'model',
            'addons' => [
                'name' => '二级目录',
                'templet' => 'text',
                'where' => function($val) {
                    return [
                        ['addon', 'LIKE', "%{$val}%"]
                    ];
                }
            ]
        ];

        if (!get_app('business')) {
            $this->mdl->form['is_business_import']['list'] = 0;
        }

        $this->mdl->tableTab['basic']['toolbar_options']['itemToolbarStyle'] = 'button';
        $this->local['afterData'] = 'afterData';
        $this->local['item_tool_bar']['delete']['where'] = '{{d.id >= 1000 || [23,24,36].indexOf(d.id) >= 0}}';
        return parent::{__FUNCTION__}();
    }

    /**
     * @Ps(false)
     */
    public function redirectIndex()
    {
        if (!isset($this->args['id'])) {
            return $this->message("缺少【id】参数", 'error');
        }
        $id = intval($this->args['id']);
        $model = $this->mdl->find($id);
        if (empty($model)) {
            return $this->message('数据不存在','error');
        }
        $namespace = "app\\admin\\controller\\";
        $url = Str::snake($model['model']) . '/index';
        if (!empty($model['addon'])) {
            $namespace .= $model['addon'] . "\\";
            $url = $model['addon'] . '.' . $url;
        }
        $namespace .= $model['model'];
        if (!class_exists($namespace)) {
            $namespace = "woo" . substr($namespace, 3);
            if (!class_exists($namespace)) {
                return $this->message('当前模型没有找到对应的列表业务', 'warm');
            }
        }
        return $this->redirect($url);
    }

    /**
     * @Ps(false)
     */
    public function redirectDeleteIndex()
    {
        if (!isset($this->args['id'])) {
            return $this->message("缺少【id】参数", 'error');
        }
        $id = intval($this->args['id']);
        $model = $this->mdl->find($id);
        if (empty($model)) {
            return $this->message('数据不存在','error');
        }
        $namespace = "app\\admin\\controller\\";
        $url = Str::snake($model['model']) . '/deleteIndex';
        if (!empty($model['addon'])) {
            $namespace .= $model['addon'] . "\\";
            $url = $model['addon'] . '.' . $url;
        }
        $namespace .= $model['model'];
        if (!class_exists($namespace)) {
            $namespace = "woo" . substr($namespace, 3);
            if (!class_exists($namespace)) {
                return $this->message('当前模型没有找到对应的回收站业务', 'warm');
            }
        }
        return $this->redirect($url);
    }

    protected function afterData()
    {
        foreach ($this->local['tableData']['data'] as &$item) {
            $modelName = (!empty($item['addon']) ? $item['addon'] . '.' : '') . $item['model'];
            if (get_model_name($modelName)) {
                $model = model($modelName);
                // 给返回的每条数据里面加一个字段，代表是否是软删除模型
                $item['is_delete_time'] = isset($model->form['delete_time']) ? 1 : 0;
            } else {
                $item['is_delete_time'] = 0;
            }
        }
    }

    /**
     * @Ps(true,name="删除")
     */
    public function delete()
    {
        if (!$this->app->isDebug()) {
            return $this->message('请在调试模型下进行删除操作','warm');
        }
        if (empty($this->local['not_check_method']) && !$this->request->isAjax()) {
            return $this->message('为防止误删，当前操作只能Ajax异步操作；不能浏览器直接访问', 'warn');
        }
        if (!isset($this->args['id'])) {
            return $this->message("缺少【id】参数", 'error');
        }

        $id = intval($this->args['id']);
        $model = $this->mdl->find($id);
        if (empty($model)) {
            return $this->message('数据不存在','error');
        }
        $result = $this->mdl->deleteData($id, $this->local['where'] ?? [], $this->local['force'] ?? false);
        if (false === $result) {
            return $this->message($this->mdl->getError()[0] ?? '删除失败', 'error');
        }
        $together = $this->request->post('together', []);
        $delete_model = ($model['addon'] ? $model['addon'] . '.' : '') . $model['model'];
        $delete_model = get_model_name($delete_model) ? model($delete_model) : null;

        if ($together && $delete_model && is_array($together)) {
            if (in_array('table', $together)) {
                try{
                    $sql = "DROP TABLE `" . $delete_model->getTable() ."`";
                    Db::connect($model['connection'] ?? '')->execute($sql);
                } catch (\Exception $e){
                    \think\facade\Log::write("SQL:[{$sql}]，错误：" . $e->getMessage(), 'error');
                }
            }
            if (in_array('model', $together)) {
                $file = app()->getBasePath() . "common" . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR
                    .  ($model['addon'] ? $model['addon'] . DIRECTORY_SEPARATOR : '')
                    . $model['model'] . '.php';

                $trait_file = app()->getBasePath() . "common" . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR
                    .  ($model['addon'] ? $model['addon'] . DIRECTORY_SEPARATOR : '')
                    . 'traits' . DIRECTORY_SEPARATOR . $model['model'] . 'Trait' . '.php';
                try {
                    if (is_file($file)) {
                        unlink($file);
                    }
                    if (is_file($trait_file)) {
                        unlink($trait_file);
                    }
                } catch(\Exception $e) {
                    \think\facade\Log::write("模型文件删除失败，错误：" . $e->getMessage(), 'error');
                }
            }
            if (in_array('controller', $together)) {
                $file = app()->getBasePath() . "admin" . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR
                    .  ($model['addon'] ? $model['addon'] . DIRECTORY_SEPARATOR : '')
                    . $model['model'] . '.php';

                $business_file = app()->getBasePath() . "business" . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR
                    .  ($model['addon'] ? $model['addon'] . DIRECTORY_SEPARATOR : '')
                    . $model['model'] . '.php';

                try {
                    if (is_file($file)) {
                        unlink($file);
                    }
                    if (is_file($business_file) && get_app('business')) {
                        unlink($business_file);
                    }

                } catch(\Exception $e) {
                    \think\facade\Log::write("控制器文件删除失败，错误：" . $e->getMessage(), 'error');
                }
            }
            if (in_array('menu', $together)) {
                $menus = Db::name('AdminRule')
                    ->where([
                        ['addon', '=', $model['addon']],
                        ['controller', '=', Str::snake($model['model'])]
                    ])->column('id');
                if ($menus) {
                    model('AdminRule')->deleteData($menus);
                }

                if (get_app('business')) {
                    $menus = Db::name('BusinessMenu')
                        ->where([
                            ['addon', '=', $model['addon']],
                            ['controller', '=', Str::snake($model['model'])]
                        ])->column('id');
                    if ($menus) {
                        model('BusinessMenu')->deleteData($menus);
                    }
                }

            }
        }
        return $this->message("{$this->mdl->cname}[ID:{$id}]删除成功",'success', $this->local['success_redirect'] ?? []);
    }

    /**
     * @Ps(false,name="批量删除")
     */
    public function batchDelete()
    {
        return $this->message('已弃用，请单独进行删除','warm');
    }

    /**
     * @Ps(name="从数据表生成")
     */
    public function createFormTable()
    {
        if (!$this->app->isDebug()) {
            return $this->message('请在调试模型下进行该操作','warm');
        }
        if ($this->request->isPost()) {
            $table = $this->request->param('full_table', '', 'trim');
        } else {
            if (empty($this->request->param('table'))) {
                return $this->message('未选择数据表', 'error');
            }
            $table = $this->request->param('table', '', 'trim');
        }

        try {
            $columns = Db::query("SHOW FULL COLUMNS FROM `{$table}`");
            $tableStatus = Db::query("SHOW TABLE STATUS LIKE '" . $table ."'");
        } catch (\Exception $e) {
            return $this->message($e->getMessage(),'warm');
        }
        if (empty($tableStatus)) {
            return $this->message('数据表获取失败', 'error');
        }
        $tableStatus = $tableStatus[0];
        $post = [];
        if ($this->request->isPost()) {
            $post = $this->request->post();
        }

        $data['model'] = Str::studly(substr($tableStatus['Name'], strlen(get_db_config('prefix'))));
        $data['addon'] = '';
        $data['full_table'] = $tableStatus['Name'];
        $data['cname'] = $tableStatus['Comment'] ?: '';
        $data['list_config'] = 'create,batch_delete,delete,detail,modify';
        if (get_app('business')) {
            $data['business_list_config'] = 'create,batch_delete,delete,detail,modify';
        }
        $data['admin_id'] = $this->login['id'];
        $fields = [];

        foreach ($columns as $key => $field) {
            $item = \woo\common\helper\Model::getItemFromDb($field);
            $this->parseField($item);
            $item = array_merge($item, $post['fields'][$key] ?? []);
            $fields[] = $item;
        }
        $data = array_merge($data, $post);
        $data['fields'] = $post['fields'] = $fields;


        $config = \woo\common\builder\form\FormConfig::get('form_item_lists');
        $options['none'] = '无需表单[none]';
        foreach ($config as $elem => $info) {
            $options[$elem] = ($info['name'] ?? $elem) . '[' . $elem .']';
        }

        $form = new \app\common\builder\FormPage($data);

        if ($this->request->isPost()) {
            $data['is_exists_table'] = 1;
            $data['is_not_create_file'] = 1;
            $data['field_list'] = 'id';
            $data['is_controller'] = 1;
            $model = model('Model');
            $result = $model->createData($data);
            if ($result) {
                try {
                    foreach ($data['fields'] as $item) {
                        $item['model_id'] = $result;
                        $item['admin_id'] = $this->login['id'];
                        $item['create_time'] = time();
                        $item['update_time'] = time();
                        $item['is_field'] = 1;
                        Db::name('Field')->insert($item);
                    }
                    $path = (new \woo\common\helper\CreateFile)->createModel(intval($result));
                    if ($path) {
                        (new \woo\common\helper\CreateFile)->createController($data['model'], $data['addon'] ? strtolower($data['addon']) : '', 'admin');
                    }

                    return $this->message('模型【' . $data['model'] . '】已经创建成功', 'success', ['back' => url('index')]);
                } catch (\Exception $e) {
                    return $this->message($e->getMessage(), 'error');
                }
            } else {
                $form->forceError($model->getError());
            }
        }

        $form->addFormItem('model', 'text')->setLabelAttr('类名');
        $form->addFormItem('full_table', 'hidden');
        $form->addFormItem('list_config', 'hidden');
        $form->addFormItem('admin_id', 'hidden');
        $form->addFormItem('cname', 'text')->setLabelAttr('名称');
        $form->addFormItem('addon', 'text')->setLabelAttr('二级目录');
        $form->addFormItem('fields', 'multiattrs', [
            'label' => '字段列表',
            'message' => '先大体完成基本信息，更多属性生成以后单独编辑；表单和列表大致根据命名习惯做了"猜测"处理，根据情况修改',
            'multiattrs'=> [
                'cancel_create' => true,
                'cancel_insert' => true,
                'cancel_delete' => true,
                'cancel_clear' => true
            ],
            'fields' => [
                'field' => [
                    'elem' => 'text',
                    'label' => '字段',
                    'attrs' => [
                        'readonly' => 'readonly'
                    ]
                ],
                'name' => [
                    'elem' => 'text',
                    'label' => '字段名称'
                ],
                'form' => [
                    'elem' => 'select',
                    'label' => '表单类型',
                    'options' => $options
                ],
                'list' => [
                    'elem' => 'text',
                    'label' => '列表模板',
                    'quick' => \woo\common\builder\form\FormConfig::get('list_item_lists')
                ]
            ]
        ]);


        $this->assign->form = $form;
        $this->setHeaderInfo('title', '从数据表生成');
        return $this->fetch('form');
    }

    protected function parseField(&$item)
    {
        $field = $item['field'] ?? '';
        if (empty($field)) {
            return;
        }
        if ($field == 'id') {
            $item['form'] = 'hidden';
            $item['list'] = 'show';
            return;
        }
        if (in_array($field, ['create_time', 'update_time'])) {
            $item['form'] = 'none';
            return;
        }
        if ($field == 'delete_time') {
            $item['form'] = 'none';
            $item['list'] = '0';
            return;
        }
        if (in_array($field, ['admin_id', 'user_id'])) {
            $item['form'] = 'none';
            $item['list'] = 'relation';
            return;
        }
        if ($field == 'image') {
            $item['form'] = 'image';
            $item['list'] = 'file';
            return;
        }
        if ($field == 'file') {
            $item['form'] = 'file';
            $item['list'] = 'file';
            return;
        }
        if ($field == 'password') {
            $item['form'] = 'password';
            $item['list'] = '0';
            return;
        }
        if ($field == 'content') {
            $item['form'] = 'ueditor';
            $item['list'] = '0';
            return;
        }
        if (in_array($field, ['date', 'datetime', 'time', 'year', 'month', 'icon'])) {
            $item['form'] = $field;
            $item['list'] = $field;
            return;
        }
        if ($field == 'color') {
            $item['form'] = 'color';
            $item['list'] = '0';
            return;
        }
        if ($field == 'parent_id') {
            $item['form'] = 'xmtree';
            return;
        }
        if (in_array($field, ['list_order', 'number', 'count', 'max', 'min'])) {
            $item['form'] = 'number';
            return;
        }
        if (substr($field, 0, 3) == 'is_') {
            $item['form'] = 'checker';
            $item['list'] = 'checker';
            return;
        }
        $item['form'] = 'text';
    }

    /**
     * @Ps(as="createFormTable")
     */
    public function selectTable()
    {
        $table = Db::query("SHOW TABLE STATUS LIKE '" . get_db_config('prefix') ."%'");
        $table = Arr::combine($table, 'Name', 'Name');

        $models = Db::name('Model')
            ->field(['id', 'model', 'addon', 'full_table'])
            ->select()
            ->toArray();
        $existsTables = [];
        foreach ($models as $m) {
            $existsTables[] = $this->getTableName($m);
        }
        $table = array_diff($table, $existsTables, $this->getDiffStatic());

        if (empty($table)) {
            return $this->message('当前没有需要生成模型的数据表', 'warm');
        }

        $form = new \app\common\builder\FormPage();
        $form->setFormInfo('action',  url('createFormTable'));
        $form->setFormInfo('method',  'GET');
        $form->addFormItem('table', 'xmselect',[
            'label' => '数据表名',
            'attrs' => [
                'data-max' => 1,
            ],
            'options' =>  $table,
            'message' => '
<h5>规范，避免多坑：</h5>
<div>1、尽量直接通过创建模型来创建数据表</div>
<div>2、主键字段名`id`，int, 自动增长</div>
<div>3、时间戳字段，系统中必须是：create_time int, update_time int,delete_time int 且默认值为0</div>
<div>4、"是否"用0,1表示，字段名`is_`开头</div>
<div>5、关联字段名：关联模型小写+下划线形式_id，如：关联AdPosition模型，关联字段名`ad_position_id`</div>
<div>6、"无限级"父ID字段，系统中必须：`parent_id`</div>
<div>7、排序权重，系统中必须：`list_order`</div>
<div>8、能给默认值类型的字段，尽量都给默认值</div>
<div>...</div>
'
        ]);

        $this->assign->form = $form;
        //$this->addAction('return','返回列表', $this->getIndexUrl(), 'layui-btn-normal', '', 10);
        $this->setHeaderInfo('title', '选择数据表');
        return $this->fetch('form');
    }

    protected function getTableName($model_data)
    {
        return empty($model_data['full_table']) ?
            get_db_config('prefix') . ($model_data['addon'] ? Str::snake($model_data['addon']) . '_' : '') .Str::snake($model_data['model'])
            :$model_data['full_table'];
    }

    protected function getDiffStatic()
    {
        $tables = ['model', 'field', 'addon', 'addon_setting', 'request_log'];
        foreach ($tables as &$t) {
            $t = get_db_config('prefix') . $t;
        }
        return $tables;
    }

    /**
     * @Forbid(nodebug=true)
     */
    public function create()
    {
        $this->mdl->form['field_list']['elem'] = 'xmselect';
        $this->mdl->form['is_exists_table']['elem'] = 'checker';
        $this->mdl->form['parent_admin_menu_id']['elem'] = 'xmtree';
        $this->mdl->form['parent_admin_menu_id']['optionsCallback'] = function ($level = 0, $value = '', $data = []) {
            $result = model('AdminRule')->form['parent_id']['optionsCallback']($level, $value);
            return isset($result[0])? $result[0]['children']: $result;
        };

        $this->setFormValue('parent_admin_menu_id', 1);
        $this->setFormValue('field_list', 'id,create_time,update_time');
        $this->setFormValue('tree_level', 0);
        $this->setFormValue('is_controller', 1);
        $this->setFormValue('list_config', 'create,batch_delete,modify,delete,detail');

        if (get_app('business')) {
            $this->mdl->form['parent_business_menu_id']['elem'] = 'xmtree';
            $this->mdl->form['parent_business_menu_id']['optionsCallback'] = function ($level = 0, $value = '', $data = []) {
                return $this->deepBusinessMenuData(tree('BusinessMenu','children', 0), $level, 1);
            };
            $this->setFormValue('is_business_copy_admin', 1);
            $this->setFormValue('business_list_config', 'create,batch_delete,modify,delete,detail');
        }
        return  parent::{__FUNCTION__}();
    }

    /**
     * @Ps(true,name="复制添加",as="create")
     * @Forbid(nodebug=true)
     */
    public function  copy()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求错误');
        }
        $id = intval($this->args['model_id'] ?? 0);
        $post = $this->request->post();
        $model = $this->mdl->where('id', '=', $id)->find();

        if (empty($model)) {
            return $this->error('ID为[' . $id .']的模型不存在');
        }
        if (empty($post['model'])) {
            return $this->error('模型名不存在');
        }
        $copyModel = ($model['addon'] ? $model['addon'] . '.' : '') . $model['model'];
        if (!get_model_name($copyModel)) {
            return $this->error('被复制的模型类不存在');
        }
        $copyModel = model($copyModel);

        // 复制模型
        $data = $model->toArray();
        $data['model'] = Str::studly($post['model']);
        $data['addon'] = $post['addon'] ?? '';
        $data['full_table'] = $post['table'] ?? '';
        $data['cname'] = !empty($post['cname']) ? $post['cname'] :$data['cname'];
        $data['admin_id'] = $this->login['id'];
        $data['is_exists_table'] = 1;
        $table_name = empty($data['full_table']) ?
            get_db_config('prefix', $model['connection'] ?? '') . ($data['addon'] ? Str::snake($data['addon']) . '_' : '') .Str::snake($data['model'])
            :$data['full_table'];
        if (!empty($data['addon']) && empty($data['full_table'])) {
            $data['full_table'] = $table_name;
        }
        unset($data['id']);
        unset($data['create_time']);
        unset($data['update_time']);
        unset($this->mdl->validate['field_list']);
        $result = $this->mdl->createData($data);
        if ($result) {
            try {
                // 复制表结构
                $sql = "CREATE  TABLE IF NOT EXISTS `{$table_name}` (LIKE `{$copyModel->getTable()}`)";
                Db::connect($model['connection'] ?? '')->execute($sql);
                // 修改表备注
                $sql = "ALTER TABLE `{$table_name}` COMMENT = '{$data['cname']}'";
                Db::connect($model['connection'] ?? '')->execute($sql);
                // 自动增长
                $sql = "ALTER TABLE `$table_name` AUTO_INCREMENT =1";
                Db::connect($model['connection'] ?? '')->execute($sql);
                // 复制字段列表到field表
                $fields = Db::name('Field')->where('model_id', '=', $id)->select()->toArray();
                $fields = array_map(function ($item) use ($result) {
                    unset($item['id']);
                    $item['model_id'] = $result;
                    $item['admin_id'] = $this->login['id'];
                    $item['create_time'] = time();
                    $item['update_time'] = time();
                    return $item;
                }, $fields);
                Db::name('Field')->insertAll($fields);
                // 生成模型和控制器文件
                $path = (new CreateFile)->createModel(intval($result));
                if ($data['is_controller'] && $path) {
                    (new CreateFile)->createController($data['model'], $data['addon'] ? strtolower($data['addon']) : '', 'admin');
                }
                return $this->success('模型[' . ($data['addon'] ? $data['addon'] . '.' : '') . $data['model']  . ']复制成功');
            } catch (\Exception $e) {
                $this->mdl->deleteData($result);
                return $this->error($e->getMessage());
            }
        } else {
            return $this->error(array_values($this->mdl->getError())[0] ?? '复制失败');
        }
    }

    protected function setFormGrid()
    {
        $this->formPage->setTab('basic', '基本信息');
        $this->formPage->setTab('admin', '后台开发配置');
        if (get_app('business')) {
            $this->formPage->setTab('business', '中台开发配置');
        }

        if (setting('do_is_batch_edit_fields') && $this->params['action'] == 'modify') {
            $this->formPage->setTab('field', '批量修改字段');
        }
        $this->formPage->setTab('table', '数据表信息');


        $this->formPage->switchTab('basic')->setGrid('a', '填完即可提交', 6, [
            'model',
            'cname',
            'addon',
            'field_list',
            [
                'is_controller',
                'is_business_controller',
            ],
            'parent_admin_menu_id',
            'parent_business_menu_id'
        ])->setGrid('b', '可后续配置的信息', 6, [
            'display',
            'parent_model',
            'order_type',
            'tree_level',
            'form_group',
            'custom_data',
        ])->setGrid('c', '关联模型', 12, [
            'relation_link' => [
                'is_not_label' => true
            ],
        ]);

        $this->formPage->switchTab('admin')->setGrid('d', '列表基础配置',12, [
            'admin_is_remove_pk',
            'list_config',
            'admin_item_checkbox',
            'admin_filter_model',
            'is_import',
        ])->setGrid('kk', '查询相关配置', 12, [
            '关联模型键名' => [
                'admin_list_with'  => [
                    'is_not_label' => true
                ]
            ],
            '列表字段配置' => [
                'admin_list_fields'  => [
                    'is_not_label' => true
                ]
            ],
            '搜索字段配置' => [
                'admin_list_filters'  => [
                    'is_not_label' => true
                ]
            ]
        ])->setGrid('e', '头部工具', 12, [
            'admin_tool_bar' => [
                'is_not_label' => true
            ]
        ])->setGrid('f', '列表项工具', 12, [
            'admin_item_tool_bar' => [
                'is_not_label' => true
            ]
        ])->setGrid('g', '列表项工具属性', 12, [
            'admin_item_toolbar_options' => [
                'is_not_label' => true
            ]
        ])->setGrid('h', '列表上方综合基础统计', 12, [
            'admin_counter' => [
                'is_not_label' => true
            ]
        ])->setGrid('hh', '列表底部表格列合计', 12, [
            'admin_total_row' => [
                'is_not_label' => true
            ]
        ])->setGrid('i', '侧边栏模型', 12, [
            'admin_siderbar' => [
                'is_not_label' => true
            ]
        ])->setGrid('j', '表格基础参数', 12, [
            'admin_table_attrs' => [
                'is_not_label' => true
            ]
        ]);

        if (get_app('business')) {
            $this->formPage->switchTab('business')->setGrid('d2', '列表基础配置',12, [
                'is_business_copy_admin',
                'business_is_remove_pk',
                'business_list_config',
                'business_item_checkbox',
                'business_filter_model',
                'is_business_import',
            ])->setGrid('kk2', '查询相关配置', 12, [
                '关联模型键名' => [
                    'business_list_with'  => [
                        'is_not_label' => true
                    ]
                ],
                '列表字段配置' => [
                    'business_list_fields'  => [
                        'is_not_label' => true
                    ]
                ],
                '搜索字段配置' => [
                    'business_list_filters'  => [
                        'is_not_label' => true
                    ]
                ]
            ])->setGrid('e2', '头部工具', 12, [
                'business_tool_bar' => [
                    'is_not_label' => true
                ]
            ])->setGrid('f2', '列表项工具', 12, [
                'business_item_tool_bar' => [
                    'is_not_label' => true
                ]
            ])->setGrid('g2', '列表项工具属性', 12, [
                'business_item_toolbar_options' => [
                    'is_not_label' => true
                ]
            ])->setGrid('h2', '列表上方综合基础统计', 12, [
                'business_counter' => [
                    'is_not_label' => true
                ]
            ])->setGrid('hh2', '列表底部表格列合计', 12, [
                'business_total_row' => [
                    'is_not_label' => true
                ]
            ])->setGrid('i2', '侧边栏模型', 12, [
                'business_siderbar' => [
                    'is_not_label' => true
                ]
            ])->setGrid('j2', '表格基础参数', 12, [
                'business_table_attrs' => [
                    'is_not_label' => true
                ]
            ]);
        } else {
            $this->formPage->removeFormItem('business_is_remove_pk');
            $this->formPage->removeFormItem('business_list_config');
            $this->formPage->removeFormItem('business_item_checkbox');
            $this->formPage->removeFormItem('business_filter_model');
            $this->formPage->removeFormItem('business_list_with');
            $this->formPage->removeFormItem('business_list_fields');
            $this->formPage->removeFormItem('business_list_filters');
            $this->formPage->removeFormItem('business_tool_bar');
            $this->formPage->removeFormItem('business_item_tool_bar');
            $this->formPage->removeFormItem('business_item_toolbar_options');
            $this->formPage->removeFormItem('business_counter');
            $this->formPage->removeFormItem('business_total_row');
            $this->formPage->removeFormItem('business_siderbar');
            $this->formPage->removeFormItem('business_table_attrs');
            $this->formPage->removeFormItem('is_business_copy_admin');
            $this->formPage->removeFormItem('is_business_import');
            $this->formPage->removeFormItem('is_business_controller');
        }

        if (setting('do_is_batch_edit_fields')  && $this->params['action'] == 'modify') {
            $this->formPage->switchTab('field')->setGrid('k', '',12, [
                'field' => [
                    'is_not_label' => true
                ]
            ]);
        }

        $this->formPage->switchTab('table')->setGrid('l', '',12, [
            '不建议的信息' => [
                'is_exists_table',
                'full_table',
                'suffix',
                'pk',
                'connection',
            ]
        ]);
    }

    /**
     * @Forbid(nodebug=true)
     */
    public function modify()
    {
        $this->mdl->form['parent_model']['elem'] = 'text';
        $this->mdl->form['display']['elem'] = 'text';
        $fieldsList = Db::name('Field')->where('model_id', '=', intval($this->args['id'] ?? 0))->order(['list_order' => 'ASC','id' => 'ASC'])->field(['id', 'field', 'name'])->cache(60)->select();;
        $fields = [];
        foreach ($fieldsList as $item) {
            $fields[$item['field']] = $item['field'];
        }
        $this->mdl->form['display']['options'] = $fields;
        $this->mdl->form['form_group']['elem'] = 'keyvalue';
        $this->mdl->form['relation_link']['elem'] = 'multiattrs';
        $this->mdl->form['is_controller']['elem'] = 0;
        $this->mdl->form['is_business_controller']['elem'] = 0;
        $this->mdl->form['full_table']['elem'] = 'format';
        $this->mdl->form['model']['elem'] = 'format';
        $this->mdl->form['addon']['elem'] = 'format';
        if (setting('do_is_batch_edit_fields')) {
            $this->mdl->form['field'] = [
                'foreign' => 'Field',
                'elem' => 'together',
                'multiattrs' => [
                    'cancel_create' => true,
                    'cancel_clear' => true,
                    'cancel_delete' => true
                ],
                'message' => '仅支持批量修改，不支持新增、删除、和批量调整数据表结构；我要到『<a style="color:#1E9FFF;font-weight:bold;" href="'.url('field/index', ['parent_id' => $this->args['id']]).'" class="new_tab">字段列表</a>』进行更多操作。',
                'fields' => [
                    'id',
                    'field' => [
                        'elem' => 'text',
                        'attrs' => [
                            'readonly' => 'readonly',
                        ]
                    ],
                    'name',
                    'model_id',
                    'form' => [
                        'elem' => 'text'
                    ],
                    'form_options',
                    'form_foreign',
                    'form_item_attrs',
                    'form_tag_attrs',
                    'list',
                    'list_attrs',
                    'list_filter',
                    'list_filter_attrs',
                    'list_filter_tag_attrs',
                    'detail',
                    'detail_attrs',
                    'list_order'
                ]
            ];
        }
        $model = array_values(model_cache('', [['id', '=', $this->args['id']]]))[0] ?? [];

        if ($model) {
            $model = ($model['addon'] ? $model['addon'] . '.' : '') . $model['model'];
            $this->mdl->form['admin_counter']['fields']['field']['elem'] = 'select';
            if (get_app('business')) {
                $this->mdl->form['business_counter']['fields']['field']['elem'] = 'select';
            }
            $list = [];
            foreach (model($model)->form as $field => $info) {
                $list[$field] = ($info['name'] ?? Str::studly($field)) . " [{$field}]";
            }
            $this->mdl->form['admin_counter']['fields']['field']['options'] = $list;
            $this->mdl->form['admin_total_row']['fields']['field']['options'] = $list;
            $this->mdl->form['admin_list_filters']['fields']['field']['options'] = $list;
            if (get_app('business')) {
                $this->mdl->form['business_counter']['fields']['field']['options'] = $list;
                $this->mdl->form['business_total_row']['fields']['field']['options'] = $list;
                $this->mdl->form['business_list_filters']['fields']['field']['options'] = $list;
            }

            unset($list[model($model)->getPk()]);
            $this->mdl->form['admin_list_fields']['fields']['field']['options'] = $list;
            if (get_app('business')) {
                $this->mdl->form['business_list_fields']['fields']['field']['options'] = $list;
            }
        }

        $parent_return =  parent::{__FUNCTION__}();
        if (empty($this->local['data']['admin_item_toolbar_options'])) {
            $this->formPage->setItemValue('admin_item_toolbar_options', '[{"is_show":"1","title":"操作","fixed":"right","min_width":"0","align":"center"}]');
        }
        if (get_app('business') && empty($this->local['data']['business_item_toolbar_options'])) {
            $this->formPage->setItemValue('business_item_toolbar_options', '[{"is_show":"1","title":"操作","fixed":"right","min_width":"0","align":"center"}]');
        }
        return $parent_return;
    }

    /**
     * @Ps(name="生成模型")
     */
    public function createModel()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止执行该操作', 'warn');
        }
        $id = intval($this->args['id'] ?? 0);
        if ($id <= 0) {
            return $this->message('参数错误', 'error');
        }
        try {
            $path = (new CreateFile)->createModel($id);
            return $this->message('模型生成成功，文件目录:' . $path, 'success');
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
    }

    protected function deepBusinessMenuData($children, $level = 0, $nowLevel = 1)
    {
        $list = [];
        $title = 'title';
        foreach ($children as $id) {
            $item = tree('BusinessMenu', $id);
            $my = [
                "name" => $item[$title],
                "value" => $id,
            ];
            if ($item['type'] == 'button') {
                continue;
            }
            if (tree('BusinessMenu', 'children', $id)) {
                $my['children'] = $this->deepBusinessMenuData(tree('BusinessMenu', 'children', $id), $level, $nowLevel + 1);
            }
            $list[] = $my;
        }
        return $list;
    }

    /**
     * @Ps(name="模型导出")
     */
    public function exportData()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止执行该操作', 'warn');
        }
        $ids = $this->request->post('ids', []);
        if (empty($ids)) {
            return $this->error('ID列表获取失败');
        }
        try {
            $exportData = $this->getExportData($ids, $this->args['list'] ?? false);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        return $this->success('数据获取成功', $exportData);
    }

    protected  function getExportData(array $ids, $is_list = false)
    {
        // 结构
        $data = model('Model')
            ->where([
                ['id', 'IN', $ids]
            ])
            ->with(['Field'])
            ->select()
            ->toArray();
        if (empty($data)) {
            throw  new \Exception('数据不存在');
        }
        foreach ($data as &$item) {
            $item['Field'] = Arr::combine($item['Field'], 'field');
            // 数据
            if (!empty($is_list)) {
                $item['MODEL_TABLE_LIST'] =
                    Db::table(model(($item['addon'] ? $item['addon'] . '.' : '') . $item['model'])->getTable())
                        ->limit(500)
                        ->select()
                        ->toArray();
            }
        }
        $key = Config::get('woomodel.export_key', '666666');
        $expire = Config::get('woomodel.export_expire', 60);
        return [
            'data' => Str::deepJsonDecode($data),
            'token' => Str::setEncrypt(json_encode(['key' => $key, 'expire' => time() + $expire])),
            'expire' => date('m-d H:i:s', time() + $expire)
        ];
    }

    /**
     * @Ps(name="模型升级")
     */
    public function importData()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止执行该操作', 'warn');
        }
        $key = $this->request->post('key', '');
        $is_list = $this->request->post('is_list', false);
        $post = $this->request->post('data', '');
        if (empty($key) || empty($post)) {
            return $this->error('提交数据不完整');
        }
        $post = Str::deepJsonDecode($post);
        $data = $post['data'] ?? [];
        $token = Str::setDecrypt($post['token'] ?? '');
        if (empty($data) || empty($token)) {
            return $this->error('数据获取失败');
        }
        $token = Str::deepJsonDecode($token);
        if ($token['key'] != $key) {
            return $this->error('导入密钥匹配失败');
        }
        if ($token['expire'] <= time()) {
            return $this->error('模型升级数据已超时，需重新导出并提交');
        }

        try {
            $this->importDataAction($data, $is_list);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        return $this->success('模型升级数据导入完成');
    }

    /**
     * @Ps(as="importData")
     */
    public function importZip()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止执行该操作', 'warn');
        }
        $key = $this->request->post('key', '');
        $is_list = $this->request->post('is_list', false);
        $updateFile = $this->request->post('file', '');
        if (empty($key) || empty($updateFile)) {
            return $this->error('提交数据不完整');
        }
        $updateFile = public_path() . substr($updateFile, 1);
        if (!is_file($updateFile)) {
            return $this->error('升级包文件不存在');
        }
        // 先清除
        rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update', true);
        if (is_file(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update.zip')) {
            unlink(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update.zip');
        }
        // 解压
        $zip = new \ZipArchive();
        if (true !== $zip->open($updateFile, \ZipArchive::CREATE)) {
            return $this->error('压缩包打开失败');
        }

        // 准备临时目录
        $runtime = root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update' . DIRECTORY_SEPARATOR;
        if (!is_dir($runtime)) {
            mkdir($runtime, 0755, true);
        }
        if (true !== $zip->extractTo($runtime)) {
            return $this->error('压缩包解压失败');
        }
        $zip->close();
        $rootpath = root_path();

        // 打开文件
        if (!is_file($runtime . 'update.txt')) {
            rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update', true);
            return $this->error('升级模型数据文件不存在');
        }
        $post = unserialize(file_get_contents($runtime . 'update.txt'));

        $data = $post['data'] ?? [];
        $files = $post['files'] ?? [];
        $token = Str::setDecrypt($post['token'] ?? '');
        if (empty($data) || empty($token)) {
            rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update', true);
            return $this->error('数据获取失败');
        }
        $token = Str::deepJsonDecode($token);
        if ($token['key'] != $key) {
            rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update', true);
            return $this->error('导入密钥匹配失败');
        }
        if ($token['expire'] <= time()) {
            rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update', true);
            return $this->error('模型升级数据已超时，需重新导出并提交');
        }

        try {
            $this->importDataAction($data, $is_list);
        } catch (\Exception $e) {
            rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update', true);
            return $this->error($e->getMessage());
        }

        // 文件覆盖
        if (!empty($files)) {
            try {
                foreach ($files as $file) {
                    if (!is_file($runtime . $file['path'])) {
                        continue;
                    }
                    if (!is_file($rootpath . $file['path'])) {
                        // 文件不存在 直接复制
                        $dir = dirname($rootpath . $file['path']);
                        if (!is_dir($dir)) {
                            mkdir($dir, 0755, true);
                        }
                        copy($runtime . $file['path'], $rootpath . $file['path']);
                        continue;
                    }
                    // 有文件
                    $mtime = filemtime($rootpath . $file['path']);// 获取本地文件的最后修改时间
                    if ($file['mtime'] > $mtime) { // 只有升级文件比本地文件 更新 才覆盖
                        copy($runtime . $file['path'], $rootpath . $file['path']);
                    }
                }
            }  catch (\Exception $e) {
                rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update', true);
                return $this->error($e->getMessage());
            }
        }
        rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update', true);
        return $this->success('模型升级包升级完成');
    }

    /**
     * @Ps(name="下载升级包")
     * @Forbid(nodebug=true)
     */
    public function exportZip()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止执行该操作', 'warn');
        }
        $ids = explode(',', $this->args['ids'] ?? '');
        try {
            $exportData = $this->getExportData($ids, true);
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }

        // 应用列表
        $apps = model('Application')->column('name');
        array_push($apps, 'admin');

        // 先清除
        rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update', true);
        if (is_file(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update.zip')) {
            unlink(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update.zip');
        }

        // 准备临时目录
        $runtime = root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update' . DIRECTORY_SEPARATOR;
        if (!is_dir($runtime)) {
            mkdir($runtime, 0755, true);
        }
        $rootpath = root_path();

        // 准备需打包的文件
        $files = [];
        foreach ($exportData['data'] as $item) {
            foreach ($apps as $app) {
                // 控制器
                $controller_file = 'app/' . $app . '/controller/' . ($item['addon']? $item['addon'] . '/' : '') . $item['model'] . '.php';
                if (is_file($rootpath . $controller_file)) {
                    $files[] = [
                        'path' => $controller_file,
                        'mtime' => filemtime($rootpath . $controller_file),
                    ];
                }
                // 视图文件
                $view_path =  'app/' . $app . '/view/' . ($item['addon']? $item['addon'] . '/' : '') . Str::snake($item['model']);
                if (is_dir($rootpath . $view_path)) {
                    $files = array_merge($files, $this->getDirFiles($rootpath, $view_path));
                }
                // 应用下的模型文件 一般没有
                $model_file = 'app/' . $app . '/model/' . ($item['addon']? $item['addon'] . '/' : '') . $item['model'] . '.php';
                if (is_file($rootpath . $model_file)) {
                    $files[] = [
                        'path' => $model_file,
                        'mtime' => filemtime($rootpath . $model_file),
                    ];
                }
                // 服务文件 一般没有 默认的命名规则 比如 Goods模型 对应的服务文件 应该是  service/GoodsService.php
                $service_file = 'app/' . $app . '/service/' . ($item['addon']? $item['addon'] . '/' : '')  . $item['model'] . 'Service.php';
                if (is_file($rootpath . $service_file)) {
                    $files[] = [
                        'path' => $service_file,
                        'mtime' => filemtime($rootpath . $service_file),
                    ];
                }
                // 独立验证器
                $validate_file = 'app/' . $app . '/validate/' . ($item['addon']? $item['addon'] . '/' : '')  . $item['model'] . '.php';
                if (is_file($rootpath . $validate_file)) {
                    $files[] = [
                        'path' => $validate_file,
                        'mtime' => filemtime($rootpath . $validate_file),
                    ];
                }
            }

            // 公共模型文件不考虑打包 因为会自动生成 这里只打包模型对应trait文件
            $model_trait = 'app/common/model/traits/' . ($item['addon']? $item['addon'] . '/' : '')  . $item['model'] . 'Trait.php';
            if (is_file($rootpath . $model_trait)) {
                $files[] = [
                    'path' => $model_trait,
                    'mtime' => filemtime($rootpath . $model_trait),
                ];
            }
            // 公共服务文件 默认的命名规则 比如 Goods模型 对应的服务文件 应该是  service/GoodsService.php
            $service_file = 'app/common/service/' . ($item['addon']? $item['addon'] . '/' : '')  . $item['model'] . 'Service.php';
            if (is_file($rootpath . $service_file)) {
                $files[] = [
                    'path' => $service_file,
                    'mtime' => filemtime($rootpath . $service_file),
                ];
            }
            // 公共独立验证器
            $validate_file = 'app/common/validate/' . ($item['addon']? $item['addon'] . '/' : '')  . $item['model'] . '.php';
            if (is_file($rootpath . $validate_file)) {
                $files[] = [
                    'path' => $validate_file,
                    'mtime' => filemtime($rootpath . $validate_file),
                ];
            }
        }

        // 拷贝文件
        foreach ($files as $file) {
            $path = $runtime . $file['path'];
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            copy($rootpath .  $file['path'], $path);
        }

        // 升级文件写入
        $exportData['files'] = $files;
        file_put_contents($runtime . 'update.txt', serialize($exportData));

        // 打包
        $zip = new \ZipArchive();
        $zip_path = root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update' . '.zip';
        if (true === $zip->open($zip_path, \ZipArchive::CREATE)) {
            addfile_tozip($runtime, $zip);
            $zip->close();
            // 提供下载
            return download($zip_path, date('YmdHis'))->expire(300);
        }
        return $this->message('打包失败', 'error');
    }

    /**
     * @Ps(as="importData")
     */
    public function zipUpload()
    {
        if (!Env::get('APP_DEBUG')) {
            return json([
                'code' => 0,
                'type' => 'error',
                'message' => '非开发调试模式下，禁止执行该操作'
            ]);
        }
        $upload = new Upload([
            'type' => 'local',
            'validExt' => 'zip',
            'model' => 'Model'
        ]);
        $filepath = $upload->putFile($this->request->file('upload'));

        if ($filepath) {
            return json([
                'code' => 0,
                'type' => 'success',
                'message' => '',
                'url' => $filepath
            ]);
        } else {
            return json([
                'code' => 0,
                'type' => 'error',
                'message' => $upload->getError()[0] ?? '上传错误'
            ]);
        }
    }

    protected function getDirFiles($path, $dir = '')
    {
        $base_path = $path . $dir;

        if (!is_dir($base_path)) {
            return [];
        }
        $handle = opendir($base_path);
        $files = [];
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($base_path . '/' . $file)) {

                    $files = array_merge($files, $this->getDirFiles($path,  $dir . '/' . $file));
                } else {
                    $files[] = [
                        'path' => $dir . '/' . $file,
                        'mtime' => filemtime($base_path . '/' . $file),
                    ];
                }
            }
        }
        closedir($handle);
        return $files;
    }

    protected function importDataAction($data, $is_list)
    {
        foreach ($data as $item) {
            $fields = $item['Field'] ?? [];
            $lists = $item['MODEL_TABLE_LIST'] ?? [];

            if (isset($item['id'])) unset($item['id']);
            if (isset($item['create_time'])) unset($item['create_time']);
            if (isset($item['update_time '])) unset($item['update_time ']);
            if (isset($item['Field'])) unset($item['Field']);
            if (isset($item['MODEL_TABLE_LIST'])) unset($item['MODEL_TABLE_LIST']);

            // 模型更新
            $model = $this->mdl->where([
                    ['model', '=', $item['model']],
                    ['addon', '=', $item['addon']]
                ])
                ->find();

            $item['is_not_create_file'] = 1;
            if (!empty($model)) {
                // 模型存在
                $model->isValidate(false)->modifyData($item);
                $is_exists = true;
            } else {
                // 不存在
                $item['field_list'] = 'id';
                $model = model('Model', '', true);
                $model->isValidate(false)->createData($item);
            }

            // 字段更新
            if (!empty($is_exists)) {
                $exists_fields = model('Field')->where('model_id', '=', $model->id)->select();
                foreach ($exists_fields as $ff) {
                    $ff->setAttr('is_not_create_file', true);
                    if (array_key_exists($ff['field'], $fields)) {
                        continue;
                    }
                    // 字段已不存在，升级的时候也需要删除
                    $ff->delete();
                }
            }
            foreach ($fields as $f) {
                if (isset($f['id'])) unset($f['id']);
                if (isset($f['create_time'])) unset($f['create_time']);
                if (isset($f['update_time '])) unset($f['update_time ']);
                $f['is_not_create_file'] = 1;
                $f['model_id'] = $model->id;

                $field = model('Field')
                    ->where([
                        ['model_id', '=', $f['model_id']],
                        ['field', '=', $f['field']]
                    ])
                    ->find();
                if (!empty($field)) {
                    // 字段存在
                    $field->isValidate(false)->modifyData($f);
                } else {
                    // 字段不存在
                    model('Field', '', true)->isValidate(false)->createData($f);
                }
            }

            // 文件生成
            $path = (new CreateFile)->createModel(intval($model->id));
            if (!empty($model['is_controller']) && $path && empty($is_exists)) {
                (new CreateFile)->createController($model['model'], $model['addon'] ? strtolower($model['addon']) : '', 'admin');
            }
            if (!empty($model['is_business_controller']) && $path && get_app('business') && empty($is_exists)) {
                (new CreateFile)->createController($model['model'], $model['addon'] ? strtolower($model['addon']) : '', 'business');
            }

            $table_name = empty($model['full_table']) ?
                get_db_config('prefix', $model['connection'] ?? '') . ($model['addon'] ? Str::snake($model['addon']) . '_' : '') . Str::snake($model['model'])
                :$model['full_table'];

            // 数据更新
            if ($is_list && !empty($lists)) {
                Db::table($table_name)->delete(true);
                Db::table($table_name)->insertAll($lists);
            }
            return true;
        }
    }
}