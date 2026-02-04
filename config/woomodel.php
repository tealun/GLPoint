<?php
// 用于填写自己自定义的表单字段规则  填写格式参考  woo/common/builder/form/FormConfig
return [
    // 注册自定义的表单组件--后台字段 才可以选择到表单类型
    'form_item_lists' => [

    ],
    // 用户创建模型的时候，快速创建字段，可以自行定义一些自己常用的字段和规则
    'base_field_lists' => [

    ],
    'list_item_lists' => [

    ],
    'detail_item_lists' => [

    ],
    'filter_item_lists' => [

    ],
    // 自定义图标 列表 自定义图标，
    // 开发者自行扩展的图标，请合法合规进行扩展；开发者自行扩展的图标所产生的一切版权纠纷、法律责任和系统无关
    'form_custom_icons' => [
        // 自行参考格式：
        /*
        [
            'name' => '复制',
            'icon' => 'my-icon-fuzhi'
        ]
        */
    ],
    // 自定义图标类名的统一前缀 - 自行修改
    'custom_icons_prefix' => 'my-icon',
    // 模型导入、导出的密码，导入的时候需要输入该值做一道验证
    'export_key' => '666666',
    // 模型导入、导出有效期 单位：s
    'export_expire' => 86400
];