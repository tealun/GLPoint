<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use \app\common\controller\Admin;

class Shortcut extends Admin
{
    public function getRelationOptions()
    {
        // admin_group_id 是你当前的关联字段名
        /*
        $this->mdl->form['admin_group_id']['foreign_tab'] = [
            'tool_bar' => [
                [
                    'name' => 'create',
                    'title' => '新增',
                    'sort' => 19,
                    'icon' =>  'layui-icon-add-circle',
                    'class' => 'btn-5 woo-layer-load',
                    // 这个from 是给admin_group/create 方法中获取来源的，然后在admin_group/create中设置返回的链接
                    'url' => (string)url('admin_group/create', array_merge($this->args, ['from' => 'Shortcut/getRelationOptions'])),
                    'power' => 'create'
                ]
            ]
        ];
        */
        return parent::{__FUNCTION__}();
    }
}