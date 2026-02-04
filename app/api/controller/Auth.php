<?php
declare (strict_types = 1);
namespace app\api\controller;

use app\common\controller\Api;
use app\api\service\Wechat;
use app\api\model\UserScore;
use app\api\library\Auth as AuthLibrary;
use think\facade\{Log, Config};
use app\common\model\{User, WechatUser};

/**
 * 认证控制器
 * @Controller("用户认证",module="认证",desc="登录、退出、密码管理")
 */
class Auth extends Api
{
    protected $noNeedLogin = ['login']; 
    
    /**
     * 统一登录入口
     * @ApiInfo(value="微信登录",method="POST",login=false)
     * @Param(name="code", type="string", require=true, desc="微信登录code")
     * @Returns(name="token", type="string", desc="登录凭证")
     * @Returns(name="user", type="object", desc="用户信息")
     * @param string $code 微信登录时的code参数
     * @return json
     */
    public function login()
    {
        try {
            // 1. 获取登录参数
            $code = $this->request->post('code');
            
            // 2. 微信登录
            if($code) {
                $result = Wechat::getOpenidByCode($code);
                Log::debug('微信登录结果: ' . json_encode($result));
                if(!isset($result['openid'])) {
                    throw new \Exception('获取openid失败: ' . ($result['errmsg'] ?? '未知错误'));
                }
                // 3. 检查openid是否存在
                $wechatUser = self::getOrCreateWechatUser($result);
                Log::debug('找到或创建的微信用户: ' . json_encode($wechatUser));
                if(!$wechatUser) {
                    throw new \Exception('微信用户不存在或创建失败');
                }
                // 4. 生成token
                $result = self::generateTokenForWechatUser($wechatUser);
                Log::debug('生成的Token: ' . json_encode($result['token']));
                Log::debug('用户信息: ' . json_encode($result['user']));
                // 5. 返回登录结果
                return $this->success('登录成功', $result);
            }

            // 6. 非微信登录一律拒绝
            return $this->error('仅支持微信登录');

        } catch(\Exception $e) {
            Log::error('登录失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

        /**
     * 检查openid是否存在，不存在就创建
     */
    protected static function getOrCreateWechatUser(array $result)
    {
        $wechatUser = model('WechatUser')
                ->where([
                    ['openid', '=', $result['openid']]
                ])
                ->find();
        Log::debug('查询微信用户: openid=' . $result['openid']);
        Log::debug('查询结果: ' . ($wechatUser ? '找到用户' : '未找到用户'));
        Log::debug('开始处理微信用户信息');
        $User = new User();

        // 如果未找到微信用户，则创建新用户
        if(!$wechatUser) {
            Log::debug('未找到微信用户，开始创建新用户');
            $randPassword = substr(md5(uniqid() ?: ''), 0, 16);
            $salt = 'YUANyin';
            $baseUsername = 'wx_' . substr($result['openid'] ?? '', -8);
            $username = $baseUsername;
            $i = 1;
            while(User::where('username', $username)->find()) {
                $username = $baseUsername . '_' . $i;
                $i++;
            }
            $userData = [
                'username' => $username,
                'nickname' => $username,
                'status' => 'verified',
                'password' => md5($randPassword . $salt),
                'avatar' => Config::get('app.default_avatar', '/static/images/default_avatar.png'),
                'salt' => $salt,
                'register_type' => 'wxmini',
                'user_group_id' => 1,
                'user_grade_id' => 1,
                'register_ip' => request()->ip(),
                'login_time' => time(),
                'create_time' => time(),
                'update_time' => time()
            ];
            Log::debug('创建新用户数据: ' . json_encode($userData));
            
            $userId = $User->insertGetId($userData);
            if(!$userId) {
                throw new \Exception('用户创建失败:' . $User->getError());
            }
            Log::debug('用户创建成功,ID=' . json_encode($userId));
            $wechatData = [
                'user_id' => $userId,
                'openid' => $result['openid'],
                'unionid' => $result['unionid'] ?? '',
                'gender' => 0, 
                'is_active' => 1,
                'create_time' => time(),
                'update_time' => time()
            ];
            $WechatUser = new WechatUser();
            $wechatUserId = $WechatUser->insertGetId($wechatData);

            if(!$wechatUserId) {
                throw new \Exception('微信用户创建失败' . $WechatUser->getError());
            }
            Log::debug('微信用户创建成功,ID=' . json_encode($wechatUserId));
            
            // 重新查询刚创建的微信用户信息
            $wechatUser = model('WechatUser')->where('id', $wechatUserId)->find();
        }
        // 获取用户的详细信息
        $user = $User->where('id', $wechatUser->user_id)
            ->where('status', 'verified')
            ->field([
            'avatar',
            'user_group_id',
            'department_id',
            'user_grade_id'
            ])
            ->find();
        Log::debug('查询用户信息: ' . json_encode($user));


        // 整合用户数据到微信用户对象
        if ($user) {
            $wechatUser->avatar = $user->avatar;
            $wechatUser->user_group_id = $user->user_group_id;
            $wechatUser->department_id = $user->department_id;
            $wechatUser->level = model('UserGrade')->where('id', $user->user_grade_id)->value('title');
        }

        $UserScore = new UserScore();
        // 获取累计积分
        $totalScore = $UserScore->getUserTotalScore($wechatUser->user_id);

        // 获取本月积分
        $monthScore = $UserScore->getUserMonthScore($wechatUser->user_id);

        // 获取本周积分
        $weekScore = $UserScore->getUserWeekScore($wechatUser->user_id);

        // 添加积分信息到微信用户对象
        $wechatUser->total_score = $totalScore;
        $wechatUser->month_score = $monthScore;
        $wechatUser->week_score = $weekScore;

        return $wechatUser;
    }

    /**
     * 生成并返回token
     */
    protected static function generateTokenForWechatUser($wechatUser): array
    {
        $token = AuthLibrary::createToken([
            'uid' => $wechatUser->user_id,
            'type' => 'wechat'
        ]);
        return [
            'token' => $token,
            'user' => $wechatUser->toArray()
        ];
    }

    /**
     * 检查登录状态
     * @ApiInfo(value="检查登录状态",method="GET",login=false)
     * @return \think\Response
     */
    public function check()
    {
        if (AuthLibrary::checkLogin()) {
            return $this->success('已登录');
        } else {
            return $this->error('未登录或登录已过期');
        }
    }

    /**
     * 退出登录
     * @ApiInfo(value="退出登录",method="POST",login=true)
     * @return \think\Response
     */
    public function logout()
    {
        try {
            $result = AuthLibrary::logout();
            if ($result) {
                return $this->success('退出成功');
            }
            return $this->error('退出失败');
        } catch (\Exception $e) {
            Log::error('退出登录失败: ' . $e->getMessage());
            return $this->error('退出失败');
        }
    }

    /**
     * 修改密码
     * @ApiInfo(value="修改密码",method="POST",login=true)
     * @Param(name="old_password", type="string", require=true, desc="旧密码")
     * @Param(name="new_password", type="string", require=true, desc="新密码")
     * @Param(name="confirm_password", type="string", require=true, desc="确认密码")
     * @return \think\Response
     */
    public function changePassword()
    {
        try {
            // 检查登录
            if (!AuthLibrary::checkLogin()) {
                return $this->error('请先登录');
            }

            $userId = AuthLibrary::getUserIdFromToken();
            if (!$userId) {
                return $this->error('用户信息获取失败');
            }

            // 获取参数
            $oldPassword = $this->request->post('old_password');
            $newPassword = $this->request->post('new_password');
            $confirmPassword = $this->request->post('confirm_password');

            if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
                return $this->error('参数不完整');
            }

            if ($newPassword !== $confirmPassword) {
                return $this->error('两次密码输入不一致');
            }

            if (strlen($newPassword) < 6) {
                return $this->error('密码长度不能少于6位');
            }

            // 查询用户
            $user = User::find($userId);
            if (!$user) {
                return $this->error('用户不存在');
            }

            // 验证旧密码
            $salt = $user->salt ?? 'YUANyin';
            if ($user->password !== md5($oldPassword . $salt)) {
                return $this->error('旧密码错误');
            }

            // 更新密码
            $user->password = md5($newPassword . $salt);
            $user->update_time = time();
            $user->save();

            return $this->success('修改成功，请重新登录');

        } catch (\Exception $e) {
            Log::error('修改密码失败: ' . $e->getMessage());
            return $this->error('修改失败');
        }
    }

    /**
     * 重置密码（仅管理员可用）
     * @ApiInfo(value="重置密码",method="POST",login=true)
     * @Param(name="user_id", type="integer", require=true, desc="目标用户ID")
     * @Param(name="new_password", type="string", require=true, desc="新密码")
     * @return \think\Response
     */
    public function resetPassword()
    {
        try {
            // 检查登录
            if (!AuthLibrary::checkLogin()) {
                return $this->error('请先登录');
            }

            $currentUserId = AuthLibrary::getUserIdFromToken();
            $currentUser = User::find($currentUserId);
            
            // 只有操作员可以重置其他用户密码
            if (!$currentUser || $currentUser->user_group_id != 3) {
                return $this->error('无权重置密码');
            }

            // 获取参数
            $userId = $this->request->post('user_id/d');
            $newPassword = $this->request->post('new_password');

            if (!$userId || empty($newPassword)) {
                return $this->error('参数错误');
            }

            if (strlen($newPassword) < 6) {
                return $this->error('密码长度不能少于6位');
            }

            // 查询目标用户
            $user = User::find($userId);
            if (!$user) {
                return $this->error('用户不存在');
            }

            // 重置密码
            $salt = $user->salt ?? 'YUANyin';
            $user->password = md5($newPassword . $salt);
            $user->update_time = time();
            $user->save();

            return $this->success('重置成功');

        } catch (\Exception $e) {
            Log::error('重置密码失败: ' . $e->getMessage());
            return $this->error('重置失败');
        }
    }
}
