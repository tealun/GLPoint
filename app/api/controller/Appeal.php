<?php
declare (strict_types = 1);
namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\Auth;
use app\common\model\ {User , ScoreAppeal, UserScore, ScoreRule};
use think\facade\Log;

/**
 * 申诉管理控制器
 * @Controller("申诉管理",module="积分",desc="积分申诉提交、审核、回复")
 */
class Appeal extends Api 
{

    /**
     * 申诉首页
     * @ApiInfo(value="获取申诉列表",method="GET",login=true)
     * @Returns(name="list", type="array", desc="申诉记录列表")
     */
    public function index()
    {
        // 1. 校验登录
        $userId = Auth::getUserIdFromToken();
        if (!$userId) {
            return $this->error('请先登录');
        }
        // 2. 获取用户信息
        $user = User::find($userId);
        if (!$user) {
            return $this->error('用户不存在');
        }
        // 3. 判断是否为操作员，获取申诉记录
        if ($user->user_group_id == 3) {
            // 操作员，获取所有申诉记录
            $appeals = ScoreAppeal::order('create_time DESC')->select();
        } else {
            // 普通用户，只获取自己的申诉记录
            $appeals = ScoreAppeal::where('user_id', $userId)->order('create_time DESC')->select();
        }
        if ($appeals->isEmpty()) {
            return $this->success('没有申诉记录', []);
        }

        return $this->success('申诉记录获取成功', $appeals ->toArray());
    }

    /**
     * 创建申诉
     * @ApiInfo(value="检查记录可申诉性",method="POST",login=true)
     * @Param(name="recordId", type="integer", require=false, desc="积分记录ID")
     * @Returns(name="records", type="array", desc="可申诉记录列表")
     */
    public function create($recordId = null)
    {
        // 1. 校验登录
        $userId = Auth::getUserIdFromToken();   
        if (!$userId) {
            return $this->error('请先登录');
        }
        // 2. 获取用户信息
        $user = User::find($userId);
        if (!$user) {
            return $this->error('用户不存在');
        }

        // 3. 组件需要返回的记录列表
        $records = [];
        // 3. 检查记录是否存在
        if ($recordId) {
        // 如果传入了记录ID，检查该记录是否存在
        $record = UserScore::where('id', $recordId)
            ->where('user_id', $userId)
            ->where('delete_time', 0)
            ->where('status', 1) // 只获取有效记录
            ->find();
        if (!$record) {
            return $this->error('记录不存在或已被删除');
        }
        // 如果记录存在，检查是否已申诉过
        $existingAppeal = ScoreAppeal::where('user_id', $userId)
            ->where('user_score_id', $recordId)
            ->where('status', 0) // 只获取待处理的申诉
            ->find();
        if ($existingAppeal) {
            return $this->error('该记录已存在未处理的申诉');
        } else {
            $records[] = $record;
        }

        // 5. 返回创建申诉需要的记录列表
        return $this->success('该记录可申诉', [
            'records' => $records
        ]);
    }
        // 如果没有传入记录ID，则获取用户的所有有效记录，且未申诉过
        $oneMonthAgo = strtotime('-1 month');
        // 先查出用户一个月内的有效记录
        $records = UserScore::where('user_id', $userId)
            ->where('delete_time', 0)
            ->where('status', 1) // 只获取有效记录
            ->where('create_time', '>=', $oneMonthAgo)
            ->order('create_time DESC')
            ->select();

        if ($records->isEmpty()) {
            return $this->error('没有可申诉的记录');
        }

        // 获取这些记录的ID
        $recordIds = $records->column('id');
        // 查找这些记录中已存在未处理申诉的记录ID
        $appealedIds = ScoreAppeal::where('user_id', $userId)
            ->whereIn('user_score_id', $recordIds)
            ->where('status', 0) // 只排除未处理的申诉
            ->column('user_score_id');

        // 过滤掉已申诉的记录
        $filteredRecords = $records->filter(function($record) use ($appealedIds) {
            return !in_array($record->id, $appealedIds);
        })->values();

        if ($filteredRecords->isEmpty()) {
            return $this->error('没有可申诉的记录');
        }

        return $this->success('获得可申诉记录', [
            'records' => $filteredRecords
        ]);
    }

    /**
     * 提交申诉创建一条申诉记录
     * @ApiInfo(value="提交申诉",method="POST",login=true)
     * @Param(name="record_id", type="integer", require=true, desc="积分记录ID")
     * @Param(name="reason", type="string", require=true, desc="申诉理由")
     * @param int $recordId 申诉记录ID
     * @return \think\Response
     * @throws \think\Exception
     */
    public function submit()
    {
        // 1. 校验登录
        $userId = Auth::getUserIdFromToken(); 
        if (!$userId) {
            return $this->error('请先登录');
        }
        // 2. 获取用户信息
        $user = User::find($userId);
        if (!$user) {
            return $this->error('用户不存在');
        }

        // 3. 获取请求参数
        $params = $this->request->post();
        $recordId = isset($params['record_id']) ? (int)$params['record_id'] : 0;
        $reason = isset($params['reason']) ? trim($params['reason']) : '';

        // 4. 校验参数
        if (!$recordId) {
            return $this->error('记录ID不能为空');
        }
        if (!$reason) {
            return $this->error('申诉理由不能为空');
        }

        // 5. 创建申诉记录
        $appeal = ScoreAppeal::create([
            'user_id'   => $userId,
            'reply_user_id' => 1, // 初始时设置为1，后续可更新为操作员ID
            'user_score_id' => $recordId,
            'reason'    => $reason,
            'status'    => 0, // 0:待处理
        ]);
        return $this->success('申诉创建成功', ['appeal_id' => $appeal->id]);
    }

    /**
     * 获取申诉详情
     * @ApiInfo(value="申诉详情",method="GET",login=true)
     * @Param(name="id", type="integer", require=true, desc="申诉ID")
     */
    public function detail($id= null)
    {
        // 1. 校验登录
        $userId = Auth::getUserIdFromToken();
        if (!$userId) {
            Log::error('[Appeal.detail] 未登录');
            return $this->error('请先登录');
        }
        // 2. 校验参数
        if (!$id) {
            Log::error('[Appeal.detail] 申诉ID为空');
            return $this->error('申诉ID不能为空');
        }
        // 3. 查询申诉记录
        $appeal = ScoreAppeal::find($id);

        if (!$appeal) {
            return $this->error('申诉记录不存在');
        }

        // 解决 $appeal->user_score_id 为 null 的问题
        // 强制从 data 属性获取
        $user_score_id = $appeal->user_score_id;
        if ($user_score_id === null && isset($appeal->getData()['user_score_id'])) {
            $user_score_id = $appeal->getData('user_score_id');
        }

        // 4. 校验权限
        $user = User::find($userId);
        if (!$user) {
            return $this->error('用户不存在');
        }
        if ($user->user_group_id != 3 && $appeal->user_id != $userId) {
            return $this->error('无权查看该申诉详情');
        }

        // 5. 组装record数组
        $recordArr = [];
        try {
            // 开启SQL日志
            \think\facade\Db::listen(function($sql, $time, $explain){
                Log::info('[Appeal.detail] SQL: ' . $sql . ' [' . $time . 'ms]');
            });

            $userScore = UserScore::where('id', $user_score_id)
                ->where('delete_time', 0)
                ->where('status', 1) // 只获取有效记录
                ->find();

            if ($userScore) {
                $recordArr = [
                    'create_time' => $userScore->create_time,
                    'score' => $userScore->score,
                    'remark' => $userScore->remark,
                ];
            }
        } catch (\Exception $e) {
            Log::error('[Appeal.detail] 查询UserScore出错: ' . $e->getMessage());
            return $this->error('查询记录出错: ' . $e->getMessage());
        }

        // 6. 组装appeal数组
        $appealArr = [
            'create_time' => $appeal->create_time,
            'reason' => $appeal->reason,
        ];

        // 7. 组装reply数组
        if ($appeal->reply_user_id > 1) {
            $replyUser = User::find($appeal->reply_user_id);
            $replyNickname = $replyUser ? $replyUser->nickname : '未回复';
        } else {
            $replyNickname = '未回复';
        }
        $replyArr = [
            'id' => $appeal->id,
            'name' => $replyNickname,
            'reply' => $appeal->reply,
            'update_time' => $appeal->update_time >= $appeal->create_time ? $appeal->update_time : '未回复',
            'status' => $appeal->status, // -1:已拒绝, 0:待处理, 1:已处理, 2:已取消
        ];

        // 8. 返回
        Log::info('[Appeal.detail] 返回数据', [
            'record' => $recordArr,
            'appeal' => $appealArr,
            'reply' => $replyArr,
        ]);
        return $this->success('申诉详情获取成功', [
            'record' => $recordArr,
            'appeal' => $appealArr,
            'reply' => $replyArr,
        ]);
    }

    /**
     * 回复申诉
     * @ApiInfo(value="回复申诉",method="POST",login=true)
     * @Param(name="id", type="integer", require=true, desc="申诉ID")
     * @Param(name="reply", type="string", require=true, desc="回复内容")
     * @Param(name="status", type="integer", require=true, desc="审核状态")
     * @param int $id 申诉ID    
     */
    public function reply($id = null)
    {
        // 1. 校验登录
        $userId = Auth::getUserIdFromToken();
        if (!$userId) {
            return $this->error('请先登录');
        }
        // 2. 校验参数
        if (!$id) {
            return $this->error('申诉ID不能为空');
        }
        // 3. 校验权限（只有操作员可以回复）
        $user = User::find($userId);
        if (!$user) {
            return $this->error('用户不存在');
        }
        if ($user->user_group_id != 3) {
            return $this->error('无权回复申诉，只有操作员可以回复');
        }

        // 4. 查询申诉记录
        $appeal = ScoreAppeal::find($id);
        if (!$appeal) {
            return $this->error('申诉记录不存在');
        }

        // 5. 获取请求参数
        $params = $this->request->post();
     
        $reply = isset($params['reply']) ? trim($params['reply']) : ''; 
        if (!$reply) {
            return $this->error('回复内容不能为空');
        }
        // 6. 更新申诉记录
        $appeal->reply = $reply;
        $appeal->reply_user_id = $userId;

        // 7. 更新申诉状态
        $appeal->status = $params['status']; // 1:已处理
        $appeal->update_time = time();  
        $appeal->save();

        // 8. 返回结果
        return $this->success('申诉回复成功', [
            'appeal_id' => $appeal->id,
            'reply' => $reply,
            'update_time' => $appeal->update_time
        ]);
    }

    /**
     * 取消申诉
     * @ApiInfo(value="取消申诉",method="POST",login=true)
     * @Param(name="id", type="integer", require=true, desc="申诉ID")
     */
    public function cancel($id = null)
    {
        // 1. 校验登录
        $userId = Auth::getUserIdFromToken();
        if (!$userId) {
            return $this->error('请先登录');
        }
        // 2. 校验参数
        if (!$id) {
            return $this->error('申诉ID不能为空');
        }
        // 3. 查询申诉记录
        $appeal = ScoreAppeal::find($id);
        if (!$appeal) {
            return $this->error('申诉记录不存在');
        }

        // 4. 校验权限
        $user = User::find($userId);
        if (!$user) {
            return $this->error('用户不存在');
        }
        if ($user->user_group_id != 3 && $appeal->user_id != $userId) {
            return $this->error('无权取消该申诉');
        }
        // 5. 取消申诉
        $appeal->status = 2; // 2:已取消
        $appeal->save();
        return $this->success('申诉取消成功');
    }
}
