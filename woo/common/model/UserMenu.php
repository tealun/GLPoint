<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\helper\Str;

class UserMenu extends App
{
    public function getCustomCache()
    {
        return [
            'nav' => [
                'where' => [
                    ['is_nav', '=', 1]
                ]
            ],
            'uni' => [
                'where' => [
                    ['is_uni', '=', 1]
                ]
            ]
        ];
    }


    public function beforeWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
//        if (!empty($this['addon'])) {
//            $this['addon'] = strtolower(trim($this['addon']));
//        }
//        if (!empty($this['controller'])) {
//            $this['controller'] = Str::snake(trim($this['controller']));
//        }
//        if (!empty($this['action'])) {
//            $this['action'] = strtolower(trim($this['action']));
//        }
        return $parent_return;
    }

}