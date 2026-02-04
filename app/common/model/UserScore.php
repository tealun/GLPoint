<?php

/**
 * 自动生成的模型文件2025-07-07 12:03:06，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\UserScoreTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class UserScore extends \woo\common\model\UserScore
{
	use traits\UserScoreTrait;

	/** 模型ID */
	protected $modelId = 21;

	/** 模型名称 */
	public $cname = '积分记录';

	/** 主显字段信息 */
	public $display = 'user_id';

	/** 自定义数据 */
	public $customData = [
		'create' => true,
		'batch_delete' => true,
		'delete' => true,
		'detail' => true,
		'modify' => true,
		'delete_index' => true,
		'copy' => true,
	];

	/** 模型关联信息 */
	public $relationLink = [
		'User' => [
			'type' => 'belongsTo',
		],
		'ScoreRule' => [
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
			'create_time' => [
				'type' => 'integer',
				'name' => '创建日期',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'width' => 146,
				],
			],
			'update_time' => [
				'type' => 'integer',
				'name' => '修改日期',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'width' => 146,
				],
			],
			'user_id' => [
				'type' => 'integer',
				'name' => '接收用户ID',
				'elem' => 'relation',
				'foreign' => 'User',
				'is_contribute' => false,
				'require' => true,
				'list' => 'relation',
			],
			'score' => [
				'type' => 'float',
				'name' => '积分',
				'elem' => 'text',
				'is_contribute' => false,
			],
			'before' => [
				'type' => 'float',
				'name' => '变动前',
				'elem' => 0,
				'is_contribute' => false,
			],
			'after' => [
				'type' => 'float',
				'name' => '变动后',
				'elem' => 0,
				'is_contribute' => false,
			],
			'remark' => [
				'type' => 'string',
				'name' => '备注',
				'elem' => 'textarea',
				'is_contribute' => false,
			],
			'delete_time' => [
				'type' => 'integer',
				'name' => '删除日期',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'giver_id' => [
				'type' => 'integer',
				'name' => '发放人ID',
				'elem' => 0,
				'foreign' => 'User',
				'is_contribute' => false,
			],
			'score_rule_id' => [
				'type' => 'integer',
				'name' => '积分规则ID',
				'elem' => 'relation',
				'foreign' => 'score_rule',
				'is_contribute' => false,
				'list' => 'relation',
			],
			'require_time' => [
				'type' => 'integer',
				'name' => '申请日期',
				'elem' => 0,
				'is_contribute' => false,
			],
			'review_time' => [
				'type' => 'integer',
				'name' => '审核日期',
				'elem' => 0,
				'is_contribute' => false,
			],
			'reviewer_id' => [
				'type' => 'integer',
				'name' => '审核人',
				'elem' => 0,
				'is_contribute' => false,
			],
			'status' => [
				'type' => 'integer',
				'name' => '审核状态',
				'elem' => 0,
				'is_contribute' => false,
			],
			'review_reason' => [
				'type' => 'string',
				'name' => '审核理由',
				'elem' => 0,
				'is_contribute' => false,
			],
			'recorder_id' => [
				'type' => 'integer',
				'name' => '记录人ID',
				'elem' => 0,
				'foreign' => 'User',
				'is_contribute' => false,
			],
		];

		/** 表单分组属性 */
		$this->formGroup = [];

		/** 表单触发器属性 */
		$this->formTrigger = [];

		/** 表单验证属性 */
		$this->validate = [
			'user_id' => [
				[
					'rule' => ['require'],
				],
				[
					'rule' => ['gt', 0],
				],
			],
			'score' => [
				[
					'rule' => ['float'],
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
			'counter' => [
				[
					'field' => 'score',
					'title' => '总发放积分',
					'type' => 'sum',
					'where_type' => '',
					'where' => '',
					'callback' => '',
					'templet' => '',
					'more' => '',
				],
				[
					'field' => 'score',
					'title' => '当月积分',
					'type' => 'sum',
					'where_type' => 'where',
					'where' => '',
					'callback' => '',
					'templet' => '',
					'more' => '',
				],
				[
					'field' => 'user_id',
					'title' => '总发放人数',
					'type' => 'count',
					'where_type' => 'where',
					'where' => '',
					'callback' => '',
					'templet' => '',
					'more' => '',
				],
			],
			'total_row' => [],
			'is_remove_pk' => 0,
			'filter_model' => '',
			'list_with' => [],
			'list_fields' => [],
			'list_filters' => [],
		];

		/** 表单场景*/
		$this->formScene = [];

		/** 表结构缓存(模型字段)*/
		$this->tableColumns = [
			'id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 'none',
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 1,
				'index' => '',
			],
			'create_time' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'update_time' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'user_id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => 'index',
			],
			'score' => [
				'is_field' => 1,
				'type' => 'DECIMAL',
				'length' => '10,2',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'before' => [
				'is_field' => 1,
				'type' => 'DECIMAL',
				'length' => '10,2',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'after' => [
				'is_field' => 1,
				'type' => 'DECIMAL',
				'length' => '10,2',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'remark' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '512',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'delete_time' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'giver_id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'score_rule_id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => 'index',
			],
			'require_time' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'review_time' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'reviewer_id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'status' => [
				'is_field' => 1,
				'type' => 'TINYINT',
				'length' => '2',
				'default' => '1',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'review_reason' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '512',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'recorder_id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
		];
	}
}
