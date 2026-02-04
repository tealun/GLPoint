<?php
declare (strict_types=1);

namespace woo\common\builder\form;

use think\Collection as BaseCollection;

class Collection extends  BaseCollection
{
    /**
     * 修改表单字段的属性
     * @param string $item
     * @param string $name
     * @param $value
     * @return bool
     */
    public function setItemAttr(string $item, $name, $value = '')
    {
        if (!isset($this->items[$item])) {
            return false;
        }
        if (is_string($name) && !is_null($value)) {
            $this->items[$item][$name] = $value;
            return false;
        }
        if (is_array($name) && isset($name['element'])) {
            $this->items[$item] =  $name;
        } elseif (!is_null($value)) {
            $this->items[$item][$name] = $value;
        }
    }

    public function getItemAttr($item, $name = '')
    {
        if (empty($name)) {
            return isset($this->items[$item]) ? $this->items[$item] : [];
        }
        return isset($this->items[$item][$name]) ? $this->items[$item][$name] : null;
    }

    public function remove(string $item)
    {
        $remove = [];
        if (isset($this->items[$item])) {
            $remove = $this->items[$item];
            unset($this->items[$item]);
        }
        return $remove;
    }

    public function itemExists(string  $item) {
        return array_key_exists($item, $this->items);
    }
}