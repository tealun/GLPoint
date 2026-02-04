<?php
declare (strict_types=1);

namespace woo\common\controller\traits;

use woo\common\Auth;
use app\common\builder\FormPage;
use woo\common\facade\ThinkApi;
use woo\common\helper\Arr;
use app\common\builder\Table;
use woo\common\helper\Str;

trait ViewCommon
{
    protected $formPage = null;

    protected $table = null;

    protected $autoCreateFormActions =  ['create', 'modify'];

    protected function initialize()
    {
        parent::{__FUNCTION__}();
        if (empty($this->formPage) && in_array($this->params['action'], $this->autoCreateFormActions)) {
            $this->createFrom();
        }
    }

    /**
     * 列表
     * @return mixed
     */
    protected function index()
    {
        if (empty($this->mdl)) {
            return $this->message('模型不存在，请生成模型以后再试', 'warn');
        }
        $table_tab = $this->local['tableTab'] ?? $this->mdl->tableTab;
        // 20230216 新增表单场景
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
        if ($this->request->isAjax()) {
            if (!empty($table_tab[$this->args['tabname'] ?? 'basic']['list_with']) && empty($this->local['with'])) {
                $this->local['with'] = $table_tab[$this->args['tabname'] ?? 'basic']['list_with'];
            }

            // 自动识别用户
            // 默认只有自己才能改自己的  如果希望都可以 $this->local['all_user_allow'] = true;
            if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
                $login_foreign_key = $this->login['login_foreign_key'];
                if (isset($this->mdl->form[$login_foreign_key])) {
                    $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
                }
            }

            // 可以通过$this->args['tabname'] 识别到当前tab 进行不同逻辑操作 如果tab有单独设置url 就对应url处理
            // 不支持ajax中动态指定limit 需要提前指定好limit 或由用户自己选择limit
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

        $this->setTableAttr();

        $this->table->setTableAttr('limit', intval($this->local['limit'] ?? 10));
        $this->table->setTableAttr('limits', $this->local['limits'] ?? [10,20,50,100]);

        $this->assign->table = $this->table;
        return $this->fetch($this->local['fetch'] ?? $this->defaultFetch[__FUNCTION__] ?? 'list');
    }

    /**
     * 用于自定义页面的翻页查询
     */
    protected function getPageData()
    {
        if (empty($this->mdl)) {
            return $this->message('模型不存在，请生成模型以后再试', 'warn');
        }

        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }

        $list = $this->mdl->getPage([
            'withTrashed' => $this->local['withTrashed'] ?? false,// 查询包含删除的数据
            'onlyTrashed' => $this->local['onlyTrashed'] ?? false,// 只查询删除的数据
            'with' => $this->local['with'] ?? [],
            'withJoin' => $this->local['withJoin'] ?? [],
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
            'whereCallback' => $this->local['whereCallback'] ?? null,
            'whereRaw' => $this->local['whereRaw'] ?? [],
            'existsWhere' => $this->local['existsWhere'] ?? [],
            'field' => $this->local['field'] ?? [],
            'order' => $this->local['order'] ?? [],
            'limit' => $this->local['limit'] ?? 10,
        ]);
        if (false === $list) {
            return $this->error(array_values($this->mdl->getError())[0] ?? '查询错误');
        }
        $this->assign->render = $list['render'];
        $this->assign->page = $list['page'];
        $this->assign->list = $list['list'];
        return $this->fetch($this->local['fetch'] ?? $this->defaultFetch[__FUNCTION__] ?? 'index');
    }

    /**
     * 添加
     * @return mixed
     * @throws \think\Exception
     */
    protected function create()
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

        if (!empty($this->local['allowField'])) {
            // 减去不允许投稿的字段列表
            $this->local['allowField'] = array_diff($this->local['allowField'], $this->mdl->getContributeFields(false));
        } else {
            // 自动获取允许投稿的字段列表
            $this->local['allowField'] = $this->mdl->getContributeFields(true);
        }
        if (isset($this->mdl->form['list_order']) && empty($this->local['allowField']['list_order'])) {
            $this->local['allowField'][]= 'list_order';
        }

        // 创建者
        if ($this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->setFormValue($login_foreign_key, $this->login['login_foreign_value']);
                if (!empty($this->local['allowField']) && !in_array($login_foreign_key, $this->local['allowField'])) {
                    $this->local['allowField'][] = $login_foreign_key;
                }
            }
        }

        // 最终表单赋值一次  如果前面就直接调用 formPage的setData方法 可能会因为表单项还没有添加 导致赋值失败
        $this->formPage->setData($this->local['form_items_value'] ?? []);

        // 数据提交
        if ($this->request->isPost() && !isset($this->local['not_save_action'])) {
            if (method_exists($this, 'beforeCreateSave') && $call_result = $this->beforeCreateSave()) {
                return $call_result;
            }
            // 保存
            $result = $this->formPage->setModel($this->mdl)->save([
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
                return $this->message('添加成功[ID：' . $result->{$this->mdlPk} .']', 'success', array_merge([
                    $this->local['return_list_text'] ?? '返回列表' => $this->local['return_list_url'] ?? $this->getIndexUrl(),
                    'back' =>  $this->local['return_list_back'] ?? true
                ], $this->local['more_success_btns'] ?? []));
            }
        } else {
            if (isset($this->args['copy_id'])) {
                $copy_id = intval($this->args['copy_id']);
                $copy_data = $this->mdl
                    ->where($this->mdlPk, '=', $copy_id)
                    ->find();
                if ($copy_data) {
                    $copy_data = $copy_data->toArray();
                    // 不允许自动赋值的字段列表
                    $unset_key = array_merge(['create_time', 'update_time', 'delete_time', 'admin_id', 'user_id', 'list_order', $this->mdlPk], array_keys($this->local['form_items_value'] ?? []), $this->local['copy_unset_field'] ?? []);
                    foreach ($copy_data as $field => $value) {
                        if (in_array($field, $unset_key)) {
                            continue;
                        }
                        $this->formPage->setItemValue($field, $value);
                    }
                }
            }
        }
        $this->assign->form = $this->formPage;
        return $this->fetch($this->local['fetch'] ?? $this->defaultFetch[__FUNCTION__] ?? 'form');
    }

    /**
     * 修改
     * @return mixed
     * @throws \think\Exception
     */
    protected function modify()
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

        // 自动识别用户
        // 默认只有自己才能改自己的  如果希望都可以 $this->local['all_user_allow'] = true;
        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }

        if (!empty($this->local['allowField'])) {
            // 减去不允许投稿的字段列表
            $this->local['allowField'] = array_diff($this->local['allowField'], $this->mdl->getContributeFields(false));
        } else {
            // 自动获取允许投稿的字段列表
            $this->local['allowField'] = $this->mdl->getContributeFields(true);
        }
        if ($this->request->isPost() && !isset($this->local['not_save_action'])) {
            if (method_exists($this, 'beforeModifySave') && $call_result = $this->beforeModifySave()) {
                return $call_result;
            }
            // 保存
            $result = $this->formPage->setModel($this->mdl)->save([
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
                return $this->message('修改成功[ID：' . $result->{$this->mdlPk} .']', 'success', array_merge([
                    $this->local['return_list_text'] ?? '返回列表' => $this->local['return_list_url'] ?? $this->getIndexUrl(),
                    'back' =>  $this->local['return_list_back'] ?? true
                ], $this->local['more_success_btns'] ?? []));
            }
        } else {
            $old = $this->mdl
                ->where($this->mdlPk, '=', $id)
                ->where($this->local['where'] ?? [])
                ->whereOr($this->local['whereOr'] ?? [])
                ->find();
            if (empty($old)) {
                return $this->message('抱歉！您没有权限修改该条数据或该条数据不存在', 'error', ['去添加' => ['create']]);
            }
            $this->local['data'] = $old->toArray();
            $this->formPage->setData($this->local['data']);
            $this->formPage->setData($this->local['form_items_value'] ?? []);
        }
        $this->assign->form = $this->formPage;
        return $this->fetch($this->local['fetch'] ?? $this->defaultFetch[__FUNCTION__] ?? 'form');
    }

    /**
     * 场景表单
     * @return mixed
     * @throws \think\Exception
     */
    protected function scene()
    {
        $this->local['id'] = isset($this->args['id']) ? intval($this->args['id']) : 0;
        $this->local['ignore_modify_elem'] = true;

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

        $form = [];
        foreach ($scene['fields'] as $item) {
            if (empty($item['field'])) {
                continue;
            }
            $info = $this->mdl->form[$item['field']] ?? [];
            if (!empty($item['elem']) && $item['elem'] != 'auto') {
                $info['elem'] = $item['modify_elem']?? $item['elem'];
            }
            if (!empty($item['validate']) ) {
                if (array_key_exists('require', $item['validate'])) {
                    $info['require'] = true;
                }
                $validate = [];
                foreach ($item['validate'] as $v => $p) {
                    $validate[$item['field']][] = ['rule' => empty($p)? [$v]: [$v, $p]];
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
        $this->local['isSetFormTab'] = true;
        $this->local['isSetFormGrid'] = true;
        $this->createFormItem($form);

        return $this->modify();
    }

    /**
     * 删除
     * @return mixed
     */
    protected function delete()
    {
        if (!isset($this->args['id'])) {
            return $this->message("缺少【id】参数", 'error');
        }
        $id = intval($this->args['id']);
        // 自动识别用户
        // 默认只有自己才能改自己的  如果希望都可以 $this->local['all_user_allow'] = true;
        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }
        $result = $this->mdl->deleteData($id, $this->local['where'] ?? [], $this->local['force'] ?? false);
        if (false === $result) {
            return $this->message($this->mdl->getError()[0] ?? '删除失败', 'error');
        }
        return $this->message("{$this->mdl->cname}[ID:{$id}]删除成功",'success', $this->local['success_redirect'] ?? []);
    }

    /**
     * 批量删除
     * @return mixed
     */
    protected function batchDelete()
    {
        if (!$this->request->isPost()) {
            return $this->message('不是一个正确的请求方式', 'error');
        }
        $selected_ids = $this->request->post('selected_id', []);
        if (empty($selected_ids)) {
            return $this->message('没有找到需要被删除的数据', 'error');
        }
        // 自动识别用户
        // 默认只有自己才能改自己的  如果希望都可以 $this->local['all_user_allow'] = true;
        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
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
     * 详情
     * @return mixed
     * @throws \think\Exception
     */
    protected function detail()
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
                    $this->local['with'][$model] = $info ?: $this->mdl->relationLink[$model];
                    $with_form[$model] = [
                        'type'  => $this->mdl->relationLink[$model]['type'],
                        'title' => model($model)->cname,
                        'form'  => $this->parseDetail(model($model)->form ?? [], model($model))
                    ];
                }
            }
        }

        // 自动识别用户
        // 默认只有自己才能改自己的  如果希望都可以 $this->local['all_user_allow'] = true;
        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }

        $data = $this->mdl
            ->with($this->mdl->parseWith($this->local['with'] ?? []))
            ->withJoin($this->mdl->parseWith($this->local['withJoin'] ?? []))
            ->field($this->local['field'] ?? true)
            ->where($this->mdlPk, '=', $id)
            ->where($this->local['where'] ?? [])
            ->whereOr($this->local['whereOr'] ?? [])
            ->find();
        if (empty($data)) {
            return $this->message("ID为[{$id}]的数据不存在", 'error');
        }
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

        return $this->fetch($this->local['fetch'] ?? $this->defaultFetch[__FUNCTION__] ?? 'detail');
    }

    /**
     * 列表开关
     * @return mixed
     */
    protected function ajaxSwitch()
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

        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }

        $origin = $this->mdl
            ->where($this->mdlPk, '=', intval($data['id']))
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
     * 批量开关
     * @return mixed
     */
    protected function batchSwitch()
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

        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }

        $list = $this->mdl
            ->where($this->mdlPk, 'IN', $selected_ids)
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
            $msg = "批量设置数据成功，{$count}条数据被成功设置";
        } else {
            $msg = "批量设置数据成功，{$count}条数据被成功设置，"  . (count($selected_ids) - $count) . "条数据设置失败";
        }
        return $this->message($msg, 'success');
    }

    protected function setTableAttr()
    {
        $this->local['tableTab'] = $this->table->getTab();
        $reflect = reflect(get_class($this));
        $this->local['load_type'] = $this->local['load_type'] ?? '';

        foreach ($this->local['tableTab'] as $tabname => $tabinfo) {
            if (!empty($tabinfo['model']) || $tabname == 'delete_index') {
                continue;
            }
            if (
                $reflect->getMethod('create')->isPublic()
                && ($this->local['tool_bar']['create'] ?? true)
                && !$this->table->switchTab($tabname)->isToolBarExists('create')
            ) {
                $this->table->switchTab($tabname)->addToolBar([
                    'name' => 'create',
                    'title' => $this->local['tool_bar']['create']['title'] ?? '新增',
                    'sort' => $this->local['tool_bar']['create']['sort'] ?? 40,
                    'icon' => $this->local['tool_bar']['create']['icon'] ?? 'layui-icon-add-circle',
                    'class' => 'btn-5 woo-layer-load ' . ($this->local['tool_bar']['create']['class'] ?? '') . ' ' . (is_array($this->local['load_type']) ? ($this->local['load_type']['create'] ?? '') : $this->local['load_type']),
                    'url' => (string)url('create', $this->args),
                ]);
            }

            if (
                $reflect->getMethod('batchDelete')->isPublic()
                && ($this->local['tool_bar']['batch_delete'] ?? true)
                && !$this->table->switchTab($tabname)->isToolBarExists('batch_delete')
            ) {
                $this->table->switchTab($tabname)->addToolBar([
                    'name' => 'batch_delete',
                    'title' => $this->local['tool_bar']['batch_delete']['title'] ?? '删除',
                    'sort' => $this->local['tool_bar']['batch_delete']['sort'] ?? 20,
                    'js_func' => $this->local['tool_bar']['batch_delete']['js_func'] ?? 'woo_batch_delete',
                    'icon' => $this->local['tool_bar']['batch_delete']['icon'] ?? 'layui-icon-close',
                    'tip' => $this->local['tool_bar']['batch_delete']['tip'] ?? '',
                    'class' => 'layui-btn-danger ' . ($this->local['tool_bar']['batch_delete']['class'] ?? ''),
                    'url' => (string)url('batchDelete'),
                    'check' => true
                ]);
            }

            if (
                $reflect->getMethod('modify')->isPublic()
                && ($this->local['item_tool_bar']['modify'] ?? true)
                && !$this->table->switchTab($tabname)->isItemToolBarExists('modify')
            ) {
                $this->table->switchTab($tabname)->addItemToolBar([
                    'name' => 'modify',
                    'title' => $this->local['item_tool_bar']['modify']['title'] ?? '',
                    'sort' => $this->local['item_tool_bar']['modify']['sort'] ?? 80,
                    'icon' => $this->local['item_tool_bar']['modify']['icon'] ?? 'layui-icon-edit',
                    'class' => 'btn-5 woo-layer-load ' . ($this->local['item_tool_bar']['modify']['class'] ?? '') . ' ' . (is_array($this->local['load_type']) ? ($this->local['load_type']['modify'] ?? '') : $this->local['load_type']),
                    'url' => (string)url('modify', ['id' => '{{d.' . $this->mdlPk . '}}']),
                    'hover' => $this->local['item_tool_bar']['modify']['hover'] ?? '编辑',
                    'where' => $this->local['item_tool_bar']['modify']['where'] ?? '',
                    'where_type' => $this->local['item_tool_bar']['modify']['where_type'] ?? 'disabled',
                ]);
            }
            if (
                $reflect->getMethod('delete')->isPublic()
                && ($this->local['item_tool_bar']['delete'] ?? true)
                && !$this->table->switchTab($tabname)->isItemToolBarExists('delete')
            ) {
                $this->table->switchTab($tabname)->addItemToolBar([
                    'name' => 'delete',
                    'title' => $this->local['item_tool_bar']['delete']['title'] ?? '',
                    'sort' => $this->local['item_tool_bar']['delete']['sort'] ?? 60,
                    'js_func' => $this->local['item_tool_bar']['delete']['js_func'] ?? 'woo_delete',
                    'tip' => $this->local['item_tool_bar']['delete']['tip'] ?? '',
                    'icon' => $this->local['item_tool_bar']['delete']['icon'] ?? 'layui-icon-delete',
                    'class' => 'layui-btn-danger ' . ($this->local['item_tool_bar']['delete']['class'] ?? ''),
                    'url' => (string)url('delete', ['id' => '{{d.' . $this->mdlPk . '}}']),
                    'hover' => $this->local['item_tool_bar']['delete']['hover'] ?? '删除',
                    'where' => $this->local['item_tool_bar']['delete']['where'] ?? '',
                    'where_type' => $this->local['item_tool_bar']['delete']['where_type'] ?? 'disabled',
                ]);
            }
            if (
                $reflect->getMethod('detail')->isPublic()
                && ($this->local['item_tool_bar']['detail'] ?? true)
                && !$this->table->switchTab($tabname)->isItemToolBarExists('detail')
            ) {
                $this->table->switchTab($tabname)->addItemToolBar([
                    'name' => 'detail',
                    'title' => $this->local['item_tool_bar']['detail']['title'] ?? '',
                    'sort' => $this->local['item_tool_bar']['detail']['sort'] ?? 20,
                    'icon' => $this->local['item_tool_bar']['detail']['icon'] ?? 'woo-icon-chakan',
                    'class' => 'btn-21 woo-layer-load ' . ($this->local['item_tool_bar']['detail']['class'] ?? '') . ' ' . (is_array($this->local['load_type']) ? ($this->local['load_type']['detail'] ?? '') : $this->local['load_type']),
                    'url' => (string)url('detail', ['id' => '{{d.' . $this->mdlPk . '}}']),
                    'power' => 'detail',
                    'hover' => $this->local['item_tool_bar']['detail']['hover'] ?? '详情',
                    'where' => $this->local['item_tool_bar']['detail']['where'] ?? '',
                    'where_type' => $this->local['item_tool_bar']['detail']['where_type'] ?? 'disabled',
                ]);
            }

            if (
                $reflect->getMethod('create')->isPublic()
                && ($this->local['item_tool_bar']['copy'] ?? false)
                && !$this->table->switchTab($tabname)->isItemToolBarExists('copy')
            ) {
                $this->table->switchTab($tabname)->addItemToolBar([
                    'name' => 'copy',
                    'title' => $this->local['item_tool_bar']['copy']['title'] ?? '',
                    'sort' => $this->local['item_tool_bar']['copy']['sort'] ?? 40,
                    'icon' => $this->local['item_tool_bar']['copy']['icon'] ?? 'woo-icon-fuzhi',
                    'class' => 'btn-15 woo-layer-load ' . ($this->local['item_tool_bar']['copy']['class'] ?? '') . ' ' . (is_array($this->local['load_type']) ? ($this->local['load_type']['copy'] ?? '') : $this->local['load_type']),
                    'url' => (string)url('create', ['copy_id' => '{{d.' . $this->mdlPk . '}}']),
                    'hover' => $this->local['item_tool_bar']['copy']['hover'] ?? '复制',
                    'where' => $this->local['item_tool_bar']['copy']['where'] ?? '',
                    'where_type' => $this->local['item_tool_bar']['copy']['where_type'] ?? 'disabled',
                ]);
            }

        }
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
}