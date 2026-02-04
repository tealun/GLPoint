<?php
declare (strict_types = 1);

namespace woo\common\builder\form;

use think\facade\Config;
use woo\common\helper\Arr;

class FormConfig
{
    protected $config = [
        'form_item_lists' => [
            'text' => [
                'name' => '单行文本框',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => '',
                ]
            ],
            'number' => [
                'name' => 'H5-number标签',
                'data' => [
                    'type' => 'INT',
                    'length' => '',
                    'default' => '0',
                ]
            ],
            'textarea' => [
                'name' => '多行文本框',
                'data' => [
                    'type' => 'TEXT',
                    'length' => '',
                    'default' => 'none',
                ]
            ],
            'password' => [
                'name' => '密码输入框',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '32',
                    'default' => ''
                ]
            ],
            'radio' => [
                'name' => '单选框',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => ''
                ]
            ],
            'select' => [
                'name' => '下拉框',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => ''
                ]
            ],
            'checkbox' => [
                'name' => '多选框',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => ''
                ]
            ],
            'checker' => [
                'name' => '是否',
                'data' => [
                    'type' => 'TINYINT',
                    'length' => '1',
                    'default' => '0',
                    'is_unsigned' => 1
                ]
            ],
            'date' => [
                'name' => '日期选择',
                'data' => [
                    'type' => 'DATE',
                    'length' => '',
                    'default' => '2021-01-01',
                    'list' => 'date'
                ]
            ],
            'datetime' => [
                'name' => '日期时间选择',
                'data' => [
                    'type' => 'DATETIME',
                    'length' => '',
                    'default' => '2021-01-01 00:00:00',
                    'list' => 'datetime'
                ]
            ],
            'time' => [
                'name' => '时间选择',
                'data' => [
                    'type' => 'TIME',
                    'length' => '',
                    'default' => '00:00:00',
                    'list' => 'time'
                ]
            ],
            'month' => [
                'name' => '年月选择',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => '',
                ]
            ],
            'year' => [
                'name' => '年份选择',
                'data' => [
                    'type' => 'INT',
                    'length' => '',
                    'default' => '0',
                ]
            ],
            'xmselect' => [
                'name' => '下拉单(多)选（更多功能需要其他属性配合）',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => ''
                ]
            ],
            'image' => [
                'name' => '单图上传（更多功能需要其他属性配合）',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '128',
                    'default' => '',
                ]
            ],
            'file' => [
                'name' => '单文件上传（更多功能需要其他属性配合）',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '128',
                    'default' => '',
                ]
            ],
            'multiimage' => [
                'name' => '多图上传（更多功能需要其他属性配合）',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '1024',
                    'default' => '',
                ]
            ],
            'multifile' => [
                'name' => '多文件上传（更多功能需要其他属性配合）',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '1024',
                    'default' => '',
                ]
            ],
            'array' => [
                'name' => '数组',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '512',
                    'default' => ''
                ]
            ],
            'keyvalue' => [
                'name' => '键值对',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '512',
                    'default' => ''
                ]
            ],
            'hidden' => [
                'name' => '隐藏域',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '128',
                    'default' => '',
                    'list' => '0'
                ]
            ],
            'relation' => [
                'name' => '模型关联(弹窗表格式)',
                'data' => [
                    'type' => 'INT',
                    'length' => '',
                    'default' => '0',
                    'list' => 'relation'
                ]
            ],
            'relation2' => [
                'name' => '模型关联(下拉列表式)[V2.2.3]',
                'data' => [
                    'type' => 'INT',
                    'length' => '',
                    'default' => '0',
                    'list' => 'relation'
                ]
            ],
            'cascader' => [
                'name' => '联级选择[V2.0.4]',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => '',
                ]
            ],
            'xmtree' => [
                'name' => '父级选择(无限极,建议)',
                'data' => [
                    'type' => 'INT',
                    'length' => '',
                    'default' => '0'
                ]
            ],
            'xmselectfortree' => [
                'name' => '父级选择(无限极)',
                'data' => [
                    'type' => 'INT',
                    'length' => '',
                    'default' => '0'
                ]
            ],
            'selectfortree' => [
                'name' => '父级选择(无限极)',
                'data' => [
                    'type' => 'INT',
                    'length' => '',
                    'default' => '0'
                ]
            ],
            'color' => [
                'name' => '取色器',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '16',
                    'default' => '',
                ]
            ],
            'amap' => [
                'name' => '高德地图[V2.0.8]',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => '',
                ]
            ],
            'slider' => [
                'name' => '滑块[V2.0.4]',
                'data' => [
                    'type' => 'INT',
                    'length' => '',
                    'default' => '0',
                ]
            ],
            'rate' => [
                'name' => '评分[V2.0.4]',
                'data' => [
                    'type' => 'TINYINT',
                    'length' => '',
                    'default' => '0',
                ]
            ],
            'transfer' => [
                'name' => '穿梭框[V2.0.4]',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '128',
                    'default' => '',
                ]
            ],
            'icon' => [
                'name' => '图标',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => '',
                    'list' => 'icon'
                ]
            ],
            'iconpicker' => [
                'name' => '图标选择器[V2.2.3]',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => '',
                    'list' => 'icon'
                ]
            ],
            'multiattrs' => [
                'name' => '多列多属性（更多功能需要其他属性配合）',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '2048',
                    'default' => '',
                ]
            ],
            'together' => [
                'name' => '表格式关联写入[V2.1.0](非真实字段)',
                'data' => [
                    'is_field' => 0,// 非真实字段
                ]
            ],
            'together2' => [
                'name' => '选项卡式关联写入[V2.2.8](非真实字段)',
                'data' => [
                    'is_field' => 0,
                ]
            ],
            'orderitem' => [
                'name' => '明细录入[V2.1.5](非真实字段)',
                'data' => [
                    'is_field' => 0
                ]
            ],
            'spec' => [
                'name' => '多规格[V2.1.2]',
                'data' => [
                    'type' => 'MEDIUMTEXT',
                    'length' => '',
                    'default' => 'none',
                    'list' => '0'
                ]
            ],
            'sortvalues' => [
                'name' => '值排序[V2.1.2]',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '128',
                    'default' => '',
                    'list' => '0'
                ]
            ],
            'ueditor' => [
                'name' => 'Ueditor富文本',
                'data' => [
                    'type' => 'MEDIUMTEXT',
                    'length' => '',
                    'default' => 'none',
                    'list' => '0',
                    'detail' => 'html'
                ]
            ],
            'json' => [
                'name' => 'Json对象编辑',
                'data' => [
                    'type' => 'TEXT',
                    'length' => '',
                    'default' => 'none',
                    'list' => '0'
                ]
            ],
            'random' => [
                'name' => '随机字符串[V2.1.0]',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => '',
                ]
            ],
            'tag' => [
                'name' => '标签',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '128',
                    'default' => '',
                ]
            ],
            'colorh5' => [
                'name' => 'H5-color标签',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '16',
                    'default' => '',
                ]
            ],
            'email' => [
                'name' => '邮箱格式输入[V2.0.5]',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => '',
                ]
            ],
            'bankcard' => [
                'name' => '银行卡格式输入[V2.0.5]',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '32',
                    'default' => '',
                ]
            ],
            'ip4' => [
                'name' => 'IP4格式输入[V2.0.5]',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '32',
                    'default' => '',
                ]
            ],
            'ip6' => [
                'name' => 'IP6格式输入[V2.0.5]',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '64',
                    'default' => '',
                ]
            ],
            'telh5' => [
                'name' => 'H5-tel标签',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '32',
                    'default' => '',
                ]
            ],
            'urlh5' => [
                'name' => 'H5-url标签',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '128',
                    'default' => '',
                ]
            ],
            'format' => [
                'name' => '直接输出',
                'data' => [
                    'type' => 'VARCHAR',
                    'length' => '128',
                    'default' => '',
                ]
            ],
            'captcha' => [
                'name' => '验证码',
                'data' => [
                    'is_field' => 0
                ]
            ],
        ],

        'base_field_lists' => [
            'id'         => [
                'name' => '主键(id)',
                'sql' => "`id` int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ID'",
                'data' => [
                    'field'     => 'id',
                    'name'     => 'ID',
                    'type'      => 'INT',
                    'length'    => '10',
                    'default'   => 'none',
                    'is_not_null'   => 1,
                    'is_unsigned' => '1',
                    'is_ai'     => 1,
                    'index'     => '',// 如果为index或unique会自动创建索引
                    'form'      => 'hidden',
                    'business_form'      => 'hidden',
                    'is_system' => '1',
                ]
            ],
            'parent_id'       => [
                'name' => '父级ID(parent_id)，无限极必须',
                'sql'  => "`parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID'",
                'data' => [
                    'field'     => 'parent_id',
                    'name'     => '父级ID',
                    'type'      => 'INT',
                    'length'    => '10',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => '1',
                    'index'     => 'index',
                    'form'      => 'xmtree',
                    'business_form'      => 'xmtree',
                    'is_system' => '1',
                    'validate'  => '[{"rule":"call","args":"checkParent","on":"","message":""}]',
                    'business_validate'  => '[{"rule":"call","args":"checkParent","on":"","message":""}]'
                ]
            ],
            'title'       => [
                'name' => '标题(title)',
                'sql'  => "`title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题'",
                'data' => [
                    'field'     => 'title',
                    'name'     => '标题',
                    'type'      => 'VARCHAR',
                    'length'    => '128',
                    'default'   => '',
                    'is_not_null'   => 1,
                    'is_unsigned' => '',
                    'index'     => '',
                    'form'      => 'text',
                    'business_form'      => 'text',
                    'is_system' => '1',
                    'validate'  => '[{"rule":"require","args":"","on":"0","message":""}]',
                    'business_validate'  => '[{"rule":"require","args":"","on":"0","message":""}]'
                ]
            ],
            'is_verify'   => [
                'name' => '审核(is_verify)',
                'sql'  => "`is_verify` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否审核'",
                'data' => [
                    'field'     => 'is_verify',
                    'name'     => '审核',
                    'type'      => 'TINYINT',
                    'length'    => '1',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 1,
                    'index'     => 'index',
                    'form'      => 'checker',
                    'business_form'      => 'checker',
                    'list' => 'checker',
                    'business_list' => 'checker',
                    'is_system' => '1',
                ]
            ],
            'date'        => [
                'name' => '日期(date)',
                'sql'  => "`date` date NOT NULL DEFAULT '1970-01-01' COMMENT '日期'",
                'data' => [
                    'field'     => 'date',
                    'name'     => '日期',
                    'type'      => 'DATE',
                    'length'    => '',
                    'default'   => '2000-01-01',
                    'is_not_null'   => 1,
                    'is_unsigned' => '',
                    'index'     => '',
                    'list'      => 'date',
                    'form'      => 'date',
                    'business_form'      => 'date',
                    'is_system' => '1',
                ]
            ],
            'image'       => [
                'name' => '图片(image)',
                'sql'  => "`image` varchar(128) NOT NULL DEFAULT '' COMMENT '图片'",
                'data' => [
                    'field'     => 'image',
                    'name'     => '图片',
                    'type'      => 'VARCHAR',
                    'length'    => '128',
                    'default'   => '',
                    'is_not_null'   => 1,
                    'is_unsigned' => '',
                    'index'     => '',
                    'form'      => 'image',
                    'list'      => 'file',
                    'business_form'      => 'image',
                    'business_list'      => 'file',
                    'is_system' => '1',
                ]
            ],
            'file'       => [
                'name' => '文件(file)',
                'sql'  => "`file` varchar(128) NOT NULL DEFAULT '' COMMENT '文件'",
                'data' => [
                    'field'     => 'file',
                    'name'     => '文件',
                    'type'      => 'VARCHAR',
                    'length'    => '128',
                    'default'   => '',
                    'is_not_null'   => 1,
                    'is_unsigned' => '',
                    'index'     => '',
                    'form'      => 'file',
                    'list'      => 'file',
                    'business_form'      => 'file',
                    'business_list'      => 'file',
                    'is_system' => '1',
                ]
            ],
            'content'     => [
                'name' => '内容(content)',
                'sql'  => "`content` mediumtext NOT NULL COMMENT '内容'",
                'data' => [
                    'field'     => 'content',
                    'name'     => '内容',
                    'type'      => 'MEDIUMTEXT',
                    'length'    => '',
                    'default'   => 'NULL',
                    'is_not_null'   => 1,
                    'is_unsigned' => '',
                    'index'     => '',
                    'form'      => 'ueditor',
                    'business_form'      => 'ueditor',
                    'is_system' => '1',
                    'list'      => '0',
                    'business_list'=> '0',
                    'detail' => 'html',
                    'business_detail' => 'html'
                ]
            ],
            'list_order'  => [
                'name' => '排序权重(list_order)',
                'sql'  => "`list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重'",
                'data' => [
                    'field'     => 'list_order',
                    'name'     => '排序权重',
                    'type'      => 'INT',
                    'length'    => '11',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 0,
                    'index'     => 'index',
                    'form'      => 'number',
                    'business_form'      => 'number',
                    'is_system' => '1',
                ]
            ],
            'admin_id'  => [
                'name' => '管理员ID(admin_id)',
                'sql'  => "`admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID'",
                'data' => [
                    'field'     => 'admin_id',
                    'name'     => '管理员ID',
                    'type'      => 'INT',
                    'length'    => '10',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 1,
                    'index'     => 'index',
                    'form'      => '',
                    'list'      => '0',
                    'business_form'      => '',
                    'business_list'      => '0',
                    'is_system' => '1',
                ]
            ],
            'user_id'  => [
                'name' => '会员ID(user_id)',
                'sql'  => "`user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID'",
                'data' => [
                    'field'     => 'user_id',
                    'name'     => '会员ID',
                    'type'      => 'INT',
                    'length'    => '10',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 1,
                    'index'     => 'index',
                    'form'      => '',
                    'list'      => '0',
                    'business_form'      => '',
                    'business_list'      => '0',
                    'is_system' => '1',
                ]
            ],
            'business_id'  => [
                'name' => '商家ID(business_id)',
                'sql'  => "`business_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID'",
                'data' => [
                    'field'     => 'business_id',
                    'name'     => '商家ID',
                    'type'      => 'INT',
                    'length'    => '10',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 1,
                    'index'     => 'index',
                    'form'      => '',
                    'list'      => '0',
                    'business_form'      => '',
                    'business_list'      => '0',
                    'is_system' => '1',
                ]
            ],
            'business_member_id'  => [
                'name' => '商家管理员ID(business_member_id)',
                'sql'  => "`business_member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商家管理员ID'",
                'data' => [
                    'field'     => 'business_member_id',
                    'name'     => '商家管理员ID',
                    'type'      => 'INT',
                    'length'    => '10',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 1,
                    'index'     => 'index',
                    'form'      => '',
                    'list'      => '0',
                    'business_form'      => '',
                    'business_list'      => '0',
                    'is_system' => '1',
                ]
            ],
            'family'       => [
                'name' => '家族(family)，无限极选用',
                'sql'  => "`family` varchar(256) NOT NULL DEFAULT '' COMMENT '家族'",
                'data' => [
                    'field'     => 'family',
                    'name'     => '家族',
                    'type'      => 'VARCHAR',
                    'length'    => '256',
                    'default'   => '',
                    'is_not_null'   => 1,
                    'is_unsigned' => '',
                    'index'     => '',
                    'form'      => '',
                    'list'      => '0',
                    'business_form'      => '',
                    'business_list'      => '0',
                    'is_system' => '1',
                ]
            ],
            'level'  => [
                'name' => '层级(level)，无限极建议',
                'sql'  => "`level` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '层级'",
                'data' => [
                    'field'     => 'level',
                    'name'     => '层级',
                    'type'      => 'SMALLINT',
                    'length'    => '5',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 1,
                    'index'     => '',
                    'form'      => '',
                    'list'      => '0',
                    'business_form'      => '',
                    'business_list'      => '0',
                    'is_system' => '1',
                ]
            ],
            'children_count'  => [
                'name' => '下级数(children_count)，无限极建议',
                'sql'  => "`children_count` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '下级数'",
                'data' => [
                    'field'     => 'children_count',
                    'name'     => '下级数',
                    'type'      => 'SMALLINT',
                    'length'    => '5',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 1,
                    'index'     => '',
                    'form'      => '',
                    'list'      => '0',
                    'business_form'      => '',
                    'business_list'      => '0',
                    'is_system' => '1',
                ]
            ],
            'create_time' => [
                'name' => '创建日期(create_time)',
                'sql'  => "`create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建日期'",
                'data' => [
                    'field'     => 'create_time',
                    'name'     => '创建日期',
                    'type'      => 'INT',
                    'length'    => '10',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 1,
                    'index'     => '',
                    'form'      => '',
                    'list_attrs' => '{"width":"146"}',
                    'business_form'      => '',
                    'business_list_attrs' => '{"width":"146"}',
                    'is_system' => '1',
                ]
            ],
            'update_time' => [
                'name' => '修改日期(update_time)',
                'sql'  => "`update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改日期'",
                'data' => [
                    'field'     => 'update_time',
                    'name'     => '修改日期',
                    'type'      => 'INT',
                    'length'    => '10',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 1,
                    'index'     => '',
                    'form'      => '',
                    'list_attrs' => '{"width":"146"}',
                    'business_form'      => '',
                    'business_list_attrs' => '{"width":"146"}',
                    'is_system' => '1',
                ]
            ],
            'delete_time' => [
                'name' => '删除日期(delete_time)',
                'sql'  => "`delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除日期'",
                'data' => [
                    'field'     => 'delete_time',
                    'name'     => '删除日期',
                    'type'      => 'INT',
                    'length'    => '10',
                    'default'   => '0',
                    'is_not_null'   => 1,
                    'is_unsigned' => 1,
                    'index'     => 'index',
                    'form'      => '',
                    'list'      => '0',
                    'list_attrs' => '{"width":"146"}',
                    'business_form'      => '',
                    'business_list'      => '0',
                    'business_list_attrs' => '{"width":"146"}',
                    'is_system' => '1',
                ]
            ]
        ],
        'list_item_lists' => [
            '' => '由程序默认处理',
            '0' => '不显示在列表[0]',
            'show' => '原样输出[show]',
            'text' => '文本输出[text]',
            'html' => 'html输出[html]（前台提交数据慎重）',
            'file' => '文件[file]',
            'avatar' => '头像[avatar]',
            'username' => '用户名[username]',
            'checker' => '列表开关[checker]',
            'checker.show' => '列表开关禁用[checker.show]',
            'checker.text' => '列表开关文本[checker.text]',
            'showAndExport' => '原样输出(针对特殊导出)[showAndExport]',
            'relation' => '模型关联[relation]',
            'options' => '选项输出[options]',
            'datetime' => '日期时间[datetime]',
            'date' => '日期[date]',
            'time' => '时间[time]',
            'year' => '年份[year]',
            'month' => '年月[month]',
            'dateFormat' =>'时间格式化[dateFormat]',
            'counter' => 'counter输出',
            'filesize' => '文件大小[filesize]',
            'icon' => '图标[icon]',
            'url' => 'URL输出[url]',
            'ua'  => 'User-Agent[ua]',
            'password' => '密码输出[password]',
        ],
        'detail_item_lists' => [
            '' => '由程序默认处理',
            '0' => '不显示在详情[0]',
            'show' => '原样输出[show]',
            'text' => '文本输出[text]',
            'file' => '文件[file]',
            'checker' => '列表开关[checker]',
            'checker.show' => '列表开关禁用[checker.show]',
            'checker.text' => '列表开关文本[checker.text]',
            'showAndExport' => '原样输出(针对特殊导出)[showAndExport]',
            'relation' => '模型关联[relation]',
            'options' => '选项输出[options]',
            'datetime' => '日期时间[datetime]',
            'date' => '日期[date]',
            'time' => '时间[time]',
            'year' => '年份[year]',
            'month' => '年月[month]',
            'dateFormat' =>'时间格式化[dateFormat]',
            'counter' => 'counter输出',
            'filesize' => '文件大小[filesize]',
            'icon' => '图标[icon]',
            'url' => 'URL输出[url]',
            'ua'  => 'User-Agent[ua]',
            'password' => '密码输出[password]',
        ],
        'filter_item_lists' => [
            '' => '非列表搜索字段',
            '1' => '由程序默认处理',
            'string' => '字符串搜索',
            'text' => '文本搜索',
            'relation' => '关联模型搜索',
            'compare' => '数字比较搜索',
            'select' => '下拉搜索',
            'date' => '日期搜索',
            /*
            'year' => '年份搜索',
            'month' => '年月搜索',
            'time' => '时间搜索',
            */
            'date_range' => '日期(区间)搜索',
            'year_range' => '年份(区间)搜索',
            'month_range' => '年月(区间)搜索',
            'time_range' => '时间(区间)搜索',
            'datetime_range' => '日期时间(区间)搜索',
            'number' => '数字搜索',
            'number_range' => '数字区间搜索',
            'cascader' => '级联搜索'
        ]
    ];

    public static function get(string $name = '')
    {
        $self = new self();
        $congig = Arr::merge($self->config, Config::get('woomodel'));
        if ($name) {
            if (isset($congig[$name])) {
                return $congig[$name];
            }
            return [];
        }
        return $congig;
    }
}