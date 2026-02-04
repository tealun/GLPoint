<?php

// +----------------------------------------------------------------------
// | 日志设置
// +----------------------------------------------------------------------
return [
    // 默认日志记录通道
    'default'      => env('log.channel', 'file'),
    // 日志记录级别
    'level'        => ['error', 'warning', 'info', 'debug'],  // 确保包含debug级别
    // 日志类型记录的通道
    'type_channel' => [
        'error'  => 'file',
        'warning' => 'file', 
        'info'   => 'file',
        'debug'  => 'file'
    ],
    // 是否关闭日志写入
    'close'       => false,
    // 全局日志处理 handle
    'handle'     => null,
    // 日志处理机制
    'channels'    => [
        'file' => [
            // 日志记录方式
            'type'           => 'File',
            // 日志保存目录
            'path'          => '',
            // 单文件日志写入
            'single'        => false,
            // 独立日志级别
            'apart_level'   => ['error', 'warning', 'info', 'debug'],
            // 最大日志文件数量
            'max_files'     => 0,
            // 使用JSON格式记录
            'json'          => false,
            // 日志处理
            'handle'       => null,
            // 时间格式化
            'time_format'  => 'Y-m-d H:i:s',
        ],
    ],
];
