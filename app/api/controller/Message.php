<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\Auth;
use app\common\model\SubscribeAuth;
use app\common\model\Notification;
use think\facade\Log;

/**
 * @Controller("订阅消息管理",module="消息",desc="订阅消息相关接口")
 */
class Message extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    /**
     * @ApiInfo(value="保存订阅消息状态",method="POST",login=true)
     * @Param(name="template_id", type="string", require=true, desc="模板ID")
     * @Param(name="status", type="string", require=true, desc="订阅状态")
     * @Param(name="openid", type="string", require=false, desc="用户openid")
     * @Returns(name="data", type="object", desc="返回数据")
     */
    public function subscribe()
    {
        try {
            // 验证登录
            if (!Auth::checkLogin()) {
                return $this->error('请先登录');
            }

            // 获取用户ID
            $user_id = Auth::getUserIdFromToken();

            // 获取请求参数
            $template_id = $this->request->post('template_id', '');
            $template_name = $this->request->post('template_name', '');
            $status = $this->request->post('status', '');

            // 验证参数
            if (empty($template_id)) {
                return $this->error('模板ID不能为空');
            }

            if (empty($status)) {
                return $this->error('订阅状态不能为空');
            }

            // 检查是否已存在该用户的订阅记录
            $existRecord = SubscribeAuth::where('user_id', $user_id)
                ->where('template_id', $template_id)
                ->find();

            $data = [
                'user_id' => $user_id,
                'template_id' => $template_id,
                'template_name' => $template_name,
                'status' => $status,
                'update_time' => time()
            ];

            if ($existRecord) {
                // 更新现有记录
                $existRecord->save($data);
                Log::info('更新订阅授权状态', $data);
            } else {
                // 创建新记录
                $data['create_time'] = time();
                SubscribeAuth::create($data);
                Log::info('创建订阅授权记录', $data);
            }

            return $this->success('订阅状态保存成功');

        } catch (\Exception $e) {
            Log::error('保存订阅状态失败: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->error('保存订阅状态失败');
        }
    }

    /**
     * @ApiInfo(value="获取用户订阅状态",method="GET",login=true)
     * @Returns(name="data", type="array", desc="订阅状态列表")
     */
    public function getStatus()
    {
        try {
            // 验证登录
            if (!Auth::checkLogin()) {
                return $this->error('请先登录');
            }

            // 获取用户ID
            $user_id = Auth::getUserIdFromToken();

            // 查询用户的订阅授权记录
            $records = SubscribeAuth::where('user_id', $user_id)
                ->where('status', 'accept')
                ->select();

            $hasSubscribed = count($records) > 0;

            return $this->success('获取成功', [
                'status' => $hasSubscribed ? 'enabled' : 'disabled',
                'records' => $records
            ]);

        } catch (\Exception $e) {
            Log::error('获取订阅状态失败: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->error('获取订阅状态失败');
        }
    }

    /**
     * @ApiInfo(value="发送订阅消息",method="POST",login=true)
     * @Param(name="template_id", type="string", require=true, desc="模板ID")
     * @Param(name="page", type="string", require=false, desc="跳转页面")
     * @Param(name="data", type="object", require=true, desc="消息数据")
     * @Returns(name="data", type="object", desc="返回数据")
     */
    public function send()
    {
        try {
            // 验证登录
            if (!Auth::checkLogin()) {
                return $this->error('请先登录');
            }

            // 获取请求参数
            $template_id = $this->request->post('template_id', '');
            $page = $this->request->post('page', '');
            $data = $this->request->post('data', []);

            // 验证参数
            if (empty($template_id)) {
                return $this->error('模板ID不能为空');
            }

            if (empty($data)) {
                return $this->error('消息数据不能为空');
            }

            // 获取用户ID
            $user_id = Auth::getUserIdFromToken();
            $target_user_id = $this->request->post('target_user_id', $user_id);

            // 检查目标用户是否已授权该模板
            $auth = SubscribeAuth::where('user_id', $target_user_id)
                ->where('template_id', $template_id)
                ->where('status', 'accept')
                ->find();

            if (!$auth) {
                return $this->error('用户未授权该订阅消息');
            }

            // TODO: 这里需要集成微信订阅消息API
            // 使用 EasyWeChat 发送订阅消息到微信服务器
            // $wechatService = new WechatService();
            // $result = $wechatService->sendSubscribeMessage($openid, $template_id, $data, $page);

            // 将通知记录保存到 woo_notification 表
            $notification_data = [
                'user_id' => $target_user_id,
                'type' => 'subscribe',
                'template_id' => $template_id,
                'title' => $auth->template_name ?? '订阅消息',
                'message' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'create_time' => time()
            ];
            
            Notification::create($notification_data);

            Log::info('发送订阅消息', [
                'target_user_id' => $target_user_id,
                'template_id' => $template_id,
                'page' => $page,
                'data' => $data
            ]);

            return $this->success('发送成功');

        } catch (\Exception $e) {
            Log::error('发送订阅消息失败: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->error('发送订阅消息失败');
        }
    }
}
