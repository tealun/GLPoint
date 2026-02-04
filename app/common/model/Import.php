<?php

/**
 * 自动生成的模型文件2023-01-29 19:58:32，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\ImportTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class Import extends \woo\common\model\Import
{
	use traits\ImportTrait;

	/** 模型名称 */
	public $cname = '导入';

	/** 主显字段信息 */
	public $display = 'title';

	/** 自定义数据 */
	public $customData = [
		'create' => true,
		'batch_delete' => true,
		'modify' => true,
		'delete' => true,
		'detail' => true,
		'business_create' => true,
		'business_batch_delete' => true,
		'business_modify' => true,
		'business_delete' => true,
		'business_detail' => true,
	];

	/** 模型关联信息 */
	public $relationLink = [
		'Business' => [
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
				'list_filter' => true,
			],
			'file' => [
				'type' => 'string',
				'name' => '文件',
				'elem' => 'file',
				'is_contribute' => false,
				'upload' => [
					'validExt' => 'xlsx|xls|csv',
					'sizeField' => 'file_size',
					'nameFiled' => 'file_name',
					'maxSize' => '100',
				],
				'list' => 'file',
			],
			'file_name' => [
				'type' => 'string',
				'name' => '文件名',
				'elem' => 0,
				'is_contribute' => false,
			],
			'file_size' => [
				'type' => 'integer',
				'name' => '文件大小',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 'filesize',
			],
			'model_id' => [
				'type' => 'integer',
				'name' => '模型',
				'elem' => 'select',
				'is_contribute' => false,
				'require' => true,
				'list_filter' => 'select',
			],
			'is_import' => [
				'type' => 'integer',
				'name' => '是否导入',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 'checker',
			],
			'type' => [
				'type' => 'string',
				'name' => '执行方式',
				'elem' => 'select',
				'is_contribute' => false,
				'require' => true,
				'options' => [
					'db' => 'Db',
					'model' => 'Model',
				],
				'attrs' => [],
				'tip' => 'Db减少数据库执行次数，但不会进行数据验证、模型事件等；Model一次插入一条，可以自动验证和执行模型事件',
			],
			'admin_id' => [
				'type' => 'integer',
				'name' => '管理员ID',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'business_id' => [
				'type' => 'integer',
				'name' => '商家ID',
				'elem' => 0,
				'foreign' => 'Business',
				'is_contribute' => false,
				'list' => 0,
			],
			'business_member_id' => [
				'type' => 'string',
				'name' => '商家用户ID',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'create_time' => [
				'type' => 'integer',
				'name' => '创建日期',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'width' => '146',
				],
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
		];
		/**
		中台模型 字段 属性
		*/
		$this->businessForm = [
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
				'list_filter' => true,
			],
			'file' => [
				'type' => 'string',
				'name' => '文件',
				'elem' => 'file',
				'is_contribute' => false,
				'upload' => [
					'validExt' => 'xlsx|xls|csv',
					'sizeField' => 'file_size',
					'nameFiled' => 'file_name',
					'maxSize' => '100',
				],
				'list' => 'file',
			],
			'file_name' => [
				'type' => 'string',
				'name' => '文件名',
				'elem' => 0,
				'is_contribute' => false,
			],
			'file_size' => [
				'type' => 'integer',
				'name' => '文件大小',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 'filesize',
			],
			'model_id' => [
				'type' => 'integer',
				'name' => '模型',
				'elem' => 'select',
				'is_contribute' => false,
				'require' => true,
				'list_filter' => 'select',
			],
			'is_import' => [
				'type' => 'integer',
				'name' => '是否导入',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 'checker',
			],
			'type' => [
				'type' => 'string',
				'name' => '执行方式',
				'elem' => 'select',
				'is_contribute' => false,
				'require' => true,
				'options' => [
					'db' => 'Db',
					'model' => 'Model',
				],
				'attrs' => [],
				'tip' => 'Db减少数据库执行次数，但不会进行数据验证、模型事件等；Model一次插入一条，可以自动验证和执行模型事件',
			],
			'admin_id' => [
				'type' => 'integer',
				'name' => '管理员ID',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
				'detail' => 0,
			],
			'business_id' => [
				'type' => 'integer',
				'name' => '商家ID',
				'elem' => 0,
				'foreign' => 'Business',
				'is_contribute' => false,
				'list' => 0,
				'detail' => 0,
			],
			'business_member_id' => [
				'type' => 'string',
				'name' => '商家用户ID',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'create_time' => [
				'type' => 'integer',
				'name' => '创建日期',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'width' => '146',
				],
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
		];

		/** 表单分组属性 */
		$this->formGroup = [];

		/** 表单触发器属性 */
		$this->formTrigger = [];
		$this->businessFormTrigger = [];

		/** 表单验证属性 */
		$this->validate = [
			'title' => [
				[
					'rule' => ['require'],
				],
			],
			'model_id' => [
				[
					'rule' => ['require'],
				],
				[
					'rule' => ['gt', 0],
				],
			],
			'type' => [
				[
					'rule' => ['require'],
				],
			],
		];
		$this->businessValidate = [
			'title' => [
				[
					'rule' => ['require'],
				],
			],
			'model_id' => [
				[
					'rule' => ['require'],
				],
				[
					'rule' => ['gt', 0],
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
		$this->businessCustomTab = [
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
