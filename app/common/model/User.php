<?php

/**
 * 自动生成的模型文件2025-06-28 08:50:47，不建议在当前文件中手动写代码；以免重新生成以后被覆盖。
 * 自定义代码应用定义在模型对应的trait文件中【app\common\model\traits\UserTrait】
 */

declare(strict_types=1);

namespace app\common\model;

class User extends \woo\common\model\User
{
	use traits\UserTrait;

	/** 模型ID */
	protected $modelId = 19;

	/** 模型名称 */
	public $cname = '用户';

	/** 主显字段信息 */
	public $display = 'nickname';

	/** 自定义数据 */
	public $customData = [
		'create' => true,
		'modify' => true,
		'delete' => true,
		'detail' => true,
		'batch_delete' => true,
		'delete_index' => true,
	];

	/** 模型关联信息 */
	public $relationLink = [
		'UserGroup' => [
			'type' => 'belongsTo',
		],
		'UserGrade' => [
			'type' => 'belongsTo',
		],
		'UserLogin' => [
			'type' => 'hasMany',
		],
		'Certification' => [
			'type' => 'hasOne',
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
				'is_contribute' => true,
				'require' => true,
				'list' => [
					'templet' => 'username',
					'width' => 160,
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
			'pay_password' => [
				'type' => 'string',
				'name' => '支付密码',
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
				'detail' => 0,
			],
			'user_group_id' => [
				'type' => 'integer',
				'name' => '用户组',
				'elem' => 'relation',
				'foreign' => 'UserGroup',
				'is_contribute' => false,
				'require' => true,
				'list' => 'relation',
			],
			'user_grade_id' => [
				'type' => 'integer',
				'name' => '等级',
				'elem' => 0,
				'foreign' => 'UserGrade',
				'is_contribute' => false,
				'list' => 'relation',
			],
			'nickname' => [
				'type' => 'string',
				'name' => '昵称',
				'elem' => 'text',
				'is_contribute' => true,
				'list_filter' => true,
			],
			'avatar' => [
				'type' => 'string',
				'name' => '头像',
				'elem' => 'image',
				'is_contribute' => false,
				'list' => [
					'templet' => 'avatar',
					'hide' => true,
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
			'is_bind_mobile' => [
				'type' => 'integer',
				'name' => '手机绑定',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => 'checker.show',
			],
			'mobile' => [
				'type' => 'string',
				'name' => '手机',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => [
					'width' => 110,
				],
				'list_filter' => true,
			],
			'is_bind_email' => [
				'type' => 'integer',
				'name' => '邮箱绑定',
				'elem' => 'checker',
				'is_contribute' => false,
				'list' => 'checker.show',
			],
			'email' => [
				'type' => 'string',
				'name' => '邮箱',
				'elem' => 'text',
				'is_contribute' => false,
				'list' => [
					'minWidth' => 115,
				],
				'list_filter' => true,
			],
			'truename' => [
				'type' => 'string',
				'name' => '真实姓名',
				'elem' => 'text',
				'is_contribute' => true,
				'list_filter' => true,
			],
			'sex' => [
				'type' => 'integer',
				'name' => '性别',
				'elem' => 'select',
				'is_contribute' => true,
				'options' => [
					'未知',
					'男',
					'女',
					'保密',
				],
				'list' => [
					'width' => 80,
				],
				'list_filter' => true,
			],
			'birthday' => [
				'type' => 'string',
				'name' => '生日',
				'elem' => 'date',
				'is_contribute' => true,
				'list' => [
					'hide' => true,
				],
				'list_filter' => 'date',
			],
			'region' => [
				'type' => 'string',
				'name' => '所在地区',
				'elem' => 'cascader',
				'foreign' => 'Region',
				'is_contribute' => true,
				'attrs' => [
					'data-url' => true,
				],
				'list' => [
					'width' => 160,
				],
				'list_filter' => [
					'templet' => 'cascader',
					'attrs' => ['data-url' => true],
				],
			],
			'address' => [
				'type' => 'string',
				'name' => '详细地址',
				'elem' => 'text',
				'is_contribute' => true,
				'list' => [
					'hide' => true,
				],
			],
			'summary' => [
				'type' => 'string',
				'name' => '简介',
				'elem' => 'textarea',
				'is_contribute' => true,
				'list' => 0,
			],
			'money' => [
				'type' => 'float',
				'name' => '余额',
				'elem' => 0,
				'is_contribute' => false,
				'list_filter' => 'number_range',
			],
			'score' => [
				'type' => 'float',
				'name' => '积分',
				'elem' => 0,
				'is_contribute' => false,
				'list_filter' => 'number_range',
			],
			'login_time' => [
				'type' => 'integer',
				'name' => '最后登录时间',
				'elem' => 0,
				'is_contribute' => false,
				'list' => [
					'templet' => 'datetime',
					'width' => 146,
				],
			],
			'login_ip' => [
				'type' => 'string',
				'name' => '最后登录IP',
				'elem' => 0,
				'is_contribute' => false,
			],
			'login_id' => [
				'type' => 'string',
				'name' => '最后登录SESS_ID',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'register_ip' => [
				'type' => 'string',
				'name' => '注册IP',
				'elem' => 0,
				'is_contribute' => false,
			],
			'register_type' => [
				'type' => 'string',
				'name' => '注册方式',
				'elem' => 0,
				'is_contribute' => false,
				'options' => [
					'wxmini' => '微信小程序',
					'univerify' => 'APP一键登录',
					'gitee' => '码云',
					'wechat' => '公众号',
					'wechat2' => '微信',
					'weibo' => '微博',
					'qq' => 'QQ',
					'' => '账号输入',
				],
				'list_filter' => true,
			],
			'is_allow_reset' => [
				'type' => 'integer',
				'name' => '是否允许初始化',
				'elem' => 0,
				'is_contribute' => true,
				'list' => 0,
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
			'delete_time' => [
				'type' => 'integer',
				'name' => '删除日期',
				'elem' => 0,
				'is_contribute' => false,
				'list' => 0,
			],
			'department_id' => [
				'type' => 'integer',
				'name' => '所属部门',
				'elem' => 'relation',
				'foreign' => 'department',
				'is_contribute' => false,
				'list' => 'relation',
			],
		];

		/** 表单分组属性 */
		$this->formGroup = [];

		/** 表单触发器属性 */
		$this->formTrigger = [];

		/** 表单验证属性 */
		$this->validate = [
			'username' => [
				[
					'rule' => ['unique', 'user'],
				],
				[
					'rule' => ['require'],
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
				[
					'rule' => ['call', 'checkPwd'],
				],
			],
			'user_group_id' => [
				[
					'rule' => ['require'],
				],
				[
					'rule' => ['gt', 0],
				],
			],
			'status' => [
				[
					'rule' => ['require'],
				],
			],
			'mobile' => [
				[
					'rule' => ['mobile'],
				],
				[
					'rule' => ['call', 'uniqueWithoutEmpty,mobile'],
				],
			],
			'email' => [
				[
					'rule' => ['email'],
				],
				[
					'rule' => ['call', 'uniqueWithoutEmpty,email'],
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
		$this->formScene = [
			2 => [
				'id' => 2,
				'title' => '修改密码',
				'is_verify' => 1,
				'list_order' => 3,
				'icon' => '',
				'hover' => '',
				'where' => '',
				'action' => '',
				'where_type' => '',
				'is_btn' => 1,
				'var' => 'password',
				'fields' => [
					[
						'field' => 'password',
						'elem' => 'password',
						'more_attrs' => ['tip' => ''],
						'validate' => ['require' => '', 'confirm' => 'repassword'],
					],
					[
						'field' => 'repassword',
						'elem' => 'password',
						'more_attrs' => [
							'name' => '确认密码',
							'attrs' => ['lay-affix' => 'eye'],
						],
						'validate' => ['require' => '', 'confirm' => 'password'],
					],
				],
				'parent' => 'more',
				'app' => [
					'admin',
				],
				'attrs' => '',
				'class' => '',
				'page_title' => '修改密码',
				'success_message' => '密码修改成功',
				'page_tip' => '',
			],
			[
				'id' => 3,
				'title' => '支付密码',
				'is_verify' => 1,
				'list_order' => 2,
				'icon' => '',
				'hover' => '',
				'where' => '',
				'action' => '',
				'where_type' => '',
				'is_btn' => 1,
				'var' => 'pay_password',
				'fields' => [
					[
						'field' => 'pay_password',
						'elem' => 'password',
						'more_attrs' => ['tip' => ''],
						'validate' => ['require' => '', 'confirm' => 'repassword'],
					],
					[
						'field' => 'repassword',
						'elem' => 'password',
						'more_attrs' => [
							'name' => '确认密码',
							'attrs' => ['lay-affix' => 'eye'],
						],
						'validate' => ['require' => '', 'confirm' => 'pay_password'],
					],
				],
				'parent' => 'more',
				'app' => [
					'admin',
				],
				'attrs' => '',
				'class' => '',
				'page_title' => '修改支付密码',
				'success_message' => '支付密码修改成功',
				'page_tip' => '',
			],
		];

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
			'username' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => 'unique',
			],
			'password' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '32',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'pay_password' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '32',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'salt' => [
				'is_field' => 1,
				'type' => 'CHAR',
				'length' => '16',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'user_group_id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => 'index',
			],
			'user_grade_id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'nickname' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'avatar' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '128',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'status' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'is_bind_mobile' => [
				'is_field' => 1,
				'type' => 'TINYINT',
				'length' => '1',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'mobile' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '11',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => 'index',
			],
			'is_bind_email' => [
				'is_field' => 1,
				'type' => 'TINYINT',
				'length' => '1',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'email' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => 'index',
			],
			'truename' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'sex' => [
				'is_field' => 1,
				'type' => 'TINYINT',
				'length' => '',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'birthday' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'region' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'address' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'summary' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '512',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'money' => [
				'is_field' => 1,
				'type' => 'DECIMAL',
				'length' => '10,2',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
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
			'login_time' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
			'login_ip' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'login_id' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '32',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'register_ip' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'register_type' => [
				'is_field' => 1,
				'type' => 'VARCHAR',
				'length' => '64',
				'default' => '',
				'is_not_null' => 1,
				'is_unsigned' => 0,
				'is_ai' => 0,
				'index' => '',
			],
			'is_allow_reset' => [
				'is_field' => 1,
				'type' => 'TINYINT',
				'length' => '1',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
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
			'delete_time' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '10',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => 'INDEX',
			],
			'department_id' => [
				'is_field' => 1,
				'type' => 'INT',
				'length' => '',
				'default' => 0,
				'is_not_null' => 1,
				'is_unsigned' => 1,
				'is_ai' => 0,
				'index' => '',
			],
		];
	}
}
