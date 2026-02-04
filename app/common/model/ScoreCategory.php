<?php

/**
 * 自动生成的模型文件2025-07-02 11:26:28，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\ScoreCategoryTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class ScoreCategory extends App
{
	use traits\ScoreCategoryTrait;

	/** 模型ID */
	protected $modelId = 1001;

	/** 据表名称 */
	protected $table = 'woo_score_category';

	/** 模型名称 */
	public $cname = '积分规则分类表（无限级）';

	/** 主显字段信息 */
	public $display = 'category_name';

	/** 自定义数据 */
	public $customData = [
		'create' => true,
		'batch_delete' => true,
		'delete' => true,
		'detail' => true,
		'modify' => true,
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
				'name' => '分类ID',
				'elem' => 'hidden',
				'is_contribute' => false,
				'list' => 'show',
			],
			'parent_id' => [
				'type' => 'integer',
				'name' => '父级分类ID（0表示顶级）',
				'elem' => 'xmtree',
				'is_contribute' => false,
			],
			'family' => [
				'type' => 'array',
				'name' => '家族路径（如 0,1,2）',
				'elem' => 'array',
				'is_contribute' => false,
			],
			'level' => [
				'type' => 'integer',
				'name' => '当前层级（从0开始）',
				'elem' => 'keyvalue',
				'is_contribute' => false,
			],
			'children_count' => [
				'type' => 'integer',
				'name' => '子级数量',
				'elem' => 'keyvalue',
				'is_contribute' => false,
			],
			'category_name' => [
				'type' => 'string',
				'name' => '分类名称',
				'elem' => 'text',
				'is_contribute' => true,
			],
			'description' => [
				'type' => 'string',
				'name' => '分类描述',
				'elem' => 'textarea',
				'is_contribute' => false,
			],
			'list_order' => [
				'type' => 'integer',
				'name' => '排序权重',
				'elem' => 'sortvalues',
				'is_contribute' => false,
			],
			'is_nav' => [
				'type' => 'integer',
				'name' => '是否显示：0-隐藏，1-显示',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => 'checker',
			],
			'create_time' => [
				'type' => 'integer',
				'name' => '创建时间',
				'elem' => 0,
				'is_contribute' => false,
			],
			'update_time' => [
				'type' => 'integer',
				'name' => '更新时间',
				'elem' => 0,
				'is_contribute' => false,
			],
			'delete_time' => [
				'type' => 'integer',
				'name' => '删除时间',
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
		$this->validate = [];

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
			'parent_id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 0,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => 'index',
			],
			'family' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '255',
				'default' => '',
				'is_not_null' => 0,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'level' => [
				'is_field' => 1,
				'type' => 'TINYINT',
				'length' => '3',
				'default' => 0,
				'is_not_null' => 0,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'children_count' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 0,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'category_name' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '255',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'description' => [
				'is_field' => 1,
				'type' => 'TEXT',
				'length' => '',
				'default' => '',
				'is_not_null' => 0,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'list_order' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 0,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'is_nav' => [
				'is_field' => 1,
				'type' => 'TINYINT',
				'length' => '1',
				'default' => '1',
				'is_not_null' => 0,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'create_time' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 0,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'update_time' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 0,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'delete_time' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 0,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
		];
	}
}
