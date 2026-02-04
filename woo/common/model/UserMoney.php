<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;

class UserMoney extends App
{
    public function beforeInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!empty($this->user_id)) {
            $this->before = model('User')->where(model('User')->getPk(), '=', $this->user_id)->value('money');
            $this->after = $this->before + floatval($this->money);
        }
        return $parent_return;
    }

    public function afterInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $user = model('User')->find($this->user_id);
        if ($user) {
            $user->money = $this->after;
            $user->save();
        }
        return $parent_return;
    }

    public function addMoneyLog($user_id, $money, $foreign, $foreign_id, $remark = '')
    {
        $this->user_id    = $user_id;
        $this->money      = $money;
        $this->foreign    = $foreign;
        $this->foreign_id = $foreign_id;
        $this->remark     = $remark;
        $this->save();
    }
}