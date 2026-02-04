<?php

declare(strict_types=1);

namespace woo\admin\controller;

class Antispam extends \app\common\controller\Admin
{
    public function index()
    {
        $this->mdl->tableTab['basic']['tool_bar'][] = [
            'name' => 'clear_data',
            'title' => '清空数据',
            'sort' => 10,
            'js_func' => 'clear_data',
            'icon' => '',
            'class' => 'btn-20',
            'url' => (string) url('clearData')
        ];
        return parent::{__FUNCTION__}();
    }

    public function clearData()
    {
        $this->local['beforeTime'] = 7 * 86400;
        return parent::{__FUNCTION__}();
    }
}
