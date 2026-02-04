<?php

/**
 * 自动生成的模型文件2023-02-08 00:20:10，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\SettingTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class Setting extends \woo\common\model\Setting
{
	use traits\SettingTrait;

	/** 父模型名 */
	public $parentModel = 'SettingGroup';

	/** 模型名称 */
	public $cname = '配置';

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
		'SettingGroup' => [
			'type' => 'belongsTo',
		],
	];


	protected function start()
	{
		parent::{__FUNCTION__}();

		/**
		模型 字段 属性
		*/
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
				'require' => true,
				'list' => [
					'width' => '200',
				],
				'list_filter' => true,
			],
			'admin_id' => [
				'type' => 'integer',
				'name' => '管理员ID',
				'elem' => 0,
				'foreign' => 'Admin',
				'is_contribute' => false,
				'list' => 0,
			],
			'setting_group_id' => [
				'type' => 'integer',
				'name' => '所属系统配置组',
				'elem' => 'relation',
				'foreign' => 'SettingGroup',
				'is_contribute' => false,
				'require' => true,
				'list' => [
					'templet' => 'relation',
					'width' => '160',
				],
				'list_filter' => true,
			],
			'var' => [
				'type' => 'string',
				'name' => '变量名',
				'elem' => 'text',
				'is_contribute' => false,
				'require' => true,
				'list' => [
					'templet' => 'show.blue',
					'width' => '200',
				],
				'list_filter' => true,
				'filter' => 'trim',
			],
			'value' => [
				'type' => 'string',
				'name' => '数据',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'type' => [
				'type' => 'string',
				'name' => '输入类型',
				'elem' => 'select',
				'is_contribute' => false,
				'require' => true,
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
			'is_js_var' => [
				'type' => 'integer',
				'name' => 'JS中调用',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => [
					'templet' => 'checker',
					'width' => '120',
				],
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
				'list' => [
					'width' => '146',
				],
			],
			'delete_time' => [
				'type' => 'integer',
				'name' => '删除日期',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
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
			'setting_group_id' => [
				[
					'rule' => ['require'],
				],
				[
					'rule' => ['egt', 1],
				],
			],
			'var' => [
				[
					'rule' => ['require'],
				],
				[
					'rule' => ['unique', 'setting'],
				],
			],
			'type' => [
				[
					'rule' => ['require'],
				],
			],
		];

		/** 定义模型列表table相关 */
		//$this->tableTab = [
		//    // 列表主Tab名都应该叫basic
		//    'basic' => [
		//        'title' => '基本信息',
		//        //'model' => 当前Tab对应数据的模型名，默认为当前模型
		//        //'list_fields' => 当前Tab需要显示的字段 不设置自动从form属性list键识别
		//        //'list_filters' => 当前Tab的搜索规则 不设置自动从form属性list_filter键识别
		//        //'tool_bar' => 列表toolBar按钮定义 自定义头部按钮 系统会自动设置新增等操作
		//        //'item_tool_bar' => 列表项目toolBar按钮定义 系统会自动设置修改、删除等操作
		//        // 'siderbar' => ['foreign' => 'Demo'] 列表指定一个 侧边栏模型 模型对应关联字段建议不要是list_filters中得字段，会搜索冲突
		//        // 'table' => []
		//        // 'counter' => [] 当前Tab列表的基础统计配置
		//        // ...
		//    ],
		//    // 更多Tab...
		//];

		/** 后台自定义列表配置 后台的请求会自动将该属性合并到tableTab中*/
		$this->adminCustomTab = [
			'tool_bar' => [],
			'item_tool_bar' => [],
			'siderbar' => [],
			'table' => [],
			'checkbox' => 'checkbox',
			'toolbar_options' => [
				'title' => '操作',
				'fixed' => 'right',
				'min_width' => 0,
				'align' => 'center',
			],
			'counter' => [],
			'total_row' => [],
			'is_remove_pk' => 0,
			'filter_model' => '',
			'list_with' => [],
			'list_fields' => [],
			'list_filters' => [],
		];
	}
}
