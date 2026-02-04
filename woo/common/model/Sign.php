<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\facade\Auth;

class Sign extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
    }

    public function checkToday($value)
    {
        if ($value <= date('Y-m-d')) {
            return true;
        }
        return '签到日期不能超过' . date('Y-m-d');
    }

    public function beforeInsertCall()
    {
        if (!empty($this['date'])) {
            $this['year'] = date('Y', strtotime($this['date']));
            $this['month'] = date('m', strtotime($this['date']));
            $this['day'] = date('d', strtotime($this['date']));
            $this['time'] = date('H:i:s');
            $continue = $this->where([
                    ['date', '=', date('Y-m-d', strtotime($this['date'])  - 86400)],
                    ['user_id', '=', Auth::user('id')]
                ])
                ->value('continue');
            $this['continue'] = $continue ? $continue + 1 : 1;
            $this['score'] = sign_give_score($this['continue']);
            model('UserScore')->createData([
                'user_id' => Auth::user('id'),
                'score' => $this['score'],
                'remark' => "签到【{$this['date']}】获得"
            ]);
        }
    }
}