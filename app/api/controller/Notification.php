<?php
declare (strict_types = 1);
namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\Auth;
use app\common\model\Notification as NotificationModel;
use think\facade\Log;

/**
 * 通知管理控制器
 * @Controller("通知管理",module="系统",desc="系统通知、消息推送")
 */
class Notification extends Api
{
    /**
     * @ApiInfo("获取所有通知",desc="获取用户所有通知列表",method="GET",login=true)
     */
    public function all()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            $list = NotificationModel::where('user_id', $user['id'])
                ->order('create_time DESC')
                ->paginate();

            return $this->success('获取成功', $list);

        } catch(\Exception $e) {
            Log::error('获取通知列表失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("获取未读通知",desc="获取用户未读通知列表",method="GET",login=true)
     */
    public function unread()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            $list = NotificationModel::where('user_id', $user['id'])
                ->where('is_read', 0)
                ->order('create_time DESC')
                ->paginate();

            return $this->success('获取成功', $list);

        } catch(\Exception $e) {
            Log::error('获取未读通知失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("获取重要通知",desc="获取用户重要通知列表",method="GET",login=true)
     */
    public function important() 
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            $list = NotificationModel::where('user_id', $user['id'])
                ->where('is_important', 1)  
                ->order('create_time DESC')
                ->paginate();

            return $this->success('获取成功', $list);

        } catch(\Exception $e) {
            Log::error('获取重要通知失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("标记已读",desc="将通知标记为已读",method="POST",login=true)
     * @Param("ids",require=true,type="array",desc="通知ID数组") 
     */
    public function read()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            $ids = $this->request->post('ids/a');
            if(!$ids) {
                return $this->error('参数错误');
            }

            model('Notification')
                ->where('user_id', $user['id'])
                ->whereIn('id', $ids)
                ->update([
                    'is_read' => 1,
                    'read_time' => time()
                ]);

            return $this->success('标记成功');

        } catch(\Exception $e) {
            Log::error('标记通知已读失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("获取通知数量",desc="获取未读通知数量",method="GET",login=true)
     */
    public function unreadCount()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            $count = NotificationModel::where('user_id', $user['id'])
                ->where('is_read', 0)
                ->count();

            return $this->success('获取成功', [
                'count' => $count
            ]);

        } catch(\Exception $e) {
            Log::error('获取未读通知数量失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("通知详情",desc="获取通知详细信息",method="GET",login=true)
     * @Param("id",require=true,type="integer",desc="通知ID")
     */
    public function detail($id = null)
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            if (!$id) {
                return $this->error('参数错误');
            }

            $notification = NotificationModel::where('id', $id)
                ->where('user_id', $user['id'])
                ->find();

            if (!$notification) {
                return $this->error('通知不存在');
            }

            // 自动标记为已读
            if ($notification->is_read == 0) {
                $notification->is_read = 1;
                $notification->read_time = time();
                $notification->save();
            }

            return $this->success('获取成功', $notification);

        } catch(\Exception $e) {
            Log::error('获取通知详情失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("全部已读",desc="将所有通知标记为已读",method="POST",login=true)
     */
    public function markAllRead()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            NotificationModel::where('user_id', $user['id'])
                ->where('is_read', 0)
                ->update([
                    'is_read' => 1,
                    'read_time' => time()
                ]);

            return $this->success('标记成功');

        } catch(\Exception $e) {
            Log::error('全部已读操作失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("清空通知",desc="清空所有通知",method="POST",login=true)
     */
    public function clear()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            NotificationModel::where('user_id', $user['id'])
                ->delete();

            return $this->success('清空成功');

        } catch(\Exception $e) {
            Log::error('清空通知失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("删除通知",desc="删除指定通知",method="POST",login=true)
     * @Param("ids",require=true,type="array",desc="通知ID数组")
     */
    public function delete()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            $ids = $this->request->post('ids/a');
            if(!$ids) {
                return $this->error('参数错误');
            }

            NotificationModel::where('user_id', $user['id'])
                ->whereIn('id', $ids)
                ->delete();

            return $this->success('删除成功');

        } catch(\Exception $e) {
            Log::error('删除通知失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }
}
