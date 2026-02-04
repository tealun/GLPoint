<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;

class Shortcut extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
        $this->form['admin_group_id']['foreign_tab'] = [
            'checkbox' => ['type' => 'checkbox']
        ];
    }
}