<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'cron:run' => \woo\common\cron\command\Run::class,
        'cron:schedule' => \woo\common\cron\command\Schedule::class,
        'hello' => 'app\command\Hello'
    ],
];
