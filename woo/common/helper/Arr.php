<?php
declare (strict_types=1);

namespace woo\common\helper;

class Arr extends \think\helper\Arr
{

    public static function combine(array $data, $keyField, $valueField = null)
    {
        $return = [];
        foreach ($data as $key => $item) {
            $return[$item[$keyField] ?? $key] = $item[$valueField] ?? $item;
        }
        return $return;
    }

    public static function fieldList(string $filed, array $list = [])
    {
        $return = [];
        foreach ($list as $key => $item) {
            if (isset($item[$filed])) {
                array_push($return, $item[$filed]);
            }
        }
        return $return;
    }


    /**
     * 对一个数组，并将其转换为标准格式.
     * @param array $data 需要转换成标准格式的数组
     * @param bool $assoc 如果TRUE，$data将被转换为关联数组 .
     * @return array
     */
    public static function normalize(array $data, $assoc = true): array
    {
        if (empty($data)) {
            return $data;
        }
        $keys = array_keys($data);
        $count = count($keys);
        $numeric = true;

        if (!$assoc) {
            for ($i = 0; $i < $count; $i++) {
                if (!is_int($keys[$i])) {
                    $numeric = false;
                    break;
                }
            }
        }
        if (!$numeric || $assoc) {
            $new_list = [];
            for ($i = 0; $i < $count; $i++) {
                if (is_int($keys[$i])) {
                    $new_list[$data[$keys[$i]]] = null;
                } else {
                    $new_list[$keys[$i]] = $data[$keys[$i]];
                }
            }
            $data = $new_list;
        }
        return $data;
    }

    public static function diff(array $data, $compare)
    {
        if (empty($data)) {
            return (array)$compare;
        }
        if (empty($compare)) {
            return (array)$data;
        }
        $intersection = array_intersect_key($data, $compare);
        while (($key = key($intersection)) !== null) {
            if ($data[$key] == $compare[$key]) {
                unset($data[$key]);
                unset($compare[$key]);
            }
            next($intersection);
        }
        return $data + $compare;
    }

    public static function merge(array $data, $merge)
    {
        $args = array_slice(func_get_args(), 1);
        $return = $data;

        foreach ($args as &$curArg) {
            $stack[] = array((array)$curArg, &$return);
        }
        unset($curArg);

        while (!empty($stack)) {
            foreach ($stack as $curKey => &$curMerge) {
                foreach ($curMerge[0] as $key => &$val) {
                    if (!empty($curMerge[1][$key]) && (array)$curMerge[1][$key] === $curMerge[1][$key] && (array)$val === $val) {
                        $stack[] = array(&$val, &$curMerge[1][$key]);
                    } elseif ((int)$key === $key && isset($curMerge[1][$key])) {
                        $curMerge[1][] = $val;
                    } else {
                        $curMerge[1][$key] = $val;
                    }
                }
                unset($stack[$curKey]);
            }
            unset($curMerge);
        }
        return $return;
    }

    public static function deepMerge(...$arrs)
    {
        $merged = [];
        while ($arrs) {
            $array = array_shift($arrs);
            if (!$array) {
                continue;
            }
            foreach ($array as $key => $value) {
                if (is_string($key)) {
                    if (is_array($value) && array_key_exists($key, $merged)
                        && is_array($merged[$key])) {
                        $merged[$key] = static::deepMerge(...[$merged[$key], $value]);
                    } else {
                        $merged[$key] = $value;
                    }
                } else {
                    $merged[] = $value;
                }
            }
        }
        return $merged;
    }

    /**
     * 给关联数组的指定位置插入一个值
     * @param $input
     * @param $offset
     * @param int $length
     * @param array $replacement
     * @return array
     */
    public static function arraySpliceAssoc(&$input, $offset, $length = 0, array $replacement = array())
    {
        $replacement = (array)$replacement;
        $key_indices = array_flip(array_keys($input));

        if (isset($input[$offset]) && is_string($offset)) {
            $offset = $key_indices[$offset] + 1;
        }
        if (isset($input[$length]) && is_string($length)) {
            $length = $key_indices[$length] - $offset;
        }
        $result = array_slice($input, 0, $offset, TRUE)
            + $replacement
            + array_slice($input, $offset + $length, NULL, TRUE);

        return $result;
    }
}