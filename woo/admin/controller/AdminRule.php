<?php
declare (strict_types=1);

namespace woo\admin\controller;

use \app\common\controller\Admin;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Env;
use woo\common\Annotation;
use woo\common\helper\Str;
use woo\common\annotation\Ps;
use ReflectionClass;

class AdminRule extends Admin
{
    use \woo\common\controller\traits\Tree;

    protected $controllerCache = [];
    protected $collection = [];

    public function index()
    {
        $this->local['fields'] = [
            'children_count' => [
                'title' => '子' . $this->mdl->cname . '数',
                'templet' => '{{# if (d.children_count> 0){ }}{{d.children_count}}{{#} }}',
                'style' => 'color:#36b368;'
            ],
            'type' => [
                'title' => '类型',
                'style' => 'color:#01AAED;',
                'templet' => '#options',
                'options' => $this->mdl->form['type']['options'],
            ]
        ];
        // if () {} 判断权限
        $this->assign->options['item_tool_bar'][] = [
            'name' => 'create_btns',
            'title' => '生成按钮',
            'sort' => 1,
            'icon' => 'layui-icon-addition',
            'class' => '',
            'url' => (string) url('createBtns', ['id' => '{{d.' . $this->mdlPk . '}}']),
            'templet' => '#createBtns',
            'js_func' => 'woo_item_tool'
        ];

        $this->local['item_tool_bar']['create_child'] = false;
        $this->assign->options['item_tool_bar'][] = [
            'name' => 'create_child',
            'title' => '新增子菜单',
            'sort' => 40,
            'icon' => 'layui-icon-add-circle',
            'class' => 'woo-layer-load',
            'url' => (string) url('create', ['parent_id' => '{{d.' . $this->mdlPk . '}}']),
            'js_func' => false,
            'templet' => '#createChild',
        ];

        if (Env::get('APP_DEBUG') && Config::get('wooauth.power_reset')) {
            $this->addAction('autocreate', '自动生成规则', (string)url('start'), 'btn-13', 'woo-icon-locus');
        }
        return $this->showList();
    }

    /**
     * @Ps(false)
     * @\woo\common\annotation\Log(false)
     */
    public function getMenu()
    {
        return json($this->mdl->getPearMenu());
    }

    public function create()
    {
        $this->setFormValue('is_nav', 1);
        $this->setFormValue('open_type', '_iframe');
        return parent::{__FUNCTION__}();
    }

    public function getRelationOptions()
    {
        $this->mdl->form['parent_id']['foreign'] = 'AdminRule';
        return  parent::{__FUNCTION__}();
    }

    /**
     * @Ps(name="清空按钮",as="delete")
     */
    protected function resetButton()
    {
        // 把protected改public，然后请求admin_rule/resetButton 可以清空所有按钮来满足你需要重新生成的情况。
        if (!Env::get('APP_DEBUG') || !Config::get('wooauth.power_reset')) {
            return $this->message('不允许清空', 'error');
        }
        set_time_limit(0);
        $list = $this->mdl->where('type', '=', 'button')->select();
        if ($list->isEmpty()) {
            return $this->message('当前还没有按钮，无需清空', 'warn');
        }
        foreach ($list as $item) {
            $item->delete();
        }
        return $this->message('按钮已清空完成', 'success');
    }

    /**
     * @Ps(name="生成按钮")
     */
    public function start()
    {
        $list = array_merge(
            $this->getControllerList(app_path() . 'controller', "app\\admin\\controller"),
            $this->getControllerList(woo_path() . 'admin' . DIRECTORY_SEPARATOR . 'controller', "woo\\admin\\controller")
        );
        if (empty($list)) {
            return $this->message(app_path() . 'controller' . '没有读的权限，控制器列表获取失败', 'error');
        }
        Cache::set('power_controller_list', $list, 7200);

        $this->setHeaderInfo('title', '自动生成规则');
        $this->addAction('return', '返回列表', (string)url('index'), 'btn-2', 'layui-icon layui-icon-return');
        return $this->fetch();
    }

    /**
     * @Ps(as="start")
     */
    public function ajaxReset()
    {
        if (!$this->request->isAjax()) {
            return $this->message('请求方式错误', 'error');
        }

        if (!Env::get('APP_DEBUG')) {
            return $this->ajax('error','非调试阶段不允许操作重置规则', ['break' => true]);
        }

        if (!Config::get('wooauth.power_reset')) {
            return $this->ajax('error','配置文件中设定不允许重置菜单规则', ['break' => true]);
        }

        if (!Cache::has('power_controller_list')) {
            return $this->ajax('error', '控制器列表数据不存在，可能是控制器目录没有可读权限', ['break' => true]);
        }
        $controller_list = Cache::get('power_controller_list');

        $index = intval(trim($this->args['index']));
        $step = 1;
        $min = ($index - 1) * $step;
        $max = $min + ($step - 1);
        $finish = false;

        for ($i = $min; $i <= $max; $i++) {
            if (!isset($controller_list[$i])) {
                $finish = true;
                break;
            }
            $info = $controller_list[$i];
            try {
                $this->setControllerNode($info, -1);
            } catch (\Exception $e) {
                return $this->ajax('error', $e->getMessage(), ['break' => true]);
            }
            $install[] = $info['controller'];
        }
        if (!$finish) {
            $finish = $max + 1 >= count($controller_list) ? true : false;
        }
        return $this->ajax('success', '控制器【' . implode('、', $install) . '】菜单规则已经安装完成', ['finish' => $finish]);
    }

    /**
     * @Ps(false)
     */
    public function writeCache()
    {
        if (!$this->request->isAjax()) {
            return $this->message('请求方式错误', 'error');
        }
        ini_set('memory_limit', '512M');
        admin_rule();
        return $this->ajax('success', '规则数据缓存生成成功');
    }

    /**
     * 获取控制器列表
     * @param string $path
     * @param string $namespace
     * @param string $addon
     * @return array
     */
    protected function getControllerList(string $path, string $namespace, string $addon = '')
    {
        if (is_dir($path) && is_readable($path)) {
            $dir = opendir($path);
            $list = [];
            while (($reader = readdir($dir)) !== false) {
                if ($reader == '.' || $reader == '..') {
                    continue;
                }
                if (strtolower(substr($reader, -4)) == '.php') {
                    $cro = $addon ? $addon . '.' . substr($reader, 0, -4) : substr($reader, 0, -4);
                    $ns = $namespace . "\\" . substr($reader, 0, -4);
                    if (in_array($cro, $this->controllerCache) || in_array($cro, ['Error'])) {
                        continue;
                    }
                    $this->controllerCache[] = $cro;
                    $list[] = [
                        'controller'      => $cro,
                        'addon'           => $addon,
                        'controller_name' => Str::studly(substr($reader, 0, -4)),
                        'namespace'       => $ns
                    ];
                } else {
                    if (empty($addon)) {
                        $list = array_merge($list, $this->getControllerList($path . DIRECTORY_SEPARATOR . $reader, $namespace . "\\" . $reader, $reader));
                    }
                }
            }
            return $list;
        } else {
            return [];
        }
    }

    /**
     * @Ps(as="start")
     */
    public function createBtns()
    {
        $id = $this->args['id'] ?? 0;
        $menu = $this->mdl
            ->where([
                ['id', '=', (int) $id]
            ])
            ->find($id);
        if (empty($menu)) {
            return $this->error('需要生成按钮菜单不存在');
        }
        if ($menu['type']  != 'menu') {
            return $this->error('只有类型为"菜单"才可以生成按钮');
        }
        if (empty($menu['controller'])) {
            return $this->error('当前菜单控制器未填写');
        }
        $namespace = "app\\admin\\controller\\";
        if ($menu['addon']) {
            $namespace .= $menu['addon'] . "\\";
        }
        $namespace .= Str::studly($menu['controller']);
        if (!class_exists($namespace)) {
            $woo_namespace = "woo" .  substr($namespace, 3);
            if (!class_exists($woo_namespace)) {
                return $this->error('控制器【' . $namespace .'】不存在');
            }
            $namespace = $woo_namespace;
        }

        $info = [
            'controller'      => $menu['addon'] ? $menu['addon'] . '.' . Str::studly($menu['controller']) : Str::studly($menu['controller']),
            'addon'           => $menu['addon'],
            'controller_name' => Str::studly($menu['controller']),
            'namespace'       => $namespace,
        ];
        $this->setControllerNode($info, $menu['id']);
        return $this->success('按钮生成成功');
    }

    protected function setControllerNode(array $info, $parent_id = -1)
    {
        $except_methods = ['getRelationOptions', 'version', 'redirectMessage', '__construct', '__get', '__set', '__destruct', '__debugInfo'];
        $main_methods = [
            'index'         => '列表',
            'create'        => '新增',
            'modify'        => '修改',
            'delete'        => '删除',
            'batchDelete'   => '批量删除',
//            'batchVerify'   => '批量审核',
//            'batchDisabled' => '批量禁用',
//            'ajaxSetField'  => '列表设置',
            'ajaxSwitch'    => '列表开关',
            'sort'          => '排序',
            'updateSort'     => '排序',
            'resetSort'     => '重置排序',
            'detail'        => '详情',
            'deleteIndex'   => '回收',
            'restore'       => '恢复',
            'batchRestore'  => '批量恢复',
            'forceDelete'   => '真删除',
            'forceBatchDelete' => '批量真删除'
        ];

        $reader = new Annotation();
        $reflection = reflect($info['namespace']);
        if (!($reflection instanceof ReflectionClass)) {
            return true;
        }
        $model = get_model_name($info['controller']);
        $classPs = $reader->getClassAnnotation($reflection, 'Ps');
        if (empty($classPs)) {
            $classPs = [
                'value' => true,
                'name' => $model ? model($model)->cname : $info['controller_name'],
                'except' => []
            ];
        } else {
            $classPs = (array) $classPs;
        }
        if (empty($classPs['name'])) {
            $classPs['name'] = $model ? model($model)->cname : $model;
        }
        // 不需要加入权限
        if ($classPs['value'] === false) {
            return true;
        }
        $info['title'] = $classPs['name'];
        $classFor = $reader->getClassAnnotation($reflection, 'Forbid', false);

        $methods = [];
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $item) {
            $name = $item->getName();
            if (in_array($name, $except_methods) || in_array($name, $classPs['except'] ?? [])) {
                continue;
            }
            $methods[$name] = $item;
        }
        $nodes = [];
        $except_nodes = [];
        foreach ($main_methods as $method => $title) {
            // 系统自带的数据操作方法 禁止访问
            if ($classFor && true === $classFor->value && $method != 'index') {
                $except_nodes[] = $method;
                continue;
            }
            if ($classFor && $classFor->only && !in_array($method, $classFor->only)) {
                $except_nodes[] = $method;
                continue;
            }
            if ($classFor && $classFor->except && in_array($method, $classFor->except)) {
                $except_nodes[] = $method;
                continue;
            }
            $result = $reader->getMethodAnnotation($reflection, 'Ps', $method);
            if ($result && (false === $result->value || $result->as)) {
                $except_nodes[] = $method;
                continue;
            }
//            if (!$model) {
//                $except_nodes[] = $method;
//                continue;
//            }
            // 不是软删除模型 相应的软删除功能不加入规则
            if (($model && !model($model)->isSoftDelete() || !$model) && in_array($method, ['deleteIndex', 'restore', 'batchRestore', 'forceDelete', 'forceBatchDelete'])) {
                $except_nodes[] = $method;
                continue;
            }
            // 没有排序功能
            if (($model && isset($model->form['list_order']) || !$model) && in_array($method, ['sort', 'updateSort', 'resetSort'])) {
                $except_nodes[] = $method;
                continue;
            }
            $nodes[$method] = [
                'title'      => ($result && $result->name) ? $result->name : $title,
                'addon'      => $info['addon'],
                'controller' => $info['controller_name'],
                'action'     => $method,
            ];
        }

        foreach ($methods as $method => $method_reflection) {
            if (isset($nodes[$method]) || in_array($method, $except_nodes)) {
                continue;
            }
            if ($classFor && $classFor->only && !in_array($method, $classFor->only)) {
                continue;
            }
            if ($classFor && $classFor->except && in_array($method, $classFor->except)) {
                continue;
            }

            $result = $reader->getMethodAnnotation($reflection, 'Ps', $method);
            if ($result && (false === $result->value || $result->as)) {
                $except_nodes[] = $method;
                continue;
            }
            $nodes[$method] = [
                'title'      => $result ? ($result->name ?: Str::studly($method)) : Str::studly($method),
                'addon'      => $info['addon'],
                'controller' => $info['controller_name'],
                'action'     => $method
            ];
        }

        if (!empty($nodes)) {
            foreach ($nodes as &$node) {
                $node['controller'] = Str::snake($node['controller']);
                $node['action'] = strtolower($node['action']);
                $node['type'] = 'button';
                $node['is_nav'] = 1;
                $exists = Db::name('AdminRule')
                    ->where([
                        ['type', '=', 'button'],
                        ['addon', '=', $node['addon']],
                        ['controller', '=', $node['controller']],
                        ['action', '=', $node['action']]
                    ])
                    ->value('id');
                if ($exists) {
                    $node['is_exists'] = 1;
                }
            }

            foreach ($nodes as $node2) {
                $pid = $parent_id;
                if (!empty($node2['is_exists'])) {
                    continue;
                }
                // 自动查找父规则
                if ($pid < 0) {
                    $pid = $this->getParentRuleId($info, $node2);
                };
                $node2['parent_id'] = $pid;
                $mdl = model('AdminRule', '', true);
                $mdl->save($node2);
            }
        }
        return true;
    }

    protected function getParentRuleId($info, $button = [])
    {
        $pid = 1;

        // 先找自己一样的菜单
        if (!empty($button['action'])) {
            $data = Db::name('AdminRule')
                ->where([
                    ['type', '=', 'menu'],
                    ['addon', '=', strtolower($info['addon'])],
                    ['controller', '=', Str::snake($info['controller_name'])],
                    ['action', '=', $button['action']]
                ])
                ->find();
            if (!empty($data)) {
                return $data['id'];
            }
        }
        $data = Db::name('AdminRule')
            ->where([
                ['type', '=', 'menu'],
                ['addon', '=', strtolower($info['addon'])],
                ['controller', '=', Str::snake($info['controller_name'])],
                ['action', '=', 'index']
            ])
            ->find();
        if (!empty($data)) {
            return $data['id'];
        }
        $data = Db::name('AdminRule')
            ->where([
                ['type', '=', 'menu'],
                ['addon', '=', strtolower($info['addon'])],
                ['controller', '=', Str::snake($info['controller_name'])]
            ])
            ->find();
        if (!empty($data)) {
            return $data['id'];
        }
        // 自动归到和父模型同一规则下
        $model = get_model_name($info['controller']);
        if ($model && !empty(model($model)->parentModel) && model($model)->parentModel != 'parent') {
            $parentinfo = [];
            foreach (Cache::get('power_controller_list') as $item) {
                if ($item['controller'] === model($model)->parentModel) {
                    $parentinfo = $item;
                    $parentinfo['title'] = model($model)->cname?: Str::studly($parentinfo['controller_name']);
                    break;
                }
            }
            if ($parentinfo) {
                $id =  $this->getParentRuleId($parentinfo);
                $pid = admin_rule($id, 'parent_id');
            }
        }

        // 是否存在改名称的
        $data = Db::name('AdminRule')
            ->where([
                ['type', '=', 'directory'],
                ['title', '=', $info['title'] ?? Str::studly($info['controller_name'])],
            ])
            ->find();
        if (!empty($data)) {
            return $data['id'];
        }
        // 自动创建一个隐藏的 根目录
        $data = [
            'parent_id' => $pid,
            'type' => 'directory',
            'title' => $info['title'] ?? Str::studly($info['controller_name']),
            'is_nav' => 0 // 隐藏 可以自行去打开
        ];
        $mdl = model('AdminRule', '', true);
        $mdl->save($data);
        return $mdl->id;
    }
}