<?php
// 该文件为 API 应用的认证配置，主要用于控制接口的登录认证方式和行为。
// 主要配置项说明如下：

return [
    'apps' => [
        'api' => [
            // 认证类型，这里为 jwt（即 JSON Web Token 认证）
            'type'                 => 'jwt',

            // 认证用的用户模型，这里为 User（即 app\api\model\User）
            'model'                => 'User',

            // 认证失败时返回的数据格式，这里为 json
            'response_mode'        => 'json',

            // 未登录时返回的 json 数据
            'response_json' => ["result" => "nologin", "message" => "未登录"],

            // 被禁止访问时返回的 json 数据
            'forbid_response_json' => ["result" => "error", "message" => "当前请求被拒绝"],

            // 允许登录的模型及其自动关联的模型（withJoin），如 User 自动关联 UserGroup、UserGrade
            'allow_login_model' => [
                'User' => [
                    'withJoin' => ['UserGroup','UserGrade']
                ]
            ],

            // 是否允许所有来源（跨域），true 表示允许
            'allow_from_all' => true,

            // 是否注解排除（一般用于开发调试），false 表示不排除
            'is_annotation_except' => false,
        ],
    ],
];