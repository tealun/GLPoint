<?php

/**
 * 自动生成的模型文件2021-10-24 01:54:03，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\AdminLoginTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class AdminLogin extends \woo\common\model\AdminLogin
{
	use traits\AdminLoginTrait;

	/** 父模型名 */
	public $parentModel = 'Admin';

	/** 模型名称 */
	public $cname = '登录日志';

	/** 主显字段信息 */
	public $display = 'ip';

	/** 自定义数据 */
	public $customData = [
		'batch_delete' => true,
		'delete' => true,
		'detail' => true,
	];

	/** 模型关联信息 */
	public $relationLink = [
		'Admin' => [
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
				'list' => [],
			],
			'admin_id' => [
				'type' => 'integer',
				'name' => '所属管理员',
				'elem' => 'relation',
				'foreign' => 'Admin',
				'is_contribute' => false,
				'list' => 'relation',
			],
			'ip' => [
				'type' => 'string',
				'name' => '登录IP',
				'elem' => 0,
				'is_contribute' => false,
			],
			'user_agent' => [
				'type' => 'string',
				'name' => '客户端',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'templet' => 'ua',
				],
				'detail' => 'show',
			],
			'summary' => [
				'type' => 'string',
				'name' => '描述',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'minWidth' => '200',
				],
			],
			'username' => [
				'type' => 'string',
				'name' => '输入账号',
				'elem' => 0,
				'is_contribute' => false,
			],
			'region' => [
				'type' => 'string',
				'name' => '登录地址',
				'elem' => 0,
				'is_contribute' => false,
			],
			'is_success' => [
				'type' => 'integer',
				'name' => '是否成功',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'create_time' => [
				'type' => 'integer',
				'name' => '登录时间',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'width' => '144',
				],
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
			'toolbar_options' => [],
			'counter' => [],
			'is_remove_pk' => false,
			'filter_model' => '',
		];
	}
}
