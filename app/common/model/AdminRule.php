<?php

/**
 * 自动生成的模型文件2023-02-18 10:56:47，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\AdminRuleTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class AdminRule extends \woo\common\model\AdminRule
{
	use traits\AdminRuleTrait;

	/** 模型ID */
	protected $modelId = 37;

	/** 父模型名 */
	public $parentModel = 'parent';

	/** 模型名称 */
	public $cname = '菜单规则';

	/** 主显字段信息 */
	public $display = 'title';

	/** 默认排序方式 默认值desc */
	public $orderType = 'asc';

	/** 无极限层数 */
	protected $treeLevel = 5;

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
			'parent_id' => [
				'type' => 'integer',
				'name' => '父级ID',
				'elem' => 'xmtree',
				'is_contribute' => false,
			],
			'title' => [
				'type' => 'string',
				'name' => '标题',
				'elem' => 'text',
				'is_contribute' => false,
				'require' => true,
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
				'list' => 0,
			],
			'type' => [
				'type' => 'string',
				'name' => '类型',
				'elem' => 'radio',
				'is_contribute' => false,
				'options' => [
					'directory' => '目录',
					'menu' => '菜单',
					'button' => '按钮',
				],
				'list_filter' => true,
			],
			'is_nav' => [
				'type' => 'integer',
				'name' => '是否显示',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => 'checker',
			],
			'icon' => [
				'type' => 'string',
				'name' => '图标',
				'elem' => 'icon',
				'is_contribute' => false,
				'list' => 'icon',
			],
			'addon' => [
				'type' => 'string',
				'name' => '二级目录',
				'elem' => 'text',
				'is_contribute' => false,
			],
			'controller' => [
				'type' => 'string',
				'name' => '控制器',
				'elem' => 'text',
				'is_contribute' => false,
				'list_filter' => true,
			],
			'action' => [
				'type' => 'string',
				'name' => '方法',
				'elem' => 'text',
				'is_contribute' => false,
			],
			'url' => [
				'type' => 'string',
				'name' => '路由',
				'elem' => 'text',
				'is_contribute' => false,
				'tip' => '单独定义路由需要填写',
			],
			'args' => [
				'type' => 'array',
				'name' => '参数',
				'elem' => 'keyvalue',
				'is_contribute' => false,
			],
			'open_type' => [
				'type' => 'string',
				'name' => '打开方式',
				'elem' => 'select',
				'is_contribute' => false,
				'options' => [
					'_iframe' => '选项卡(默认)',
					'_blank' => '新窗口',
					'_ajax' => 'Ajax请求',
					'_event' => 'JS事件回调',
					'_auto' => '自动获取表单加载',
					'_layer' => '嵌入弹窗',
					'_drawer' => '嵌入抽屉',
					'_open' => '独立窗口',
				],
			],
			'js_func' => [
				'type' => 'string',
				'name' => '回调事件名',
				'elem' => 'text',
				'is_contribute' => false,
				'tip' => '需自行定义该名称的全局JS函数',
			],
			'jianpin' => [
				'type' => 'string',
				'name' => '简拼',
				'elem' => 'text',
				'is_contribute' => false,
				'tip' => '如不填写，系统自动获取',
			],
			'pinyin' => [
				'type' => 'string',
				'name' => '拼音',
				'elem' => 'text',
				'is_contribute' => false,
				'tip' => '如不填写，系统自动获取',
			],
			'rule' => [
				'type' => 'string',
				'name' => '路由规则',
				'elem' => 0,
				'is_contribute' => false,
			],
			'list_order' => [
				'type' => 'integer',
				'name' => '排序权重',
				'elem' => 'number',
				'is_contribute' => false,
			],
			'other_name' => [
				'type' => 'string',
				'name' => '第三方名称',
				'elem' => 0,
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
		$this->formTrigger = [
			'type' => [
				'menu' => 'addon|controller|action|url|args|icon|open_type',
				'button' => 'addon|controller|action|url|args',
				'directory' => 'icon',
			],
			'open_type' => [
				'_event' => 'js_func',
			],
		];

		/** 表单验证属性 */
		$this->validate = [
			'parent_id' => [
				[
					'rule' => ['call', 'checkParent'],
				],
			],
			'title' => [
				[
					'rule' => ['require'],
				],
			],
			'controller' => [
				[
					'rule' => ['call', 'checkController'],
				],
			],
			'action' => [
				[
					'rule' => ['call', 'checkAction'],
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
