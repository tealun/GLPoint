<?php
declare (strict_types=1);

namespace woo\common\facade;

use think\Facade;

class Auth  extends Facade
{
    protected static function getFacadeClass()
    {
        return 'woo\common\Auth';
    }
}