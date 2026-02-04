<?php
declare (strict_types=1);

namespace woo\common\coroutine;

use think\swoole\coroutine\Context;

class SwooleContext extends Context
{
    public static function getAllData($default = [])
    {
        return static::$data[static::getCoroutineId()] ?? $default;
    }
}