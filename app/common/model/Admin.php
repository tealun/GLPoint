<?php

/**
 * 自动生成的模型文件2023-02-23 09:35:07，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\AdminTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class Admin extends \woo\common\model\Admin
{
	use traits\AdminTrait;

	/** 模型ID */
	protected $modelId = 7;

	/** 模型名称 */
	public $cname = '管理员';

	/** 主显字段信息 */
	public $display = 'username';

	/** 自定义数据 */
	public $customData = [
		'create' => true,
		'batch_delete' => true,
		'modify' => true,
		'delete' => true,
		'detail' => true,
		'delete_index' => true,
	];

	/** 模型关联信息 */
	public $relationLink = [
		'AdminGroup' => [
			'type' => 'belongsToMany',
			'middle' => 'AdminUseAdminGroup',
		],
		'Department' => [
			'type' => 'belongsTo',
		],
		'AdminLogin' => [
			'type' => 'hasMany',
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
			'username' => [
				'type' => 'string',
				'name' => '用户名',
				'elem' => 'text',
				'is_contribute' => false,
				'require' => true,
				'list' => [
					'templet' => 'username',
					'width' => '160',
					'title' => '账号',
					'fixed' => 'left',
				],
				'list_filter' => true,
			],
			'password' => [
				'type' => 'string',
				'name' => '密码',
				'elem' => 'password',
				'modify_elem' => 0,
				'is_contribute' => false,
				'attrs' => [
					'lay-affix' => 'eye',
				],
				'list' => 0,
				'detail' => 0,
				'tip' => '不修改请保持为空',
				'rsa' => true,
			],
			'salt' => [
				'type' => 'string',
				'name' => '密码盐',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'truename' => [
				'type' => 'string',
				'name' => '真实姓名',
				'elem' => 'text',
				'is_contribute' => false,
				'list_filter' => true,
				'form_group' => 'admin',
			],
			'nickname' => [
				'type' => 'string',
				'name' => '昵称',
				'elem' => 'text',
				'is_contribute' => false,
				'list_filter' => true,
				'form_group' => 'admin',
			],
			'avatar' => [
				'type' => 'string',
				'name' => '头像',
				'elem' => 'image',
				'is_contribute' => false,
				'upload' => [
					'maxSize' => '512',
					'validExt' => 'png|jpg|gif|jpeg',
					'resizeWidth' => '200',
					'resizeHeight' => '200',
					'resizeMethod' => '3',
				],
				'list' => [
					'hide' => true,
				],
				'form_group' => 'admin',
			],
			'mobile' => [
				'type' => 'string',
				'name' => '手机',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => [
					'width' => '110',
				],
				'list_filter' => true,
				'form_group' => 'admin',
			],
			'sex' => [
				'type' => 'integer',
				'name' => '性别',
				'elem' => 'select',
				'is_contribute' => false,
				'options' => [
					'未知',
					'男',
					'女',
					'保密',
				],
				'list' => [
					'templet' => 'options',
					'width' => '80',
				],
				'list_filter' => true,
				'form_group' => 'admin',
			],
			'idcard' => [
				'type' => 'string',
				'name' => '身份证',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => [
					'width' => '160',
				],
				'list_filter' => true,
				'form_group' => 'admin',
			],
			'email' => [
				'type' => 'string',
				'name' => '邮箱',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => [
					'minWidth' => '150',
				],
				'list_filter' => true,
				'form_group' => 'admin',
			],
			'admin_group_id' => [
				'type' => 'none',
				'name' => '所属角色组',
				'elem' => 'xmtree',
				'foreign' => 'AdminGroup',
				'is_contribute' => false,
				'list' => [
					'templet' => 'relation',
					'width' => '180',
				],
				'tip' => '无角色将禁止登录',
			],
			'department_id' => [
				'type' => 'integer',
				'name' => '所属部门',
				'elem' => 'xmtree',
				'foreign' => 'Department',
				'is_contribute' => false,
				'list' => [
					'templet' => 'relation',
					'width' => '110',
				],
			],
			'status' => [
				'type' => 'string',
				'name' => '状态',
				'elem' => 'select',
				'is_contribute' => false,
				'require' => true,
				'options' => [
					'verified' => '已激活',
					'unverified' => '未激活',
					'banned' => '已禁用',
				],
				'list_filter' => true,
			],
			'region' => [
				'type' => 'string',
				'name' => '家庭所在地',
				'elem' => 'cascader',
				'foreign' => 'Region',
				'is_contribute' => false,
				'attrs' => [
					'data-url' => true,
				],
				'list' => [
					'width' => '160',
				],
				'list_filter' => [
					'templet' => 'cascader',
					'attrs' => ['data-url' => true, 'data-nostrict' => true],
				],
				'form_group' => 'admin',
			],
			'address' => [
				'type' => 'string',
				'name' => '详情地址',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => 0,
				'form_group' => 'admin',
			],
			'data_allow' => [
				'type' => 'integer',
				'name' => '独立数据权限',
				'elem' => 'select',
				'is_contribute' => false,
				'options' => [
					0 => '全部数据权限',
					1 => '仅本人数据权限',
					2 => '本部门数据权限',
					3 => '部门及以下数据权限',
					4 => '自定义数据权限',
					5 => '所在顶级及以下部门权限',
					-1 => '继承角色数据权限',
				],
				'list' => [
					'width' => '160',
				],
				'list_filter' => true,
			],
			'custom_data_allow' => [
				'type' => 'string',
				'name' => '自定义数据权限',
				'elem' => 'xmtree',
				'foreign' => 'Department',
				'is_contribute' => false,
				'list' => 0,
			],
			'login_time' => [
				'type' => 'integer',
				'name' => '登录日期',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'templet' => 'datetime',
					'width' => '154',
				],
			],
			'login_ip' => [
				'type' => 'string',
				'name' => '登录IP',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'width' => '130',
				],
			],
			'login_id' => [
				'type' => 'string',
				'name' => '登录SESSID',
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
					'width' => '152',
				],
			],
			'update_time' => [
				'type' => 'integer',
				'name' => '修改日期',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'width' => '152',
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
		$this->formGroup = [
			'basic' => '账号信息',
			'admin' => '用户资料',
		];

		/** 表单触发器属性 */
		$this->formTrigger = [
			'data_allow' => [
				4 => 'custom_data_allow',
			],
		];

		/** 表单验证属性 */
		$this->validate = [
			'username' => [
				[
					'rule' => ['require'],
				],
				[
					'rule' => ['unique', 'admin'],
				],
			],
			'password' => [
				[
					'rule' => ['require'],
					'on' => 'add',
				],
				[
					'rule' => ['length', '6,16'],
				],
			],
			'mobile' => [
				[
					'rule' => ['mobile'],
				],
				[
					'rule' => ['unique', 'admin'],
				],
			],
			'idcard' => [
				[
					'rule' => ['idCard'],
				],
			],
			'email' => [
				[
					'rule' => ['email'],
				],
				[
					'rule' => ['unique', 'admin'],
				],
			],
			'admin_group_id' => [
				[
					'rule' => ['call', 'checkAdminGroup'],
				],
			],
			'status' => [
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
					'field' => 'id',
					'title' => '总人数',
					'type' => 'count',
					'where_type' => '',
					'where' => '',
					'callback' => '',
					'templet' => '',
					'more' => '',
				],
				[
					'field' => 'id',
					'title' => '男性人数',
					'type' => 'count',
					'where_type' => 'where',
					'where' => [
						['sex', '=', 1],
					],
					'callback' => '',
					'templet' => '',
					'more' => '',
				],
				[
					'field' => 'id',
					'title' => '女性人数',
					'type' => 'count',
					'where_type' => 'where',
					'where' => [
						['sex', '=', 2],
					],
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
		$this->formScene = [
			1 => [
				'id' => 1,
				'title' => '',
				'is_verify' => 1,
				'list_order' => 71,
				'icon' => 'layui-icon-password',
				'hover' => '密码',
				'where' => '',
				'action' => 'password',
				'where_type' => '',
				'is_btn' => 1,
				'var' => 'password',
				'fields' => [
					[
						'field' => 'password',
						'elem' => 'password',
						'more_attrs' => [
							'tip' => '',
							'attrs' => ['lay-affix' => 'eye', 'autocomplete' => 'new-password'],
						],
						'validate' => ['require' => '', 'confirm' => 'repassword'],
					],
					[
						'field' => 'repassword',
						'elem' => 'password',
						'more_attrs' => [
							'name' => '确认密码',
							'attrs' => ['lay-affix' => 'eye', 'autocomplete' => 'new-password'],
						],
						'validate' => ['require' => '', 'confirm' => 'password'],
					],
				],
				'parent' => '',
				'app' => [
					'admin',
				],
				'attrs' => '',
				'class' => 'btn-37',
				'page_title' => '修改密码',
				'success_message' => '密码修改成功',
				'page_tip' => '',
			],
		];
	}
}
