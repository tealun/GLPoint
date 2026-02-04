<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;

class Attachement extends App
{
    protected function afterStart()
    {
        if (app('http')->getName() == 'admin') {
            $this->tableTab['basic']['siderbar'] = [
                'foreign' => 'Folder'
            ];
        }

        $this->tableTab['basic']['table']['skin'] = 'line';
        parent::{__FUNCTION__}();
    }
}