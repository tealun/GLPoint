<?php

return [
    // 开发企业信息
    'about' => [
        // 登录页
        'login_title' => '自定义后台管理系统标题',
        'login_ex_title' => 'Management System',
        'login_copyright' => '自定义登录页底部信息　精心铸造应用管理系统',
        'login_copyright_link' => '',

        // 开发企业
        'title'     => '我的公司名称',// 公司名称
        'site'      => 'https://www.我的官网.cn',// 网站
        'service'   => '123456',// 客服
        'tel'       => '13688886666',
        'email'     => '123456@qq.com',
        'address'   => '成都市锦江区XXX',

        // 管理名称
        'logo_title' => '我的后台管理系统',
        'logo_image' => 'static/woo/pear/admin/images/logo.png'
     ],
    'public_name' => 'public',
    // 创建模型的时候是否备份原文件
    'is_model_backup' => false,

    // 自定义Table模板文件 只能是一个文件 不支持数组
    'custom_templet_file' => root_path() . 'app/common/view/table/templet/common.html',

    // ThinkApi AppCode  https://market.topthink.com/my/security获取AppCode：
    'app_code' => '',

    'oauth_register_url' => '',

    // 系统封装createData和modifyData添加和修改是否严格检查投稿字段
    'strict_check_contribute_fields' => true,

    //RSA公钥
    'rsa_public' => '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDGS3TxdYcmiRrvFhj2H6N3JUg8
13AgCyf6sczRZbY/wNwH1BdsMhuYsFfsYXcmITp3SBj1lFtAIYtrk89+CjdV0Kw8
4uktlhT3KtlXYjFr9mMcGQ4qHyROZz7/wHtCH+/eZ06HF+X0cjkuEIkuzEKdy9UN
00xEWT7AT3QLXyWAAwIDAQAB
-----END PUBLIC KEY-----',

    //RSA私钥 -- 请妥善保管
    'rsa_private' => '-----BEGIN PRIVATE KEY-----
MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAMZLdPF1hyaJGu8W
GPYfo3clSDzXcCALJ/qxzNFltj/A3AfUF2wyG5iwV+xhdyYhOndIGPWUW0Ahi2uT
z34KN1XQrDzi6S2WFPcq2VdiMWv2YxwZDiofJE5nPv/Ae0If795nTocX5fRyOS4Q
iS7MQp3L1Q3TTERZPsBPdAtfJYADAgMBAAECgYBrrBlp60lhsV0Aqd0P+AoI7iOV
dd4aaDRjOhxfL32QjKZtUcsOIv89H7P+VMYf3DclAe8bml0aK5zK403twJpedy53
P7bkmdmaSBfpdN+wOB+ucZSW8M7TYTUSbaVTyVS6YHGQ3Q7yZvAaaJt/zMdJ9Y0p
dfle/mo/jqNgJdb/sQJBAPODlXfHlsYG+p5O6AjGXJYs0BlUKkIiavrT36ycFsJ/
0KrBSIAmyMdOUgwK1/BEdAwf3GAURvt0UWpdCWcDxocCQQDQdlDJ+Ul1jf4l8NmG
VAvaKay/O8l3w1Cj4TUmgWxg0KtF8KsZo93cIlBzslGtg0XIzp+QsW3dfKOiPO2P
DN2lAkBjDHWid+OC/tm3xL6quwxz5RxsAQkDR36eMcn8Kq0zRcv5eI7l2WC3eMr0
rQBycVWGjPsVdYn9w61OBzPI3AdHAkAyF3wqTGC+grRDYbCjeqaucb005qTuxlwm
RQOEkSz9xqahU8eJjbrOHuC+LGc8DoNCUYQ+PKRtyHl5jrJ24VyRAkAlVHG9A3ow
v0hed6E1FD7LlVmyJ2QsjL1CWBmuJ3RbGaQXVW0FYJ08wNWm1mCDz01YaTDriyVP
F8tiXnHrumcQ
-----END PRIVATE KEY-----'


];
