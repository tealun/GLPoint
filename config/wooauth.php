<?php
return [
    // Auth 处理类 你可以继承系统的Auth，然后自行进行扩展或修改
    'handler' => \woo\common\Auth::class,
    'session_key' => 'abcxyz666',//v2.0.4以后新增 自定义统一的key
    'apps' => [

        'admin' => [
            'type'                 => 'session', //验证方式 支持session和jwt
            //'session_key'          => 'admin_login', //2.0.4以后已取消该配置 不能每个应用自定义key了
            'model'                => 'Admin',
            'response_mode'        => 'Admin/login',
            'response_json'        => ["result" => "nologin", "message" => "当前账号未登录，请刷新"],
            'except'               => ['Admin/login', 'Admin/ajaxlogin'],
            'forbid_response_json' => ["result" => "error", "message" => "当前请求被拒绝"],
            'forbid_resonse_view'  => \think\facade\App::getBasePath() . 'common/view/common/403.html',
            'allow_login_model' => ['Admin' => ['withJoin' => ['AdminGroup','Department']]]
            //'allow_login_model' => ['Admin' => ['withJoin' => ['Department']]]
        ],
        /* // 后台想用jwt把上面注释了 把这个注释打开
        'admin' => [
            'type'                 => 'jwt', //验证方式 支持session和jwt
            'model'                => 'Admin',
            'response_mode'        => 'Admin/login',
            'response_json'        => ["result" => "nologin", "message" => "当前账号未登录，请刷新"],
            'except'               => ['Admin/login', 'Admin/ajaxlogin'],
            'forbid_response_json' => ["result" => "error", "message" => "当前请求被拒绝"],
            'forbid_resonse_view'  => \think\facade\App::getBasePath() . 'common/view/common/403.html',
            'allow_login_model' => ['Admin' => ['withJoin' => ['AdminGroup','Department']]]
        ],
        */
        'index' => [
            'type'                 => 'session',
            'model'                => 'User',
            'response_mode'        => 'json',
            'allow_from_all'       => true,
            'except'               => [],// 必须登录才可以访问的列表  Index  Index/index
            'is_annotation_except' => true, //true 以后才可以使用Except注解控制登录访问
        ],
        //...
    ],

    'power_check'                 => true,
    'power_reset'                 => true,
    // 超级权限角色组ID
    'super_group_id'              => 1,
    // 没有在权限节点的路由是否强制鉴权
    'force_check_power'           => false,

    'is_log'                      => false,
    'is_request_log'              => false,

    // api应用 调试阶段是否启用权限验证
    'is_api_debug_power'          => false,

    // cms等应用 调试阶段是否启用权限验证
    'is_user_debug_power' => true,

    // 中台如果没有菜单授权就给与全部菜单权限  比如：针对每个商家都开启所有功能的情况，不然每次改了菜单 都要重新授权 麻烦
    'business_no_power_is_all' => true,
    // 是否时时检查（每次请求检查一次）商家状态（比如 时候关停、是否启用、停用）  如果你的业务没有这些需求就 关闭时时检查
    'business_check_always' => false,

];