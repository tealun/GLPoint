-- ----------------------------
-- 数据库升级脚本 v1.1.0 - 订阅消息功能
-- 升级日期：2026-02-04
-- 用途：为已安装的系统添加订阅消息功能
-- 注意：此脚本支持重复执行（幂等性）
-- ----------------------------

-- 1. 扩展 woo_notification 表，添加订阅消息支持
-- 检查字段是否存在，不存在则添加
SET @db_name = DATABASE();

-- 添加 type 字段
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'woo_notification' AND COLUMN_NAME = 'type');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `woo_notification` ADD COLUMN `type` ENUM(''system'', ''subscribe'') DEFAULT ''system'' COMMENT ''通知类型：system-系统通知，subscribe-订阅消息'' AFTER `user_id`',
    'SELECT ''Column type already exists'' AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加 template_id 字段
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'woo_notification' AND COLUMN_NAME = 'template_id');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `woo_notification` ADD COLUMN `template_id` VARCHAR(100) DEFAULT '''' COMMENT ''订阅消息模板ID（仅订阅消息类型使用）'' AFTER `type`',
    'SELECT ''Column template_id already exists'' AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加 title 字段
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'woo_notification' AND COLUMN_NAME = 'title');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `woo_notification` ADD COLUMN `title` VARCHAR(255) DEFAULT '''' COMMENT ''通知标题'' AFTER `template_id`',
    'SELECT ''Column title already exists'' AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加索引（如果不存在）
SET @index_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'woo_notification' AND INDEX_NAME = 'idx_type');
SET @sql = IF(@index_exists = 0,
    'ALTER TABLE `woo_notification` ADD INDEX `idx_type` (`type`)',
    'SELECT ''Index idx_type already exists'' AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'woo_notification' AND INDEX_NAME = 'idx_template');
SET @sql = IF(@index_exists = 0,
    'ALTER TABLE `woo_notification` ADD INDEX `idx_template` (`template_id`)',
    'SELECT ''Index idx_template already exists'' AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. 创建订阅消息授权表（如果不存在）
CREATE TABLE IF NOT EXISTS `woo_subscribe_auth` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '主键ID',
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID（关联woo_user.id）',
  `template_id` VARCHAR(100) NOT NULL COMMENT '微信订阅消息模板ID',
  `template_name` VARCHAR(100) DEFAULT '' COMMENT '模板名称（如：积分变动通知）',
  `status` ENUM('accept', 'reject', 'ban') NOT NULL DEFAULT 'reject' COMMENT '订阅状态：accept-同意，reject-拒绝，ban-封禁',
  `create_time` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
  `update_time` INT UNSIGNED DEFAULT 0 COMMENT '更新时间',
  INDEX `idx_user_template` (`user_id`, `template_id`),
  INDEX `idx_status` (`status`),
  UNIQUE KEY `uk_user_template` (`user_id`, `template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订阅消息授权表';

-- 添加外键（如果不存在）
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'woo_subscribe_auth' 
    AND CONSTRAINT_NAME = 'fk_subscribe_auth_user');
SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE `woo_subscribe_auth` ADD CONSTRAINT `fk_subscribe_auth_user` FOREIGN KEY (`user_id`) REFERENCES `woo_user`(`id`) ON DELETE CASCADE',
    'SELECT ''Foreign key fk_subscribe_auth_user already exists'' AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. 更新现有通知数据，设置默认类型为系统通知（仅当字段存在且值为空时）
UPDATE `woo_notification` 
SET `type` = 'system' 
WHERE `type` IS NULL OR `type` = '' OR `type` NOT IN ('system', 'subscribe');
