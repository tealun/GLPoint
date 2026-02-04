<?php
declare (strict_types=1);

namespace woo\common\controller;

use think\App;
use think\Exception;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Env;
use woo\common\annotation\Ps;
use woo\common\Auth;
use app\common\builder\Table;
use woo\common\helper\Str;
use woo\common\View;

/**
 * 系统控制器基础类
 */
abstract class Controller
{
    use traits\Stand;
    /**
     * 系统版本号
     */
    const VERSION = '2.3.4';

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 当前操作模型对象
     */
    public $mdl;

    /**
     * 当前当前对象的主键字段
     */
    public $mdlPk;

    /**
     * 数据传递
     * @var array
     */
    protected $local = [];

    /**
     * 登录信息
     * @var bool
     */
    protected $login = false;

    /**
     * 是否是微信中浏览器打开
     * @var bool
     */
    public $isMeixin = false;

    /**
     * 构造方法
     * @param App $app
     * @throws Exception
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        array_unshift($this->middleware, \woo\common\middleware\AuthCheck::class);
        $this->assign = app(View::class);
        $this->isMeixin = $this->request->isMicroMessenger();
        $this->initialize();
        if (app('http')->getName()) {
            app(\woo\common\Callback::class, [$this])->before();
        }
    }

    /**
     * 初始化
     */
    protected function initialize()
    {
        // 防Host伪造
        if (false == stripos($this->request->url(true), $this->request->host())) {
            throw new Exception('Host check failed');
        }
        // url相关信息
        $this->params = $this->request->getParams();
        $this->params['addon_name'] = $this->addonName ?? $this->params['addon_name'];

        //url参数
        $this->args = $this->params['args'];

        // 当前模型
        if (empty($this->mdl)) {
            $model_name = get_model_name(($this->params['addon_name'] ? $this->params['addon_name'] . '.' : '') . $this->params['controller']);

            if ($model_name) {
                $this->mdl = $model_name;
            }
        }


        if (!empty($this->mdl) && is_string($this->mdl)) {
            $this->mdl = $this->loadModel($this->mdl);
            if ($parent_id = $this->mdl->getParentId()) {
                $this->local['parent_id'] = $parent_id;
            }
        }
        // 当前模型主键
        if (empty($this->mdlPk) && $this->mdl) {
            $this->mdlPk = $this->mdl->getPk();
        }

        if (empty($this->login)) {
            $this->login = (new Auth())->user();
        }
        $this->assign->login = $this->login;
        // 根地址
        $this->assign->root = $this->request->root();
        $this->assign->absroot = $this->request->root(true);
        $this->assign->approot = $this->request->appRoot();
        $this->assign->params = $this->params;
        $this->assign->isDebug = Env::get('APP_DEBUG');
        $this->assign->app_name =  app('http')->getName();
        $this->assign->darkMode =  $this->assign->login && Cookie::has(app('http')->getName() . 'dark-mode')? !!Cookie::get(app('http')->getName() . 'dark-mode'): false;
        $this->assign->setScriptData('wwwroot', $this->assign->root);
        $this->assign->setScriptData('absroot', $this->assign->absroot);
        $this->assign->setScriptData('approot', $this->assign->approot);
        $this->assign->setScriptData('params', $this->params);
        $this->assign->setScriptData('setting', setting_for_js());
        $this->assign->setScriptData('upload_domains', get_upload_domains());
        $this->assign->setScriptData('app_name', $this->assign->app_name);
        $this->assign->setScriptData('custom_icons_prefix', Config::get('woomodel.custom_icons_prefix', ''));
        $this->assign->setScriptData('dark_mode', $this->assign->darkMode);
        $this->assign->now = time();
        $this->assign->is_mobile = $this->assign->isMobile = $this->request->isMobile();
        $this->assign->isMeixin = $this->isMeixin;
        $this->assign->setScriptData('is_mobile', $this->assign->is_mobile);
        $this->assign->setScriptData('is_meixin', $this->assign->isMeixin);
        $this->assign->mdl = $this->mdl;
        $this->assign->is_pear = false; // 兼容其他用于使用表格构建器 不存在这个变量报错
        $this->addTitle(setting('project_title'));
    }

    /**
     * 获取系统版本
     */
    public static function version()
    {
        return static::VERSION;
    }

    protected function getRelationFilter()
    {
        $display = trim($this->args['field'] ?? '');
        $model_name = trim($this->args['model'] ?? '');
        $keyword = trim(strip_tags(addslashes($this->args['keyword'] ?? '')));
        if (!$display || !$model_name) {
            return $this->message('参数model或field不存在', 'error');
        }
        $model = get_model_name($model_name);
        if (!$model) {
            return $this->message("模型{$model_name}不存在", 'error');
        }
        $model = model($model_name);

        if ($keyword != '') {
            $result = null;
            if (isset($this->local['keywordWhereCallback']) && is_callable($this->local['keywordWhereCallback'])) {
                $result = $this->local['keywordWhereCallback']($keyword, $display, $model_name);
            }
            if (empty($result)) {
                if (is_numeric($keyword)) {
                    $this->local['whereRaw'] = ["`{$model->getPk()}` = :id OR `{$display}` LIKE :title", ['id' => $keyword, 'title' => "%{$keyword}%"]];
                } else {
                    $this->local['where'][] = [$display, 'LIKE', "%{$keyword}%"];
                }
            }
        }

        try {
            $data = $model->getPage([
                'withTrashed' => $this->local['withTrashed'] ?? false,// 查询包含删除的数据
                'onlyTrashed' => $this->local['onlyTrashed'] ?? false,// 只查询删除的数据
                'with' => $this->local['with'] ?? [],
                'where' => $this->local['where'] ?? [],
                'whereOr' => $this->local['whereOr'] ?? [],
                'whereColumn' => $this->local['whereColumn'] ?? [],
                'whereTime' => $this->local['whereTime'] ?? [],
                'whereBetweenTime' => $this->local['whereBetweenTime'] ?? [],
                'whereNotBetweenTime' => $this->local['whereNotBetweenTime'] ?? [],
                'whereYear' => $this->local['whereYear'] ?? [],
                'whereMonth' => $this->local['whereMonth'] ?? [],
                'whereWeek' => $this->local['whereWeek'] ?? [],
                'whereDay' => $this->local['whereDay'] ?? [],
                'whereBetweenTimeField' => $this->local['whereBetweenTimeField'] ?? [],
                'field' => $this->local['field'] ?? [$model->getPk(), $display],
                'order' => $this->local['order'] ?? $model->getDefaultOrder(),
                'group' => $this->local['group'] ?? [],
                'having' => $this->local['having'] ?? '',
                'limit' => $this->local['limit'] ?? 10,
                'whereRaw' => $this->local['whereRaw'] ?? [],
                'existsWhere' => $this->local['existsWhere'] ?? []
            ]);
            unset($data['render']);
            $data['field_map'] = ['id' => $model->getPk(), 'display' => $display];
            return $this->ajax('success', '', $data);
        } catch (\Exception $e) {
            return $this->ajax('error', $e->getMessage());
        }
    }

    /**
     * 获取关联信息
     * @return string|\think\response\Json|\think\response\Redirect|void
     * @throws \Exception
     */
    protected function getRelationOptions()
    {
        $field = trim($this->args['field']);
        $keyword = trim(strip_tags(addslashes($this->args['keyword'] ?? '')));
        if (empty($field)) {
            return $this->message('当前请求中field参数不存在', 'error');
        }
        $info = $this->mdl->form[$field] ?? [];
        $foreign = $info['foreign'] ?? '';
        if (empty($foreign)) {
            $foreign = Str::studly(substr($field, 0, -3));
        }
        try {
            $foreign = get_relation($foreign, $this->mdl);
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
        if (!get_model_name($foreign[0])) {
            return $this->message('关联模型[' . $foreign[0] . ']不存在', 'error');
        }
        $model = model($foreign[0]);

        if (!empty($this->args['id']) && app('http')->getName() == 'admin' && get_app('business') && isset($this->mdl->form['business_id']) && isset($model->form['business_id'])) {
            $id = (int) $this->args['id'];
            if ($business_id = $this->mdl->where($this->mdlPk, '=', $id)->value('business_id')) {
                $this->local['where'][] = ['business_id', '=', $business_id];
            }
        }

        $table_tab = $info['foreign_tab'] ?? [];
        $table_tab['is_remove_pk'] = $table_tab['is_remove_pk'] ?? ($model->tableTab['basic']['is_remove_pk'] ?? 0);

        if (!empty($table_tab['where']) && is_array($table_tab['where'])) {
            $this->local['where'] = array_merge($this->local['where'], $table_tab['where']);
        }

        if (empty($table_tab['list_fields'])) {
            $table_tab['list_fields'] = [$model->getPk(), $foreign[1]];
            // 加上商家
            if (isset($model->form['business_id']) && app('http')->getName() == 'admin' && get_app('business')) {
                $table_tab['list_fields']['business_id'] = [
                    'templet' => 'relation',
                ];
            }
        } else {
            if (!in_array($foreign[1], $table_tab['list_fields'])) {
                array_push($table_tab['list_fields'], $foreign[1]);
            }
        }
        if (!isset($table_tab['table']['defaultToolbar'])) {
            $table_tab['table']['defaultToolbar'] = [];
        }

        if (!isset($table_tab['checkbox']) && isset($info['attrs']['data-type'])) {
            $table_tab['checkbox'] = $info['attrs']['data-type'];
        }
        if (!isset($table_tab['checkbox'])) {
            $table_tab['checkbox'] = 'radio';
            if (isset($foreign['type']) && $foreign['type'] == 'belongsToMany') {
                $table_tab['checkbox'] = 'checkbox';
            }
        }

        if (!isset($table_tab['table']['limits'])) {
            $table_tab['table']['limits'] = [10, 20, 30, 40, 50, 100];
        }
        $table_tab['table']['relation_field'] = $field;
        $table_tab['table']['relation_display'] = $foreign[1];
        $table_tab['table']['sortable'] = false;

        $table = new Table($model, ['basic' => $table_tab]);
        if (!empty($this->args['value'])) {
            $table->setAutoCheckedIds($this->args['value']);
        }
        if ($keyword != '') {
            $result = null;
            if (isset($this->local['keywordWhereCallback']) && is_callable($this->local['keywordWhereCallback'])) {
                $result = $this->local['keywordWhereCallback']($keyword, $field, $model);
            }
            if (empty($result)) {
                if (is_numeric($keyword)) {
                    $this->local['whereRaw'] = ["`{$model->getPk()}` = :id OR `{$model->display}` LIKE :title", ['id' => $keyword, 'title' => "%{$keyword}%"]];
                } else {
                    $this->local['where'][] = [$model->display, 'LIKE', "%{$keyword}%"];
                }
            }
        }

        $table->switchTab('basic')->addToolBar([
            'name' => 'relation_select',
            'title' => '选择',
            'sort' => 20,
            'js_func' => 'relation_select',
            'icon' => 'layui-icon-ok',
            'check' => true,
            'class' => 'btn-11 woo-theme-btn'
        ]);
        if ($this->request->isAjax()) {
            $data = $this->getTableData($table);
            if (isset($this->local['afterOptionsData'])) {
                if (is_callable($this->local['afterOptionsData'])) {
                    $result = $this->local['afterOptionsData']($data['data'] ?? [], $field);
                    $data['data'] = $result && is_array($result) ? $result : $data;
                } elseif (is_string($this->local['afterOptionsData']) && method_exists($this, $this->local['afterOptionsData'])) {
                    $result = $this->{$this->local['afterOptionsData']}($data['data'] ?? [], $field);
                    $data['data'] = $result && is_array($result) ? $result : $data;
                }
            }
            return $data;
        }
        $limit = $this->local['limit'] ?? 10;
        $table->setTableAttr('limit', intval($limit));
        $this->assign->table = $table;
        $this->request->isNotStore = true;
        return $this->fetch('relation_list');
    }

    protected function getCascaderData()
    {
        if (!isset($this->args['id'])) {
            return $this->message('缺少参数【id】', 'error');
        }
        $id = intval($this->args['id']);
        if (!isset($this->args['field'])) {
            return $this->message('缺少参数【field】', 'error');
        }
        $field = trim($this->args['field']);
        $info = $this->mdl->form[$field] ?? [];
        $foreign = $info['foreign'] ?? '';
        if (empty($foreign)) {
            $foreign = Str::studly(substr($field, 0, -3));
        }
        try {
            $foreign = get_relation($foreign, $this->mdl);
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
        if (!get_model_name($foreign[0])) {
            return $this->message('关联模型[' . $foreign[0] . ']不存在', 'error');
        }
        $model = model($foreign[0]);
        if (!isset($model->form['parent_id'])) {
            return $this->message('模型没有【parent_id】字段，不能使用该模型', 'error');
        }
        $data = $model->find($id);
        if (empty($data)) {
            return $this->message('数据不存在', 'error');
        }

        $options[$id] = ['parent_id' => $data['parent_id'], 'children' => []];
        $list = $model->where('parent_id', '=', $id)->order($model->getDefaultOrder())->select()->toArray();
        foreach ($list as $item) {
            array_push($options[$id]['children'], [
                'id' => $item[$model->getPk()],
                'title' => $item[$model->display],
                'is_children' => isset($item['children_count']) ?
                    ($item['children_count'] ? true : false) :
                    ($model->where('parent_id', '=', $item[$model->getPk()])->count() ? true : false)
            ]);
        }
        return $this->ajax('success', '数据查询成功', [
            'id' => $id,
            'field' => $field,
            'options' => $options
        ]);
    }

    protected function getTableData($table)
    {
        return $table->getData([
            'withTrashed' => $this->local['withTrashed'] ?? false,// 查询包含删除的数据
            'onlyTrashed' => $this->local['onlyTrashed'] ?? false,// 只查询删除的数据
            'with' => $this->local['with'] ?? [],
            'where' => $this->local['where'] ?? [],
            'whereOr' => $this->local['whereOr'] ?? [],
            'whereColumn' => $this->local['whereColumn'] ?? [],
            'whereTime' => $this->local['whereTime'] ?? [],
            'whereBetweenTime' => $this->local['whereBetweenTime'] ?? [],
            'whereNotBetweenTime' => $this->local['whereNotBetweenTime'] ?? [],
            'whereYear' => $this->local['whereYear'] ?? [],
            'whereMonth' => $this->local['whereMonth'] ?? [],
            'whereWeek' => $this->local['whereWeek'] ?? [],
            'whereDay' => $this->local['whereDay'] ?? [],
            'whereBetweenTimeField' => $this->local['whereBetweenTimeField'] ?? [],
            'field' => $this->local['field'] ?? [],
            'order' => $this->local['order'] ?? [],
            'group' => $this->local['group'] ?? [],
            'having' => $this->local['having'] ?? '',
            'forceCache' => $this->local['forceCache'] ?? null,
            'whereCallback' => $this->local['whereCallback'] ?? false,
            'whereRaw' => $this->local['whereRaw'] ?? [],
            'existsWhere' => $this->local['existsWhere'] ?? [],
            'cancelCheckAdmin' => $this->local['cancelCheckAdmin'] ?? false,
        ]);
    }
}