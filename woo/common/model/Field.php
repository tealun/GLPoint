<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use think\facade\Db;
use think\facade\Env;
use think\facade\Log;
use woo\common\helper\CreateFile;
use woo\common\helper\Str;
use woo\common\builder\form\FormConfig;
use think\facade\Cache;

class Field extends App
{
    public $cname = '字段';
    public $parentModel = 'Model';
    public $display = 'field';
    public $relationLink = array(
        'Model' => [
            'type' => 'belongsTo'
        ]
    );

    public $sortable = true;
    public $orderType = 'ASC';
    /** 自定义数据 */
    public $customData = [
        'create' => true,
        'batch_delete' => true,
        'modify' => true,
        'delete' => true,
        'detail' => true,
    ];

    protected function start()
    {
        $this->form = [
            'id' => [
                'type' => 'integer',
                'name' => 'ID',
                'elem' => 'hidden',
            ],
            'field' => [
                'type' => 'string',
                'name' => '字段名',
                'require' => true,
                'elem' => 'text',
                'filter' => function ($val) {
                    return Str::snake(trim($val));
                },
                'list' => [
                    'templet' => 'show.blue',
                    'width' => 160
                ],
                'attrs' => [
                    'placeholder' => '如：title'
                ]
            ],
            'name' => [
                'type' => 'string',
                'name' => '字段名称',
                'elem' => 'text',
                'require' => true,
                'filter' => 'trim',
                'list' => [
                    'width' => 140
                ],
                'attrs' => [
                    'placeholder' => '如：标题'
                ]

            ],
            'model_id' => [
                'type' => 'integer',
                'name' => '所属模型',
                //'elem' => 0,
                'list' => [
                    'templet' => 'relation',
                    'name' => '所属模型'
                ],
                'foreign' => 'Model', //如果字段是按规范定义的 可以省略改属性
                'foreign_tab' => [
                ]
            ],
            'form' => [
                'type' => 'string',
                'name' => '表单类型',
                'elem' => 'xmselect',
                'attrs' => [
                    'data-max' => 1
                ],
                'list' => [
                    'title' => '后台表单类型/修改时类型',
                    'templet' => 'merge',
                    'width' => 280,
                    'merge_fields' => [
                        'form' => [
                            'templet' => 'show'
                        ],
                        'modify_form' => [
                            'templet' => 'show'
                        ],
                    ]
                ]
            ],
            'business_form' => [
                'type' => 'string',
                'name' => '表单类型',
                'elem' => 'xmselect',
                'attrs' => [
                    'data-max' => 1
                ],
                'list' => [
                    'title' => '中台表单类型/修改时类型',
                    'templet' => 'merge',
                    'width' => 280,
                    'merge_fields' => [
                        'business_form' => [
                            'templet' => 'show'
                        ],
                        'business_modify_form' => [
                            'templet' => 'show'
                        ],
                    ]
                ]
            ],
            'modify_form' => [
                'type' => 'string',
                'name' => '修改时表单类型',
                'elem' => 'xmselect',
                'tip' => '不定义默认都取上一个字段的值，特殊情况修改时不一样时定义，仅后台修改操作有效',
                'attrs' => [
                    'data-max' => 1
                ],
                'list' => 0
            ],
            'business_modify_form' => [
                'type' => 'string',
                'name' => '修改时表单类型',
                'elem' => 'xmselect',
                'tip' => '不定义默认都取上一个字段的值，特殊情况修改时不一样时定义，仅后台修改操作有效',
                'attrs' => [
                    'data-max' => 1
                ],
                'list' => 0
            ],
            'form_options' => [
                'type' => 'array',
                'name' => '选项值列表',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'business_form_options' => [
                'type' => 'array',
                'name' => '选项值列表',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'form_foreign' => [
                'type' => 'string',
                'name' => '关联键名',
                'elem' => 'text',
                'attrs' => [
                    'placeholder' => "填写格式如：Model或Model-name"
                ],
                'list' => 0
            ],
            'business_form_foreign' => [
                'type' => 'string',
                'name' => '关联键名',
                'elem' => 'text',
                'attrs' => [
                    'placeholder' => "填写格式如：Model或Model-name"
                ],
                'list' => 0
            ],
            'form_item_attrs' => [
                'type' => 'array',
                'name' => '表单项属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'business_form_item_attrs' => [
                'type' => 'array',
                'name' => '表单项属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'form_tag_attrs' => [
                'type' => 'array',
                'name' => '标签属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'business_form_tag_attrs' => [
                'type' => 'array',
                'name' => '标签属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'form_upload' => [
                'type' => 'array',
                'name' => '上传配置',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'business_form_upload' => [
                'type' => 'array',
                'name' => '上传配置',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'form_trigger' => [
                'type' => 'array',
                'name' => '表单触发器',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'business_form_trigger' => [
                'type' => 'array',
                'name' => '表单触发器',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'list' => [
                'type' => 'html',
                'name' => '列表模板',
                'elem' => 'text',
                'quick' => [],
                'attrs' => [
                    'placeholder' => '请自行填写列表模板名称，下拉仅仅辅助你快速填写特殊值而已',
                    'autocomplete' => 'off'
                ],
                'list' => 0,
            ],
            'business_list' => [
                'type' => 'html',
                'name' => '列表模板',
                'elem' => 'text',
                'quick' => [],
                'attrs' => [
                    'placeholder' => '请自行填写列表模板名称，下拉仅仅辅助你快速填写特殊值而已',
                    'autocomplete' => 'off'
                ],
                'list' => 0,
            ],
            'list_attrs' => [
                'type' => 'array',
                'name' => '列表属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'business_list_attrs' => [
                'type' => 'array',
                'name' => '列表属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'list_filter' => [
                'type' => 'string',
                'name' => '列表搜索',
                'elem' => 'text',
                'quick' => [],
                'attrs' => [
                    'placeholder' => '请自行填写列表搜索方式，下拉仅仅辅助你快速填写特殊值而已',
                ],
                'list' => 0,
            ],
            'business_list_filter' => [
                'type' => 'string',
                'name' => '列表搜索',
                'elem' => 'text',
                'quick' => [],
                'attrs' => [
                    'placeholder' => '请自行填写列表搜索方式，下拉仅仅辅助你快速填写特殊值而已',
                ],
                'list' => 0,
            ],
            'list_filter_attrs' => [
                'type' => 'array',
                'name' => '搜索属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'business_list_filter_attrs' => [
                'type' => 'array',
                'name' => '搜索属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'list_filter_tag_attrs' => [
                'type' => 'array',
                'name' => '搜索标签属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'business_list_filter_tag_attrs' => [
                'type' => 'array',
                'name' => '搜索标签属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'detail' => [
                'type' => 'html',
                'name' => '详情模板',
                'elem' => 'text',
                'quick' => [],
                'attrs' => [
                    'placeholder' => '请自行填写列表模板名称，下拉仅仅辅助你快速填写特殊值而已',
                ],
                'list' => 0,
            ],
            'business_detail' => [
                'type' => 'html',
                'name' => '详情模板',
                'elem' => 'text',
                'quick' => [],
                'attrs' => [
                    'placeholder' => '请自行填写列表模板名称，下拉仅仅辅助你快速填写特殊值而已',
                ],
                'list' => 0,
            ],
            'detail_attrs' => [
                'type' => 'array',
                'name' => '详情属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'business_detail_attrs' => [
                'type' => 'array',
                'name' => '详情属性',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'validate' => [
                'type' => 'array',
                'name' => '数据验证',
                'elem' => 'multiattrs',
                'list' => 0,
                'fields' => [
                    'rule' => [
                        'label' => '规则',
                        'elem' => 'text',
                        'quick' => [
                            '格式验证类(无需参数)' => [
                                'require' => '必须',
                                'number' => '纯数字',
                                'integer'  => '整数',
                                'float' => '浮点数',
                                'email' => '邮箱',
                                'mobile' => '手机',
                                'idCard' => '身份证',
                                'alpha'  => '纯字母',
                                'chs'  => '汉字',
                                'lower'  => '小写',
                                'upper'  => '大写',
                                'url'  => 'URL',
                                'ip'  => 'IP',
                                'captcha' => '验证码',
                                'dateFormat:Y-m-d' => 'Y-m-d日期',
                                'dateFormat:Y-m-d H:i:s' => 'Y-m-d H:i:s日期时间'
                            ],
                            '长度和区间(需要参数)' => [
                                'in'  => '某个范围',
                                'between'  => '否在某个区间',
                                'length'  => '长度是否在某个范围',
                                'max'  => '最大长度',
                                'min'  => '最小长度'
                            ],
                            '比较(需要参数)' => [
                                'confirm'  => '和另外一个字段的值一致',
                                'different'  => '和另外一个字段的值不一致',
                                'eq'  => '等于',
                                'egt'  => '大于等于',
                                'gt'  => '大于',
                                'elt'  => '小于等于',
                                'lt'  => '小于',
                                'filter'  => '使用filter_var进行验证',
                                'regex'  => '正则验证'
                            ],
                            '其他(需要参数)' => [
                                'unique'  => '值是否为唯一',
                                'requireIf'  => '值等于某个值的时候必须',
                                'requireWith'  => '某个字段有值的时候必须',
                                'call' => '自定义，需要自行在模型中定义验证方法'
                            ],
                            '' => '更多请查阅手册“内置规则”手动填写',
                        ],

                    ],
                    'args' => [
                        'label' => '规则参数',
                        'elem' => 'text',
                        'attrs' => [
                            'placeholder' => '多个参数之间,号分割'
                        ]
                    ],
                    'on' => [
                        'label' => '验证场景',
                        'elem' => 'select',
                        'options' => [
                            '0' => '不限',
                            'add '=> '添加',
                            'edit' => '修改'
                        ]
                    ],
                    'message' => [
                        'label' => '错误信息',
                        'elem' => 'text',
                        'attrs' => [
                            'placeholder' => '不填写会有默认的提示'
                        ]
                    ]
                ]
            ],
            'business_validate' => [
                'type' => 'array',
                'name' => '数据验证',
                'elem' => 'multiattrs',
                'list' => 0,
                'fields' => [
                    'rule' => [
                        'label' => '规则',
                        'elem' => 'text',
                        'quick' => [
                            '格式验证类(无需参数)' => [
                                'require' => '必须',
                                'number' => '纯数字',
                                'integer'  => '整数',
                                'float' => '浮点数',
                                'email' => '邮箱',
                                'mobile' => '手机',
                                'idCard' => '身份证',
                                'alpha'  => '纯字母',
                                'chs'  => '汉字',
                                'lower'  => '小写',
                                'upper'  => '大写',
                                'url'  => 'URL',
                                'ip'  => 'IP',
                                'captcha' => '验证码',
                                'dateFormat:Y-m-d' => 'Y-m-d日期',
                                'dateFormat:Y-m-d H:i:s' => 'Y-m-d H:i:s日期时间'
                            ],
                            '长度和区间(需要参数)' => [
                                'in'  => '某个范围',
                                'between'  => '否在某个区间',
                                'length'  => '长度是否在某个范围',
                                'max'  => '最大长度',
                                'min'  => '最小长度'
                            ],
                            '比较(需要参数)' => [
                                'confirm'  => '和另外一个字段的值一致',
                                'different'  => '和另外一个字段的值不一致',
                                'eq'  => '等于',
                                'egt'  => '大于等于',
                                'gt'  => '大于',
                                'elt'  => '小于等于',
                                'lt'  => '小于',
                                'filter'  => '使用filter_var进行验证',
                                'regex'  => '正则验证'
                            ],
                            '其他(需要参数)' => [
                                'unique'  => '值是否为唯一',
                                'requireIf'  => '值等于某个值的时候必须',
                                'requireWith'  => '某个字段有值的时候必须',
                                'call' => '自定义，需要自行在模型中定义验证方法'
                            ],
                            '' => '更多请查阅手册“内置规则”手动填写',
                        ],

                    ],
                    'args' => [
                        'label' => '规则参数',
                        'elem' => 'text',
                        'attrs' => [
                            'placeholder' => '多个参数之间,号分割'
                        ]
                    ],
                    'on' => [
                        'label' => '验证场景',
                        'elem' => 'select',
                        'options' => [
                            '0' => '不限',
                            'add '=> '添加',
                            'edit' => '修改'
                        ]
                    ],
                    'message' => [
                        'label' => '错误信息',
                        'elem' => 'text',
                        'attrs' => [
                            'placeholder' => '不填写会有默认的提示'
                        ]
                    ]
                ]
            ],
            'is_field' => [
                'type' => 'boolean',
                'name' => '真实字段',
                'elem' => 'select',
                'options' => [
                    1 => '数据表字段',
                    0 => '非数据字段'
                ],
                'list' => 'hide',
                'detail' => 'checker',
                'tip' => '不选中表示仅希望表单显示，但非真实数据表字段'
            ],
            'is_business_copy_admin' => [
                'type' => 'boolean',
                'name' => '复制后台配置',
                'elem' => 'checker',
                'tip' => '中台配置自动复制后台的配置，原有配置将被替换',
                'list' => 0,
            ],
            'type' => [
                'type' => 'string',
                'name' => '类型',
                'elem' => 'select',
                'options' => [
                    '数字类型' => [
                        'INT' => 'INT',
                        'TINYINT' => 'TINYINT',
                        'SMALLINT' => 'SMALLINT',
                        'MEDIUMINT' => 'MEDIUMINT',
                        'BIGINT' => 'BIGINT',
                        'DECIMAL' => 'DECIMAL',
                        'FLOAT' => 'FLOAT',
                        'DOUBLE' => 'DOUBLE',
                        'BOOLEAN' => 'BOOLEAN'
                    ],
                    '字符串类型' => [
                        'CHAR' => 'CHAR',
                        'VARCHAR' => 'VARCHAR',
                        'TEXT' => 'TEXT',
                        'MEDIUMTEXT' => 'MEDIUMTEXT',
                        'LONGTEXT' => 'LONGTEXT',
                        'BLOB' => 'BLOB',
                        'ENUM' => 'ENUM',
                        'JSON' => 'JSON(建议mysql8环境下使用)'
                    ],
                    '日期时间' => [
                        'DATE' => 'DATE',
                        'DATETIME' => 'DATETIME',
                        'TIME' => 'TIME',
                        'TIMESTAMP' => 'TIMESTAMP',
                        'YEAR' => 'YEAR'
                    ]
                ],
                'list' => [
                    'title' => '类型/长度/默认值',
                    'templet' => 'merge',
                    'width' => 150,
                    'merge_fields' => [
                        'type' => [
                            'templet' => 'show'
                        ],
                        'length',
                        'default'
                    ]
                ]
            ],
            'length' => [
                'type' => 'string',
                'name' => '长度/值',
                'elem' => 'text',
                'filter' => function ($val) {
                    if (!is_string($val)) {
                        return $val;
                    }
                    $val = trim($val);
                    if (substr($val, 0 , 1) == '(') {
                        $val = substr($val, 1);
                    }
                    if (substr($val, -1, 1) == ')') {
                        $val = substr($val,0, -1);
                    }
                    return $val;
                },
                'list' => 0,
            ],
            'default' => [
                'type' => 'string',
                'name' => '默认值',
                'elem' => 'text',
                'quick' => [
                    'none' => '无',
                    ''     => '空字符串',
                    '0'    => '数字：0',
                    'NULL' => 'NULL',
                    'CURRENT_TIMESTAMP' => 'CURRENT_TIMESTAMP',
                    '1970-01-01' => '1970-01-01',
                    '1970-01-01 00:00:00' => '1970-01-01 00:00:00',
                ],
                'list' => 0
            ],
            'is_not_null' => [
                'type' => 'boolean',
                'name' => 'NOT NULL',
                'elem' => 'checker',
                'list' => 0,
            ],
            'is_unsigned' => [
                'type' => 'boolean',
                'name' => 'UNSIGNED',
                'elem' => 'checker',
                'list' => 0,
            ],
            'is_ai' => [
                'type' => 'boolean',
                'name' => '自动增长',
                'elem' => 'checker',
                'list' => 0,
                'tip的数据已经不存在，修改失败' => '勾选后自动主键，当然数据类型你要选择为整型'
            ],
            'is_system' => [
                'type' => 'boolean',
                'name' => '系统字段',
                'elem' => 'checker',
                'list' => 'checker.show',
                'list' => 'checker'
            ],
            'is_contribute' => [
                'type' => 'boolean',
                'name' => '是否投稿',
                'elem' => 'checker',
                'list' => 'checker'
            ],
            'index' => [
                'type' => 'string',
                'name' => '索引',
                'elem' => 'select',
                'options' => [
                    'unique' => 'UNIQUE',
                    'index' => 'INDEX'
                ],
                'list' => 0
            ],
            'after' => [
                'type' => 'string',
                'name' => '于...之后',
                'elem' => 'text',
                'list' => 0
            ],
            'admin_id' => [
                'type' => 'integer',
                'name' => '管理员ID',
                'elem' => 0,
                'list' => 0
            ],
            'list_order' => [
                'type' => 'integer',
                'name' => '排序权重',
                'elem' => 'number',
                'list' => [
                    'width' => 110
                ]
            ],
            'create_time' => [
                'type' => 'integer',
                'name' => '创建日期',
                'elem' => 0,
                'list' => 0
            ],
            'update_time' => [
                'type' => 'integer',
                'name' => '修改日期',
                'elem' => 0,
                'list' => 0
            ]
        ];

        $config = FormConfig::get('form_item_lists');
        $options['none'] = '无需表单[none]';
        foreach ($config as $elem => $info) {
            $options[$elem] = ($info['name'] ?? $elem) . '[' . $elem .']';
        }

        $this->form['form']['options'] = $options;
        $this->form['modify_form']['options'] = $options;
        $this->form['list']['quick'] = FormConfig::get('list_item_lists');
        $this->form['list_filter']['quick'] = FormConfig::get('filter_item_lists');
        $this->form['detail']['quick'] = FormConfig::get('detail_item_lists');

        $this->form['business_form']['options'] = $options;
        $this->form['business_modify_form']['options'] = $options;
        $this->form['business_list']['quick'] = FormConfig::get('list_item_lists');
        $this->form['business_list_filter']['quick'] = FormConfig::get('filter_item_lists');
        $this->form['business_detail']['quick'] = FormConfig::get('detail_item_lists');

        $this->formTrigger = [
            'form' => [
                'none' => 'form_foreign',
                'relation' => 'form_foreign',
                'relation2' => 'form_foreign',
                'xmtree' => 'form_foreign',
                'xmselectforteee' => 'form_foreign',
                'selectfortree' => 'form_foreign',
                'cascader' => 'form_foreign',
                'transfer' => 'form_foreign',
                'together' => 'form_foreign',
                'together2' => 'form_foreign',
                'orderitem' => 'form_foreign',
                'image' => 'form_upload',
                'file' => 'form_upload',
                'multiimage' => 'form_upload',
                'multifile' => 'form_upload'
            ],
            'business_form' => [
                'none' => 'business_form_foreign',
                'relation' => 'business_form_foreign',
                'relation2' => 'business_form_foreign',
                'xmtree' => 'business_form_foreign',
                'xmselectforteee' => 'business_form_foreign',
                'selectfortree' => 'business_form_foreign',
                'cascader' => 'business_form_foreign',
                'transfer' => 'business_form_foreign',
                'together' => 'business_form_foreign',
                'together2' => 'business_form_foreign',
                'orderitem' => 'business_form_foreign',
                'image' => 'business_form_upload',
                'file' => 'business_form_upload',
                'multiimage' => 'business_form_upload',
                'multifile' => 'business_form_upload'
            ],
            'modify_form' => [
                'none' => 'form_foreign',
                'relation' => 'form_foreign',
                'relation2' => 'form_foreign',
                'xmtree' => 'form_foreign',
                'xmselectforteee' => 'form_foreign',
                'selectfortree' => 'form_foreign',
                'cascader' => 'form_foreign',
                'transfer' => 'form_foreign',
                'together' => 'form_foreign',
                'together2' => 'form_foreign',
                'orderitem' => 'form_foreign',
                'image' => 'form_upload',
                'file' => 'form_upload',
                'multiimage' => 'form_upload',
                'multifile' => 'form_upload'
            ],
            'business_modify_form' => [
                'none' => 'business_form_foreign',
                'relation' => 'business_form_foreign',
                'relation2' => 'business_form_foreign',
                'xmtree' => 'business_form_foreign',
                'xmselectforteee' => 'business_form_foreign',
                'selectfortree' => 'business_form_foreign',
                'cascader' => 'business_form_foreign',
                'transfer' => 'business_form_foreign',
                'together' => 'business_form_foreign',
                'together2' => 'business_form_foreign',
                'orderitem' => 'business_form_foreign',
                'image' => 'business_form_upload',
                'file' => 'business_form_upload',
                'multiimage' => 'business_form_upload',
                'multifile' => 'business_form_upload'
            ],
            'is_field' => [
                '1' => [
                    'type',
                    'length',
                    'default',
                    'is_not_null',
                    'is_unsigned',
                    'is_ai',
                    'index',
                    'after'
                ]
            ]
        ];

        $this->validate = [
            'field' => [
                [
                    'rule' => ['require'],
                ],
                [
                    'rule' => ['unique', 'field,model_id^field']
                ],
                [
                    'rule' => ['regex', '/^[a-z]+[a-z0-9_]*$/i']
                ]
            ],
            'model_id' => [
                'rule' => ['>', 0]
            ],
            'name' => [
                'rule' => 'require'
            ],
            'type' => [
                [
                    'rule' => ['requireIf', 'is_field,1']
                ],
                [
                    'rule' => ['call', 'checkTypeLength']
                ]
            ]
        ];

        parent::{__FUNCTION__}();
    }

    public function checkTypeLength($value, $rule, $data) {
        if (is_null($value)) {
            return true;
        }
        $value = strtolower($value);
        if (empty($this->getData()['is_field'])) {
            return true;
        }
        $data = $this->getData();
        if (in_array($value, ['char', 'varchar', 'decimal', 'enum']) && empty($data['length'])) {
            $this->forceError('length', '数据类型为' . strtoupper($value) . '时，必须定义长度/值');
        }
        if (
            in_array($value, ['text', 'mediumtext', 'longtext', 'blob', 'date', 'datetime', 'time', 'timestamp','boolean', 'json'])
            &&
            !empty($data['length'])
        ) {
            $this->forceError('length', '数据类型为' . strtoupper($value) . '时，不能定义长度/值');
        }

        if (in_array($value, ['int', 'tinyint', 'smallint', 'bigint', 'mediumint']) && $data['default'] != 'none' && $data['default'] != '' && !is_numeric($data['default'])) {

            $this->forceError('default', '数据类型为' . strtoupper($value) . '时，默认值必须是数字');
        }

        if ($data['default'] == 'CURRENT_TIMESTAMP' && $value != 'TIMESTAMP') {
            $this->forceError('default', '数据类型为' . strtoupper($value) . '时，默认值错误');
        }

        return true;
    }

    public function beforeInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (empty($this['is_field']) && $this['list'] === '') {
            $this['list'] = 0;
        }
        return $parent_return;
    }

    public function beforeWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $data = $this->getData();
        if (!empty($data['is_business_copy_admin'])) {
            $this['business_form'] = $data['form'] ?? '';
            $this['business_modify_form'] = $data['modify_form'] ?? '';
            $this['business_form_foreign'] = $data['form_foreign'] ?? '';
            $this['business_form_options'] = $data['form_options'] ?? '';
            $this['business_form_item_attrs'] = $data['form_item_attrs'] ?? '';
            $this['business_form_tag_attrs'] = $data['form_tag_attrs'] ?? '';
            $this['business_form_upload'] = $data['form_upload'] ?? '';
            $this['business_form_trigger'] = $data['form_trigger'] ?? '';
            $this['business_list'] = $data['list'] ?? '';
            $this['business_list_attrs'] = $data['list_attrs'] ?? '';
            $this['business_detail'] = $data['detail'] ?? '';
            $this['business_detail_attrs'] = $data['detail_attrs'] ?? '';
            $this['business_list_filter'] = $data['list_filter'] ?? '';
            $this['business_list_filter_attrs'] = $data['list_filter_attrs'] ?? '';
            $this['business_list_filter_tag_attrs'] = $data['list_filter_tag_attrs'] ?? '';
            $this['business_validate'] = $data['validate'] ?? '';
        }

        return $parent_return;
    }

    public function afterInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!Env::get('APP_DEBUG')) {
            return $parent_return;
        }

        $data = $this->getData();

        if (empty($data['is_field']) || !empty($data['is_exists'])) {
            return $parent_return;
        }
        $model  = model('Model')->where('id', '=', $data['model_id'])->find();
        Cache::tag(($model['addon'] ? $model['addon'] . '.' : '') . $model['model'])->clear();

        $table_name = empty($model['full_table']) ?
            get_db_config('prefix', $model['connection'] ?? '') . ($model['addon'] ? Str::snake($model['addon']) . '_' : '') . Str::snake($model['model'])
            :$model['full_table'];

        $sql = sprintf(
            "ALTER TABLE `%s` ADD `%s` %s ",
            $table_name,
            $data['field'],
            $data['type']
        );

        if ($data['length']) {
            $sql .= "({$data['length']}) ";
        }
        if (in_array(strtoupper($data['type']), ['TINYINT','SMALLINT','MEDIUMINT','INT','BIGINT']) && !empty($data['is_unsigned'])) {
            $sql .= "UNSIGNED ";
        }
        $sql .= $data['is_not_null'] && $data['default'] != 'NULL' ? 'NOT NULL ' : "NULL ";
        if ($data['default'] != 'none') {
            if ($data['type'] != 'TIMESTAMP' && $data['default'] != 'NULL') {
                $sql .= "DEFAULT '{$data['default']}' ";
            } elseif ($data['default'] == 'NULL') {
                $sql .= "DEFAULT {$data['default']} ";
            } elseif ($data['type'] == 'TIMESTAMP') {
                $sql .= "DEFAULT CURRENT_TIMESTAMP ";
            }
        }

        if ($data['is_ai'] && in_array(strtoupper($data['type']), ['TINYINT','SMALLINT','MEDIUMINT','INT','BIGINT'])) {
            $sql .= 'AUTO_INCREMENT ';
            $ispk = true;
        }

        $sql .= "COMMENT '{$data['name']}' ";
        if (!empty($data['after'])) {
            $sql .= "AFTER `{$data['after']}`";
        }

        if (empty($ispk) && $data['index']) {
            $sql .= ", ADD {$data['index']} (`{$data['field']}`)";
        } elseif (!empty($ispk)) {
            $sql .= ",  ADD PRIMARY KEY (`{$data['field']}`)";
        }

        try {
            Db::connect($model['connection'] ?? '')->execute($sql);
            Log::write("SQL:[{$sql}]", 'info');
        } catch (\Exception $e) {
            Db::name($this->name)->delete($data[$this->getPk()]);
            Log::write("SQL:[{$sql}]，错误：" . $e->getMessage(), 'error');
            throw new \Exception($e->getMessage());
        }
        return $parent_return;
    }

    public function setTogetherItem($data)
    {
        $data['is_together'] = 1;
        return $data;
    }

    public function  afterUpdateCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!Env::get('APP_DEBUG')) {
            return $parent_return;
        }
        $data = $this->getData();
        if (empty($data['is_field'])) {
            return $parent_return;
        }
        $origin = $this->getOrigin();
        if (!empty($data['is_together']) || empty($origin)) {
            return $parent_return;
        }
        $model  = model('Model')->where('id', '=', $data['model_id'])->find();
        Cache::tag(($model['addon'] ? $model['addon'] . '.' : '') . $model['model'])->clear();

        $table_name = empty($model['full_table']) ?
            get_db_config('prefix', $model['connection'] ?? '') . ($model['addon'] ? Str::snake($model['addon']) . '_' : '') . Str::snake($model['model'])
            :$model['full_table'];

        $sql = sprintf(
            "ALTER TABLE `%s` CHANGE `%s` `%s` %s ",
            $table_name,
            $origin['field'],
            $data['field'],
            $data['type']
        );
        if ($data['length']) {
            $sql .= "({$data['length']}) ";
        }
        if (in_array(strtoupper($data['type']), ['TINYINT','SMALLINT','MEDIUMINT','INT','BIGINT']) && !empty($data['is_unsigned'])) {
            $sql .= "UNSIGNED ";
        }
        $sql .= $data['is_not_null'] && $data['default'] != 'NULL' ? 'NOT NULL ' : "NULL ";
        if ($data['default'] != 'none') {
            if ($data['type'] != 'TIMESTAMP' && $data['default'] != 'NULL') {
                $sql .= "DEFAULT '{$data['default']}' ";
            } elseif ($data['default'] == 'NULL') {
                $sql .= "DEFAULT {$data['default']} ";
            } elseif ($data['type'] == 'TIMESTAMP') {
                $sql .= "DEFAULT CURRENT_TIMESTAMP ";
            }
        }

        if ($data['is_ai']) {
            $sql .= 'AUTO_INCREMENT ';
        }

        $sql .= "COMMENT '{$data['name']}' ";

        if (!empty($data['after'])) {
            $sql .= "AFTER `{$data['after']}` ";
        }

        if (!$origin['index'] && $data['index']) {
            $sql .= ", ADD {$data['index']} (`{$data['field']}`)";
        } elseif ($origin['index'] &&  !$data['index']) {
            $sql .= ", DROP INDEX `{$origin['field']}`";
        } elseif ($origin['field'] != $data['field'] && $data['index']) {
            $sql .= ", DROP INDEX `{$origin['field']}`";
            $sql .= ", ADD {$data['index']} (`{$data['field']}`)";
        } elseif ($origin['index'] && $data['index'] && $origin['index'] != $data['index']) {
            $sql .= ", DROP INDEX `{$origin['field']}`";
            Db::name($this->name)->where('id', '=', $data['id'])->update(['index' => '']);
        }

        try {
            Db::connect($model['connection'] ?? '')->execute($sql);
            Log::write("SQL:[{$sql}]", 'info');
        } catch (\Exception $e) {
            Log::write("SQL:[{$sql}]，错误：" . $e->getMessage(), 'error');
            throw new \Exception($e->getMessage());
        }
        return $parent_return;
    }

    public function afterWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $data = $this->getData();
        if (!empty($data['model_id']) && empty($this['is_not_create_file'])) {
            try {
                $path = (new CreateFile)->createModel($data['model_id']);
            } catch (\Exception $e) {

            }
        }
        return $parent_return;
    }

    public function afterDeleteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!Env::get('APP_DEBUG')) {
            return $parent_return;
        }
        $data = $this->getData();
        if (empty($data['is_field'])) {
            return $parent_return;
        }
        $model  = model('Model')->where('id', '=', $data['model_id'])->find();
        if (empty($model)) {
            return $parent_return;
        }

        $table_name = empty($model['full_table']) ?
            get_db_config('prefix', $model['connection'] ?? '') . ($model['addon'] ? Str::snake($model['addon']) . '_' : '') . Str::snake($model['model'])
            :$model['full_table'];

        $sql = sprintf(
            " ALTER TABLE `%s` DROP `%s`",
            $table_name,
            $data['field']
        );
        try {
            Db::connect($model['connection'] ?? '')->execute($sql);
            Log::write("SQL:[{$sql}]", 'info');
            if (!empty($data['model_id']) && empty($this['is_not_create_file'])) {
                $path = (new CreateFile)->createModel($data['model_id']);
            }
        } catch (\Exception $e) {
            Log::write("SQL:[{$sql}]，错误：" . $e->getMessage(), 'error');
            throw new \Exception($e->getMessage());
        }
        return $parent_return;
    }


}