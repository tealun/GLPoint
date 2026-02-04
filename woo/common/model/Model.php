<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use think\facade\Cache;
use think\facade\Config;
use woo\common\builder\form\FormConfig;
use woo\common\facade\Auth;
use woo\common\helper\Arr;
use woo\common\helper\CreateFile;
use woo\common\helper\Str;
use think\facade\Db;
use think\facade\Log;

class Model extends App
{

    public $cname = '模型';
    public $display = 'cname';
    public $relationLink = array(
        'Field' => [
            'type' => 'hasMany'
        ]
    );

    public $orderType = 'desc';

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
            'cname' => [
                'type' => 'string',
                'name' => '名称',
                'elem' => 'text',
                'filter' => 'trim',
                'require' => true,
                'list_filter' => true,
                'list' => [
                    'minWidth' => 140
                ],
                'attrs' => [
                    'placeholder' => '填写模型名称，如：文章'
                ]
            ],
            'addon' => [
                'type' => 'string',
                'name' => '二级目录',
                'elem' => 'text',
                'filter' => 'trim',
                'list_filter' => true,
                'list' => [
                    // 'hide' => true,
                    'minWidth' => 110,
                    'sort' => true
                ]
            ],
            'is_controller' => [
                'type' => 'integer',
                'name' => '后台控制器',
                'elem' => 'checker',
                'list' => 0,
                'tip' => '如果拥有二级目录需单独自行创建控制器'
            ],
            'is_business_controller' => [
                'type' => 'integer',
                'name' => '中台控制器',
                'elem' => 'checker',
                'list' => 0,
                'tip' => '如果拥有二级目录需单独自行创建控制器'
            ],
            'model' => [
                'type' => 'string',
                'name' => '类名',
                'elem' => 'text',
                'tip' => '如：AdPosition，大驼峰命名',
                'require' => true,
                'filter' => function($val) {
                    return Str::studly(trim($val));
                },
                'list' => [
                    'templet' => 'show.blue',
                    'minWidth' => 170,
                    'sort' => true
                ],
                'list_filter' => true,
                'attrs' => [
                    'placeholder' => '填写模型类名，如：Article'
                ]
            ],

            'order_type' => [
                'type' => 'string',
                'name' => '默认排序',
                'elem' => 'select',
                'options' => [
                    'desc' => '倒序',
                    'asc' => '正序'
                ],
                'list' => 0
            ],
            'tree_level' => [
                'type' => 'integer',
                'name' => '无限极',
                'elem' => 'number',
                'tip' => '无限极必须有parent_id字段，不是无限极保持为0',
                'list' => 0
            ],

            'full_table' => [
                'type' => 'string',
                'name' => '完整表名',
                'elem' => 'text',
                'filter' => 'trim',
                'tip' => '不建议自行定义，按规则来由程序自动定义',
                'list' => 0
            ],

            'suffix' => [
                'type' => 'string',
                'name' => '表后缀',
                'elem' => 'text',
                'filter' => 'trim',
                'tip' => '一般不填写，默认为空，特殊情况才会需要',
                'list' => 0
            ],
            'pk' => [
                'type' => 'string',
                'name' => '主键名',
                'elem' => 'text',
                'filter' => 'trim',
                'tip' => '强烈建议不定义，保持为空，由程序默认使用`id`主键名',
                'list' => 0
            ],
            'connection' => [
                'type' => 'string',
                'name' => '数据库连接',
                'elem' => 'select',
                'filter' => 'trim',
            ],
            'parent_model' => [
                'type' => 'string',
                'name' => '父模型名',
                'elem' => 'text',
                'list' => 0
            ],
            'display' => [
                'type' => 'string',
                'name' => '主显字段名',
                'elem' => 'text',
                'list' => 0,
                'tip' => '建议填写，用于关联、选择的显示字段；默认主键',
                'attrs' => [
                    'placeholder' => '填写主显字段，如：title、username、name'
                ]
            ],
            'form_group' => [
                'type' => 'array',
                'name' => '表单分组名',
                'elem' => 'keyvalue',
                'list' => 0,
            ],
            'custom_data' => [
                'type' => 'array',
                'name' => '自定义数据',
                'elem' => 'keyvalue',
                'list' => 0
            ],
            'list_config' => [
                'type' => 'string',
                'name' => '列表配置',
                'elem' => 'xmselect',
                'attrs' => [
                    'data-max' => 20
                ],
                'options' => [
                    'create' => '新增按钮',
                    'batch_delete' => '批量删除按钮',
                    'sortable' => '列表拖拽排序',
                    'modify' => '编辑按钮',
                    'delete' => '删除按钮',
                    'detail' => '详情按钮',
                    'copy' => '复制按钮',
                    'delete_index' => '回收站'
                ],
                'list' => 0
            ],
            'business_list_config' => [
                'type' => 'string',
                'name' => '列表配置',
                'elem' => 'xmselect',
                'attrs' => [
                    'data-max' => 20
                ],
                'options' => [
                    'create' => '新增按钮',
                    'batch_delete' => '批量删除按钮',
                    'sortable' => '列表拖拽排序',
                    'modify' => '编辑按钮',
                    'delete' => '删除按钮',
                    'detail' => '详情按钮',
                    'copy' => '复制按钮',
                    'delete_index' => '回收站'
                ],
                'list' => 0
            ],
            'relation_link' => [
                'type' => 'array',
                'name' => '关联模型',
                'elem' => 'multiattrs',
                'message' => '关联字段名默认：<b>关联键名小写+下划线_id</b>，不是按这个规范的必须定义"foreignKey"；<br>带二级目录的模型，请务必指明模型名，如：erp.Product<br>当前模型有多个字段关联同一个模型，可以定义不同关联键名，模型名相同<br>hasOne, hasMany, belongsToMany可定义"deleteWith"实现当前模型数据删除以后关联模型数据自动删除<br>多对多关联必须定义"middle"中间表模型名.....还有更多功能请查阅文档',
                'fields' => [
                    'key' => [
                        'label' => '关联键名',
                        'elem' => 'text',
                        "tip" => "关联标识，一般用模型名为键名，大驼峰，不要带.",
                        "attrs" => [
                            'placeholder' => "必须填写,大驼峰"
                        ]
                    ],
                    'foreign' => [
                        'label' => '模型名',
                        'elem' => 'text',
                        'tip' => '一般不用填写，默认使用键名；有二级目录的模型必须填写',

                    ],
                    'type' => [
                        'label' => '关联类型',
                        'elem' => 'select',
                        'options' => [
                            'hasOne' => 'hasOne【一对一】',
                            'hasMany' => 'hasMany【一对多】',
                            'belongsTo' => 'belongsTo【反向一对一】',
                            'belongsToMany' => 'belongsToMany【多对多】',
                            'hasOneThrough' => 'hasOneThrough【远程一对一】',
                            'belongsToThrough' => 'belongsToThrough【远程反向一对一】',
                            'hasManyThrough' => 'hasManyThrough【远程一对多】'
                        ]
                    ],
                    'foreign_key' => [
                        'label' => '外键名',
                        'elem' => 'text',
                        'attrs' => [
                            'placeholder' => '不填写会自动识别'
                        ]
                    ],
                    'more' => [
                        'label' => '更多属性',
                        'elem' => 'keyvalue',
                        'width' => '25%'
                    ],
                ],
                'list' => 0
            ],
            'is_business_copy_admin' => [
                'type' => 'boolean',
                'name' => '复制后台配置',
                'elem' => 'checker',
                'tip' => '中台配置自动复制后台的配置，原有配置将被替换',
                'list' => 0,
            ],
            'admin_tool_bar' => [
                'type' => 'array',
                'name' => '头部工具',
                'elem' => 'multiattrs',
                'message' => '这里添加自定义工具按钮，系统自带按钮请"基本信息-列表配置"勾选即可',
                'fields' => [
                    'name' => [
                        'label' => '标识',
                        'elem' => 'text',
                        'tip' => '请填写唯一标主键/序号显示识'
                    ],
                    'title' => [
                        'label' => '名称',
                        'elem' => 'text',
                    ],
                    'class' => [
                        'label' => '类名',
                        'elem' => 'text',
                    ],
                    'icon' => [
                        'label' => '图标',
                        'elem' => 'icon',
                    ],
                    'sort' => [
                        'label' => '排序',
                        'elem' => 'number',
                        'default' => 0
                    ],
                    'js_func' => [
                        'label' => 'JS函数',
                        'elem' => 'text',
                    ],
                    'url' => [
                        'label' => 'URL地址',
                        'elem' => 'text',
                        'width' => '14%',
                        'tip' => '只支持js模板语法'
                    ],
                    'templet' => [
                        'label' => '自定义模板',
                        'elem' => 'text',
                        'tip' => '需要自行定义模型'
                    ],
                    'hover' => [
                        'label' => 'hover名称',
                        'elem' => 'text',
                        'tip' => '鼠标移入显示文字'
                    ],
                    'check' => [
                        'label' => '是否check',
                        'elem' => 'checker',
                        'tip' => '是否监听项选择切换禁用状态'
                    ],
                    'length' => [
                        'label' => '字符数',
                        'elem' => 'number',
                        'default' => 0,
                        'tip' => '针对特殊情况title名称不能正确计算字符长度时，单独指定'
                    ],
                    'power' => [
                        'label' => '权限标识',
                        'elem' => 'text',
                    ],
                    'parent' => [
                        'label' => '父标识',
                        'elem' => 'text',
                        'tip' => '二级按钮才填写'
                    ],
                    'attrs' => [
                        'label' => '标签属性',
                        'elem' => 'keyvalue'
                    ]
                ],
                'list' => 0,
            ],
            'business_tool_bar' => [
                'type' => 'array',
                'name' => '头部工具',
                'elem' => 'multiattrs',
                'message' => '这里添加自定义工具按钮，系统自带按钮请"基本信息-列表配置"勾选即可',
                'fields' => [
                    'name' => [
                        'label' => '标识',
                        'elem' => 'text',
                        'tip' => '请填写唯一标主键/序号显示识'
                    ],
                    'title' => [
                        'label' => '名称',
                        'elem' => 'text',
                    ],
                    'class' => [
                        'label' => '类名',
                        'elem' => 'text',
                    ],
                    'icon' => [
                        'label' => '图标',
                        'elem' => 'icon',
                    ],
                    'sort' => [
                        'label' => '排序',
                        'elem' => 'number',
                        'default' => 0
                    ],
                    'js_func' => [
                        'label' => 'JS函数',
                        'elem' => 'text',
                    ],
                    'url' => [
                        'label' => 'URL地址',
                        'elem' => 'text',
                        'width' => '14%',
                        'tip' => '只支持js模板语法'
                    ],
                    'templet' => [
                        'label' => '自定义模板',
                        'elem' => 'text',
                        'tip' => '需要自行定义模型'
                    ],
                    'hover' => [
                        'label' => 'hover名称',
                        'elem' => 'text',
                        'tip' => '鼠标移入显示文字'
                    ],
                    'check' => [
                        'label' => '是否check',
                        'elem' => 'checker',
                        'tip' => '是否监听项选择切换禁用状态'
                    ],
                    'length' => [
                        'label' => '字符数',
                        'elem' => 'number',
                        'default' => 0,
                        'tip' => '针对特殊情况title名称不能正确计算字符长度时，单独指定'
                    ],
                    'power' => [
                        'label' => '权限标识',
                        'elem' => 'text',
                    ],
                    'parent' => [
                        'label' => '父标识',
                        'elem' => 'text',
                        'tip' => '二级按钮才填写'
                    ],
                    'attrs' => [
                        'label' => '标签属性',
                        'elem' => 'keyvalue'
                    ]
                ],
                'list' => 0,
            ],
            'admin_item_tool_bar' => [
                'type' => 'array',
                'name' => '列表项工具',
                'elem' => 'multiattrs',
                'fields' => [
                    'name' => [
                        'label' => '标识',
                        'elem' => 'text',
                        'tip' => '请填写唯一标识'
                    ],
                    'title' => [
                        'label' => '名称',
                        'elem' => 'text',
                    ],
                    'class' => [
                        'label' => '类名',
                        'elem' => 'text',
                    ],
                    'icon' => [
                        'label' => '图标',
                        'elem' => 'icon',
                    ],
                    'sort' => [
                        'label' => '排序',
                        'elem' => 'number',
                        'default' => 0
                    ],
                    'js_func' => [
                        'label' => 'JS函数',
                        'elem' => 'text',
                    ],
                    'url' => [
                        'label' => 'URL地址',
                        'elem' => 'text',
                        'tip' => '只支持js模板语法'
                    ],
                    'templet' => [
                        'label' => '自定义模板',
                        'elem' => 'text',
                        'tip' => '需要自行定义模型'
                    ],
                    'hover' => [
                        'label' => 'hover名称',
                        'elem' => 'text',
                        'tip' => '鼠标移入显示文字'
                    ],
                    'where' => [
                        'label' => '渲染条件',
                        'elem' => 'text',
                    ],
                    'where_type' => [
                        'label' => '条件方式',
                        'elem' => 'select',
                        'options' => ['disabled' => '禁用', 'hidden' => '隐藏'],
                        'tip' => '不满条件的按钮处理方式'
                    ],
                    'length' => [
                        'label' => '字符数',
                        'elem' => 'number',
                        'default' => 0,
                        'tip' => '针对特殊情况title名称不能正确计算字符长度时，单独指定'
                    ],
                    'power' => [
                        'label' => '权限标识',
                        'elem' => 'text',
                    ],
                    'parent' => [
                        'label' => '父标识',
                        'elem' => 'text',
                        'tip' => '二级按钮才填写'
                    ],
                    'attrs' => [
                        'label' => '标签属性',
                        'elem' => 'keyvalue'
                    ]
                ],
                'list' => 0,
            ],
            'business_item_tool_bar' => [
                'type' => 'array',
                'name' => '列表项工具',
                'elem' => 'multiattrs',
                'fields' => [
                    'name' => [
                        'label' => '标识',
                        'elem' => 'text',
                        'tip' => '请填写唯一标识'
                    ],
                    'title' => [
                        'label' => '名称',
                        'elem' => 'text',
                    ],
                    'class' => [
                        'label' => '类名',
                        'elem' => 'text',
                    ],
                    'icon' => [
                        'label' => '图标',
                        'elem' => 'icon',
                    ],
                    'sort' => [
                        'label' => '排序',
                        'elem' => 'number',
                        'default' => 0
                    ],
                    'js_func' => [
                        'label' => 'JS函数',
                        'elem' => 'text',
                    ],
                    'url' => [
                        'label' => 'URL地址',
                        'elem' => 'text',
                        'tip' => '只支持js模板语法'
                    ],
                    'templet' => [
                        'label' => '自定义模板',
                        'elem' => 'text',
                        'tip' => '需要自行定义模型'
                    ],
                    'hover' => [
                        'label' => 'hover名称',
                        'elem' => 'text',
                        'tip' => '鼠标移入显示文字'
                    ],
                    'where' => [
                        'label' => '渲染条件',
                        'elem' => 'text',
                    ],
                    'where_type' => [
                        'label' => '条件方式',
                        'elem' => 'select',
                        'options' => ['disabled' => '禁用', 'hidden' => '隐藏'],
                        'tip' => '不满条件的按钮处理方式'
                    ],
                    'length' => [
                        'label' => '字符数',
                        'elem' => 'number',
                        'default' => 0,
                        'tip' => '针对特殊情况title名称不能正确计算字符长度时，单独指定'
                    ],
                    'power' => [
                        'label' => '权限标识',
                        'elem' => 'text',
                    ],
                    'parent' => [
                        'label' => '父标识',
                        'elem' => 'text',
                        'tip' => '二级按钮才填写'
                    ],
                    'attrs' => [
                        'label' => '标签属性',
                        'elem' => 'keyvalue'
                    ]
                ],
                'list' => 0,
            ],
            'admin_siderbar' => [
                'type' => 'array',
                'name' => '侧边栏模型',
                'elem' => 'array',
                'list' => 0,
                'message' => '模型名需要自行在"关联模型中"配置，这里只支持配置模型名；如需自定义更多属性请手动完成代码；关联字段不能被设置为搜索字段，否则侧边栏不显示'
            ],
            'business_siderbar' => [
                'type' => 'array',
                'name' => '侧边栏模型',
                'elem' => 'array',
                'list' => 0,
                'message' => '模型名需要自行在"关联模型中"配置，这里只支持配置模型名；如需自定义更多属性请手动完成代码；关联字段不能被设置为搜索字段，否则侧边栏不显示'
            ],
            'admin_table_attrs' => [
                'type' => 'array',
                'name' => '数据表格table属性',
                'elem' => 'json',
                'list' => 0,
                'message' => '定义表格"table"属性，具体参数参考"<a href="https://layui.dev/docs/table/#options" target="_blank">layui数据表格-基础参数一览表</a>"'
            ],
            'business_table_attrs' => [
                'type' => 'array',
                'name' => '数据表格table属性',
                'elem' => 'json',
                'list' => 0,
                'message' => '定义表格"table"属性，具体参数参考"<a href="https://layui.dev/docs/table/#options" target="_blank">layui数据表格-基础参数一览表</a>"'
            ],

            'admin_item_checkbox' => [
                'type' => 'string',
                'name' => '左侧选择框',
                'elem' => 'select',
                'tip' => '自定义列表模板选择无效且只支持多选框',
                'options' => [
                    'false' => '不显示',
                    'checkbox' => '多选框',
                    'radio' => '单选框'
                ],
                'default' => 'checkbox',
                'list' => 0,
            ],
            'business_item_checkbox' => [
                'type' => 'string',
                'name' => '左侧选择框',
                'elem' => 'select',
                'tip' => '自定义列表模板选择无效且只支持多选框',
                'options' => [
                    'false' => '不显示',
                    'checkbox' => '多选框',
                    'radio' => '单选框'
                ],
                'default' => 'checkbox',
                'list' => 0,
            ],
            'admin_is_remove_pk' => [
                'type' => 'integer',
                'name' => '主键/序号显示',
                'elem' => 'select',
                'options' => [
                    0 => '只显示主键(默认)',
                    1 => '主键、序号均显示',
                    2 => '只显示序号',
                    3 => '主键、序号均不显示'
                ],
                'list' => 0
            ],
            'business_is_remove_pk' => [
                'type' => 'integer',
                'name' => '主键/序号显示',
                'elem' => 'select',
                'options' => [
                    0 => '只显示主键(默认)',
                    1 => '主键、序号均显示',
                    2 => '只显示序号',
                    3 => '主键、序号均不显示'
                ],
                'list' => 0
            ],
            'admin_filter_model' => [
                'type' => 'string',
                'name' => '列表搜索展现方式',
                'elem' => 'select',
                'list' => 0,
                'options' => [
                    'hidden' => '默认隐藏',
                    'show' => '默认展开',
                    'onlyshow' => '展开不可关闭',
                    'remove' => '移除搜索'
                ]
            ],
            'business_filter_model' => [
                'type' => 'string',
                'name' => '列表搜索展现方式',
                'elem' => 'select',
                'list' => 0,
                'options' => [
                    'hidden' => '默认隐藏',
                    'show' => '默认展开',
                    'onlyshow' => '展开不可关闭',
                    'remove' => '移除搜索'
                ]
            ],
            'admin_item_toolbar_options' => [
                'type' => 'array',
                'name' => '列表项工具属性',
                'elem' => 'multiattrs',
                'fields' => [
                    'is_show' => [
                        'label' => '是否显示',
                        'elem' => 'checker',
                        'width' => 120
                    ],
                    'title' => [
                        'label' => '标题',
                        'elem' => 'text'
                    ],
                    'fixed' => [
                        'label' => '固定',
                        'elem' => 'select',
                        'options' => [
                            'none' => '不固定',
                            'right' => '右边',
                            'left' => '左边'
                        ]
                    ],
                    'min_width' => [
                        'label' => '最小宽度',
                        'elem' => 'number',
                        'tip' => '为0自动计算'
                    ],
                    'align' => [
                        'label' => '位置',
                        'elem' => 'select',
                        'options' => [
                            'left' => '居左',
                            'center' => '居中',
                            'right' => '居右'
                        ]
                    ],
                    'more' => [
                        'label' => '更多属性',
                        'elem' => 'keyvalue',
                        'width' => '25%'
                    ],
                ],
                'multiattrs' => [
                    'max' => 1,
                    'cancel_insert' => true,
                    'cancel_create' => true,
                    'cancel_clear' => true,
                    'cancel_delete' => true,
                ],
                'default' => '[{"is_show":"1","title":"操作","fixed":"right","min_width":"0","align":"center"}]',
                'list' => 0,
            ],
            'business_item_toolbar_options' => [
                'type' => 'array',
                'name' => '列表项工具属性',
                'elem' => 'multiattrs',
                'fields' => [
                    'is_show' => [
                        'label' => '是否显示',
                        'elem' => 'checker',
                        'width' => 120
                    ],
                    'title' => [
                        'label' => '标题',
                        'elem' => 'text'
                    ],
                    'fixed' => [
                        'label' => '固定',
                        'elem' => 'select',
                        'options' => [
                            'none' => '不固定',
                            'right' => '右边',
                            'left' => '左边'
                        ]
                    ],
                    'min_width' => [
                        'label' => '最小宽度',
                        'elem' => 'number',
                        'tip' => '为0自动计算'
                    ],
                    'align' => [
                        'label' => '位置',
                        'elem' => 'select',
                        'options' => [
                            'left' => '居左',
                            'center' => '居中',
                            'right' => '居右'
                        ]
                    ],
                    'more' => [
                        'label' => '更多属性',
                        'elem' => 'keyvalue',
                        'width' => '25%'
                    ],
                ],
                'multiattrs' => [
                    'max' => 1,
                    'cancel_insert' => true,
                    'cancel_create' => true,
                    'cancel_clear' => true,
                    'cancel_delete' => true,
                ],
                'default' => '[{"is_show":"1","title":"操作","fixed":"right","min_width":"0","align":"center"}]',
                'list' => 0,
            ],
            'admin_counter' => [
                'type' => 'array',
                'name' => '列表上方综合基础统计',
                'elem' => 'multiattrs',
                'fields' => [
                    'field' => [
                        'label' => '统计字段',
                        'elem' => 'text',
                        'width' => 145
                    ],
                    'title' => [
                        'label' => '名称',
                        'elem' => 'text',
                        'tip'  => '如果为空自动获取字段名',
                        'width' => 135
                    ],
                    'type' => [
                        'label' => '统计函数',
                        'elem' => 'select',
                        'options' => [
                            'min' => '最小值 [min]',
                            'max' => '最大值 [max]',
                            'count' => '计数 [count]',
                            'avg' => '平均值 [avg]',
                            'sum' => '求和 [sum]',
                        ],
                        'width' => 125
                    ],
                    'where_type' => [
                        'label' => '条件方式',
                        'elem' => 'select',
                        'options' => [
                            'none' => '无条件',
                            'auto' => '自动获取列表搜索条件',
                            'where' => '自定义条件',
                            'callback' => '自定义回调统计'
                        ],
                        'width' => 135
                    ],
                    'where' => [
                        'label' => '自定义条件',
                        'elem' => 'text',
                        'tip'  => '请填写二维数组条件的json格式',
                    ],
                    'callback' => [
                        'label' => '自定义回调方法',
                        'elem' => 'text',
                        'tip'  => '支持传入模型方法名或代码中自定义闭包函数',
                    ],
                    'templet' => [
                        'label' => '自定义模板',
                        'elem' => 'text',
                    ],
                    'more' => [
                        'label' => '更多参数',
                        'elem' => 'keyvalue',
                    ],
                ],
                'list' => 0,
            ],
            'business_counter' => [
                'type' => 'array',
                'name' => '列表上方综合基础统计',
                'elem' => 'multiattrs',
                'fields' => [
                    'field' => [
                        'label' => '统计字段',
                        'elem' => 'text',
                        'width' => 145
                    ],
                    'title' => [
                        'label' => '名称',
                        'elem' => 'text',
                        'tip'  => '如果为空自动获取字段名',
                        'width' => 135
                    ],
                    'type' => [
                        'label' => '统计函数',
                        'elem' => 'select',
                        'options' => [
                            'min' => '最小值 [min]',
                            'max' => '最大值 [max]',
                            'count' => '计数 [count]',
                            'avg' => '平均值 [avg]',
                            'sum' => '求和 [sum]',
                        ],
                        'width' => 125
                    ],
                    'where_type' => [
                        'label' => '条件方式',
                        'elem' => 'select',
                        'options' => [
                            'none' => '无条件',
                            'auto' => '自动获取列表搜索条件',
                            'where' => '自定义条件',
                            'callback' => '自定义回调统计'
                        ],
                        'width' => 135
                    ],
                    'where' => [
                        'label' => '自定义条件',
                        'elem' => 'text',
                        'tip'  => '请填写二维数组条件的json格式',
                    ],
                    'callback' => [
                        'label' => '自定义回调方法',
                        'elem' => 'text',
                        'tip'  => '支持传入模型方法名或代码中自定义闭包函数',
                    ],
                    'templet' => [
                        'label' => '自定义模板',
                        'elem' => 'text',
                    ],
                    'more' => [
                        'label' => '更多参数',
                        'elem' => 'keyvalue',
                    ],
                ],
                'list' => 0,
            ],
            'admin_total_row' => [
                'type' => 'array',
                'name' => '列表底部表格列合计',
                'elem' => 'multiattrs',
                'fields' => [
                    'field' => [
                        'label' => '统计字段',
                        'elem' => 'text',
                        'width' => 145,
                        'tip' => '必须是列表有的字段，否则不能输出'
                    ],
                    'row_text' => [
                        'label' => '合计头文本',
                        'tip' => '一般第一个字段设置，只有合计方式为"无"时有效',
                        'elem' => 'text',
                        'width' => 135
                    ],
                    'total_row' => [
                        'label' => '合计方式',
                        'elem' => 'select',
                        'options' => [
                            'none' => '无',
                            'default' => '默认',
                            'sum' => '求和',
                            'count' => '计算',
                            'avg' => '平均值',
                            'max' => '最大值',
                            'min' => '最小值',
                            'callback' => '自定义回调'
                        ]
                    ],
                    'callback' => [
                        'label' => '回调模型方法',
                        'elem' => 'text',
                        'tip'  => '合计方式为"自定义回调"时有效',
                    ],
                    'templet' => [
                        'label' => '自定义渲染格式',
                        'tip' => '支持layui模板语法，"d.TOTAL_NUMS"获取统计数据',
                        'elem' => 'text',
                    ],
                ],
                'list' => 0
            ],
            'business_total_row' => [
                'type' => 'array',
                'name' => '列表底部表格列合计',
                'elem' => 'multiattrs',
                'fields' => [
                    'field' => [
                        'label' => '统计字段',
                        'elem' => 'text',
                        'width' => 145,
                        'tip' => '必须是列表有的字段，否则不能输出'
                    ],
                    'row_text' => [
                        'label' => '合计头文本',
                        'tip' => '一般第一个字段设置，只有合计方式为"无"时有效',
                        'elem' => 'text',
                        'width' => 135
                    ],
                    'total_row' => [
                        'label' => '合计方式',
                        'elem' => 'select',
                        'options' => [
                            'none' => '无',
                            'default' => '默认',
                            'sum' => '求和',
                            'count' => '计算',
                            'avg' => '平均值',
                            'max' => '最大值',
                            'min' => '最小值',
                            'callback' => '自定义回调'
                        ]
                    ],
                    'callback' => [
                        'label' => '回调模型方法',
                        'elem' => 'text',
                        'tip'  => '合计方式为"自定义回调"时有效',
                    ],
                    'templet' => [
                        'label' => '自定义渲染格式',
                        'tip' => '支持layui模板语法，"d.TOTAL_NUMS"获取统计数据',
                        'elem' => 'text',
                    ],
                ],
                'list' => 0
            ],
            'admin_list_with' => [
                'type' => 'array',
                'name' => '关联模型键名',
                'elem' => 'keyvalue',
                'list' => 0,
                'message' => '必须是关联中定义的键，强制定义列表关联模型，只会响应关联模型数据，具体如何显示需自行列表项处理；一般在key中关联键名即可，具体参考教程'
            ],
            'business_list_with' => [
                'type' => 'array',
                'name' => '关联模型键名',
                'elem' => 'keyvalue',
                'list' => 0,
                'message' => '必须是关联中定义的键，强制定义列表关联模型，只会响应关联模型数据，具体如何显示需自行列表项处理；一般在key中关联键名即可，具体参考教程'
            ],
            'admin_list_fields' => [
                'type' => 'array',
                'name' => '列表字段',
                'elem' => 'multiattrs',
                'fields' => [
                    'field' => [
                        'elem' => 'text',
                        'label' => '字段名',
                        'width' => 200
                    ],
                    'title' => [
                        'elem' => 'text',
                        'label' => '列头名称',
                        'tip' => '真实字段不填，自动获取',
                        'width' => 150
                    ],
                    'templet' => [
                        'elem' => 'text',
                        'label' => '展示模板',

                        'tip' => '自定义html结构，必须用div包住'
                    ],
                    'attr' => [
                        'elem' => 'keyvalue',
                        'label' => '列表字段属性',
                        'width' => 340,
                    ]
                ],
                'message' => '一般不用单独设置，会自动获取字段的列表属性自动渲染；一旦配置该处配置优先；真实字段一般只选择字段名即可，其他数据也会自动从字段中读取',
                'list' => 0
            ],
            'business_list_fields' => [
                'type' => 'array',
                'name' => '列表字段',
                'elem' => 'multiattrs',
                'fields' => [
                    'field' => [
                        'elem' => 'text',
                        'label' => '字段名',
                        'width' => 200
                    ],
                    'title' => [
                        'elem' => 'text',
                        'label' => '列头名称',
                        'tip' => '真实字段不填，自动获取',
                        'width' => 150
                    ],
                    'templet' => [
                        'elem' => 'text',
                        'label' => '展示模板',

                        'tip' => '自定义html结构，必须用div包住'
                    ],
                    'attr' => [
                        'elem' => 'keyvalue',
                        'label' => '列表字段属性',
                        'width' => 340,
                    ]
                ],
                'message' => '一般不用单独设置，会自动获取字段的列表属性自动渲染；一旦配置该处配置优先；真实字段一般只选择字段名即可，其他数据也会自动从字段中读取',
                'list' => 0
            ],
            'admin_list_filters' => [
                'type' => 'array',
                'name' => '搜索字段',
                'elem' => 'multiattrs',
                'fields' => [
                    'field' => [
                        'elem' => 'text',
                        'label' => '字段名',
                        'width' => 200
                    ],
                    'title' => [
                        'elem' => 'text',
                        'label' => '搜索名称',
                        'tip' => '真实字段不填，自动获取',
                        'width' => 150
                    ],
                    'templet' => [
                        'elem' => 'text',
                        'label' => '搜索模板',
                        'tip' => ''
                    ],
                    'attr' => [
                        'elem' => 'keyvalue',
                        'label' => '搜索字段属性',
                        'width' => 340,
                    ]
                ],
                'message' => '一般不用单独设置，会自动获取字段的搜索属性自动渲染；一旦配置该处配置优先；真实字段一般只选择字段名即可，其他数据也会自动从字段中读取',
                'list' => 0,
            ],
            'business_list_filters' => [
                'type' => 'array',
                'name' => '搜索字段',
                'elem' => 'multiattrs',
                'fields' => [
                    'field' => [
                        'elem' => 'text',
                        'label' => '字段名',
                        'width' => 200
                    ],
                    'title' => [
                        'elem' => 'text',
                        'label' => '搜索名称',
                        'tip' => '真实字段不填，自动获取',
                        'width' => 150
                    ],
                    'templet' => [
                        'elem' => 'text',
                        'label' => '搜索模板',
                        'tip' => ''
                    ],
                    'attr' => [
                        'elem' => 'keyvalue',
                        'label' => '搜索字段属性',
                        'width' => 340,
                    ]
                ],
                'message' => '一般不用单独设置，会自动获取字段的搜索属性自动渲染；一旦配置该处配置优先；真实字段一般只选择字段名即可，其他数据也会自动从字段中读取',
                'list' => 0,
            ],
            'field_list' => [
                'name' => '基础字段',
                'elem' => 0,
                'require' => true,
                'attrs' => [
                    'style' => 'max-width: 100%',
                    'data-pagesize' => 8
                ],
                'options' => [
                ],
                'list' => 0,
                'tip' => '用于指定快速创建基础默认字段'
            ],
            'parent_admin_menu_id' => [
                'name' => '后台父菜单',
                'elem' => 0,
                'foreign' => 'AdminRule',
                'list' => 0,
                'tip' => '如有选择，将自动添加到指定菜单下'
            ],
            'parent_business_menu_id' => [
                'name' => '中台父菜单',
                'elem' => 0,
                'foreign' => 'BusinessMenu',
                'list' => 0,
                'tip' => '如有选择，将自动添加到指定菜单下'
            ],
            'is_import' => [
                'type' => 'boolean',
                'name' => '后台导入',
                'elem' => 'checker',
                'templet' => 'merge',
                'list' => [
                    'templet' => 'checker.show',
                    'width' => 90
                ]
            ],
            'is_business_import' => [
                'type' => 'boolean',
                'name' => '中台导入',
                'elem' => 'checker',
                'list' => [
                    'templet' => 'checker.show',
                    'width' => 90
                ]
            ],
            'is_exists_table' => [
                'type' => 'boolean',
                'name' => '不自动建表',
                'elem' => 0,
                'list' => 0,
                'tip' => '用于特殊情况，只添加一条模型数据用于创建模型文件；不自动建表'
            ],
            'admin_id' => [
                'type' => 'integer',
                'name' => '管理员ID',
                'elem' => 0,
                'list' => 0
            ],
            'create_time' => [
                'type' => 'integer',
                'name' => '创建日期',
                'elem' => 0,
                'list' => [
                    'minWidth' => 145
                ],
            ],
            'update_time' => [
                'type' => 'integer',
                'name' => '修改日期',
                'elem' => 0,
                'list' => [
                    'minWidth' => 145
                ],
            ]
        ];

        $config = FormConfig::get('base_field_lists');
        $options = [];
        foreach ($config as $field => $info) {
            if (!get_app('business') && in_array($field, ['business_id', 'business_member_id'])) {
                continue;
            }
            $options[$field] = $info['name'] ?? $field;
        }
        $this->form['field_list']['options'] = $options;
        $this->form['admin_list_fields']['fields']['templet']['options'] = FormConfig::get('list_item_lists');
        $this->form['admin_list_filters']['fields']['templet']['options'] = FormConfig::get('filter_item_lists');
        $dbconnection = [];
        foreach ((array) Config::get('database.connections') as $key => $item) {
            $dbconnection[$key] = "{$key} / 库名：{$item['database']}";
        }
        $this->form['connection']['options'] = $dbconnection;
        $this->form['connection']['tip'] = '不建议选择；修改连接不会重新建表；如不选择，使用默认配置连接名：' . Config::get('database.default');

        parent::{__FUNCTION__}();
    }

    public function beforeInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $field_list = explode(',', $this['field_list'] ?? '');
        if (in_array('parent_id', $field_list)) {
            $this['parent_model'] = 'parent';
        }
        $relation_link = [];
        if (!empty($this['relation_link']) && is_array($this['relation_link'])) {
            $relation_link = Arr::combine($this['relation_link'], 'key');
        }
        if (in_array('admin_id', $field_list) && !array_key_exists('Admin', $relation_link)) {
            $relation_link['Admin'] = [
                'key' => 'Admin',
                'foreign' => '',
                'type' => 'belongsTo',
                'foreign_key' => '',
                'model' => ''
            ];
        }
        if (in_array('user_id', $field_list) && !array_key_exists('User', $relation_link)) {
            $relation_link['User'] = [
                'key' => 'User',
                'foreign' => '',
                'type' => 'belongsTo',
                'foreign_key' => '',
                'model' => ''
            ];
        }
        if (in_array('business_id', $field_list) && !array_key_exists('Business', $relation_link)) {
            $relation_link['Business'] = [
                'key' => 'Business',
                'foreign' => '',
                'type' => 'belongsTo',
                'foreign_key' => '',
                'model' => ''
            ];
        }
        if (in_array('business_member_id', $field_list) && !array_key_exists('BusinessMember', $relation_link)) {
            $relation_link['BusinessMember'] = [
                'key' => 'BusinessMember',
                'foreign' => '',
                'type' => 'belongsTo',
                'foreign_key' => '',
                'model' => ''
            ];
        }
        $this['relation_link'] = array_values($relation_link);

        $this['display'] = isset($this['display']) ? trim($this['display']) : '';
        if (empty($this['display']) && in_array('title', $field_list)) {
            $this['display'] = 'title';
        }
        return $parent_return;
    }

    public function beforeWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $data = $this->getData();
        if (!empty($data['is_business_copy_admin'])) {
            $this['business_is_remove_pk'] = $data['admin_is_remove_pk'] ?? '';
            $this['business_list_config'] = $data['list_config'] ?? '';
            $this['business_item_checkbox'] = $data['admin_item_checkbox'] ?? '';
            $this['business_filter_model'] = $data['admin_filter_model'] ?? '';
            $this['business_list_with'] = $data['admin_list_with'] ?? '';
            $this['business_list_fields'] = $data['admin_list_fields'] ?? '';
            $this['business_list_filters'] = $data['admin_list_filters'] ?? '';
            $this['business_tool_bar'] = $data['admin_tool_bar'] ?? '';
            $this['business_item_tool_bar'] = $data['admin_item_tool_bar'] ?? '';
            $this['business_item_toolbar_options'] = $data['admin_item_toolbar_options'] ?? '';
            $this['business_counter'] = $data['admin_counter'] ?? '';
            $this['business_total_row'] = $data['admin_total_row'] ?? '';
            $this['business_siderbar'] = $data['admin_siderbar'] ?? '';
            $this['business_table_attrs'] = $data['admin_table_attrs'] ?? '';
            $this['is_business_import'] = $data['is_import'] ?? '';
        }
        return $parent_return;
    }

    public function afterDeleteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $data = $this->getData();
        if (isset($data['addon']) && isset($data['model'])) {
            Cache::tag(($data['addon'] ? $data['addon'] . '.' : '') . $data['model'])->clear();
        }
        Db::name('Field')->where('model_id', '=', $this['id'])->delete();

        return $parent_return;
    }

    public function afterUpdateCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!empty($this->id) && empty($this['is_not_create_file'])) {
            try {
                $path = (new CreateFile)->createModel(intval($this->id));
            } catch (\Exception $e) {}
        }
        return $parent_return;
    }

    public function afterInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $data = $this->getData();
        /*
        if (!empty($data['is_exists_table'])) {
            return $parent_return;
        }
        */
        if (isset($data['parent_admin_menu_id']) && $data['parent_admin_menu_id'] != '') {
            model('AdminRule')
                ->createData([
                    'parent_id' => $data['parent_admin_menu_id'],
                    'type' => 'menu',
                    'title' => $data['cname'] . '列表',
                    'addon' => $data['addon'],
                    'controller' => Str::snake($data['model']),
                    'action' => 'index',
                    'open_type' => '_iframe',
                    'is_nav' => 1
                ]);
        }
        if (isset($data['parent_business_menu_id']) && $data['parent_business_menu_id'] != '' && get_app('business')) {
            model('BusinessMenu')
                ->createData([
                    'parent_id' => $data['parent_business_menu_id'],
                    'type' => 'menu',
                    'title' => $data['cname'] . '列表',
                    'addon' => $data['addon'],
                    'controller' => Str::snake($data['model']),
                    'action' => 'index',
                    'open_type' => '_iframe',
                    'is_nav' => 1,
                    'is_open' => 1
                ]);
        }

        if (!empty($data['field_list']) && empty($data['is_exists_table'])) {
            $field_list = explode(',', $data['field_list']);
            $sql_list = [];
            $index_sql_list = [];
            $data_list = [];
            $config = FormConfig::get('base_field_lists');

            $pk = trim($data['pk'] ?? '');

            foreach ($config as $field => $info) {
                if (!in_array($field, $field_list) || empty($info['sql']) || empty($info['data'])) {
                    continue;
                }
                $info['sql'] = is_array($info['sql']) ? $info['sql'] : [$info['sql']];
                // 主键
                if (!empty($pk) && $field == 'id') {
                    $info['data']['field'] = $pk;
                    $info['sql'][0] = str_replace('`id`', '`' . $pk . '`', $info['sql'][0]);
                }
                array_push($sql_list, ...$info['sql']);
                $info['data']['model_id'] = $data['id'];
                array_push($data_list, $info['data']);
                if (!empty($info['data']['index'])) {
                    if (strtolower($info['data']['index']) == 'unique') {
                        array_push($index_sql_list, "UNIQUE KEY `{$field}` (`{$field}`)");
                    } else {
                        array_push($index_sql_list, "KEY `{$field}` (`{$field}`)");
                    }
                }
            }
            if (!empty($sql_list)) {
                // 自动添加关联字段
                if (!empty($this['relation_link'])) {
                    $relation = json_decode($this['relation_link'], true);
                    // 目前暂时只管belongsTo 在当前表加入关联字段  其他类型后期考虑是否自动加字段加入关联字段
                    $start = 1;
                    foreach ($relation as $link) {
                        if ($link['type'] == 'belongsTo') {
                            $foreign_key = $link['foreign_key'] ?: Str::snake($link['key']) . '_id';
                            if (in_array($foreign_key, $field_list)) {
                                continue;
                            }
                            $foreign_title = '所属' . $link['key'] . 'ID';
                            $foreign_form = 'relation';
                            if (get_model_name($link['foreign'] ?: $link['key'])) {
                                $foreign_model = model($link['foreign'] ?: $link['key']);
                                $foreign_title = '所属' . $foreign_model->cname . 'ID';
                                if ($foreign_model->getTreeLevel() > 0) {
                                    $foreign_form =  'xmtree';
                                }
                            }
                            array_splice($sql_list, $start, 0, "`{$foreign_key}` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '{$foreign_title}'");
                            array_push($index_sql_list, "KEY `{$foreign_key}` (`{$foreign_key}`)");
                            array_splice($data_list, $start, 0, [[
                                'field'     => $foreign_key,
                                'name'     => $foreign_title,
                                'type'      => 'INT',
                                'length'    => '10',
                                'form_foreign' => $link['key'],
                                'default'   => '0',
                                'list' => 'relation',
                                'is_not_null'   => 1,
                                'is_unsigned' => 1,
                                'index'     => 'index',
                                'form'      => $foreign_form,
                                'business_form'      => $foreign_form,
                                'is_system' => '1',
                                'validate'  => '[{"rule":"gt","args":"0","on":"0","message":""}]',
                                'business_validate'  => '[{"rule":"gt","args":"0","on":"0","message":""}]',
                                'model_id' => $this['id']
                            ]]);
                        } elseif ($link['type'] == 'belongsToMany') {
                            $foreign_key = $link['foreign_key'] ?: Str::snake($link['key']) . '_id';
                            if (in_array($foreign_key, $field_list)) {
                                continue;
                            }
                            $foreign_title = $link['key'];
                            $foreign_form = 'relation';
                            if (get_model_name($link['foreign'] ?: $link['key'])) {
                                $foreign_model = model($link['foreign'] ?: $link['key']);
                                $foreign_title = $foreign_model->cname;
                                if ($foreign_model->getTreeLevel() > 0) {
                                    $foreign_form =  'xmtree';
                                }
                            }
                            array_splice($data_list, $start, 0, [[
                                'field'     => $foreign_key,
                                'name'     => $foreign_title,
                                'is_field' => 0,
                                'form_foreign' => $link['key'],
                                'list' => 'relation',
                                'form'      => $foreign_form,
                                'business_form_foreign' => $link['key'],
                                'business_list' => 'relation',
                                'business_form'      => $foreign_form,
                                'is_system' => '0',
                                'validate'  => '',
                                'model_id' => $this['id']
                            ]]);
                        }
                        $start++;
                    }
                }

                $sql = sprintf("CREATE TABLE IF NOT EXISTS `%s%s`(%s%s) ENGINE=InnoDB  DEFAULT CHARSET=%s COMMENT='%s'",
                    empty($data['full_table']) ? get_db_config('prefix', $data['connection'] ?? '') : '',
                    empty($data['full_table']) ? (empty($data['addon']) ? Str::snake($data['model']) : Str::snake($data['addon']) . '_' . Str::snake($data['model']))  : trim($data['full_table']),
                    implode(',', $sql_list),
                    $index_sql_list ? ',' . implode(',', $index_sql_list) : '',
                    get_db_config('charset', $data['connection'] ?? ''),
                    $data['cname'] ?? $data['model']
                );
                try {
                    Db::connect($data['connection'] ?? '')->execute($sql);
                    foreach ($data_list as $item) {
                        $fieldModel = new Field();
                        $item['is_exists'] = 1;
                        $item['create_time'] = time();
                        $item['update_time'] = time();
                        $item['is_field'] = isset($item['is_field']) ? $item['is_field'] : 1;
                        $item['admin_id'] = Auth::user('id');
                        $fieldModel->isValidate(false)->createData($item);
                    }
                } catch (\Exception $e) {
                    Log::write("SQL:[{$sql}]，错误：" . $e->getMessage(), 'error');
                    throw new \Exception($e->getMessage());
                }

            }
        }

        if (!empty($this->id) && empty($data['is_not_create_file'])) {
            try {
                $path = (new CreateFile)->createModel(intval($this->id));
                if (!empty($data['is_controller']) && $path) {
                    (new CreateFile)->createController($data['model'], $data['addon'] ? strtolower($data['addon']) : '', 'admin');
                }
                if (!empty($data['is_business_controller']) && $path && get_app('business')) {
                    (new CreateFile)->createController($data['model'], $data['addon'] ? strtolower($data['addon']) : '', 'business');
                }
            } catch (\Exception $e) {}
        }

        return $parent_return;
    }

    public $validate = [
        'model' => [
            [
                'rule' => 'require'
            ],
            [
                'rule' => ['unique', 'model,model^addon']
            ],
            [
                'rule' => ['regex', '/^[a-z]+[a-z0-9]+$/i']
            ],
            [
                'rule' => ['call', 'notEqList']
            ]
        ],
        'cname' => [
            'rule' => 'require'
        ],
        'field_list' => [
            'rule' => 'requireIf:is_exists_table,0',
            'on' => 'add'
        ]
    ];

    public function  notEqList($val)
    {
        $val = Str::studly($val);
        if (in_array($val, ['App', 'Model', 'Field'])) {
            return "模型名不允许为：" . $val;
        }
        return true;
    }

    public function clearCache($model)
    {
        if (is_int($model)) {
            $data = $this->find($model);
            if (empty($data)) {
                return false;
            }
            $model = ($data['addon'] ? $data['addon'] . '.' : '') . $data['model'];
        }
        Cache::tag($model)->clear();
        return true;
    }

}