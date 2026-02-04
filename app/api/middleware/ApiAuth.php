<?php
declare (strict_types = 1);

namespace app\api\middleware;

use app\api\library\Auth;
use think\Response;

class ApiAuth
{
    public function handle($request, \Closure $next)
    {
        try {
            // 🔍 只对 api 应用的请求进行验证，其他应用直接放行
            $pathinfo = request()->pathinfo();
            if (!preg_match('#^/?api(/|$)#i', $pathinfo)) {
                return $next($request);
            }
            
            // 获取当前控制器和方法
            $controller = request()->controller();
            $action = request()->action();
            
            // 检查是否需要登录验证
            $noNeedLogin = $this->getNoNeedLogin();
            $currentPath = strtolower("{$controller}/{$action}");
            
            // 先检查白名单，在白名单中直接放行
            if(in_array($currentPath, $noNeedLogin)) {
                return $next($request);
            }
            
            // 不在白名单，验证Token（force=false不抛异常）
            $payload = Auth::verifyToken(null, false);
            if(!$payload) {
                return json(['code' => 401, 'msg' => '请先登录']);
            }
            
            // 验证用户状态  
            $user = Auth::getUser();
            if(!$user || $user['status'] !== 'verified') {
                return json(['code' => 403, 'msg' => '账号已被禁用']);
            }
            
            return $next($request);
            
        } catch(\Exception $e) {
            return json(['code' => 500, 'msg' => '服务器错误: ' . $e->getMessage()]);
        }
    }

    /**
     * 获取无需登录的接口
     */
    protected function getNoNeedLogin(): array
    {
        return [
            'index/index',      // 首页（公开访问）
            'auth/login',       // 微信登录
            'auth/check',       // 检查登录状态
            'usergrade/index',  // 用户等级列表（公开）
            'rules/index',      // 积分规则
            'rules/categories'  // 规则分类 
        ];
    }
}
