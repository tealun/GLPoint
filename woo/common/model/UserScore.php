<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;

class UserScore extends App
{

    public function beforeInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!empty($this->user_id)) {
            $this->before = model('User')->where(model('User')->getPk(), '=', $this->user_id)->value('score');
            $this->after = $this->before + floatval($this->score);
        }
        return $parent_return;
    }

    public function afterInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $user = model('User')->find($this->user_id);
        if ($user) {
            $user->score = $this->after;
            $grade = model('UserGrade')
                ->where('min', '<=', $this->after)
                ->where('max', '>', $this->after)
                ->value(model('UserGrade')->getPk());
            $grade = intval($grade);
            if ($grade != $user->user_grade_id) {
                $user->user_grade_id = $grade;
            }
            $user->save();
        }
        return $parent_return;
    }
}