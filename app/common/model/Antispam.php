<?php

/**
 * 自动生成的模型文件2022-05-16 13:41:05，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\AntispamTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class Antispam extends \woo\common\model\Antispam
{
	use traits\AntispamTrait;

	/** 模型名称 */
	public $cname = '文本审核';

	/** 主显字段信息 */
	public $display = 'title';

	/** 自定义数据 */
	public $customData = [
		'batch_delete' => true,
		'modify' => true,
		'delete' => true,
		'detail' => true,
		'business_batch_delete' => true,
		'business_modify' => true,
		'business_delete' => true,
		'business_detail' => true,
	];

	/** 模型关联信息 */
	public $relationLink = [];


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
				'name' => '审核模型',
				'elem' => 'text',
				'is_contribute' => false,
			],
			'content' => [
				'type' => 'string',
				'name' => '审核内容',
				'elem' => 'textarea',
				'is_contribute' => false,
				'list' => 0,
			],
			'admin_id' => [
				'type' => 'integer',
				'name' => '管理员ID',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'foreign_id' => [
				'type' => 'integer',
				'name' => '模型ID',
				'elem' => 'number',
				'is_contribute' => false,
			],
			'is_verify' => [
				'type' => 'integer',
				'name' => '审核状态',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => 'checker.show',
			],
			'msg' => [
				'type' => 'string',
				'name' => '内容提示',
				'elem' => 'textarea',
				'is_contribute' => false,
			],
			'words' => [
				'type' => 'string',
				'name' => '不合格字符',
				'elem' => 'textarea',
				'is_contribute' => false,
				'list' => 0,
			],
			'result' => [
				'type' => 'string',
				'name' => '返回结果',
				'elem' => 'textarea',
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
				'detail' => 0,
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
				'name' => '审核模型',
				'elem' => 'text',
				'is_contribute' => false,
			],
			'content' => [
				'type' => 'string',
				'name' => '审核内容',
				'elem' => 'textarea',
				'is_contribute' => false,
				'list' => 0,
			],
			'admin_id' => [
				'type' => 'integer',
				'name' => '管理员ID',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
				'detail' => 0,
			],
			'foreign_id' => [
				'type' => 'integer',
				'name' => '模型ID',
				'elem' => 'number',
				'is_contribute' => false,
			],
			'is_verify' => [
				'type' => 'integer',
				'name' => '审核状态',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => 'checker.show',
			],
			'msg' => [
				'type' => 'string',
				'name' => '内容提示',
				'elem' => 'textarea',
				'is_contribute' => false,
			],
			'words' => [
				'type' => 'string',
				'name' => '不合格字符',
				'elem' => 'textarea',
				'is_contribute' => false,
				'list' => 0,
			],
			'result' => [
				'type' => 'string',
				'name' => '返回结果',
				'elem' => 'textarea',
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
				'detail' => 0,
			],
		];

		/** 表单分组属性 */
		$this->formGroup = [];

		/** 表单触发器属性 */
		$this->formTrigger = [];
		$this->businessFormTrigger = [];

		/** 表单验证属性 */
		$this->validate = [];
		$this->businessValidate = [];

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
