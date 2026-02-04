<?php

/**
 * 自动生成的模型文件2023-02-14 23:25:24，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\AdminGroupTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class AdminGroup extends \woo\common\model\AdminGroup
{
	use traits\AdminGroupTrait;

	/** 模型ID */
	protected $modelId = 38;

	/** 父模型名 */
	public $parentModel = 'parent';

	/** 模型名称 */
	public $cname = '角色';

	/** 主显字段信息 */
	public $display = 'title';

	/** 默认排序方式 默认值desc */
	public $orderType = 'asc';

	/** 无极限层数 */
	protected $treeLevel = 3;

	/** 自定义数据 */
	public $customData = [
		'create' => true,
		'batch_delete' => true,
		'modify' => true,
		'delete' => true,
		'detail' => true,
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
				'name' => '角色名',
				'elem' => 'text',
				'is_contribute' => false,
				'require' => true,
				'list' => [
					'width' => '240',
				],
			],
			'parent_id' => [
				'type' => 'integer',
				'name' => '父级ID',
				'elem' => 'xmtree',
				'is_contribute' => false,
				'list' => [
					'hide' => true,
				],
			],
			'dashboard' => [
				'type' => 'string',
				'name' => '主面板URL',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => [
					'templet' => 'url',
					'width' => '200',
				],
			],
			'is_admin' => [
				'type' => 'integer',
				'name' => '后台登录',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => [
					'templet' => 'checker.show',
					'width' => '110',
				],
			],
			'data_allow' => [
				'type' => 'integer',
				'name' => '数据权限',
				'elem' => 'select',
				'is_contribute' => false,
				'options' => [
					0 => '全部数据权限',
					1 => '仅本人数据权限',
					2 => '本部门数据权限',
					3 => '部门及以下数据权限',
					4 => '自定义数据权限',
					5 => '所在顶级及以下部门权限',
					-1 => '继承父角色权限',
				],
				'list' => [
					'width' => '160',
				],
			],
			'custom_data_allow' => [
				'type' => 'string',
				'name' => '自定义数据权限',
				'elem' => 'xmtree',
				'foreign' => 'Department',
				'is_contribute' => false,
				'list' => 0,
			],
			'list_order' => [
				'type' => 'integer',
				'name' => '排序权重',
				'elem' => 'number',
				'is_contribute' => false,
				'list' => [
					'width' => '120',
				],
			],
			'family' => [
				'type' => 'string',
				'name' => '家族',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'level' => [
				'type' => 'integer',
				'name' => '层级',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'children_count' => [
				'type' => 'integer',
				'name' => '下级数',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'hide' => true,
				],
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
			],
		];

		/** 表单分组属性 */
		$this->formGroup = [];

		/** 表单触发器属性 */
		$this->formTrigger = [
			'data_allow' => [
				4 => 'custom_data_allow',
			],
		];

		/** 表单验证属性 */
		$this->validate = [
			'title' => [
				[
					'rule' => ['require'],
				],
			],
			'parent_id' => [
				[
					'rule' => ['call', 'checkParent'],
				],
			],
			'data_allow' => [
				[
					'rule' => ['call', 'checkDataAllow'],
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
			'table' => [
				'treetable' => true,
			],
			'checkbox' => 'checkbox',
			'toolbar_options' => [
				'title' => '操作',
				'fixed' => 'right',
				'min_width' => 0,
				'align' => 'center',
			],
			'counter' => [],
			'total_row' => [],
			'is_remove_pk' => 3,
			'filter_model' => '',
			'list_with' => [],
			'list_fields' => [],
			'list_filters' => [],
		];

		/** 表单场景*/
		$this->formScene = [];
	}
}
