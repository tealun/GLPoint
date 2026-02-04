<?php
declare (strict_types=1);

namespace woo\common\helper;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;

class CreateFile
{

    public function createModel(int $id)
    {
        $data = model('Model')->with(['Field' => ['order' => ['list_order' => 'ASC','id' => 'ASC']]])->where('id', '=', $id)->find();
        if (empty($data)) {
            throw new \Exception('模型数据不存在');
        }
        $data = $data->toArray();
        if ($data['Field']) {
            $db_fields_list = Arr::combine($data['Field'], 'field');
            try {
                $table_fields = get_table_columns(0, $data, false);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            foreach ($table_fields as $finfo) {
                $f = $finfo['Field'];
                if (array_key_exists($f, $db_fields_list)) {
                    continue;
                }
                $db_fields_list[$f] = [
                    'field' => $f,
                    'name' => $finfo['Comment'] ?: $f
                ];
            }
            $data['Field'] = $db_fields_list;
        }

        $formScene = [];
        if (get_model_name('FormScene')) {
            $formScene = model('FormScene')
                ->where([
                    ['model_id', '=', $data['id']],
                    ['is_verify', '=', 1]
                ])
                ->withoutField(['model_id', 'create_time', 'update_time'])
                ->order(model('FormScene')->getDefaultOrder())
                ->select()
                ->toArray();
            $formScene = Str::deepJsonDecode($formScene);
            $formScene = Arr::combine($formScene, 'id');
        }


        $model_name = $data['model'];
        $model_full_name = ($data['addon'] ? $data['addon'] . ' .' : '') . $model_name;
        Cache::tag($model_full_name)->clear();
        $namespace_name = "app\\common\\model" . ($data['addon'] ? "\\" . strtolower($data['addon']) : "");
        $namespace_woo = "woo\\common\\model" . ($data['addon'] ? "\\" . strtolower($data['addon']) : "");
        $model_trait = $namespace_name . "\\traits\\" . $model_name . 'Trait';
        $extends_name = "app\\common\\model\\App";
        $is_extends_woo = false;
        if (class_exists($namespace_woo . "\\" . $model_name)) {
            $is_extends_woo = true;
            $extends_name = $namespace_woo . "\\" . $model_name;
        }

        if (!trait_exists($model_trait)) {
            $file = new PhpFile();
            $file->addComment("你把当前trait文件理解为模型文件【" . $namespace_name . "\\" . $model_name .  "】的一部分，自定义代码都定义在当前文件中");
            $trait = $file->addTrait($model_trait);


            $start = $trait->addMethod('afterStart');
            $start->setProtected();
            $startBody = <<<DOC
parent::{__FUNCTION__}();
// 代码执行到这里的时候已经 直接执行过了start方法 所以start定义的属性都可以获取到 当然也可以在该文件定义更多的自定义属性和方法
// \$this->form[字段名] =  动态修改字段的某个属性;
// \$this->form[字段名]['filter'] =  function(\$value){};// 自定义字段提交以后的数据处理
// \$this->form[字段名]['options'] = dict(模型名, 字段名);// 利用字典功能把字段的选项做活
// 建议多了解模型事件、模型获取器、修改器；它们都会成为你开发的利器。可以多搜索下\woo\common\model下的文件里面很多模型都有定义模型事件，去多理解下。
DOC;
            $start->setBody($startBody);

            $event = $trait->addMethod('afterInsertCall');
            $event->addComment("模型事件示范\n自执行时机：新增后\n一共11个，自行查阅文档");
            $event->setPublic();
            $body = <<<DOC
// 调用父类同名方法，防止父类定义的模型事件代码丢失
\$parent_return = parent::{__FUNCTION__}();
// 你的自定义代码 ...

return \$parent_return;
DOC;
            $event->setBody($body);


            $store_path = app()->getBasePath() . "common" . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR
                .  ($data['addon'] ? $data['addon'] . DIRECTORY_SEPARATOR : '')
                . 'traits' . DIRECTORY_SEPARATOR;
            if (!is_dir($store_path)) {
                mkdir($store_path, 0777, true);
            }
            file_put_contents($store_path . $model_name . 'Trait' . '.php', $file);
        }

        $modelData = \woo\common\helper\Model::parseModel($data);
        $file = new PhpFile();
        $file->setStrictTypes();
        $file->addComment("自动生成的模型文件".date('Y-m-d H:i:s')."，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。\n自定义代码应用定义在模型对应的trait文件中【{$model_trait}】");
        $namespace = $file->addNamespace($namespace_name);
        $class = $namespace->addClass($model_name);
        $class->addExtend($extends_name);
        $class->addTrait($model_trait);

        $property = [
            [
                'name' => 'modelId',
                'comment' => '模型ID',
                'visibility' => 'protected'
            ],
            [
                'name' => 'pk',
                'comment' => '数据表主键',
                'visibility' => 'protected'
            ],
            [
                'name' => 'table',
                'comment' => '据表名称',
                'visibility' => 'protected'
            ],
            [
                'name' => 'suffix',
                'comment' => '数据表后缀',
                'visibility' => 'protected'
            ],
            [
                'name' => 'connection',
                'comment' => '数据库配置',
                'visibility' => 'protected'
            ],
            [
                'name' => 'parentModel',
                'comment' => '父模型名',
                'visibility' => 'public'
            ],
            [
                'name' => 'cname',
                'comment' => '模型名称',
                'visibility' => 'public'
            ],
            [
                'name' => 'display',
                'comment' => '主显字段信息',
                'visibility' => 'public'
            ],
            [
                'name' => 'orderType',
                'comment' => '默认排序方式 默认值desc',
                'visibility' => 'public'
            ],
            [
                'name' => 'treeLevel',
                'comment' => '无极限层数',
                'visibility' => 'protected'
            ],
            [
                'name' => 'sortable',
                'comment' => '列表是否开启拖拽排序 需要有list_order字段有效',
                'visibility' => 'public'
            ],
            [
                'name' => 'customData',
                'comment' => '自定义数据',
                'visibility' => 'public'
            ],
            [
                'name' => 'relationLink',
                'comment' => '模型关联信息',
                'visibility' => 'public'
            ]
        ];

        foreach ($property as $pro) {
            if (isset($modelData[$pro['name']])) {
                $class->addProperty($pro['name'])
                    ->setValue($modelData[$pro['name']])
                    ->setComment($pro['comment'])
                    ->setVisibility($pro['visibility']);
            }
        }

        $start = $class->addMethod('start');
        $start->setProtected();
        $form = Str::varExport($modelData['form']);
        $formTrigger = Str::varExport($modelData['formTrigger']);
        $formGroup = Str::varExport($modelData['formGroup']);
        $validate = Str::varExport($modelData['validate']);
        $adminCustomTab = Str::varExport($modelData['adminCustomTab']);

        $businessForm = Str::varExport($modelData['businessForm']);
        $businessFormTrigger = Str::varExport($modelData['businessFormTrigger']);
        $businessValidate = Str::varExport($modelData['businessValidate']);
        $businessCustomTab = Str::varExport($modelData['businessCustomTab']);

        $formScene = Str::varExport($formScene);
        $tableColumns = Str::varExport($modelData['tableColumns']);

        $is_business = isset($data['Field']) && (isset($data['Field']['business_id']) || isset($data['Field']['business_member_id']));

        if ($is_business && !empty($modelData['businessSortable'])) {
            $class->addProperty('businessSortable')
                ->setValue(true)
                ->setComment('中台列表是否开启拖拽排序 需要有list_order字段有效')
                ->setVisibility('public');
        }


        if (!$is_business) {
            $startBody = <<<DOC
parent::{__FUNCTION__}();

/**
模型 字段 属性
*/
\$this->form = $form;

/** 表单分组属性 */
\$this->formGroup = $formGroup;

/** 表单触发器属性 */
\$this->formTrigger = $formTrigger;

/** 表单验证属性 */
\$this->validate = $validate;

/** 定义模型列表table相关 */
//\$this->tableTab = [
//    // 列表主Tab名都应该叫basic
//    'basic' => [
//        'title' => '基本信息',
//        //'model' => 当前Tab对应数据的模型名，默认为当前模型
//        //'list_fields' => 当前Tab需要显示的字段 不设置自动从form属性list键识别
//        //'list_filters' => 当前Tab的搜索规则 不设置自动从form属性list_filter键识别
//        //'tool_bar' => 列表toolBar按钮定义 自定义头部按钮 系统会自动设置新增等操作 
//        //'item_tool_bar' => 列表项目toolBar按钮定义 系统会自动设置修改、删除等操作
//        // 'siderbar' => ['foreign' => 'Demo'] 列表指定一个 侧边栏模型 模型对应关联字段建议不要是list_filters中得字段，会搜索冲突
//        // 'table' => []
//        // 'counter' => [] 当前Tab列表的基础统计配置
//        // ...
//    ],
//    // 更多Tab...
//];

/** 后台自定义列表配置 后台的请求会自动将该属性合并到tableTab中*/
\$this->adminCustomTab = $adminCustomTab;

/** 表单场景*/
\$this->formScene = $formScene;

/** 表结构缓存(模型字段)*/
\$this->tableColumns = $tableColumns;
DOC;
        } else {
            $startBody = <<<DOC
parent::{__FUNCTION__}();

/**
模型 字段 属性
*/
\$this->form = $form;
/**
中台模型 字段 属性
*/
\$this->businessForm = $businessForm;

/** 表单分组属性 */
\$this->formGroup = $formGroup;

/** 表单触发器属性 */
\$this->formTrigger = $formTrigger;
\$this->businessFormTrigger = $businessFormTrigger;

/** 表单验证属性 */
\$this->validate = $validate;
\$this->businessValidate = $businessValidate;

/** 定义模型列表table相关 */
//\$this->tableTab = [
//    // 列表主Tab名都应该叫basic
//    'basic' => [
//        'title' => '基本信息',
//        //'model' => 当前Tab对应数据的模型名，默认为当前模型
//        //'list_fields' => 当前Tab需要显示的字段 不设置自动从form属性list键识别
//        //'list_filters' => 当前Tab的搜索规则 不设置自动从form属性list_filter键识别
//        //'tool_bar' => 列表toolBar按钮定义 自定义头部按钮 系统会自动设置新增等操作 
//        //'item_tool_bar' => 列表项目toolBar按钮定义 系统会自动设置修改、删除等操作
//        // 'siderbar' => ['foreign' => 'Demo'] 列表指定一个 侧边栏模型 模型对应关联字段建议不要是list_filters中得字段，会搜索冲突
//        // 'table' => []
//        // 'counter' => [] 当前Tab列表的基础统计配置
//        // ...
//    ],
//    // 更多Tab...
//];

/** 后台自定义列表配置 后台的请求会自动将该属性合并到tableTab中*/
\$this->adminCustomTab = $adminCustomTab;
\$this->businessCustomTab = $businessCustomTab;

/** 表单场景*/
\$this->formScene = $formScene;

/** 表结构缓存(模型字段)*/
\$this->tableColumns = $tableColumns;
DOC;
        }


        $start->setBody($startBody);
        $store_path = app()->getBasePath() . "common" . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR;
        if ($data['addon']) {
            $store_path .= $data['addon'] . DIRECTORY_SEPARATOR;
        }
        try {
            $file_content = (new Printer)->printFile($file);
        } catch (\Exception $e) {
            throw new \Exception('文件内容生成失败：' . $e->getMessage());
        }

        if (is_file($store_path . $model_name . '.php') && Config::get('woo.is_model_backup')) {
            try {
                copy($store_path . $model_name . '.php', $store_path . $model_name . '_backup' . date('YmdHis') . '.php');
            } catch (\Exception $e) {
                throw new \Exception('原模型文件备份失败，请返回手动处理代码');
            }
        }
        try {
            if (!is_dir($store_path)) {
                mkdir($store_path, 0755, true);
            }
            $result = file_put_contents($store_path . $model_name . '.php', $file);
            if ($result) {
                return $store_path . $model_name . '.php';
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function createAddonController(string $name, string $addon)
    {
        $name = Str::studly($name);
        $addon = Str::snake($addon);
        $namespace = "addons\\{$addon}\\controller";
        $class_name = $namespace ."\\" . $name;
        if (class_exists($class_name)) {
            return false;
        }
        $extends_name = "addons\\{$addon}\\BaseController";
        if (!class_exists($extends_name)) {
            $extends_name = "woo\\common\\addons\\Controller";
        }
        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = $file->addNamespace($namespace);
        $class = $namespace->addClass($name);
        $class->addExtend($extends_name);
        $func = $class->addMethod('index');
        $func->setPublic();

        $store_path = app()->addons->getAddonPath($addon) . "controller" . DIRECTORY_SEPARATOR;
        if (!is_dir($store_path)) {
            mkdir($store_path);
        }

        $result = file_put_contents($store_path . $name . '.php', $file);
        if ($result) {
            return $class_name;
        }
        return false;
    }

    public function createController(string $name, string $addon = '', string $appName = 'admin')
    {
        $name = Str::studly($name);
        $namespace = $this->getNamespace('controller', $addon, $appName);
        $class_name = $namespace ."\\" . $name;
        if (class_exists($class_name)) {
            return false;
        }
        $extends_name = $this->getExtends($class_name, 'controller');

        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = $file->addNamespace($namespace);
        $class = $namespace->addClass($name);
        $class->addExtend($extends_name);
        if ($appName === 'admin' || $appName === 'business') {
            $model = Db::name('Model')->where([
                ['addon', '=', $addon],
                ['model', '=', $name]
            ])->find();

            if ($model) {
                if (!empty($model['tree_level'])) {
                    $class->addTrait('\woo\common\controller\traits\Tree');
                }
            } else {
                $model = false;
            }

            $func = $class->addMethod('index');
            $func->setPublic();
            $func->addComment('列表操作');

            if (!($model && !empty($model['tree_level']))) {
                $func_body = <<<DOC
// 自定义列表的一些业务情况 ...
// 比如需要自定义条件： \$this->local['where'][] = ['字段','符号','值'];// 支持也更多的条件传递方式
// 可以通过 \$this->mdl 获取到当前控制器对应的同名模型实例  \$this->args 获取到url参数
// \$this->mdl->tableTab['basic'] 获取到basic的Tab定义实现动态改变相关数据
// 比如：\$this->mdl->tableTab['basic']['item_tool_bar'][] = [] 实现自定义项目按钮
// 比如：\$this->mdl->tableTab['basic']['tool_bar'][] = [] 实现添加表格头部按钮
// 比如：\$this->mdl->tableTab['basic']['list_fields']= [] 实现独立控制列表字段项
// 比如：\$this->mdl->tableTab['basic']['list_filters']= [] 实现独立控制搜索字段项
// \$this->local['header_title'] = '自定义标题';
// \$this->setHeaderInfo('ex_title', '自定义副标题');
// \$this->setHeaderInfo('ex_title_href', '副标题链接');
// \$this->setHeaderInfo('tip', '自定义网页提示语');
// \$this->addAction('随意的唯一标识', '按钮名', 'URL地址', '类名自定义类名；btn-0到btn-17设置按钮样式', '图标', 排序权重, JS函数名（然后自定义对应的函数名，默认false）);// 自定义顶部按钮
// \$this->assign->addCss('/files/loaders/loaders');// 添加自己的css文件
// \$this->assign->addJs('/js/jquery', true);// 添加自己的js文件 true 表示js加到body结尾 反之加到head中
// \$this->assign->setScriptData('myvar', 'test');// 添加自己的全局Js变量值  js代码中通过 ：woo_script_vars.myvar 获取
// 调用父类方法
return parent::{__FUNCTION__}();
DOC;
            } else {
                $func_body = <<<DOC
// 开启ajax加载下级  如果数据量比较多 可以开启
//\$this->assign->options['is_ajax'] = true;
// 关闭 添加一级分类 按钮
//\$this->local['tool_bar']['create'] = false;
// 关闭 排序一级 分类 按钮
//\$this->local['tool_bar']['sortable'] = false;
// 关闭 添加子分类 按钮
//\$this->local['item_tool_bar']['create_child'] = false;
// 关闭 排序子分类 按钮
//\$this->local['item_tool_bar']['sort_child'] = false;
// 关闭  编辑 按钮
//\$this->local['item_tool_bar']['modify'] = false;
// 关闭删除 子分类 按钮
//\$this->local['item_tool_bar']['delete'] = false;
// 添加一个字段显示 默认只显示 id和标题（主显字段）
\$this->local['fields'] = [
   'children_count' => [
       'title' => '子' . \$this->mdl->cname . '数',
       'templet' => '{{# if (d.children_count> 0){ }}{{d.children_count}}{{#} }}',
       'style' => 'color:#36b368;'
   ]
];
return \$this->showList();
DOC;

            }
            $func->setBody($func_body);

            $func = $class->addMethod('create');
            $func->setPublic();
            $func->addComment('添加操作');
            $func_body = <<<DOC
// 自定义添加的一些业务情况 ...
// 比如需要设置字段的默认值：  \$this->setFormValue('date', date('Y-m-d'));
// 比如需要添加的时候改变表单类型： \$this->mdl->form[字段]['elem'] = '类型'
// 调用父类方法
return parent::{__FUNCTION__}();
DOC;
            $func->setBody($func_body);

            $func = $class->addMethod('modify');
            $func->setPublic();
            $func->addComment('修改操作');
            $func_body = <<<DOC
// 自定义修改的一些业务情况 ...
// 比如需要设置字段的默认值：  \$this->setFormValue('date', date('Y-m-d'));
// 比如需要修改的时候改变表单类型： \$this->mdl->form[字段]['elem'] = '类型'
// 比如需要自定义条件： \$this->local['where'][] = ['字段','符号','值'];
// 调用父类方法
return parent::{__FUNCTION__}();
DOC;
            $func->setBody($func_body);

            $more_func = [
                'delete' => [
                    'comment' => '删除操作'
                ],
                'batchDelete' => [
                    'comment' => '批量删除操作'
                ],
                'detail' => [
                    'comment' => '详情操作'
                ],
                'ajaxSwitch' => [
                    'comment' => '列表开关操作'
                ],
                'deleteIndex' => [
                    'comment' => '回收操作'
                ],
                'restore' => [
                    'comment' => '恢复操作'
                ],
                'batchRestore' => [
                    'comment' => '批量恢复操作'
                ],
                'forceDelete' => [
                    'comment' => '强制删除操作'
                ],
                'forceBatchDelete' => [
                    'comment' => '批量强制删除操作'
                ],
                'sort' => [
                    'comment' => '排序渲染操作'
                ],
                'updateSort' => [
                    'comment' => '排序数据数据提交操作'
                ]
            ];

            $default_body = <<<DOC
return parent::{__FUNCTION__}();
DOC;
            foreach ($more_func as $func_name => $info) {
                $func = $class->addMethod($func_name);
                $func->setVisibility($info['visibility'] ?? 'public');
                $func->addComment($info['comment'] ?? '');
                $func->setBody($info['body'] ?? $default_body);
            }
        }

        $store_path = base_path() . $appName . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR;
        if ($addon) {
            $store_path .= $addon . DIRECTORY_SEPARATOR;
            if (!is_dir($store_path)) {
                mkdir($store_path,0755, true);
            }
        }

        $result = file_put_contents($store_path . $name . '.php', $file);
        if ($result) {
            return $class_name;
        }
        return false;
    }

    protected function getNamespace(string $type = 'controller', string $addon = '', string $appName = 'common')
    {
        return "app\\{$appName}\\{$type}" . ($addon ? "\\" .$addon : "");
    }

    protected function getExtends(string $className, string $type = 'controller')
    {
        $wooName = str_replace("app\\", "woo\\", $className);
        if (class_exists($wooName)) {
            return $wooName;
        }
        $name_explode = explode("\\", $className);
        $app_name = $name_explode[1];
        if ($type == 'controller') {
            return "app\\common\\controller\\" . Str::studly($app_name);
        } elseif ($type == 'model') {
            return "app\\common\\model\\App";
        } else {
            return '';
        }
    }
}