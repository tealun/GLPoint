<?php
/**
 * 微信配置示例文件
 * 
 * 此文件为配置模板，实际配置从 .env 文件读取
 * 请在 .env 中配置以下变量：
 * - WECHAT_MINI_APP_ID
 * - WECHAT_MINI_APP_SECRET
 * - WECHAT_MINI_APP_NAME
 * 
 * 实际配置请使用 /config/wechat.php
 */
return [
    'mini' => [
        'appid' => env('WECHAT_MINI_APP_ID', ''),
        'secret' => env('WECHAT_MINI_APP_SECRET', ''),
    ],
];
