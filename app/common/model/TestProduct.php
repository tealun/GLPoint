<?php

/**
 * 自动生成的模型文件2023-03-02 00:19:14，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\TestProductTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class TestProduct extends App
{
	use traits\TestProductTrait;

	/** 模型ID */
	protected $modelId = 36;

	/** 模型名称 */
	public $cname = '测试产品';

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
		'copy' => true,
		'delete_index' => true,
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
			'number' => [
				'type' => 'string',
				'name' => '产品编号',
				'elem' => 'text',
				'is_contribute' => false,
				'require' => true,
				'list' => [
					'fixed' => 'left',
				],
				'list_filter' => true,
			],
			'title' => [
				'type' => 'string',
				'name' => '标题',
				'elem' => 'text',
				'is_contribute' => false,
				'require' => true,
				'attrs' => [
					'class' => ['tooltip'],
					'data-tip-text' => '请输入',
					'data-tip-bg' => '#ff0000',
				],
				'list' => [
					'width' => '120',
				],
				'list_filter' => true,
			],
			'unit' => [
				'type' => 'string',
				'name' => '单位',
				'elem' => 'text',
				'is_contribute' => false,
			],
			'price' => [
				'type' => 'float',
				'name' => '成本价',
				'elem' => 'text',
				'is_contribute' => false,
			],
			'sell_price' => [
				'type' => 'float',
				'name' => '销售价',
				'elem' => 'text',
				'is_contribute' => false,
			],
			'is_verify' => [
				'type' => 'integer',
				'name' => '审核',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => 'checker',
			],
			'image' => [
				'type' => 'string',
				'name' => '图片',
				'elem' => 'multiimage',
				'is_contribute' => false,
				'upload' => [
					'resizeWidth' => '200',
				],
				'list' => 'file',
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

		/** 表单验证属性 */
		$this->validate = [
			'number' => [
				[
					'rule' => ['require'],
				],
			],
			'title' => [
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
			'counter' => [
				[
					'field' => 'price',
					'title' => '',
					'type' => 'avg',
					'where_type' => '',
					'where' => '',
					'callback' => '',
					'templet' => '',
					'more' => '',
				],
				[
					'field' => 'number',
					'title' => '',
					'type' => 'count',
					'where_type' => '',
					'where' => '',
					'callback' => '',
					'templet' => '',
					'more' => '',
				],
				[
					'field' => 'id',
					'title' => '',
					'type' => 'min',
					'where_type' => '',
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
	}
}
