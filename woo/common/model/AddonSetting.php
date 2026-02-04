<?php
declare(strict_types=1);

namespace woo\common\model;

use app\common\model\App;

class AddonSetting extends App
{
    /** 父模型名 */
    public $parentModel = 'Addon';

    /** 模型名称 */
    public $cname = '插件配置';

    /** 主显字段信息 */
    public $display = 'title';

    /** 默认排序方式 默认值desc */
    public $orderType = 'asc';

    /** 列表是否开启拖拽排序 需要有list_order字段有效 */
    public $sortable = true;

    /** 自定义数据 */
    public $customData = [
        'create' => true,
        'batch_delete' => true,
        'modify' => true,
        'delete' => true,
        'detail' => true,
    ];

    /** 模型关联信息 */
    public $relationLink = [
        'Addon' => [
            'type' => 'belongsTo',
        ],
    ];


    protected function start()
    {
        parent::{__FUNCTION__}();

        /** 表单form属性 */
        $this->form = [
            'id' => [
                'type' => 'integer',
                'name' => 'ID',
                'elem' => 'hidden',
                'is_contribute' => false,
            ],
            'title' => [
                'type' => 'string',
                'name' => '标题',
                'elem' => 'text',
                'is_contribute' => false,
                'list_filter' => true,
            ],
            'addon_id' => [
                'type' => 'integer',
                'name' => '所属插件',
                'elem' => 'relation',
                'foreign' => 'Addon',
                'is_contribute' => false,
                'list' => 'relation',
                'list_filter' => 'relation',
            ],
            'var' => [
                'type' => 'string',
                'name' => '变量名',
                'elem' => 'text',
                'is_contribute' => false,
                'list' => 'show.blue',
                'list_filter' => true,
                'filter' => 'trim',
            ],
            'value' => [
                'type' => 'string',
                'name' => '数据',
                'elem' => 0,
                'is_contribute' => false,
            ],
            'type' => [
                'type' => 'string',
                'name' => '输入类型',
                'elem' => 'select',
                'is_contribute' => false,
                'options' => [
                    'text' => '单行文本',
                    'textarea' => '多行文本',
                    'number' => '数字',
                    'radio' => '单选',
                    'select' => '下拉',
                    'checker' => '是否',
                    'checkbox' => '多选',
                    'image' => '图片',
                    'file' => '上传',
                    'array' => '数组',
                    'keyvalue' => '键值对',
                    'password' => '密码',
                    'color' => '取色器',
                    'ckeditor' => '富文本',
                    'multiimage' => '多图',
                ],
            ],
            'options' => [
                'type' => 'array',
                'name' => '选项',
                'elem' => 'keyvalue',
                'is_contribute' => false,
                'list' => 0,
            ],
            'tip' => [
                'type' => 'string',
                'name' => '提示',
                'elem' => 'text',
                'is_contribute' => false,
                'list' => 0,
            ],
            'list_order' => [
                'type' => 'integer',
                'name' => '排序权重',
                'elem' => 'number',
                'is_contribute' => false,
            ],
            'create_time' => [
                'type' => 'integer',
                'name' => '创建日期',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 0,
            ],
            'update_time' => [
                'type' => 'integer',
                'name' => '修改日期',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 0,
            ]
        ];

        /** 表单分组属性 */
        $this->formGroup = [];

        /** 表单触发器属性 */
        $this->formTrigger = [];

        /** 表单验证属性 */
        $this->validate = [
            'title' => [
                [
                    'rule' => ['require'],
                ],
            ],
            'addon_id' => [
                [
                    'rule' => ['require'],
                ],
                [
                    'rule' => ['egt', -1],// 不要改 故意写的-1
                ],
            ],
            'var' => [
                [
                    'rule' => ['require'],
                ],
                [
                    'rule' => ['unique', 'addon_setting'],
                ],
            ],
            'type' => [
                [
                    'rule' => ['require'],
                ],
            ],
        ];
    }

    public function beforeWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!empty($this->addon_id) && $this->addon_id > 0 && !empty($this->var)) {
            $parent_var = model('Addon')->where('id', '=', intval($this->addon_id))->value('name');
            if (strpos($this->var, $parent_var . '_') !== 0) {
                $this->var =  $parent_var . '_' . $this->var;
            }
        }
        return $parent_return;
    }
}
