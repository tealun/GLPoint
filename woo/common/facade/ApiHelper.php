<?php
declare (strict_types=1);

namespace woo\common\facade;

use think\Facade;

class ApiHelper  extends Facade
{
    protected static function getFacadeClass()
    {
        return 'woo\common\helper\ApiHelper';
    }
}