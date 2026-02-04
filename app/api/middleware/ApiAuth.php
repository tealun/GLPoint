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
            // 获取当前控制器和方法
            $controller = request()->controller();
            $action = request()->action();
            
            // 🔍 如果controller为空，尝试从pathinfo解析
            if (empty($controller)) {
                $pathinfo = request()->pathinfo();
                // pathinfo格式可能是: api/index/index 或 index/index
                $parts = array_filter(explode('/', $pathinfo));
                $parts = array_values($parts);
                
                // 如果第一部分是'api'，跳过它
                if (!empty($parts) && strtolower($parts[0]) === 'api') {
                    array_shift($parts);
                    $parts = array_values($parts);
                }
                
                // 解析controller和action
                if (isset($parts[0])) {
                    $controller = $parts[0];
                }
                if (isset($parts[1])) {
                    $action = $parts[1];
                }
            }
            
            // 检查是否需要登录验证
            $noNeedLogin = $this->getNoNeedLogin();
            $currentPath = strtolower("{$controller}/{$action}");
            
            // 🔍 增强调试信息：添加路由和URL信息
            $debugInfo = [
                'controller' => $controller,
                'action' => $action,
                'currentPath' => $currentPath,
                'request_url' => request()->url(true),
                'request_path' => request()->pathinfo(),
                'request_method' => request()->method(),
                'app_name' => app('http')->getName(),
                'route_info' => request()->rule() ? request()->rule()->getRule() : 'no_rule',
                'noNeedLogin' => $noNeedLogin,
                'inWhitelist' => in_array($currentPath, $noNeedLogin),
            ];
            
            // 先检查白名单，在白名单中的直接放行
            if(in_array($currentPath, $noNeedLogin)) {
                // 🔍 白名单放行
                return $next($request);
            }
            
            // 不在白名单中，需要验证Token（传入force=false避免抛异常）
            $payload = Auth::verifyToken(null, false);
            if(!$payload) {
                return json([
                    'code' => 401, 
                    'msg' => '请先登录',
                    'debug' => $debugInfo
                ]);
            }
            
            // 验证用户状态  
            $user = Auth::getUser();
            if(!$user || $user['status'] !== 'verified') {
                return json(['code' => 403, 'msg' => '账号已被禁用']);
            }
            
            return $next($request);
            
        } catch(\Exception $e) {
            // 🔍 在异常时也输出调试信息
            $debugInfo = [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'controller' => $controller ?? 'unknown',
                'action' => $action ?? 'unknown',
                'currentPath' => isset($controller, $action) ? strtolower("{$controller}/{$action}") : 'unknown',
                'trace' => $e->getTraceAsString(),
            ];
            return json([
                'code' => 401, 
                'msg' => '🔍异常调试: ' . json_encode($debugInfo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ]);
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
