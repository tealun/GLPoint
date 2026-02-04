<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;

class Recharge extends App
{
    public function afterInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!empty($this->user_id)) {
            model('UserMoney')->addMoneyLog($this->user_id, floatval($this->money), $this->name, $this->id, date('Ymd H:i:s') . "成功充值{$this->money}元");
        }
        return $parent_return;
    }
}