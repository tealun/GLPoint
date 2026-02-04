# GitHub Actions 自动部署配置指南

## 📋 概述

本项目使用 GitHub Actions 实现自动部署到生产服务器。当代码推送到 `main` 分支时，会自动触发部署流程。

## 🔐 配置 GitHub Secrets

### 必需配置的 Secrets

在 GitHub 仓库设置中配置以下 Secrets：

**Settings → Secrets and variables → Actions → New repository secret**

| Secret 名称 | 说明 | 示例 |
|------------|------|------|
| `SERVER_HOST` | 服务器 IP 地址或域名 | `123.456.789.0` |
| `SERVER_USER` | SSH 登录用户名 | `root` 或 `www` |
| `SSH_PRIVATE_KEY` | SSH 私钥（完整内容） | `-----BEGIN RSA PRIVATE KEY-----...` |
| `SERVER_PORT` | SSH 端口（可选，默认22） | `22` |
| `PROJECT_DIR` | 服务器项目目录 | `/www/wwwroot/glpoint` |
| `BACKUP_DIR` | 备份目录 | `/www/backup` |
| `WEB_USER` | Web 服务器用户（可选，默认www） | `www` 或 `nginx` |
| `API_URL` | API 地址（用于健康检查） | `https://api.yourdomain.com` |

### 可选配置（通知相关）

| Secret 名称 | 说明 | 获取方式 |
|------------|------|---------|
| `TELEGRAM_BOT_TOKEN` | Telegram 机器人 Token | 联系 @BotFather 创建 |
| `TELEGRAM_CHAT_ID` | Telegram 聊天 ID | 联系 @userinfobot 获取 |

## 🔑 生成 SSH 密钥

### 步骤 1：在本地生成密钥对

```bash
# 生成新的 SSH 密钥对
ssh-keygen -t rsa -b 4096 -C "github-actions@glpoint" -f ~/.ssh/glpoint_deploy

# 会生成两个文件：
# - glpoint_deploy (私钥) → 配置到 GitHub Secrets
# - glpoint_deploy.pub (公钥) → 添加到服务器
```

### 步骤 2：添加公钥到服务器

```bash
# 方式 A：手动添加
cat ~/.ssh/glpoint_deploy.pub
# 复制输出内容，添加到服务器 ~/.ssh/authorized_keys

# 方式 B：使用 ssh-copy-id
ssh-copy-id -i ~/.ssh/glpoint_deploy.pub root@your-server-ip
```

### 步骤 3：添加私钥到 GitHub

```bash
# 复制私钥内容
cat ~/.ssh/glpoint_deploy

# 完整复制（包括 BEGIN 和 END 行），添加到 GitHub Secrets
# Secret 名称: SSH_PRIVATE_KEY
```

### 步骤 4：测试连接

```bash
# 测试 SSH 连接
ssh -i ~/.ssh/glpoint_deploy root@your-server-ip

# 如果能成功连接，说明配置正确
```

## 📁 服务器准备

### 1. 创建必要的目录

```bash
# 连接到服务器
ssh root@your-server-ip

# 创建项目目录
mkdir -p /www/wwwroot/glpoint

# 创建备份目录
mkdir -p /www/backup

# 设置权限
chown -R www:www /www/wwwroot/glpoint
```

### 2. 配置环境文件

```bash
# 在服务器上创建 .env 文件
cd /www/wwwroot/glpoint
vim .env

# 配置数据库、微信等信息
# 参考 .env.example
```

### 3. 导入数据库

```bash
# 首次部署需要导入数据库
mysql -u root -p glpoint < /path/to/database.sql
```

## 🚀 使用方式

### 自动部署

```bash
# 本地提交代码
git add .
git commit -m "feat: 新功能"
git push origin main

# GitHub Actions 会自动检测到推送并开始部署
# 可以在 GitHub 仓库的 Actions 标签页查看部署进度
```

### 手动触发

1. 进入 GitHub 仓库
2. 点击 **Actions** 标签
3. 选择 **部署到生产服务器** workflow
4. 点击 **Run workflow** 按钮
5. 选择分支，点击 **Run workflow**

## 📊 部署流程说明

### 部署步骤

```
1. 检出代码
   ↓
2. 设置 PHP 环境
   ↓
3. 安装 Composer 依赖
   ↓
4. 打包部署文件
   ↓
5. 连接服务器
   ↓
6. 备份当前版本
   ↓
7. 备份配置文件 (.env, uploads, runtime)
   ↓
8. 上传新代码
   ↓
9. 解压覆盖
   ↓
10. 恢复配置文件
   ↓
11. 设置目录权限
   ↓
12. 清理缓存
   ↓
13. 重启 PHP-FPM
   ↓
14. 健康检查
   ↓
15. 发送通知（可选）
```

### 自动保护的文件和目录

部署过程会自动保护以下内容不被覆盖：

- `.env` - 环境配置文件
- `public/uploads/` - 用户上传的文件
- `runtime/` - 运行时缓存

## 🔍 查看部署日志

### 在 GitHub 查看

1. 进入仓库 **Actions** 标签
2. 点击最近的 workflow 运行记录
3. 展开各个步骤查看详细日志

### 在服务器查看

```bash
# 查看 PHP-FPM 日志
tail -f /var/log/php-fpm/error.log

# 查看 Nginx 日志
tail -f /var/log/nginx/error.log

# 查看应用日志
tail -f /www/wwwroot/glpoint/runtime/log/$(date +%Y%m%d).log
```

## ⚠️ 故障排查

### 问题 1：SSH 连接失败

**错误信息**：`Permission denied (publickey)`

**解决方案**：
1. 检查私钥格式是否完整（包括 BEGIN/END 行）
2. 确认公钥已添加到服务器 `~/.ssh/authorized_keys`
3. 检查服务器 SSH 配置允许密钥登录

```bash
# 服务器检查
cat ~/.ssh/authorized_keys  # 确认公钥存在
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys

# 检查 SSH 配置
vim /etc/ssh/sshd_config
# 确认以下配置：
# PubkeyAuthentication yes
# AuthorizedKeysFile .ssh/authorized_keys
```

### 问题 2：权限不足

**错误信息**：`Permission denied` 或 `Operation not permitted`

**解决方案**：

```bash
# 给 GitHub Actions 使用的用户 sudo 权限
visudo

# 添加（如果使用 deploy 用户）：
deploy ALL=(ALL) NOPASSWD: /usr/bin/systemctl restart php-fpm

# 或者使用 root 用户部署（不推荐）
```

### 问题 3：Composer 安装失败

**错误信息**：`composer install failed`

**解决方案**：

```yaml
# 在 workflow 中添加镜像配置
- name: 配置 Composer 镜像
  run: composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

- name: 安装依赖
  run: composer install --no-dev --optimize-autoloader
```

### 问题 4：健康检查失败

**错误信息**：`Health check failed (HTTP 500)`

**解决方案**：

```bash
# 连接服务器检查
ssh root@your-server

# 检查错误日志
tail -50 /www/wwwroot/glpoint/runtime/log/$(date +%Y%m%d).log

# 检查权限
ls -la /www/wwwroot/glpoint/runtime
ls -la /www/wwwroot/glpoint/public/uploads

# 清理缓存
cd /www/wwwroot/glpoint
php think clear

# 重启服务
systemctl restart php-fpm
systemctl restart nginx
```

## 🔄 回滚部署

如果部署后出现问题，可以快速回滚：

```bash
# SSH 连接到服务器
ssh root@your-server-ip

# 查看备份
ls -lh /www/backup/

# 回滚到最近的备份
cd /www/wwwroot
rm -rf glpoint
mkdir glpoint
tar -xzf /www/backup/glpoint_YYYYMMDD_HHMMSS.tar.gz -C glpoint/

# 重启服务
systemctl restart php-fpm
```

## 📧 配置 Telegram 通知（可选）

### 步骤 1：创建 Telegram Bot

1. 在 Telegram 中搜索 `@BotFather`
2. 发送 `/newbot` 命令
3. 按提示设置机器人名称
4. 获取 Bot Token（格式：`123456789:ABCdefGHIjklMNOpqrsTUVwxyz`）

### 步骤 2：获取 Chat ID

1. 在 Telegram 中搜索 `@userinfobot`
2. 点击 Start
3. 获取你的 Chat ID（纯数字）

### 步骤 3：配置 Secrets

在 GitHub Secrets 中添加：
- `TELEGRAM_BOT_TOKEN`: Bot Token
- `TELEGRAM_CHAT_ID`: Chat ID

### 步骤 4：测试

推送代码触发部署，成功后会收到 Telegram 消息通知。

## 🔒 安全建议

### 1. 使用专用部署用户

```bash
# 创建专用部署用户（推荐）
useradd -m -s /bin/bash deploy
usermod -aG www deploy

# 使用 deploy 用户而不是 root
```

### 2. 限制 SSH 访问

```bash
# 编辑 SSH 配置
vim /etc/ssh/sshd_config

# 仅允许密钥登录
PasswordAuthentication no
PubkeyAuthentication yes

# 限制登录用户
AllowUsers deploy

# 重启 SSH
systemctl restart sshd
```

### 3. 定期轮换密钥

```bash
# 每季度生成新密钥对
ssh-keygen -t rsa -b 4096 -C "github-actions@glpoint" -f ~/.ssh/glpoint_deploy_$(date +%Y%m)

# 更新服务器和 GitHub Secrets
```

### 4. 监控部署活动

- 启用 GitHub Actions 审计日志
- 配置部署失败告警
- 定期检查部署日志

## 📚 相关文档

- [部署指南](DEPLOYMENT.md)
- [环境配置](ENV_CONFIG.md)
- [架构文档](ARCHITECTURE.md)

## 🔗 参考资源

- [GitHub Actions 文档](https://docs.github.com/cn/actions)
- [SSH Action](https://github.com/appleboy/ssh-action)
- [SCP Action](https://github.com/appleboy/scp-action)

---

**维护者**: GLpoint Team  
**更新时间**: 2026-02-04
