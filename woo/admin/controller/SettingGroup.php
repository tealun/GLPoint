<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use app\common\controller\Admin;

class SettingGroup extends  Admin
{
    public function index()
    {
        $this->mdl->tableTab['basic']['item_tool_bar'][] = [
            'name' => 'setting_list',
            'title' => '',
            'sort' => 100,
            'class' => 'btn-23',
            'icon' => 'woo-icon-set',
            'hover' => '配置列表',
            'url' => (string)url('Setting/index', ['parent_id' => '{{d.id}}']),
            'power' => 'Setting/index'
        ];
        $this->local['item_tool_bar']['delete']['where'] = '{{d.id}} > 6';
        return parent::{__FUNCTION__}();
    }
}