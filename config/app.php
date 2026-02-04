<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------


$business_domain_bind = [];
if (is_woo_installed()) {
    try {
        if ($domains = read_file_cache('business_domain_bind')) {
            foreach ($domains as $domain => $id) {
                $business_domain_bind[$domain] = 'business';
            }
        }
    } catch (\Exception $e){}
}

return [
    // 应用地址
    'app_host'         => env('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 默认应用
    'default_app'      => run_mode() == 'fpm' || is_woo_installed() ? 'index' : 'install',// FPM模式下或安装以后这里可以直接写死 如：'index'

    //'default_app' => 'cms',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [
        'run' => 'admin',
        'b' => 'business',
        //'a' => 'cms'
    ],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => array_merge(
        $business_domain_bind,
        [
            // 你自定义的域名绑定 这这里写即可
            // 'www' => 'cms',
            // 'admin' => 'admin',
            // 如果有商家使用了独立域名，那么business就不能再用"/应用名"的形式访问，你一定要给business绑定一个默认的域名，否则访问会出错
            // 'www.xxx.com' => 'business'
        ]
    ),
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [
        'common'
    ],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => false,
    'http_exception_template'    =>  [
        404 =>  \think\facade\App::getBasePath() . 'common/view/common/404.html',
        403 =>  \think\facade\App::getBasePath() . 'common/view/common/403.html',
        500 =>  \think\facade\App::getBasePath() . 'common/view/common/500.html',
    ],
    // 服务注册
    'service' => [
        \app\common\CustomJWTService::class,
    ]

];
