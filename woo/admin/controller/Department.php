<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use app\common\controller\Admin;
use think\facade\Db;
use woo\common\helper\Arr;

class Department extends Admin
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
            'power' => 'create'
        ];

        $this->local['afterData'] = 'afterData';
        return parent::{__FUNCTION__}();
    }

    protected function afterData()
    {
        $ids = [];
        foreach ($this->local['tableData']['data'] as $item) {
            if (empty($item['leader_ids'])) {
                continue;
            }
            array_push($ids, ...explode(',', $item['leader_ids']));
        }
        $ids = array_unique($ids);
        if ($ids) {
            $admins = Db::name('Admin')
                ->where('id', count($ids) != 1 ? 'IN' : '=', count($ids) != 1 ? $ids : $ids[0])
                ->select()
                ->toArray();
            $admins = Arr::combine($admins, 'id');
            foreach ($this->local['tableData']['data'] as &$item) {
                if (empty($item['leader_ids'])) {
                    continue;
                }
                $leader_ids = explode(',', $item['leader_ids']);
                $leader = [];
                foreach ($leader_ids as $id) {
                    if (isset($admins[$id])) {
                        array_push($leader, '<span class="layui-badge layui-bg-blue woo-theme-background">' . ($admins[$id]['truename'] ?: $admins[$id]['username']) . '</span>');
                    }
                }
                $item['leader_ids'] = implode(' ', $leader);
            }
        }
    }

    protected function detailCallback($data)
    {
        if (!empty($data['leader_ids'])) {
            $ids = explode(',', $data['leader_ids']);
            $admins = Db::name('Admin')
                ->where('id', count($ids) == 1 ? '=' : 'IN', count($ids) == 1 ? $ids[0] : $ids)
                ->select()
                ->toArray();
            $admins = Arr::combine($admins, 'id');
            $leader_ids = explode(',', $data['leader_ids']);
            $leader = [];
            foreach ($leader_ids as $id) {
                if (isset($admins[$id])) {
                    array_push($leader, '<span class="layui-badge layui-bg-blue woo-theme-background">' . ($admins[$id]['truename'] ?: $admins[$id]['username']) . '</span>');
                }
            }
            $data['leader_ids'] = implode(' ', $leader);
        }
        return $data;
    }

    public function create()
    {
        $this->setFormValue('is_admin', 1);
        return parent::{__FUNCTION__}();
    }

    public function getRelationOptions()
    {
        if ($this->args['field'] == 'leader_ids' ?? '') {
            $this->mdl->form['leader_ids']['foreign_tab'] = [
                'list_fields' => [
                    'id',
                    'username',
                    'truename',
                    'mobile'
                ]
            ];
        }
        return parent::{__FUNCTION__}();
    }

}