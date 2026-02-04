<?php
// +----------------------------------------------------------------------
// | Trace设置 开启调试模式后有效
// +----------------------------------------------------------------------
return [
    // 内置Html和Console两种方式 支持扩展
    'type'    => 'Html',
    // 读取的日志通道名
    'channel' => '',

    // 关闭trace 修改了底层源码实现的功能 默认只有关debug 确实没有效果 估计是composer更新以后 修改部分被覆盖了
    'is_trace' => !setting('admin_is_trace', true),
    // trace未关闭得情况下， 哪些应用禁止使用trace
    'trace_deny_app_list' => ['api', 'unicms', 'install'],

    // 是否将trace写入数据库（一般用于api调试）
    'is_db_trace' => setting('admin_is_db_trace', false),
    // 哪些应用 或控制器 禁止使用trace写入数据库
    // 如：'cms', 'install', 'index'应用全部禁用，admin应用中trace、index控制器的请求禁用
    'db_trace_deny_app_list' => ['cms', 'install', 'index', 'admin' => ['trace.Trace', 'Index']],
];
