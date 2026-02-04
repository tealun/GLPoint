<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\helper\Str;

class Setting extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
    }

    public function beforeWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!empty($this->setting_group_id) && !empty($this->var)) {
            $parent_var = model('SettingGroup')->where('id', '=', intval($this->setting_group_id))->value('var');
            if (strpos($this->var, $parent_var . '_') !== 0) {
                $this->var =  $parent_var . '_' . $this->var;
            }
        }
        return $parent_return;
    }
}