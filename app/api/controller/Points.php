<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\{Auth, ListToTree, Export}; 
use app\common\model\{User,ScoreRule, UserScore};
use think\facade\Log;

/**
 * 积分管理控制器
 * @Controller("积分管理",module="积分",desc="积分查询、奖励、申请")
 */
class Points extends Api
{
    /**
     * 获取积分记录
     */
    public function index()
    {
        try {
            $rules = (new UserScore())
                ->with(['user', 'scoreRule'])
                ->where('status', 1)
                ->select();

            return $this->success('获取成功', $rules->toArray());

        } catch(\Exception $e) {
            Log::error('获取积分记录失败: ' . $e->getMessage());
            return $this->error('获取积分记录失败');
        }
    }

    /**
     * 获取积分列表
     * 只允许POST，支持type参数筛选
     * @ApiInfo(value="获取积分列表",method="POST",login=true)
     * @Param(name="type", type="string", require=false, desc="时间类型:thisWeek/lastWeek/thisMonth/lastMonth/threeMonths/halfYear/oneYear/all")
     * @Returns(name="list", type="array", desc="积分列表")
     */
    public function list()
    {
        // 增加登录验证
        if (!Auth::checkLogin()) {
            return $this->error('请先登录');
        }

        if (!$this->request->isPost()) {
            return $this->error('只允许POST请求');
        }

        try {
            $user_id = Auth::getUserIdFromToken();
            if (!$user_id) {
                return $this->error('用户信息获取失败');
            }

            $type = $this->request->post('type', 'all');
            Log::debug('[积分列表] user_id: ' . $user_id . ', type: ' . $type . ', post: ' . json_encode($this->request->post()));

            $query = (new UserScore())
                ->where('status', 1)
                ->where('user_id', $user_id);

            // 时间范围处理
            $now = time();
            switch ($type) {
                case 'thisWeek':
                    $start = strtotime('monday this week');
                    $end = strtotime('sunday this week 23:59:59');
                    Log::debug("[积分列表] thisWeek: start=$start, end=$end");
                    $query->where('create_time', '>=', $start)
                          ->where('create_time', '<=', $end);
                    break;
                case 'lastWeek':
                    $start = strtotime('monday last week');
                    $end = strtotime('sunday last week 23:59:59');
                    Log::debug("[积分列表] lastWeek: start=$start, end=$end");
                    $query->where('create_time', '>=', $start)
                          ->where('create_time', '<=', $end);
                    break;
                case 'thisMonth':
                    $start = strtotime(date('Y-m-01 00:00:00'));
                    $end = strtotime(date('Y-m-t 23:59:59'));
                    Log::debug("[积分列表] thisMonth: start=$start, end=$end");
                    $query->where('create_time', '>=', $start)
                          ->where('create_time', '<=', $end);
                    break;
                case 'lastMonth':
                    $start = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
                    $end = strtotime(date('Y-m-t 23:59:59', strtotime('-1 month')));
                    Log::debug("[积分列表] lastMonth: start=$start, end=$end");
                    $query->where('create_time', '>=', $start)
                          ->where('create_time', '<=', $end);
                    break;
                case 'threeMonths':
                    $start = strtotime('-3 months', strtotime(date('Y-m-01 00:00:00')));
                    $end = strtotime(date('Y-m-t 23:59:59'));
                    Log::debug("[积分列表] threeMonths: start=$start, end=$end");
                    $query->where('create_time', '>=', $start)
                          ->where('create_time', '<=', $end);
                    break;
                case 'halfYear':
                    $start = strtotime('-6 months', strtotime(date('Y-m-01 00:00:00')));
                    $end = strtotime(date('Y-m-t 23:59:59'));
                    Log::debug("[积分列表] halfYear: start=$start, end=$end");
                    $query->where('create_time', '>=', $start)
                          ->where('create_time', '<=', $end);
                    break;
                case 'oneYear':
                    $start = strtotime('-1 year', strtotime(date('Y-m-01 00:00:00')));
                    $end = strtotime(date('Y-m-t 23:59:59'));
                    Log::debug("[积分列表] oneYear: start=$start, end=$end");
                    $query->where('create_time', '>=', $start)
                          ->where('create_time', '<=', $end);
                    break;
                case 'all':
                default:
                    Log::debug("[积分列表] all: 不加时间条件");
                    // 不加时间条件
                    break;
            }

            $rules = $query->field(['id', 'score','remark', 'create_time'])->order('create_time', 'desc')->select();
            Log::debug('[积分列表] 查询结果数量: ' . count($rules));

            $result = array_map(function($item) {
                $arr = is_object($item) ? $item->toArray() : (array)$item;
                return [
                    'id' => $arr['id'],
                    'score' => $arr['score'],
                    'create_time' => $arr['create_time'],
                    'remark' => $arr['remark'] ?? '无', // 确保remark存在
                ];
            }, is_object($rules) ? $rules->toArray() : (array)$rules);

            return $this->success('获取成功', $result);

        } catch(\Exception $e) {
            Log::error('获取积分记录失败: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return $this->error('获取积分记录失败: ' . $e->getMessage());
        }
    }

    /**
     * 申请积分
     * @ApiInfo(value="申请积分",method="POST",login=true)
     * @Param(name="points", type="integer", require=true, desc="申请积分数")
     * @Param(name="reason", type="string", require=true, desc="申请理由")
     * @Param(name="rule_id", type="integer", require=true, desc="规则ID")
     */
    public function require()
    {
        try {
            // 验证token并获取当前用户
            $auth = Auth::checkLogin();
            if (!$auth) {
                return $this->error('请先登录');
            }
            $user_id = Auth::getUserIdFromToken();

            // 获取POST数据
            $data = $this->request->post();
            if (
                !isset($data['points']) ||
                !isset($data['reason']) ||
                !isset($data['rule_id'])
            ) {
                return $this->error('参数错误');
            }

            $record = [
                'user_id'       => $user_id,
                'score'         => (int)$data['points'],
                'remark'        => htmlspecialchars(trim($data['reason']), ENT_QUOTES, 'UTF-8'),
                'score_rule_id' => (int)$data['rule_id'],
                'require_time'  => time(),
                'status' => 0, // 正在申请中 -1拒绝 0待审核 1通过
            ];

            UserScore::create($record);

            return $this->success('积分申请成功');
        } catch (\Exception $e) {
            Log::error('积分申请失败: ' . $e->getMessage());
            return $this->error('积分申请失败');
        }
    }

    /**
     * 获取积分申请列表
     */
    public function requireList()
    {
        try {
            // 验证token并获取当前用户
            $auth = Auth::checkLogin();
            if (!$auth) {
                return $this->error('请先登录');
            }

            $data = $this->request->post();
            $user_id = isset($data['user_id']) ? (int)$data['user_id'] : Auth::getUserIdFromToken();
            $user = User::find($user_id);

            if (!$user) {
                return $this->error('用户不存在');
            }

            // 判断是否为操作员
            $isOperator = (isset($user->user_group_id) && $user->user_group_id == 3);

            $UserScore = new UserScore();
            $query = $UserScore->with(['user', 'scoreRule'])
                ->where('require_time', ">",0);

            if (!$isOperator) {
                $query->where('user_id', $user_id);
            }

            $records = $query->order('id', 'desc')->select();

            $result = [
                'applying' => [],
                'processed' => [],
            ];

            foreach ($records as $record) {
                if ($record->status == 0) {
                    $result['applying'][] = $record;
                } elseif ($record->status == 1 || $record->status == -1) {
                    $result['processed'][] = $record;
                }
            }

            return $this->success('获取成功', [
                'applying' => array_values(array_map(function($item){
                    $arr = $item->toArray();
                    $arr['user'] = $arr['user'] ?? null;
                    $arr['score_rule'] = $arr['score_rule'] ?? null;
                    return $arr;
                }, $result['applying'])),
                'processed' => array_values(array_map(function($item){
                    $arr = $item->toArray();
                    $arr['user'] = $arr['user'] ?? null;
                    $arr['score_rule'] = $arr['score_rule'] ?? null;
                    return $arr;
                }, $result['processed'])),
            ]);
        } catch (\Exception $e) {
            Log::error('获取积分申请列表失败: ' . $e->getMessage());
            return $this->error('获取积分申请列表失败');
        }
    }

    /**
     * 积分奖励
     * @ApiInfo(value="积分奖励",method="POST",login=true)
     * @Param(name="receivers", type="array", require=true, desc="接收人列表")
     * @Param(name="points", type="integer", require=true, desc="积分数")
     * @Param(name="reason", type="string", require=true, desc="奖励理由")
     * @Param(name="rule_id", type="integer", require=true, desc="规则ID")
     */
    public function award()
    {
        try {
            // 验证token并获取当前用户
            $auth = Auth::checkLogin();
            if (!$auth) {
                return $this->error('请先登录');
            }
            $user = Auth::getUserIdFromToken();
            $user = User::find($user);

            // 检查用户权限
            if (!$user || $user->user_group_id != 3) {
                return $this->error('无发放积分权限');
            }
            // 获取POST数据
            $data = $this->request->post();
            if (
                empty($data['receivers']) ||
                !is_array($data['receivers']) ||
                !isset($data['points']) ||
                !isset($data['reason'])
            ) {
                return $this->error('参数错误');
            }

            $giver_id = $user->id;
            $score = (int)$data['points'];
            $score_rule_id = (int)$data['rule_id'];
            // 防止SQL注入，严格过滤remark内容
            $remark = htmlspecialchars(trim($data['reason']), ENT_QUOTES, 'UTF-8');
            $receivers = $data['receivers'];

            foreach ($receivers as $receiver) {
                $user_id = is_array($receiver) ? $receiver['id'] : $receiver;
                // 只查询接收人最新一条积分记录的 after 字段
                $last = UserScore::where('user_id', $user_id)
                    ->order('id', 'desc')
                    ->field('after')
                    ->find();
                $before = $last ? (int)$last->after : 0;
                $after = $before + $score;

                $record = [
                    'user_id'       => $user_id,
                    'giver_id'      => $giver_id,
                    'score'         => $score,
                    'before'        => $before,
                    'after'         => $after,
                    'score_rule_id' => $score_rule_id,
                    'remark'        => $remark,
                    'status'        => 1,
                    'create_time'   => time(),
                ];
                UserScore::create($record);
                // 更新用户积分
                User::where('id', $user_id)
                    ->update(['score' => $after]);
            }

            return $this->success('积分发放成功');
        } catch (\Exception $e) {
            Log::error('积分发放失败: ' . $e->getMessage());
            return $this->error('积分发放失败');
        }
    }

    /**
     * 积分详情
     */
    public function detail($id)
    {
        // 只允许POST
        if (!$this->request->isPost()) {
            return $this->error('只允许POST请求');
        }

        // 校验登录
        if (!Auth::checkLogin()) {
            return $this->error('请先登录');
        }

        $user_id = Auth::getUserIdFromToken();
        if (!$user_id) {
            return $this->error('用户信息获取失败');
        }

        // 查询当前用户信息
        $user = User::find($user_id);
        if (!$user) {
            return $this->error('用户不存在');
        }

        // 查询积分记录
        $record = UserScore::where('id', $id)->find();
        if (!$record) {
            return $this->error('积分记录不存在');
        }

        // 判断权限：不是操作员且不是本人则禁止访问
        if ($user->user_group_id != 3 && $record->user_id != $user_id) {
            return $this->error('无权查看该积分记录');
        }

        // 获取receiver_name
        $receiver = User::find($record->user_id);
        $receiver_name = $receiver ? $receiver->nickname : '';

        // 获取giver_name
        $giver = null;
        if (!empty($record->giver_id)) {
            $giver = User::find($record->giver_id);
        }
        $giver_name = $giver ? $giver->nickname : '系统';

        // 获取规则信息
        $rule = ScoreRule::find($record->score_rule_id);
        // 获取规则分类名称
        $rule_category = '';
        if ($rule && isset($rule->score_category_id)) {
            $category = \app\common\model\ScoreCategory::find($rule->score_category_id);
            $rule_category = $category ? $category->category_name : '';
        }
        $rule_name = $rule ? $rule->rule_name : '';

        // can_appeal 逻辑：review_time大于0则不可申诉
        $can_appeal = !(isset($record->review_time) && $record->review_time > 0);

        $result = [
            [
                'id' => (string)$record->id,
                'points' => (int)$record->score,
                'created_at' => date('Y-m-d H:i:s', is_numeric($record->create_time) ? $record->create_time : strtotime($record->create_time)),
                'receiver_name' => $receiver_name,
                'giver_name' => $giver_name,
                'rule_category' => $rule_category,
                'rule_name' => $rule_name,
                'reason' => $record->remark,
                'can_appeal' => $can_appeal
            ]
        ];

        return $this->success('获取成功', $result);
    }

    /**
     * 审核积分申请（通过）
     * @ApiInfo(value="审核通过积分申请",method="POST",login=true)
     * @Param(name="id", type="integer", require=true, desc="积分记录ID")
     * @Param(name="review_reason", type="string", require=false, desc="审核理由")
     */
    public function approve($id = null)
    {
        try {
            // 验证登录
            if (!Auth::checkLogin()) {
                return $this->error('请先登录');
            }

            $userId = Auth::getUserIdFromToken();
            $user = User::find($userId);

            // 检查权限（只有操作员可以审核）
            if (!$user || $user->user_group_id != 3) {
                return $this->error('无审核权限');
            }

            if (!$id) {
                return $this->error('参数错误');
            }

            // 查询积分申请记录
            $record = UserScore::find($id);
            if (!$record) {
                return $this->error('积分记录不存在');
            }

            // 检查是否为申请中的记录
            if ($record->status != 0 || $record->require_time == 0) {
                return $this->error('该记录不是待审核状态');
            }

            // 获取用户当前积分
            $lastRecord = UserScore::where('user_id', $record->user_id)
                ->where('status', 1)
                ->order('id', 'desc')
                ->field('after')
                ->find();
            $before = $lastRecord ? (float)$lastRecord->after : 0;

            // 审核通过，更新记录
            $record->status = 1; // 已通过
            $record->reviewer_id = $userId;
            $record->review_time = time();
            $record->review_reason = $this->request->post('review_reason', '');
            $record->before = $before;
            $record->after = $before + $record->score;
            $record->save();

            // 更新用户积分
            User::where('id', $record->user_id)
                ->update(['score' => $record->after]);

            return $this->success('审核通过');

        } catch (\Exception $e) {
            Log::error('审核积分申请失败: ' . $e->getMessage());
            return $this->error('审核失败');
        }
    }

    /**
     * 拒绝积分申请
     * @ApiInfo(value="拒绝积分申请",method="POST",login=true)
     * @Param(name="id", type="integer", require=true, desc="积分记录ID")
     * @Param(name="review_reason", type="string", require=true, desc="拒绝理由")
     */
    public function reject($id = null)
    {
        try {
            // 验证登录
            if (!Auth::checkLogin()) {
                return $this->error('请先登录');
            }

            $userId = Auth::getUserIdFromToken();
            $user = User::find($userId);

            // 检查权限（只有操作员可以审核）
            if (!$user || $user->user_group_id != 3) {
                return $this->error('无审核权限');
            }

            if (!$id) {
                return $this->error('参数错误');
            }

            $reason = $this->request->post('review_reason');
            if (empty($reason)) {
                return $this->error('拒绝理由不能为空');
            }

            // 查询积分申请记录
            $record = UserScore::find($id);
            if (!$record) {
                return $this->error('积分记录不存在');
            }

            // 检查是否为申请中的记录
            if ($record->status != 0 || $record->require_time == 0) {
                return $this->error('该记录不是待审核状态');
            }

            // 拒绝申请
            $record->status = -1; // 已拒绝
            $record->reviewer_id = $userId;
            $record->review_time = time();
            $record->review_reason = htmlspecialchars(trim($reason), ENT_QUOTES, 'UTF-8');
            $record->save();

            return $this->success('已拒绝');

        } catch (\Exception $e) {
            Log::error('拒绝积分申请失败: ' . $e->getMessage());
            return $this->error('拒绝失败');
        }
    }
}
