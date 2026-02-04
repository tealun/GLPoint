<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\helper\Pinyin;

class Region extends App
{
    public function beforeWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $data = $this->getData();
        if (!empty($data['title']) && empty($data['pinyin'])) {
            $this['pinyin'] = Pinyin::get($data['title']);
        }
        if (!empty($data['title']) && empty($data['jianpin'])) {
            $this['jianpin'] = Pinyin::get($data['title'], true);
        }
        if (!empty($data['title']) && empty($data['first'])) {
            $this['first'] = substr(Pinyin::get($data['title'], true), 0, 1);
        }
        return $parent_return;
    }
}