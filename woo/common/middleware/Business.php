<?php
declare (strict_types=1);

namespace woo\common\middleware;

use think\facade\Config;

class Business
{
    public function handle($request, \Closure $next)
    {
        if (is_woo_installed() && get_app('business')) {
            //------- 检查商家ID 域名 登录者 请求头 等等 -------//
            // 独立域名检查
            if ($domains = read_file_cache('business_domain_bind')) {
                $host = $request->host();
                if (!empty($domains[$host])) {
                    $request->business_id = (int) $domains[$host];
                }
            }

            // APPID 可以考虑通过请求头的形式获取APPID 进行对应商家 暂不做

            // 登录者
            $authClass =  Config::get('wooauth.handler');
            $authClass = new $authClass();
            if ($business_id = $authClass->user('business_id')) {
                $request->business_id = (int) $business_id;
                // 时时拦截 判断
                if (Config::get('wooauth.business_check_always', true) && app('http')->getName() === 'business' && true !== $authClass->intercept() && false === stripos($request->pathInfo(), '/intercept')) {
                    return redirect((string) url('index/intercept'));
                }
            }  else {
                $authClass->logout();
            }
        }
        return $next($request);
    }
}