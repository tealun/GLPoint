<?php
declare (strict_types=1);

namespace woo\common;

class Cache extends \think\Cache
{
    public function has($key): bool
    {
        if (app()->isDebug()) {
            return false;
        }
        return parent::has($key);
    }

    public function set($key, $value, $ttl = null): bool
    {
        if (app()->isDebug()) {
            return true;
        }
        return parent::set($key, $value, $ttl);
    }
}