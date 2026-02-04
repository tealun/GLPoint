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