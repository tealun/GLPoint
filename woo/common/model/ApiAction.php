<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use think\facade\Db;
use woo\common\Auth;
use woo\common\helper\Str;

class ApiAction extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
        $this->form['name']['options'] = [
            'create' => '添加',
            'modify' => '修改',
            'delete' => '删除',
            'batchDelete' => '批量删除',
            'page' => '列表翻页',
            'get' => '查询(单条)'
        ];

        $this->form['info']['fields'] = [
            'value' => [
                'label' => '操作标题',
                'elem' => 'text',
                'tip' => '必填',
                'width' => 150
            ],
            'method' => [
                'label' => '请求方法',
                'elem' => 'select',
                'options' => [
                    'GET' => 'GET',
                    'POST' => 'POST',
                    'DELETE' => 'DELETE',
                    'PUT' => 'PUT'
                ],
                'tip' => '必选',
                'width' => 100
            ],
            'login' => [
                'label' => '登录访问',
                'elem' => 'checker',
                'width' => 100
            ],
            'power' => [
                'label' => '是否需要授权',
                'elem' => 'checker',
                'tip' => '用于控制mapi插件是否授权',
                'width' => 120
            ],
            'author' => [
                'label' => '作者',
                'elem' => 'text',
                'width' => 120
            ],
            'url' => [
                'label' => 'URL地址',
                'elem' => 'text',
                'tip' => '如果有定义路由，需要填写',
            ],
            'tag' => [
                'label' => '标签',
                'elem' => 'text',
                'tip' => '多个之间|分割'
            ],
            'desc' => [
                'label' => '描述',
                'elem' => 'text',
            ],
            'is' => [
                'label' => '生成文档',
                'elem' => 'checker',
                'tip' => '用于控制mapi插件是否生成api文档',
                'width' => 100
            ],
            'isForbidden' => [
                'label' => '拒绝访问',
                'elem' => 'checker',
                'width' => 100
            ],
            'validate' => [
                'label' => '参数注解验证',
                'elem' => 'text',
                'tip' => '填false，不自动验证；也可填自定义验证器命名空间'
            ],
        ];
        $this->form['info']['multiattrs'] = [
            'cancel_create' => true,
            'cancel_insert' => true,
            'cancel_delete' => true,
            'cancel_clear' => true
        ];
        $this->form['info']['message'] = '添加【POST】 修改【POST】 删除【GET或DELETE】 查询【GET】';
        $this->form['params']['fields'] = [
            'is' => [
                'label' => '选中',
                'elem' => 'checkbox',
                'options' => [1 => ''],
                'width' => 70
            ],
            'value' => [
                'label' => '字段名',
                'elem' => 'text',
                'width' => 150
            ],
            'title' => [
                'label' => '名称',
                'elem' => 'text',
                'width' => 180
            ],
            'type' => [
                'label' => '类型',
                'elem' => 'select',
                'options' => [
                    'int' => 'int',
                    'float' => 'float',
                    'string' => 'string',
                    'boolean' => 'boolean',
                    'object' => 'object',
                    'array' => 'array',
                    'file' => 'file',
                ],
                'width' => 120
            ],
            'require' => [
                'label' => '是否必须',
                'elem' => 'checker',
                'width' => 100
            ],
            'default' => [
                'label' => '默认值',
                'elem' => 'text',
                'width' => 120
            ],
            'example' => [
                'label' => '示例值',
                'elem' => 'text',
                'width' => 120
            ],
            'desc' => [
                'label' => '描述',
                'elem' => 'text'
            ],
            'validate' => [
                'label' => '参数规则',
                'elem' => 'text',
                'tip' => '请求前的基础验证，非具体操作验证'
            ],
            'message' => [
                'label' => '规则提示',
                'elem' => 'text'
            ]
        ];
        $this->form['params']['message'] = '"删除、列表"操作系统会自行处理参数';

        $this->form['headers']['fields'] = [
            'is' => [
                'label' => '选中',
                'elem' => 'checkbox',
                'options' => [1 => ''],
                'width' => 70
            ],
            'value' => [
                'label' => '请求头',
                'elem' => 'text',
                'width' => 150
            ],
            'title' => [
                'label' => '名称',
                'elem' => 'text',
                'width' => 180
            ],
            'require' => [
                'label' => '是否必须',
                'elem' => 'checker',
                'width' => 100
            ],
            'default' => [
                'label' => '默认值',
                'elem' => 'text',
                'width' => 120
            ],
            'example' => [
                'label' => '示例值',
                'elem' => 'text',
                'width' => 120
            ],
            'desc' => [
                'label' => '描述',
                'elem' => 'text'
            ],
        ];

        $this->form['returns']['fields'] = [
            'is' => [
                'label' => '选中',
                'elem' => 'checkbox',
                'options' => [1 => ''],
                'width' => 70
            ],
            'value' => [
                'label' => '字段名',
                'elem' => 'text',
                'width' => 150
            ],
            'title' => [
                'label' => '名称',
                'elem' => 'text',
                'width' => 180
            ],
            'type' => [
                'label' => '类型',
                'elem' => 'select',
                'options' => [
                    'int' => 'int',
                    'float' => 'float',
                    'string' => 'string',
                    'boolean' => 'boolean',
                    'object' => 'object',
                    'array' => 'array',
                    'file' => 'file',
                ],
                'width' => 120
            ],
            'example' => [
                'label' => '示例值',
                'elem' => 'text',
                'width' => 120
            ],
            'desc' => [
                'label' => '描述',
                'elem' => 'text'
            ],
            'target' => [
                'label' => '目标类库',
                'elem' => 'text'
            ],
            'children_params' => [
                'label' => '指定字段列表',
                'elem' => 'text'
            ],
            'without_field' => [
                'label' => '过滤字段列表',
                'elem' => 'text'
            ],
        ];
        $this->form['returns']['message'] = '"添加、修改、删除、列表翻页"操作系统会自行处理返回值';


        $this->form['wheres']['fields'] = [
            'is' => [
                'label' => '选中',
                'elem' => 'checkbox',
                'options' => [1 => ''],
                'width' => 70
            ],
            'field' => [
                'label' => '字段名',
                'elem' => 'text'
            ],
            'sign' => [
                'label' => '插件条件',
                'elem' => 'select',
                'options' => [
                    '=' => '=',
                    '>' => '>',
                    '>=' => '>=',
                    '<' => '<',
                    '<=' => '<=',
                    '<>' => '<>',
                    'IN' => 'IN',
                    'NOT IN' => 'NOT IN',
                    'LIKE' => 'LIKE',
                    'NOT LIKE' => 'NOT LIKE',
                    'BETWEEN' => 'BETWEEN',
                    'NOT BETWEEN' => 'NOT BETWEEN',
                    'exp' => 'exp'
                ]
            ],
            'value' => [
                'label' => '查询值',
                'elem' => 'text'
            ],
        ];
        $this->form['wheres']['message'] = '查询值中支持使用"ARGS"字符会被自动替换为请求参数';
        $this->form['with']['message'] = '关联模型键名 => 字段列表；字段为空表示全字段,多个字段之间,分隔';
    }

    public function beforeWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $this->name = Str::camel(trim($this->name));
        return $parent_return;
    }
}