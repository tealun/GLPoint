CREATE TABLE `woo_addon` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '插件目录',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `author` varchar(64) NOT NULL DEFAULT '' COMMENT '作者',
  `version` varchar(64) NOT NULL DEFAULT '' COMMENT '版本',
  `is_verify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否启用',
  `is_disuninstall` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '禁止卸载',
  `describe` text NOT NULL COMMENT '插件描述',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='插件';

--
-- 转存表中的数据 `woo_addon`
--

INSERT INTO `woo_addon` (`id`, `name`, `title`, `author`, `version`, `is_verify`, `is_disuninstall`, `describe`, `admin_id`, `create_time`, `update_time`) VALUES
(1, 'ueditor', 'ueditor富文本', 'WOO官方', '2.0.1', 1, 0, 'Ueditor的上传服务插件', 1, 1746081593, 1746081593);

-- --------------------------------------------------------

--
-- 表的结构 `woo_addon_setting`
--

CREATE TABLE `woo_addon_setting` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `addon_id` int(11) NOT NULL DEFAULT '0' COMMENT '插件ID',
  `var` varchar(64) NOT NULL DEFAULT '' COMMENT '变量名',
  `value` text NOT NULL COMMENT '数据',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '输入类型',
  `options` varchar(512) NOT NULL DEFAULT '' COMMENT '选项',
  `tip` varchar(128) NOT NULL DEFAULT '' COMMENT '提示',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='插件配置';

-- --------------------------------------------------------

--
-- 表的结构 `woo_admin`
--

CREATE TABLE `woo_admin` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(32) NOT NULL DEFAULT '' COMMENT '邮箱',
  `department_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属部门',
  `nickname` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称',
  `status` varchar(16) NOT NULL DEFAULT '' COMMENT '状态',
  `truename` varchar(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `avatar` varchar(128) NOT NULL DEFAULT '' COMMENT '头像',
  `salt` varchar(16) NOT NULL DEFAULT '' COMMENT '密码盐',
  `login_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录日期',
  `login_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '登录IP',
  `login_id` varchar(32) NOT NULL DEFAULT '' COMMENT '登录SESSID',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机',
  `sex` tinyint(4) NOT NULL DEFAULT '0' COMMENT '性别',
  `idcard` char(32) NOT NULL DEFAULT '' COMMENT '身份证',
  `region` varchar(64) NOT NULL DEFAULT '' COMMENT '家庭所在地',
  `address` varchar(128) NOT NULL DEFAULT '' COMMENT '详情地址',
  `data_allow` tinyint(4) NOT NULL DEFAULT '0' COMMENT '独立数据权限',
  `custom_data_allow` varchar(256) NOT NULL DEFAULT '' COMMENT '自定义数据权限',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员';

--
-- 转存表中的数据 `woo_admin`
--

INSERT INTO `woo_admin` (`id`, `username`, `password`, `email`, `department_id`, `nickname`, `status`, `truename`, `avatar`, `salt`, `login_time`, `login_ip`, `login_id`, `mobile`, `sex`, `idcard`, `region`, `address`, `data_allow`, `custom_data_allow`, `create_time`, `update_time`, `delete_time`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@example.com', 1, '', 'verified', '管理员', '', 'salt123', 0, '0.0.0.0', '', '', 0, '', '', '', 0, '', 1740631605, 1740631605, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_admin_group`
--

CREATE TABLE `woo_admin_group` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '角色名',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `family` varchar(256) NOT NULL DEFAULT '' COMMENT '家族',
  `level` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '层级',
  `children_count` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下级数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `dashboard` varchar(64) NOT NULL DEFAULT '' COMMENT '主面板URL',
  `data_allow` tinyint(4) NOT NULL DEFAULT '0' COMMENT '数据权限',
  `custom_data_allow` varchar(256) NOT NULL DEFAULT '' COMMENT '自定义数据权限',
  `is_admin` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '后台登录'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色组';

--
-- 转存表中的数据 `woo_admin_group`
--

INSERT INTO `woo_admin_group` (`id`, `parent_id`, `title`, `list_order`, `family`, `level`, `children_count`, `create_time`, `update_time`, `dashboard`, `data_allow`, `custom_data_allow`, `is_admin`) VALUES
(1, 0, '超级管理', 1, ',1,', 1, 0, 1740631596, 1740631596, '', 0, '', 1),
(2, 0, '管理员', 2, ',2,', 1, 0, 1740632386, 1740632386, '', 3, '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `woo_admin_login`
--

CREATE TABLE `woo_admin_login` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属管理员',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '输入账号',
  `ip` varchar(64) NOT NULL DEFAULT '' COMMENT '登录IP',
  `user_agent` varchar(256) NOT NULL DEFAULT '' COMMENT '客户端',
  `region` varchar(128) NOT NULL DEFAULT '' COMMENT '登录地址',
  `summary` varchar(128) NOT NULL DEFAULT '' COMMENT '描述',
  `is_success` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否成功',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='登录日志';

--
-- 转存表中的数据 `woo_admin_login`
--

INSERT INTO `woo_admin_login` (`id`, `admin_id`, `username`, `ip`, `user_agent`, `region`, `summary`, `is_success`, `create_time`, `update_time`) VALUES
(1, 1, 'admin', '0.0.0.0', 'Mozilla/5.0', '', '登录成功', 1, 1740631619, 1740631619);

-- --------------------------------------------------------

--
-- 表的结构 `woo_admin_rule`
--

CREATE TABLE `woo_admin_rule` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `is_nav` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否显示',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `family` varchar(256) NOT NULL DEFAULT '' COMMENT '家族',
  `level` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '层级',
  `children_count` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下级数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `type` char(32) NOT NULL DEFAULT '' COMMENT '类型',
  `icon` varchar(64) NOT NULL DEFAULT '' COMMENT '图标',
  `addon` varchar(64) NOT NULL DEFAULT '' COMMENT '二级目录',
  `controller` varchar(64) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(64) NOT NULL DEFAULT '' COMMENT '方法',
  `url` varchar(128) NOT NULL DEFAULT '' COMMENT '路由',
  `args` varchar(256) NOT NULL DEFAULT '' COMMENT '参数',
  `open_type` varchar(64) NOT NULL DEFAULT '' COMMENT '打开方式',
  `jianpin` varchar(64) NOT NULL DEFAULT '' COMMENT '简拼',
  `pinyin` varchar(64) NOT NULL DEFAULT '' COMMENT '拼音',
  `rule` varchar(128) NOT NULL DEFAULT '' COMMENT '路由规则',
  `other_name` varchar(64) NOT NULL DEFAULT '' COMMENT '第三方名称',
  `js_func` varchar(64) NOT NULL DEFAULT '' COMMENT '回调事件名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='菜单规则';

--
-- 转存表中的数据 `woo_admin_rule`
--

INSERT INTO `woo_admin_rule` (`id`, `parent_id`, `title`, `is_nav`, `list_order`, `family`, `level`, `children_count`, `create_time`, `update_time`, `type`, `icon`, `addon`, `controller`, `action`, `url`, `args`, `open_type`, `jianpin`, `pinyin`, `rule`, `other_name`, `js_func`) VALUES
(1, 0, '系统', 1, 409, ',1,', 1, 8, 1740631596, 1740631596, 'directory', 'layui-icon-app', '', '', '', '', '', '', 'xt', 'xitong', '', '', ''),
(2, 1, '常规管理', 1, 2, ',1,2,', 2, 5, 1740631596, 1740631596, 'directory', 'layui-icon-console', '', '', '', '', '', '', 'cggl', 'changguiguanli', '', '', ''),
(3, 1, '组织权限', 1, 3, ',1,3,', 2, 5, 1740631596, 1740631596, 'directory', 'layui-icon-auz', '', '', '', '', '', '', 'zzqx', 'zuzhiquanxian', '', '', ''),
(4, 1, '系统管理', 1, 4, ',1,4,', 2, 12, 1740631596, 1740631596, 'directory', 'layui-icon-set', '', '', '', '', '', '', 'xtgl', 'xitongguanli', '', '', ''),
(5, 1, '开发中心', 1, 5, ',1,5,', 2, 9, 1740631596, 1740631596, 'directory', 'layui-icon-engine', '', '', '', '', '', '', 'kfzx', 'kaifazhongxin', '', '', ''),
(6, 2, '系统配置', 1, 6, ',1,2,6,', 3, 7, 1740631596, 1740631596, 'menu', '', '', 'setting', 'set', '', '', '_iframe', 'xtpz', 'xitongpeizhi', 'setting/set', '', ''),
(7, 2, '附件管理', 1, 7, ',1,2,7,', 3, 7, 1740631596, 1740631596, 'menu', '', '', 'attachement', 'index', '', '', '_iframe', 'fjgl', 'fujianguanli', 'attachement/index', '', ''),
(8, 2, '个人信息', 1, 8, ',1,2,8,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'admin', 'home', '', '', '_iframe', 'grxx', 'gerenxinxi', 'admin/home', '', ''),
(9, 3, '管理员管理', 1, 9, ',1,3,9,', 3, 7, 1740631596, 1746064040, 'menu', '', '', 'admin', 'index', '', '', '_iframe', 'yhgl', 'yonghuguanli', 'admin/index', '', ''),
(10, 3, '角色管理', 1, 10, ',1,3,10,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'admin_group', 'index', '', '', '_iframe', 'jsgl', 'jiaoseguanli', 'admin_group/index', '', ''),
(11, 3, '部门管理', 1, 11, ',1,3,11,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'department', 'index', '', '', '_iframe', 'bmgl', 'bumenguanli', 'department/index', '', ''),
(12, 3, '菜单规则', 1, 12, ',1,3,12,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'admin_rule', 'index', '', '', '_iframe', 'qxjd', 'quanxianjiedian', 'admin_rule/index', '', ''),
(13, 3, '权限管理', 1, 13, ',1,3,13,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'power', 'index', '', '', '_iframe', 'qxgl', 'quanxianguanli', 'power/index', '', ''),
(14, 5, '模型管理', 1, 14, ',1,5,14,', 3, 10, 1740631596, 1740631596, 'menu', '', '', 'model', 'index', '', '', '_iframe', 'mxgl', 'moxingguanli', 'model/index', '', ''),
(15, 4, '字典管理', 1, 15, ',1,4,15,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'dictionary', 'index', '', '', '_iframe', 'zdgl', 'zidianguanli', 'dictionary/index', '', ''),
(16, 0, '用户', 1, 16, ',16,', 1, 4, 1740631596, 1740631596, 'directory', 'layui-icon-user', '', '', '', '', '', '', 'hy', 'huiyuan', '', '', ''),
(17, 16, '用户管理', 1, 17, ',16,17,', 2, 5, 1740631596, 1740631596, 'directory', 'layui-icon-user', '', '', '', '', '', '', 'hygl', 'huiyuanguanli', '', '', ''),
(18, 17, '用户分类', 1, 18, ',16,17,18,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'user_group', 'index', '', '', '_iframe', 'hyfl', 'huiyuanfenlei', 'user_group/index', '', ''),
(20, 4, '登录日志', 1, 57, ',1,4,20,', 3, 3, 1740631596, 1740631596, 'menu', '', '', 'admin_login', 'index', '', '', '_iframe', 'dlrz', 'denglurizhi', 'admin_login/index', '', ''),
(21, 0, '示例', 1, 21, ',21,', 1, 4, 1740631596, 1740631596, 'directory', 'woo-icon-youxi', '', '', '', '', '', '', 'sl', 'shili', '', '', ''),
(22, 21, '表单示例', 1, 22, ',21,22,', 2, 5, 1740631596, 1740631596, 'directory', 'layui-icon-form', '', '', '', '', '', '', 'bdsl', 'biaodanshili', '', '', ''),
(23, 22, '表单类型', 1, 23, ',21,22,23,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo1', '', '', '_iframe', 'bdlx', 'biaodanleixing', 'demo/demo1', '', ''),
(25, 4, '操作日志', 1, 47, ',1,4,25,', 3, 3, 1740631596, 1740631596, 'menu', '', '', 'log', 'index', '', '', '_iframe', 'czrz', 'caozuorizhi', 'log/index', '', ''),
(26, 22, '表单分组', 1, 26, ',21,22,26,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo2', '', '', '_iframe', 'bdfz', 'biaodanfenzu', 'demo/demo2', '', ''),
(27, 17, '用户列表', 1, 27, ',16,17,27,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'user', 'index', '', '', '_iframe', 'hylb', 'huiyuanliebiao', 'user/index', '', ''),
(33, 21, '表格示例', 1, 33, ',21,33,', 2, 5, 1740631596, 1740631596, 'directory', 'layui-icon-table', '', '', '', '', '', '', 'bgsl', 'biaogeshili', '', '', ''),
(34, 21, '其他示例', 1, 171, ',21,34,', 2, 7, 1740631596, 1740631596, 'directory', 'layui-icon-light', '', '', '', '', '', '', 'qtsl', 'qitashili', '', '', ''),
(35, 22, '表单触发', 1, 35, ',21,22,35,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo3', '', '', '_iframe', 'bdcf', 'biaodanchufa', 'demo/demo3', '', ''),
(36, 22, '表单布局', 1, 36, ',21,22,36,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo4', '', '', '_iframe', 'bdbj', 'biaodanbuju', 'demo/demo4', '', ''),
(37, 34, '自定义页', 1, 37, ',21,34,37,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo5', '', '', '_iframe', 'zdyy', 'zidingyiye', 'demo/demo5', '', ''),
(39, 2, '配置组管理', 0, 39, ',1,2,39,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'setting_group', 'index', '', '', '_iframe', 'pzzgl', 'peizhizuguanli', 'setting_group/index', '', ''),
(40, 33, '主要功能', 1, 40, ',21,33,40,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo6', '', '', '_iframe', 'zygn', 'zhuyaogongneng', 'demo/demo6', '', ''),
(41, 2, '附件目录', 0, 41, ',1,2,41,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'folder', 'index', '', '', '_iframe', 'fjml', 'fujianmulu', 'folder/index', '', ''),
(42, 4, '地区管理', 0, 63, ',1,4,42,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'region', 'index', '', '', '_iframe', 'dqgl', 'diquguanli', 'region/index', '', ''),
(43, 4, '数据备份', 1, 25, ',1,4,43,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'database', 'index', '', '', '_iframe', 'sjbf', 'shujubeifen', 'database/index', '', ''),
(44, 5, '创控制器', 1, 74, ',1,5,44,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'tool', 'makecontroller', '', '', '_iframe', 'ckzq', 'chuangkongzhiqi', 'tool/makecontroller', '', ''),
(45, 33, '静态数据', 1, 71, ',21,33,45,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo7', '', '', '_iframe', 'jtsj', 'jingtaishuju', 'demo/demo7', '', ''),
(46, 4, '快捷方式', 0, 64, ',1,4,46,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'shortcut', 'index', '', '', '_iframe', 'kjfs', 'kuaijiefangshi', 'shortcut/index', '', ''),
(47, 4, '统计图标', 0, 158, ',1,4,47,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'statistics', 'index', '', '', '_iframe', 'tjtb', 'tongjitubiao', 'statistics/index', '', ''),
(48, 34, '注解功能', 1, 50, ',21,34,48,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo8', '', '', '_iframe', 'zjgn', 'zhujiegongneng', 'demo/demo8', '', ''),
(49, 34, '403页面', 1, 51, ',21,34,49,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo9', '', '', '_iframe', '403ym', '403yemian', 'demo/demo9', '', ''),
(50, 34, '404页面', 1, 175, ',21,34,50,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo10', '', '', '_iframe', '404ym', '404yemian', 'demo/demo10', '', ''),
(51, 34, '500页面', 1, 176, ',21,34,51,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo11', '', '', '_iframe', '500ym', '500yemian', 'demo/demo11', '', ''),
(52, 17, '用户等级', 1, 52, ',16,17,52,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'user_grade', 'index', '', '', '_iframe', 'hydj', 'huiyuandengji', 'user_grade/index', '', ''),
(53, 327, '积分记录', 1, 53, ',327,53,', 2, 7, 1740631596, 1740648497, 'menu', 'layui-icon-list', '', 'user_score', 'index', '', '', '_iframe', 'jfjl', 'jifenjilu', 'user_score/index', '', ''),
(54, 55, '收支记录', 1, 54, ',16,55,54,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'user_money', 'index', '', '', '_iframe', 'szjl', 'shouzhijilu', 'user_money/index', '', ''),
(55, 16, '财务管理', 1, 55, ',16,55,', 2, 2, 1740631596, 1740631596, 'directory', 'layui-icon-rmb', '', '', '', '', '', '', 'cwgl', 'caiwuguanli', '', '', ''),
(56, 55, '充值记录', 1, 56, ',16,55,56,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'recharge', 'index', '', '', '_iframe', 'czjl', 'chongzhijilu', 'recharge/index', '', ''),
(57, 4, '数据导入', 1, 42, ',1,4,57,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'import', 'index', '', '', '_iframe', 'sjdr', 'shujudaoru', 'import/index', '', ''),
(58, 5, '应用中心', 1, 58, ',1,5,58,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'application', 'index', '', '', '_iframe', 'yyzx', 'yingyongzhongxin', 'application/index', '', ''),
(59, 4, '敏感词库', 1, 43, ',1,4,59,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'sensitive', 'index', '', '', '_iframe', 'mgck', 'minganciku', 'sensitive/index', '', ''),
(60, 5, '插件管理', 1, 60, ',1,5,60,', 3, 6, 1740631596, 1740631596, 'menu', '', '', 'addon', 'index', '', '', '_iframe', 'cjgl', 'chajianguanli', 'addon/index', '', ''),
(61, 17, '登录日志', 1, 61, ',16,17,61,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'user_login', 'index', '', '', '_iframe', 'dlrz', 'denglurizhi', 'user_login/index', '', ''),
(62, 17, '实名认证', 0, 62, ',16,17,62,', 3, 7, 1740631596, 1740631596, 'menu', '', '', 'certification', 'index', '', '', '_iframe', 'smrz', 'shimingrenzheng', 'certification/index', '', ''),
(63, 4, '文本审核', 1, 46, ',1,4,63,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'antispam', 'index', '', '', '_iframe', 'wbsh', 'wenbenshenhe', 'antispam/index', '', ''),
(64, 4, '请求日志', 0, 59, ',1,4,64,', 3, 3, 1740631596, 1740631596, 'menu', '', '', 'request_log', 'index', '', '', '_iframe', 'qqrz', 'qingqiurizhi', 'request_log/index', '', ''),
(65, 16, '其他管理', 1, 65, ',16,65,', 2, 4, 1740631596, 1740631596, 'directory', 'layui-icon-rate-half', '', '', '', '', '', '', 'qtgl', 'qitaguanli', '', '', ''),
(66, 65, '用户栏目', 1, 66, ',16,65,66,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'user_menu', 'index', '', '', '_iframe', 'hylm', 'huiyuanlanmu', 'user_menu/index', '', ''),
(67, 65, '用户权限', 1, 67, ',16,65,67,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'user_power', 'index', '', '', '_iframe', 'hyqx', 'huiyuanquanxian', 'user_power/index', '', ''),
(68, 65, '禁止登录', 1, 68, ',16,65,68,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'denied', 'index', '', '', '_iframe', 'jzdl', 'jinzhidenglu', 'denied/index', '', ''),
(69, 65, '签到记录', 1, 69, ',16,65,69,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'sign', 'index', '', '', '_iframe', 'qdjl', 'qiandaojilu', 'sign/index', '', ''),
(70, 5, '后台模板', 1, 80, ',1,5,70,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'tool', 'maketemplate', '', '', '_iframe', 'htmb', 'houtaimoban', 'tool/maketemplate', '', ''),
(71, 33, '订单模板', 1, 72, ',21,33,71,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo61', '', '', '_iframe', 'ddmb', 'dingdanmoban', 'demo/demo61', '', ''),
(72, 33, '图文模板', 1, 166, ',21,33,72,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo62', '', '', '_iframe', 'twmb', 'tuwenmoban', 'demo/demo62', '', ''),
(73, 22, '调拨单录入', 1, 73, ',21,22,73,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'order', '', '', '_iframe', 'dbdlr', 'diaobodanluru', 'demo/order', '', ''),
(74, 5, '更多工具', 0, 164, ',1,5,74,', 3, 3, 1740631596, 1740631596, 'menu', '', '', 'tool', 'index', '', '', '_iframe', 'gdgj', 'gengduogongju', 'tool/index', '', ''),
(80, 5, '插件配置', 0, 70, ',1,5,80,', 3, 6, 1740631596, 1740631596, 'directory', '', '', '', '', '', '', '', 'cjpz', 'chajianpeizhi', '', '', ''),
(81, 21, '菜单示例', 1, 34, ',21,81,', 2, 7, 1740631596, 1740631596, 'directory', 'woo-icon-fenlei', '', '', '', '', '', '_iframe', 'cdsl', 'caidanshili', '', '', ''),
(82, 4, '字典项', 0, 20, ',1,4,82,', 3, 5, 1740631596, 1740631596, 'directory', '', '', '', '', '', '', '', 'zdx', 'zidianxiang', '', '', ''),
(83, 5, '字段', 0, 44, ',1,5,83,', 3, 5, 1740631596, 1740631596, 'directory', '', '', '', '', '', '', '', 'zd', 'ziduan', '', '', ''),
(84, 5, '表单场景', 0, 165, ',1,5,84,', 3, 5, 1740631596, 1740631596, 'menu', '', '', 'form_scene', 'index', '', '', '', 'bdcj', 'biaodanchangjing', 'form_scene/index', '', ''),
(85, 33, '表格按钮', 1, 45, ',21,33,85,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo63', '', '', '_iframe', 'bgan', 'biaogeanniu', 'demo/demo63', '', ''),
(86, 81, '打开外链', 1, 168, ',21,81,86,', 3, 0, 1740631596, 1740631596, 'menu', '', '', '', '', 'https://www.baidu.com', '', '_blank', 'dkwl', 'dakaiwailian', '', '', ''),
(87, 81, '异步请求', 1, 173, ',21,81,87,', 3, 0, 1740631596, 1740631596, 'menu', '', '', '', '', 'tool/clearCache', '', '_ajax', 'ybqq', 'yibuqingqiu', '', '', ''),
(88, 81, '事件回调', 1, 174, ',21,81,88,', 3, 0, 1740631596, 1740631596, 'menu', '', '', '', '', 'event', '', '_event', 'sjhd', 'shijianhuidiao', '', '', 'testClick'),
(89, 81, '独立窗口', 1, 169, ',21,81,89,', 3, 0, 1740631596, 1740631596, 'menu', '', '', '', '', 'test_product/index', '', '_open', 'dlck', 'dulichuangkou', '', '', ''),
(90, 81, '默认方式', 1, 167, ',21,81,90,', 3, 0, 1740631596, 1740631596, 'menu', '', '', '', '', 'test_product/index', '', '_iframe', 'mrfs', 'morenfangshi', '', '', ''),
(91, 81, '嵌入弹窗', 1, 170, ',21,81,91,', 3, 0, 1740631596, 1740631596, 'menu', '', '', '', '', 'test_product/index', '', '_layer', 'qrdc', 'qianrudanchuang', '', '', ''),
(92, 81, '嵌入抽屉', 1, 172, ',21,81,92,', 3, 0, 1740631596, 1740631596, 'menu', '', '', '', '', 'test_product/index', '', '_drawer', 'qrct', 'qianruchouti', '', '', ''),
(93, 34, '大屏示例', 1, 49, ',21,34,93,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'demo12', '', '', '_blank', 'dpsl', 'dapingshili', 'demo/demo12', '', ''),
(94, 34, '扩展图标', 1, 48, ',21,34,94,', 3, 1, 1740631596, 1740631596, 'menu', '', '', 'demo', 'icon', '', '', '_iframe', 'kztb', 'kuozhantubiao', 'demo/icon', '', ''),
(95, 6, '列表', 1, 177, ',1,2,6,95,', 4, 0, 1740632493, 1740632493, 'button', '', '', 'setting', 'index', '', '', '', 'lb', 'liebiao', 'setting/index', '', ''),
(96, 6, '新增', 1, 178, ',1,2,6,96,', 4, 0, 1740632493, 1740632493, 'button', '', '', 'setting', 'create', '', '', '', 'xz', 'xinzeng', 'setting/create', '', ''),
(97, 6, '修改', 1, 179, ',1,2,6,97,', 4, 0, 1740632493, 1740632493, 'button', '', '', 'setting', 'modify', '', '', '', 'xg', 'xiugai', 'setting/modify', '', ''),
(98, 6, '删除', 1, 180, ',1,2,6,98,', 4, 0, 1740632493, 1740632493, 'button', '', '', 'setting', 'delete', '', '', '', 'sc', 'shanchu', 'setting/delete', '', ''),
(99, 6, '排序', 1, 181, ',1,2,6,99,', 4, 0, 1740632493, 1740632493, 'button', '', '', 'setting', 'sort', '', '', '', 'px', 'paixu', 'setting/sort', '', ''),
(100, 6, '回收站', 1, 182, ',1,2,6,100,', 4, 0, 1740632493, 1740632493, 'button', '', '', 'setting', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'setting/deleteindex', '', ''),
(101, 6, '配置', 1, 183, ',1,2,6,101,', 4, 0, 1740632493, 1740632493, 'button', '', '', 'setting', 'set', '', '', '', 'pz', 'peizhi', 'setting/set', '', ''),
(102, 42, '列表', 1, 184, ',1,4,42,102,', 4, 0, 1740632494, 1740632494, 'button', '', '', 'region', 'index', '', '', '', 'lb', 'liebiao', 'region/index', '', ''),
(103, 42, '新增', 1, 185, ',1,4,42,103,', 4, 0, 1740632494, 1740632494, 'button', '', '', 'region', 'create', '', '', '', 'xz', 'xinzeng', 'region/create', '', ''),
(104, 42, '修改', 1, 186, ',1,4,42,104,', 4, 0, 1740632494, 1740632494, 'button', '', '', 'region', 'modify', '', '', '', 'xg', 'xiugai', 'region/modify', '', ''),
(105, 42, '删除', 1, 187, ',1,4,42,105,', 4, 0, 1740632494, 1740632494, 'button', '', '', 'region', 'delete', '', '', '', 'sc', 'shanchu', 'region/delete', '', ''),
(106, 42, '排序', 1, 188, ',1,4,42,106,', 4, 0, 1740632494, 1740632494, 'button', '', '', 'region', 'sort', '', '', '', 'px', 'paixu', 'region/sort', '', ''),
(107, 52, '列表', 1, 189, ',16,17,52,107,', 4, 0, 1740632494, 1740632494, 'button', '', '', 'user_grade', 'index', '', '', '', 'lb', 'liebiao', 'user_grade/index', '', ''),
(108, 52, '新增', 1, 190, ',16,17,52,108,', 4, 0, 1740632494, 1740632494, 'button', '', '', 'user_grade', 'create', '', '', '', 'xz', 'xinzeng', 'user_grade/create', '', ''),
(109, 52, '修改', 1, 191, ',16,17,52,109,', 4, 0, 1740632494, 1740632494, 'button', '', '', 'user_grade', 'modify', '', '', '', 'xg', 'xiugai', 'user_grade/modify', '', ''),
(110, 52, '删除', 1, 192, ',16,17,52,110,', 4, 0, 1740632494, 1740632494, 'button', '', '', 'user_grade', 'delete', '', '', '', 'sc', 'shanchu', 'user_grade/delete', '', ''),
(111, 52, '排序', 1, 193, ',16,17,52,111,', 4, 0, 1740632494, 1740632494, 'button', '', '', 'user_grade', 'sort', '', '', '', 'px', 'paixu', 'user_grade/sort', '', ''),
(112, 43, '数据管理', 1, 194, ',1,4,43,112,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'database', 'index', '', '', '', 'sjgl', 'shujuguanli', 'database/index', '', ''),
(113, 43, '删除备份', 1, 195, ',1,4,43,113,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'database', 'delete', '', '', '', 'scbf', 'shanchubeifen', 'database/delete', '', ''),
(114, 43, '备份下载', 1, 196, ',1,4,43,114,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'database', 'download', '', '', '', 'bfxz', 'beifenxiazai', 'database/download', '', ''),
(115, 43, '备份', 1, 197, ',1,4,43,115,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'database', 'backup', '', '', '', 'bf', 'beifen', 'database/backup', '', ''),
(116, 43, '修复表', 1, 198, ',1,4,43,116,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'database', 'repair', '', '', '', 'xfb', 'xiufubiao', 'database/repair', '', ''),
(117, 43, '优化表', 1, 199, ',1,4,43,117,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'database', 'optimize', '', '', '', 'yhb', 'youhuabiao', 'database/optimize', '', ''),
(118, 60, '列表', 1, 200, ',1,5,60,118,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'addon', 'index', '', '', '', 'lb', 'liebiao', 'addon/index', '', ''),
(119, 60, '新增', 1, 201, ',1,5,60,119,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'addon', 'create', '', '', '', 'xz', 'xinzeng', 'addon/create', '', ''),
(120, 60, '修改', 1, 202, ',1,5,60,120,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'addon', 'modify', '', '', '', 'xg', 'xiugai', 'addon/modify', '', ''),
(121, 60, '排序', 1, 203, ',1,5,60,121,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'addon', 'sort', '', '', '', 'px', 'paixu', 'addon/sort', '', ''),
(122, 60, '安装插件', 1, 204, ',1,5,60,122,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'addon', 'install', '', '', '', 'azcj', 'anzhuangchajian', 'addon/install', '', ''),
(123, 60, '卸载插件', 1, 205, ',1,5,60,123,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'addon', 'uninstall', '', '', '', 'xzcj', 'xiezaichajian', 'addon/uninstall', '', ''),
(124, 11, '列表', 1, 206, ',1,3,11,124,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'department', 'index', '', '', '', 'lb', 'liebiao', 'department/index', '', ''),
(125, 11, '新增', 1, 207, ',1,3,11,125,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'department', 'create', '', '', '', 'xz', 'xinzeng', 'department/create', '', ''),
(126, 11, '修改', 1, 208, ',1,3,11,126,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'department', 'modify', '', '', '', 'xg', 'xiugai', 'department/modify', '', ''),
(127, 11, '删除', 1, 209, ',1,3,11,127,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'department', 'delete', '', '', '', 'sc', 'shanchu', 'department/delete', '', ''),
(128, 11, '排序', 1, 210, ',1,3,11,128,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'department', 'sort', '', '', '', 'px', 'paixu', 'department/sort', '', ''),
(129, 25, '列表', 1, 211, ',1,4,25,129,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'log', 'index', '', '', '', 'lb', 'liebiao', 'log/index', '', ''),
(130, 25, '删除', 1, 212, ',1,4,25,130,', 4, 0, 1740632495, 1740632495, 'button', '', '', 'log', 'delete', '', '', '', 'sc', 'shanchu', 'log/delete', '', ''),
(131, 25, '排序', 1, 213, ',1,4,25,131,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'log', 'sort', '', '', '', 'px', 'paixu', 'log/sort', '', ''),
(132, 57, '列表', 1, 214, ',1,4,57,132,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'import', 'index', '', '', '', 'lb', 'liebiao', 'import/index', '', ''),
(133, 57, '新增', 1, 215, ',1,4,57,133,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'import', 'create', '', '', '', 'xz', 'xinzeng', 'import/create', '', ''),
(134, 57, '修改', 1, 216, ',1,4,57,134,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'import', 'modify', '', '', '', 'xg', 'xiugai', 'import/modify', '', ''),
(135, 57, '删除', 1, 217, ',1,4,57,135,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'import', 'delete', '', '', '', 'sc', 'shanchu', 'import/delete', '', ''),
(136, 57, '排序', 1, 218, ',1,4,57,136,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'import', 'sort', '', '', '', 'px', 'paixu', 'import/sort', '', ''),
(137, 57, '导入', 1, 219, ',1,4,57,137,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'import', 'preview', '', '', '', 'dr', 'daoru', 'import/preview', '', ''),
(138, 7, '列表', 1, 220, ',1,2,7,138,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'attachement', 'index', '', '', '', 'lb', 'liebiao', 'attachement/index', '', ''),
(139, 7, '新增', 1, 221, ',1,2,7,139,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'attachement', 'create', '', '', '', 'xz', 'xinzeng', 'attachement/create', '', ''),
(140, 7, '修改', 1, 222, ',1,2,7,140,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'attachement', 'modify', '', '', '', 'xg', 'xiugai', 'attachement/modify', '', ''),
(141, 7, '删除', 1, 223, ',1,2,7,141,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'attachement', 'delete', '', '', '', 'sc', 'shanchu', 'attachement/delete', '', ''),
(142, 7, '排序', 1, 224, ',1,2,7,142,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'attachement', 'sort', '', '', '', 'px', 'paixu', 'attachement/sort', '', ''),
(143, 7, '回收站', 1, 225, ',1,2,7,143,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'attachement', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'attachement/deleteindex', '', ''),
(144, 7, '上传', 1, 226, ',1,2,7,144,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'attachement', 'upload', '', '', '', 'sc', 'shangchuan', 'attachement/upload', '', ''),
(145, 66, '列表', 1, 227, ',16,65,66,145,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'user_menu', 'index', '', '', '', 'lb', 'liebiao', 'user_menu/index', '', ''),
(146, 66, '新增', 1, 228, ',16,65,66,146,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'user_menu', 'create', '', '', '', 'xz', 'xinzeng', 'user_menu/create', '', ''),
(147, 66, '修改', 1, 229, ',16,65,66,147,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'user_menu', 'modify', '', '', '', 'xg', 'xiugai', 'user_menu/modify', '', ''),
(148, 66, '删除', 1, 230, ',16,65,66,148,', 4, 0, 1740632496, 1740632496, 'button', '', '', 'user_menu', 'delete', '', '', '', 'sc', 'shanchu', 'user_menu/delete', '', ''),
(149, 66, '排序', 1, 231, ',16,65,66,149,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'user_menu', 'sort', '', '', '', 'px', 'paixu', 'user_menu/sort', '', ''),
(150, 54, '列表', 1, 232, ',16,55,54,150,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'user_money', 'index', '', '', '', 'lb', 'liebiao', 'user_money/index', '', ''),
(151, 54, '新增', 1, 233, ',16,55,54,151,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'user_money', 'create', '', '', '', 'xz', 'xinzeng', 'user_money/create', '', ''),
(152, 54, '修改', 1, 234, ',16,55,54,152,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'user_money', 'modify', '', '', '', 'xg', 'xiugai', 'user_money/modify', '', ''),
(153, 54, '删除', 1, 235, ',16,55,54,153,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'user_money', 'delete', '', '', '', 'sc', 'shanchu', 'user_money/delete', '', ''),
(154, 54, '排序', 1, 236, ',16,55,54,154,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'user_money', 'sort', '', '', '', 'px', 'paixu', 'user_money/sort', '', ''),
(155, 54, '回收站', 1, 237, ',16,55,54,155,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'user_money', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'user_money/deleteindex', '', ''),
(156, 14, '列表', 1, 238, ',1,5,14,156,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'model', 'index', '', '', '', 'lb', 'liebiao', 'model/index', '', ''),
(157, 14, '新增', 1, 239, ',1,5,14,157,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'model', 'create', '', '', '', 'xz', 'xinzeng', 'model/create', '', ''),
(158, 14, '修改', 1, 240, ',1,5,14,158,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'model', 'modify', '', '', '', 'xg', 'xiugai', 'model/modify', '', ''),
(159, 14, '删除', 1, 241, ',1,5,14,159,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'model', 'delete', '', '', '', 'sc', 'shanchu', 'model/delete', '', ''),
(160, 14, '排序', 1, 242, ',1,5,14,160,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'model', 'sort', '', '', '', 'px', 'paixu', 'model/sort', '', ''),
(161, 14, '从数据表生成', 1, 243, ',1,5,14,161,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'model', 'createformtable', '', '', '', 'csjbsc', 'congshujubiaoshengcheng', 'model/createformtable', '', ''),
(162, 14, '生成模型', 1, 244, ',1,5,14,162,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'model', 'createmodel', '', '', '', 'scmx', 'shengchengmoxing', 'model/createmodel', '', ''),
(163, 14, '模型导出', 1, 245, ',1,5,14,163,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'model', 'exportdata', '', '', '', 'mxdc', 'moxingdaochu', 'model/exportdata', '', ''),
(164, 14, '模型升级', 1, 246, ',1,5,14,164,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'model', 'importdata', '', '', '', 'mxsj', 'moxingshengji', 'model/importdata', '', ''),
(165, 14, '下载升级包', 1, 247, ',1,5,14,165,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'model', 'exportzip', '', '', '', 'xzsjb', 'xiazaishengjibao', 'model/exportzip', '', ''),
(166, 84, '列表', 1, 248, ',1,5,84,166,', 4, 0, 1740632497, 1740632497, 'button', '', '', 'form_scene', 'index', '', '', '', 'lb', 'liebiao', 'form_scene/index', '', ''),
(167, 84, '新增', 1, 249, ',1,5,84,167,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'form_scene', 'create', '', '', '', 'xz', 'xinzeng', 'form_scene/create', '', ''),
(168, 84, '修改', 1, 250, ',1,5,84,168,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'form_scene', 'modify', '', '', '', 'xg', 'xiugai', 'form_scene/modify', '', ''),
(169, 84, '删除', 1, 251, ',1,5,84,169,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'form_scene', 'delete', '', '', '', 'sc', 'shanchu', 'form_scene/delete', '', ''),
(170, 84, '排序', 1, 252, ',1,5,84,170,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'form_scene', 'sort', '', '', '', 'px', 'paixu', 'form_scene/sort', '', ''),
(171, 46, '列表', 1, 253, ',1,4,46,171,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'shortcut', 'index', '', '', '', 'lb', 'liebiao', 'shortcut/index', '', ''),
(172, 46, '新增', 1, 254, ',1,4,46,172,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'shortcut', 'create', '', '', '', 'xz', 'xinzeng', 'shortcut/create', '', ''),
(173, 46, '修改', 1, 255, ',1,4,46,173,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'shortcut', 'modify', '', '', '', 'xg', 'xiugai', 'shortcut/modify', '', ''),
(174, 46, '删除', 1, 256, ',1,4,46,174,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'shortcut', 'delete', '', '', '', 'sc', 'shanchu', 'shortcut/delete', '', ''),
(175, 46, '排序', 1, 257, ',1,4,46,175,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'shortcut', 'sort', '', '', '', 'px', 'paixu', 'shortcut/sort', '', ''),
(176, 46, '回收站', 1, 258, ',1,4,46,176,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'shortcut', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'shortcut/deleteindex', '', ''),
(177, 18, '列表', 1, 259, ',16,17,18,177,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'user_group', 'index', '', '', '', 'lb', 'liebiao', 'user_group/index', '', ''),
(178, 18, '新增', 1, 260, ',16,17,18,178,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'user_group', 'create', '', '', '', 'xz', 'xinzeng', 'user_group/create', '', ''),
(179, 18, '修改', 1, 261, ',16,17,18,179,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'user_group', 'modify', '', '', '', 'xg', 'xiugai', 'user_group/modify', '', ''),
(180, 18, '删除', 1, 262, ',16,17,18,180,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'user_group', 'delete', '', '', '', 'sc', 'shanchu', 'user_group/delete', '', ''),
(181, 18, '排序', 1, 263, ',16,17,18,181,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'user_group', 'sort', '', '', '', 'px', 'paixu', 'user_group/sort', '', ''),
(182, 61, '列表', 1, 264, ',16,17,61,182,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'user_login', 'index', '', '', '', 'lb', 'liebiao', 'user_login/index', '', ''),
(183, 61, '新增', 1, 265, ',16,17,61,183,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'user_login', 'create', '', '', '', 'xz', 'xinzeng', 'user_login/create', '', ''),
(184, 61, '修改', 1, 266, ',16,17,61,184,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'user_login', 'modify', '', '', '', 'xg', 'xiugai', 'user_login/modify', '', ''),
(185, 61, '删除', 1, 267, ',16,17,61,185,', 4, 0, 1740632498, 1740632498, 'button', '', '', 'user_login', 'delete', '', '', '', 'sc', 'shanchu', 'user_login/delete', '', ''),
(186, 61, '排序', 1, 268, ',16,17,61,186,', 4, 0, 1740632499, 1740632499, 'button', '', '', 'user_login', 'sort', '', '', '', 'px', 'paixu', 'user_login/sort', '', ''),
(187, 13, '授权', 1, 269, ',1,3,13,187,', 4, 0, 1740632499, 1740632499, 'button', '', '', 'power', 'index', '', '', '', 'sq', 'shouquan', 'power/index', '', ''),
(188, 27, '列表', 1, 270, ',16,17,27,188,', 4, 0, 1740632499, 1740632499, 'button', '', '', 'user', 'index', '', '', '', 'lb', 'liebiao', 'user/index', '', ''),
(189, 27, '新增', 1, 271, ',16,17,27,189,', 4, 0, 1740632499, 1740632499, 'button', '', '', 'user', 'create', '', '', '', 'xz', 'xinzeng', 'user/create', '', ''),
(190, 27, '修改', 1, 272, ',16,17,27,190,', 4, 0, 1740632499, 1740632499, 'button', '', '', 'user', 'modify', '', '', '', 'xg', 'xiugai', 'user/modify', '', ''),
(191, 27, '删除', 1, 273, ',16,17,27,191,', 4, 0, 1740632499, 1740632499, 'button', '', '', 'user', 'delete', '', '', '', 'sc', 'shanchu', 'user/delete', '', ''),
(192, 27, '排序', 1, 274, ',16,17,27,192,', 4, 0, 1740632499, 1740632499, 'button', '', '', 'user', 'sort', '', '', '', 'px', 'paixu', 'user/sort', '', ''),
(193, 27, '回收站', 1, 275, ',16,17,27,193,', 4, 0, 1740632499, 1740632499, 'button', '', '', 'user', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'user/deleteindex', '', ''),
(194, 67, '授权', 1, 276, ',16,65,67,194,', 4, 0, 1740632499, 1740632499, 'button', '', '', 'user_power', 'index', '', '', '', 'sq', 'shouquan', 'user_power/index', '', ''),
(195, 58, '列表', 1, 277, ',1,5,58,195,', 4, 0, 1740632499, 1740632499, 'button', '', '', 'application', 'index', '', '', '', 'lb', 'liebiao', 'application/index', '', ''),
(196, 58, '新增', 1, 278, ',1,5,58,196,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'application', 'create', '', '', '', 'xz', 'xinzeng', 'application/create', '', ''),
(197, 58, '修改', 1, 279, ',1,5,58,197,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'application', 'modify', '', '', '', 'xg', 'xiugai', 'application/modify', '', ''),
(198, 58, '排序', 1, 280, ',1,5,58,198,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'application', 'sort', '', '', '', 'px', 'paixu', 'application/sort', '', ''),
(199, 58, '安装应用', 1, 281, ',1,5,58,199,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'application', 'install', '', '', '', 'azyy', 'anzhuangyingyong', 'application/install', '', ''),
(200, 58, '卸载应用', 1, 282, ',1,5,58,200,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'application', 'uninstall', '', '', '', 'xzyy', 'xiezaiyingyong', 'application/uninstall', '', ''),
(201, 9, '列表', 1, 283, ',1,3,9,201,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'admin', 'index', '', '', '', 'lb', 'liebiao', 'admin/index', '', ''),
(202, 9, '新增', 1, 284, ',1,3,9,202,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'admin', 'create', '', '', '', 'xz', 'xinzeng', 'admin/create', '', ''),
(203, 9, '修改', 1, 285, ',1,3,9,203,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'admin', 'modify', '', '', '', 'xg', 'xiugai', 'admin/modify', '', ''),
(204, 9, '删除', 1, 286, ',1,3,9,204,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'admin', 'delete', '', '', '', 'sc', 'shanchu', 'admin/delete', '', ''),
(205, 9, '排序', 1, 287, ',1,3,9,205,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'admin', 'sort', '', '', '', 'px', 'paixu', 'admin/sort', '', ''),
(206, 9, '回收站', 1, 288, ',1,3,9,206,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'admin', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'admin/deleteindex', '', ''),
(207, 9, '修改密码', 1, 289, ',1,3,9,207,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'admin', 'password', '', '', '', 'xgmm', 'xiugaimima', 'admin/password', '', ''),
(208, 8, '个人信息', 1, 290, ',1,2,8,208,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'admin', 'home', '', '', '', 'grxx', 'gerenxinxi', 'admin/home', '', ''),
(209, 69, '列表', 1, 291, ',16,65,69,209,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'sign', 'index', '', '', '', 'lb', 'liebiao', 'sign/index', '', ''),
(210, 69, '新增', 1, 292, ',16,65,69,210,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'sign', 'create', '', '', '', 'xz', 'xinzeng', 'sign/create', '', ''),
(211, 69, '修改', 1, 293, ',16,65,69,211,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'sign', 'modify', '', '', '', 'xg', 'xiugai', 'sign/modify', '', ''),
(212, 69, '删除', 1, 294, ',16,65,69,212,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'sign', 'delete', '', '', '', 'sc', 'shanchu', 'sign/delete', '', ''),
(213, 69, '排序', 1, 295, ',16,65,69,213,', 4, 0, 1740632500, 1740632500, 'button', '', '', 'sign', 'sort', '', '', '', 'px', 'paixu', 'sign/sort', '', ''),
(214, 53, '列表', 1, 296, ',327,53,214,', 3, 0, 1740632500, 1740632500, 'button', '', '', 'user_score', 'index', '', '', '', 'lb', 'liebiao', 'user_score/index', '', ''),
(215, 53, '新增', 1, 297, ',327,53,215,', 3, 0, 1740632500, 1740632500, 'button', '', '', 'user_score', 'create', '', '', '', 'xz', 'xinzeng', 'user_score/create', '', ''),
(216, 53, '修改', 1, 298, ',327,53,216,', 3, 0, 1740632500, 1740632500, 'button', '', '', 'user_score', 'modify', '', '', '', 'xg', 'xiugai', 'user_score/modify', '', ''),
(217, 53, '删除', 1, 299, ',327,53,217,', 3, 0, 1740632501, 1740632501, 'button', '', '', 'user_score', 'delete', '', '', '', 'sc', 'shanchu', 'user_score/delete', '', ''),
(218, 53, '排序', 1, 300, ',327,53,218,', 3, 0, 1740632501, 1740632501, 'button', '', '', 'user_score', 'sort', '', '', '', 'px', 'paixu', 'user_score/sort', '', ''),
(219, 53, '回收站', 1, 301, ',327,53,219,', 3, 0, 1740632501, 1740632501, 'button', '', '', 'user_score', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'user_score/deleteindex', '', ''),
(220, 20, '列表', 1, 302, ',1,4,20,220,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'admin_login', 'index', '', '', '', 'lb', 'liebiao', 'admin_login/index', '', ''),
(221, 20, '删除', 1, 303, ',1,4,20,221,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'admin_login', 'delete', '', '', '', 'sc', 'shanchu', 'admin_login/delete', '', ''),
(222, 20, '排序', 1, 304, ',1,4,20,222,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'admin_login', 'sort', '', '', '', 'px', 'paixu', 'admin_login/sort', '', ''),
(223, 62, '列表', 1, 305, ',16,17,62,223,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'certification', 'index', '', '', '', 'lb', 'liebiao', 'certification/index', '', ''),
(224, 62, '新增', 1, 306, ',16,17,62,224,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'certification', 'create', '', '', '', 'xz', 'xinzeng', 'certification/create', '', ''),
(225, 62, '修改', 1, 307, ',16,17,62,225,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'certification', 'modify', '', '', '', 'xg', 'xiugai', 'certification/modify', '', ''),
(226, 62, '删除', 1, 308, ',16,17,62,226,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'certification', 'delete', '', '', '', 'sc', 'shanchu', 'certification/delete', '', ''),
(227, 62, '排序', 1, 309, ',16,17,62,227,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'certification', 'sort', '', '', '', 'px', 'paixu', 'certification/sort', '', ''),
(228, 62, '回收站', 1, 310, ',16,17,62,228,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'certification', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'certification/deleteindex', '', ''),
(229, 62, '认证', 1, 311, ',16,17,62,229,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'certification', 'cert', '', '', '', 'rz', 'renzheng', 'certification/cert', '', ''),
(230, 59, '列表', 1, 312, ',1,4,59,230,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'sensitive', 'index', '', '', '', 'lb', 'liebiao', 'sensitive/index', '', ''),
(231, 59, '新增', 1, 313, ',1,4,59,231,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'sensitive', 'create', '', '', '', 'xz', 'xinzeng', 'sensitive/create', '', ''),
(232, 59, '修改', 1, 314, ',1,4,59,232,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'sensitive', 'modify', '', '', '', 'xg', 'xiugai', 'sensitive/modify', '', ''),
(233, 59, '删除', 1, 315, ',1,4,59,233,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'sensitive', 'delete', '', '', '', 'sc', 'shanchu', 'sensitive/delete', '', ''),
(234, 59, '排序', 1, 316, ',1,4,59,234,', 4, 0, 1740632501, 1740632501, 'button', '', '', 'sensitive', 'sort', '', '', '', 'px', 'paixu', 'sensitive/sort', '', ''),
(235, 12, '列表', 1, 317, ',1,3,12,235,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'admin_rule', 'index', '', '', '', 'lb', 'liebiao', 'admin_rule/index', '', ''),
(236, 12, '新增', 1, 318, ',1,3,12,236,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'admin_rule', 'create', '', '', '', 'xz', 'xinzeng', 'admin_rule/create', '', ''),
(237, 12, '修改', 1, 319, ',1,3,12,237,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'admin_rule', 'modify', '', '', '', 'xg', 'xiugai', 'admin_rule/modify', '', ''),
(238, 12, '删除', 1, 320, ',1,3,12,238,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'admin_rule', 'delete', '', '', '', 'sc', 'shanchu', 'admin_rule/delete', '', ''),
(239, 12, '排序', 1, 321, ',1,3,12,239,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'admin_rule', 'sort', '', '', '', 'px', 'paixu', 'admin_rule/sort', '', ''),
(240, 12, '生成按钮', 1, 322, ',1,3,12,240,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'admin_rule', 'start', '', '', '', 'scan', 'shengchenganniu', 'admin_rule/start', '', ''),
(241, 41, '列表', 1, 323, ',1,2,41,241,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'folder', 'index', '', '', '', 'lb', 'liebiao', 'folder/index', '', ''),
(242, 41, '新增', 1, 324, ',1,2,41,242,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'folder', 'create', '', '', '', 'xz', 'xinzeng', 'folder/create', '', ''),
(243, 41, '修改', 1, 325, ',1,2,41,243,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'folder', 'modify', '', '', '', 'xg', 'xiugai', 'folder/modify', '', ''),
(244, 41, '删除', 1, 326, ',1,2,41,244,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'folder', 'delete', '', '', '', 'sc', 'shanchu', 'folder/delete', '', ''),
(245, 41, '排序', 1, 327, ',1,2,41,245,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'folder', 'sort', '', '', '', 'px', 'paixu', 'folder/sort', '', ''),
(246, 41, '回收站', 1, 328, ',1,2,41,246,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'folder', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'folder/deleteindex', '', ''),
(247, 82, '列表', 1, 329, ',1,4,82,247,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'dictionary_item', 'index', '', '', '', 'lb', 'liebiao', 'dictionary_item/index', '', ''),
(248, 82, '新增', 1, 330, ',1,4,82,248,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'dictionary_item', 'create', '', '', '', 'xz', 'xinzeng', 'dictionary_item/create', '', ''),
(249, 82, '修改', 1, 331, ',1,4,82,249,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'dictionary_item', 'modify', '', '', '', 'xg', 'xiugai', 'dictionary_item/modify', '', ''),
(250, 82, '删除', 1, 332, ',1,4,82,250,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'dictionary_item', 'delete', '', '', '', 'sc', 'shanchu', 'dictionary_item/delete', '', ''),
(251, 82, '排序', 1, 333, ',1,4,82,251,', 4, 0, 1740632502, 1740632502, 'button', '', '', 'dictionary_item', 'sort', '', '', '', 'px', 'paixu', 'dictionary_item/sort', '', ''),
(252, 47, '列表', 1, 334, ',1,4,47,252,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'statistics', 'index', '', '', '', 'lb', 'liebiao', 'statistics/index', '', ''),
(253, 47, '新增', 1, 335, ',1,4,47,253,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'statistics', 'create', '', '', '', 'xz', 'xinzeng', 'statistics/create', '', ''),
(254, 47, '修改', 1, 336, ',1,4,47,254,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'statistics', 'modify', '', '', '', 'xg', 'xiugai', 'statistics/modify', '', ''),
(255, 47, '删除', 1, 337, ',1,4,47,255,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'statistics', 'delete', '', '', '', 'sc', 'shanchu', 'statistics/delete', '', ''),
(256, 47, '排序', 1, 338, ',1,4,47,256,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'statistics', 'sort', '', '', '', 'px', 'paixu', 'statistics/sort', '', ''),
(257, 47, '回收站', 1, 339, ',1,4,47,257,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'statistics', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'statistics/deleteindex', '', ''),
(258, 83, '列表', 1, 340, ',1,5,83,258,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'field', 'index', '', '', '', 'lb', 'liebiao', 'field/index', '', ''),
(259, 83, '新增', 1, 341, ',1,5,83,259,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'field', 'create', '', '', '', 'xz', 'xinzeng', 'field/create', '', ''),
(260, 83, '修改', 1, 342, ',1,5,83,260,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'field', 'modify', '', '', '', 'xg', 'xiugai', 'field/modify', '', ''),
(261, 83, '删除', 1, 343, ',1,5,83,261,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'field', 'delete', '', '', '', 'sc', 'shanchu', 'field/delete', '', ''),
(262, 83, '排序', 1, 344, ',1,5,83,262,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'field', 'sort', '', '', '', 'px', 'paixu', 'field/sort', '', ''),
(263, 56, '列表', 1, 345, ',16,55,56,263,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'recharge', 'index', '', '', '', 'lb', 'liebiao', 'recharge/index', '', ''),
(264, 56, '新增', 1, 346, ',16,55,56,264,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'recharge', 'create', '', '', '', 'xz', 'xinzeng', 'recharge/create', '', ''),
(265, 56, '修改', 1, 347, ',16,55,56,265,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'recharge', 'modify', '', '', '', 'xg', 'xiugai', 'recharge/modify', '', ''),
(266, 56, '删除', 1, 348, ',16,55,56,266,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'recharge', 'delete', '', '', '', 'sc', 'shanchu', 'recharge/delete', '', ''),
(267, 56, '排序', 1, 349, ',16,55,56,267,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'recharge', 'sort', '', '', '', 'px', 'paixu', 'recharge/sort', '', ''),
(268, 56, '回收站', 1, 350, ',16,55,56,268,', 4, 0, 1740632503, 1740632503, 'button', '', '', 'recharge', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'recharge/deleteindex', '', ''),
(269, 39, '列表', 1, 351, ',1,2,39,269,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'setting_group', 'index', '', '', '', 'lb', 'liebiao', 'setting_group/index', '', ''),
(270, 39, '新增', 1, 352, ',1,2,39,270,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'setting_group', 'create', '', '', '', 'xz', 'xinzeng', 'setting_group/create', '', ''),
(271, 39, '修改', 1, 353, ',1,2,39,271,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'setting_group', 'modify', '', '', '', 'xg', 'xiugai', 'setting_group/modify', '', ''),
(272, 39, '删除', 1, 354, ',1,2,39,272,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'setting_group', 'delete', '', '', '', 'sc', 'shanchu', 'setting_group/delete', '', ''),
(273, 39, '排序', 1, 355, ',1,2,39,273,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'setting_group', 'sort', '', '', '', 'px', 'paixu', 'setting_group/sort', '', ''),
(274, 39, '回收站', 1, 356, ',1,2,39,274,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'setting_group', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'setting_group/deleteindex', '', ''),
(275, 10, '列表', 1, 357, ',1,3,10,275,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'admin_group', 'index', '', '', '', 'lb', 'liebiao', 'admin_group/index', '', ''),
(276, 10, '新增', 1, 358, ',1,3,10,276,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'admin_group', 'create', '', '', '', 'xz', 'xinzeng', 'admin_group/create', '', ''),
(277, 10, '修改', 1, 359, ',1,3,10,277,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'admin_group', 'modify', '', '', '', 'xg', 'xiugai', 'admin_group/modify', '', ''),
(278, 10, '删除', 1, 360, ',1,3,10,278,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'admin_group', 'delete', '', '', '', 'sc', 'shanchu', 'admin_group/delete', '', ''),
(279, 10, '排序', 1, 361, ',1,3,10,279,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'admin_group', 'sort', '', '', '', 'px', 'paixu', 'admin_group/sort', '', ''),
(280, 23, '表单类型', 1, 362, ',21,22,23,280,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'demo', 'demo1', '', '', '', 'bdlx', 'biaodanleixing', 'demo/demo1', '', ''),
(281, 73, '调拨单录入', 1, 363, ',21,22,73,281,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'demo', 'order', '', '', '', 'dbdlr', 'diaobodanluru', 'demo/order', '', ''),
(282, 26, '表单分组', 1, 364, ',21,22,26,282,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'demo', 'demo2', '', '', '', 'bdfz', 'biaodanfenzu', 'demo/demo2', '', ''),
(283, 35, '表单触发', 1, 365, ',21,22,35,283,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'demo', 'demo3', '', '', '', 'bdcf', 'biaodanchufa', 'demo/demo3', '', ''),
(284, 36, '表单布局', 1, 366, ',21,22,36,284,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'demo', 'demo4', '', '', '', 'bdbj', 'biaodanbuju', 'demo/demo4', '', ''),
(285, 37, '自定义页', 1, 367, ',21,34,37,285,', 4, 0, 1740632504, 1740632504, 'button', '', '', 'demo', 'demo5', '', '', '', 'zdyy', 'zidingyiye', 'demo/demo5', '', ''),
(286, 94, '扩展图标', 1, 368, ',21,34,94,286,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'icon', '', '', '', 'kztb', 'kuozhantubiao', 'demo/icon', '', ''),
(287, 40, '表格功能', 1, 369, ',21,33,40,287,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'demo6', '', '', '', 'bggn', 'biaogegongneng', 'demo/demo6', '', ''),
(288, 85, '表格按钮', 1, 370, ',21,33,85,288,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'demo63', '', '', '', 'bgan', 'biaogeanniu', 'demo/demo63', '', ''),
(289, 71, '订单模板', 1, 371, ',21,33,71,289,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'demo61', '', '', '', 'ddmb', 'dingdanmoban', 'demo/demo61', '', ''),
(290, 72, '图文模板', 1, 372, ',21,33,72,290,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'demo62', '', '', '', 'twmb', 'tuwenmoban', 'demo/demo62', '', ''),
(291, 45, '静态数据', 1, 373, ',21,33,45,291,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'demo7', '', '', '', 'jtsj', 'jingtaishuju', 'demo/demo7', '', ''),
(292, 48, '注解功能', 1, 374, ',21,34,48,292,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'demo8', '', '', '', 'zjgn', 'zhujiegongneng', 'demo/demo8', '', ''),
(293, 49, '403页面', 1, 375, ',21,34,49,293,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'demo9', '', '', '', '403ym', '403yemian', 'demo/demo9', '', ''),
(294, 50, '404页面', 1, 376, ',21,34,50,294,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'demo10', '', '', '', '404ym', '404yemian', 'demo/demo10', '', ''),
(295, 51, '500页面', 1, 377, ',21,34,51,295,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'demo11', '', '', '', '500ym', '500yemian', 'demo/demo11', '', ''),
(296, 93, '大屏展示', 1, 378, ',21,34,93,296,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'demo', 'demo12', '', '', '', 'dpzs', 'dapingzhanshi', 'demo/demo12', '', ''),
(297, 64, '列表', 1, 379, ',1,4,64,297,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'request_log', 'index', '', '', '', 'lb', 'liebiao', 'request_log/index', '', ''),
(298, 64, '删除', 1, 380, ',1,4,64,298,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'request_log', 'delete', '', '', '', 'sc', 'shanchu', 'request_log/delete', '', ''),
(299, 64, '排序', 1, 381, ',1,4,64,299,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'request_log', 'sort', '', '', '', 'px', 'paixu', 'request_log/sort', '', ''),
(300, 80, '列表', 1, 382, ',1,5,80,300,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'addon_setting', 'index', '', '', '', 'lb', 'liebiao', 'addon_setting/index', '', ''),
(301, 80, '新增', 1, 383, ',1,5,80,301,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'addon_setting', 'create', '', '', '', 'xz', 'xinzeng', 'addon_setting/create', '', ''),
(302, 80, '修改', 1, 384, ',1,5,80,302,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'addon_setting', 'modify', '', '', '', 'xg', 'xiugai', 'addon_setting/modify', '', ''),
(303, 80, '删除', 1, 385, ',1,5,80,303,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'addon_setting', 'delete', '', '', '', 'sc', 'shanchu', 'addon_setting/delete', '', ''),
(304, 80, '排序', 1, 386, ',1,5,80,304,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'addon_setting', 'sort', '', '', '', 'px', 'paixu', 'addon_setting/sort', '', ''),
(305, 80, '配置', 1, 387, ',1,5,80,305,', 4, 0, 1740632505, 1740632505, 'button', '', '', 'addon_setting', 'set', '', '', '', 'pz', 'peizhi', 'addon_setting/set', '', ''),
(306, 63, '列表', 1, 388, ',1,4,63,306,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'antispam', 'index', '', '', '', 'lb', 'liebiao', 'antispam/index', '', ''),
(307, 63, '新增', 1, 389, ',1,4,63,307,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'antispam', 'create', '', '', '', 'xz', 'xinzeng', 'antispam/create', '', ''),
(308, 63, '修改', 1, 390, ',1,4,63,308,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'antispam', 'modify', '', '', '', 'xg', 'xiugai', 'antispam/modify', '', ''),
(309, 63, '删除', 1, 391, ',1,4,63,309,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'antispam', 'delete', '', '', '', 'sc', 'shanchu', 'antispam/delete', '', ''),
(310, 63, '排序', 1, 392, ',1,4,63,310,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'antispam', 'sort', '', '', '', 'px', 'paixu', 'antispam/sort', '', ''),
(311, 74, '清除缓存', 1, 393, ',1,5,74,311,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'tool', 'clearcache', '', '', '', 'qchc', 'qingchuhuancun', 'tool/clearcache', '', '');
INSERT INTO `woo_admin_rule` (`id`, `parent_id`, `title`, `is_nav`, `list_order`, `family`, `level`, `children_count`, `create_time`, `update_time`, `type`, `icon`, `addon`, `controller`, `action`, `url`, `args`, `open_type`, `jianpin`, `pinyin`, `rule`, `other_name`, `js_func`) VALUES
(312, 74, '日志下载', 1, 394, ',1,5,74,312,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'tool', 'getlog', '', '', '', 'rzxz', 'rizhixiazai', 'tool/getlog', '', ''),
(313, 74, '清临时文件', 1, 395, ',1,5,74,313,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'tool', 'removetemp', '', '', '', 'qlswj', 'qinglinshiwenjian', 'tool/removetemp', '', ''),
(314, 70, '创建模板', 1, 396, ',1,5,70,314,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'tool', 'maketemplate', '', '', '', 'cjmb', 'chuangjianmoban', 'tool/maketemplate', '', ''),
(315, 44, '创控制器', 1, 397, ',1,5,44,315,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'tool', 'makecontroller', '', '', '', 'ckzq', 'chuangkongzhiqi', 'tool/makecontroller', '', ''),
(316, 15, '列表', 1, 398, ',1,4,15,316,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'dictionary', 'index', '', '', '', 'lb', 'liebiao', 'dictionary/index', '', ''),
(317, 15, '新增', 1, 399, ',1,4,15,317,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'dictionary', 'create', '', '', '', 'xz', 'xinzeng', 'dictionary/create', '', ''),
(318, 15, '修改', 1, 400, ',1,4,15,318,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'dictionary', 'modify', '', '', '', 'xg', 'xiugai', 'dictionary/modify', '', ''),
(319, 15, '删除', 1, 401, ',1,4,15,319,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'dictionary', 'delete', '', '', '', 'sc', 'shanchu', 'dictionary/delete', '', ''),
(320, 15, '排序', 1, 402, ',1,4,15,320,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'dictionary', 'sort', '', '', '', 'px', 'paixu', 'dictionary/sort', '', ''),
(321, 15, '回收站', 1, 403, ',1,4,15,321,', 4, 0, 1740632506, 1740632506, 'button', '', '', 'dictionary', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'dictionary/deleteindex', '', ''),
(322, 68, '列表', 1, 404, ',16,65,68,322,', 4, 0, 1740632507, 1740632507, 'button', '', '', 'denied', 'index', '', '', '', 'lb', 'liebiao', 'denied/index', '', ''),
(323, 68, '新增', 1, 405, ',16,65,68,323,', 4, 0, 1740632507, 1740632507, 'button', '', '', 'denied', 'create', '', '', '', 'xz', 'xinzeng', 'denied/create', '', ''),
(324, 68, '修改', 1, 406, ',16,65,68,324,', 4, 0, 1740632507, 1740632507, 'button', '', '', 'denied', 'modify', '', '', '', 'xg', 'xiugai', 'denied/modify', '', ''),
(325, 68, '删除', 1, 407, ',16,65,68,325,', 4, 0, 1740632507, 1740632507, 'button', '', '', 'denied', 'delete', '', '', '', 'sc', 'shanchu', 'denied/delete', '', ''),
(326, 68, '排序', 1, 408, ',16,65,68,326,', 4, 0, 1740632507, 1740632507, 'button', '', '', 'denied', 'sort', '', '', '', 'px', 'paixu', 'denied/sort', '', ''),
(327, 0, '积分', 1, 3, ',327,', 1, 5, 1740643417, 1740643606, 'directory', 'woo-icon-jifen', '', '', '', '', '', '_iframe', 'jf', 'jifen', '', '', ''),
(331, 327, '积分发放', 1, 411, ',327,331,', 2, 1, 1740644738, 1740649814, 'menu', 'layui-icon-add-1', '', 'user_score', 'create', '', '', '_iframe', 'jfff', 'jifenfafang', 'user_score/create', '', ''),
(333, 331, '发放', 1, 412, ',327,331,333,', 3, 0, 1740648591, 1740649333, 'button', '', '', 'score_handle', 'send', '', '', '_iframe', 'ffjf', 'fafangjifen', 'score_handle/send', '', ''),
(334, 53, '申诉', 1, 413, ',327,53,334,', 3, 0, 1740648761, 1740649262, 'button', '', '', 'score_appeal', 'appeal', '', '', '_iframe', 'ss', 'shensu', 'score_appeal/appeal', '', ''),
(335, 327, '积分申诉', 1, 414, ',327,335,', 2, 9, 1740648844, 1740648844, 'menu', 'woo-icon-reception', '', 'score_appeal', 'index', '', '', '_iframe', 'jfss', 'jifenshensu', 'score_appeal/index', '', ''),
(336, 335, '申请', 1, 415, ',327,335,336,', 3, 0, 1740648931, 1740649223, 'button', '', '', 'score_appeal', 'appeal', '', '{\"status\":\"pending\"}', '_iframe', 'sq', 'shenqing', 'score_appeal/appeal', '', ''),
(337, 335, '批准', 1, 416, ',327,335,337,', 3, 0, 1740649085, 1740649085, 'button', '', '', 'score_appeal', 'handle', '', '{\"status\":\"approved\"}', '_iframe', 'pz', 'pizhun', 'score_appeal/handle', '', ''),
(338, 335, '驳回', 1, 417, ',327,335,338,', 3, 0, 1740649143, 1740649143, 'button', '', '', 'score_appeal', 'handle', '', '{\"status\":\"rejected\"}', '_iframe', 'bh', 'bohui', 'score_appeal/handle', '', ''),
(339, 1, '用户通知表', 0, 418, ',1,339,', 2, 6, 1740656983, 1740656983, 'directory', '', '', '', '', '', '', '', 'yhtzb', 'yonghutongzhibiao', '', '', ''),
(340, 339, '列表', 1, 419, ',1,339,340,', 3, 0, 1740656983, 1740656983, 'button', '', '', 'notification', 'index', '', '', '', 'lb', 'liebiao', 'notification/index', '', ''),
(341, 339, '新增', 1, 420, ',1,339,341,', 3, 0, 1740656983, 1740656983, 'button', '', '', 'notification', 'create', '', '', '', 'xz', 'xinzeng', 'notification/create', '', ''),
(342, 339, '修改', 1, 421, ',1,339,342,', 3, 0, 1740656983, 1740656983, 'button', '', '', 'notification', 'modify', '', '', '', 'xg', 'xiugai', 'notification/modify', '', ''),
(343, 339, '删除', 1, 422, ',1,339,343,', 3, 0, 1740656983, 1740656983, 'button', '', '', 'notification', 'delete', '', '', '', 'sc', 'shanchu', 'notification/delete', '', ''),
(344, 339, '排序', 1, 423, ',1,339,344,', 3, 0, 1740656983, 1740656983, 'button', '', '', 'notification', 'sort', '', '', '', 'px', 'paixu', 'notification/sort', '', ''),
(345, 339, '回收站', 1, 424, ',1,339,345,', 3, 0, 1740656983, 1740656983, 'button', '', '', 'notification', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'notification/deleteindex', '', ''),
(346, 327, '积分规则表', 1, 425, ',327,346,', 2, 6, 1740656984, 1746076565, 'menu', 'woo-icon-list-full', '', 'score_rule', 'index', '', '', '', 'jfgzb', 'jifenguizebiao', 'score_rule/index', '', ''),
(347, 346, '列表', 1, 426, ',327,346,347,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'score_rule', 'index', '', '', '', 'lb', 'liebiao', 'score_rule/index', '', ''),
(348, 346, '新增', 1, 427, ',327,346,348,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'score_rule', 'create', '', '', '', 'xz', 'xinzeng', 'score_rule/create', '', ''),
(349, 346, '修改', 1, 428, ',327,346,349,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'score_rule', 'modify', '', '', '', 'xg', 'xiugai', 'score_rule/modify', '', ''),
(350, 346, '删除', 1, 429, ',327,346,350,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'score_rule', 'delete', '', '', '', 'sc', 'shanchu', 'score_rule/delete', '', ''),
(351, 346, '排序', 1, 430, ',327,346,351,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'score_rule', 'sort', '', '', '', 'px', 'paixu', 'score_rule/sort', '', ''),
(352, 346, '回收站', 1, 431, ',327,346,352,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'score_rule', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'score_rule/deleteindex', '', ''),
(353, 16, '微信用户表', 1, 432, ',16,353,', 2, 6, 1740656984, 1746064297, 'directory', '', '', '', '', '', '', '', 'wxyhb', 'weixinyonghubiao', '', '', ''),
(354, 353, '列表', 1, 433, ',16,353,354,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'wechat_user', 'index', '', '', '', 'lb', 'liebiao', 'wechat_user/index', '', ''),
(355, 353, '新增', 1, 434, ',16,353,355,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'wechat_user', 'create', '', '', '', 'xz', 'xinzeng', 'wechat_user/create', '', ''),
(356, 353, '修改', 1, 435, ',16,353,356,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'wechat_user', 'modify', '', '', '', 'xg', 'xiugai', 'wechat_user/modify', '', ''),
(357, 353, '删除', 1, 436, ',16,353,357,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'wechat_user', 'delete', '', '', '', 'sc', 'shanchu', 'wechat_user/delete', '', ''),
(358, 353, '排序', 1, 437, ',16,353,358,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'wechat_user', 'sort', '', '', '', 'px', 'paixu', 'wechat_user/sort', '', ''),
(359, 353, '回收站', 1, 438, ',16,353,359,', 3, 0, 1740656984, 1740656984, 'button', '', '', 'wechat_user', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'wechat_user/deleteindex', '', ''),
(360, 335, '列表', 1, 439, ',327,335,360,', 3, 0, 1740656985, 1740656985, 'button', '', '', 'score_appeal', 'index', '', '', '', 'lb', 'liebiao', 'score_appeal/index', '', ''),
(361, 335, '新增', 1, 440, ',327,335,361,', 3, 0, 1740656985, 1740656985, 'button', '', '', 'score_appeal', 'create', '', '', '', 'xz', 'xinzeng', 'score_appeal/create', '', ''),
(362, 335, '修改', 1, 441, ',327,335,362,', 3, 0, 1740656985, 1740656985, 'button', '', '', 'score_appeal', 'modify', '', '', '', 'xg', 'xiugai', 'score_appeal/modify', '', ''),
(363, 335, '删除', 1, 442, ',327,335,363,', 3, 0, 1740656985, 1740656985, 'button', '', '', 'score_appeal', 'delete', '', '', '', 'sc', 'shanchu', 'score_appeal/delete', '', ''),
(364, 335, '排序', 1, 443, ',327,335,364,', 3, 0, 1740656985, 1740656985, 'button', '', '', 'score_appeal', 'sort', '', '', '', 'px', 'paixu', 'score_appeal/sort', '', ''),
(365, 335, '回收站', 1, 444, ',327,335,365,', 3, 0, 1740656985, 1740656985, 'button', '', '', 'score_appeal', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'score_appeal/deleteindex', '', ''),
(366, 1, 'ScoreOverview', 0, 445, ',1,366,', 2, 4, 1740656985, 1740656985, 'directory', '', '', '', '', '', '', '', 'scoreoverview', 'scoreoverview', '', '', ''),
(367, 366, '列表', 1, 446, ',1,366,367,', 3, 0, 1740656985, 1740656985, 'button', '', '', 'score_overview', 'index', '', '', '', 'lb', 'liebiao', 'score_overview/index', '', ''),
(368, 366, '新增', 1, 447, ',1,366,368,', 3, 0, 1740656985, 1740656985, 'button', '', '', 'score_overview', 'create', '', '', '', 'xz', 'xinzeng', 'score_overview/create', '', ''),
(369, 366, '修改', 1, 448, ',1,366,369,', 3, 0, 1740656985, 1740656985, 'button', '', '', 'score_overview', 'modify', '', '', '', 'xg', 'xiugai', 'score_overview/modify', '', ''),
(370, 366, '删除', 1, 449, ',1,366,370,', 3, 0, 1740656985, 1740656985, 'button', '', '', 'score_overview', 'delete', '', '', '', 'sc', 'shanchu', 'score_overview/delete', '', ''),
(371, 1, 'ScoreRanking', 0, 450, ',1,371,', 2, 4, 1740656986, 1740656986, 'directory', '', '', '', '', '', '', '', 'scoreranking', 'scoreranking', '', '', ''),
(372, 371, '列表', 1, 451, ',1,371,372,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_ranking', 'index', '', '', '', 'lb', 'liebiao', 'score_ranking/index', '', ''),
(373, 371, '新增', 1, 452, ',1,371,373,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_ranking', 'create', '', '', '', 'xz', 'xinzeng', 'score_ranking/create', '', ''),
(374, 371, '修改', 1, 453, ',1,371,374,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_ranking', 'modify', '', '', '', 'xg', 'xiugai', 'score_ranking/modify', '', ''),
(375, 371, '删除', 1, 454, ',1,371,375,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_ranking', 'delete', '', '', '', 'sc', 'shanchu', 'score_ranking/delete', '', ''),
(376, 1, 'ScoreHandle', 0, 455, ',1,376,', 2, 4, 1740656986, 1740656986, 'directory', '', '', '', '', '', '', '', 'scorehandle', 'scorehandle', '', '', ''),
(377, 376, '列表', 1, 456, ',1,376,377,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_handle', 'index', '', '', '', 'lb', 'liebiao', 'score_handle/index', '', ''),
(378, 376, '新增', 1, 457, ',1,376,378,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_handle', 'create', '', '', '', 'xz', 'xinzeng', 'score_handle/create', '', ''),
(379, 376, '修改', 1, 458, ',1,376,379,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_handle', 'modify', '', '', '', 'xg', 'xiugai', 'score_handle/modify', '', ''),
(380, 376, '删除', 1, 459, ',1,376,380,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_handle', 'delete', '', '', '', 'sc', 'shanchu', 'score_handle/delete', '', ''),
(381, 327, '积分规则分类表（无限级）', 1, 460, ',327,381,', 2, 6, 1740656986, 1746076482, 'menu', 'layui-icon-list', '', 'score_category', 'index', '', '', '', 'jfgzflbwxj', 'jifenguizefenleibiaowuxianji', 'score_category/index', '', ''),
(382, 381, '列表', 1, 461, ',327,381,382,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_category', 'index', '', '', '', 'lb', 'liebiao', 'score_category/index', '', ''),
(383, 381, '新增', 1, 462, ',327,381,383,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_category', 'create', '', '', '', 'xz', 'xinzeng', 'score_category/create', '', ''),
(384, 381, '修改', 1, 463, ',327,381,384,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_category', 'modify', '', '', '', 'xg', 'xiugai', 'score_category/modify', '', ''),
(385, 381, '删除', 1, 464, ',327,381,385,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_category', 'delete', '', '', '', 'sc', 'shanchu', 'score_category/delete', '', ''),
(386, 381, '排序', 1, 465, ',327,381,386,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_category', 'sort', '', '', '', 'px', 'paixu', 'score_category/sort', '', ''),
(387, 381, '回收站', 1, 466, ',327,381,387,', 3, 0, 1740656986, 1740656986, 'button', '', '', 'score_category', 'deleteindex', '', '', '', 'hsz', 'huishouzhan', 'score_category/deleteindex', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `woo_admin_use_admin_group`
--

CREATE TABLE `woo_admin_use_admin_group` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属管理员ID',
  `admin_group_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属角色ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户对应角色';

--
-- 转存表中的数据 `woo_admin_use_admin_group`
--

INSERT INTO `woo_admin_use_admin_group` (`id`, `admin_id`, `admin_group_id`, `create_time`, `update_time`) VALUES
(1, 1, 1, 1740631605, 1740631605),
(2, 2, 2, 1740650767, 1740650767);

-- --------------------------------------------------------

--
-- 表的结构 `woo_antispam`
--

CREATE TABLE `woo_antispam` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '审核模型',
  `foreign_id` int(11) NOT NULL DEFAULT '0' COMMENT '模型ID',
  `is_verify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核状态',
  `content` mediumtext NOT NULL COMMENT '审核内容',
  `result` text NOT NULL COMMENT '返回结果',
  `msg` text NOT NULL COMMENT '内容提示',
  `words` text NOT NULL COMMENT '不合格字符',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `business_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家ID',
  `business_member_id` varchar(64) NOT NULL DEFAULT '' COMMENT '商家用户ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文本审核';

-- --------------------------------------------------------

--
-- 表的结构 `woo_application`
--

CREATE TABLE `woo_application` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '应用目录',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '应用名称',
  `author` varchar(64) NOT NULL DEFAULT '' COMMENT '作者',
  `version` varchar(64) NOT NULL DEFAULT '' COMMENT '版本',
  `is_verify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否启用',
  `is_api` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否API',
  `is_disuninstall` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '禁止卸载',
  `describe` mediumtext NOT NULL COMMENT '应用描述',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='应用';

--
-- 转存表中的数据 `woo_application`
--

INSERT INTO `woo_application` (`id`, `name`, `title`, `author`, `version`, `is_verify`, `is_api`, `is_disuninstall`, `describe`, `admin_id`, `create_time`, `update_time`) VALUES
(1, 'api', 'api', 'System', '0.1', 1, 1, 0, '系统API应用', 1, 1746112047, 1746112047);

-- --------------------------------------------------------

--
-- 表的结构 `woo_attachement`
--

CREATE TABLE `woo_attachement` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '附件名',
  `folder_id` int(11) NOT NULL DEFAULT '0' COMMENT '文件夹',
  `url` varchar(128) NOT NULL DEFAULT '' COMMENT 'URL',
  `ext` varchar(16) NOT NULL DEFAULT '' COMMENT '后缀',
  `size` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '大小',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '类型',
  `width` mediumint(8) UNSIGNED NOT NULL DEFAULT '0' COMMENT '宽度',
  `height` mediumint(8) UNSIGNED NOT NULL DEFAULT '0' COMMENT '高度',
  `model` varchar(32) NOT NULL DEFAULT '' COMMENT '模型',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `business_member_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家用户ID',
  `business_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家ID',
  `hash` varchar(64) NOT NULL DEFAULT '' COMMENT 'Hash',
  `driver` varchar(32) NOT NULL DEFAULT '' COMMENT '上传方式',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件';

-- --------------------------------------------------------

--
-- 表的结构 `woo_certification`
--

CREATE TABLE `woo_certification` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `truename` varchar(64) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机',
  `id_card` varchar(32) NOT NULL DEFAULT '' COMMENT '身份证',
  `id_card_front` varchar(128) NOT NULL DEFAULT '' COMMENT '身份证正面',
  `id_card_back` varchar(128) NOT NULL DEFAULT '' COMMENT '身份证背面',
  `is_cert` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '通过',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='实名认证';

-- --------------------------------------------------------

--
-- 表的结构 `woo_denied`
--

CREATE TABLE `woo_denied` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `model` varchar(64) NOT NULL DEFAULT '' COMMENT '登录模型',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `ip` varchar(32) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `expire` int(11) NOT NULL DEFAULT '0' COMMENT '到期时间',
  `is_verify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否执行',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='禁止登录';

-- --------------------------------------------------------

--
-- 表的结构 `woo_department`
--

CREATE TABLE `woo_department` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '部门名称',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `family` varchar(256) NOT NULL DEFAULT '' COMMENT '家族',
  `level` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '层级',
  `children_count` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下级数',
  `leader_ids` varchar(128) NOT NULL DEFAULT '' COMMENT '部门领导',
  `is_admin` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '后台登录'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='部门';

--
-- 转存表中的数据 `woo_department`
--

INSERT INTO `woo_department` (`id`, `parent_id`, `title`, `create_time`, `update_time`, `list_order`, `family`, `level`, `children_count`, `leader_ids`, `is_admin`) VALUES
(1, 0, '总部', 1740631596, 1740631596, 1, ',1,', 1, 3, '', 1),
(3, 1, '财务部', 1740631596, 1740631596, 3, ',1,3,', 2, 0, '', 1),
(4, 1, '技术部', 1740631596, 1740631596, 4, ',1,4,', 2, 0, '', 1),
(6, 1, '国内销售部', 1740650625, 1740650625, 6, ',1,6,', 2, 0, '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `woo_dictionary`
--

CREATE TABLE `woo_dictionary` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `model` varchar(32) NOT NULL DEFAULT '' COMMENT '模型名',
  `field` varchar(64) NOT NULL DEFAULT '' COMMENT '字段名',
  `dictionary_item_count` smallint(6) NOT NULL DEFAULT '0' COMMENT '字典项计数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字典';

-- --------------------------------------------------------

--
-- 表的结构 `woo_dictionary_item`
--

CREATE TABLE `woo_dictionary_item` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `dictionary_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属字典',
  `key` varchar(64) NOT NULL DEFAULT '' COMMENT '键',
  `value` varchar(64) NOT NULL DEFAULT '' COMMENT '值',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字典项';

-- --------------------------------------------------------

--
-- 表的结构 `woo_field`
--

CREATE TABLE `woo_field` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `field` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `model_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `form` varchar(128) NOT NULL DEFAULT '',
  `business_form` varchar(128) NOT NULL DEFAULT '',
  `modify_form` varchar(128) NOT NULL DEFAULT '',
  `business_modify_form` varchar(128) NOT NULL DEFAULT '',
  `form_foreign` varchar(64) NOT NULL DEFAULT '',
  `business_form_foreign` varchar(64) NOT NULL DEFAULT '',
  `form_item_attrs` text,
  `business_form_item_attrs` text,
  `form_tag_attrs` text,
  `business_form_tag_attrs` text,
  `form_options` text,
  `business_form_options` text,
  `form_upload` text,
  `business_form_upload` text,
  `form_trigger` text,
  `business_form_trigger` text,
  `list` varchar(128) NOT NULL DEFAULT '',
  `business_list` varchar(128) NOT NULL DEFAULT '',
  `list_attrs` text,
  `business_list_attrs` text,
  `list_filter` varchar(128) NOT NULL DEFAULT '',
  `business_list_filter` varchar(128) NOT NULL DEFAULT '',
  `list_filter_attrs` varchar(512) NOT NULL DEFAULT '',
  `business_list_filter_attrs` varchar(512) NOT NULL DEFAULT '',
  `list_filter_tag_attrs` varchar(512) NOT NULL DEFAULT '',
  `business_list_filter_tag_attrs` varchar(512) NOT NULL DEFAULT '',
  `detail` varchar(128) NOT NULL DEFAULT '',
  `business_detail` varchar(128) NOT NULL DEFAULT '',
  `detail_attrs` varchar(512) NOT NULL DEFAULT '',
  `business_detail_attrs` varchar(512) NOT NULL DEFAULT '',
  `validate` text,
  `business_validate` text,
  `is_field` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `type` varchar(64) NOT NULL DEFAULT '',
  `length` varchar(128) NOT NULL DEFAULT '',
  `default` varchar(128) NOT NULL DEFAULT '',
  `is_not_null` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `is_unsigned` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `is_ai` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `is_system` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `is_contribute` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `index` varchar(32) NOT NULL DEFAULT '',
  `after` varchar(32) NOT NULL DEFAULT '',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `list_order` int(11) NOT NULL DEFAULT '0',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字段';

--
-- 转存表中的数据 `woo_field`
--

INSERT INTO `woo_field` (`id`, `field`, `name`, `model_id`, `form`, `business_form`, `modify_form`, `business_modify_form`, `form_foreign`, `business_form_foreign`, `form_item_attrs`, `business_form_item_attrs`, `form_tag_attrs`, `business_form_tag_attrs`, `form_options`, `business_form_options`, `form_upload`, `business_form_upload`, `form_trigger`, `business_form_trigger`, `list`, `business_list`, `list_attrs`, `business_list_attrs`, `list_filter`, `business_list_filter`, `list_filter_attrs`, `business_list_filter_attrs`, `list_filter_tag_attrs`, `business_list_filter_tag_attrs`, `detail`, `business_detail`, `detail_attrs`, `business_detail_attrs`, `validate`, `business_validate`, `is_field`, `type`, `length`, `default`, `is_not_null`, `is_unsigned`, `is_ai`, `is_system`, `is_contribute`, `index`, `after`, `admin_id`, `list_order`, `create_time`, `update_time`) VALUES
(1, 'id', 'ID', 1, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{}', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 1, 1740631596, 1740631596),
(2, 'title', '组标题', 1, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'id', 1, 2, 1740631596, 1740631596),
(3, 'list_order', '排序权重', 1, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 5, 1740631596, 1740631596),
(4, 'admin_id', '管理员ID', 1, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', 'list_order', 1, 6, 1740631596, 1740631596),
(5, 'create_time', '创建日期', 1, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 7, 1740631596, 1740631596),
(6, 'update_time', '修改日期', 1, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 186, 1740631596, 1740631596),
(7, 'delete_time', '删除日期', 1, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 10211, 1740631596, 1740631596),
(8, 'id', 'ID', 2, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '', 1, 1, 1, 1, 0, '', '', 1, 8, 1740631596, 1740631596),
(9, 'title', '标题', 2, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"200\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 9, 1740631596, 1740631596),
(10, 'list_order', '排序权重', 2, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 18, 1740631596, 1740631596),
(11, 'admin_id', '管理员ID', 2, 'none', '', '', '', 'Admin', '', '[]', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', 'list_order', 1, 10, 1740631596, 1740631596),
(12, 'create_time', '创建日期', 2, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 19, 1740631596, 1740631596),
(13, 'update_time', '修改日期', 2, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 20, 1740631596, 1740631596),
(14, 'delete_time', '删除日期', 2, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 190, 1740631596, 1740631596),
(15, 'setting_group_id', '所属系统配置组', 2, 'relation', '', '', '', 'SettingGroup', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '{\"width\":\"160\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"egt\",\"args\":\"1\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', '', 1, 11, 1740631596, 1740631596),
(16, 'var', '变量名', 2, 'text', '', '', '', '', '', '{\"filter\":\"trim\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'show.blue', '', '{\"width\":\"200\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"unique\",\"args\":\"setting\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, 'unique', '', 1, 12, 1740631596, 1740631596),
(17, 'value', '数据', 2, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TEXT', '', 'none', 1, 0, 0, 1, 0, '', 'var', 1, 13, 1740631596, 1740631596),
(18, 'type', '输入类型', 2, 'select', '', '', '', '', '', '', NULL, '', NULL, '{\"text\":\"单行文本\",\"textarea\":\"多行文本\",\"number\":\"数字\",\"radio\":\"单选\",\"select\":\"下拉\",\"checker\":\"是否\",\"checkbox\":\"多选\",\"image\":\"图片\",\"file\":\"上传\",\"array\":\"数组\",\"keyvalue\":\"键值对\",\"password\":\"密码\",\"color\":\"取色器\",\"ckeditor\":\"富文本\",\"multiimage\":\"多图\"}', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'value', 1, 14, 1740631596, 1740631596),
(19, 'options', '选项', 2, 'keyvalue', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '512', '', 1, 0, 0, 1, 0, '', 'type', 1, 15, 1740631596, 1740631596),
(20, 'tip', '提示', 2, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'options', 1, 16, 1740631596, 1740631596),
(40, 'id', 'ID', 4, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 40, 1740631596, 1740631596),
(41, 'parent_id', '父级', 4, 'xmtree', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 41, 1740631596, 1740631596),
(42, 'title', '名称', 4, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '100', '', 1, 0, 0, 1, 0, '', '', 1, 42, 1740631596, 1740631596),
(43, 'first', '首字母', 4, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'CHAR', '20', '', 1, 0, 0, 1, 0, '', '', 1, 43, 1740631596, 1740631596),
(44, 'pinyin', '拼音', 4, 'text', '', '', '', '', '', '[]', NULL, '{\"placeholder\":\"如为空，系统会自动识别；格式：beijing\"}', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'title', 1, 44, 1740631596, 1740631596),
(45, 'jianpin', '简拼', 4, 'text', '', '', '', '', '', '', NULL, '{\"placeholder\":\"如为空，系统会自动识别；格式：bj\"}', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'pinyin', 1, 45, 1740631596, 1740631596),
(46, 'children_count', '下级数', 4, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'SMALLINT', '', '0', 1, 1, 0, 1, 0, '', 'list_order', 1, 46, 1740631596, 1740631596),
(47, 'family', '家族', 4, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'children_count', 1, 47, 1740631596, 1740631596),
(48, 'level', '层级', 4, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '', '0', 1, 0, 0, 1, 0, '', 'family', 1, 48, 1740631596, 1740631596),
(49, 'code', '代码编号', 4, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'title', 1, 49, 1740631596, 1740631596),
(50, 'lng', '经度', 4, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '9,6', '0', 1, 0, 0, 1, 0, '', 'level', 1, 50, 1740631596, 1740631596),
(51, 'lat', '纬度', 4, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '9,6', '0', 1, 0, 0, 1, 0, '', 'lng', 1, 51, 1740631596, 1740631596),
(52, 'list_order', 'list_order', 4, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, '', '', 1, 52, 1740631596, 1740631596),
(61, 'id', 'ID', 6, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 61, 1740631596, 1740631596),
(62, 'parent_id', '父级ID', 6, 'xmtree', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"hide\":\"true\"}', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"egt\",\"args\":\"0\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 63, 1740631596, 1740631596),
(63, 'title', '部门名称', 6, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"240\"}', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 62, 1740631596, 1740631596),
(64, 'create_time', '创建日期', 6, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 498, 1740631596, 1740631596),
(65, 'update_time', '修改日期', 6, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 499, 1740631596, 1740631596),
(71, 'id', 'ID', 7, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 71, 1740631596, 1740631596),
(72, 'create_time', '创建日期', 7, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"152\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 490, 1740631596, 1740631596),
(73, 'update_time', '修改日期', 7, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"152\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 491, 1740631596, 1740631596),
(74, 'delete_time', '删除日期', 7, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 492, 1740631596, 1740631596),
(75, 'username', '用户名', 7, 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'username', '', '{\"width\":\"160\",\"title\":\"账号\",\"fixed\":\"left\"}', '', '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"unique\",\"args\":\"admin\",\"on\":\"0\",\"message\":\"\"}]', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, 'unique', 'id', 1, 72, 1740631596, 1740631596),
(76, 'password', '密码', 7, 'password', '', 'none', '', '', '', '{\"tip\":\"不修改请保持为空\",\"rsa\":\"true\"}', NULL, '{\"lay-affix\":\"eye\"}', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '0', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"add \",\"message\":\"\"},{\"rule\":\"length\",\"args\":\"6,16\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'username', 1, 73, 1740631596, 1740631596),
(77, 'email', '邮箱', 7, 'text', '', '', '', '', '', '{\"form_group\":\"admin\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"minWidth\":\"150\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"email\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"unique\",\"args\":\"admin\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', '', 1, 82, 1740631596, 1740631596),
(79, 'department_id', '所属部门', 7, 'xmtree', '', '', '', 'Department', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '{\"width\":\"110\"}', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 1, 0, 1, 0, '', '', 1, 84, 1740631596, 1740631596),
(80, 'nickname', '昵称', 7, 'text', '', '', '', '', '', '{\"form_group\":\"admin\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'department_id', 1, 76, 1740631596, 1740631596),
(81, 'avatar', '头像', 7, 'image', '', '', '', '', '', '{\"form_group\":\"admin\"}', '', '', '', '', '', '{\"maxSize\":\"512\",\"validExt\":\"png|jpg|gif|jpeg\",\"resizeWidth\":\"200\",\"resizeHeight\":\"200\",\"resizeMethod\":\"3\"}', '', '', '', '', '', '{\"hide\":\"true\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'truename', 1, 77, 1740631596, 1740631596),
(82, 'login_time', '登录日期', 7, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'datetime', '', '{\"width\":\"154\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 1, 0, 1, 0, '', '', 1, 487, 1740631596, 1740631596),
(83, 'login_ip', '登录IP', 7, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"130\"}', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'login_time', 1, 488, 1740631596, 1740631596),
(84, 'login_id', '登录SESSID', 7, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'login_ip', 1, 489, 1740631596, 1740631596),
(85, 'status', '状态', 7, 'select', '', '', '', '', '', '', NULL, '', NULL, '{\"verified\":\"已激活\",\"unverified\":\"未激活\",\"banned\":\"已禁用\"}', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '16', '', 1, 0, 0, 1, 0, '', 'department_id', 1, 85, 1740631596, 1740631596),
(86, 'truename', '真实姓名', 7, 'text', '', '', '', '', '', '{\"form_group\":\"admin\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'nickname', 1, 75, 1740631596, 1740631596),
(87, 'id', 'ID', 8, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{}', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 87, 1740631596, 1740631596),
(88, 'create_time', '登录时间', 8, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 95, 1740631596, 1740631596),
(89, 'update_time', '修改日期', 8, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 96, 1740631596, 1740631596),
(90, 'admin_id', '所属管理员', 8, 'relation', '', '', '', 'Admin', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'id', 1, 88, 1740631596, 1740631596),
(91, 'ip', '登录IP', 8, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'admin_id', 1, 89, 1740631596, 1740631596),
(92, 'user_agent', '客户端', 8, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'ua', '', '{}', NULL, '', '', '', '', '', '', 'show', '', '', '', '', NULL, 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', 'ip', 1, 90, 1740631596, 1740631596),
(93, 'summary', '描述', 8, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"minWidth\":\"200\"}', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'user_agent', 1, 91, 1740631596, 1740631596),
(94, 'username', '输入账号', 8, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'admin_id', 1, 92, 1740631596, 1740631596),
(95, 'region', '登录地址', 8, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'user_agent', 1, 93, 1740631596, 1740631596),
(96, 'is_success', '是否成功', 8, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'summary', 1, 94, 1740631596, 1740631596),
(97, 'id', 'ID', 9, 'hidden', 'hidden', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 97, 1740631596, 1740631596),
(98, 'create_time', '创建日期', 9, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 192, 1740631596, 1740631596),
(99, 'update_time', '修改日期', 9, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 193, 1740631596, 1740631596),
(100, 'id', 'ID', 10, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 100, 1740631596, 1740631596),
(101, 'title', '标题', 10, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 101, 1740631596, 1740631596),
(102, 'create_time', '创建日期', 10, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 110, 1740631596, 1740631596),
(103, 'update_time', '修改日期', 10, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 111, 1740631596, 1740631596),
(104, 'delete_time', '删除日期', 10, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'INDEX', '', 1, 112, 1740631596, 1740631596),
(105, 'id', 'ID', 11, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 105, 1740631596, 1740631596),
(106, 'create_time', '创建日期', 11, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 115, 1740631596, 1740631596),
(107, 'update_time', '修改日期', 11, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 116, 1740631596, 1740631596),
(109, 'salt', '密码盐', 7, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '16', '', 1, 0, 0, 1, 0, '', 'password', 1, 74, 1740631596, 1740631596),
(110, 'model', '模型名', 10, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'title', 1, 102, 1740631596, 1740631596),
(111, 'field', '字段名', 10, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'model', 1, 103, 1740631596, 1740631596),
(112, 'dictionary_item_count', '字典项计数', 10, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'counter', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'SMALLINT', '', '0', 1, 0, 0, 1, 0, '', 'field', 1, 104, 1740631596, 1740631596),
(113, 'dictionary_id', '所属字典', 11, 'relation', '', '', '', 'Dictionary', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"},{\"rule\":\"egt\",\"args\":\"1\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'INT', '', '0', 1, 1, 0, 1, 0, '', 'id', 1, 106, 1740631596, 1740631596),
(114, 'key', '键', 11, 'text', '', '', '', '', '', '{\"tip\":\"为空，将使用ID\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'dictionary_id', 1, 107, 1740631596, 1740631596),
(115, 'value', '值', 11, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'key', 1, 113, 1740631596, 1740631596),
(116, 'list_order', '排序权重', 11, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'value', 1, 114, 1740631596, 1740631596),
(117, 'id', 'ID', 12, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 117, 1740631596, 1740631596),
(118, 'parent_id', '父级ID', 12, 'xmtree', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 118, 1740631596, 1740631596),
(119, 'title', '标题', 12, 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"150\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 119, 1740631596, 1740631596),
(120, 'list_order', '排序权重', 12, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 121, 1740631596, 1740631596),
(121, 'create_time', '创建日期', 12, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 122, 1740631596, 1740631596),
(122, 'update_time', '修改日期', 12, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 123, 1740631596, 1740631596),
(123, 'delete_time', '删除日期', 12, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'INDEX', '', 1, 132, 1740631596, 1740631596),
(124, 'id', 'ID', 13, 'hidden', 'hidden', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 124, 1740631596, 1740631596),
(125, 'title', '附件名', 13, 'text', 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"160\"}', '{\"width\":\"160\"}', '1', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 125, 1740631596, 1740631596),
(126, 'list_order', '排序权重', 13, 'number', 'number', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 141, 1740631596, 1740631596),
(127, 'admin_id', '管理员ID', 13, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 126, 1740631596, 1740631596),
(128, 'user_id', '用户ID', 13, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 127, 1740631596, 1740631596),
(129, 'create_time', '创建日期', 13, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 194, 1740631596, 1740631596),
(130, 'update_time', '修改日期', 13, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 482, 1740631596, 1740631596),
(131, 'delete_time', '删除日期', 13, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 483, 1740631596, 1740631596),
(132, 'ex_title', '副标题', 12, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'title', 1, 120, 1740631596, 1740631596),
(133, 'folder_id', '文件夹', 13, 'relation', 'relation', '', '', 'Folder', 'Folder', '', '', '', '', '', '', '', '', '', '', 'relation', 'relation', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'title', 1, 128, 1740631596, 1740631596),
(134, 'url', 'URL', 13, 'file', 'file', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'file', 'file', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'folder_id', 1, 129, 1740631596, 1740631596),
(135, 'ext', '后缀', 13, 'text', 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'text', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '16', '', 1, 0, 0, 1, 0, '', 'url', 1, 130, 1740631596, 1740631596),
(136, 'size', '大小', 13, 'number', 'number', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'filesize', 'filesize', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '', '0', 1, 1, 0, 1, 0, '', 'ext', 1, 131, 1740631596, 1740631596),
(137, 'type', '类型', 13, 'select', 'select', '', '', '', '', '', '', '', '', '{\"image\":\"图片\",\"file\":\"文件\"}', '{\"image\":\"图片\",\"file\":\"文件\"}', '', '', '', '', '0', '0', '', '', '1', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'size', 1, 133, 1740631596, 1740631596),
(138, 'width', '宽度', 13, 'number', 'number', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'MEDIUMINT', '', '0', 1, 1, 0, 1, 0, '', 'type', 1, 134, 1740631596, 1740631596),
(139, 'height', '高度', 13, 'number', 'number', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'MEDIUMINT', '', '0', 1, 1, 0, 1, 0, '', 'width', 1, 135, 1740631596, 1740631596),
(140, 'model', '模型', 13, 'text', 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'height', 1, 136, 1740631596, 1740631596),
(141, 'driver', '上传方式', 13, 'none', 'none', '', '', '', '', '', '', '', '', '{\"local\":\"本地上传\",\"qiniu\":\"七牛云\",\"oss\":\"阿里云OSS\",\"cos\":\"腾讯云COS\"}', '{\"local\":\"本地上传\",\"qiniu\":\"七牛云\",\"oss\":\"阿里云OSS\",\"cos\":\"腾讯云COS\"}', '', '', '', '', '', '', '', '', '1', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'hash', 1, 137, 1740631596, 1740631596),
(151, 'id', 'ID', 15, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 151, 1740631596, 1740631596),
(152, 'admin_group_id', '角色ID', 15, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', 'id', 1, 152, 1740631596, 1740631596),
(153, 'create_time', '创建日期', 15, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 179, 1740631596, 1740631596),
(154, 'update_time', '修改日期', 15, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 10210, 1740631596, 1740631596),
(155, 'id', 'ID', 16, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 155, 1740631596, 1740631596),
(156, 'title', '标题', 16, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 156, 1740631596, 1740631596),
(157, 'is_verify', '审核', 16, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 159, 1740631596, 1740631596),
(158, 'list_order', '排序权重', 16, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 171, 1740631596, 1740631596),
(159, 'create_time', '创建日期', 16, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 172, 1740631596, 1740631596),
(160, 'update_time', '修改日期', 16, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 173, 1740631596, 1740631596),
(161, 'delete_time', '删除日期', 16, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'INDEX', '', 1, 259, 1740631596, 1740631596),
(162, 'id', 'ID', 17, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 162, 1740631596, 1740631596),
(163, 'title', '标题', 17, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 163, 1740631596, 1740631596),
(164, 'is_verify', '审核', 17, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 165, 1740631596, 1740631596),
(165, 'list_order', '排序权重', 17, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 195, 1740631596, 1740631596),
(166, 'create_time', '创建日期', 17, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 196, 1740631596, 1740631596),
(167, 'update_time', '修改日期', 17, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 260, 1740631596, 1740631596),
(168, 'delete_time', '删除日期', 17, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'INDEX', '', 1, 261, 1740631596, 1740631596),
(169, 'type', '类型', 16, 'select', '', '', '', '', '', '', NULL, '', NULL, '{\"func\":\"JS函数\",\"url\":\"链接跳转\"}', NULL, '', NULL, '{\"func\":\"url|func\",\"url\":\"url|target\"}', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'title', 1, 157, 1740631596, 1740631596),
(170, 'url', 'URL', 16, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'url', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', 'type', 1, 158, 1740631596, 1740631596),
(171, 'func', '函数名', 16, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'url', 1, 160, 1740631596, 1740631596),
(172, 'target', '跳转方式', 16, 'select', '', '', '', '', '', '', NULL, '', NULL, '{\"newtab\":\"Tab打开\",\"blank\":\"新窗口\"}', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'func', 1, 161, 1740631596, 1740631596),
(173, 'icon', '图标', 16, 'icon', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'icon', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'title', 1, 169, 1740631596, 1740631596),
(179, 'content', '授权', 15, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '20480', '', 1, 0, 0, 1, 0, '', 'admin_group_id', 1, 154, 1740631596, 1740631596),
(180, 'admin_id', '操作人ID', 9, 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', 'relation', '', '', '', '', '', '', '0', '', '', '', '', 1, 'INT', '', '0', 1, 0, 0, 1, 0, 'index', 'id', 1, 98, 1740631596, 1740631596),
(181, 'controller', '控制器', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'addon', 1, 99, 1740631596, 1740631596),
(182, 'addon', '二级目录', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'admin_id', 1, 180, 1740631596, 1740631596),
(183, 'action', '方法', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{}', '{}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'controller', 1, 181, 1740631596, 1740631596),
(184, 'method', '请求方式', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{}', '{}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', '', 1, 182, 1740631596, 1740631596),
(185, 'args', '数据', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '10240', '', 1, 0, 0, 1, 0, '', 'method', 1, 183, 1740631596, 1740631596),
(186, 'var', '组变量', 1, 'text', '', '', '', '', '', '{\"filter\":\"trim\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'show.blue', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"unique\",\"args\":\"setting_group\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'title', 1, 3, 1740631596, 1740631596),
(187, 'url', 'URL地址', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'url', 'url', '', '', '1', '1', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '255', '', 1, 0, 0, 1, 0, '', 'action', 1, 184, 1740631596, 1740631596),
(188, 'ip', 'IP地址', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'text', 'text', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'args', 1, 185, 1740631596, 1740631596),
(189, 'user_agent', '客户端', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'ua', 'ua', '', '', '', '', '', '', '', '', 'show', 'show', '', '', '', '', 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', 'ip', 1, 187, 1740631596, 1740631596),
(190, 'is_js_var', 'JS中调用', 2, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker', '', '{\"width\":\"120\"}', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'admin_id', 1, 17, 1740631596, 1740631596),
(191, 'username', '操作人', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'admin_id', 1, 188, 1740631596, 1740631596),
(192, 'region', '操作地址', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'ip', 1, 189, 1740631596, 1740631596),
(193, 'isp', '网络ISP', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'region', 1, 191, 1740631596, 1740631596),
(194, 'hash', 'Hash', 13, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'user_id', 1, 138, 1740631596, 1740631596),
(195, 'model', '模型名称', 17, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'title', 1, 164, 1740631596, 1740631596),
(196, 'is_self', '统计自己', 17, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'model', 1, 166, 1740631596, 1740631596),
(197, 'id', 'ID', 18, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 197, 1740631596, 1740631596),
(198, 'title', '标题', 18, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 198, 1740631596, 1740631596),
(199, 'create_time', '创建日期', 18, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 200, 1740631596, 1740631596),
(200, 'update_time', '修改日期', 18, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 201, 1740631596, 1740631596),
(201, 'summary', '描述', 18, 'textarea', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TEXT', '', 'none', 1, 0, 0, 1, 0, '', 'title', 1, 199, 1740631596, 1740631596),
(202, 'id', 'ID', 19, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 202, 1740631596, 1740631596),
(203, 'create_time', '创建日期', 19, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 230, 1740631596, 1740631596),
(204, 'update_time', '修改日期', 19, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 231, 1740631596, 1740631596),
(205, 'delete_time', '删除日期', 19, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'INDEX', '', 1, 232, 1740631596, 1740631596),
(206, 'username', '用户名', 19, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'username', '', '{\"width\":\"160\",\"title\":\"账号\",\"fixed\":\"left\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"unique\",\"args\":\"user\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 1, 'unique', 'id', 1, 203, 1740631596, 1740631596),
(207, 'password', '密码', 19, 'password', '', 'none', '', '', '', '{\"tip\":\"不修改请保持为空\",\"rsa\":\"true\"}', NULL, '{\"lay-affix\":\"eye\"}', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '0', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"add \",\"message\":\"\"},{\"rule\":\"length\",\"args\":\"6,16\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"call\",\"args\":\"checkPwd\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'username', 1, 204, 1740631596, 1740631596),
(208, 'salt', '密码盐', 19, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '0', '', '', '', '', NULL, 1, 'CHAR', '16', '', 1, 0, 0, 1, 0, '', 'password', 1, 206, 1740631596, 1740631596),
(209, 'user_group_id', '用户组', 19, 'relation', '', '', '', 'UserGroup', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'INT', '', '0', 1, 1, 0, 1, 0, 'index', 'salt', 1, 207, 1740631596, 1740631596),
(210, 'user_grade_id', '等级', 19, 'none', '', '', '', 'UserGrade', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 1, 0, 1, 0, '', 'user_group_id', 1, 208, 1740631596, 1740631596),
(211, 'email', '邮箱', 19, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"minWidth\":\"115\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"email\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"call\",\"args\":\"uniqueWithoutEmpty,email\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, 'index', 'is_bind_mobile', 1, 215, 1740631596, 1740631596),
(212, 'mobile', '手机', 19, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"110\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"mobile\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"call\",\"args\":\"uniqueWithoutEmpty,mobile\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '11', '', 1, 0, 0, 1, 0, 'index', 'user_grade_id', 1, 213, 1740631596, 1740631596),
(213, 'avatar', '头像', 19, 'image', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'avatar', '', '{\"hide\":\"true\"}', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'mobile', 1, 210, 1740631596, 1740631596),
(214, 'status', '状态', 19, 'select', '', '', '', '', '', '', NULL, '', NULL, '{\"verified\":\"已激活\",\"unverified\":\"未激活\",\"banned\":\"已禁用\"}', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'avatar', 1, 211, 1740631596, 1740631596),
(215, 'nickname', '昵称', 19, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 1, '', 'status', 1, 209, 1740631596, 1740631596),
(216, 'sex', '性别', 19, 'select', '', '', '', '', '', '', NULL, '', NULL, '[\"未知\",\"男\",\"女\",\"保密\"]', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"80\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '', '0', 1, 0, 0, 1, 1, '', 'nickname', 1, 217, 1740631596, 1740631596),
(217, 'birthday', '生日', 19, 'date', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"hide\":\"true\"}', NULL, 'date', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 1, '', 'sex', 1, 218, 1740631596, 1740631596);
INSERT INTO `woo_field` (`id`, `field`, `name`, `model_id`, `form`, `business_form`, `modify_form`, `business_modify_form`, `form_foreign`, `business_form_foreign`, `form_item_attrs`, `business_form_item_attrs`, `form_tag_attrs`, `business_form_tag_attrs`, `form_options`, `business_form_options`, `form_upload`, `business_form_upload`, `form_trigger`, `business_form_trigger`, `list`, `business_list`, `list_attrs`, `business_list_attrs`, `list_filter`, `business_list_filter`, `list_filter_attrs`, `business_list_filter_attrs`, `list_filter_tag_attrs`, `business_list_filter_tag_attrs`, `detail`, `business_detail`, `detail_attrs`, `business_detail_attrs`, `validate`, `business_validate`, `is_field`, `type`, `length`, `default`, `is_not_null`, `is_unsigned`, `is_ai`, `is_system`, `is_contribute`, `index`, `after`, `admin_id`, `list_order`, `create_time`, `update_time`) VALUES
(218, 'summary', '简介', 19, 'textarea', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '512', '', 1, 0, 0, 1, 1, '', 'birthday', 1, 221, 1740631596, 1740631596),
(219, 'money', '余额', 19, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, 'number_range', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 1, 0, '', 'summary', 1, 222, 1740631596, 1740631596),
(220, 'score', '积分', 19, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, 'number_range', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 1, 0, '', 'money', 1, 223, 1740631596, 1740631596),
(221, 'login_time', '最后登录时间', 19, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'datetime', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 1, 0, 1, 0, '', 'score', 1, 224, 1740631596, 1740631596),
(222, 'login_ip', '最后登录IP', 19, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'login_time', 1, 225, 1740631596, 1740631596),
(223, 'login_id', '最后登录SESS_ID', 19, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'login_ip', 1, 226, 1740631596, 1740631596),
(224, 'register_ip', '注册IP', 19, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'login_id', 1, 227, 1740631596, 1740631596),
(225, 'truename', '真实姓名', 19, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 1, '', 'status', 1, 216, 1740631596, 1740631596),
(226, 'pay_password', '支付密码', 19, 'password', '', 'none', '', '', '', '{\"tip\":\"不修改请保持为空\",\"rsa\":\"true\"}', NULL, '{\"lay-affix\":\"eye\"}', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '0', '', '', '', '', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'password', 1, 205, 1740631596, 1740631596),
(227, 'is_bind_email', '邮箱绑定', 19, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker.show', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'email', 1, 214, 1740631596, 1740631596),
(228, 'is_bind_mobile', '手机绑定', 19, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker.show', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'mobile', 1, 212, 1740631596, 1740631596),
(229, 'region', '所在地区', 19, 'cascader', '', '', '', 'Region', '', '', NULL, '{\"data-url\":\"true\"}', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"160\"}', NULL, 'cascader', '', '{}', '', '{\"data-url\":\"true\"}', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 1, '', 'birthday', 1, 219, 1740631596, 1740631596),
(230, 'address', '详细地址', 19, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"hide\":\"true\"}', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 1, '', 'region', 1, 220, 1740631596, 1740631596),
(231, 'register_type', '注册方式', 19, 'none', '', '', '', '', '', '', NULL, '', NULL, '{\"wxmini\":\"微信小程序\",\"univerify\":\"APP一键登录\",\"gitee\":\"码云\",\"wechat\":\"公众号\",\"wechat2\":\"微信\",\"weibo\":\"微博\",\"qq\":\"QQ\",\"\":\"账号输入\"}', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'register_ip', 1, 228, 1740631596, 1740631596),
(232, 'is_allow_reset', '是否允许初始化', 19, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 1, '', 'register_type', 1, 229, 1740631596, 1740631596),
(233, 'id', 'ID', 20, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 233, 1740631596, 1740631596),
(234, 'title', '标题', 20, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, 'string', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 234, 1740631596, 1740631596),
(235, 'list_order', '排序权重', 20, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 238, 1740631596, 1740631596),
(236, 'create_time', '创建日期', 20, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 239, 1740631596, 1740631596),
(237, 'update_time', '修改日期', 20, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 240, 1740631596, 1740631596),
(238, 'image', '等级图标', 20, 'image', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'file', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'title', 1, 235, 1740631596, 1740631596),
(239, 'min', '积分最低值', 20, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"integer\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'image', 1, 236, 1740631596, 1740631596),
(240, 'max', '积分最大值', 20, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"integer\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'min', 1, 237, 1740631596, 1740631596),
(241, 'id', 'ID', 21, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 241, 1740631596, 1740631596),
(242, 'create_time', '创建日期', 21, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 247, 1740631596, 1740631596),
(243, 'update_time', '修改日期', 21, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 248, 1740631596, 1740631596),
(244, 'user_id', '用户ID', 21, 'relation', '', '', '', 'User', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"},{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'INT', '', '0', 1, 1, 0, 1, 0, 'index', 'id', 1, 242, 1740631596, 1740631596),
(245, 'score', '积分', 21, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"float\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 1, 0, '', 'before', 1, 243, 1740631596, 1740631596),
(246, 'before', '变动前', 21, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 1, 0, '', 'score', 1, 244, 1740631596, 1740631596),
(247, 'after', '变动后', 21, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 1, 0, '', 'score', 1, 245, 1740631596, 1740631596),
(248, 'remark', '备注', 21, 'textarea', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '512', '', 1, 0, 0, 1, 0, '', 'after', 1, 246, 1740631596, 1740631596),
(249, 'id', 'ID', 22, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 249, 1740631596, 1740631596),
(250, 'user_id', '用户ID', 22, 'relation', '', '', '', 'User', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', 'id', 1, 250, 1740631596, 1740631596),
(251, 'create_time', '创建日期', 22, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 258, 1740631596, 1740631596),
(252, 'update_time', '修改日期', 22, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 293, 1740631596, 1740631596),
(253, 'delete_time', '删除日期', 22, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'INDEX', '', 1, 294, 1740631596, 1740631596),
(254, 'delete_time', '删除日期', 21, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 1, 0, 1, 0, '', 'update_time', 1, 254, 1740631596, 1740631596),
(255, 'money', '金额', 22, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"float\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 1, 0, '', 'before', 1, 251, 1740631596, 1740631596),
(256, 'before', '变动前', 22, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 1, 0, '', 'money', 1, 252, 1740631596, 1740631596),
(257, 'after', '变动后', 22, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 1, 0, '', 'money', 1, 253, 1740631596, 1740631596),
(258, 'remark', '备注', 22, 'textarea', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '512', '', 1, 0, 0, 1, 0, '', 'after', 1, 255, 1740631596, 1740631596),
(259, 'admin_group_id', '用户组ID', 16, 'xmtree', '', '', '', 'AdminGroup', '', '{\"tip\":\"如果不选，每个用户都会显示\"}', NULL, '{\"data-max\":\"20\"}', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 1, 0, 1, 0, '', 'list_order', 1, 170, 1740631596, 1740631596),
(260, 'admin_group_id', '用户组ID', 17, 'xmtree', '', '', '', 'AdminGroup', '', '{\"tip\":\"如果不选，每个用户都会显示\"}', NULL, '{\"data-max\":\"20\"}', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 1, 0, 1, 0, '', 'list_order', 1, 167, 1740631596, 1740631596),
(261, 'url', 'URL地址', 17, 'text', '', '', '', '', '', '{\"tip\":\"如果不填，默认链接到index方法\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'url', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'admin_group_id', 1, 168, 1740631596, 1740631596),
(262, 'id', 'ID', 23, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 262, 1740631596, 1740631596),
(263, 'parent_id', '父级ID', 23, 'xmtree', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'INDEX', '', 1, 263, 1740631596, 1740631596),
(264, 'title', '标题', 23, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 264, 1740631596, 1740631596),
(265, 'list_order', '排序权重', 23, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 265, 1740631596, 1740631596),
(266, 'family', '家族', 23, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', '', 1, 266, 1740631596, 1740631596),
(267, 'level', '层级', 23, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'SMALLINT', '5', '0', 1, 1, 0, 1, 0, '', '', 1, 267, 1740631596, 1740631596),
(268, 'children_count', '下级数', 23, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'SMALLINT', '5', '0', 1, 1, 0, 1, 0, '', '', 1, 268, 1740631596, 1740631596),
(269, 'create_time', '创建日期', 23, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 269, 1740631596, 1740631596),
(270, 'update_time', '修改日期', 23, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 270, 1740631596, 1740631596),
(271, 'id', 'ID', 24, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 271, 1740631596, 1740631596),
(272, 'title', '标题', 24, 'text', '', '', '', '', '', '', NULL, '', NULL, '{}', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 272, 1740631596, 1740631596),
(273, 'is_verify', '审核', 24, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 274, 1740631596, 1740631596),
(274, 'date', '日期', 24, 'date', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DATE', '', '2000-01-01', 1, 0, 0, 1, 0, '', '', 1, 275, 1740631596, 1740631596),
(275, 'image', '图片', 24, 'image', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'file', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 276, 1740631596, 1740631596),
(276, 'content', '内容', 24, 'ckeditor', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'MEDIUMTEXT', '', 'none', 1, 0, 0, 1, 0, '', '', 1, 277, 1740631596, 1740631596),
(277, 'list_order', '排序权重', 24, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 278, 1740631596, 1740631596),
(278, 'admin_id', '管理员ID', 24, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 279, 1740631596, 1740631596),
(279, 'create_time', '创建日期', 24, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 282, 1740631596, 1740631596),
(280, 'update_time', '修改日期', 24, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 283, 1740631596, 1740631596),
(281, 'delete_time', '删除日期', 24, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'INDEX', '', 1, 284, 1740631596, 1740631596),
(282, 'test_menu_id', '所属分类', 24, 'xmtree', '', '', '', 'TestMenu', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"},{\"rule\":\"egt\",\"args\":\"1\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'title', 1, 273, 1740631596, 1740631596),
(283, 'author', '作者', 24, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'date', 1, 281, 1740631596, 1740631596),
(284, 'from', '来源', 24, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'author', 1, 280, 1740631596, 1740631596),
(285, 'id', 'ID', 25, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 285, 1740631596, 1740631596),
(286, 'user_id', '用户ID', 25, 'relation', '', '', '', 'User', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"},{\"rule\":\"float\",\"args\":\"\",\"on\":\"\",\"message\":\"\"},{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 286, 1740631596, 1740631596),
(287, 'create_time', '创建日期', 25, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 290, 1740631596, 1740631596),
(288, 'update_time', '修改日期', 25, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 291, 1740631596, 1740631596),
(289, 'delete_time', '删除日期', 25, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'INDEX', '', 1, 292, 1740631596, 1740631596),
(290, 'money', '金额', 25, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 1, 0, '', 'user_id', 1, 287, 1740631596, 1740631596),
(291, 'type', '充值方式', 25, 'select', '', '', '', '', '', '', NULL, '', NULL, '{\"zfb\":\"支付宝\",\"wx\":\"微信\",\"yl\":\"银联\",\"rg\":\"人工\"}', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'money', 1, 288, 1740631596, 1740631596),
(292, 'remark', '备注', 25, 'textarea', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '512', '', 1, 0, 0, 1, 0, '', 'type', 1, 289, 1740631596, 1740631596),
(293, 'foreign', '关联模型', 22, 'select', '', '', '', '', '', '', NULL, '', NULL, '{\"Recharge\":\"充值\",\"Order\":\"订单\",\"CashOut\":\"提现\"}', NULL, '', NULL, '', NULL, 'options', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'after', 1, 256, 1740631596, 1740631596),
(294, 'foreign_id', '关联模型ID', 22, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 1, 0, 1, 0, '', 'foreign', 1, 257, 1740631596, 1740631596),
(295, 'id', 'ID', 26, 'hidden', 'hidden', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 295, 1740631596, 1740631596),
(296, 'title', '标题', 26, 'text', 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '1', '1', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 296, 1740631596, 1740631596),
(297, 'file', '文件', 26, 'file', 'file', '', '', '', '', '', '', '', '', '', '', '{\"validExt\":\"xlsx|xls|csv\",\"sizeField\":\"file_size\",\"nameFiled\":\"file_name\",\"maxSize\":\"100\"}', '{\"validExt\":\"xlsx|xls|csv\",\"sizeField\":\"file_size\",\"nameFiled\":\"file_name\",\"maxSize\":\"100\"}', '', '', 'file', 'file', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 297, 1740631596, 1740631596),
(298, 'admin_id', '管理员ID', 26, '', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 303, 1740631596, 1740631596),
(299, 'create_time', '创建日期', 26, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 10204, 1740631596, 1740631596),
(300, 'update_time', '修改日期', 26, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 10205, 1740631596, 1740631596),
(301, 'file_name', '文件名', 26, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'file', 1, 298, 1740631596, 1740631596),
(302, 'file_size', '文件大小', 26, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'filesize', 'filesize', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'file_name', 1, 299, 1740631596, 1740631596),
(303, 'model_id', '模型', 26, 'select', 'select', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'select', 'select', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"0\",\"message\":\"\"}]', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"0\",\"message\":\"\"}]', 1, 'INT', '', '0', 1, 1, 0, 1, 0, '', 'title', 1, 300, 1740631596, 1740631596),
(304, 'is_import', '是否导入', 26, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'checker', 'checker', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'file', 1, 301, 1740631596, 1740631596),
(305, 'type', '执行方式', 26, 'select', 'select', '', '', '', '', '{\"tip\":\"Db减少数据库执行次数，但不会进行数据验证、模型事件等；Model一次插入一条，可以自动验证和执行模型事件\"}', '{\"tip\":\"Db减少数据库执行次数，但不会进行数据验证、模型事件等；Model一次插入一条，可以自动验证和执行模型事件\"}', '{}', '{}', '{\"db\":\"Db\",\"model\":\"Model\"}', '{\"db\":\"Db\",\"model\":\"Model\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'admin_id', 1, 302, 1740631596, 1740631596),
(306, 'id', 'ID', 27, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 306, 1740631596, 1740631596),
(307, 'title', '应用名称', 27, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'name', 1, 307, 1740631596, 1740631596),
(308, 'is_verify', '是否启用', 27, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 308, 1740631596, 1740631596),
(309, 'is_disuninstall', '禁止卸载', 27, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 309, 1740631596, 1740631596),
(310, 'describe', '应用描述', 27, 'textarea', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'MEDIUMTEXT', '', 'none', 1, 0, 0, 1, 0, '', '', 1, 310, 1740631596, 1740631596),
(311, 'admin_id', '管理员ID', 27, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 311, 1740631596, 1740631596),
(312, 'create_time', '创建日期', 27, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 316, 1740631596, 1740631596),
(313, 'update_time', '修改日期', 27, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 317, 1740631596, 1740631596),
(314, 'name', '应用目录', 27, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"unique\",\"args\":\"application\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"regex\",\"args\":\"\\/^[a-z]+[a-z0-9]+$\\/i\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'id', 1, 312, 1740631596, 1740631596),
(315, 'author', '作者', 27, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'title', 1, 313, 1740631596, 1740631596),
(316, 'version', '版本', 27, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'author', 1, 314, 1740631596, 1740631596),
(317, 'is_api', '是否API', 27, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'is_verify', 1, 315, 1740631596, 1740631596),
(318, 'id', 'ID', 28, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 318, 1740631596, 1740631596),
(319, 'title', '敏感词', 28, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 319, 1740631596, 1740631596),
(320, 'is_verify', '审核', 28, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 320, 1740631596, 1740631596),
(321, 'admin_id', '管理员ID', 28, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 321, 1740631596, 1740631596),
(322, 'create_time', '创建日期', 28, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 322, 1740631596, 1740631596),
(323, 'update_time', '修改日期', 28, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 323, 1740631596, 1740631596),
(324, 'id', 'ID', 29, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 1, '', '', 1, 324, 1740631596, 1740631596),
(325, 'username', '用户名', 29, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 1, '', '', 1, 325, 1740631596, 1740631596),
(326, 'user_id', '用户ID', 29, 'relation', '', '', '', 'User', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 1, 'index', '', 1, 326, 1740631596, 1740631596),
(327, 'create_time', '创建日期', 29, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 1, '', '', 1, 333, 1740631596, 1740631596),
(328, 'update_time', '修改日期', 29, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 334, 1740631596, 1740631596),
(329, 'ip', '登录IP', 29, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 1, '', 'user_id', 1, 327, 1740631596, 1740631596),
(330, 'user_agent', '客户端', 29, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 1, '', 'ip', 1, 328, 1740631596, 1740631596),
(331, 'region', '登录地址', 29, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 1, '', 'user_agent', 1, 329, 1740631596, 1740631596),
(332, 'summary', '描述', 29, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 1, '', 'region', 1, 330, 1740631596, 1740631596),
(333, 'is_success', '是否成功', 29, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 0, 0, '', 'summary', 1, 331, 1740631596, 1740631596),
(334, 'type', '登录方式', 29, 'none', '', '', '', '', '', '', NULL, '', NULL, '{\"wxmini\":\"微信小程序\",\"univerify\":\"APP一键登录\",\"gitee\":\"码云\",\"wechat\":\"公众号\",\"wechat2\":\"微信\",\"weibo\":\"微博\",\"qq\":\"QQ\",\"\":\"账号输入\"}', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'is_success', 1, 332, 1740631596, 1740631596),
(335, 'id', 'ID', 30, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 335, 1740631596, 1740631596),
(336, 'user_id', '用户ID', 30, 'relation', '', '', '', 'User', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"egt\",\"args\":\"1\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"unique\",\"args\":\"certification\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 336, 1740631596, 1740631596),
(337, 'create_time', '创建日期', 30, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 343, 1740631596, 1740631596),
(338, 'update_time', '修改日期', 30, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 344, 1740631596, 1740631596),
(339, 'delete_time', '删除日期', 30, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 345, 1740631596, 1740631596),
(340, 'truename', '姓名', 30, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'user_id', 1, 337, 1740631596, 1740631596),
(341, 'mobile', '手机', 30, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"mobile\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"unique\",\"args\":\"certification\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '16', '', 1, 0, 0, 1, 0, '', 'truename', 1, 338, 1740631596, 1740631596),
(342, 'id_card', '身份证', 30, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"200\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"idCard\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'mobile', 1, 339, 1740631596, 1740631596),
(343, 'id_card_front', '身份证正面', 30, 'image', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'file', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'id_card', 1, 340, 1740631596, 1740631596),
(344, 'id_card_back', '身份证背面', 30, 'image', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'file', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'id_card_front', 1, 341, 1740631596, 1740631596),
(345, 'is_cert', '通过', 30, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker.show', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'id_card_back', 1, 342, 1740631596, 1740631596),
(346, 'id', 'ID', 31, 'hidden', 'hidden', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 346, 1740631596, 1740631596),
(347, 'title', '审核模型', 31, 'text', 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 347, 1740631596, 1740631596),
(348, 'content', '审核内容', 31, 'textarea', 'textarea', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'MEDIUMTEXT', '', 'none', 1, 0, 0, 1, 0, '', 'is_verify', 1, 348, 1740631596, 1740631596),
(349, 'admin_id', '管理员ID', 31, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 349, 1740631596, 1740631596),
(350, 'create_time', '创建日期', 31, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 355, 1740631596, 1740631596),
(351, 'update_time', '修改日期', 31, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 356, 1740631596, 1740631596),
(352, 'foreign_id', '模型ID', 31, 'number', 'number', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'title', 1, 350, 1740631596, 1740631596),
(353, 'is_verify', '审核状态', 31, 'checker', 'checker', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'checker.show', 'checker.show', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'foreign_id', 1, 351, 1740631596, 1740631596),
(354, 'msg', '内容提示', 31, 'textarea', 'textarea', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TEXT', '', 'none', 1, 0, 0, 1, 0, '', 'result', 1, 352, 1740631596, 1740631596),
(355, 'words', '不合格字符', 31, 'textarea', 'textarea', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TEXT', '', 'none', 1, 0, 0, 1, 0, '', 'msg', 1, 353, 1740631596, 1740631596),
(356, 'result', '返回结果', 31, 'textarea', 'textarea', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TEXT', '', 'none', 1, 0, 0, 1, 0, '', 'content', 1, 354, 1740631596, 1740631596),
(357, 'id', 'ID', 32, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 357, 1740631596, 1740631596),
(358, 'parent_id', '父级ID', 32, 'xmtree', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"call\",\"args\":\"checkParent\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 358, 1740631596, 1740631596),
(359, 'title', '菜单标题', 32, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 359, 1740631596, 1740631596),
(360, 'is_nav', '是否菜单', 32, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 362, 1740631596, 1740631596),
(361, 'url', '规则', 32, 'text', '', '', '', '', '', '{\"tip\":\"控制器请写小写加下划线形式；如：user.ad_position/index\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'show.blue', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', 'title', 1, 360, 1740631596, 1740631596),
(362, 'list_order', '排序权重', 32, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 364, 1740631596, 1740631596),
(363, 'admin_id', '管理员ID', 32, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 366, 1740631596, 1740631596),
(364, 'family', '家族', 32, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', '', 1, 367, 1740631596, 1740631596),
(365, 'level', '层级', 32, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'SMALLINT', '5', '0', 1, 1, 0, 1, 0, '', '', 1, 368, 1740631596, 1740631596),
(366, 'children_count', '下级数', 32, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'SMALLINT', '5', '0', 1, 1, 0, 1, 0, '', '', 1, 369, 1740631596, 1740631596),
(367, 'remark', '备注', 32, 'textarea', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TEXT', '', 'none', 1, 0, 0, 1, 0, '', 'list_order', 1, 370, 1740631596, 1740631596),
(368, 'icon', '图标', 32, 'icon', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'icon', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'url', 1, 361, 1740631596, 1740631596),
(369, 'args', '参数', 32, 'keyvalue', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '512', '', 1, 0, 0, 1, 0, '', 'icon', 1, 365, 1740631596, 1740631596),
(370, 'create_time', '创建日期', 32, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 402, 1740631596, 1740631596),
(371, 'update_time', '修改日期', 32, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 403, 1740631596, 1740631596),
(372, 'is_not_power', '不关心权限', 32, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'is_nav', 1, 363, 1740631596, 1740631596),
(373, 'id', 'ID', 33, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 373, 1740631596, 1740631596),
(374, 'create_time', '创建日期', 33, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 379, 1740631596, 1740631596),
(375, 'update_time', '修改日期', 33, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 380, 1740631596, 1740631596),
(376, 'username', '用户名', 33, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'model', 1, 374, 1740631596, 1740631596),
(377, 'ip', 'IP地址', 33, 'ip4', '', '', '', '', '', '{\"message\":\"值为空即所有IP全封\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '32', '', 1, 0, 0, 1, 0, '', 'username', 1, 375, 1740631596, 1740631596),
(378, 'expire', '到期时间', 33, 'datetime', '', '', '', '', '', '{\"type\":\"time\",\"message\":\"为空即长时间限制登录，用户重置密码以后自动解除；也可以禁用账号来永久禁用\"}', NULL, '[]', NULL, '', NULL, '', NULL, '', NULL, 'datetime', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'ip', 1, 376, 1740631596, 1740631596),
(379, 'is_verify', '是否执行', 33, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', 'expire', 1, 377, 1740631596, 1740631596),
(380, 'model', '登录模型', 33, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', 'id', 1, 378, 1740631596, 1740631596),
(381, 'id', 'ID', 34, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 381, 1740631596, 1740631596),
(382, 'date', '日期', 34, 'date', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"dateFormat:Y-m-d\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"unique\",\"args\":\"sign,user_id^date\",\"on\":\"0\",\"message\":\"该日期已签到\"},{\"rule\":\"call\",\"args\":\"checkToday\",\"on\":\"\",\"message\":\"\"}]', NULL, 1, 'DATE', '', '2000-01-01', 1, 0, 0, 1, 0, 'index', 'user_id', 1, 382, 1740631596, 1740631596),
(383, 'user_id', '用户ID', 34, 'relation', '', '', '', 'User', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, 'relation', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', 'id', 1, 383, 1740631596, 1740631596),
(384, 'create_time', '创建日期', 34, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 390, 1740631596, 1740631596),
(385, 'update_time', '修改日期', 34, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 391, 1740631596, 1740631596),
(386, 'year', '年', 34, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'date', 1, 384, 1740631596, 1740631596);
INSERT INTO `woo_field` (`id`, `field`, `name`, `model_id`, `form`, `business_form`, `modify_form`, `business_modify_form`, `form_foreign`, `business_form_foreign`, `form_item_attrs`, `business_form_item_attrs`, `form_tag_attrs`, `business_form_tag_attrs`, `form_options`, `business_form_options`, `form_upload`, `business_form_upload`, `form_trigger`, `business_form_trigger`, `list`, `business_list`, `list_attrs`, `business_list_attrs`, `list_filter`, `business_list_filter`, `list_filter_attrs`, `business_list_filter_attrs`, `list_filter_tag_attrs`, `business_list_filter_tag_attrs`, `detail`, `business_detail`, `detail_attrs`, `business_detail_attrs`, `validate`, `business_validate`, `is_field`, `type`, `length`, `default`, `is_not_null`, `is_unsigned`, `is_ai`, `is_system`, `is_contribute`, `index`, `after`, `admin_id`, `list_order`, `create_time`, `update_time`) VALUES
(387, 'month', '月', 34, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'year', 1, 385, 1740631596, 1740631596),
(388, 'day', '日', 34, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'month', 1, 386, 1740631596, 1740631596),
(389, 'score', '获得积分', 34, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'day', 1, 387, 1740631596, 1740631596),
(390, 'continue', '连续天数', 34, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', 'score', 1, 388, 1740631596, 1740631596),
(391, 'time', '签到时间', 34, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'CHAR', '32', '', 1, 0, 0, 1, 0, '', 'continue', 1, 389, 1740631596, 1740631596),
(392, 'id', 'ID', 35, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 392, 1740631596, 1740631596),
(393, 'user_group_id', '所属用户组ID', 35, 'relation', '', '', '', 'UserGroup', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 393, 1740631596, 1740631596),
(394, 'content', '内容', 35, 'none', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '0', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'MEDIUMTEXT', '', 'none', 1, 0, 0, 1, 0, '', '', 1, 394, 1740631596, 1740631596),
(395, 'create_time', '创建日期', 35, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 395, 1740631596, 1740631596),
(396, 'update_time', '修改日期', 35, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 396, 1740631596, 1740631596),
(397, 'is_uni', '移动端栏目', 32, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 0, 0, '', '', 1, 371, 1740631596, 1740631596),
(398, 'uni_route_type', '跳转方式', 32, 'select', '', '', '', '', '', '', NULL, '', NULL, '{\"navigateTo\":\"navigateTo\",\"redirectTo\":\"redirectTo\",\"switchTab\":\"switchTab\",\"reLaunch\":\"reLaunch\",\"navigateAuth\":\"navigateTo（默认）会先判断是否登录\"}', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 0, 0, '', '', 1, 372, 1740631596, 1740631596),
(399, 'uni_route', '跳转路由', 32, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 0, 0, '', '', 1, 397, 1740631596, 1740631596),
(400, 'uni_icon', '字体图标', 32, 'text', '', '', '', '', '', '{\"message\":\"填写uview图标名称https://www.uviewui.com/components/icon.html\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 0, 0, '', '', 1, 398, 1740631596, 1740631596),
(401, 'uni_icon_image', '图片图标', 32, 'image', '', '', '', '', '', '{\"message\":\"自行设计图标更美观，优先级高于字体图标\"}', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 0, 0, '', '', 1, 399, 1740631596, 1740631596),
(402, 'uni_click_func', '点击回调', 32, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 0, 0, '', '', 1, 400, 1740631596, 1740631596),
(403, 'is_uni_index', '快捷显示', 32, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 0, 0, '', '', 1, 401, 1740631596, 1740631596),
(404, 'id', 'ID', 36, 'hidden', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 451, 1740631596, 1740631596),
(405, 'title', '标题', 36, 'text', '', '', '', '', '', '', NULL, '{\"class\":\"[\\\"tooltip\\\"]\",\"data-tip-text\":\"请输入\",\"data-tip-bg\":\"#ff0000\"}', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"120\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 453, 1740631596, 1740631596),
(406, 'is_verify', '审核', 36, 'checker', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, 'checker', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 457, 1740631596, 1740631596),
(407, 'image', '图片', 36, 'multiimage', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '{\"resizeWidth\":\"200\"}', NULL, '', NULL, 'file', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '1024', '', 1, 0, 0, 1, 0, '', '', 1, 458, 1740631596, 1740631596),
(408, 'list_order', '排序权重', 36, 'number', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 459, 1740631596, 1740631596),
(409, 'create_time', '创建日期', 36, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 460, 1740631596, 1740631596),
(410, 'update_time', '修改日期', 36, '', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 461, 1740631596, 1740631596),
(411, 'number', '产品编号', 36, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '{\"fixed\":\"left\"}', NULL, '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 0, 0, '', '', 1, 452, 1740631596, 1740631596),
(412, 'price', '成本价', 36, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 0, 0, '', '', 1, 455, 1740631596, 1740631596),
(413, 'sell_price', '销售价', 36, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'DECIMAL', '10,2', '0', 1, 0, 0, 0, 0, '', '', 1, 456, 1740631596, 1740631596),
(414, 'unit', '单位', 36, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 0, 0, '', '', 1, 454, 1740631596, 1740631596),
(415, 'business_member_id', '商家用户ID', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 10138, 1740631596, 1740631596),
(416, 'business_id', '商家ID', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 10139, 1740631596, 1740631596),
(417, 'appname', '应用名', 9, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', 1, 'CHAR', '16', '', 1, 0, 0, 1, 0, '', '', 1, 10140, 1740631596, 1740631596),
(418, 'business_id', '商家ID', 26, 'none', 'none', '', '', 'Business', 'Business', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', '', 1, 304, 1740631596, 1740631596),
(419, 'business_member_id', '商家用户ID', 26, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 305, 1740631596, 1740631596),
(420, 'business_id', '商家ID', 31, 'none', 'none', '', '', 'Business', 'Business', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', '', 1, 10206, 1740631596, 1740631596),
(421, 'business_member_id', '商家用户ID', 31, 'none', 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 10207, 1740631596, 1740631596),
(422, 'id', 'ID', 37, 'hidden', 'hidden', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 422, 1740631596, 1740631596),
(423, 'parent_id', '父级ID', 37, 'xmtree', 'xmtree', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"call\",\"args\":\"checkParent\",\"on\":\"\",\"message\":\"\"}]', '[{\"rule\":\"call\",\"args\":\"checkParent\",\"on\":\"\",\"message\":\"\"}]', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 423, 1740631596, 1740631596),
(424, 'title', '标题', 37, 'text', 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 424, 1740631596, 1740631596),
(425, 'is_nav', '是否显示', 37, 'checker', 'checker', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'checker', 'checker', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 429, 1740631596, 1740631596),
(426, 'list_order', '排序权重', 37, 'number', 'number', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 441, 1740631596, 1740631596),
(427, 'family', '家族', 37, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', '', 1, 425, 1740631596, 1740631596),
(428, 'level', '层级', 37, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'SMALLINT', '5', '0', 1, 1, 0, 1, 0, '', '', 1, 426, 1740631596, 1740631596),
(429, 'children_count', '下级数', 37, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'SMALLINT', '5', '0', 1, 1, 0, 1, 0, '', '', 1, 427, 1740631596, 1740631596),
(430, 'create_time', '创建日期', 37, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 443, 1740631596, 1740631596),
(431, 'update_time', '修改日期', 37, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 444, 1740631596, 1740631596),
(432, 'type', '类型', 37, 'radio', '', '', '', '', '', '', '', '', '', '{\"directory\":\"目录\",\"menu\":\"菜单\",\"button\":\"按钮\"}', '', '', '', '{\"menu\":\"addon|controller|action|url|args|icon|open_type\",\"button\":\"addon|controller|action|url|args\",\"directory\":\"icon\"}', '', '', '', '', '', '1', '', '', '', '', '', '', '', '', '', '', '', 1, 'CHAR', '32', '', 1, 0, 0, 1, 0, '', '', 1, 428, 1740631596, 1740631596),
(433, 'icon', '图标', 37, 'icon', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'icon', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 430, 1740631596, 1740631596),
(434, 'addon', '二级目录', 37, 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 431, 1740631596, 1740631596),
(435, 'controller', '控制器', 37, 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"call\",\"args\":\"checkController\",\"on\":\"\",\"message\":\"\"}]', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 432, 1740631596, 1740631596),
(436, 'action', '方法', 37, 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"call\",\"args\":\"checkAction\",\"on\":\"\",\"message\":\"\"}]', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 433, 1740631596, 1740631596),
(437, 'url', '路由', 37, 'text', '', '', '', '', '', '{\"tip\":\"单独定义路由需要填写\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 434, 1740631596, 1740631596),
(438, 'args', '参数', 37, 'keyvalue', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', '', 1, 435, 1740631596, 1740631596),
(439, 'open_type', '打开方式', 37, 'select', '', '', '', '', '', '', '', '', '', '{\"_iframe\":\"选项卡(默认)\",\"_blank\":\"新窗口\",\"_ajax\":\"Ajax请求\",\"_event\":\"JS事件回调\",\"_auto\":\"自动获取表单加载\",\"_layer\":\"嵌入弹窗\",\"_drawer\":\"嵌入抽屉\",\"_open\":\"独立窗口\"}', '', '', '', '{\"_event\":\"js_func\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 436, 1740631596, 1740631596),
(440, 'jianpin', '简拼', 37, 'text', '', '', '', '', '', '{\"tip\":\"如不填写，系统自动获取\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 438, 1740631596, 1740631596),
(441, 'pinyin', '拼音', 37, 'text', '', '', '', '', '', '{\"tip\":\"如不填写，系统自动获取\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 439, 1740631596, 1740631596),
(442, 'rule', '路由规则', 37, 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 440, 1740631596, 1740631596),
(443, 'other_name', '第三方名称', 37, 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 442, 1740631596, 1740631596),
(444, 'js_func', '回调事件名', 37, 'text', '', '', '', '', '', '{\"tip\":\"需自行定义该名称的全局JS函数\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 437, 1740631596, 1740631596),
(445, 'id', 'ID', 38, 'hidden', 'hidden', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 445, 1740631596, 1740631596),
(446, 'parent_id', '父级ID', 38, 'xmtree', 'xmtree', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"hide\":\"true\"}', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"call\",\"args\":\"checkParent\",\"on\":\"0\",\"message\":\"\"}]', '[{\"rule\":\"call\",\"args\":\"checkParent\",\"on\":\"\",\"message\":\"\"}]', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 447, 1740631596, 1740631596),
(447, 'title', '角色名', 38, 'text', 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"240\"}', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 446, 1740631596, 1740631596),
(448, 'list_order', '排序权重', 38, 'number', 'number', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"120\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 452, 1740631596, 1740631596),
(449, 'family', '家族', 38, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', '', 1, 453, 1740631596, 1740631596),
(450, 'level', '层级', 38, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'SMALLINT', '5', '0', 1, 1, 0, 1, 0, '', '', 1, 454, 1740631596, 1740631596),
(451, 'children_count', '下级数', 38, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '{\"hide\":\"true\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'SMALLINT', '5', '0', 1, 1, 0, 1, 0, '', '', 1, 455, 1740631596, 1740631596),
(452, 'create_time', '创建日期', 38, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 456, 1740631596, 1740631596),
(453, 'update_time', '修改日期', 38, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 463, 1740631596, 1740631596),
(454, 'dashboard', '主面板URL', 38, 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'url', '', '{\"width\":\"200\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 448, 1740631596, 1740631596),
(455, 'data_allow', '数据权限', 38, 'select', '', '', '', '', '', '', '', '', '', '{\"0\":\"全部数据权限\",\"1\":\"仅本人数据权限\",\"2\":\"本部门数据权限\",\"3\":\"部门及以下数据权限\",\"4\":\"自定义数据权限\",\"5\":\"所在顶级及以下部门权限\",\"-1\":\"继承父角色权限\"}', '', '', '', '{\"4\":\"custom_data_allow\"}', '', '', '', '{\"width\":\"160\"}', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"call\",\"args\":\"checkDataAllow\",\"on\":\"0\",\"message\":\"\"}]', '', 1, 'TINYINT', '', '0', 1, 0, 0, 1, 0, '', '', 1, 450, 1740631596, 1740631596),
(456, 'custom_data_allow', '自定义数据权限', 38, 'xmtree', '', '', '', 'Department', '', '', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', '', 1, 451, 1740631596, 1740631596),
(457, 'id', 'ID', 39, 'hidden', 'hidden', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 457, 1740631596, 1740631596),
(458, 'admin_id', '所属管理员ID', 39, 'relation', 'relation', '', '', 'Admin', '', '', '', '', '', '', '', '', '', '', '', 'relation', '', '', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"0\",\"message\":\"\"}]', '[{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"0\",\"message\":\"\"}]', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 458, 1740631596, 1740631596),
(459, 'admin_group_id', '所属角色ID', 39, 'xmtree', 'xmtree', '', '', 'AdminGroup', '', '', '', '', '', '', '', '', '', '', '', 'relation', '', '', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"0\",\"message\":\"\"}]', '[{\"rule\":\"gt\",\"args\":\"0\",\"on\":\"0\",\"message\":\"\"}]', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 459, 1740631596, 1740631596),
(460, 'create_time', '创建日期', 39, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 460, 1740631596, 1740631596),
(461, 'update_time', '修改日期', 39, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 461, 1740631596, 1740631596),
(462, 'admin_group_id', '所属角色组', 7, 'xmtree', '', '', '', 'AdminGroup', '', '{\"tip\":\"无角色将禁止登录\"}', '', '', '', '', '', '', '', '', '', 'relation', '', '{\"width\":\"180\"}', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"call\",\"args\":\"checkAdminGroup\",\"on\":\"0\",\"message\":\"\"}]', '', 0, 'INT', '', '0', 1, 0, 0, 1, 0, '', '', 1, 83, 1740631596, 1740631596),
(463, 'is_admin', '后台登录', 38, 'checker', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'checker.show', '', '{\"width\":\"110\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', '', 1, 449, 1740631596, 1740631596),
(464, 'id', 'ID', 40, 'hidden', 'hidden', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"}]', '', 1, 'INT', '10', 'none', 1, 1, 1, 1, 0, '', '', 1, 464, 1740631596, 1740631596),
(465, 'model_id', '所属模型ID', 40, 'none', 'relation', '', '', 'Model', '', '', '', '', '', '', '', '', '', '', '', 'relation', '', '{\"width\":\"180\"}', '', 'relation', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"gt\",\"args\":0,\"on\":0,\"message\":\"\"}]', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, 'index', '', 1, 466, 1740631596, 1740631596),
(466, 'title', '按钮标题', 40, 'text', 'text', '', '', '', '', '{\"form_group\":\"tool\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '1', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":0,\"message\":\"\"}]', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 469, 1740631596, 1740631596),
(467, 'is_verify', '审核', 40, 'checker', 'checker', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'checker', 'checker', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 473, 1740631596, 1740631596),
(468, 'list_order', '排序权重', 40, 'number', 'number', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"110\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '11', '0', 1, 0, 0, 1, 0, 'index', '', 1, 10212, 1740631596, 1740631596),
(469, 'create_time', '创建日期', 40, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 10213, 1740631596, 1740631596),
(470, 'update_time', '修改日期', 40, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"146\"}', '{\"width\":\"146\"}', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 10214, 1740631596, 1740631596),
(471, 'icon', '图标', 40, 'icon', '', '', '', '', '', '{\"form_group\":\"tool\"}', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 477, 1740631596, 1740631596),
(472, 'hover', 'hover名称', 40, 'text', '', '', '', '', '', '{\"form_group\":\"tool\"}', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 478, 1740631596, 1740631596),
(473, 'where', '渲染条件', 40, 'text', '', '', '', '', '', '{\"form_group\":\"tool\"}', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', '', 1, 479, 1740631596, 1740631596),
(474, 'action', '独立方法', 40, 'text', '', '', '', '', '', '{\"tip\":\"独立方法需自行编码\",\"filter\":\"trim\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 470, 1740631596, 1740631596),
(475, 'where_type', '条件方式', 40, 'select', '', '', '', '', '', '{\"form_group\":\"tool\"}', '', '', '', '{\"disabled\":\"禁用\",\"hidden\":\"隐藏\"}', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 480, 1740631596, 1740631596),
(476, 'is_btn', '列表按钮', 40, 'checker', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'checker', '', '{\"width\":\"120\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', '', 1, 474, 1740631596, 1740631596),
(477, 'var', '标识符', 40, 'text', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"140\"}', '', '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"require\",\"args\":\"\",\"on\":\"0\",\"message\":\"\"},{\"rule\":\"unique\",\"args\":\"form_scene,model_id^var\",\"on\":\"0\",\"message\":\"\"}]', '', 1, 'CHAR', '32', '', 1, 0, 0, 1, 0, '', '', 1, 465, 1740631596, 1740631596),
(478, 'fields', '表单字段', 40, 'multiattrs', '', '', '', '', '', '{\"message\":\"建议字段的具体信息都在\\\"字段管理\\\"中设置，这里只选择当前场景需要的字段即可\"}', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TEXT', '', 'none', 1, 0, 0, 1, 0, '', '', 1, 475, 1740631596, 1740631596),
(479, 'parent', '父标识', 40, 'text', '', '', '', '', '', '{\"tip\":\"列表下拉工具按钮时需要\",\"form_group\":\"tool\"}', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'CHAR', '32', '', 1, 0, 0, 1, 0, '', '', 1, 481, 1740631596, 1740631596),
(481, 'app', '可用应用', 40, 'checkbox', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"\",\"args\":\"\",\"on\":\"edit\",\"message\":\"\"},{\"rule\":\"\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 471, 1740631596, 1740631596),
(482, 'business_member_id', '商家用户ID', 13, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '11', '0', 1, 1, 0, 1, 0, '', '', 1, 140, 1740631596, 1740631596),
(483, 'business_id', '商家ID', 13, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '10', '0', 1, 1, 0, 1, 0, '', '', 1, 139, 1740631596, 1740631596),
(484, 'attrs', '标签属性', 40, 'keyvalue', '', '', '', '', '', '{\"form_group\":\"tool\"}', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '512', '', 1, 0, 0, 1, 0, '', '', 1, 484, 1740631596, 1740631596),
(485, 'admin_id', '管理员ID', 15, 'none', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', '', 1, 153, 1740631596, 1740631596),
(486, 'mobile', '手机', 7, 'text', '', '', '', '', '', '{\"form_group\":\"admin\"}', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"110\"}', '', '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"mobile\",\"args\":\"\",\"on\":\"\",\"message\":\"\"},{\"rule\":\"unique\",\"args\":\"admin\",\"on\":\"\",\"message\":\"\"}]', '', 1, 'VARCHAR', '16', '', 1, 0, 0, 1, 0, 'index', '', 1, 79, 1740631596, 1740631596),
(487, 'sex', '性别', 7, 'select', '', '', '', '', '', '{\"form_group\":\"admin\"}', '', '', '', '[\"未知\",\"男\",\"女\",\"保密\"]', '', '', '', '', '', 'options', '', '{\"width\":\"80\"}', '', '1', '', '', '', '', '', '', '', '', '', '', '', 1, 'TINYINT', '', '0', 1, 0, 0, 1, 0, '', '', 1, 80, 1740631596, 1740631596),
(488, 'idcard', '身份证', 7, 'text', '', '', '', '', '', '{\"form_group\":\"admin\"}', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"160\"}', '', '1', '', '', '', '', '', '', '', '', '', '[{\"rule\":\"idCard\",\"args\":\"\",\"on\":\"\",\"message\":\"\"}]', '', 1, 'CHAR', '32', '', 1, 0, 0, 1, 0, '', '', 1, 81, 1740631596, 1740631596),
(489, 'region', '家庭所在地', 7, 'cascader', '', '', '', 'Region', '', '{\"form_group\":\"admin\"}', '', '{\"data-url\":\"true\"}', '', '', '', '', '', '', '', '', '', '{\"width\":\"160\"}', '', 'cascader', '', '', '', '{\"data-url\":\"true\",\"data-nostrict\":\"true\"}', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 86, 1740631596, 1740631596),
(490, 'address', '详情地址', 7, 'text', '', '', '', '', '', '{\"form_group\":\"admin\"}', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 109, 1740631596, 1740631596),
(491, 'data_allow', '独立数据权限', 7, 'select', '', '', '', '', '', '', '', '', '', '{\"0\":\"全部数据权限\",\"1\":\"仅本人数据权限\",\"2\":\"本部门数据权限\",\"3\":\"部门及以下数据权限\",\"4\":\"自定义数据权限\",\"5\":\"所在顶级及以下部门权限\",\"-1\":\"继承角色数据权限\"}', '', '', '', '{\"4\":\"custom_data_allow\"}', '', '', '', '{\"width\":\"160\"}', '', '1', '', '', '', '', '', '', '', '', '', '', '', 1, 'TINYINT', '', '0', 1, 0, 0, 1, 0, '', '', 1, 462, 1740631596, 1740631596),
(492, 'custom_data_allow', '自定义数据权限', 7, 'xmtree', '', '', '', 'Department', '', '', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', '', 1, 486, 1740631596, 1740631596),
(493, 'is_scene', '是否场景组', 1, 'checker', '', '', '', '', '', '{\"tip\":\"选中以后，默认系统设置中将不会显示该组\"}', '', '', '', '', '', '', '', '', '', 'checker.show', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, 'index', '', 1, 4, 1740631596, 1740631596),
(494, 'list_order', '排序权重', 6, 'number', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '', '0', 1, 0, 0, 1, 0, '', '', 1, 494, 1740631596, 1740631596),
(495, 'family', '家族', 6, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', '', 1, 495, 1740631596, 1740631596),
(496, 'level', '层级', 6, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'SMALLINT', '5', '0', 1, 1, 0, 1, 0, '', '', 1, 496, 1740631596, 1740631596),
(497, 'children_count', '下级数', 6, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '{\"hide\":\"true\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'SMALLINT', '5', '0', 1, 1, 0, 1, 0, '', '', 1, 497, 1740631596, 1740631596),
(498, 'leader_ids', '部门领导', 6, 'relation', '', '', '', 'Admin', '', '', '', '{\"data-type\":\"checkbox\"}', '', '', '', '', '', '', '', 'html', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '128', '', 1, 0, 0, 1, 0, '', '', 1, 64, 1740631596, 1740631596),
(499, 'is_admin', '后台登录', 6, 'checker', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'checker.show', '', '{\"width\":\"110\"}', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'TINYINT', '1', '0', 1, 1, 0, 1, 0, '', '', 1, 65, 1740631596, 1740631596),
(500, 'class', '类名', 40, 'text', '', '', '', '', '', '{\"form_group\":\"tool\"}', '', '{\"placeholder\":\"标签class类名\"}', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 476, 1740631596, 1740631596),
(501, 'page_title', '网页标题', 40, 'text', '', '', '', '', '', '{\"form_group\":\"page\"}', '', '', '', '', '', '', '', '', '', '', '', '{\"width\":\"150\"}', '', '1', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 467, 1740631596, 1740631596),
(502, 'success_message', '成功提示', 40, 'text', '', '', '', '', '', '{\"form_group\":\"page\"}', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '64', '', 1, 0, 0, 1, 0, '', '', 1, 468, 1740631596, 1740631596),
(503, 'page_tip', '头部提示', 40, 'textarea', '', '', '', '', '', '{\"form_group\":\"page\"}', '', '', '', '', '', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'VARCHAR', '256', '', 1, 0, 0, 1, 0, '', '', 1, 500, 1740631596, 1740631596),
(10000, 'id', '微信用户ID', 1000, 'hidden', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'show', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', 'none', 1, 1, 1, 0, 0, '', '', 1, 0, 1740637685, 1740637685),
(10001, 'user_id', '关联用户ID（woo_user.id）', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'relation', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '', 1, 1, 0, 0, 0, 'index', '', 1, 0, 1740637685, 1740637685),
(10002, 'openid', '微信 OpenID', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'VARCHAR', '64', '', 1, 0, 0, 0, 0, 'unique', '', 1, 0, 1740637685, 1740637685),
(10003, 'unionid', '微信 UnionID', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'VARCHAR', '64', '', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637685, 1740637685),
(10004, 'nickname', '微信昵称', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'VARCHAR', '255', '', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637685, 1740637685),
(10005, 'avatar_url', '用户头像 URL', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'VARCHAR', '255', '', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637685, 1740637685),
(10006, 'gender', '性别：0-未知，1-男，2-女', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TINYINT', '4', '0', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637685, 1740637685),
(10007, 'phone', '电话号码', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'VARCHAR', '20', '', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637685, 1740637685),
(10008, 'is_active', '是否启用：0-禁用，1-启用', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'checker', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TINYINT', '1', '1', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637685, 1740637685),
(10009, 'create_time', '创建时间', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637685, 1740637685),
(10010, 'update_time', '更新时间', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637685, 1740637685),
(10011, 'delete_time', '删除时间', 1000, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637685, 1740637685),
(10012, 'id', '分类ID', 1001, 'hidden', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'show', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', 'none', 1, 1, 1, 0, 0, '', '', 1, 0, 1740637887, 1740637887),
(10013, 'parent_id', '父级分类ID（0表示顶级）', 1001, 'xmtree', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, 'index', '', 1, 0, 1740637887, 1740637887),
(10014, 'family', '家族路径（如 0,1,2）', 1001, 'array', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'VARCHAR', '255', '', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637887, 1740637887),
(10015, 'level', '当前层级（从0开始）', 1001, 'keyvalue', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TINYINT', '3', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637887, 1740637887),
(10016, 'children_count', '子级数量', 1001, 'keyvalue', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637887, 1740637887),
(10017, 'category_name', '分类名称', 1001, 'text', '', '', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'VARCHAR', '255', '', 1, 0, 0, 0, 1, '', '', 1, 0, 1740637887, 1746090894),
(10018, 'description', '分类描述', 1001, 'textarea', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TEXT', '', '', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637887, 1740637887),
(10019, 'list_order', '排序权重', 1001, 'sortvalues', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637887, 1740637887),
(10020, 'is_nav', '是否显示：0-隐藏，1-显示', 1001, 'checker', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'checker', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TINYINT', '1', '1', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637887, 1740637887),
(10021, 'create_time', '创建时间', 1001, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637887, 1740637887),
(10022, 'update_time', '更新时间', 1001, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637887, 1740637887),
(10023, 'delete_time', '删除时间', 1001, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637887, 1740637887),
(10024, 'id', '规则ID', 1002, 'hidden', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'show', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', 'none', 1, 1, 1, 0, 0, '', '', 1, 0, 1740637991, 1740637991),
(10025, 'score_category_id', '分类ID', 1002, 'relation', '', '', '', 'ScoreCategory', '', '[\"\"]', NULL, '', NULL, '{\"id\":\"id\",\"name\":\"category_name\"}', NULL, '', NULL, '[\"\"]', NULL, 'relation', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '1', 1, 1, 0, 0, 0, 'index', 'id', 1, 0, 1740637991, 1746107166),
(10026, 'rule_name', '规则名称', 1002, 'text', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'VARCHAR', '255', '', 1, 0, 0, 0, 0, '', '', 1, 0, 1740637991, 1740637991),
(10027, 'score', '积分数', 1002, 'number', '', 'number', '', '', '', '', NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, 1, 'INT', '10', '0', 1, 0, 0, 0, 0, '', '', 1, 0, 1740637991, 1746082833),
(10028, 'description', '规则描述', 1002, 'textarea', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TEXT', '', '', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637991, 1740637991),
(10029, 'list_order', '排序权重', 1002, 'number', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637991, 1740637991),
(10030, 'is_nav', '是否显示：0-隐藏，1-显示', 1002, 'checker', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'checker', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TINYINT', '1', '1', 0, 0, 0, 0, 0, '', '', 1, 0, 1740637991, 1740637991),
(10031, 'create_time', '创建时间', 1002, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637991, 1740637991),
(10032, 'update_time', '更新时间', 1002, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637991, 1740637991),
(10033, 'delete_time', '删除时间', 1002, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740637991, 1740637991),
(10034, 'id', '通知ID', 1003, 'hidden', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'show', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', 'none', 1, 1, 1, 0, 0, '', '', 1, 0, 1740638082, 1740638082),
(10035, 'user_id', '接收用户ID（woo_wechat_user.id）', 1003, 'relation', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'relation', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '', 1, 1, 0, 0, 0, 'index', '', 1, 0, 1740638082, 1740638082),
(10036, 'message', '通知内容', 1003, 'text', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TEXT', '', '', 1, 0, 0, 0, 0, '', '', 1, 0, 1740638082, 1740638082),
(10037, 'is_read', '是否已读：0-未读，1-已读', 1003, 'checker', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'checker', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TINYINT', '1', '0', 0, 0, 0, 0, 0, '', '', 1, 0, 1740638082, 1740638082),
(10038, 'create_time', '创建时间', 1003, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740638082, 1740638082),
(10039, 'read_time', '已读时间', 1003, 'text', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740638082, 1740638082),
(10040, 'delete_time', '删除时间', 1003, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740638082, 1740638082),
(10060, 'giver_id', '发放人ID', 21, 'none', '', '', '', 'User', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '', '0', 1, 1, 0, 1, 0, '', 'id', 1, 10215, 1740647617, 1746091514),
(10061, 'score_rule_id', '积分规则ID', 21, 'relation', '', '', '', 'score_rule', '', '', '', '', '', '', '', '', '', '', '', 'relation', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '', '0', 1, 1, 0, 1, 0, 'index', 'user_id', 1, 10216, 1740647787, 1746108796),
(10062, 'id', '申诉ID', 1006, 'hidden', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'show', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', 'none', 1, 1, 1, 0, 0, '', '', 1, 0, 1740648048, 1740648048),
(10063, 'user_score_id', '积分记录ID（关联woo_user_score.id）', 1006, 'relation', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '', 1, 1, 0, 0, 0, 'index', '', 1, 0, 1740648048, 1740648048),
(10064, 'user_id', '申诉用户ID（关联woo_user.id）', 1006, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'relation', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '', 1, 1, 0, 0, 0, 'index', '', 1, 0, 1740648048, 1740648048),
(10065, 'reply_user_id', '回复人ID（关联woo_user.id）', 1006, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '', 1, 1, 0, 0, 0, 'index', '', 1, 0, 1740648048, 1740648048),
(10066, 'reason', '申诉理由', 1006, 'textarea', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TEXT', '', '', 1, 0, 0, 0, 0, '', '', 1, 0, 1740648048, 1740648048),
(10067, 'reply', '管理员回复', 1006, 'textarea', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'TEXT', '', '', 0, 0, 0, 0, 0, '', '', 1, 0, 1740648048, 1740648048),
(10068, 'status', '申诉状态', 1006, 'radio', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'ENUM', '\'pending\',\'approved\',\'rejected\'', 'pending', 1, 0, 0, 0, 0, '', '', 1, 0, 1740648048, 1740648048),
(10069, 'create_time', '创建时间', 1006, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740648048, 1740648048),
(10070, 'update_time', '更新时间', 1006, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740648048, 1740648048),
(10071, 'delete_time', '删除时间', 1006, 'none', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', NULL, NULL, 1, 'INT', '10', '0', 0, 1, 0, 0, 0, '', '', 1, 0, 1740648048, 1740648048),
(10072, 'department_id', '所属部门', 19, 'relation', '', '', '', 'department', '', '', '', '', '', '', '', '', '', '', '', 'relation', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'INT', '', '0', 1, 1, 0, 0, 0, '', 'salt', 1, 10217, 1740726789, 1740726789);

-- --------------------------------------------------------

--
-- 表的结构 `woo_folder`
--

CREATE TABLE `woo_folder` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `ex_title` varchar(64) NOT NULL DEFAULT '' COMMENT '副标题',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件夹';

--
-- 转存表中的数据 `woo_folder`
--

INSERT INTO `woo_folder` (`id`, `parent_id`, `title`, `ex_title`, `list_order`, `create_time`, `update_time`, `delete_time`) VALUES
(1, 0, '文件', 'File', 1, 1740631596, 1740631596, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_form_scene`
--

CREATE TABLE `woo_form_scene` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `model_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属模型ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '按钮标题',
  `is_verify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `icon` varchar(64) NOT NULL DEFAULT '' COMMENT '图标',
  `hover` varchar(64) NOT NULL DEFAULT '' COMMENT 'hover名称',
  `where` varchar(256) NOT NULL DEFAULT '' COMMENT '渲染条件',
  `action` varchar(64) NOT NULL DEFAULT '' COMMENT '独立方法',
  `where_type` varchar(64) NOT NULL DEFAULT '' COMMENT '条件方式',
  `is_btn` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '列表按钮',
  `var` char(32) NOT NULL DEFAULT '' COMMENT '标识符',
  `fields` text NOT NULL COMMENT '表单字段',
  `parent` char(32) NOT NULL DEFAULT '' COMMENT '父标识',
  `app` varchar(64) NOT NULL DEFAULT '' COMMENT '可用应用',
  `attrs` varchar(512) NOT NULL DEFAULT '' COMMENT '标签属性',
  `class` varchar(64) NOT NULL DEFAULT '' COMMENT '类名',
  `page_title` varchar(64) NOT NULL DEFAULT '' COMMENT '网页标题',
  `success_message` varchar(64) NOT NULL DEFAULT '' COMMENT '成功提示',
  `page_tip` varchar(256) NOT NULL DEFAULT '' COMMENT '头部提示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='表单场景';

--
-- 转存表中的数据 `woo_form_scene`
--

INSERT INTO `woo_form_scene` (`id`, `model_id`, `title`, `is_verify`, `list_order`, `create_time`, `update_time`, `icon`, `hover`, `where`, `action`, `where_type`, `is_btn`, `var`, `fields`, `parent`, `app`, `attrs`, `class`, `page_title`, `success_message`, `page_tip`) VALUES
(1, 7, '', 1, 71, 1740631596, 1740631596, 'layui-icon-password', '密码', '', 'password', '', 1, 'password', '[{\"field\":\"password\",\"elem\":\"password\",\"more_attrs\":\"{\\\"tip\\\":\\\"\\\"}\",\"validate\":\"{\\\"require\\\":\\\"\\\",\\\"confirm\\\":\\\"repassword\\\"}\"},{\"field\":\"repassword\",\"elem\":\"password\",\"more_attrs\":\"{\\\"name\\\":\\\"确认密码\\\",\\\"attrs\\\":\\\"{\\\\\\\"lay-affix\\\\\\\":\\\\\\\"eye\\\\\\\"}\\\"}\",\"validate\":\"{\\\"require\\\":\\\"\\\",\\\"confirm\\\":\\\"password\\\"}\"}]', '', '[\"admin\"]', '', 'btn-37', '修改密码', '密码修改成功', ''),
(2, 19, '修改密码', 1, 3, 1740631596, 1740631596, '', '', '', '', '', 1, 'password', '[{\"field\":\"password\",\"elem\":\"password\",\"more_attrs\":\"{\\\"tip\\\":\\\"\\\"}\",\"validate\":\"{\\\"require\\\":\\\"\\\",\\\"confirm\\\":\\\"repassword\\\"}\"},{\"field\":\"repassword\",\"elem\":\"password\",\"more_attrs\":\"{\\\"name\\\":\\\"确认密码\\\",\\\"attrs\\\":\\\"{\\\\\\\"lay-affix\\\\\\\":\\\\\\\"eye\\\\\\\"}\\\"}\",\"validate\":\"{\\\"require\\\":\\\"\\\",\\\"confirm\\\":\\\"password\\\"}\"}]', 'more', '[\"admin\"]', '', '', '修改密码', '密码修改成功', ''),
(3, 19, '支付密码', 1, 2, 1740631596, 1740631596, '', '', '', '', '', 1, 'pay_password', '[{\"field\":\"pay_password\",\"elem\":\"password\",\"more_attrs\":\"{\\\"tip\\\":\\\"\\\"}\",\"validate\":\"{\\\"require\\\":\\\"\\\",\\\"confirm\\\":\\\"repassword\\\"}\"},{\"field\":\"repassword\",\"elem\":\"password\",\"more_attrs\":\"{\\\"name\\\":\\\"确认密码\\\",\\\"attrs\\\":\\\"{\\\\\\\"lay-affix\\\\\\\":\\\\\\\"eye\\\\\\\"}\\\"}\",\"validate\":\"{\\\"require\\\":\\\"\\\",\\\"confirm\\\":\\\"pay_password\\\"}\"}]', 'more', '[\"admin\"]', '', '', '修改支付密码', '支付密码修改成功', '');

-- --------------------------------------------------------

--
-- 表的结构 `woo_import`
--

CREATE TABLE `woo_import` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `model_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '模型',
  `file` varchar(128) NOT NULL DEFAULT '' COMMENT '文件',
  `is_import` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否导入',
  `file_name` varchar(64) NOT NULL DEFAULT '' COMMENT '文件名',
  `file_size` int(11) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `business_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家ID',
  `business_member_id` varchar(64) NOT NULL DEFAULT '' COMMENT '商家用户ID',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '执行方式',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='导入';

-- --------------------------------------------------------

--
-- 表的结构 `woo_log`
--

CREATE TABLE `woo_log` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `appname` char(16) NOT NULL DEFAULT '' COMMENT '应用名',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '操作人',
  `addon` varchar(32) NOT NULL DEFAULT '' COMMENT '二级目录',
  `business_member_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家用户ID',
  `business_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家ID',
  `controller` varchar(64) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(64) NOT NULL DEFAULT '' COMMENT '方法',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT 'URL地址',
  `method` varchar(32) NOT NULL DEFAULT '' COMMENT '请求方式',
  `args` varchar(10240) NOT NULL DEFAULT '' COMMENT '数据',
  `ip` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `region` varchar(128) NOT NULL DEFAULT '' COMMENT '操作地址',
  `isp` varchar(64) NOT NULL DEFAULT '' COMMENT '网络ISP',
  `user_agent` varchar(256) NOT NULL DEFAULT '' COMMENT '客户端',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='操作日志';

-- --------------------------------------------------------

--
-- 表的结构 `woo_model`
--

CREATE TABLE `woo_model` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `model` varchar(64) NOT NULL DEFAULT '' COMMENT '模型名',
  `cname` varchar(64) NOT NULL DEFAULT '',
  `addon` varchar(64) NOT NULL DEFAULT '',
  `full_table` varchar(128) NOT NULL DEFAULT '',
  `order_type` varchar(64) NOT NULL DEFAULT '',
  `tree_level` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `custom_data` text,
  `list_config` varchar(256) NOT NULL DEFAULT '',
  `business_list_config` varchar(256) NOT NULL DEFAULT '',
  `suffix` varchar(32) NOT NULL DEFAULT '',
  `pk` varchar(16) NOT NULL DEFAULT '',
  `connection` varchar(32) NOT NULL DEFAULT '',
  `is_import` tinyint(4) NOT NULL DEFAULT '0',
  `is_business_import` tinyint(1) NOT NULL DEFAULT '0',
  `parent_model` varchar(32) NOT NULL DEFAULT '',
  `display` varchar(32) NOT NULL DEFAULT '',
  `form_group` text,
  `relation_link` text,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `is_controller` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `is_business_controller` tinyint(1) NOT NULL DEFAULT '0',
  `admin_tool_bar` text,
  `business_tool_bar` text,
  `admin_item_tool_bar` text,
  `business_item_tool_bar` text,
  `admin_siderbar` text,
  `business_siderbar` text,
  `admin_table_attrs` text,
  `business_table_attrs` text,
  `admin_item_checkbox` varchar(64) NOT NULL DEFAULT '',
  `business_item_checkbox` varchar(64) NOT NULL DEFAULT '',
  `admin_item_toolbar_options` text,
  `business_item_toolbar_options` text,
  `admin_counter` text,
  `business_counter` text,
  `admin_total_row` text,
  `business_total_row` text,
  `admin_is_remove_pk` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `business_is_remove_pk` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `admin_filter_model` varchar(64) NOT NULL DEFAULT '',
  `business_filter_model` varchar(64) NOT NULL DEFAULT '',
  `admin_list_with` text,
  `business_list_with` text,
  `admin_list_fields` text,
  `business_list_fields` text,
  `admin_list_filters` text,
  `business_list_filters` text,
  `admin_list_where` text,
  `create_time` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模型';

--
-- 转存表中的数据 `woo_model`
--

INSERT INTO `woo_model` (`id`, `model`, `cname`, `addon`, `full_table`, `order_type`, `tree_level`, `custom_data`, `list_config`, `business_list_config`, `suffix`, `pk`, `connection`, `is_import`, `is_business_import`, `parent_model`, `display`, `form_group`, `relation_link`, `admin_id`, `is_controller`, `is_business_controller`, `admin_tool_bar`, `business_tool_bar`, `admin_item_tool_bar`, `business_item_tool_bar`, `admin_siderbar`, `business_siderbar`, `admin_table_attrs`, `business_table_attrs`, `admin_item_checkbox`, `business_item_checkbox`, `admin_item_toolbar_options`, `business_item_toolbar_options`, `admin_counter`, `business_counter`, `admin_total_row`, `business_total_row`, `admin_is_remove_pk`, `business_is_remove_pk`, `admin_filter_model`, `business_filter_model`, `admin_list_with`, `business_list_with`, `admin_list_fields`, `business_list_fields`, `admin_list_filters`, `business_list_filters`, `admin_list_where`, `create_time`, `update_time`) VALUES
(1, 'SettingGroup', '配置组', '', '', 'asc', 0, '', 'create,batch_delete,sortable,modify,delete,detail', '', '', '', '', 0, 0, '', 'title', '', '[{\"key\":\"Setting\",\"foreign\":\"\",\"type\":\"hasMany\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 0, 0, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(2, 'Setting', '配置', '', '', 'asc', 0, '', 'create,batch_delete,sortable,modify,delete,detail', '', '', '', '', 0, 0, 'SettingGroup', 'title', '', '[{\"key\":\"SettingGroup\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 0, 0, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(4, 'Region', '地区', '', '', 'asc', 5, '', 'create,batch_delete,delete,detail,modify', '', '', '', '', 0, 0, 'parent', 'title', '', '', 1, 0, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(6, 'Department', '部门', '', '', 'asc', 5, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, '', 'title', '', '[{\"key\":\"Admin\",\"foreign\":\"\",\"type\":\"hasMany\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 0, 0, '', NULL, '', NULL, '', NULL, '{\"treetable\":\"true\"}', NULL, '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', NULL, '', NULL, '', NULL, 3, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(7, 'Admin', '管理员', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail,delete_index', '', '', '', '', 0, 0, '', 'username', '{\"basic\":\"账号信息\",\"admin\":\"用户资料\"}', '[{\"key\":\"AdminGroup\",\"foreign\":\"\",\"type\":\"belongsToMany\",\"foreign_key\":\"\",\"more\":\"{\\\"middle\\\":\\\"AdminUseAdminGroup\\\"}\"},{\"key\":\"Department\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"},{\"key\":\"AdminLogin\",\"foreign\":\"\",\"type\":\"hasMany\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 0, 0, '', NULL, '', NULL, '', NULL, '', NULL, 'checkbox', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', NULL, '[{\"field\":\"id\",\"title\":\"总人数\",\"type\":\"count\",\"where_type\":\"\",\"where\":\"\",\"callback\":\"\",\"templet\":\"\",\"more\":\"\"},{\"field\":\"id\",\"title\":\"男性人数\",\"type\":\"count\",\"where_type\":\"where\",\"where\":\"[[\\\"sex\\\",\\\"=\\\",1]]\",\"callback\":\"\",\"templet\":\"\",\"more\":\"\"},{\"field\":\"id\",\"title\":\"女性人数\",\"type\":\"count\",\"where_type\":\"where\",\"where\":\"[[\\\"sex\\\",\\\"=\\\",2]]\",\"callback\":\"\",\"templet\":\"\",\"more\":\"\"}]', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(8, 'AdminLogin', '登录日志', '', '', '', 0, '', 'batch_delete,delete,detail', '', '', '', '', 0, 0, 'Admin', 'ip', '', '[{\"key\":\"Admin\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 0, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(9, 'Log', '操作日志', '', '', '', 0, '', 'batch_delete,delete,detail', 'batch_delete,delete,detail', '', '', '', 0, 0, '', 'controller', '', '[{\"key\":\"Admin\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '', '', '', '', 0, 2, '', '', '', '', '', '', '', '', '', 1740631596, 1740631596),
(10, 'Dictionary', '字典', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, '', 'title', '', '[{\"key\":\"DictionaryItem\",\"foreign\":\"\",\"type\":\"hasMany\",\"foreign_key\":\"\",\"more\":\"{\\\"deleteWith\\\":\\\"true\\\"}\"}]', 1, 0, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(11, 'DictionaryItem', '字典项', '', '', 'asc', 0, '', 'create,batch_delete,modify,delete,detail,sortable', '', '', '', '', 0, 0, 'Dictionary', '', '', '[{\"key\":\"Dictionary\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"{\\\"counterCache\\\":\\\"true\\\"}\"}]', 1, 0, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(12, 'Folder', '文件夹', '', '', 'asc', 5, '', 'create,modify,detail,batch_delete,delete', '', '', '', '', 0, 0, '', 'title', '', '', 1, 0, 0, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(13, 'Attachement', '附件', '', '', '', 0, '', 'batch_delete,modify,delete,detail', 'batch_delete,create,modify,delete,detail', '', '', '', 0, 0, 'Folder', 'title', '', '[{\"key\":\"Folder\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740631596, 1740631596),
(15, 'Power', '权限', '', '', 'asc', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, '', '', '', '', 1, 0, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(16, 'Shortcut', '快捷方式', '', '', 'asc', 0, '', 'create,batch_delete,modify,delete,detail,sortable', '', '', '', '', 0, 0, '', 'title', '', '', 1, 0, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(17, 'Statistics', '统计', '', '', 'asc', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, '', 'title', '', '', 1, 0, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(18, 'UserGroup', '用户组', '', '', 'asc', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, '', 'title', '', '[{\"key\":\"User\",\"foreign\":\"\",\"type\":\"hasMany\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(19, 'User', '用户', '', '', '', 0, '', 'create,modify,delete,detail,batch_delete,delete_index', '', '', '', '', 1, 0, '', 'username', '', '[{\"key\":\"UserGroup\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"},{\"key\":\"UserGrade\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"},{\"key\":\"UserLogin\",\"foreign\":\"\",\"type\":\"hasMany\",\"foreign_key\":\"\",\"more\":\"\"},{\"key\":\"Certification\",\"foreign\":\"\",\"type\":\"hasOne\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(20, 'UserGrade', '等级', '', '', 'asc', 0, '', 'create,batch_delete,modify,delete,detail,sortable', '', '', '', '', 0, 0, '', 'title', '', '', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(21, 'UserScore', '积分记录', '', '', '', 0, '', 'create,batch_delete,delete,detail', '', '', '', '', 0, 0, '', 'user_id', '', '[{\"key\":\"User\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"},{\"key\":\"ScoreRule\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', NULL, '[{\"field\":\"score\",\"title\":\"总发放积分\",\"type\":\"sum\",\"where_type\":\"none\",\"where\":\"\",\"callback\":\"\",\"templet\":\"\",\"more\":\"\"},{\"field\":\"score\",\"title\":\"当月积分\",\"type\":\"sum\",\"where_type\":\"where\",\"where\":\"\",\"callback\":\"\",\"templet\":\"\",\"more\":\"\"}]', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1746114295),
(22, 'UserMoney', '收支', '', '', '', 0, '', 'batch_delete,delete,detail', '', '', '', '', 0, 0, '', 'user_id', '', '[{\"key\":\"User\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(23, 'TestMenu', '测试分类', '', '', 'asc', 3, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, 'parent', 'title', '', '', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(24, 'TestArticle', '测试文章', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail,sortable', '', '', '', '', 0, 0, '', 'title', '', '[{\"key\":\"TestMenu\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, '', NULL, '', NULL, '[\"TestMenu\"]', NULL, '', NULL, '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(25, 'Recharge', '充值', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, '', 'user_id', '', '[{\"key\":\"User\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(26, 'Import', '导入', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail', 'create,batch_delete,modify,delete,detail', '', '', '', 0, 0, '', 'title', '', '[{\"key\":\"Business\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, '', '', '', '', '', '', '', '', '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740631596, 1740631596),
(27, 'Application', '应用', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail,sortable', '', '', '', '', 0, 0, '', 'title', '', '', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(28, 'Sensitive', '敏感词', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 1, 0, '', 'title', '', '', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(29, 'UserLogin', '登录日志', '', '', '', 0, '', 'batch_delete,delete,detail', '', '', '', '', 0, 0, '', 'username', '', '[{\"key\":\"User\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(30, 'Certification', '实名认证', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, 'User', 'truename', '', '[{\"key\":\"User\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(31, 'Antispam', '文本审核', '', '', '', 0, '', 'batch_delete,modify,delete,detail', 'batch_delete,modify,delete,detail', '', '', '', 0, 0, '', 'title', '', '[{\"key\":\"Admin\",\"foreign\":\"\",\"type\":\"\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, '', '', '', '', '', '', '', '', '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740631596, 1740631596),
(32, 'UserMenu', '用户菜单', '', '', 'asc', 3, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, 'parent', 'title', '', '', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(33, 'Denied', '禁止登录', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, '', 'model', '', '', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(34, 'Sign', '签到', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, '', 'date', '', '[{\"key\":\"User\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', '', '', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(35, 'UserPower', '用户权限', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, '', '', '', '[{\"key\":\"UserGroup\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, '', NULL, '', NULL, '', NULL, '', NULL, '', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"{}\"}]', NULL, '', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(36, 'TestProduct', '测试产品', '', '', 'asc', 0, '', 'create,batch_delete,modify,delete,detail,sortable,copy,delete_index', '', '', '', '', 0, 0, '', 'title', '', '', 1, 1, 0, '', NULL, '', NULL, '', NULL, '', NULL, 'checkbox', '', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', NULL, '[{\"field\":\"price\",\"title\":\"\",\"type\":\"avg\",\"where_type\":\"none\",\"where\":\"\",\"callback\":\"\",\"templet\":\"\",\"more\":\"\"},{\"field\":\"number\",\"title\":\"\",\"type\":\"count\",\"where_type\":\"none\",\"where\":\"\",\"callback\":\"\",\"templet\":\"\",\"more\":\"\"},{\"field\":\"id\",\"title\":\"\",\"type\":\"min\",\"where_type\":\"none\",\"where\":\"\",\"callback\":\"\",\"templet\":\"\",\"more\":\"\"}]', NULL, '', NULL, 0, 0, '', '', '', NULL, '', NULL, '', NULL, '', 1740631596, 1740631596),
(37, 'AdminRule', '菜单规则', '', '', 'asc', 5, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, 'parent', 'title', '', '', 1, 1, 0, '', '', '', '', '', '', '{\"treetable\":\"true\"}', '', 'checkbox', 'checkbox', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740631596, 1740631596),
(38, 'AdminGroup', '角色', '', '', 'asc', 3, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, 'parent', 'title', '', '', 1, 1, 0, '', '', '', '', '', '', '{\"treetable\":\"true\"}', '', 'checkbox', 'checkbox', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '', '', '', '', 3, 0, '', '', '', '', '', '', '', '', '', 1740631596, 1740631596),
(39, 'AdminUseAdminGroup', '管理员对应角色', '', '', '', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, 'AdminGroup', '', '', '[{\"key\":\"Admin\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"},{\"key\":\"AdminGroup\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, '', '', '', '', '', '', '', '', 'checkbox', 'checkbox', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740631596, 1746081244),
(40, 'FormScene', '表单场景', '', '', 'desc', 0, '', 'create,batch_delete,modify,delete,detail', '', '', '', '', 0, 0, 'Model', 'title', '{\"basic\":\"基本信息\",\"tool\":\"按钮信息\",\"page\":\"页面信息\"}', '[{\"key\":\"Model\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, '', '', '', '', '', '', '', '', 'checkbox', 'checkbox', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '[{\"is_show\":1,\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":0,\"align\":\"center\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740631596, 1740631596),
(1000, 'WechatUser', '微信用户表', '', 'woo_wechat_user', '', 0, '', 'create,batch_delete,delete,detail,modify', '', '', '', '', 0, 0, '', '', '', '[]', 1, 1, 0, '', '', '', '', '', '', '', '', 'checkbox', 'checkbox', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740637685, 1740637685),
(1001, 'ScoreCategory', '积分规则分类表（无限级）', '', 'woo_score_category', '', 0, '', 'create,batch_delete,delete,detail,modify', '', '', '', '', 0, 0, '', 'category_name', '', '', 1, 1, 0, '', '', '', '', '', '', '', '', 'checkbox', 'checkbox', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740637887, 1746093403),
(1002, 'ScoreRule', '积分规则表', '', 'woo_score_rule', '', 0, '', 'create,batch_delete,delete,detail,modify', '', '', '', '', 0, 0, '', 'rule_name', '', '[{\"key\":\"ScoreCategory\",\"foreign\":\"\",\"type\":\"belongsTo\",\"foreign_key\":\"\",\"more\":\"\"}]', 1, 1, 0, '', '', '', '', '', '', '', '', 'checkbox', 'checkbox', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\",\"more\":\"\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740637991, 1746107408),
(1003, 'Notification', '用户通知表', '', 'woo_notification', '', 0, '', 'create,batch_delete,delete,detail,modify', '', '', '', '', 0, 0, '', '', '', '[]', 1, 1, 0, '', '', '', '', '', '', '', '', 'checkbox', 'checkbox', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740638082, 1740638082),
(1006, 'ScoreAppeal', '积分申诉记录表', '', 'woo_score_appeal', '', 0, '', 'create,batch_delete,delete,detail,modify', '', '', '', '', 0, 0, '', '', '', '[]', 1, 1, 0, '', '', '', '', '', '', '', '', 'checkbox', 'checkbox', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '[{\"is_show\":\"1\",\"title\":\"操作\",\"fixed\":\"right\",\"min_width\":\"0\",\"align\":\"center\"}]', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 1740648048, 1740648048);

-- --------------------------------------------------------

--
-- 表的结构 `woo_notification`
--

CREATE TABLE `woo_notification` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '通知ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '接收用户ID（woo_wechat_user.id）',
  `message` text NOT NULL COMMENT '通知内容',
  `is_read` tinyint(1) DEFAULT '0' COMMENT '是否已读：0-未读，1-已读',
  `create_time` int(10) UNSIGNED DEFAULT '0' COMMENT '创建时间',
  `read_time` int(10) UNSIGNED DEFAULT '0' COMMENT '已读时间',
  `delete_time` int(10) UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户通知表';

-- --------------------------------------------------------

--
-- 表的结构 `woo_power`
--

CREATE TABLE `woo_power` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `admin_group_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '角色ID',
  `content` text NOT NULL COMMENT '授权',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限';

--
-- 转存表中的数据 `woo_power`
--

INSERT INTO `woo_power` (`id`, `admin_group_id`, `content`, `create_time`, `update_time`, `admin_id`) VALUES
(1, 2, '[62,1,4,5,16,17,18,27,43,52,53,58,61,65,68,69,107,108,109,110,111,115,177,178,179,180,181,182,183,184,185,186,188,189,190,191,192,193,195,196,197,198,199,200,209,210,211,212,213,214,215,216,217,218,219,223,224,225,226,227,228,229,322,323,324,325,326,327,331,333,334,335,336,337,338,353,354]', 1740632629, 1746109156, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_recharge`
--

CREATE TABLE `woo_recharge` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '充值方式',
  `remark` varchar(512) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='充值';

--
-- 转存表中的数据 `woo_recharge`
--

INSERT INTO `woo_recharge` (`id`, `user_id`, `money`, `type`, `remark`, `create_time`, `update_time`, `delete_time`) VALUES
(1, 1, 100.00, 'zfb', '', 1740631596, 1740631596, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_region`
--

CREATE TABLE `woo_region` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '名称',
  `code` int(11) NOT NULL DEFAULT '0' COMMENT '代码编号',
  `pinyin` varchar(64) NOT NULL DEFAULT '' COMMENT '拼音',
  `jianpin` varchar(64) NOT NULL DEFAULT '' COMMENT '简拼',
  `first` char(2) NOT NULL DEFAULT '' COMMENT '首字母',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `children_count` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下级数',
  `family` varchar(128) NOT NULL DEFAULT '' COMMENT '家族',
  `level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '层级',
  `lng` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '经度',
  `lat` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '纬度'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='地区';

-- --------------------------------------------------------

--
-- 表的结构 `woo_request_log`
--

CREATE TABLE `woo_request_log` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `appname` varchar(32) NOT NULL DEFAULT '' COMMENT '应用/插件名',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `business_member_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家用户ID',
  `controller` varchar(64) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(64) NOT NULL DEFAULT '' COMMENT '方法',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT 'URL地址',
  `method` varchar(32) NOT NULL DEFAULT '' COMMENT '请求方法',
  `args` varchar(10240) NOT NULL DEFAULT '' COMMENT '数据',
  `ip` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `region` varchar(128) NOT NULL DEFAULT '' COMMENT '请求地址',
  `isp` varchar(64) NOT NULL DEFAULT '' COMMENT '网络ISP',
  `user_agent` varchar(255) NOT NULL DEFAULT '' COMMENT '客户端',
  `referer` varchar(255) NOT NULL DEFAULT '' COMMENT '来源',
  `code` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态码',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='请求日志';

-- --------------------------------------------------------

--
-- 表的结构 `woo_score_appeal`
--

CREATE TABLE `woo_score_appeal` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '申诉ID',
  `user_score_id` int(10) UNSIGNED NOT NULL COMMENT '积分记录ID（关联woo_user_score.id）',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '申诉用户ID（关联woo_user.id）',
  `reply_user_id` int(10) UNSIGNED NOT NULL COMMENT '回复人ID（关联woo_user.id）',
  `reason` text NOT NULL COMMENT '申诉理由',
  `reply` text COMMENT '管理员回复',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending' COMMENT '申诉状态',
  `create_time` int(10) UNSIGNED DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分申诉记录表';

-- --------------------------------------------------------

--
-- 表的结构 `woo_score_category`
--

CREATE TABLE `woo_score_category` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '分类ID',
  `parent_id` int(10) UNSIGNED DEFAULT '0' COMMENT '父级分类ID（0表示顶级）',
  `family` varchar(255) DEFAULT NULL COMMENT '家族路径（如 0,1,2）',
  `level` tinyint(3) UNSIGNED DEFAULT '0' COMMENT '当前层级（从0开始）',
  `children_count` int(10) UNSIGNED DEFAULT '0' COMMENT '子级数量',
  `category_name` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `description` text COMMENT '分类描述',
  `list_order` int(10) UNSIGNED DEFAULT '0' COMMENT '排序权重',
  `is_nav` tinyint(1) DEFAULT '1' COMMENT '是否显示：0-隐藏，1-显示',
  `create_time` int(10) UNSIGNED DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分规则分类表（无限级）';

--
-- 转存表中的数据 `woo_score_category`
--

INSERT INTO `woo_score_category` (`id`, `parent_id`, `family`, `level`, `children_count`, `category_name`, `description`, `list_order`, `is_nav`, `create_time`, `update_time`, `delete_time`) VALUES
(1, 0, '', 0, 0, '营销', '营销类积分奖励', 1, 0, 1746076786, 1746076786, 0),
(2, 1, '', 0, 0, '回款', '营销回款类积分奖励', 2, 0, 1746076859, 1746076859, 0),
(3, 1, '', 0, 0, '销售达标', '销售达标类积分奖励', 3, 0, 1746076955, 1746076955, 0),
(4, 1, '', 0, 0, '销售任务达标', '销售类任务达标积分奖励', 4, 0, 1746076995, 1746076995, 0),
(5, 1, '', 0, 0, '年度任务增幅', '年度任务增幅类积分奖励', 5, 0, 1746077040, 1746077040, 0),
(6, 1, '', 0, 0, '销冠', '销售冠军类积分奖励，分月度、季度、年度', 6, 0, 1746077112, 1746077112, 0),
(7, 1, '', 0, 0, '平均单价排名', '销售平均单价名次类积分奖励', 7, 0, 1746077163, 1746077163, 0),
(8, 0, '', 0, 0, '工作类', '工作类积分奖励', 8, 0, 1746077190, 1746077190, 0),
(9, 8, '', 0, 0, '6S管理排名', '6S管理排名类积分奖励', 9, 0, 1746077217, 1746077217, 0),
(10, 8, '', 0, 0, '会议类', '会议类积分奖励', 10, 0, 1746077238, 1746077238, 0),
(11, 8, '', 0, 0, '工作问题解决类', '解决工作中的问题类积分奖励', 11, 0, 1746077288, 1746077288, 0),
(12, 0, '', 0, 0, '日常类', '日常类积分奖励', 12, 0, 1746077334, 1746077334, 0),
(13, 12, '', 0, 0, '考勤类', '考勤类积分奖励', 13, 0, 1746077357, 1746077357, 0),
(14, 12, '', 0, 0, '宣传类', '日常宣传类积分奖励，如发朋友圈、转发公司视频等', 14, 0, 1746077403, 1746077403, 0),
(15, 0, '', 0, 0, '活动类', '与活动相关的一些积分奖励', 15, 0, 1746077449, 1746077449, 0),
(16, 15, '', 0, 0, '部门活动', '与部门活动相关的积分奖励', 16, 0, 1746077472, 1746077472, 0),
(17, 15, '', 0, 0, '公司活动', '与公司活动相关的积分奖励', 17, 0, 1746077500, 1746077500, 0),
(18, 15, '', 0, 0, '外部活动', '与外部活动相关的积分奖励', 18, 0, 1746077530, 1746077530, 0),
(19, 0, '', 0, 0, '个人类', '与个人相关的积分奖励', 19, 0, 1746077571, 1746077571, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_score_rule`
--

CREATE TABLE `woo_score_rule` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '规则ID',
  `score_category_id` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '分类ID',
  `rule_name` varchar(255) NOT NULL COMMENT '规则名称',
  `score` int(10) NOT NULL DEFAULT '0' COMMENT '积分数',
  `description` text COMMENT '规则描述',
  `list_order` int(10) UNSIGNED DEFAULT '0' COMMENT '排序权重',
  `is_nav` tinyint(1) DEFAULT '1' COMMENT '是否显示：0-隐藏，1-显示',
  `create_time` int(10) UNSIGNED DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分规则表';

--
-- 转存表中的数据 `woo_score_rule`
--

INSERT INTO `woo_score_rule` (`id`, `score_category_id`, `rule_name`, `score`, `description`, `list_order`, `is_nav`, `create_time`, `update_time`, `delete_time`) VALUES
(1, 2, '每月回款无欠款', 10, '销售员每月回款无欠款行为', 1, 1, 1746078616, 1746082868, 0),
(2, 3, '绝对原点数卡销售达标', 20, '销售员销售“绝对原点数卡”任务达标', 2, 1, 1746092560, 1746092560, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_sensitive`
--

CREATE TABLE `woo_sensitive` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `is_verify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否审核',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='敏感词';

-- --------------------------------------------------------

--
-- 表的结构 `woo_setting`
--

CREATE TABLE `woo_setting` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '标题',
  `setting_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属系统配置组',
  `var` varchar(64) NOT NULL DEFAULT '' COMMENT '变量名',
  `value` text NOT NULL COMMENT '数据',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '输入类型',
  `options` varchar(512) NOT NULL DEFAULT '' COMMENT '选项',
  `tip` varchar(128) NOT NULL DEFAULT '' COMMENT '提示',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `is_js_var` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'JS中调用',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统设置';

--
-- 转存表中的数据 `woo_setting`
--

INSERT INTO `woo_setting` (`id`, `title`, `setting_group_id`, `var`, `value`, `type`, `options`, `tip`, `list_order`, `admin_id`, `is_js_var`, `create_time`, `update_time`, `delete_time`) VALUES
(1, '项目名称 ', 1, 'project_title', '广陆数测积分制管理发放系统', 'text', '', '', 1, 1, 0, 1740631596, 1744878301, 0),
(2, '审核默认值', 2, 'admin_default_verify', '1', 'radio', '[\"审核不通过\",\"审核通过\"]', '', 3, 1, 0, 1740631596, 1740631596, 0),
(3, '默认分页Limit', 2, 'admin_page_limit', '15', 'number', '', '', 5, 1, 0, 1740631596, 1740631596, 0),
(4, '是否开启缓存', 4, 'table_is_cache', '0', 'checker', '', '仅针对模型数据查询的数据，静态数据自行缓存', 4, 1, 0, 1740631596, 1740631596, 0),
(5, '表单加载方式', 2, 'admin_form_load_type', 'drawer', 'select', '{\"default\":\"跳转页面\",\"layer\":\"嵌入弹窗\",\"drawer\":\"嵌入抽屉\",\"open\":\"独立窗口\"}', '系统自带的添加、修改表单加载方式', 28, 1, 1, 1740631596, 1740631596, 0),
(6, '图片最大限制', 3, 'upload_image_max_size', '2048', 'number', '', '单位：KB，特殊情况可以模型对应字段的upload属性单独定义maxSize', 7, 1, 0, 1740631596, 1740631596, 0),
(7, '图片允许后缀', 3, 'upload_image_valid_ext', 'jpg|png|gif|jpeg|webp', 'text', '', '多个之间用\"|\"分割，特殊情况可以模型对应字段的upload属性单独定义validExt', 8, 1, 0, 1740631596, 1740631596, 0),
(8, '文件最大限制', 3, 'upload_file_max_size', '2048', 'number', '', '单位：KB，特殊情况可以模型对应字段的upload属性单独定义maxSize', 9, 1, 0, 1740631596, 1740631596, 0),
(9, '文件允许后缀', 3, 'upload_file_valid_ext', 'doc|docx|xls|xlsx|ppt|pptx|pdf|wps|txt|rar|zip|jpg|png|gif|jpeg|webp', 'text', '', '多个之间用\"|\"分割，特殊情况可以模型对应字段的upload属性单独定义validExt', 10, 1, 0, 1740631596, 1740631596, 0),
(10, '多文件上传最大数量', 3, 'upload_max_length', '5', 'number', '', '多图、多文件上传默认最大数量，特殊情可模型中单独定义maxLength', 11, 1, 0, 1740631596, 1740631596, 0),
(11, '全局缩略图宽度', 3, 'upload_thumb_width', '400', 'number', '', '', 12, 1, 1, 1740631596, 1740631596, 0),
(12, '全局缩略图高度', 3, 'upload_thumb_height', '300', 'number', '', '', 13, 1, 1, 1740631596, 1740631596, 0),
(13, '全局缩略图方式', 3, 'upload_thumb_method', '3', 'select', '{\"1\":\"等比例缩放\",\"2\":\"缩放后填充\",\"3\":\"居中裁剪\",\"4\":\"左上角裁剪\",\"5\":\"右下角裁剪\",\"6\":\"固定尺寸缩放\"}', '', 14, 1, 1, 1740631596, 1740631596, 0),
(14, '启用图片水印', 3, 'upload_is_water', '0', 'checker', '', '', 15, 1, 0, 1740631596, 1740631596, 0),
(15, '水印类型', 3, 'upload_water_type', 'text', 'radio', '{\"image\":\"图片\",\"text\":\"文字\"}', '', 16, 1, 0, 1740631596, 1740631596, 0),
(16, '水印图片', 3, 'upload_water_image', '', 'image', '', '', 17, 1, 0, 1740631596, 1740631596, 0),
(17, '水印文字', 3, 'upload_water_text', '', 'text', '', '', 21, 1, 0, 1740631596, 1740631596, 0),
(18, '水印位置', 3, 'upload_water_location', '9', 'select', '{\"1\":\"左上角\",\"2\":\"上居中\",\"3\":\"右上角\",\"4\":\"左居中\",\"5\":\"居中\",\"6\":\"右居中\",\"7\":\"左下角\",\"8\":\"下居中\",\"9\":\"右下角\"}', '', 19, 1, 0, 1740631596, 1740631596, 0),
(19, '水印文字大小', 3, 'upload_water_text_size', '20', 'number', '', '', 22, 1, 0, 1740631596, 1740631596, 0),
(20, '水印文字颜色', 3, 'upload_water_text_color', '#000000', 'color', '', '', 32, 1, 0, 1740631596, 1740631596, 0),
(21, '水印字体文件', 3, 'upload_water_text_font', '', 'file', '', '', 20, 1, 0, 1740631596, 1740631596, 0),
(22, '水印图片透明度', 3, 'upload_water_image_opacity', '100', 'number', '', '可设置值为0~100，数字越小，透明度越高', 18, 1, 0, 1740631596, 1740631596, 0),
(23, '时间戳字段默认颜色', 4, 'table_timestamp_color', '#888888', 'color', '', '', 25, 1, 0, 1740631596, 1740631596, 0),
(24, '单元格默认最小宽度', 4, 'table_cell_min_width', '100', 'number', '', '', 26, 1, 0, 1740631596, 1740631596, 0),
(25, '没有数据时默认提示', 4, 'table_none_tip', '', 'text', '', '', 27, 1, 0, 1740631596, 1740631596, 0),
(26, '默认显示条数Limits', 4, 'table_default_limits', '10|20|30|50|100|200|500|1000', 'text', '', '多个之间用\"|\"分割', 31, 1, 0, 1740631596, 1740631596, 0),
(27, '后台名称', 2, 'admin_title', '积分管理系统', 'text', '', '', 2, 1, 0, 1740631596, 1744878029, 0),
(28, 'Ajax请求间隔', 2, 'admin_ajax_inteval', '2000', 'number', '', '单位：毫秒，1秒=1000毫秒', 33, 1, 1, 1740631596, 1740631596, 0),
(29, 'Ajax次数触发判断', 2, 'admin_ajax_length', '4', 'number', '', '', 38, 1, 1, 1740631596, 1740631596, 0),
(30, '后台列表缓存时间', 4, 'table_cache_expire', '600', 'number', '', '单位：秒', 23, 1, 0, 1740631596, 1740631596, 0),
(31, '检查已上传', 3, 'upload_is_check_uploaded', '0', 'checker', '', '相同文件不会重复上传，但会导致多个地方共用一个文件路径', 6, 1, 0, 1740631596, 1740631596, 0),
(32, '弹窗是否自动全屏', 2, 'admin_is_layer_full', '0', 'checker', '', '开启弹窗情况下有效', 29, 1, 1, 1740631596, 1740631596, 0),
(33, '公司名称', 1, 'project_corp_title', '桂林广陆数字测控有限公司', 'text', '', '', 34, 1, 0, 1740631596, 1744878301, 0),
(34, '公司电话', 1, 'project_tel', '0773-5801111', 'text', '', '', 35, 1, 0, 1740631596, 1744878301, 0),
(35, '公司邮箱', 1, 'project_email', '123456@qq.com', 'text', '', '', 36, 1, 0, 1740631596, 1744878301, 0),
(36, '公司地址', 1, 'project_address', '桂林高铁经济产业园27号', 'text', '', '', 37, 1, 0, 1740631596, 1744878301, 0),
(37, '首页统计缓存时间', 2, 'admin_statistics_cache_expire', '300', 'number', '', '单位：秒，如果为0为实时查询数据库', 43, 1, 0, 1740631596, 1740631596, 0),
(38, '是否开启敏感词功能', 5, 'do_is_sensitive', '0', 'checker', '', '', 39, 1, 0, 1740631596, 1740631596, 0),
(39, '敏感词替换字符', 5, 'do_sensitive_replace', '', 'text', '', '', 40, 1, 0, 1740631596, 1740631596, 0),
(40, '是否开启请求日志', 5, 'do_is_request_log', '1', 'checker', '', '', 41, 1, 0, 1740631596, 1740631596, 0),
(41, '写请求日志的应用/插件名', 5, 'do_request_log_app_list', '[\"cms\",\"api\",\"business\"]', 'array', '', '', 42, 1, 0, 1740631596, 1740631596, 0),
(42, '登录页大背景', 2, 'admin_login_bg', '', 'image', '', '用于替换登录页面的大背景图片', 44, 1, 0, 1740631596, 1740631596, 0),
(43, '表单抽屉配置', 2, 'admin_form_drawer_setting', '{\"size\":\"60%\",\"shade\":\"1\",\"position\":\"right\"}', 'keyvalue', '', '', 30, 1, 1, 1740631596, 1740631596, 0),
(44, '登录失败禁止时间', 6, 'user_denied_interval', '5', 'number', '', '连续登录失败5次以后，禁止登陆的时间间隔；单位：分钟', 47, 1, 0, 1740631596, 1740631596, 0),
(45, '签到积分规则', 6, 'user_sign_give_score', '{\"1\":\"1\",\"2\":\"2\",\"3\":\"3\",\"4\":\"4\",\"7\":\"7\",\"15\":\"15\",\"30\":\"30\",\"50\":\"50\",\"100\":\"100\"}', 'keyvalue', '', '连续签到天数(必须整数) => 赠送积分', 48, 1, 0, 1740631596, 1744878157, 0),
(46, '是否开启用户系统', 6, 'user_is_user', '1', 'checker', '', '', 46, 1, 0, 1740631596, 1740631596, 0),
(47, '高德地图KEY值', 5, 'do_amap_key', '', 'text', '', '应用于高德地图', 49, 1, 1, 1740631596, 1740631596, 0),
(48, '是否关闭Trace', 2, 'admin_is_trace', '0', 'checker', '', '', 50, 1, 0, 1740631596, 1740631596, 0),
(49, '模型是否批量设置字段', 5, 'do_is_batch_edit_fields', '0', 'checker', '', '', 51, 1, 0, 1740631596, 1740631596, 0),
(50, '缓存列表页码数', 4, 'table_is_store_page', '0', 'checker', '', '当无搜索情况下才会记录', 52, 1, 1, 1740631596, 1740631596, 0),
(51, '主页Tab最大数', 2, 'admin_tab_max', '20', 'number', '', '', 53, 1, 0, 1740631596, 1740631596, 0),
(52, '页脚版权信息', 1, 'project_copyright', 'Copyright © 2021-2026 Your Company', 'text', '', '', 54, 1, 0, 1740631596, 1744878301, 0),
(53, '列表项工具按钮风格', 4, 'table_item_toolbar_style', 'text', 'select', '{\"button\":\"按钮风格(默认)\",\"text\":\"文本不带图标风格\",\"text_icon\":\"文本可带图标风格\"}', '', 55, 1, 0, 1740631596, 1740631596, 0),
(54, '列表项文本按钮类名', 4, 'table_item_toolbar_text_class', '', 'text', '', '可通过控制类名实现按钮颜色和样式的控制；类名\"woo-theme-color\"实现按钮颜色随主题', 56, 1, 0, 1740631596, 1740631596, 0),
(55, '表格全局默认高度', 4, 'table_default_height', '0', 'number', '', '如不设置默认高度，请填写为0', 57, 1, 0, 1740631596, 1740631596, 0),
(56, '开启RSA自动加/解密', 5, 'do_is_rsa', '0', 'checker', '', '必须开启\"openssl\"扩展', 58, 1, 0, 1740631596, 1740631596, 0),
(57, '选项卡样式', 2, 'admin_tab_style', 'layui-tab-brief', 'select', '{\"layui-tab-brief\":\"简洁风格\",\"layui-tab-default\":\"凹型风格\",\"layui-tab-card\":\"卡片风格\"}', '', 59, 1, 0, 1740631596, 1740631596, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_setting_group`
--

CREATE TABLE `woo_setting_group` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '组标题',
  `var` varchar(64) NOT NULL DEFAULT '' COMMENT '组变量',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `is_scene` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否场景组',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统设置组';

--
-- 转存表中的数据 `woo_setting_group`
--

INSERT INTO `woo_setting_group` (`id`, `title`, `var`, `list_order`, `admin_id`, `is_scene`, `create_time`, `update_time`, `delete_time`) VALUES
(1, '项目信息', 'project', 6, 1, 0, 1740631596, 1740631596, 0),
(2, '后台全局', 'admin', 1, 1, 0, 1740631596, 1740631596, 0),
(3, '上传配置', 'upload', 3, 1, 0, 1740631596, 1740631596, 0),
(4, '表格配置', 'table', 2, 1, 0, 1740631596, 1740631596, 0),
(5, '功能配置', 'do', 5, 1, 0, 1740631596, 1740631596, 0),
(6, '用户配置', 'user', 4, 1, 0, 1740631596, 1740631596, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_shortcut`
--

CREATE TABLE `woo_shortcut` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '类型',
  `url` varchar(256) NOT NULL DEFAULT '' COMMENT 'URL',
  `icon` varchar(64) NOT NULL DEFAULT '' COMMENT '图标',
  `func` varchar(64) NOT NULL DEFAULT '' COMMENT '函数名',
  `target` varchar(64) NOT NULL DEFAULT '' COMMENT '跳转方式',
  `is_verify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `admin_group_id` varchar(128) NOT NULL DEFAULT '' COMMENT '用户组ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='快捷方式';

--
-- 转存表中的数据 `woo_shortcut`
--

INSERT INTO `woo_shortcut` (`id`, `title`, `type`, `url`, `icon`, `func`, `target`, `is_verify`, `list_order`, `admin_group_id`, `create_time`, `update_time`, `delete_time`) VALUES
(1, '清除缓存', 'func', 'tool/clearCache', 'woo-icon-qingchuhuancun', 'simple_clear', '', 1, 1, '', 1740631596, 1740631596, 0),
(2, '下载日志 ', 'url', 'tool/getLog', 'layui-icon-download-circle', '', '', 1, 2, '', 1740631596, 1740631596, 1740631596),
(3, '临时文件', 'func', 'tool/removeTemp', 'woo-icon-yaoqing', 'simple_clear', '', 1, 3, '4,2,1', 1740631596, 1740631596, 0),
(4, '系统配置', 'url', 'setting/set', 'layui-icon-set-sm', '', 'newtab', 1, 5, '', 1740631596, 1740631596, 0),
(5, '个人信息', 'url', 'admin/home', 'layui-icon-username', '', 'newtab', 1, 6, '', 1740631596, 1740631596, 0),
(6, '系统官网', 'url', '', 'layui-icon-website', '', 'newtab', 1, 7, '', 1740631596, 1740631596, 0),
(7, '开发手册', 'url', '', 'layui-icon-survey', '', 'newtab', 1, 10, '', 1740631596, 1740631596, 0),
(8, '百度搜索', 'url', 'https://www.baidu.com/', 'layui-icon-search', '', 'newtab', 1, 11, '', 1740631596, 1740631596, 0),
(9, '联系客服', 'url', '', 'layui-icon-service', '', 'newtab', 1, 8, '', 1740631596, 1740631596, 0),
(10, '加入群聊', 'url', '', 'layui-icon-login-qq', '', 'newtab', 1, 9, '', 1740631596, 1740631596, 0),
(11, '公司OA', 'func', '', 'layui-icon-heart', '', '', 1, 12, '', 1740631596, 1740631596, 0),
(12, '自定义一', 'func', '', 'layui-icon-ios', '', '', 1, 13, '', 1740631596, 1740631596, 0),
(13, '日志下载', 'url', 'tool/getLog', 'woo-icon-daoru', '', '', 1, 4, '', 1740631596, 1740631596, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_sign`
--

CREATE TABLE `woo_sign` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `date` date NOT NULL DEFAULT '2000-01-01' COMMENT '日期',
  `year` int(11) NOT NULL DEFAULT '0' COMMENT '年',
  `month` int(11) NOT NULL DEFAULT '0' COMMENT '月',
  `day` int(11) NOT NULL DEFAULT '0' COMMENT '日',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '获得积分',
  `continue` int(11) NOT NULL DEFAULT '0' COMMENT '连续天数',
  `time` char(32) NOT NULL DEFAULT '' COMMENT '签到时间',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='签到';

-- --------------------------------------------------------

--
-- 表的结构 `woo_statistics`
--

CREATE TABLE `woo_statistics` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `model` varchar(64) NOT NULL DEFAULT '' COMMENT '模型名称',
  `is_self` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '统计自己',
  `is_verify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `admin_group_id` varchar(128) NOT NULL DEFAULT '' COMMENT '用户组ID',
  `url` varchar(64) NOT NULL DEFAULT '' COMMENT 'URL地址',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='统计';

--
-- 转存表中的数据 `woo_statistics`
--

INSERT INTO `woo_statistics` (`id`, `title`, `model`, `is_self`, `is_verify`, `list_order`, `admin_group_id`, `url`, `create_time`, `update_time`, `delete_time`) VALUES
(1, '模型', 'Model', 0, 1, 1, '', '', 1740631596, 1740631596, 0),
(2, '用户', 'User', 0, 1, 2, '', '', 1740631596, 1740631596, 0),
(3, '订单', 'Order', 0, 1, 3, '', '', 1740631596, 1740631596, 0),
(4, '业绩', 'A', 0, 1, 4, '', '', 1740631596, 1740631596, 0),
(5, '商家', 'B', 0, 1, 5, '', '', 1740631596, 1740631596, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_test_article`
--

CREATE TABLE `woo_test_article` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `test_menu_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属分类',
  `is_verify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '审核',
  `date` date NOT NULL DEFAULT '2000-01-01' COMMENT '日期',
  `author` varchar(64) NOT NULL DEFAULT '' COMMENT '作者',
  `from` varchar(64) NOT NULL DEFAULT '' COMMENT '来源',
  `image` varchar(128) NOT NULL DEFAULT '' COMMENT '图片',
  `content` mediumtext NOT NULL COMMENT '内容',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='测试文章';

-- --------------------------------------------------------

--
-- 表的结构 `woo_test_menu`
--

CREATE TABLE `woo_test_menu` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `family` varchar(256) NOT NULL DEFAULT '' COMMENT '家族',
  `level` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '层级',
  `children_count` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下级数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='测试分类';

--
-- 转存表中的数据 `woo_test_menu`
--

INSERT INTO `woo_test_menu` (`id`, `parent_id`, `title`, `list_order`, `family`, `level`, `children_count`, `create_time`, `update_time`) VALUES
(1, 0, '新闻中心', 1, ',1,', 1, 2, 1740631596, 1740631596),
(2, 0, '测试分类', 2, ',2,', 1, 0, 1740631596, 1740631596),
(3, 1, '公司新闻', 3, ',1,3,', 2, 0, 1740631596, 1740631596),
(4, 1, '行业动态', 4, ',1,4,', 2, 0, 1740631596, 1740631596);

-- --------------------------------------------------------

--
-- 表的结构 `woo_test_product`
--

CREATE TABLE `woo_test_product` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `is_verify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否审核',
  `image` varchar(128) NOT NULL DEFAULT '' COMMENT '图片',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `number` varchar(64) NOT NULL DEFAULT '' COMMENT '产品编号',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `sell_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价',
  `unit` varchar(64) NOT NULL DEFAULT '' COMMENT '单位'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='测试产品';

--
-- 转存表中的数据 `woo_test_product`
--

INSERT INTO `woo_test_product` (`id`, `title`, `is_verify`, `image`, `list_order`, `create_time`, `update_time`, `number`, `price`, `sell_price`, `unit`) VALUES
(1, '测试产品一', 1, '', 1, 1740631596, 1740631596, '10001', 88.00, 188.00, '公斤'),
(2, '测试产品二', 1, '', 2, 1740631596, 1740631596, '10002', 48.00, 88.00, '克'),
(3, '测试产品三', 1, '', 3, 1740631596, 1740631596, '10003', 12.00, 28.00, '只'),
(4, '测试产品四', 1, '', 4, 1740631596, 1740631596, '10004', 10.00, 18.88, '把');

-- --------------------------------------------------------

--
-- 表的结构 `woo_user`
--

CREATE TABLE `woo_user` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `pay_password` varchar(32) NOT NULL DEFAULT '' COMMENT '支付密码',
  `salt` char(16) NOT NULL DEFAULT '' COMMENT '密码盐',
  `department_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属部门',
  `user_group_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户组',
  `user_grade_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '等级',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机',
  `avatar` varchar(256) NOT NULL DEFAULT '' COMMENT '头像',
  `is_bind_mobile` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '手机绑定',
  `email` varchar(64) NOT NULL DEFAULT '' COMMENT '邮箱',
  `is_bind_email` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '邮箱绑定',
  `status` varchar(64) NOT NULL DEFAULT '' COMMENT '状态',
  `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '昵称',
  `truename` varchar(64) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `sex` tinyint(4) NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` varchar(64) NOT NULL DEFAULT '' COMMENT '生日',
  `region` varchar(64) NOT NULL DEFAULT '' COMMENT '所在地区',
  `address` varchar(64) NOT NULL DEFAULT '' COMMENT '详细地址',
  `summary` varchar(512) NOT NULL DEFAULT '' COMMENT '简介',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '积分',
  `login_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `login_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `login_id` varchar(32) NOT NULL DEFAULT '' COMMENT '最后登录SESS_ID',
  `register_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '注册IP',
  `register_type` varchar(64) NOT NULL DEFAULT '' COMMENT '注册方式',
  `is_allow_reset` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否允许初始化',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户';

--
-- 转存表中的数据 `woo_user`
--

INSERT INTO `woo_user` (`id`, `username`, `password`, `pay_password`, `salt`, `department_id`, `user_group_id`, `user_grade_id`, `mobile`, `avatar`, `is_bind_mobile`, `email`, `is_bind_email`, `status`, `nickname`, `truename`, `sex`, `birthday`, `region`, `address`, `summary`, `money`, `score`, `login_time`, `login_ip`, `login_id`, `register_ip`, `register_type`, `is_allow_reset`, `create_time`, `update_time`, `delete_time`) VALUES
(1, 'user001', '5f4dcc3b5aa765d61d8327deb882cf99', '5f4dcc3b5aa765d61d8327deb882cf99', 'salt456', 0, 1, 3, '', '', 0, '', 0, 'verified', '示例用户', '', 0, '', '', '', '', 100.00, 0.00, 0, '0.0.0.0', '', '0.0.0.0', '', 0, 1740631596, 1740631596, 0),
-- --------------------------------------------------------

--
-- 表的结构 `woo_user_grade`
--

CREATE TABLE `woo_user_grade` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `image` varchar(128) NOT NULL DEFAULT '' COMMENT '等级图标',
  `min` int(11) NOT NULL DEFAULT '0' COMMENT '积分最低值',
  `max` int(11) NOT NULL DEFAULT '0' COMMENT '积分最大值',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户等级';

--
-- 转存表中的数据 `woo_user_grade`
--

INSERT INTO `woo_user_grade` (`id`, `title`, `image`, `min`, `max`, `list_order`, `create_time`, `update_time`) VALUES
(1, '零星游侠', '', 10, 100, 1, 1740631596, 1740651513),
(2, '十方行客', '', 100, 500, 2, 1740631596, 1740651523),
(3, '百川剑客', '', 500, 1500, 3, 1740631596, 1740651533),
(4, '千仞刀狂', '', 1500, 3000, 4, 1740631596, 1740651546),
(5, '万钧掌尊', '', 3000, 5000, 5, 1740631596, 1740651688),
(6, '兆载狂煞', '', 5000, 8000, 6, 1740631596, 1740651734),
(7, '​亿兆琴魔', '', 8000, 15000, 7, 1740651780, 1740651780),
(8, '无量剑圣', '', 15000, 25000, 8, 1740651819, 1740651819),
(9, '混沌尊者', '', 25000, 50000, 9, 1740651870, 1740651870),
(10, '永恒无相', '', 50000, 9999999, 10, 1740651890, 1740651890);

-- --------------------------------------------------------

--
-- 表的结构 `woo_user_group`
--

CREATE TABLE `woo_user_group` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `summary` text NOT NULL COMMENT '描述',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户组';

--
-- 转存表中的数据 `woo_user_group`
--

INSERT INTO `woo_user_group` (`id`, `title`, `summary`, `create_time`, `update_time`) VALUES
(1, '注册用户', '', 1740631596, 1740631596),
(2, '企业用户', '已经审核进入企业的用户', 1740650865, 1740650865);

-- --------------------------------------------------------

--
-- 表的结构 `woo_user_login`
--

CREATE TABLE `woo_user_login` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `ip` varchar(32) NOT NULL DEFAULT '' COMMENT '登录IP',
  `user_agent` varchar(256) NOT NULL DEFAULT '' COMMENT '客户端',
  `region` varchar(128) NOT NULL DEFAULT '' COMMENT '登录地址',
  `summary` varchar(128) NOT NULL DEFAULT '' COMMENT '描述',
  `is_success` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否成功',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '登录方式',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='登录日志';

-- --------------------------------------------------------

--
-- 表的结构 `woo_user_menu`
--

CREATE TABLE `woo_user_menu` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '菜单标题',
  `url` varchar(128) NOT NULL DEFAULT '' COMMENT '规则',
  `icon` varchar(32) NOT NULL DEFAULT '' COMMENT '图标',
  `args` varchar(512) NOT NULL DEFAULT '' COMMENT '参数',
  `is_nav` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否菜单',
  `is_not_power` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '不关心权限',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `remark` text NOT NULL COMMENT '备注',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `family` varchar(256) NOT NULL DEFAULT '' COMMENT '家族',
  `level` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '层级',
  `children_count` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下级数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `is_uni` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '移动端栏目',
  `uni_route_type` varchar(64) NOT NULL DEFAULT '' COMMENT '跳转方式',
  `uni_route` varchar(64) NOT NULL DEFAULT '' COMMENT '跳转路由',
  `uni_icon` varchar(64) NOT NULL DEFAULT '' COMMENT '字体图标',
  `uni_icon_image` varchar(128) NOT NULL DEFAULT '' COMMENT '图片图标',
  `uni_click_func` varchar(64) NOT NULL DEFAULT '' COMMENT '点击回调',
  `is_uni_index` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '快捷显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户菜单';

--
-- 转存表中的数据 `woo_user_menu`
--

INSERT INTO `woo_user_menu` (`id`, `parent_id`, `title`, `url`, `icon`, `args`, `is_nav`, `is_not_power`, `list_order`, `remark`, `admin_id`, `family`, `level`, `children_count`, `create_time`, `update_time`, `is_uni`, `uni_route_type`, `uni_route`, `uni_icon`, `uni_icon_image`, `uni_click_func`, `is_uni_index`) VALUES
(1, 0, '个人设置', '', '', '', 1, 0, 1, '', 1, ',1,', 1, 5, 1740631596, 1740631596, 1, '', '', '', '', '', 0),
(2, 1, '个人主页', 'user.index/index', 'layui-icon-home', '[]', 1, 0, 2, '', 1, ',1,2,', 2, 0, 1740631596, 1740631596, 0, '', '', '', '/uploads/user_menu/20210716/bde0f2c55338ee7996a08c3a25ebafb5.png', '', 0),
(3, 1, '个人资料', 'user.user/info', 'layui-icon-username', '', 1, 0, 3, '', 1, ',1,3,', 2, 0, 1740631596, 1740631596, 1, 'navigateAuth', '/pages/user/info/info', 'man-add-fill', '/uploads/file/20211111/cc2ad0eefba2650480f507514289b584.png', '', 1),
(4, 1, '安全中心', 'user.user/safe', 'layui-icon-auz', '', 1, 0, 4, '', 1, ',1,4,', 2, 8, 1740631596, 1740631596, 1, 'navigateAuth', '/pages/user/safe/safe', '', '/uploads/file/20211111/eec144c1d13e34a209f022401dc4618e.png', 'safe', 1),
(5, 0, '内容管理', '', '', '', 1, 0, 5, '', 1, ',5,', 1, 4, 1740631596, 1740631596, 1, '', '', '', '', '', 0),
(6, 5, '投稿管理', 'user.document/index', 'layui-icon-template-1', '', 1, 0, 6, '', 1, ',5,6,', 2, 0, 1740631596, 1740631596, 0, '', '', '', '', '', 0),
(7, 5, '我要投稿', 'user.document/create', 'layui-icon-add-circle-fine', '', 1, 0, 7, '', 1, ',5,7,', 2, 0, 1740631596, 1740631596, 0, '', '', '', '', '', 0),
(8, 5, '评论管理', 'user.comment/index', 'layui-icon-survey', '', 1, 0, 8, '', 1, ',5,8,', 2, 0, 1740631596, 1740631596, 1, 'navigateAuth', '/pages/user/comment/comment', '', '/uploads/file/20211111/733aa95825bac85bf0e8b97726cfc6e6.png', 'comment', 1),
(9, 0, '账户管理', '', '', '', 1, 0, 9, '', 1, ',9,', 1, 3, 1740631596, 1740631596, 1, '', '', '', '', '', 0),
(10, 9, '每日签到', 'user.sign/index', 'layui-icon-location', '', 1, 0, 10, '', 1, ',9,10,', 2, 0, 1740631596, 1740631596, 1, 'navigateAuth', '/pages/user/sign/sign', '', '/uploads/file/20211111/dc854247db75a6272741003aca1b72ed.png', 'sign', 1),
(11, 5, '我的关注', 'user.follow/index', 'layui-icon-heart', '', 1, 0, 11, '', 1, ',5,11,', 2, 0, 1740631596, 1740631596, 1, 'navigateAuth', '/pages/user/follow/follow', '', '/uploads/file/20211110/d9bd2bd89c7007bc2798a84a9861aa37.png', 'follow', 1),
(13, 1, '退出登录', 'user.user/logout', 'layui-icon-logout', '', 1, 0, 23, '', 1, ',1,13,', 2, 0, 1740631596, 1740631596, 1, '', '', '', '/uploads/user_menu/20210716/5667ab69724c2543c0a563642a84d8af.png', 'logout', 0),
(14, 4, '绑定手机', 'user.user/bindmobile', '', '', 0, 0, 13, '', 1, ',1,4,14,', 3, 0, 1740631596, 1740631596, 0, '', '', '', '/uploads/user/20210715/e534f95bf5d9f3ca3b8cf57f2d59fed4.jpg', '', 0),
(15, 4, '手机解绑', 'user.user/unbindmobile', '', '', 0, 0, 14, '', 1, ',1,4,15,', 3, 0, 1740631596, 1740631596, 0, '', '', '', '', '', 0),
(16, 4, '邮箱绑定', 'user.user/bindemail', '', '', 0, 0, 15, '', 1, ',1,4,16,', 3, 0, 1740631596, 1740631596, 0, '', '', '', '', '', 0),
(17, 4, '邮箱解绑', 'user.user/unbindemail', '', '', 0, 0, 16, '', 1, ',1,4,17,', 3, 0, 1740631596, 1740631596, 0, '', '', '', '', '', 0),
(18, 4, '修改密码', 'user.user/password', '', '', 0, 0, 17, '', 1, ',1,4,18,', 3, 0, 1740631596, 1740631596, 0, '', '', '', '', '', 0),
(19, 4, '支付密码', 'user.user/paypassword', '', '', 0, 0, 18, '', 1, ',1,4,19,', 3, 0, 1740631596, 1740631596, 0, '', '', '', '', '', 0),
(20, 4, '注销账号', 'user.user/cancel', '', '', 0, 0, 19, '', 1, ',1,4,20,', 3, 0, 1740631596, 1740631596, 0, '', '', '', '', '', 0),
(21, 4, '实名认证', 'user.user/cert', '', '', 0, 0, 20, '', 1, ',1,4,21,', 3, 0, 1740631596, 1740631596, 0, '', '', '', '', '', 0),
(22, 9, '登录日志', 'user.user_login/index', 'layui-icon-log', '', 1, 0, 21, '', 1, ',9,22,', 2, 0, 1740631596, 1740631596, 1, 'navigateAuth', '/pages/user/user-login/user-login', '', '/uploads/file/20211110/82a0527f878c2e4d78d74a5c9157f4df.png', 'userlogin', 1),
(23, 9, '我的积分', 'user.user_score/index', 'layui-icon-diamond', '', 1, 0, 22, '', 1, ',9,23,', 2, 0, 1740631596, 1740631596, 1, 'navigateAuth', '/pages/user/user-score/user-score', '', '/uploads/file/20211111/734d82e2823351af28dd8abd4011e8d8.png', 'userscore', 1),
(24, 1, '账号绑定', 'user.user/bind', 'layui-icon-auz', '', 1, 0, 12, '', 1, ',1,24,', 2, 0, 1740631596, 1740631596, 0, '', '', '', '', '', 0),
(26, 0, '点击测试', '', '', '', 0, 0, 24, '', 1, ',26,', 1, 0, 1740631596, 1740631596, 0, '', '', 'thumb-up', '/uploads/file/20211110/216b9decbb882c99e6f5e6acab00d1a5.png', 'testClick', 1);

-- --------------------------------------------------------

--
-- 表的结构 `woo_user_money`
--

CREATE TABLE `woo_user_money` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动前',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动后',
  `foreign` varchar(64) NOT NULL DEFAULT '' COMMENT '关联模型',
  `foreign_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关联模型ID',
  `remark` varchar(512) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='余额记录';

--
-- 转存表中的数据 `woo_user_money`
--

INSERT INTO `woo_user_money` (`id`, `user_id`, `before`, `money`, `after`, `foreign`, `foreign_id`, `remark`, `create_time`, `update_time`, `delete_time`) VALUES
(1, 1, 0.00, 100.00, 100.00, 'Recharge', 1, '20200816 23:49:55成功充值100元', 1740631596, 1740631596, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_user_power`
--

CREATE TABLE `woo_user_power` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `user_group_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属用户组ID',
  `content` mediumtext NOT NULL COMMENT '内容',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户权限';

--
-- 转存表中的数据 `woo_user_power`
--

INSERT INTO `woo_user_power` (`id`, `user_group_id`, `content`, `create_time`, `update_time`) VALUES
(1, 1, '{\"2\":\"user.index\\/index\",\"3\":\"user.user\\/info\",\"4\":\"user.user\\/safe\",\"6\":\"user.document\\/index\",\"7\":\"user.document\\/create\",\"10\":\"user.sign\\/index\",\"11\":\"user.follow\\/index\",\"13\":\"user.user\\/logout\",\"14\":\"user.user\\/bindmobile\",\"15\":\"user.user\\/unbindmobile\",\"16\":\"user.user\\/bindemail\",\"17\":\"user.user\\/unbindemail\",\"18\":\"user.user\\/password\",\"19\":\"user.user\\/paypassword\",\"20\":\"user.user\\/cancel\",\"21\":\"user.user\\/cert\",\"22\":\"user.user_login\\/index\",\"23\":\"user.user_score\\/index\",\"24\":\"user.user\\/bind\"}', 1740632344, 1744877832);

-- --------------------------------------------------------

--
-- 表的结构 `woo_user_score`
--

CREATE TABLE `woo_user_score` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `giver_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发放人ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `score_rule_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '积分规则ID',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动前',
  `score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '积分',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动后',
  `remark` varchar(512) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改日期',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分记录';

--
-- 转存表中的数据 `woo_user_score`
--

INSERT INTO `woo_user_score` (`id`, `giver_id`, `user_id`, `score_rule_id`, `before`, `score`, `after`, `remark`, `create_time`, `update_time`, `delete_time`) VALUES
(1, 0, 1, 0, 0.00, 30.00, 30.00, '', 1740631596, 1740631596, 0),
(2, 0, 1, 0, 30.00, 600.00, 630.00, '', 1740631596, 1740631596, 0),
(3, 0, 1, 0, 631.00, 10.00, 641.00, 'test', 1746023525, 1746023525, 0),
(4, 0, 1, 2, 641.00, 20.00, 661.00, 'test', 1746092637, 1746092637, 0);

-- --------------------------------------------------------

--
-- 表的结构 `woo_wechat_user`
--

CREATE TABLE `woo_wechat_user` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '微信用户ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '关联用户ID（woo_user.id）',
  `openid` varchar(64) NOT NULL COMMENT '微信 OpenID',
  `unionid` varchar(64) DEFAULT NULL COMMENT '微信 UnionID',
  `nickname` varchar(255) DEFAULT NULL COMMENT '微信昵称',
  `avatar_url` varchar(255) DEFAULT NULL COMMENT '用户头像 URL',
  `gender` tinyint(4) DEFAULT '0' COMMENT '性别：0-未知，1-男，2-女',
  `phone` varchar(20) DEFAULT NULL COMMENT '电话号码',
  `is_active` tinyint(1) DEFAULT '1' COMMENT '是否启用：0-禁用，1-启用',
  `create_time` int(10) UNSIGNED DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信用户表';

--
-- 转储表的索引
--

--
-- 表的索引 `woo_addon`
--
ALTER TABLE `woo_addon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_verify` (`is_verify`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 表的索引 `woo_addon_setting`
--
ALTER TABLE `woo_addon_setting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `addon_id` (`addon_id`);

--
-- 表的索引 `woo_admin`
--
ALTER TABLE `woo_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `delete_time` (`delete_time`),
  ADD KEY `mobile` (`mobile`),
  ADD KEY `department_id` (`department_id`);

--
-- 表的索引 `woo_admin_group`
--
ALTER TABLE `woo_admin_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `list_order` (`list_order`);

--
-- 表的索引 `woo_admin_login`
--
ALTER TABLE `woo_admin_login`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `woo_admin_rule`
--
ALTER TABLE `woo_admin_rule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `is_nav` (`is_nav`);

--
-- 表的索引 `woo_admin_use_admin_group`
--
ALTER TABLE `woo_admin_use_admin_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `admin_group_id` (`admin_group_id`);

--
-- 表的索引 `woo_antispam`
--
ALTER TABLE `woo_antispam`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 表的索引 `woo_application`
--
ALTER TABLE `woo_application`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_verify` (`is_verify`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 表的索引 `woo_attachement`
--
ALTER TABLE `woo_attachement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `delete_time` (`delete_time`);

--
-- 表的索引 `woo_certification`
--
ALTER TABLE `woo_certification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `delete_time` (`delete_time`);

--
-- 表的索引 `woo_denied`
--
ALTER TABLE `woo_denied`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- 表的索引 `woo_department`
--
ALTER TABLE `woo_department`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `woo_dictionary`
--
ALTER TABLE `woo_dictionary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delete_time` (`delete_time`);

--
-- 表的索引 `woo_dictionary_item`
--
ALTER TABLE `woo_dictionary_item`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `woo_field`
--
ALTER TABLE `woo_field`
  ADD PRIMARY KEY (`id`),
  ADD KEY `model_id` (`model_id`),
  ADD KEY `field` (`field`);

--
-- 表的索引 `woo_folder`
--
ALTER TABLE `woo_folder`
  ADD PRIMARY KEY (`id`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `delete_time` (`delete_time`);

--
-- 表的索引 `woo_form_scene`
--
ALTER TABLE `woo_form_scene`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_verify` (`is_verify`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `model_id` (`model_id`);

--
-- 表的索引 `woo_import`
--
ALTER TABLE `woo_import`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 表的索引 `woo_log`
--
ALTER TABLE `woo_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 表的索引 `woo_model`
--
ALTER TABLE `woo_model`
  ADD PRIMARY KEY (`id`),
  ADD KEY `model` (`model`),
  ADD KEY `cname` (`cname`);

--
-- 表的索引 `woo_notification`
--
ALTER TABLE `woo_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`);

--
-- 表的索引 `woo_power`
--
ALTER TABLE `woo_power`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_group_id` (`admin_group_id`);

--
-- 表的索引 `woo_recharge`
--
ALTER TABLE `woo_recharge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `delete_time` (`delete_time`);

--
-- 表的索引 `woo_region`
--
ALTER TABLE `woo_region`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- 表的索引 `woo_request_log`
--
ALTER TABLE `woo_request_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `woo_score_appeal`
--
ALTER TABLE `woo_score_appeal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_score_id` (`user_score_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reply_user_id` (`reply_user_id`);

--
-- 表的索引 `woo_score_category`
--
ALTER TABLE `woo_score_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_parent` (`parent_id`);

--
-- 表的索引 `woo_score_rule`
--
ALTER TABLE `woo_score_rule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`score_category_id`);

--
-- 表的索引 `woo_sensitive`
--
ALTER TABLE `woo_sensitive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 表的索引 `woo_setting`
--
ALTER TABLE `woo_setting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `var` (`var`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 表的索引 `woo_setting_group`
--
ALTER TABLE `woo_setting_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `is_scene` (`is_scene`);

--
-- 表的索引 `woo_shortcut`
--
ALTER TABLE `woo_shortcut`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_verify` (`is_verify`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `delete_time` (`delete_time`);

--
-- 表的索引 `woo_sign`
--
ALTER TABLE `woo_sign`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `date` (`date`);

--
-- 表的索引 `woo_statistics`
--
ALTER TABLE `woo_statistics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_verify` (`is_verify`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `delete_time` (`delete_time`);

--
-- 表的索引 `woo_test_article`
--
ALTER TABLE `woo_test_article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_verify` (`is_verify`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `delete_time` (`delete_time`);

--
-- 表的索引 `woo_test_menu`
--
ALTER TABLE `woo_test_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `list_order` (`list_order`);

--
-- 表的索引 `woo_test_product`
--
ALTER TABLE `woo_test_product`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `woo_user`
--
ALTER TABLE `woo_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `delete_time` (`delete_time`),
  ADD KEY `user_group_id` (`user_group_id`),
  ADD KEY `email` (`email`),
  ADD KEY `mobile` (`mobile`);

--
-- 表的索引 `woo_user_grade`
--
ALTER TABLE `woo_user_grade`
  ADD PRIMARY KEY (`id`),
  ADD KEY `list_order` (`list_order`);

--
-- 表的索引 `woo_user_group`
--
ALTER TABLE `woo_user_group`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `woo_user_login`
--
ALTER TABLE `woo_user_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- 表的索引 `woo_user_menu`
--
ALTER TABLE `woo_user_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `list_order` (`list_order`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `is_nav` (`is_nav`);

--
-- 表的索引 `woo_user_money`
--
ALTER TABLE `woo_user_money`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `delete_time` (`delete_time`);

--
-- 表的索引 `woo_user_power`
--
ALTER TABLE `woo_user_power`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_group_id` (`user_group_id`);

--
-- 表的索引 `woo_user_score`
--
ALTER TABLE `woo_user_score`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `score_rule_id` (`score_rule_id`);

--
-- 表的索引 `woo_wechat_user`
--
ALTER TABLE `woo_wechat_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `openid` (`openid`),
  ADD KEY `user_id` (`user_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `woo_addon`
--
ALTER TABLE `woo_addon`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `woo_addon_setting`
--
ALTER TABLE `woo_addon_setting`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_admin`
--
ALTER TABLE `woo_admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `woo_admin_group`
--
ALTER TABLE `woo_admin_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `woo_admin_login`
--
ALTER TABLE `woo_admin_login`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=31;

--
-- 使用表AUTO_INCREMENT `woo_admin_rule`
--
ALTER TABLE `woo_admin_rule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=388;

--
-- 使用表AUTO_INCREMENT `woo_admin_use_admin_group`
--
ALTER TABLE `woo_admin_use_admin_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `woo_antispam`
--
ALTER TABLE `woo_antispam`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_application`
--
ALTER TABLE `woo_application`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `woo_attachement`
--
ALTER TABLE `woo_attachement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_certification`
--
ALTER TABLE `woo_certification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_denied`
--
ALTER TABLE `woo_denied`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_department`
--
ALTER TABLE `woo_department`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `woo_dictionary`
--
ALTER TABLE `woo_dictionary`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_dictionary_item`
--
ALTER TABLE `woo_dictionary_item`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_field`
--
ALTER TABLE `woo_field`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=10073;

--
-- 使用表AUTO_INCREMENT `woo_folder`
--
ALTER TABLE `woo_folder`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `woo_form_scene`
--
ALTER TABLE `woo_form_scene`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `woo_import`
--
ALTER TABLE `woo_import`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_log`
--
ALTER TABLE `woo_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_model`
--
ALTER TABLE `woo_model`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=1007;

--
-- 使用表AUTO_INCREMENT `woo_notification`
--
ALTER TABLE `woo_notification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '通知ID';

--
-- 使用表AUTO_INCREMENT `woo_power`
--
ALTER TABLE `woo_power`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `woo_recharge`
--
ALTER TABLE `woo_recharge`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `woo_region`
--
ALTER TABLE `woo_region`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_request_log`
--
ALTER TABLE `woo_request_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_score_appeal`
--
ALTER TABLE `woo_score_appeal`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '申诉ID';

--
-- 使用表AUTO_INCREMENT `woo_score_category`
--
ALTER TABLE `woo_score_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分类ID', AUTO_INCREMENT=20;

--
-- 使用表AUTO_INCREMENT `woo_score_rule`
--
ALTER TABLE `woo_score_rule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '规则ID', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `woo_sensitive`
--
ALTER TABLE `woo_sensitive`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_setting`
--
ALTER TABLE `woo_setting`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=58;

--
-- 使用表AUTO_INCREMENT `woo_setting_group`
--
ALTER TABLE `woo_setting_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `woo_shortcut`
--
ALTER TABLE `woo_shortcut`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=14;

--
-- 使用表AUTO_INCREMENT `woo_sign`
--
ALTER TABLE `woo_sign`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_statistics`
--
ALTER TABLE `woo_statistics`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `woo_test_article`
--
ALTER TABLE `woo_test_article`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_test_menu`
--
ALTER TABLE `woo_test_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `woo_test_product`
--
ALTER TABLE `woo_test_product`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `woo_user`
--
ALTER TABLE `woo_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `woo_user_grade`
--
ALTER TABLE `woo_user_grade`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `woo_user_group`
--
ALTER TABLE `woo_user_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `woo_user_login`
--
ALTER TABLE `woo_user_login`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `woo_user_menu`
--
ALTER TABLE `woo_user_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=27;

--
-- 使用表AUTO_INCREMENT `woo_user_money`
--
ALTER TABLE `woo_user_money`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `woo_user_power`
--
ALTER TABLE `woo_user_power`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `woo_user_score`
--
ALTER TABLE `woo_user_score`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `woo_wechat_user`
--
ALTER TABLE `woo_wechat_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '微信用户ID';

--
-- 限制导出的表
--

--
-- 限制表 `woo_notification`
--
ALTER TABLE `woo_notification`
  ADD CONSTRAINT `woo_notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `woo_wechat_user` (`id`) ON DELETE CASCADE;

--
-- 限制表 `woo_score_appeal`
--
ALTER TABLE `woo_score_appeal`
  ADD CONSTRAINT `woo_score_appeal_ibfk_1` FOREIGN KEY (`user_score_id`) REFERENCES `woo_user_score` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `woo_score_appeal_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `woo_user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `woo_score_appeal_ibfk_3` FOREIGN KEY (`reply_user_id`) REFERENCES `woo_user` (`id`) ON DELETE CASCADE;

--
-- 限制表 `woo_score_rule`
--
ALTER TABLE `woo_score_rule`
  ADD CONSTRAINT `woo_score_rule_ibfk_1` FOREIGN KEY (`score_category_id`) REFERENCES `woo_score_category` (`id`) ON DELETE CASCADE;

--
-- 限制表 `woo_wechat_user`
--
ALTER TABLE `woo_wechat_user`
  ADD CONSTRAINT `woo_wechat_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `woo_user` (`id`) ON DELETE CASCADE;
COMMIT;
-- ----------------------------