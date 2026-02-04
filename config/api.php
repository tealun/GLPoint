<?php
return [
    // 是否自动检测注解参数
    'is_check_param' => true,

    // 短信服务商 TopthinkCloud（顶想云） AlibabaCloud（阿里云）
    'sms_driver' => 'TopthinkCloud',

    // 自定义API返回码(非http状态码)
    'status' => [
        'success' => [
            'code' => 0,
            'message' => '成功'
        ],
        'error' => [
            'code' => -1,
            'message' => '失败'
        ],
        'exception' => [
            'code' => 4500,
            'message' => '异常'
        ],
        'nologin' => [
            'code' => 4401,
            'message' => '未登录'
        ],
        'nopower' => [
            'code' => 4402,
            'message' => '无权限'
        ],
        'forbidden' => [
            'code' => 4403,
            'message' => '拒绝访问'
        ],
        'badMethod' => [
            'code' => 4405,
            'message' => '请求方法错误'
        ],
        'badParam' => [
            'code' => 4406,
            'message' => '请求参数错误'
        ],
    ]
];