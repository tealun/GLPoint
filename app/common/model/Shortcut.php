<?php

/**
 * 自动生成的模型文件2023-01-18 00:29:04，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\ShortcutTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class Shortcut extends \woo\common\model\Shortcut
{
	use traits\ShortcutTrait;

	/** 模型名称 */
	public $cname = '快捷方式';

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
				'name' => '标题',
				'elem' => 'text',
				'is_contribute' => false,
				'require' => true,
			],
			'type' => [
				'type' => 'string',
				'name' => '类型',
				'elem' => 'select',
				'is_contribute' => false,
				'require' => true,
				'options' => [
					'func' => 'JS函数',
					'url' => '链接跳转',
				],
			],
			'url' => [
				'type' => 'string',
				'name' => 'URL',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => 'url',
			],
			'is_verify' => [
				'type' => 'integer',
				'name' => '审核',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => 'checker',
			],
			'func' => [
				'type' => 'string',
				'name' => '函数名',
				'elem' => 'text',
				'is_contribute' => false,
			],
			'target' => [
				'type' => 'string',
				'name' => '跳转方式',
				'elem' => 'select',
				'is_contribute' => false,
				'options' => [
					'newtab' => 'Tab打开',
					'blank' => '新窗口',
				],
			],
			'icon' => [
				'type' => 'string',
				'name' => '图标',
				'elem' => 'icon',
				'is_contribute' => false,
				'require' => true,
				'list' => 'icon',
			],
			'admin_group_id' => [
				'type' => 'string',
				'name' => '用户组ID',
				'elem' => 'xmtree',
				'foreign' => 'AdminGroup',
				'is_contribute' => false,
				'attrs' => [
					'data-max' => '20',
				],
				'tip' => '如果不选，每个用户都会显示',
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
		$this->formTrigger = [
			'type' => [
				'func' => 'url|func',
				'url' => 'url|target',
			],
		];

		/** 表单验证属性 */
		$this->validate = [
			'title' => [
				[
					'rule' => ['require'],
				],
			],
			'type' => [
				[
					'rule' => ['require'],
				],
			],
			'icon' => [
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
			'toolbar_options' => [],
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
