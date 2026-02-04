<?php
declare (strict_types = 1);

namespace woo\common\controller;

use woo\common\facade\Cache;
use think\facade\Db;
use woo\common\annotation\Forbid;
use woo\common\annotation\Log;
use woo\common\Auth;
use app\common\builder\FormPage;
use woo\common\facade\ThinkApi;
use woo\common\helper\Arr;
use app\common\builder\Table;
use woo\common\helper\Str;
use woo\common\annotation\Ps;

class AdminController extends Controller
{
    /**
     * FormPage 实例
     * @var null
     */
    protected $formPage = null;

    protected $table = null;
    /**
     * FormPage 实例 并不是每个方法都需要的 你可以通过定义 该属性设置哪些方法需要自动创建FormPage实例
     * @var array
     */
    protected $autoCreateFormActions =  ['create', 'modify'];

    /**
     * 头部信息
     * @var array
     */
    protected $headerInfo = [
        'class' => '',
        'title' => '',
        'ex_title' => '',
        'ex_title_href' => '',
        'tip' => ''
    ];

    /**
     * 头部按钮列表
     * @var array
     */
    protected $actionList = [];

    protected function initialize()
    {
        $this->addTitle(setting('admin_title'));
        parent::initialize();
        if (empty($this->formPage)
            && in_array($this->params['action'], $this->autoCreateFormActions)
        ) {
            $this->createFrom();
        }
        $this->assign->admin_info = $this->app->config->get('woo.about');
        $this->assign->is_pear = true;
    }

    /**
     * @Ps(true,name="列表")
     * @Log(only={"ajax"})
     */
    public function index()
    {
        if (empty($this->mdl)) {
            return $this->message('模型不存在，请生成模型以后再试', 'warn');
        }

        if (app('http')->getName() == 'admin' && !$this->request->isAjax() && $this->app->isDebug() && !empty($this->login['AdminGroup']) && $this->login['AdminGroup'][0]['id'] == $this->app->config->get('wooauth.super_group_id')) {
            $model_info = model('Model')
                ->where([
                    ['addon', '=', $this->params['addon_name']],
                    ['model', '=', $this->params['controller']]
                ])
                ->find();
            if ($model_info) {
                $this->assign->model_link_url = (string) url('model/modify', ['id' => $model_info['id'], 'tab_index' => 1, 'reload' => 1]);
                $this->assign->field_link_url = (string) url('field/index', ['parent_id' => $model_info['id'], 'reload' => 1]);
            }
        }

        $this->local['is_form_scene'] = $this->local['is_form_scene'] ?? true;

        // 可以在你给定的tableTab中，设置一个名为 delete_index的tab 用于快速在tab中加上回收站所对应功能 注意：权限还是算在index操作中 而非deleteindex操作
        $this->local['tableTab'] = $this->local['tableTab'] ?? $this->mdl->tableTab;
        if (!isset($this->local['tableTab']['delete_index']) && isset($this->mdl->form['delete_time']) && isset($this->mdl->customData['delete_index']) && admin_link_power('deleteindex')) {
            $this->local['tableTab']['delete_index'] = [
                'title' => '回收站',
                'list_fields' => $this->local['tableTab']['basic']['list_fields'] ?? [],
                'list_filters' => $this->local['tableTab']['basic']['list_filters'] ?? [],
                'siderbar' => $this->local['tableTab']['basic']['siderbar'] ?? [],
            ];
            /*
            $this->mdl->form['delete_time']['list'] = [
                'templet' => 'datetime',
                'width' => 148,
                'style' => 'color:' . setting('table_timestamp_color') . ';'
            ];
            */
            if (!empty($this->local['tableTab']['delete_index']['list_fields'])) {
                $this->local['tableTab']['delete_index']['list_fields'][] = 'delete_time';
            }
        }
        if (!empty($this->local['tableTab']) && array_key_exists('delete_index', $this->local['tableTab'])) {
            $this->local['indexTogetherDelete'] = true;
        }
        if ($this->request->isAjax()) {
            if (isset($this->args['tabname']) && $this->args['tabname'] === 'delete_index') {
                $this->local['onlyTrashed'] = true;
            }
        }
        if (isset($this->local['parent_id']) && $this->local['parent_id'] == 'parent_id' && !empty($this->local['tableTab'][$this->args['tabname'] ?? 'basic']['table']['data'])) {
            $this->local['not_parse_parent'] = true;
        }
        $this->parseParent();

        if ($setAntispam = $this->setAntispam()) {
            if (!empty($setAntispam['verify_field']) && array_key_exists($setAntispam['verify_field'], $this->mdl->form)) {
                $this->mdl->form[$setAntispam['verify_field']]['list'] = 'checker.show';
            }
        }

        // 调用公共列表方法
        return $this->getIndex();
    }

    /**
     * @Ps(true,name="列表选项",as="index")
     */
    public function index2()
    {
        // 目前在“单据明细” 关联模型选项中有使用
        if (empty($this->mdl)) {
            return $this->message('模型不存在，请生成模型以后再试', 'warn');
        }
        $this->mdl->tableTab['basic']['tool_bar'] = [
            [
                'name' => 'relation_orderitem_select_colse',
                'title' => '选择并关闭',
                'sort' => 20,
                'class' => 'btn-2 woo-theme-btn',
                'js_func' => 'relation_orderitem_select_colse',
            ],
            [
                'name' => 'close',
                'title' => '关闭',
                'sort' => 0,
                'class' => 'btn-4',
                'js_func' => 'relation_orderitem_close',
                'icon' => 'layui-icon-close'
            ]
        ];
        if (!isset($this->mdl->tableTab['basic']['table']['defaultToolbar'])) {
            $this->mdl->tableTab['basic']['table']['defaultToolbar'] = [];
        }

        $this->local['callback'] = function () {
            if (!empty($this->args['default_value_123'])) {
                $this->table->setAutoCheckedIds($this->args['default_value_123']);
            }
        };
        $this->request->isNotStore = true;
        // 调用公共列表方法
        $this->local['fetch'] = 'list2';
        return $this->getIndex();
    }

    /**
     * @Ps(true,name="排序")
     */
    public function sort()
    {
        if (!isset($this->mdl->form['list_order'])) {
            return $this->message('模型【' . $this->params['controller'] . '】form属性中不含有list_order字段', 'error');
        }

        if (isset($this->local['parent_id']) && isset($this->args['parent_id'])) {
            $this->local['where'][] = [$this->local['parent_id'], '=', intval($this->args['parent_id'])];
        }
        $limit  = 500;
        if (isset($this->args['limit'])) {
            $limit = intval($this->args['limit']);
        }
        if (empty($this->local['field'])) {
            $this->local['field'] = array_unique([$this->mdlPk, $this->mdl->display, 'list_order']);
        }
        if (in_array($this->mdlPk, $this->local['field']) && array_key_exists($this->mdlPk, $this->local['field'])) {
            array_unshift($this->local['field'], $this->mdlPk);
        }
        $this->local['field'] = Arr::normalize($this->local['field']);
        try {
            $data = $this->mdl->getPage(
                [
                    'with' => $this->local['with'] ?? [],
                    'field' => array_keys($this->local['field']),
                    'where' => $this->local['where'] ?? [],
                    'whereOr' => $this->local['whereOr'] ?? [],
                    'limit' => $limit
                ]
            );
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
        if (empty($data['list'])) {
            return $this->message('当前条件下数据不存在', 'error');
        }

        foreach ($this->local['field'] as $field => &$item) {
            if (!isset($item['name'])) {
                $item['name'] = $this->mdl->form[$field]['name'] ?? Str::studly($field);
            }
        }
        $this->assign->pk = $this->mdlPk;
        $this->assign->fields = $this->local['field'];
        $this->assign->data = $data;

        $this->parseParent();
        // 头部操作
        $this->addAction('return','返回列表', $this->local['return_list_url'] ?? $this->getIndexUrl(), 'layui-btn-normal return-index-btn btn-2', 'layui-icon layui-icon-return', 10);
        $this->addAction('sort','提交排序', (string) url('updateSort'), 'layui-btn-warm', 'woo-icon-paixu', 10, 'woo_table_sort');
        // 头部标题
        $this->local['header_title'] = $this->local['header_title'] ?? $this->mdl->cname . '排序';

        return $this->fetch($this->local['fetch'] ?? 'sort');
    }

    /**
     * @Ps(true,name="新增")
     * @Log(only={"post"})
     */
    public function create()
    {
        if (
            isset($this->local['parent_id'])
            && isset($this->mdl->form[$this->local['parent_id']])
            && !isset($this->mdl->form[$this->local['parent_id']]['elem'])
        ) {
            if (empty($this->args['parent_id'])) {
                $this->mdl->form[$this->local['parent_id']]['elem'] = 'relation';
            } else {
                $this->mdl->form[$this->local['parent_id']]['elem'] = 0;
            }
        }
        // 创建FormPage 实例 -- 正常情况下FormPage实例在initialize方法中已经实例过了  防止万一
        $this->createFrom();
        // 处理表单项目Tab -- 如果需要完全自定义表单Tab 就重写该方法 默认读取当前模型的formGroup属性自动创建
        $this->setFormTab();
        // 自动创建表单项目  -- 如果需要完全自定义表单项目 就重写该方法 默认读取当前模型的form属性自动创建
        $this->createFormItem();
        // 处理表单Tab栅格布局  -- 如果需要完全自定义Tab的栅格布局 就重写该方法  默认不会处理特殊布局的Tab栅格
        $this->setFormGrid();
        // 处理表单项目触发器  -- 根据某个字段的值不同 切换不同表单项的显示与否 默认读取当前模型的formTrigger属性自动处理  也可以重新该方法自行定义触发
        $this->setFormTrigger();

        // 通过URL参数设置默认值
        foreach ($this->args as $field => $value) {
            if ($field == 'parent_id') {
                if (isset($this->mdl->parentModel) && isset($this->mdl->form[$this->local['parent_id']])) {
                    $this->setFormValue($this->local['parent_id'], intval($this->args['parent_id']));
                }
            } else if (isset($this->mdl->form[$field])) {
                $this->setFormValue($field, $value);
            }
        }

        // 创建者
        if (isset($this->mdl->form[$this->login['login_foreign_key']])) {
            $this->setFormValue($this->login['login_foreign_key'], $this->login['id'] ?? 0);
        }

        // 审核字段默认值
        if (isset($this->mdl->form['is_verify']) && empty($this->setAntispam())) {
            $this->setFormValue('is_verify', setting('admin_default_verify'));
        }
        // 最终表单赋值一次  如果前面就直接调用 formPage的setData方法 可能会因为表单项还没有添加 导致赋值失败
        $this->formPage->setData($this->local['form_items_value'] ?? []);

        // 渲染临时保存按钮
        if ($this->local['draftSave'] ?? false) {
            $this->assign->draftSave = true;
            if (is_string($this->local['draftSave'])) {
                $this->assign->draftSaveSubmitText = $this->local['draftSave'];
            }
        }

        // 数据提交
        if ($this->request->isPost() && !isset($this->local['not_save_action'])) {
            // 保存前回调 无特殊情况 不要有返回值 可以通过 $this->local['forceData'] 强制修改数据
            // 确实需要拦截提交 可以 return $this->message进行提示拦截
            if (method_exists($this, 'beforeCreateSave') && $call_result = $this->beforeCreateSave()) {
                return $call_result;
            }
            $result = $this->formPage->setDraftSave(boolval($this->local['draftSave'] ?? false))->setModel($this->mdl)->save([
                'allowField' => $this->local['allowField'] ?? [],
                'forceData' => $this->local['forceData'] ?? [],
                'subversionData' => $this->local['subversionData'] ?? [],
                'forceError' => $this->local['forceError'] ?? [],
            ]);
            $this->local['data'] = $this->formPage->getData();
            if ($result) {
                $this->local['save_success'] = true;
                $this->mdl = $result;
                if (method_exists($this, 'afterCreateSave') && $call_result = $this->afterCreateSave()) {
                    return $call_result;
                }
                return $this->message(($this->local['save_success_message'] ?? '添加成功') . '[ID：' . $result->{$this->mdlPk} .']', 'success', array_merge([
                    $this->local['return_list_text'] ?? '返回列表' => $this->local['return_list_url'] ?? $this->getIndexUrl(),
                    'back' =>  $this->local['return_list_back'] ?? true
                ], $this->local['more_success_btns'] ?? []));
            }
        } else {
            if (isset($this->args['copy_id'])) {
                $copy_id = intval($this->args['copy_id']);
                $copy_data = $this->mdl
                    ->where($this->mdl->getCheckAdminWhere())
                    ->where($this->mdlPk, '=', $copy_id)
                    ->find();
                if ($copy_data) {
                    $copy_data = $copy_data->toArray();
                    // 不允许自动赋值的字段列表
                    $unset_key = array_merge(['create_time', 'update_time', 'delete_time', 'admin_id', 'user_id', 'list_order','business_id','business_member_id', $this->mdlPk], array_keys($this->local['form_items_value'] ?? []), $this->local['copy_unset_field'] ?? []);
                    foreach ($copy_data as $field => $value) {
                        if (in_array($field, $unset_key)) {
                            continue;
                        }
                        $this->formPage->setItemValue($field, $value);
                    }
                }
            }
        }
        // 表单即将assign并渲染，如果formPage还有特殊需求 可以再beforeFormAssign方法中处理
        if (method_exists($this, 'beforeFormAssign')) {
            call_user_func([$this, 'beforeFormAssign']);
        }

        // 头部操作
        $this->addAction('return',$this->local['return_list_title'] ?? '返回列表', $this->local['return_list_url'] ?? $this->getIndexUrl(), 'layui-btn-normal return-index-btn btn-2', 'layui-icon layui-icon-return', 10);

        // 头部标题
        $this->local['header_title'] = $this->local['header_title'] ?? $this->mdl->cname . '新增';

        $this->assign->form = $this->formPage;
        return $this->fetch($this->local['fetch'] ?? 'form');
    }

    /**
     * @Ps(true,name="修改")
     * @Log(only={"post"})
     */
    public function modify()
    {
        if (isset($this->local['id'])) {
            $id = intval($this->local['id']);
        } else {
            $id = isset($this->args['id']) ? intval($this->args['id']) : 0;
        }
        if ($id <= 0) {
            return $this->redirect('create');
        }

        if (empty($this->local['ignore_modify_elem'])) {
            foreach ($this->mdl->form as $field => &$item) {
                if (!isset($item['modify_elem'])) {
                    continue;
                }
                $item['elem'] = $item['modify_elem'];
                unset($item['modify_elem']);
            }
        }
        // 创建FormPage 实例 -- 正常情况下FormPage实例d在initialize方法中已经实例过了  防止万一
        $this->createFrom();
        // 处理表单项目Tab -- 如果需要完全自定义表单Tab 就重写该方法 默认读取当前模型的formGroup属性自动创建
        $this->setFormTab();
        // 自动创建表单项目  -- 如果需要完全自定义表单项目 就重写该方法 默认读取当前模型的form属性自动创建
        $this->createFormItem();
        // 处理表单Tab栅格布局  -- 如果需要完全自定义Tab的栅格布局 就重写该方法  默认不会处理特殊布局的Tab栅格
        $this->setFormGrid();
        // 处理表单项目触发器  -- 根据某个字段的值不同 切换不同表单项的显示与否 默认读取当前模型的formTrigger属性自动处理  也可以重新该方法自行定义触发
        $this->setFormTrigger();

        // 渲染临时保存按钮
        if ($this->local['draftSave'] ?? false) {
            $this->assign->draftSave = true;
            if (is_string($this->local['draftSave'])) {
                $this->assign->draftSaveSubmitText = $this->local['draftSave'];
            }
        }

        if ($this->request->isPost() && !isset($this->local['not_save_action'])) {
            // 保存前回调 无特殊情况 不要有返回值 可以通过 $this->local['forceData'] 强制修改数据
            // 确实需要拦截提交 可以 return $this->message进行提示拦截
            if (method_exists($this, 'beforeModifySave') && $call_result = $this->beforeModifySave()) {
                return $call_result;
            }

            $result = $this->formPage->setDraftSave(boolval($this->local['draftSave'] ?? false))->setModel($this->mdl)->save([
                'allowField' => $this->local['allowField'] ?? [],
                'forceData' => $this->local['forceData'] ?? [],
                'subversionData' => $this->local['subversionData'] ?? [],
                'forceError' => $this->local['forceError'] ?? [],
                'id' => $id
            ]);
            $this->local['data'] = $this->formPage->getData();
            if ($result) {
                $this->local['save_success'] = true;
                $this->mdl = $result;
                if (method_exists($this, 'afterModifySave') && $call_result = $this->afterModifySave()) {
                    return $call_result;
                }
                return $this->message(($this->local['save_success_message'] ?? '修改成功') . '[ID：' . $result->{$this->mdlPk} .']', 'success', array_merge([
                    $this->local['return_list_text'] ?? '返回列表' => $this->local['return_list_url'] ?? $this->getIndexUrl(),
                    'back' =>  $this->local['return_list_back'] ?? true
                ], $this->local['more_success_btns'] ?? []));
            }
        } else {
            $old = $this->mdl
                ->where($this->mdlPk, '=', $id)
                ->where($this->mdl->getCheckAdminWhere())
                ->where($this->local['where'] ?? [])
                ->whereOr($this->local['whereOr'] ?? [])
                ->find();
            if (empty($old)) {
                return $this->message('抱歉！您没有权限修改该条数据或该条数据不存在', 'error', ['去添加' => ['create']]);
            }
            $old = $old->getBelongsToManyFieldsValue();

            $this->local['data'] = $old->toArray();
            $this->formPage->setData($this->local['data']);

            if (!empty($this->local['form_items_value'])) {
                $this->formPage->setData($this->local['form_items_value']);
            }
        }
        // 表单即将assign并渲染，如果formPage还有特殊需求 可以再beforeFormAssign方法中处理
        if (method_exists($this, 'beforeFormAssign')) {
            call_user_func([$this, 'beforeFormAssign']);
        }
        // 头部操作
        $this->addAction('return',$this->local['return_list_title'] ?? '返回列表', $this->local['return_list_url'] ?? $this->getIndexUrl(), 'layui-btn-normal return-index-btn btn-2', 'layui-icon layui-icon-return', 10);
        // 头部标题
        $this->local['header_title'] = $this->local['header_title'] ?? $this->mdl->cname . '修改';

        $this->assign->form = $this->formPage;
        return $this->fetch($this->local['fetch'] ?? 'form');
    }

    /**
     * @Ps(true,as="modify")
     * @Log(only={"post"})
     */
    public function scene()
    {
        $this->local['id'] = isset($this->args['id']) ? intval($this->args['id']) : 0;

        $scene_id = isset($this->args['scene_id']) ? intval($this->args['scene_id']) : 0;
        if ($scene_id <= 0) {
            return $this->message('缺少必须参数scene_id', 'error');
        }
        if (!isset($this->mdl->formScene[$scene_id])) {
            return $this->message('场景表单已不存在', 'error');
        }
        $scene = $this->mdl->formScene[$scene_id];
        if (empty($scene['is_verify'])) {
            return $this->message('场景表单' . $scene['var'] . '已下线', 'error');
        }
        if (empty($scene['fields'])) {
            return $this->message('场景表单' .  $scene['var'] .'还未设置字段列表', 'error');
        }

        $this->local['ignore_modify_elem'] = true;

        $form = [];
        foreach ($scene['fields'] as $item) {
            if (empty($item['field'])) {
                continue;
            }
            $info = $this->mdl->form[$item['field']] ?? [];
            if (isset($info['modify_elem'])) {
                $info['elem'] = $info['modify_elem'];
                unset($info['modify_elem']);
            }
            if (!empty($item['elem']) && $item['elem'] != 'auto') {
                $info['elem'] = $item['modify_elem']?? $item['elem'];
            }
            if (!empty($item['validate']) ) {
                if (array_key_exists('require', $item['validate'])) {
                    $info['require'] = true;
                }
                $validate = [];
                foreach ($item['validate'] as $v => $p) {
                    $validate[$item['field']][] = ['rule' => $p === ''? [$v]: [$v, $p]];
                }
                $info['validate'] = $validate;
            }
            if (!empty($item['more_attrs']) && is_array($item['more_attrs'])) {
                $info = array_merge($info, $item['more_attrs']);
            }
            $form[$item['field']] = $info;
        }

        if (!empty($scene['page_title'])) {
            $this->local['header_title'] =$scene['page_title'];
        }

        if (!empty($scene['page_tip'])) {
            $this->local['header_tip'] = $scene['page_tip'];
        }
        if (!empty($scene['success_message'])) {
            $this->local['save_success_message'] = $scene['success_message'];
        }

        $this->createFrom();
        if (empty($scene['is_tab'])) {
            $this->local['isSetFormTab'] = true;
        }
        if (empty($scene['is_grid'])) {
            $this->local['isSetFormGrid'] = true;
        }
        $this->createFormItem($form);

        return $this->modify();
    }

    /**
     * @Ps(true,name="删除")
     */
    public function delete()
    {
        if (empty($this->local['not_check_method']) && !$this->request->isAjax()) {
            return $this->message('为防止误删，当前操作只能Ajax异步操作；不能浏览器直接访问', 'warn');
        }
        if (!isset($this->args['id'])) {
            return $this->message("缺少【id】参数", 'error');
        }
        $id = intval($this->args['id']);
        $result = $this->mdl->deleteData($id, $this->local['where'] ?? [], $this->local['force'] ?? false);
        if (false === $result) {
            return $this->message($this->mdl->getError()[0] ?? '删除失败', 'error');
        }
        return $this->message("{$this->mdl->cname}[ID:{$id}]删除成功",'success', $this->local['success_redirect'] ?? []);
    }

    /**
     * @Ps(true,name="批量删除",as="delete")
     */
    public function batchDelete()
    {
        if (!$this->request->isPost()) {
            return $this->message('不是一个正确的请求方式', 'error');
        }
        $selected_ids = $this->request->post('selected_id', []);
        if (empty($selected_ids)) {
            return $this->message('没有找到需要被删除的数据', 'error');
        }
        $result = $this->mdl->deleteData($selected_ids, $this->local['where'] ?? [], $this->local['force'] ?? false);
        if (false === $result) {
            return $this->message($this->mdl->getError()[0] ?? '批量删除失败', 'error');
        }

        if ($result['delete_count'] == $result['count']) {
            $msg = "批量删除成功，{$result['delete_count']}条数据被成功删除";
        } else {
            $msg = "批量删除成功，{$result['delete_count']}条数据被成功删除，"  . ($result['count'] - $result['delete_count']) . "条数据因权限不足删除失败";
        }
        return $this->message($msg,'success', $this->local['success_redirect'] ?? []);
    }

    /**
     * @Ps(true,name="详情",as="index")
     */
    public function detail()
    {
        if (!isset($this->args['id'])) {
            return $this->message("缺少【id】参数", 'error');
        }
        $id = intval($this->args['id']);

        $this->local['with'] = [];
        $with_form = [];

        if (!empty($this->local['detail_with'])) {
            $detail_with = Arr::normalize($this->local['detail_with']);
            foreach ($detail_with as $model => $info) {
                if (array_key_exists($model, $this->mdl->relationLink)) {
                    if (!get_model_name($foreign = $this->mdl->relationLink[$model]['foreign'])) {
                        continue;
                    }
                    $this->local['with'][$model] = $info ?: $this->mdl->relationLink[$model];
                    $foreign = model($foreign);
                    $with_form[$model] = [
                        'type'  => $this->mdl->relationLink[$model]['type'],
                        'title' => $foreign->cname,
                        'form'  => $this->parseDetail($foreign->form ?? [], $foreign)
                    ];
                }
            }
        }

        $data = $this->mdl
            ->with($this->mdl->parseWith($this->local['with'] ?? []))
            ->withJoin($this->mdl->parseWith($this->local['withJoin'] ?? []))
            ->field($this->local['field'] ?? true)
            ->where($this->mdlPk, '=', $id)
            ->where($this->mdl->getCheckAdminWhere())
            ->where($this->local['where'] ?? [])
            ->whereOr($this->local['whereOr'] ?? [])
            ->find();
        if (empty($data)) {
            return $this->message("ID为[{$id}]的数据不存在", 'error');
        }
        $data = $data->getBelongsToManyFieldsValue();
        $data = $data->toArray();

        if (method_exists($this, 'detailCallback'))
        {
            $data = $this->detailCallback($data) ?? $data;
        }
        $this->local['data'] = $data;
        $this->assign->data = $data;
        $this->assign->form = $this->parseDetail($this->mdl->form, $this->mdl);
        $this->assign->with_form = $with_form;

        $this->assign->common_templet_file =  \think\facade\Config::get('woo.custom_templet_file');
        $this->assign->woo_templet_file = woo_path() . 'common/builder/table/templet/default.html';

        // 头部操作
        $this->addAction('return',$this->local['return_list_title'] ?? '返回列表', $this->local['return_list_url'] ?? $this->getIndexUrl(), 'layui-btn-normal return-index-btn btn-2', 'layui-icon layui-icon-return', 10);
        // 头部标题
        $this->local['header_title'] = $this->local['header_title'] ?? $this->mdl->cname . "详情[" .strtoupper($this->mdlPk) . ":{$data[$this->mdlPk]}]";

        return $this->fetch($this->local['fetch'] ?? 'detail');
    }

    /**
     * @Ps(true,name="列表开关",as="modify")
     * @Forbid(only={"ajax","post"})
     */
    public function ajaxSwitch()
    {
        $data = $this->request->post();
        if (empty($data['id']) || empty($data['field']) || !isset($data['value'])) {
            return $this->ajax('error',"缺少参数");
        }
        if (!isset($this->mdl->form[$data['field']])) {
            return $this->ajax('error',"模型中不包含{$data['field']}字段");
        }
        $list = $this->mdl->form[$data['field']]['list'] ?? [];
        if (empty($list)) {
            return $this->ajax('error',"字段不允许设置");
        }
        $list = is_array($list) ? ($list['templet'] ?? '') : (string)$list;
        if (strpos($list, 'checker') === false) {
            return $this->ajax('error',"字段不允许设置");
        }
        $origin = $this->mdl
            ->where($this->mdlPk, '=', intval($data['id']))
            ->where($this->mdl->getCheckAdminWhere())
            ->where($this->local['where'] ?? [])
            ->whereOr($this->local['whereOr'] ?? [])
            ->find();
        if (empty($origin)) {
            return $this->ajax('error','数据【ID:' . $data['id'] . '】不存在或不允许设置');
        }
        $origin[$data['field']] = $data['value'];
        try {
            $result = $origin->save();
            if ($result) {
                return $this->ajax('success', '数据[' . $this->mdl->form[$data['field']]['name'] .']设置成功');

            } else {
                return $this->ajax('error', array_values($origin->getError())[0] ?? '设置失败');
            }
        } catch (\Exception $e) {
            return $this->ajax('error', $e->getMessage());
        }
    }

    /**
     * @Ps(true,name="批量开关",as="modify")
     * @Forbid(only={"ajax","post"})
     */
    public function batchSwitch()
    {
        $selected_ids = $this->request->post('selected_id', []);
        if (empty($selected_ids)) {
            return $this->message('没有找到需要被批量设置的数据', 'error');
        }
        if (empty($this->args['field'])) {
            return $this->message('URL中缺少field参数', 'error');
        }
        if (!isset($this->args['value'])) {
            return $this->message('URL中缺少value参数', 'error');
        }
        $field = trim($this->args['field']);
        if (!array_key_exists($field, $this->mdl->form)) {
            return $this->message('当前模型中不存在字段' . $field, 'error');
        }
        $value = intval(!!trim($this->args['value']));
        $key = !!trim($this->args['value']) ? 'yes' : 'no';

        $list = $this->mdl
            ->where($this->mdlPk, 'IN', $selected_ids)
            ->where($this->mdl->getCheckAdminWhere())
            ->where($this->local['where'] ?? [])
            ->whereOr($this->local['whereOr'] ?? [])
            ->select();

        $count = 0;
        foreach ($list as $item) {
            if ($item->isValidate(false)->modifyData([$field => $this->mdl->form[$field]['options'][$key] ?? $value])) {
                $count++;
            }
        }
        if ($count == count($selected_ids)) {
            $msg = "批量设置[". $this->mdl->form[$field]['name'] ."]成功，{$count}条数据被成功设置";
        } else {
            $msg = "批量设置[". $this->mdl->form[$field]['name'] ."]成功，{$count}条数据被成功设置，"  . (count($selected_ids) - $count) . "条数据设置失败";
        }
        return $this->message($msg, 'success');
    }

    /**
     * @Ps(true,name="回收站")
     * @Log(only={"ajax"})
     */
    public function deleteIndex()
    {
        if (!$this->mdl->isSoftDelete()) {
            return $this->message('当前模型非软删除模型，没有回收站功能', 'warm', ['返回列表' => url('index')]);
        }
        if (!isset($this->local['tableTab'])) {
            if (!empty($this->mdl->tableTab)) {
                $basic = array_keys($this->mdl->tableTab)[0];
                $basic_tab = $this->mdl->tableTab[$basic];
                $basic_tab['url'] = '';
                $this->local['tableTab'][$basic] = $basic_tab;
            } else {
                $this->local['tableTab'] = [
                    'basic' => [
                        'title' => '回收站'
                    ]
                ];
            }
        }
        $this->local['tableTab']['basic']['delete_index'] = true;

        if (empty($this->local['tableTab']['basic']['list_fields'])) {
            $this->local['tableTab']['basic']['list_fields'] = array_keys($this->mdl->form);
        }
        $this->mdl->form['delete_time']['list'] = [
            'templet' => 'datetime',
            'width' => 148,
            'style' => 'color:' . setting('table_timestamp_color') . ';'
        ];
        $this->local['onlyTrashed'] = true;
        $this->setHeaderInfo('tip', $this->local['header_tip'] ?? '注：回收站删除功能，将彻底删除数据，无法恢复！');
        $this->local['header_title'] = $this->local['header_title'] ?? $this->mdl->cname . '回收站';
        // 调用公共列表方法
        return $this->getIndex();
    }

    /**
     * @Ps(true,name="回收恢复",as="deleteIndex")
     */
    public function restore()
    {
        if (!$this->mdl->isSoftDelete()) {
            return $this->message('当前模型非软删除模型，不支持该功能', 'error', ['返回列表' => url('index')]);
        }
        if (!isset($this->args['id'])) {
            return $this->message("缺少【id】参数", 'error');
        }
        $id = intval($this->args['id']);
        $result = $this->mdl->restoreData($id, $this->local['where'] ?? []);
        if (false === $result) {
            return $this->message($this->mdl->getError()[0] ?? '恢复数据失败', 'error');
        }
        return $this->message("{$this->mdl->cname}[ID:{$id}]恢复数据成功",'success', $this->local['success_redirect'] ?? []);
    }

    /**
     * @Ps(true,name="批量恢复",as="restore")
     */
    public function batchRestore()
    {
        if (!$this->mdl->isSoftDelete()) {
            return $this->message('当前模型非软删除模型，不支持该功能', 'error', ['返回列表' => url('index')]);
        }
        if (!$this->request->isPost()) {
            return $this->message('不是一个正确的请求方式', 'error');
        }
        $selected_ids = $this->request->post('selected_id', []);
        if (empty($selected_ids)) {
            return $this->message('没有找到需要被恢复的数据', 'error');
        }
        $result = $this->mdl->restoreData($selected_ids, $this->local['where'] ?? []);
        if (false === $result) {
            return $this->message($this->mdl->getError()[0] ?? '批量恢复数据失败', 'error');
        }

        if ($result['restore_count'] == $result['count']) {
            $msg = "批量恢复数据成功，{$result['restore_count']}条数据被成功恢复";
        } else {
            $msg = "批量恢复数据成功，{$result['restore_count']}条数据被成功恢复，"  . ($result['count'] - $result['restore_count']) . "条数据因权限不足恢复失败";
        }
        return $this->message($msg,'success', $this->local['success_redirect'] ?? []);
    }

    /**
     * @Ps(true,name="真删除",as="delete")
     */
    public function forceDelete()
    {
        if (!$this->mdl->isSoftDelete()) {
            return $this->message('当前模型非软删除模型，不支持该功能', 'error', ['返回列表' => url('index')]);
        }
        $this->local['force'] = true;
        return $this->delete();
    }

    /**
     * @Ps(true,name="批量真删除",as="forceDelete")
     */
    public function forceBatchDelete()
    {
        if (!$this->mdl->isSoftDelete()) {
            return $this->message('当前模型非软删除模型，不支持该功能', 'error', ['返回列表' => url('index')]);
        }
        $this->local['force'] = true;
        return $this->batchDelete();
    }

    /**
     * @Ps(true,name="排序",as="sort")
     */
    public function updateSort()
    {
        if (!$this->request->isAjax()) {
            return $this->message('请求方式错误', 'error');
        }
        $data = $this->request->post();
        if (empty($data)) {
            return $this->message('请提交排序数据', 'error');
        }
        if (!isset($this->mdl->form['list_order'])) {
            return $this->message('当前模型不存在"list_order"排序字段', 'error');
        }

        $data = Arr::combine($data['data'], 'from', 'to');
        try {
            $list = $this->mdl
                ->field([$this->mdlPk, 'list_order'])
                ->where($this->mdlPk, 'IN', array_values($data))
                ->where($this->mdl->getCheckAdminWhere())
                ->where($this->local['where'] ?? [])
                ->select()
                ->toArray();
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
        $list = Arr::combine($list, $this->mdlPk, 'list_order');
        $change = [];
        foreach ($data as $from => $to) {
            if (isset($list[$to])) {
                $change[] = [
                    $this->mdlPk => $from,
                    'list_order' => $list[$to]
                ];
            }
        }
        try {
            $table = $this->mdl->getTable();
            foreach ($change as $item) {
                Db::table($table)->save($item);
            }
            $this->mdl->clearModelTagCache();
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
        return $this->message('数据排序更新成功！','success');
    }

    /**
     * @Ps(true,name="重置排序",as="sort")
     */
    public function resetSort()
    {
        if (!$this->request->isAjax()) {
            // return $this->message('请求方式错误', 'error');
        }
        if (!isset($this->mdl->form['list_order'])) {
            return $this->message('当前模型不存在"list_order"排序字段', 'error');
        }
        $where = [];
        if (isset($this->args['parent_id'])) {
            $where[] = [$this->local['parent_id'], '=', $this->args['parent_id']];
        }
        if (empty($where)) {
            $where = '1=1';
        }
        try {
            Db::table($this->mdl->getTable())
                ->where($where)
                ->update(['list_order' => Db::raw($this->mdl->getPk())]);
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
        $this->mdl->clearModelTagCache();
        return $this->message('排序重置成功', 'success');
    }

    /**
     * @Ps(true,name="清空数据",as="delete")
     */
    protected function clearData()
    {
        if (!empty($this->local['beforeTime']) && array_key_exists('create_time', $this->mdl->form)) {
            $this->local['where'][] = ['create_time', '<=', time() - intval($this->local['beforeTime'])];
            $msg = "已经清空" . round($this->local['beforeTime'] / 86400) . "天之前的数据";
        }
        try {
            \think\facade\Db::table($this->mdl->getTable())
                ->where($this->local['where'] ?? [[$this->mdlPk, '>=', 1]])
                ->where($this->mdl->getCheckAdminWhere())
                ->delete();
            $this->mdl->clearModelTagCache();
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        return $this->success($msg ?? '数据已清空');
    }

    /**
     * @Ps(name="内容审核")
     */
    protected function antispam()
    {
        $id = intval($this->local['id'] ?? ($this->args['id'] ?? 0));
        $antispam = $this->setAntispam();
        if (empty($antispam) || !is_array($antispam) || empty($antispam['content_fields'])) {
            return $this->error('未通过【setAntispam】配置正确内容审核参数');
        }
        $data = $this->mdl
            ->where($this->local['where'] ?? [])
            ->where($this->mdlPk, '=', $id)
            ->where($this->mdl->getCheckAdminWhere())
            ->find();

        if (empty($data)) {
            return $this->error('需要审核的数据不存在');
        }
        $checkData = $data->toArray();

        if (isset($this->local['mergeData'])) {
            $checkData = array_merge($checkData, $this->local['mergeData']);
        }

        if (!empty($antispam['verify_field']) && !empty($checkData[$antispam['verify_field']])) {
            return $this->success('当前数据已经审核通过，可以编辑取消审核以后再来');
        }
        $content = [];
        foreach ((array)$antispam['content_fields'] as $field) {
            array_push($content, $checkData[$field] ?? '');
        }
        $content = trim(preg_replace('/\s/', '', strip_tags(implode('', $content))));

        // 可更改检查场景
        $result = ThinkApi::thinkAudit($content, $this->local['type'] ?? '1,2,3,4,5,6,7');

        if ($result['code'] == 0) {
            $store = [
                'title' => get_base_class($this->mdl),
                'foreign_id' => $id,
                'content' => mb_substr($content, 0, 5000),
                'is_verify' => !empty($result['data']['pass']) ? 1 : 0,
                'result' => $result,
                'words' => isset($result['data']['result']) ? $result['data']['result'] : '',
                'msg' => isset($result['message']) ? $result['message'] : '',
            ];
            if (isset($this->mdl->form[$this->login['login_foreign_key']])) {
                $store[$this->login['login_foreign_key']] = $this->login['id'] ?? 0;
            }

            model('Antispam')->createData($store);

            if (!empty($result['data']['pass'])) {
                $my_result = null;
                if (!empty($antispam['verify_field'])) {
                    $my_result = $data->modifyData([$antispam['verify_field'] => 1]);
                }

                if (isset($antispam['verify_callback']) && is_callable($antispam['verify_callback'])) {
                    $call_result = call_user_func_array($antispam['verify_callback'], [$result, $antispam]);
                    if ($call_result === true) {
                        return $this->success('内容审核验证通过');
                    } elseif (is_string($call_result)) {
                        return $this->success($call_result);
                    }
                }
                if (isset($my_result)) {
                    if ($my_result) {
                        return $this->success('内容审核验证通过');
                    }
                    return $this->error('内容审核验证通过，但是' . array_values($data->getError())[0] ?? '数据写入失败');
                } else {
                    return $this->success('内容审核验证通过');
                }
            } else {
                return $this->error('内核审核不通过，请查看审核记录了解详情');
            }
        } else {
            return $this->error($result['message']);
        }
    }

    protected function setAntispam()
    {
        return false;
    }

    /**
     * @Ps(false)
     */
    public function getRelationFilter()
    {
        return parent::{__FUNCTION__}();
    }

    /**
     * @Ps(false)
     */
    public function getRelationOptions()
    {
        return parent::{__FUNCTION__}();
    }

    /**
     * @Ps(false)
     */
    public function getCascaderData()
    {
        return parent::{__FUNCTION__}();
    }

    protected function getIndex()
    {
        // 创建Table构建器实例  可以通过$this->local['tableTab'] 设置tab相关信息 默认读取模型的tableTab属性
        $table_tab = $this->local['tableTab'] ?? $this->mdl->tableTab;

        // 20230213 新增表单场景
        if (!empty($this->local['is_form_scene']) && !empty($this->mdl->formScene)) {
            $this->local['load_type'] = $this->local['load_type'] ?? '';

            $item_tool_bar = $table_tab['basic']['item_tool_bar'] ?? [];
            $item_tool_bar = Arr::combine($item_tool_bar, 'name');

            foreach ($this->mdl->formScene as $item) {
                if (!in_array(app('http')->getName(), (array) $item['app']) || empty($item['is_btn']) || empty($item['is_verify'])) {
                    continue;
                }
                $tool = [
                    'name'  => $item['var'],
                    'title' => $item['title'],
                    'url' => (string)url($item['action'] ?: 'scene', ['id' => '{{d.' . $this->mdlPk . '}}', 'scene_id' => $item['id']]),
                    'class' => $item['class'] . ' woo-layer-load ' . (is_array($this->local['load_type']) ? ($this->local['load_type']['form_scene'] ?? '') : $this->local['load_type']),
                    'icon'  => $item['icon'],
                    'sort'  => $item['list_order'],
                    'power'  => $item['action']?: 'scene',
                    'hover' => $item['hover'],
                    'where' => $item['where'],
                    'where_type' => $item['where_type'] ?: 'disabled',
                    'attrs' => $item['attrs']?: [],
                ];

                if (!empty($item['parent']) && !isset($item_tool_bar[$item['parent']])) {
                    $item_tool_bar[$item['parent']] = [
                        'name'  => $item['parent'],
                        'title' => $item['parent'] == 'more'? '更多': $item['parent'],
                        'sort'  => 0,
                        'children' => [],
                        'class' => 'btn-23'
                    ];
                }
                if (empty($item['parent'])) {
                    $item_tool_bar[] = $tool;
                } else {
                    $item_tool_bar[$item['parent']]['children'][] = $tool;
                }
            }
            $item_tool_bar = array_values($item_tool_bar);
            $table_tab['basic']['item_tool_bar'] = $item_tool_bar;
        }

        $this->table = new Table($this->mdl, $table_tab);

        if (!$this->request->isAjax()) {
            // 设置表格 系统默认只会给basic进行自动处理，其他tab请自行设置
            // 不建议通过该方法动态设置表格，建议直接在$this->local['tableTab'] 或 模型tableTab属性中把所有表格信息全部配置好
            // 你可以通过重载该方法实现覆盖和自定义
            if ($this->params['action'] == 'index') {
                $this->setTableAttr();
                if (!empty($this->local['indexTogetherDelete'])) {
                    $this->setTableAttrForDelete('delete_index');
                }
            } elseif (strtolower($this->params['action']) == 'deleteindex') {
                $this->setTableAttrForDelete();
            } else {
                $this->setTableAttrForOther();
            }
            // 默认每页条数
            $this->table->switchTab('basic')->setTableAttr('limit', intval($this->local['limit'] ?? setting('admin_page_limit')));
        }

        // table即将assign并渲染，如果table还有特殊需求 可以再beforeTableAssign方法中处理
        if (method_exists($this, 'beforeTableAssign')) {
            call_user_func([$this, 'beforeTableAssign']);
        }

        if (isset($this->local['callback'])) {
            if (is_callable($this->local['callback'])) {
                $this->local['callback']();
            } elseif (is_string($this->local['callback']) && method_exists($this, $this->local['callback'])) {
                $this->{$this->local['callback']}();
            }
        }


        if ($this->request->isAjax()) {
            // 可以通过$this->args['tabname'] 识别到当前tab 进行不同逻辑操作 如果tab有单独设置url 就对应url处理
            // 不支持ajax中动态指定limit 需要提前指定好limit 或由用户自己选择limit

            $tabname = $this->args['tabname'] ?? 'basic';
            if (!empty($table_tab[$tabname]['list_with']) && empty($this->local['with'])) {
                $this->local['with'] = $table_tab[$tabname]['list_with'];
            }

            // treetable 异步加载
            if (!empty($table_tab[$tabname]['table']['treetable'])  && !empty($table_tab[$tabname]['table']['tree']['async']['enable'])) {
                $this->local['where'][] = ['parent_id', '=', intval($this->args['pid'] ?? 0)];
            }


            $this->local['tableData'] =  $this->getTableData($this->table);
            // $this->local['tableData'] 获取到列表数据 通过$this->local['afterData']指定一个回调 在数据返回前拦截处理数据 可以参考Model控制器就有使用它拦截给数据加字段
            if (isset($this->local['afterData'])) {
                if (is_callable($this->local['afterData'])) {
                    $this->local['afterData']();
                } elseif (is_string($this->local['afterData']) && method_exists($this, $this->local['afterData'])) {
                    $this->{$this->local['afterData']}();
                }
            }
            return json($this->local['tableData']);
        }
        // 头部标题
        $this->local['header_title'] = $this->local['header_title'] ?? $this->mdl->cname . '列表';

        $this->assign->table = $this->table;
        return $this->fetch($this->local['fetch'] ?? 'list');
    }

    /**
     * 系统默认的设置表格信息 你可以通过重载该方法实现覆盖和自定义
     */
    protected function setTableAttr()
    {
        $this->local['tableTab'] = $this->table->getTab();
        //$basic = array_keys($this->local['tableTab'])[0];
        $this->local['load_type'] = $this->local['load_type'] ?? '';

        foreach ($this->local['tableTab'] as $tabname => $tabinfo) {
            if (!empty($tabinfo['model']) || $tabname == 'delete_index') {
                continue;
            }
            // 只会给当前控制器对象模型的tab自动加按钮工具 其他模型 自行配置
            if ($this->local['tool_bar']['batch_delete'] ?? true && isset($this->mdl->customData['batch_delete']) && !$this->table->switchTab($tabname)->isToolBarExists('batch_delete')) {
                $this->table->switchTab($tabname)->addToolBar([
                    'name' => 'batch_delete',
                    'title' => $this->local['tool_bar']['batch_delete']['title'] ?? '删除',
                    'sort' => $this->local['tool_bar']['batch_delete']['sort'] ?? 20,
                    'js_func' => $this->local['tool_bar']['batch_delete']['js_func'] ?? 'woo_batch_delete',
                    'icon' => $this->local['tool_bar']['batch_delete']['icon'] ?? 'woo-icon-feijiuhuishou',
                    'tip' => $this->local['tool_bar']['batch_delete']['tip'] ?? '',
                    'class' => 'layui-btn-danger ' . ($this->local['tool_bar']['batch_delete']['class'] ?? ''),
                    'url' => (string)url('batchDelete'),
                    'power' => 'batchdelete',
                    'check' => true,
                    'attrs' => $this->local['tool_bar']['batch_delete']['attrs'] ?? false
                ]);
            }
            if ($this->local['tool_bar']['create'] ?? true && isset($this->mdl->customData['create']) && !$this->table->switchTab($tabname)->isToolBarExists('create')) {
                $this->table->switchTab($tabname)->addToolBar([
                    'name' => 'create',
                    'title' => $this->local['tool_bar']['create']['title'] ?? '新增',
                    'sort' => $this->local['tool_bar']['create']['sort'] ?? 40,
                    'icon' => $this->local['tool_bar']['create']['icon'] ?? 'layui-icon-add-1',
                    'class' => 'woo-theme-btn woo-layer-load ' . ($this->local['tool_bar']['create']['class'] ?? '') . ' ' . (is_array($this->local['load_type']) ? ($this->local['load_type']['create'] ?? '') : $this->local['load_type']),
                    'url' => (string)url('create', $this->args),
                    'power' => 'create',
                    'attrs' => $this->local['tool_bar']['create']['attrs'] ?? false
                ]);
            }
            if ($this->local['item_tool_bar']['detail'] ?? true && isset($this->mdl->customData['detail']) && !$this->table->switchTab($tabname)->isItemToolBarExists('detail')) {
                $this->table->switchTab($tabname)->addItemToolBar([
                    'name' => 'detail',
                    'title' => $this->local['item_tool_bar']['detail']['title'] ?? '',
                    'sort' => $this->local['item_tool_bar']['detail']['sort'] ?? 20,
                    'icon' => $this->local['item_tool_bar']['detail']['icon'] ?? 'woo-icon-visible',
                    'class' => 'btn-38 woo-layer-load ' . ($this->local['item_tool_bar']['detail']['class'] ?? '') . ' ' . (is_array($this->local['load_type']) ? ($this->local['load_type']['detail'] ?? '') : $this->local['load_type']),
                    'url' => (string)url('detail', ['id' => '{{d.' . $this->mdlPk . '}}']),
                    'power' => 'detail',
                    'hover' => $this->local['item_tool_bar']['detail']['hover'] ?? '详情',
                    'where' => $this->local['item_tool_bar']['detail']['where'] ?? '',
                    'where_type' => $this->local['item_tool_bar']['detail']['where_type'] ?? 'disabled',
                    'attrs' => $this->local['item_tool_bar']['detail']['attrs'] ?? false
                ]);
            }
            if ($this->local['item_tool_bar']['delete'] ?? true && isset($this->mdl->customData['delete']) && !$this->table->switchTab($tabname)->isItemToolBarExists('delete')) {
                $this->table->switchTab($tabname)->addItemToolBar([
                    'name' => 'delete',
                    'title' => $this->local['item_tool_bar']['delete']['title'] ?? '',
                    'sort' => $this->local['item_tool_bar']['delete']['sort'] ?? 60,
                    'js_func' => $this->local['item_tool_bar']['delete']['js_func'] ?? 'woo_delete',
                    'tip' => $this->local['item_tool_bar']['delete']['tip'] ?? '',
                    'icon' => $this->local['item_tool_bar']['delete']['icon'] ?? 'layui-icon-delete',
                    'class' => 'btn-25 ' . ($this->local['item_tool_bar']['delete']['class'] ?? ''),
                    'url' => (string)url('delete', ['id' => '{{d.' . $this->mdlPk . '}}']),
                    'power' => 'delete',
                    'hover' => $this->local['item_tool_bar']['delete']['hover'] ?? '删除',
                    'where' => $this->local['item_tool_bar']['delete']['where'] ?? '',
                    'where_type' => $this->local['item_tool_bar']['delete']['where_type'] ?? 'disabled',
                    'attrs' => $this->local['item_tool_bar']['delete']['attrs'] ?? false
                ]);
            }
            if ($this->local['item_tool_bar']['modify'] ?? true && isset($this->mdl->customData['modify']) && !$this->table->switchTab($tabname)->isItemToolBarExists('modify')) {
                $this->table->switchTab($tabname)->addItemToolBar([
                    'name' => 'modify',
                    'title' => $this->local['item_tool_bar']['modify']['title'] ?? '',
                    'sort' => $this->local['item_tool_bar']['modify']['sort'] ?? 80,
                    'icon' => $this->local['item_tool_bar']['modify']['icon'] ?? 'woo-icon-xiugai',
                    'class' => 'btn-22 woo-layer-load ' . ($this->local['item_tool_bar']['modify']['class'] ?? '') . ' ' . (is_array($this->local['load_type']) ? ($this->local['load_type']['modify'] ?? '') : $this->local['load_type']),
                    'url' => (string)url('modify', ['id' => '{{d.' . $this->mdlPk . '}}']),
                    'power' => 'modify',
                    'hover' => $this->local['item_tool_bar']['modify']['hover'] ?? '编辑',
                    'where' => $this->local['item_tool_bar']['modify']['where'] ?? '',
                    'where_type' => $this->local['item_tool_bar']['modify']['where_type'] ?? 'disabled',
                    'attrs' => $this->local['item_tool_bar']['modify']['attrs'] ?? false
                ]);
            }
            if ($this->local['item_tool_bar']['copy'] ?? true && isset($this->mdl->customData['copy']) && !$this->table->switchTab($tabname)->isItemToolBarExists('copy')) {
                $this->table->switchTab($tabname)->addItemToolBar([
                    'name' => 'copy',
                    'title' => $this->local['item_tool_bar']['copy']['title'] ?? '',
                    'sort' => $this->local['item_tool_bar']['copy']['sort'] ?? 40,
                    'icon' => $this->local['item_tool_bar']['copy']['icon'] ?? 'woo-icon-fuzhi',
                    'class' => 'btn-27 woo-layer-load ' . ($this->local['item_tool_bar']['copy']['class'] ?? '') . ' ' . (is_array($this->local['load_type']) ? ($this->local['load_type']['copy'] ?? '') : $this->local['load_type']),
                    'url' => (string)url('create', ['copy_id' => '{{d.' . $this->mdlPk . '}}']),
                    'power' => 'create',
                    'hover' => $this->local['item_tool_bar']['copy']['hover'] ?? '复制',
                    'where' => $this->local['item_tool_bar']['copy']['where'] ?? '',
                    'where_type' => $this->local['item_tool_bar']['copy']['where_type'] ?? 'disabled',
                    'attrs' => $this->local['item_tool_bar']['copy']['attrs'] ?? false
                ]);
            }

            if ($this->setAntispam() && class_exists(\think\api\Client::class)) {
                $this->table->switchTab($tabname)->addItemToolBar([
                    'name' => 'antispam',
                    'title' => $this->setAntispam()['btn_text'] ?? '',
                    'sort' => $this->setAntispam()['btn_sort'] ?? 19,
                    'icon' => $this->setAntispam()['btn_icon'] ?? 'layui-icon-vercode',
                    'js_func' => 'woo_item_tool',
                    'class' => $this->setAntispam()['btn_class'] ?? 'btn-37',
                    'url' => (string)url('antispam', ['id' => '{{d.' . $this->mdlPk . '}}']),
                    'power' => 'antispam',
                    'hover' => $this->setAntispam()['btn_hover'] ?? '内容检测',
                    'where' => $this->setAntispam()['btn_where'] ?? '',
                    'where_type' => $this->setAntispam()['btn_where_type'] ?? 'disabled',
                    'attrs' => $this->setAntispam()['attrs'] ?? false
                ]);
            }
        }
    }

    protected function setTableAttrForDelete()
    {
        if (0 === func_num_args()) {
            $this->local['tableTab'] = $this->table->getTab();
            $basic = array_keys($this->local['tableTab'])[0];
        } else {
            $basic = func_get_arg(0);
        }

        if ($this->local['delete_index_tool_bar']['batch_delete'] ?? true) {
            $this->table->switchTab($basic)->addToolBar([
                'name' => 'batch_delete',
                'title' => $this->local['delete_index_tool_bar']['batch_delete']['title'] ?? '删除',
                'sort' => $this->local['delete_index_tool_bar']['batch_delete']['sort'] ?? 20,
                'js_func' => $this->local['delete_index_tool_bar']['batch_delete']['js_func'] ?? 'woo_batch_delete',
                'icon' => $this->local['delete_index_tool_bar']['batch_delete']['icon'] ?? 'woo-icon-feijiuhuishou',
                'tip' => $this->local['delete_index_tool_bar']['batch_delete']['tip'] ?? '',
                'class' => 'layui-btn-danger ' . ($this->local['delete_index_tool_bar']['batch_delete']['class'] ?? ''),
                'url' => (string)url('forceBatchDelete'),
                'power' => 'forceBatchDelete',
                'check' => true,
                'attrs' => $this->local['delete_index_tool_bar']['batch_delete']['attrs'] ?? false
            ]);

        }

        if ($this->local['delete_index_tool_bar']['restore'] ?? true) {
            $this->table->switchTab($basic)->addToolBar([
                'name' => 'batch_restore',
                'title' => $this->local['delete_index_tool_bar']['batch_restore']['title'] ?? '恢复',
                'sort' => $this->local['delete_index_tool_bar']['batch_restore']['sort'] ?? 40,
                'js_func' => $this->local['delete_index_tool_bar']['batch_restore']['js_func'] ?? 'woo_batch_restore',
                'icon' => $this->local['delete_index_tool_bar']['batch_restore']['icon'] ?? 'layui-icon-release',
                'tip' => $this->local['delete_index_tool_bar']['batch_restore']['tip'] ?? '',
                'class' => 'woo-theme-btn ' . ($this->local['delete_index_tool_bar']['batch_restore']['class'] ?? ''),
                'url' => (string)url('batchRestore'),
                'power' => 'batchRestore',
                'check' => true,
                'attrs' => $this->local['delete_index_tool_bar']['batch_restore']['attrs'] ?? false
            ]);
        }

        if ($this->local['delete_index_item_tool_bar']['delete'] ?? true) {
            $this->table->switchTab($basic)->addItemToolBar([
                'name' => 'delete',
                'title' => $this->local['delete_index_item_tool_bar']['delete']['title'] ?? '',
                'sort' => $this->local['delete_index_item_tool_bar']['delete']['sort'] ?? 40,
                'js_func' => $this->local['delete_index_item_tool_bar']['delete']['js_func'] ?? 'woo_delete',
                'tip' => $this->local['delete_index_item_tool_bar']['delete']['tip'] ?? '',
                'icon' => $this->local['delete_index_item_tool_bar']['delete']['icon'] ?? 'layui-icon-delete',
                'class' => 'btn-25 ' . ($this->local['delete_index_item_tool_bar']['delete']['class'] ?? ''),
                'url' => (string) url('forceDelete', ['id' => '{{d.' . $this->mdlPk  . '}}']),
                'hover' => '真删除',
                'power' => 'forceDelete',
                'where' => $this->local['delete_index_item_tool_bar']['delete']['where'] ?? '',
                'where_type' => $this->local['delete_index_item_tool_bar']['delete']['where_type'] ?? 'disabled',
                'attrs' => $this->local['delete_index_tool_bar']['delete']['attrs'] ?? false
            ]);
        }

        if ($this->local['delete_index_item_tool_bar']['restore'] ?? true) {
            $this->table->switchTab($basic)->addItemToolBar([
                'name' => 'restore',
                'title' => $this->local['delete_index_item_tool_bar']['restore']['title'] ?? '',
                'sort' => $this->local['delete_index_item_tool_bar']['restore']['sort'] ?? 60,
                'js_func' => $this->local['delete_index_item_tool_bar']['restore']['js_func'] ?? 'woo_restore',
                'tip' => $this->local['delete_index_item_tool_bar']['restore']['tip'] ?? '',
                'icon' => $this->local['delete_index_item_tool_bar']['restore']['icon'] ?? 'layui-icon-release',
                'class' => $this->local['delete_index_item_tool_bar']['restore']['class'] ?? 'btn-22',
                'url' => (string) url('restore', ['id' => '{{d.' . $this->mdlPk  . '}}']),
                'hover' => '恢复',
                'power' => 'restore',
                'where' => $this->local['delete_index_item_tool_bar']['restore']['where'] ?? '',
                'where_type' => $this->local['delete_index_item_tool_bar']['restore']['where_type'] ?? 'disabled',
                'attrs' => $this->local['delete_index_tool_bar']['delete']['restore'] ?? false
            ]);
        }
    }

    protected function setTableAttrForOther()
    {
        $this->local['tableTab'] = $this->table->getTab();
        $basic = array_keys($this->local['tableTab'])[0];
    }

    /**
     * 自动创建表单项目  -- 如果需要完全自定义表单项目 就重写该方法 默认读取当前模型的form属性自动创建
     * @param array $form  表单项目数组 默认读取模型的form属性 可以自定义 格式参考模型的form属性
     * @return $this
     */
    protected function createFormItem(array $form = [])
    {
        if (!empty($this->local['isCreateFormItem'])) {
            return $this;
        }
        $this->local['rsa'] = array_unique(array_merge($this->mdl->getRsaFields($form), $this->local['rsa'] ?? []));
        $this->formPage->createFormItem($form, $this->local['fields_list'] ?? []);
        $this->local['isCreateFormItem'] = true;
        return $this;
    }

    /**
     * 处理表单项目Tab -- 如果需要完全自定义表单Tab 就重写该方法 默认读取当前模型的formGroup属性自动创建
     * @param array $groups 表单分组 默认自动读取 模型的gormGroup属性
     * @return $this
     */
    protected function setFormTab(array $groups = [])
    {
        if (!empty($this->local['isSetFormTab'])) {
            return $this;
        }
        $this->formPage->setFormTab($groups);
        $this->local['isSetFormTab'] = true;
        return $this;
    }

    /**
     * 处理表单Tab栅格布局  -- 如果需要完全自定义Tab的栅格布局 就重写该方法  默认不会处理特殊布局的Tab栅格 只会简单分组设置
     * @return $this
     */
    protected function setFormGrid()
    {
        if (!empty($this->local['isSetFormGrid'])) {
            return $this;
        }
        $this->formPage->setFormGrid();
        $this->local['isSetFormGrid'] = true;
        return $this;
    }

    /**
     * 处理表单项目触发器  -- 根据某个字段的值不同 切换不同表单项的显示与否 默认读取当前模型的formTrigger属性自动处理  也可以重新该方法自行定义触发
     * @param array $trigger
     * @return $this
     */
    protected function setFormTrigger(array $trigger = [])
    {
        if (!empty($this->local['isSetFormTrigger'])) {
            return $this;
        }
        $this->formPage->setFormTrigger();
        $this->local['isSetFormTrigger'] = true;
        return $this;
    }

    /**
     * 创建formPage对象
     * @param array $data
     * @return $this
     * @throws \think\Exception
     */
    protected function createFrom($data = [])
    {
        if (empty($this->formPage)) {
            $this->formPage = new FormPage($data, $this->mdl);
        }
        if (isset($this->local['form_config']) && is_array($this->local['form_config'])) {
            $this->formPage->setConfig($this->local['form_config']);
        }
        return $this;
    }

    /**
     * 设置表单默认值
     * @param string $field
     * @param string $value
     * @return $this
     */
    protected function setFormValue($field = '', $value = '')
    {
        $this->local['form_items_value'][$field] = $value;
        return $this;
    }

    /**
     * 去除掉某个字段值 -- 必须是通过setFormValue 或 setFormData 设置的值有效
     * @param $field
     * @return $this
     */
    protected function removeFormValue($field)
    {
        if (isset($this->local['form_items_value'][$field])) {
            unset($this->local['form_items_value'][$field]);
        }
        return $this;
    }

    /**
     * 重置表单数据
     * @param $data
     * @return $this
     */
    protected function setFormData($data)
    {
        $this->local['form_items_value'] = isset($this->local['form_items_value']) ?
            array_merge($this->local['form_items_value'], $data) : $data;
        return $this;
    }

    /**
     * 自动设置头部信息
     * @return $this
     */
    protected function autoSetHeaderInfo()
    {
        foreach ($this->local as $key => $value) {
            if (0 === strpos($key, 'header_')) {
                $key = substr($key, 7);
                $this->setHeaderInfo($key, $value);
            }
        }
        return $this;
    }

    /**
     * 设置头部信息
     * @param $key
     * @param $value
     * @return $this
     */
    protected function setHeaderInfo($key, $value)
    {
        $this->headerInfo[$key] = $value;
        if ($key === 'title') {
            $this->addTitle($value);
        }
        return $this;
    }

    /**
     * 添加头部操作按钮
     * * @param string $name  按钮标识
     * @param string $title  标题
     * @param string $url   url
     * @param string $class  类名
     * @param string $icon  图标
     * @param int $sort   排序 任意数字  越大越前面
     * @param bool|string $js_func  如果点击以后需要js执行业务  需要给一个 你自己js函数的名字
     * @return $this
     */
    protected function addAction(
        string $name,
        string $title = '',
        string $url = '' ,
        string $class = '',
        string $icon = '',
        int $sort = 0,
        $js_func = false
    )
    {
        foreach ($this->actionList as $item) {
            if ($item['name'] === $name) {
                return $this;
            }
        }

        if (!admin_link_power($url)) {
            return $this;
        }

        array_push($this->actionList, [
            'name'  => $name,
            'title' => $title,
            'url' => $url,
            'class' => $class,
            'icon' => $icon,
            'sort' => $sort,
            'js_func' => $js_func
        ]);
        return $this;
    }

    /**
     * 根据name信息  修改指定按钮的某个属性值
     * @param $name
     * @param $key
     * @param $value
     * @return $this
     */
    protected function setActionInfo($name, $key, $value) {
        $this->actionList = array_map(function ($item) use ($name, $key, $value) {
            if ($item['name'] == $name) {
                $item[$key] = $value;
            }
            return $item;
        }, $this->actionList);
        return $this;
    }

    /**
     * 获取到头部操作按钮列表
     * @return array
     */
    protected function getActionList()
    {
        $sort_list = array_column($this->actionList, 'sort');
        array_multisort($sort_list,SORT_DESC, $this->actionList);
        return $this->actionList;
    }

    /**
     * 解析详情字段
     */
    protected function parseDetail($form, $model)
    {
        $foreignKeys = [];
        foreach ($model->relationLink as $m => $in) {
            if (in_array($in['type'], ['belongsTo', 'hasOne'])) {
                array_push($foreignKeys, isset($in['foreignKey']) ? $in['foreignKey'] : Str::snake($m) . '_id');
            }
        }
        $key = ['name', 'options', 'list', 'detail', 'attrs', 'foreign', 'counter','elem', 'modify_elem'];
        foreach ($form as $field => &$info) {
            $backup = $info;
            $info = array_intersect_key($info, array_flip($key));
            if (isset($info['detail']) && !$info['detail']) {
                unset($form[$field]);
            }
            if (!isset($info['detail']) && isset($info['list'])) {
                $info['detail'] = $info['list'];
            }
            if (isset($info['list'])) {
                unset($info['list']);
            }
            if (isset($info['detail']) && is_string($info['detail'])) {
                $info['detail'] = ['templet' => $info['detail']];
            }
            $info['detail'] = $info['detail'] ?? [];
            if (!is_array($info['detail'])) {
                $info['detail'] = [];
            }
            if (empty($info['detail']['templet']) && isset($info['options'])) {
                $info['detail']['templet'] = 'options';
            }

            if (empty($info['detail']['templet']) && isset($backup['elem']) && $backup['elem'] === 'checker') {
                $info['detail']['templet'] = 'checker';
            }

            if (empty($info['detail']['templet']) && in_array($field, $foreignKeys)) {
                $info['detail']['templet'] = 'relation';
            }
            if (!isset($info['detail']['templet'])) {
                $info['detail'] = ['templet' => 'show'];
            }

            if ($info['detail']['templet'] === 'relation') {
                if (empty($info['foreign'])) {
                    $info['foreign'] = Str::studly(substr($field, 0, -3));
                }
                if (isset($info['foreign'])) {
                    $relation = get_relation($info['foreign'], $model);
                    if (isset($relation['key'])) {
                        $info['relation'] = [
                            'model' => $relation['key'],
                            'field' => $relation[1],
                            'type' => $relation['type']
                        ];
                    } else {
                        $info['detail']['templet'] = 'show';
                    }
                    unset($info['foreign']);
                } else {
                    $info['detail']['templet'] = 'show';
                }
            }

            if (false === strpos($info['detail']['templet'], '<')) {
                $info['detail']['templet'] = '#' . Str::camel($info['detail']['templet']);
                if (strpos($info['detail']['templet'], '.') !== false) {
                    list($templet, $sub) = explode('.', $info['detail']['templet']);
                    $info['detail']['templet'] = $templet;
                    $info['sub'] = $sub;
                }
            }

            if ($info['detail']['templet'] === '#checker' && !isset($info['options'])) {
                $info['options'] = ['yes' => 1, 'no' => 0];
            }

            $info['field'] = $field;
            $info['pk'] = $model->getPk();
        }
        return $form;
    }

    /**
     * 处理父级数据
     */
    protected function parseParent()
    {
        if (isset($this->local['not_parse_parent']) && $this->local['not_parse_parent']) {
            return false;
        }
        if (isset($this->local['parent_id'])) {
            $parent_mdl = $this->mdl->getParentModel();
            if ($this->mdl->parentModel != 'parent') {
                $parent_mdl = model($parent_mdl);
            } else {
                $parent_mdl = $this->mdl;
            }
            if (!empty($this->args['parent_id'])) {
                if ($parent = $parent_mdl->find(intval($this->args['parent_id']))) {
                    if (!isset($this->local['header_ex_title'])) {
                        $this->local['header_ex_title'] = "所属{$parent_mdl->cname}：" . $parent[$parent_mdl->display];
                    }
                    if (!isset($this->local['header_ex_title_href'])) {
                        $this->local['header_ex_title_href'] = (string) url($this->params['action'], array_merge($this->args, ['parent_id' => null]));
                    }
                }
            }
            if ($this->mdl->parentModel != 'parent' && empty($this->local['not_parent_return'])) {
                if (empty($this->local['parent_return_url'])) {
                    $parent_parent_id = $parent_mdl->getParentId();
                    if ($parent_parent_id && !empty($parent)) {
                        $parent_parent_id = $parent[$parent_parent_id] ?? null;
                        $this->local['parent_return_url'] = (string)url($this->mdl->getParentModel() . '/index', ['parent_id' => $parent_parent_id]);
                    } else {
                        $this->local['parent_return_url'] = (string)url($this->mdl->getParentModel() . '/index');
                    }
                }
                $this->addAction('return_parent',"返回{$parent_mdl->cname}" , $this->local['parent_return_url'], 'btn-2', '', 0);
            }
        }
        return true;
    }

    /**
     * 返回列表页地址
     */
    protected function getIndexUrl($data = [], array $args = [])
    {
        if (!isset($this->local['parent_id'])) {
            return (string) url('index', $args);
        }
        if (empty($data) && isset($this->local['data'])) {
            $data = $this->local['data'];
        }
        $parent_id = $this->args['parent_id'] ?? ($data[$this->local['parent_id']] ?? 0);

        if (isset($parent_id)) {
            return (string) url('index', array_merge(['parent_id' => $parent_id], $args));
        }

        return (string) url('index', $args);
    }

    protected function fetch(string $tempate = '', array $vars = [])
    {
        // 自动设置头部信息
        $this->autoSetHeaderInfo();
        $vars['header_info'] = $this->headerInfo;
        $vars['action_list'] = $this->getActionList();
        return parent::{__FUNCTION__}($tempate, $vars);
    }
}