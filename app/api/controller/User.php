<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\Auth;
use app\common\model\User as UserModel;
use app\common\model\WechatUser as WechatUser;
use app\api\model\UserScore;
use think\facade\Log;

/**
 * 用户管理控制器
 * @Controller("用户管理",module="用户",desc="用户信息、修改、头像")
 */
class User extends Api
{
    /**
     * 获取用户信息
     * @ApiInfo(value="获取用户信息",method="GET",login=true)
     * @Returns(name="user", type="object", desc="用户信息")
     */
    public function index()
    {
        try {

            $login = Auth::checkLogin();
            if (!$login) {
                return $this->error('未登录或登录已过期');
            }

            $uid = Auth::getUserIdFromToken();
            // 获取当前登录用户信息
            $userInfo = self::getUser($uid);

            if (!$userInfo) {
                return $this->error('用户不存在');
            }

            return $this->success('获取成功', $userInfo);

        } catch(\Exception $e) {
            return $this->error($e->getMessage()); 
        }
    }
    
    /**
     * 获取当前用户
     */
    public static function getUser($uid): ?array
    {
        try {

            $user = UserModel::where('id', $uid)
                ->where('status', 'verified')
                ->field([
                    'username',
                    'nickname',
                    'mobile',
                    'user_group_id',
                    'avatar',
                    'department_id',
                    'email',
                    'status',
                    'score'
                ])
                ->find();

            if (!$user) {
                return null;
            }
            
            $wechatUser = WechatUser::where('user_id', $uid)
                ->where('delete_time', 0)
                ->field([
                    'id',
                    'openid',
                    'gender'
                ])
                ->find();
            if (!$wechatUser) {
                return null;
            }
            
            // 合并用户信息
            $user->id = $wechatUser->id; // WechatUser的ID
            $user->user_id = $uid; // 用户userID
            $user->openid = $wechatUser->openid; // 微信用户的openid
            $user->gender = $wechatUser->gender; // 微信用户的性别

            $UserScore = new UserScore();
            // 获取累计积分
            $totalScore = $UserScore->getUserTotalScore($uid);

            // 获取本月积分
            $monthScore = $UserScore->getUserMonthScore($uid);

            // 获取本周积分
            $weekScore = $UserScore->getUserWeekScore($uid);

            // 获取用户等级
            $userGrade = model('UserGrade')->where('min', '<=', $totalScore)
                ->order('min', 'desc')
                ->find();
            $user->level = $userGrade ? $userGrade->title : '';
            
            // 添加积分信息到用户对象
            $user->total_score = $totalScore;
            $user->month_score = $monthScore;
            $user->week_score = $weekScore;

            return $user->toArray();

        } catch(\Exception $e) {
            return null;
        }
    }

    /**
     * 更新用户信息
     * @ApiInfo(value="更新用户信息",method="POST",login=true)
     * @Param(name="user_id", type="integer", require=true, desc="用户ID")
     * @Param(name="name", type="string", require=false, desc="昵称")
     * @Param(name="gender", type="integer", require=false, desc="性别")
     * @Param(name="department_id", type="integer", require=false, desc="部门ID")
     */
    public function update()
    {
        $login = Auth::checkLogin();
        if (!$login) {
            return $this->error('未登录或登录已过期');
        }
        try {
            $data = $this->request->post();

            $userId = $data['user_id'] ?? 0;
            if (!$userId) {
                return $this->error('用户ID不能为空');
            }

            // 更新 User 表
            $user = UserModel::find($userId);
            if (!$user) {
                return $this->error('用户不存在');
            }

            // 只允许更新的字段
            $updateData = [
                'nickname'       => $data['name'] ?? $user->nickname,
                'sex'            => $data['gender'] ?? 1, // 默认性别为1
                'department_id'  => $data['department_id'] ?? $user->department_id
            ];

            // 更新 User 表
            $userRes = $user->modifyData($updateData);

            if (method_exists($user, 'getError') && $user->getError()) {
                return $this->error('更新用户信息失败: ' . json_encode($user->getError()));
            }

            // 更新 WechatUser 表
            $wechatUser = WechatUser::where('user_id', $userId)->find();
            if (!$wechatUser) {
                return $this->error('微信用户不存在');
            }

            $updateData = [
                'nickname' => $data['name'] ?? $wechatUser->nickname,
                'gender' => $data['gender'] ?? 1, // 默认性别为1
            ];

            $wechatRes = $wechatUser->modifyData($updateData);

            if ($userRes !== false && $wechatRes !== false) {
                return $this->success('更新成功');
            } else {
                return $this->error('更新用户信息失败');
            }
        } catch (\Exception $e) {
            return $this->error('更新失败: ' . $e->getMessage());
        }
    }

    
}