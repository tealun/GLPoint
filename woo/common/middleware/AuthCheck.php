<?php
declare (strict_types=1);

namespace woo\common\middleware;

use think\exception\HttpException;
use think\facade\Config;

class AuthCheck
{
    public function handle($request, \Closure $next)
    {
        if (!is_woo_installed()) {
            return redirect($request->root() . '?s=install');
        }
        // 应用被禁用
        if (app('http')->getName() && array_key_exists(app('http')->getName(), get_app())) {
            $app = get_app(app('http')->getName());
            if (empty($app['is_verify'])) {
                return abort(403, '访问被拒绝');
            }
        }

        if ($request->action() == 'thumb') {
            return $next($request);
        }
        if (!class_exists(Config::get('wooauth.handler'))) {
            throw new HttpException(403, __('Auth class does not exist ,access denied'));
        }
        $authHandler = app(Config::get('wooauth.handler'), [], true);
        $result = $authHandler->forbidCheck();
        if (true === $result) {
            return $authHandler->forbidResponse() ?? abort(403, '访问被拒绝');
        }
        $result = $authHandler->loginRequired();
        if (true === $result) {
            $result = $authHandler->checkPower();
            if (true === $result) {
                $authHandler->writeLog();
                return $next($request);
            }
        }
        return $result;
    }
}