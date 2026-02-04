<?php
// 事件定义文件
return [
    'bind'      => [
    ],
    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        // 上传事件
        'Upload'   => [
            \woo\common\event\Upload::class
        ],
        // Admin登录事件，如需自定义就改成自己的命名空间
        'AdminLogin'   => [
            \woo\common\event\AdminLogin::class
        ],
        // User登录事件，如需自定义就改成自己的命名空间
        'UserLogin'   => [
            \woo\common\event\UserLogin::class
        ],
        // Business登录事件，如需自定义就改成自己的命名空间
        'BusinessMemberLogin'   => [
            \woo\common\event\BusinessMemberLogin::class
        ]
    ],
    'subscribe' => [
    ],
];
