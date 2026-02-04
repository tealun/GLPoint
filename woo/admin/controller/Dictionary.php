<?php
declare (strict_types = 1);

namespace woo\admin\controller;


class Dictionary extends \app\common\controller\Admin
{
    public function index()
    {
        $this->mdl->tableTab['basic']['item_tool_bar'][] = [
            'name' => 'create_item',
            'title' => '新增字典项',
            'sort' => 60,
            'class' => 'btn-23',
            'icon' => '',
            'url' => (string)url('DictionaryItem/create', ['parent_id' => '{{d.id}}']),
        ];
        return parent::{__FUNCTION__}();
    }

}