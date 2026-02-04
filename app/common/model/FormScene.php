<?php

/**
 * 自动生成的模型文件2023-05-05 10:05:04，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\FormSceneTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class FormScene extends \woo\common\model\FormScene
{
	use traits\FormSceneTrait;

	/** 模型ID */
	protected $modelId = 40;

	/** 父模型名 */
	public $parentModel = 'Model';

	/** 模型名称 */
	public $cname = '表单场景';

	/** 主显字段信息 */
	public $display = 'title';

	/** 默认排序方式 默认值desc */
	public $orderType = 'desc';

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
		'Model' => [
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
			'var' => [
				'type' => 'string',
				'name' => '标识符',
				'elem' => 'text',
				'is_contribute' => false,
				'require' => true,
				'list' => [
					'width' => 140,
				],
				'list_filter' => true,
			],
			'model_id' => [
				'type' => 'integer',
				'name' => '所属模型ID',
				'elem' => 0,
				'foreign' => 'Model',
				'is_contribute' => false,
				'list' => [
					'templet' => 'relation',
					'width' => 180,
				],
				'list_filter' => 'relation',
			],
			'page_title' => [
				'type' => 'string',
				'name' => '网页标题',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => [
					'width' => 150,
				],
				'list_filter' => true,
				'form_group' => 'page',
			],
			'success_message' => [
				'type' => 'string',
				'name' => '成功提示',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => 0,
				'form_group' => 'page',
			],
			'title' => [
				'type' => 'string',
				'name' => '按钮标题',
				'elem' => 'text',
				'is_contribute' => false,
				'list_filter' => true,
				'form_group' => 'tool',
			],
			'action' => [
				'type' => 'string',
				'name' => '独立方法',
				'elem' => 'text',
				'is_contribute' => false,
				'tip' => '独立方法需自行编码',
				'filter' => 'trim',
			],
			'app' => [
				'type' => 'array',
				'name' => '可用应用',
				'elem' => 'checkbox',
				'is_contribute' => false,
			],
			'is_verify' => [
				'type' => 'integer',
				'name' => '审核',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => 'checker',
			],
			'is_btn' => [
				'type' => 'integer',
				'name' => '列表按钮',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => [
					'templet' => 'checker',
					'width' => 120,
				],
			],
			'fields' => [
				'type' => 'array',
				'name' => '表单字段',
				'elem' => 'multiattrs',
				'is_contribute' => false,
				'list' => 0,
				'message' => '建议字段的具体信息都在"字段管理"中设置，这里只选择当前场景需要的字段即可',
			],
			'class' => [
				'type' => 'string',
				'name' => '类名',
				'elem' => 'text',
				'is_contribute' => false,
				'attrs' => [
					'placeholder' => '标签class类名',
				],
				'list' => 0,
				'form_group' => 'tool',
			],
			'icon' => [
				'type' => 'string',
				'name' => '图标',
				'elem' => 'icon',
				'is_contribute' => false,
				'list' => 0,
				'form_group' => 'tool',
			],
			'hover' => [
				'type' => 'string',
				'name' => 'hover名称',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => 0,
				'form_group' => 'tool',
			],
			'where' => [
				'type' => 'string',
				'name' => '渲染条件',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => 0,
				'form_group' => 'tool',
			],
			'where_type' => [
				'type' => 'string',
				'name' => '条件方式',
				'elem' => 'select',
				'is_contribute' => false,
				'options' => [
					'disabled' => '禁用',
					'hidden' => '隐藏',
				],
				'list' => 0,
				'form_group' => 'tool',
			],
			'parent' => [
				'type' => 'string',
				'name' => '父标识',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => 0,
				'tip' => '列表下拉工具按钮时需要',
				'form_group' => 'tool',
			],
			'attrs' => [
				'type' => 'array',
				'name' => '标签属性',
				'elem' => 'keyvalue',
				'is_contribute' => false,
				'list' => 0,
				'form_group' => 'tool',
			],
			'page_tip' => [
				'type' => 'string',
				'name' => '头部提示',
				'elem' => 'textarea',
				'is_contribute' => false,
				'list' => 0,
				'form_group' => 'page',
			],
			'list_order' => [
				'type' => 'integer',
				'name' => '排序权重',
				'elem' => 'number',
				'is_contribute' => false,
				'list' => [
					'width' => 110,
				],
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
		];

		/** 表单分组属性 */
		$this->formGroup = [
			'basic' => '基本信息',
			'tool' => '按钮信息',
			'page' => '页面信息',
		];

		/** 表单触发器属性 */
		$this->formTrigger = [];

		/** 表单验证属性 */
		$this->validate = [
			'var' => [
				[
					'rule' => ['require'],
				],
				[
					'rule' => ['unique', 'form_scene,model_id^var'],
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
			'var' => [
				'is_field' => 1,
				'type' => 'CHAR',
				'length' => '32',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'model_id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => 'index',
			],
			'page_title' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'success_message' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'title' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '128',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'action' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'app' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'is_verify' => [
				'is_field' => 1,
				'type' => 'TINYINT',
				'length' => '1',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => 'index',
			],
			'is_btn' => [
				'is_field' => 1,
				'type' => 'TINYINT',
				'length' => '1',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'fields' => [
				'is_field' => 1,
				'type' => 'TEXT',
				'length' => '',
				'default' => 'none',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'class' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'icon' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'hover' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'where' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '256',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'where_type' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'parent' => [
				'is_field' => 1,
				'type' => 'CHAR',
				'length' => '32',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'attrs' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '512',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'page_tip' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '256',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'list_order' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '11',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => 'index',
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
		];
	}
}
