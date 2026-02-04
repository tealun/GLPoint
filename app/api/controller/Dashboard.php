<?php
declare (strict_types = 1);
namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\Auth as AuthLibrary;
use app\api\model\UserScore;
use think\facade\Request;

/**
 * 仪表板控制器
 * @Controller("仪表板",module="统计",desc="积分统计、图表、动态")
 */
class Dashboard extends Api
{

    

    /**
     * 获取积分趋势图数据
     * POST
     * @ApiInfo(value="积分趋势图",method="POST",login=true)
     * @Param(name="type", type="string", require=false, desc="类型:week/month/year/total")
     * @Returns(name="trendDataArray", type="array", desc="趋势数据")
     */
    public function charts()
    {
        // 1. 校验登录
        $user_id = AuthLibrary::getUserIdFromToken();
        if (!$user_id) {
            return $this->error('未登录或登录已过期');
        }

        // 2. 获取type参数
        $type = $this->request->post('type', 'week');

        $trendDataArray = [];

        switch ($type) {
            case 'week':
                // 本周每天积分
                $startOfWeek = strtotime('monday this week');
                $days = [];
                for ($i = 0; $i < 7; $i++) {
                    $dayStart = strtotime("+$i day", $startOfWeek);
                    $dayEnd = strtotime('23:59:59', $dayStart);
                    $score = UserScore::where('user_id', $user_id)
                        ->where('delete_time', 0)
                        ->where('status', 1)
                        ->where('create_time', '>=', $dayStart)
                        ->where('create_time', '<=', $dayEnd)
                        ->sum('score');
                    $days[] = [
                        'day' => '周' . ['一','二','三','四','五','六','日'][$i],
                        'value' => (int)$score
                    ];
                }
                $trendDataArray = $days;
                break;
            case 'month':
                // 本月每天积分
                $startOfMonth = strtotime(date('Y-m-01 00:00:00'));
                $daysInMonth = date('t');
                $days = [];
                for ($i = 0; $i < $daysInMonth; $i++) {
                    $dayStart = strtotime("+$i day", $startOfMonth);
                    $dayEnd = strtotime('23:59:59', $dayStart);
                    $score = UserScore::where('user_id', $user_id)
                        ->where('delete_time', 0)
                        ->where('status', 1)
                        ->where('create_time', '>=', $dayStart)
                        ->where('create_time', '<=', $dayEnd)
                        ->sum('score');
                    $days[] = [
                        'day' => ($i + 1) . '日', // 保持"日"字，前端不再添加
                        'value' => (int)$score
                    ];
                }
                $trendDataArray = $days;
                break;
            case 'year':
                // 本年每月积分
                $year = date('Y');
                $months = [];
                for ($m = 1; $m <= 12; $m++) {
                    $monthStart = strtotime("$year-$m-01 00:00:00");
                    $monthEnd = strtotime(date('Y-m-t 23:59:59', $monthStart));
                    $score = UserScore::where('user_id', $user_id)
                        ->where('delete_time', 0)
                        ->where('status', 1)
                        ->where('create_time', '>=', $monthStart)
                        ->where('create_time', '<=', $monthEnd)
                        ->sum('score');
                    $months[] = [
                        'month' => $m . '月', // 统一由后端输出完整格式
                        'value' => (int)$score
                    ];
                }
                $trendDataArray = $months;
                break;
            case 'total':
                // 历年积分
                $first = UserScore::where('user_id', $user_id)
                    ->where('delete_time', 0)
                    ->where('status', 1)
                    ->order('create_time', 'asc')
                    ->value('create_time');
                $last = UserScore::where('user_id', $user_id)
                    ->where('delete_time', 0)
                    ->where('status', 1)
                    ->order('create_time', 'desc')
                    ->value('create_time');
                if (!$first || !$last) {
                    $trendDataArray = [];
                } else {
                    $startYear = date('Y', $first);
                    $endYear = date('Y', $last);
                    $years = [];
                    for ($y = $startYear; $y <= $endYear; $y++) {
                        $yearStart = strtotime("$y-01-01 00:00:00");
                        $yearEnd = strtotime("$y-12-31 23:59:59");
                        $score = UserScore::where('user_id', $user_id)
                            ->where('delete_time', 0)
                            ->where('status', 1)
                            ->where('create_time', '>=', $yearStart)
                            ->where('create_time', '<=', $yearEnd)
                            ->sum('score');
                        $years[] = [
                            'year' => (string)$y,
                            'value' => (int)$score
                        ];
                    }
                    $trendDataArray = $years;
                }
                break;
            default:
                return $this->error('type参数不正确');
        }

        return $this->success('获取成功', ['trendDataArray' => $trendDataArray]);
    }

    /**
     * 获取积分汇总数据
     * POST
     */
    public function summary()
    {
        $user_id = AuthLibrary::getUserIdFromToken();
        if (!$user_id) {
            return $this->error('未登录或登录已过期');
        }

        $UserScore = new UserScore();

        $summary = [
            'week'  => (int)$UserScore->getUserWeekScore($user_id),
            'month' => (int)$UserScore->getUserMonthScore($user_id),
            'year'  => (int)$UserScore->getUserYearScore($user_id),
            'total' => (int)$UserScore->getUserTotalScore($user_id),
        ];

        return $this->success('获取成功', ['summary' => $summary]);
    }

    /**
     * 获取最新积分动态
     * POST
     */
    public function dynamics()
    {
        $user_id = AuthLibrary::getUserIdFromToken();
        if (!$user_id) {
            return $this->error('未登录或登录已过期');
        }

        $UserScore = new UserScore();
        $records = $UserScore->getUserScoreRecords($user_id, 1, 10);

        $dynamics = [];
        foreach ($records as $item) {
            $createTime = $item['create_time'];
            if (!is_numeric($createTime)) {
                $createTime = strtotime($createTime);
            }
            $dynamics[] = [
                'id' => $item['id'],
                'created_at' => date('Y-m-d H:i', $createTime),
                'change' => (int)$item['score'],
                'remark' => $item['remark'] ?: '无',
            ];
        }

        return $this->success('获取成功', ['dynamics' => $dynamics]);
    }
}
