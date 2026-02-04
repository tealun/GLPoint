<?php
declare (strict_types = 1);

namespace app\api\service;

use think\facade\Log;
use think\facade\Config;
class Wechat
{

    /**
     * 通过code获取openid
     */
    public static function getOpenidByCode(string $code): array
    {
        // 从配置中获取appid和secret
        $config = Config::get('wechat.mini_program');
        $appid = $config['app_id'] ?? '';
        $secret = $config['app_secret'] ?? '';

        Log::debug('微信登录配置: appid=' . $appid . 
                  ', secret=' . substr($secret ?? '', 0, 6) . '***' .
                  ', code=' . $code);

        if(empty($appid) || empty($secret)) {
            throw new \Exception('微信小程序配置错误');
        }

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
        Log::debug('微信登录请求URL: ' . $url);
        $response = file_get_contents($url);
        Log::debug('微信登录响应: ' . $response);
        $result = json_decode($response, true);

        if(!isset($result['openid'])) {
            throw new \Exception('获取openid失败:' . ($result['errmsg'] ?? '未知错误'));
        }
        return $result;
    }

}
