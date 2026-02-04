<?php
declare(strict_types=1);

namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\Auth;
use app\common\model\Feedback as FeedbackModel;
use app\common\model\FeedbackReply;
use think\facade\Log;

/**
 * 反馈管理控制器
 * @Controller("反馈管理",module="系统",desc="用户反馈提交、查看、回复")
 */
class Feedback extends Api
{
    /**
     * 获取反馈列表
     * @ApiInfo(value="反馈列表",method="GET",login=true)
     * @Param(name="status", type="integer", require=false, desc="状态：0待处理/1处理中/2已解决/3已关闭")
     * @Returns(name="list", type="array", desc="反馈列表")
     */
    public function index()
    {
        try {
            // 校验登录
            $userId = Auth::getUserIdFromToken();
            if (!$userId) {
                return $this->error('请先登录');
            }

            // 获取用户的反馈列表
            $page = $this->request->param('page/d', 1);
            $limit = $this->request->param('limit/d', 10);

            $list = FeedbackModel::where('user_id', $userId)
                ->where('delete_time', 0)
                ->order('create_time DESC')
                ->page($page, $limit)
                ->select();

            return $this->success('获取成功', $list);

        } catch (\Exception $e) {
            Log::error('获取反馈列表失败: ' . $e->getMessage());
            return $this->error('获取反馈列表失败');
        }
    }

    /**
     * 提交反馈
     * @ApiInfo(value="提交反馈",method="POST",login=true)
     * @Param(name="content", type="string", require=true, desc="反馈内容")
     */
    public function submit()
    {
        try {
            // 校验登录
            $userId = Auth::getUserIdFromToken();
            if (!$userId) {
                return $this->error('请先登录');
            }

            // 获取参数
            $content = $this->request->post('content');
            if (empty($content)) {
                return $this->error('反馈内容不能为空');
            }

            // 创建反馈
            $feedback = FeedbackModel::create([
                'user_id' => $userId,
                'content' => htmlspecialchars(trim($content), ENT_QUOTES, 'UTF-8'),
                'status' => 'pending', // 待处理
                'is_closed' => 0,
                'create_time' => time(),
            ]);

            return $this->success('提交成功', ['id' => $feedback->id]);

        } catch (\Exception $e) {
            Log::error('提交反馈失败: ' . $e->getMessage());
            return $this->error('提交失败');
        }
    }

    /**
     * 反馈详情
     * @ApiInfo(value="反馈详情",method="GET",login=true)
     * @Param(name="id", type="integer", require=true, desc="反馈ID")
     * @Returns(name="feedback", type="object", desc="反馈详情（含回复列表）")
     */
    public function detail($id = null)
    {
        try {
            // 校验登录
            $userId = Auth::getUserIdFromToken();
            if (!$userId) {
                return $this->error('请先登录');
            }

            if (!$id) {
                return $this->error('参数错误');
            }

            // 查询反馈
            $feedback = FeedbackModel::find($id);
            if (!$feedback) {
                return $this->error('反馈不存在');
            }

            // 校验权限
            if ($feedback->user_id != $userId) {
                return $this->error('无权查看该反馈');
            }

            // 获取回复列表
            $replies = FeedbackReply::where('feedback_id', $id)
                ->where('delete_time', 0)
                ->with(['user'])
                ->order('create_time ASC')
                ->select();

            $feedback->replies = $replies;

            return $this->success('获取成功', $feedback);

        } catch (\Exception $e) {
            Log::error('获取反馈详情失败: ' . $e->getMessage());
            return $this->error('获取反馈详情失败');
        }
    }

    /**
     * 删除反馈
     * @ApiInfo(value="删除反馈",method="POST",login=true)
     * @Param(name="id", type="integer", require=true, desc="反馈ID")
     */
    public function delete($id = null)
    {
        try {
            // 校验登录
            $userId = Auth::getUserIdFromToken();
            if (!$userId) {
                return $this->error('请先登录');
            }

            if (!$id) {
                return $this->error('参数错误');
            }

            // 查询反馈
            $feedback = FeedbackModel::find($id);
            if (!$feedback) {
                return $this->error('反馈不存在');
            }

            // 校验权限
            if ($feedback->user_id != $userId) {
                return $this->error('无权删除该反馈');
            }

            // 软删除
            $feedback->delete_time = time();
            $feedback->save();

            return $this->success('删除成功');

        } catch (\Exception $e) {
            Log::error('删除反馈失败: ' . $e->getMessage());
            return $this->error('删除失败');
        }
    }

    /**
     * 回复反馈（管理员）     * @ApiInfo(value="回复反馈",method="POST",login=true)
     * @Param(name="id", type="integer", require=true, desc="反馈ID")
     * @Param(name="content", type="string", require=true, desc="回复内容")     */
    public function reply($id = null)
    {
        try {
            // 校验登录
            $userId = Auth::getUserIdFromToken();
            if (!$userId) {
                return $this->error('请先登录');
            }

            // 校验权限（只有操作员可以回复）
            $user = \app\common\model\User::find($userId);
            if (!$user || $user->user_group_id != 3) {
                return $this->error('无权回复反馈');
            }

            if (!$id) {
                return $this->error('参数错误');
            }

            // 获取回复内容
            $content = $this->request->post('content');
            if (empty($content)) {
                return $this->error('回复内容不能为空');
            }

            // 查询反馈
            $feedback = FeedbackModel::find($id);
            if (!$feedback) {
                return $this->error('反馈不存在');
            }

            // 创建回复
            $reply = FeedbackReply::create([
                'feedback_id' => $id,
                'user_id' => $userId,
                'content' => htmlspecialchars(trim($content), ENT_QUOTES, 'UTF-8'),
                'create_time' => time(),
            ]);

            // 更新反馈状态
            $feedback->process_time = time();
            if ($feedback->status === 'pending') {
                $feedback->status = 'processing'; // 处理中
            }
            $feedback->save();

            return $this->success('回复成功', ['reply_id' => $reply->id]);

        } catch (\Exception $e) {
            Log::error('回复反馈失败: ' . $e->getMessage());
            return $this->error('回复失败');
        }
    }

    /**
     * 设置反馈状态（管理员）
     * @ApiInfo(value="更新反馈状态",method="POST",login=true)
     * @Param(name="id", type="integer", require=true, desc="反馈ID")
     * @Param(name="status", type="string", require=true, desc="状态：pending/processing/resolved/closed")
     */
    public function setStatus($id = null)
    {
        try {
            // 校验登录
            $userId = Auth::getUserIdFromToken();
            if (!$userId) {
                return $this->error('请先登录');
            }

            // 校验权限（只有操作员可以设置状态）
            $user = \app\common\model\User::find($userId);
            if (!$user || $user->user_group_id != 3) {
                return $this->error('无权设置状态');
            }

            if (!$id) {
                return $this->error('参数错误');
            }

            // 获取状态参数
            $status = $this->request->post('status');
            $validStatus = ['pending', 'processing', 'adopted', 'rejected', 'closed'];
            if (!in_array($status, $validStatus)) {
                return $this->error('状态参数错误');
            }

            // 查询反馈
            $feedback = FeedbackModel::find($id);
            if (!$feedback) {
                return $this->error('反馈不存在');
            }

            // 更新状态
            $feedback->status = $status;
            $feedback->process_time = time();
            
            if ($status === 'closed') {
                $feedback->is_closed = 1;
                $feedback->close_time = time();
            }
            
            $feedback->save();

            return $this->success('设置成功');

        } catch (\Exception $e) {
            Log::error('设置反馈状态失败: ' . $e->getMessage());
            return $this->error('设置失败');
        }
    }
}
