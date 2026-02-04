<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use think\facade\Cache;
use think\facade\Cookie;
use think\facade\Db;
use woo\common\Auth;
use woo\common\helper\Str;

class User extends App
{
    protected function afterStart()
    {
        $this->tableTab['basic']['siderbar'] = [
            [
                'foreign' => 'UserGroup'
            ],
            [
                'foreign' => 'UserGrade'
            ],
        ];
        //$this->tableTab['basic']['table']['skin'] = 'line';
        //$this->tableTab['basic']['table']['even'] = true;
        parent::{__FUNCTION__}();
    }

    // 导入示范
    public function setImportItemAttr($item)
    {
        $item['salt'] = Str::random(mt_rand(4, 8), 0);// 加字段
        //$item['password']  = Auth::password($item['password'], $item['salt']);// 改字段值
        //$item['pay_password']  = Auth::password($item['password'], $item['salt']);// 改字段值
        $item['status'] = 'verified';// 默认都激活状态
        // 用户组如果是中文 没有直接匹配好数字 进行查询匹配 当然这个字段还有更好的处理方式 这样查询 效率太低了
        // $item['user_group_id'] = model('UserGroup')->where('title', '=', $item['user_group_id'])->value('id');
        return $item;
    }

    public function beforeInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (empty($this->salt)) {
            $this->salt = Str::random(mt_rand(4, 8), 0);
        }
        if (empty($this->pay_password)) {
            $this->pay_password = $this->password;
        }
        return $parent_return;
    }

    public function afterWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!empty($this->password) || !empty($this->pay_password)) {
            $pk = $this->getPk();
            $salt = empty($this->salt) ? $this->where($pk, '=', $this[$pk])->value('salt') : $this->salt;
            $data = [];
            if (!empty($this->password)) {
                $data['password'] = Auth::password($this->password, $salt);
            }
            if (!empty($this->pay_password)) {
                $data['pay_password'] = Auth::password($this->pay_password, $salt);
            }
            Db::name($this->name)
                ->where($pk, '=', $this[$pk])
                ->update($data);
        }
        return $parent_return;
    }

    public function beforeUpdateCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (isset($this->password)) {
            if (empty(trim($this->password)) || strlen($this->password) == 32) {
                unset($this->password);
            }
        }
        if (isset($this->pay_password)) {
            if (empty(trim($this->pay_password)) || strlen($this->pay_password) == 32) {
                unset($this->pay_password);
            }
        }
        return $parent_return;
    }

    public function checkPwd($val)
    {
        if (empty($val)) {
            return true;
        }
        $count = ['upper' => 0, 'lower' => 0, 'number' => 0, 'else' => 0];
        for ($i = 0; $i <= strlen($val) - 1; $i++) {
            $ord = ord($val[$i]);
            if ($ord >= 48 && $ord <= 57) {
                $count['number'] = 1;
            } elseif ($ord >= 65 && $ord <= 90) {
                $count['upper'] = 1;
            } elseif ($ord >= 97 && $ord <= 122) {
                $count['lower'] = 1;
            } else {
                $count['else'] = 1;
            }
        }
        if ($count['upper'] + $count['lower'] + $count['number'] + $count['else'] < 2) {
            return '密码过于简单';
        }
        return true;
    }

    public function getTodayRegisterNumber()
    {
        return $this->where([
            ['create_time', '>=', strtotime(date('Y-m-d'))],
            ['create_time', '<=', strtotime(date('Y-m-d 23:59:59'))]
        ])->count();
    }

    public function getTodayBirthdayNumber()
    {
        return $this->where([
            ['birthday', '=', date('Y-m-d')]
        ])->count();
    }

    public function getTodayLoginNumber()
    {
        return $this->where([
            ['login_time', '>=', strtotime(date('Y-m-d'))],
            ['login_time', '<=', strtotime(date('Y-m-d 23:59:59'))]
        ])->count();
    }

    public function getTodayRechargeSum()
    {
        return model('Recharge')
            ->where([
                ['create_time', '>=', strtotime(date('Y-m-d'))],
                ['create_time', '<=', strtotime(date('Y-m-d 23:59:59'))]
            ])->sum('money');
    }

    public function getWeekRegisterData()
    {
        $dark = Cookie::has(app('http')->getName() . 'dark-mode')? !!Cookie::get(app('http')->getName() . 'dark-mode'): false;
        if (Cache::has('user_charts_data')) {
            $cache = Cache::get('user_charts_data');
            $data = $cache['data'];
            $value = $cache['value'];
        } else {
            $now = time();
            for ($i = 6; $i >= 0; $i--) {
                $data[] = date('m-d', $now - $i * 86400);
                $start = date('Y-m-d', $now - $i * 86400);
                $end = date('Y-m-d 23:59:59', $now - $i * 86400);
                $value[] = model('User')
                    ->whereTime('create_time', 'between', [$start, $end])
                    ->count();
            }
            Cache::tag('User')->set('user_charts_data', ['data' => $data, 'value' => $value], 86400);
        }
        return [
            'title' => [
                'text' =>  '',
                'left' => 'center',
                'top' => '10px',
                'textStyle' => [
                    'fontWeight' => 'normal',
                    'color' => !$dark ? '#888' : '#b4c2c5',
                    'fontSize' => '13px'
                ]
            ],
            'grid' => [
                'top' => '10px',
                'bottom' => '20px',
                'left' => '15px',
                'right' => '20px',
            ],
            'tooltip' => [
                'trigger' => 'item',
                'formatter' => '{b}:{c}'
            ],
            'xAxis' => [
                'type' => 'category',
                'data' => $data,
                'boundaryGap'=> false,
                'splitLine' => [
                    'show' => true,
                    'lineStyle' => [
                        'color' => !$dark ? '#f6ffed' : '#535c65'
                    ]
                ],
                'axisTick' => [
                    'show' => false
                ],
                'axisLine' => [
                    'lineStyle' => [
                        'color' => '#5fb878',
                        'width' => 2,
                        'opacity' => 0.8
                    ]
                ],
                'axisLabel' => [
                    'color' => !$dark ? '#888' : '#b4c2c5',
                ]
            ],
            'yAxis' => [
                'type' => 'value',
                'axisTick' => [
                    'show' => false
                ],
                'splitLine' => [
                    'show' => true,
                    'lineStyle' => [
                        'color' =>  !$dark ? '#f6ffed' : '#535c65'
                    ]
                ],
                'axisLine' => [
                    'lineStyle' => [
                        'color' => '#5fb878',
                        'width' => 2,
                        'opacity' => 0.8
                    ]
                ],
                'axisLabel' => [
                    'color' => '#fff'
                ]
            ],
            'series' => [
                [
                    'data' => $value,
                    'type' => 'line',
                    'smooth' => true,
                    'itemStyle' => [
                        'color' => '#b2f0d1',
                        'borderColor' => '#00cc66'
                    ],
                    'areaStyle' => [
                    ],
                    'lineStyle' => [
                        'color' => '#00cc66',
                        'wdith' => 1
                    ]
                ]
            ]
        ];
    }
}