<?php

declare(strict_types=1);

namespace woo\admin\controller;

class AdminGroup extends \app\common\controller\Admin
{
    public function index()
    {
        if (isset($this->args['parent_id'])) {
            return $this->redirect('index');
        }
        $this->mdl->tableTab['basic']['tool_bar'][] = [
            'name' => 'sort',
            'title' => '一级' . $this->mdl->cname . '排序',
            'class' => 'layui-btn-warm',
            'icon' => 'woo-icon-paixu',
            'sort' => 20,
            'url' => (string)url('sort', ['parent_id' => "0"]),
            'power' => 'sort'
        ];
        $this->mdl->tableTab['basic']['item_tool_bar'][] = [
            'name' => 'sort',
            'title' => '',
            'class' => 'btn-24',
            'icon' => 'woo-icon-paixu',
            'sort' => 60,
            'hover' => '下级排序',
            'url' => (string)url('sort', ['parent_id' => "{{d.id}}"]),
            'where' => '{{d.children_count > 1}}',
            'power' => 'sort'
        ];
        $this->mdl->tableTab['basic']['item_tool_bar'][] = [
            'name' => 'create',
            'title' => '',
            'icon' => 'layui-icon-add-circle',
            'class' => 'woo-layer-load btn-22',
            'sort' => 120,
            'hover' => '新增下级',
            'url' => (string)url('create', ['parent_id' => "{{d.id}}"]),
            'power' => 'create',
            'where' => '{{d.id != ' . $this->app->config->get('wooauth.super_group_id') . '}}'
        ];

        $this->addAction('power', '授权', (string) url('power/index'), 'btn-2', 'layui-icon-vercode');
        $this->local['item_tool_bar']['delete']['where'] = '{{d.id != ' . $this->app->config->get('wooauth.super_group_id') . '}}';
        $this->local['afterData'] = 'afterData';
        return parent::{__FUNCTION__}();
    }

    protected function afterData()
    {
        foreach ($this->local['tableData']['data'] as &$item) {
            if ($item['id'] == $this->app->config->get('wooauth.super_group_id')) {
                $item['LAY_DISABLED'] = true;
                break;
            }
        }
    }

    public function create()
    {
        // 如果是 Shortcut/getRelationOptions 链接过来的 返回链接 设置为返回回去
        if (isset($this->args['from']) && $this->args['from'] == 'Shortcut/getRelationOptions') {
            $this->local['return_list_url'] = (string) url('Shortcut/getRelationOptions', array_merge($this->args, ['from' => null]));
        }
        $this->setFormValue('data_allow', 0);
        if (!empty($this->args['parent_id'])) {
            $this->setFormValue('data_allow', -1);
        }
        $this->setFormValue('is_admin', 1);

        return parent::{__FUNCTION__}();
    }

    public function delete()
    {
        $this->local['where'][] = ['id', '<>', $this->app->config->get('wooauth.super_group_id')];
        return parent::{__FUNCTION__}();
    }

    public function batchDelete()
    {
        $this->local['where'][] = ['id', '<>', $this->app->config->get('wooauth.super_group_id')];
        return parent::{__FUNCTION__}();
    }
}
