<?php
declare (strict_types=1);

namespace woo\common\coroutine;

use Closure;

class FpmContext
{
    protected static $data = [];

    public static function getData(string $key, $default = null)
    {
        return static::$data[$key] ?? $default;
    }

    public static function getAllData($default = [])
    {
        return static::$data ?? $default;
    }

    public static function hasData(string $key)
    {
        return array_key_exists($key, static::$data);
    }

    public static function rememberData(string $key, $value)
    {
        if (self::hasData($key)) {
            return self::getData($key);
        }

        if ($value instanceof Closure) {
            $value = $value();
        }

        self::setData($key, $value);

        return $value;
    }

    public static function setData(string $key, $value)
    {
        static::$data[$key] = $value;
    }

    public static function removeData(string $key)
    {
        unset(static::$data[$key]);
    }

    public static function getDataKeys()
    {
        return array_keys(static::$data ?? []);
    }

    public static function clear()
    {
        static::$data = [];
    }
}