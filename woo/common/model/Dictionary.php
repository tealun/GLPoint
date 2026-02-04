<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use think\facade\Cache;

class Dictionary extends App
{

    public function resetDictCache()
    {
        $data = $this->order($this->getDefaultOrder())->select()->toArray();
        $cache = [];
        foreach ($data as $item) {
            $cache[$item['model']][$item['field']] = $this->getDictKeyValue($item['id']);
        }
        Cache::tag($this->name)->set('dictionary_static_cache', $cache);
        return $cache;
    }


    protected function getDictKeyValue($id)
    {
        $list = model('DictionaryItem')
            ->where('dictionary_id', '=', $id)
            ->order(model('DictionaryItem')->getDefaultOrder())
            ->select()
            ->toArray();
        if ($list) {
            $dict = [];
            foreach ($list as $item) {
                if ($item['key'] !== '' && $item['key'] !== null) {
                    $dict[trim($item['key'])] = $item['value'];
                } else {
                    $dict[$item['id']] = $item['value'];
                }
            }
            return $dict;
        } else {
            return [];
        }
    }
}