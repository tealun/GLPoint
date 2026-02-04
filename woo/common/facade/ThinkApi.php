<?php
declare (strict_types=1);

namespace woo\common\facade;

use think\Facade;

class ThinkApi  extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\common\ThinkApi';
    }
}