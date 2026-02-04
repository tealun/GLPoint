<?php
declare (strict_types = 1);
namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\Auth;
use app\common\model\ {User , ScoreAppeal, UserScore, ScoreRule};

/**
 * 积分排行榜控制器
 * @Controller("积分排行榜",module="积分",desc="周榜、月榜、年榜、总榜")
 */
class Ranking extends Api
{
    /**
     * 获得积分排行榜
     * @ApiInfo(value="积分排行榜",method="POST",login=true)
     * @Param(name="period", type="string", require=false, desc="周期:week/month/year/total/current")
     * @Returns(name="list", type="array", desc="排名列表")
     */
    public function index()
    {
        // 1. 登录验证
        $userId = Auth::getUserIdFromToken();
        if (!$userId) {
            return $this->error('请先登录');
        }

        // 2. 必须POST请求
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        // 3. 获取周期参数，默认 current
        $period = $this->request->post('period', 'current');
        $validPeriods = ['week', 'month', 'year', 'total', 'current'];
        if (!in_array($period, $validPeriods)) {
            $period = 'current';
        }

        // 4. 构建时间范围
        $where = [];
        switch ($period) {
            case 'week':
                $start = strtotime('monday this week');
                $end = strtotime('sunday this week 23:59:59');
                $where[] = ['create_time', 'between', [$start, $end]];
                break;
            case 'month':
                $start = strtotime(date('Y-m-01 00:00:00'));
                $end = strtotime(date('Y-m-t 23:59:59'));
                $where[] = ['create_time', 'between', [$start, $end]];
                break;
            case 'year':
                $start = strtotime(date('Y-01-01 00:00:00'));
                $end = strtotime(date('Y-12-31 23:59:59'));
                $where[] = ['create_time', 'between', [$start, $end]];
                break;
            case 'total':
                // 不加时间条件
                break;
            case 'current':
            default:
                // 默认当前榜单（可自定义，这里用本周）
                $start = strtotime('monday this week');
                $end = strtotime('sunday this week 23:59:59');
                $where[] = ['create_time', 'between', [$start, $end]];
                break;
        }

        // 5. 查询积分榜（只统计已审核通过的有效记录）
        $userScoreModel = new UserScore();
        $where[] = ['status', '=', 1]; // 只统计审核通过的记录
        $where[] = ['delete_time', '=', 0]; // 排除已删除记录
        $list = $userScoreModel->where($where)
            ->field('user_id, sum(score) as score')
            ->group('user_id')
            ->order('score desc')
            ->limit(50)
            ->select();

        // 6. 获取用户信息
        $userIds = array_column($list->toArray(), 'user_id');
        $userModel = new User();
        $users = $userModel->whereIn('id', $userIds)
            ->field('id,nickname,sex')
            ->select()
            ->column(null, 'id');

        // 7. 组装返回数据
        $result = [];
        foreach ($list as $item) {
            $user = $users[$item['user_id']] ?? [];
            $result[] = [
                'user_id'  => $item['user_id'],
                'nickname' => $user['nickname'] ?? '',
                'score'    => (int)$item['score'],
                'sex'      => $user['sex'] ?? 1 // 默认性别为1（男）,
            ];
        }

        return $this->success('排名获取成功', $result);
    }
}