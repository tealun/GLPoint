-- 积分排名菜单添加脚本
-- 说明：在积分（parent_id=327）下添加"积分排名"菜单项
-- 执行方式：在数据库管理工具中执行此SQL，或在后台系统中手动添加菜单

-- 1. 添加"积分排名"菜单（parent_id=327为积分目录）
-- 注意：如果id=360已存在，会自动跳过
INSERT IGNORE INTO `woo_admin_rule` (
    `id`, 
    `parent_id`, 
    `title`, 
    `is_nav`, 
    `list_order`, 
    `family`, 
    `level`, 
    `children_count`, 
    `create_time`, 
    `update_time`, 
    `type`, 
    `icon`, 
    `addon`, 
    `controller`, 
    `action`, 
    `url`, 
    `args`, 
    `open_type`, 
    `jianpin`, 
    `pinyin`, 
    `rule`, 
    `other_name`, 
    `js_func`
) VALUES (
    360,                    -- id（请根据实际情况调整）
    327,                    -- parent_id（积分目录）
    '积分排名',              -- title
    1,                      -- is_nav（显示在导航）
    2,                      -- list_order（排序，放在积分发放后面）
    ',327,360,',            -- family
    2,                      -- level
    0,                      -- children_count
    UNIX_TIMESTAMP(),       -- create_time
    UNIX_TIMESTAMP(),       -- update_time
    'menu',                 -- type
    'layui-icon-chart',     -- icon
    '',                     -- addon
    'user_score',           -- controller
    'ranking',              -- action
    '',                     -- url
    '',                     -- args
    '_iframe',              -- open_type
    'jfpm',                 -- jianpin（简拼）
    'jifenpaiming',         -- pinyin（拼音）
    'user_score/ranking',   -- rule
    '',                     -- other_name
    ''                      -- js_func
);

-- 2. 更新积分目录的children_count（从5增加到6）
UPDATE `woo_admin_rule` SET `children_count` = `children_count` + 1 WHERE `id` = 327;

-- 3. 查询验证
SELECT * FROM `woo_admin_rule` WHERE `id` IN (327, 360);
