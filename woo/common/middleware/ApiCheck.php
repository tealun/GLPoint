<?php
declare (strict_types=1);

namespace woo\common\middleware;

use think\facade\Config;
use woo\common\helper\ApiHelper;

class ApiCheck
{
    public function handle($request, \Closure $next)
    {
        $helper = new ApiHelper();
        
        // 🔍 调试信息：获取注解信息
        $info = $helper->getActionApiInfo();
        $debugInfo = [
            'controller' => $request->controller(),
            'action' => $request->action(),
            'url' => $request->url(true),
            'api_info' => $info,
            'login_isset' => isset($info['login']),
            'login_value' => $info['login'] ?? 'NOT_SET',
            'login_type' => isset($info['login']) ? gettype($info['login']) : 'NOT_SET',
            'login_empty' => isset($info['login']) ? empty($info['login']) : 'NOT_SET',
        ];
        
        // 验证拒绝访问 -- 由于系统默认写好了很多接口 不用的可以自行关闭
        if ($helper->checkForbidden()) {
            return ajax('forbidden', '已禁用', $debugInfo);
        }

        // 验证请求方式
        if (!$helper->checkMethod()) {
            return ajax('badMethod', $helper->getError()['badMethod'] ?? '', $debugInfo);
        }

        // 验证登录
        if (!$helper->checkLogin()) {
            // 在登录失败时返回详细的调试信息
            return ajax('nologin', '🔍调试: ' . json_encode($debugInfo, JSON_UNESCAPED_UNICODE), $debugInfo);
        }

        // 验证权限
        if (!$helper->checkPower()) {
            return ajax('nopower', $helper->getError()['nopower'] ?? '');
        }

        // 验证参数规则
        if (!$helper->checkParam() && Config::get('api.is_check_param', true)) {
            return ajax('badParam', '', $helper->getError());
        }

        return $next($request);
    }
}