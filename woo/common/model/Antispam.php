<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\helper\Arr;

class Antispam extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
    }

}