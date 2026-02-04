<?php
namespace app\api\library;

class ListToTree
{
    /**
     * 将平级数组转为树形结构
     * @param array $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int $root
     * @return array
     */
    public static function toTree($list, $pk = 'id', $pid = 'parent_id', $child = 'children', $root = 0)
    {
        $tree = [];
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            $parentId = $data[$pid];
            if ($parentId == $root) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
        return $tree;
    }
}
