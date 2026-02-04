<?php
declare (strict_types=1);

namespace woo\common\coroutine;


class Context
{
    public  static function __callStatic($name, $arguments)
    {
        if (in_array($name, ['get', 'set', 'has', 'remember', 'remove', 'getAll'])) {
            $name .= 'Data';
        }
        return run_mode() === 'swoole' ?
            SwooleContext::$name(...$arguments):
            FpmContext::$name(...$arguments);
    }
}