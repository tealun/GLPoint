# GLpoint 系统部署与更新指南

## 📋 目录

- [快速更新](#快速更新)
- [完整部署流程](#完整部署流程)
- [推荐部署方案](#推荐部署方案)
- [注意事项](#注意事项)
- [回滚方案](#回滚方案)
- [常见问题](#常见问题)

## ⚡ 快速更新

### 方式一：文件覆盖（适合小更新）

```bash
# 1. 备份当前版本
cp -r /www/wwwroot/glpoint /www/backup/glpoint_$(date +%Y%m%d_%H%M%S)

# 2. 上传新文件覆盖（保留配置和数据）
# 使用 FTP/SFTP 上传以下文件：
# - app/
# - config/ (⚠️ 不要覆盖配置文件)
# - public/
# - woo/
# - vendor/ (如果有依赖更新)

# 3. 清理缓存
php think clear

# 4. 重启 PHP-FPM（如果需要）
systemctl restart php-fpm
```

⚠️ **注意**：直接覆盖文件简单快速，但**不推荐**用于重要更新，因为：
- 容易遗漏文件
- 可能覆盖掉服务器配置
- 没有版本控制
- 出问题难以回滚

## 🚀 完整部署流程

### 首次部署

#### 1. 准备服务器环境

```bash
# 安装 LNMP/LAMP 环境
# - Nginx/Apache
# - PHP 7.2+ (推荐 8.0+)
# - MySQL 5.7+ / MariaDB 10.3+
# - Redis (可选)

# 检查 PHP 扩展
php -m | grep -E 'pdo_mysql|mbstring|json|openssl|gd|redis'
```

#### 2. 上传代码

```bash
# 方式 A: FTP/SFTP 上传整个项目
# 使用 FileZilla 等工具上传

# 方式 B: Git 克隆（推荐）
cd /www/wwwroot
git clone https://github.com/tealun/GLPoint.git glpoint
cd glpoint
```

#### 3. 配置环境

```bash
# 复制环境配置
cp .env.example .env
vim .env

# 配置数据库
DATABASE_HOSTNAME = 127.0.0.1
DATABASE_DATABASE = glpoint
DATABASE_USERNAME = root
DATABASE_PASSWORD = your_password

# 配置微信小程序
WECHAT_MINI_APP_ID = wx...
WECHAT_MINI_APP_SECRET = ...

# 配置 JWT（必须修改）
JWT_SECRET = your-32-character-secret-key

# 配置 API 地址
API_BASE_URL = https://api.yourdomain.com
```

#### 4. 安装依赖

```bash
# 安装 Composer（如果未安装）
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# 安装项目依赖
composer install --no-dev --optimize-autoloader
```

#### 5. 导入数据库

```bash
# 创建数据库
mysql -u root -p -e "CREATE DATABASE glpoint CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 导入数据
mysql -u root -p glpoint < data/database.sql
mysql -u root -p glpoint < data/point.sql
mysql -u root -p glpoint < data/region.sql
```

#### 6. 配置目录权限

```bash
# 设置所有者
chown -R www:www /www/wwwroot/glpoint

# 设置权限
chmod -R 755 /www/wwwroot/glpoint
chmod -R 777 /www/wwwroot/glpoint/runtime
chmod -R 777 /www/wwwroot/glpoint/public/uploads
```

#### 7. 配置 Nginx

```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    root /www/wwwroot/glpoint/public;
    index index.php index.html;

    # 跨域配置（如果需要）
    add_header Access-Control-Allow-Origin *;
    add_header Access-Control-Allow-Methods 'GET, POST, OPTIONS';
    add_header Access-Control-Allow-Headers 'DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Authorization';

    location / {
        if (!-e $request_filename) {
            rewrite ^(.*)$ /index.php?s=$1 last;
            break;
        }
    }

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

#### 8. 重启服务

```bash
# 重启 Nginx
systemctl restart nginx

# 重启 PHP-FPM
systemctl restart php-fpm
```

#### 9. 测试访问

```bash
# 测试 API
curl https://api.yourdomain.com

# 应该返回 ThinkPHP 的默认页面或API响应
```

### 日常更新流程

#### 标准更新步骤（推荐）

```bash
# 1. 进入项目目录
cd /www/wwwroot/glpoint

# 2. 备份当前版本
tar -czf /www/backup/glpoint_$(date +%Y%m%d_%H%M%S).tar.gz .

# 3. 拉取最新代码（Git方式）
git fetch origin
git pull origin main

# 或者上传文件覆盖（FTP方式）
# 使用 FTP 工具上传更新的文件

# 4. 更新依赖（如果 composer.json 有变化）
composer install --no-dev --optimize-autoloader

# 5. 执行数据库迁移（如果有）
# 检查 data/upgrade/ 目录是否有新的 SQL 文件
mysql -u root -p glpoint < data/upgrade/v1.x.x_xxx.sql

# 6. 清理缓存
php think clear
php think optimize:route
php think optimize:schema

# 7. 重启服务
systemctl restart php-fpm

# 8. 验证更新
curl https://api.yourdomain.com/api/index/index
```

## 🎯 推荐部署方案

### 使用 Git + Webhook 自动部署

#### 1. 服务器配置 SSH 密钥

```bash
# 生成密钥
ssh-keygen -t rsa -b 4096 -C "server@yourdomain.com"

# 添加公钥到 GitHub
cat ~/.ssh/id_rsa.pub
# 复制到 GitHub Settings → SSH Keys
```

#### 2. 创建部署脚本

```bash
# 创建 /www/scripts/deploy_glpoint.sh
#!/bin/bash

PROJECT_DIR="/www/wwwroot/glpoint"
BACKUP_DIR="/www/backup"
LOG_FILE="/www/logs/deploy.log"

echo "[$(date '+%Y-%m-%d %H:%M:%S')] 开始部署..." >> $LOG_FILE

# 1. 备份
tar -czf $BACKUP_DIR/glpoint_$(date +%Y%m%d_%H%M%S).tar.gz -C $PROJECT_DIR .
echo "[$(date '+%Y-%m-%d %H:%M:%S')] 备份完成" >> $LOG_FILE

# 2. 拉取代码
cd $PROJECT_DIR
git pull origin main >> $LOG_FILE 2>&1

# 3. 更新依赖
composer install --no-dev --optimize-autoloader >> $LOG_FILE 2>&1

# 4. 清理缓存
php think clear >> $LOG_FILE 2>&1

# 5. 重启服务
systemctl restart php-fpm
echo "[$(date '+%Y-%m-%d %H:%M:%S')] 部署完成" >> $LOG_FILE

exit 0
```

```bash
# 赋予执行权限
chmod +x /www/scripts/deploy_glpoint.sh
```

#### 3. 配置 GitHub Webhook

在 GitHub 仓库设置中：
- Settings → Webhooks → Add webhook
- Payload URL: `https://yourdomain.com/deploy.php`
- Content type: `application/json`
- Secret: 设置一个密钥

创建 `/www/wwwroot/deploy.php`：

```php
<?php
// GitHub Webhook 接收脚本
$secret = 'your-webhook-secret'; // 与 GitHub 设置的一致

$signature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';
$payload = file_get_contents('php://input');

// 验证签名
list($algo, $hash) = explode('=', $signature, 2);
$payloadHash = hash_hmac($algo, $payload, $secret);

if ($hash !== $payloadHash) {
    http_response_code(403);
    die('Signature verification failed');
}

// 解析 payload
$data = json_decode($payload, true);

// 只在 push 到 main 分支时部署
if ($data['ref'] === 'refs/heads/main') {
    // 异步执行部署脚本
    exec('/www/scripts/deploy_glpoint.sh > /dev/null 2>&1 &');
    echo 'Deployment triggered';
} else {
    echo 'Not a main branch push, skipping deployment';
}
?>
```

## ⚠️ 注意事项

### 必须保留的文件和目录

更新时**不要覆盖**以下文件：

```
.env                              # 环境配置
/data/config/database.php         # 数据库配置（如果使用）
/public/uploads/                  # 用户上传的文件
/runtime/                         # 运行时缓存
```

### 必须执行的操作

每次更新后**必须执行**：

```bash
# 1. 清理缓存
php think clear

# 2. 优化自动加载
composer dump-autoload --optimize

# 3. 检查目录权限
chmod -R 777 runtime/
chmod -R 777 public/uploads/

# 4. 重启 PHP-FPM
systemctl restart php-fpm
```

### 数据库更新

如果有数据库结构变更：

```bash
# 1. 备份数据库
mysqldump -u root -p glpoint > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. 执行升级脚本
mysql -u root -p glpoint < data/upgrade/v1.x.x_xxx.sql

# 3. 验证表结构
mysql -u root -p glpoint -e "SHOW TABLES;"
```

### 小程序更新

小程序代码更新后：

```bash
# 1. 修改配置（如果需要）
cd app/mini
cp config/index.example.js config/index.js
vim config/index.js  # 修改 BASE_URL

# 2. 用微信开发者工具打开
# 3. 点击"上传"按钮上传代码
# 4. 登录微信公众平台提交审核
```

## 🔄 回滚方案

### 快速回滚

```bash
# 1. 停止服务
systemctl stop php-fpm

# 2. 恢复备份
cd /www/wwwroot
rm -rf glpoint
tar -xzf /www/backup/glpoint_YYYYMMDD_HHMMSS.tar.gz -C glpoint/

# 3. 恢复数据库（如果需要）
mysql -u root -p glpoint < /www/backup/backup_YYYYMMDD_HHMMSS.sql

# 4. 重启服务
systemctl start php-fpm
```

### Git 回滚

```bash
# 回滚到上一个版本
git reset --hard HEAD^

# 回滚到指定版本
git reset --hard <commit-hash>

# 强制推送（如果需要）
git push origin main --force
```

## 🐛 常见问题

### Q1: 更新后页面空白或报错

**原因**：缓存问题或权限问题

**解决**：
```bash
# 清理所有缓存
php think clear
rm -rf runtime/cache/*
rm -rf runtime/temp/*

# 重新生成缓存
php think optimize:route
php think optimize:schema

# 检查权限
chmod -R 777 runtime/
```

### Q2: 更新后 API 返回 404

**原因**：路由缓存或 Nginx 配置问题

**解决**：
```bash
# 清理路由缓存
php think clear

# 检查 Nginx 配置
nginx -t

# 重启 Nginx
systemctl restart nginx
```

### Q3: 数据库连接失败

**原因**：.env 配置被覆盖

**解决**：
```bash
# 恢复 .env 配置
cp /www/backup/glpoint_xxx/.env .env

# 或重新配置
vim .env
```

### Q4: Composer 依赖安装失败

**原因**：网络问题或版本冲突

**解决**：
```bash
# 使用中国镜像
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# 清理缓存重新安装
composer clear-cache
composer install --no-dev --optimize-autoloader
```

## 📚 相关文档

- [环境配置指南](ENV_CONFIG.md)
- [架构文档](ARCHITECTURE.md)
- [开发指南](DEVELOPMENT.md)
- [安全配置](SECURITY_CONFIG.md)

## 🔗 参考资源

- [ThinkPHP 6.0 部署](https://www.kancloud.cn/manual/thinkphp6_0/1037488)
- [宝塔面板部署教程](https://www.bt.cn/bbs/thread-54319-1-1.html)
- [Nginx 配置详解](https://nginx.org/en/docs/)

---

**维护者**: GLpoint Team  
**更新时间**: 2026-02-04
