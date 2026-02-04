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
        // 验证拒绝访问 -- 由于系统默认写好了很多接口 不用的可以自行关闭
        if ($helper->checkForbidden()) {
            return ajax('forbidden');
        }

        // 验证请求方式
        if (!$helper->checkMethod()) {
            return ajax('badMethod', $helper->getError()['badMethod'] ?? '');
        }

        // 验证登录
        if (!$helper->checkLogin()) {
            return ajax('nologin', $helper->getError()['nologin'] ?? '');
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