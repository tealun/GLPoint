-- ----------------------------
-- 1. 微信用户表 (woo_wechat_user)
-- ----------------------------
CREATE TABLE `woo_wechat_user` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '微信用户ID',
    `user_id` INT UNSIGNED NOT NULL COMMENT '关联用户ID（woo_user.id）',
    `openid` VARCHAR(64) NOT NULL UNIQUE COMMENT '微信 OpenID',
    `unionid` VARCHAR(64) COMMENT '微信 UnionID',
    `nickname` VARCHAR(255) NULL DEFAULT NULL COMMENT '微信昵称',
    `avatar_url` VARCHAR(255) COMMENT '用户头像 URL',
    `gender` TINYINT DEFAULT 0 COMMENT '性别：0-未知，1-男，2-女',
    `phone` VARCHAR(20) COMMENT '电话号码',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '是否启用：0-禁用，1-启用',
    `create_time` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    `update_time` INT UNSIGNED DEFAULT 0 COMMENT '更新时间',
    `delete_time` INT UNSIGNED DEFAULT 0 COMMENT '删除时间',
    FOREIGN KEY (`user_id`) REFERENCES `woo_user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信用户表';

-- ----------------------------
-- 2. 用户通知表 (woo_notification)
-- ----------------------------
CREATE TABLE `woo_notification` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '通知ID',
    `user_id` INT UNSIGNED NOT NULL COMMENT '接收用户ID（woo_wechat_user.id）',
    `type` ENUM('system', 'subscribe') DEFAULT 'system' COMMENT '通知类型：system-系统通知，subscribe-订阅消息',
    `template_id` VARCHAR(100) DEFAULT '' COMMENT '订阅消息模板ID（仅订阅消息类型使用）',
    `title` VARCHAR(255) DEFAULT '' COMMENT '通知标题',
    `message` TEXT NOT NULL COMMENT '通知内容',
    `is_read` TINYINT(1) DEFAULT 0 COMMENT '是否已读：0-未读，1-已读',
    `create_time` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    `read_time` INT UNSIGNED DEFAULT 0 COMMENT '已读时间',
    `delete_time` INT UNSIGNED DEFAULT 0 COMMENT '删除时间',
    INDEX `idx_user` (`user_id`),
    INDEX `idx_type` (`type`),
    INDEX `idx_template` (`template_id`),
    FOREIGN KEY (`user_id`) REFERENCES `woo_wechat_user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户通知表';

-- ----------------------------
-- 3. 积分规则分类表 (woo_score_category) - 支持无限级
-- ----------------------------
CREATE TABLE `woo_score_category` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '分类ID',
    `parent_id` INT UNSIGNED DEFAULT 0 COMMENT '父级分类ID（0表示顶级）',
    `family` VARCHAR(255) COMMENT '家族路径（如 0,1,2）',
    `level` TINYINT UNSIGNED DEFAULT 0 COMMENT '当前层级（从0开始）',
    `children_count` INT UNSIGNED DEFAULT 0 COMMENT '子级数量',
    `category_name` VARCHAR(255) NOT NULL COMMENT '分类名称',
    `description` TEXT COMMENT '分类描述',
    `list_order` INT UNSIGNED DEFAULT 0 COMMENT '排序权重',
    `is_nav` TINYINT(1) DEFAULT 1 COMMENT '是否显示：0-隐藏，1-显示',
    `create_time` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    `update_time` INT UNSIGNED DEFAULT 0 COMMENT '更新时间',
    `delete_time` INT UNSIGNED DEFAULT 0 COMMENT '删除时间',
    INDEX `idx_parent` (`parent_id`)  -- 父级ID索引优化查询
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分规则分类表（无限级）';

-- ----------------------------
-- 4. 积分规则表 (woo_score_rule)
-- ----------------------------
CREATE TABLE `woo_score_rule` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '规则ID',
    `category_id` INT UNSIGNED NOT NULL COMMENT '分类ID',
    `rule_name` VARCHAR(255) NOT NULL COMMENT '规则名称',
    `score` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '积分数',
    `description` TEXT COMMENT '规则描述',
    `list_order` INT UNSIGNED DEFAULT 0 COMMENT '排序权重',
    `is_nav` TINYINT(1) DEFAULT 1 COMMENT '是否显示：0-隐藏，1-显示',
    `create_time` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    `update_time` INT UNSIGNED DEFAULT 0 COMMENT '更新时间',
    `delete_time` INT UNSIGNED DEFAULT 0 COMMENT '删除时间',
    FOREIGN KEY (`category_id`) REFERENCES `woo_score_category`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分规则表';

-- ----------------------------
-- 5. 积分申诉表 (woo_score_appeal)
-- ----------------------------
CREATE TABLE `woo_score_appeal` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '申诉ID',
    `user_score_id` INT UNSIGNED NOT NULL COMMENT '积分记录ID（关联woo_user_score.id）',
    `user_id` INT UNSIGNED NOT NULL COMMENT '申诉用户ID（关联woo_user.id）',
    `reply_user_id` INT UNSIGNED NOT NULL COMMENT '回复人ID（关联woo_user.id）',
    `reason` TEXT NOT NULL COMMENT '申诉理由',
    `reply` TEXT COMMENT '管理员回复',
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' COMMENT '申诉状态',
    `create_time` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    `update_time` INT UNSIGNED DEFAULT 0 COMMENT '更新时间',
    `delete_time` INT UNSIGNED DEFAULT 0 COMMENT '删除时间',
    FOREIGN KEY (`user_score_id`) REFERENCES `woo_user_score`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `woo_user`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`reply_user_id`) REFERENCES `woo_user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分申诉记录表';

-- ----------------------------
-- 6. 订阅消息授权表 (woo_subscribe_auth)
-- ----------------------------
CREATE TABLE `woo_subscribe_auth` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '主键ID',
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID（关联woo_user.id）',
    `template_id` VARCHAR(100) NOT NULL COMMENT '微信订阅消息模板ID',
    `template_name` VARCHAR(100) DEFAULT '' COMMENT '模板名称（如：积分变动通知）',
    `status` ENUM('accept', 'reject', 'ban') NOT NULL DEFAULT 'reject' COMMENT '订阅状态：accept-同意，reject-拒绝，ban-封禁',
    `create_time` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    `update_time` INT UNSIGNED DEFAULT 0 COMMENT '更新时间',
    INDEX `idx_user_template` (`user_id`, `template_id`),
    INDEX `idx_status` (`status`),
    UNIQUE KEY `uk_user_template` (`user_id`, `template_id`),
    FOREIGN KEY (`user_id`) REFERENCES `woo_user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订阅消息授权表';

-- 修复用户表ID自增问题,修改users表的AUTO_INCREMENT属性:
ALTER TABLE `woo_user` AUTO_INCREMENT=1;

-- ----------------------------
-- 7. 积分系统菜单数据 (woo_admin_rule)
-- ----------------------------
-- 注意：以下菜单插入需要在完整安装 database.sql 后执行
-- 如果是全新安装，这些菜单会与主系统菜单一起创建

-- 积分目录菜单（如果不存在）
INSERT IGNORE INTO `woo_admin_rule` (
    `id`, `parent_id`, `title`, `is_nav`, `list_order`, `family`, `level`, `children_count`,
    `create_time`, `update_time`, `type`, `icon`, `addon`, `controller`, `action`,
    `url`, `args`, `open_type`, `jianpin`, `pinyin`, `rule`, `other_name`, `js_func`
) VALUES (
    327, 0, '积分', 1, 3, ',327,', 1, 6, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    'directory', 'woo-icon-jifen', '', '', '', '', '', '_iframe', 'jf', 'jifen', '', '', ''
);

-- 积分发放菜单
INSERT IGNORE INTO `woo_admin_rule` (
    `id`, `parent_id`, `title`, `is_nav`, `list_order`, `family`, `level`, `children_count`,
    `create_time`, `update_time`, `type`, `icon`, `addon`, `controller`, `action`,
    `url`, `args`, `open_type`, `jianpin`, `pinyin`, `rule`, `other_name`, `js_func`
) VALUES (
    331, 327, '积分发放', 1, 1, ',327,331,', 2, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    'menu', 'layui-icon-add-1', '', 'user_score', 'create', '', '', '_iframe', 'jfff', 'jifenfafang', 'user_score/create', '', ''
);

-- 积分排名菜单 ⭐ 新增
INSERT IGNORE INTO `woo_admin_rule` (
    `id`, `parent_id`, `title`, `is_nav`, `list_order`, `family`, `level`, `children_count`,
    `create_time`, `update_time`, `type`, `icon`, `addon`, `controller`, `action`,
    `url`, `args`, `open_type`, `jianpin`, `pinyin`, `rule`, `other_name`, `js_func`
) VALUES (
    360, 327, '积分排名', 1, 2, ',327,360,', 2, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    'menu', 'layui-icon-chart', '', 'user_score', 'ranking', '', '', '_iframe', 'jfpm', 'jifenpaiming', 'user_score/ranking', '', ''
);

-- 积分记录菜单
INSERT IGNORE INTO `woo_admin_rule` (
    `id`, `parent_id`, `title`, `is_nav`, `list_order`, `family`, `level`, `children_count`,
    `create_time`, `update_time`, `type`, `icon`, `addon`, `controller`, `action`,
    `url`, `args`, `open_type`, `jianpin`, `pinyin`, `rule`, `other_name`, `js_func`
) VALUES (
    53, 327, '积分记录', 1, 7, ',327,53,', 2, 7, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    'menu', 'layui-icon-list', '', 'user_score', 'index', '', '', '_iframe', 'jfjl', 'jifenjilu', 'user_score/index', '', ''
);

-- 积分申诉菜单
INSERT IGNORE INTO `woo_admin_rule` (
    `id`, `parent_id`, `title`, `is_nav`, `list_order`, `family`, `level`, `children_count`,
    `create_time`, `update_time`, `type`, `icon`, `addon`, `controller`, `action`,
    `url`, `args`, `open_type`, `jianpin`, `pinyin`, `rule`, `other_name`, `js_func`
) VALUES (
    335, 327, '积分申诉', 1, 9, ',327,335,', 2, 9, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    'menu', 'woo-icon-reception', '', 'score_appeal', 'index', '', '', '_iframe', 'jfss', 'jifenshensu', 'score_appeal/index', '', ''
);

-- 积分规则表菜单
INSERT IGNORE INTO `woo_admin_rule` (
    `id`, `parent_id`, `title`, `is_nav`, `list_order`, `family`, `level`, `children_count`,
    `create_time`, `update_time`, `type`, `icon`, `addon`, `controller`, `action`,
    `url`, `args`, `open_type`, `jianpin`, `pinyin`, `rule`, `other_name`, `js_func`
) VALUES (
    346, 327, '积分规则表', 1, 6, ',327,346,', 2, 6, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    'menu', 'woo-icon-list-full', '', 'score_rule', 'index', '', '', '_iframe', 'jfgzb', 'jifenguizebiao', 'score_rule/index', '', ''
);

-- 积分看板菜单（Dashboard）
INSERT IGNORE INTO `woo_admin_rule` (
    `id`, `parent_id`, `title`, `is_nav`, `list_order`, `family`, `level`, `children_count`,
    `create_time`, `update_time`, `type`, `icon`, `addon`, `controller`, `action`,
    `url`, `args`, `open_type`, `jianpin`, `pinyin`, `rule`, `other_name`, `js_func`
) VALUES (
    359, 327, '积分看板', 1, 0, ',327,359,', 2, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    'menu', 'layui-icon-chart-screen', '', 'user_score', 'dashboard', '', '', '_iframe', 'jfkb', 'jifenkanban', 'user_score/dashboard', '', ''
);

-- 说明：
-- 1. 使用 INSERT IGNORE 避免重复插入
-- 2. id 需要与 database.sql 保持一致
-- 3. create_time 和 update_time 使用 UNIX_TIMESTAMP() 自动生成
-- 4. 新安装时会自动创建所有积分相关菜单
-- 5. 升级时不会影响已存在的菜单记录