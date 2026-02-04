<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;

class AdminUseAdminGroup extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
    }
}