<?php

return [
    'mini_program' => [
        // 小程序 appId（从 .env 读取）
        'app_id' => env('WECHAT_MINI_APP_ID', ''),
        
        // 小程序 appSecret（从 .env 读取）
        'app_secret' => env('WECHAT_MINI_APP_SECRET', ''),
        
        // 小程序名称
        'name' => env('WECHAT_MINI_APP_NAME', 'GLpoint积分系统'),
    ]
];
