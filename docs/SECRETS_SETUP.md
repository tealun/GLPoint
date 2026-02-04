# GitHub Secrets 配置清单

## 🔐 必需配置的 Secrets

复制以下内容到 GitHub 仓库的 Settings → Secrets and variables → Actions

### 服务器连接配置

```bash
# 1. SERVER_HOST - 服务器 IP 或域名
# 示例: 123.456.789.0 或 server.yourdomain.com
SERVER_HOST=

# 2. SERVER_USER - SSH 登录用户名
# 推荐使用: www 或 deploy（不推荐用 root）
SERVER_USER=

# 3. SSH_PRIVATE_KEY - SSH 私钥
# 生成方法: ssh-keygen -t rsa -b 4096 -C "github-actions@glpoint"
# 需要完整内容，包括 -----BEGIN RSA PRIVATE KEY----- 和 -----END RSA PRIVATE KEY-----
SSH_PRIVATE_KEY=

# 4. SERVER_PORT - SSH 端口（可选，默认 22）
SERVER_PORT=22
```

### 项目路径配置

```bash
# 5. PROJECT_DIR - 服务器项目目录
# 示例: /www/wwwroot/glpoint 或 /var/www/html/glpoint
PROJECT_DIR=

# 6. BACKUP_DIR - 备份目录
# 示例: /www/backup 或 /var/backups/glpoint
BACKUP_DIR=
```

### 系统配置

```bash
# 7. WEB_USER - Web 服务器运行用户（可选，默认 www）
# 常见值: www, nginx, apache, www-data
WEB_USER=www

# 8. API_URL - API 地址（用于健康检查）
# 示例: https://api.yourdomain.com
API_URL=
```

## 📧 可选配置（部署通知）

```bash
# 9. TELEGRAM_BOT_TOKEN - Telegram 机器人 Token
# 获取方法: 联系 @BotFather 创建机器人
# 格式: 123456789:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_BOT_TOKEN=

# 10. TELEGRAM_CHAT_ID - Telegram 聊天 ID
# 获取方法: 联系 @userinfobot
# 格式: 纯数字，如 123456789
TELEGRAM_CHAT_ID=
```

## 🔑 SSH 密钥配置步骤

### 1. 生成密钥对

```bash
# 在本地执行
ssh-keygen -t rsa -b 4096 -C "github-actions@glpoint" -f ~/.ssh/glpoint_deploy

# 输出两个文件:
# - glpoint_deploy (私钥)
# - glpoint_deploy.pub (公钥)
```

### 2. 添加公钥到服务器

```bash
# 查看公钥内容
cat ~/.ssh/glpoint_deploy.pub

# 复制内容，然后连接到服务器
ssh root@your-server-ip

# 添加到 authorized_keys
mkdir -p ~/.ssh
chmod 700 ~/.ssh
echo "你的公钥内容" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

### 3. 获取私钥内容

```bash
# 查看私钥（完整内容）
cat ~/.ssh/glpoint_deploy

# 复制从 -----BEGIN RSA PRIVATE KEY----- 到 -----END RSA PRIVATE KEY----- 的所有内容
# 包括这两行
```

### 4. 添加到 GitHub Secrets

1. 打开仓库页面
2. Settings → Secrets and variables → Actions
3. 点击 "New repository secret"
4. Name: `SSH_PRIVATE_KEY`
5. Value: 粘贴完整私钥内容
6. 点击 "Add secret"

## ✅ 配置验证清单

### 服务器端检查

```bash
# 连接到服务器
ssh root@your-server-ip

# 1. 检查目录存在
ls -la /www/wwwroot/glpoint
ls -la /www/backup

# 2. 检查权限
ls -la /www/wwwroot/ | grep glpoint
# 应该显示 www:www 或对应的 web 用户

# 3. 检查 PHP
php -v

# 4. 检查 Composer
composer --version

# 5. 检查 .env 文件
cat /www/wwwroot/glpoint/.env

# 6. 测试 PHP-FPM 重启权限
systemctl restart php-fpm
# 或
service php-fpm restart
```

### GitHub 端检查

1. **验证 Secrets 配置**
   - Settings → Secrets and variables → Actions
   - 确认所有必需的 Secrets 都已添加

2. **测试手动触发**
   - Actions → 部署到生产服务器 → Run workflow
   - 选择 main 分支
   - 点击 Run workflow

3. **查看部署日志**
   - 等待 workflow 运行完成
   - 点击 workflow 查看详细日志
   - 检查每个步骤是否成功

### 本地测试 SSH 连接

```bash
# 使用生成的密钥测试连接
ssh -i ~/.ssh/glpoint_deploy root@your-server-ip

# 如果成功，应该能直接登录（不需要密码）
```

## 🚨 常见错误排查

### Error: Permission denied (publickey)

**原因**: 私钥或公钥配置错误

**解决**:
1. 确认私钥完整（包括 BEGIN/END 行）
2. 确认公钥已添加到服务器
3. 检查服务器 SSH 配置允许密钥登录

### Error: Host key verification failed

**原因**: 首次连接服务器，未信任主机

**解决**:
```yaml
# 在 deploy.yml 中添加
script: |
  mkdir -p ~/.ssh
  ssh-keyscan -H ${{ secrets.SERVER_HOST }} >> ~/.ssh/known_hosts
```

### Error: php: command not found

**原因**: 服务器未安装 PHP 或 PATH 配置问题

**解决**:
```bash
# 服务器安装 PHP
apt install php7.4-cli  # Ubuntu/Debian
yum install php74-cli   # CentOS

# 或指定完整路径
/usr/bin/php think clear
```

### Error: Permission denied on restart

**原因**: 用户无权重启服务

**解决**:
```bash
# 给用户 sudo 权限（不需要密码）
visudo
# 添加:
deploy ALL=(ALL) NOPASSWD: /usr/bin/systemctl restart php-fpm
```

## 📝 配置示例

### 完整配置示例

```
SERVER_HOST=123.45.67.89
SERVER_USER=deploy
SSH_PRIVATE_KEY=-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEA1234567890...
(完整私钥内容，包含多行)
...
-----END RSA PRIVATE KEY-----
SERVER_PORT=22
PROJECT_DIR=/www/wwwroot/glpoint
BACKUP_DIR=/www/backup
WEB_USER=www
API_URL=https://api.glpoint.com
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHI_jklMNOpqrsTUVwxyz
TELEGRAM_CHAT_ID=987654321
```

## 🔒 安全提示

1. ✅ **永远不要**将私钥提交到代码仓库
2. ✅ **定期轮换** SSH 密钥（建议每季度）
3. ✅ **使用专用用户**部署，不要使用 root
4. ✅ **限制 SSH 访问**，仅允许密钥登录
5. ✅ **监控部署活动**，启用审计日志

## 📚 相关文档

- [GitHub Actions 配置指南](GITHUB_ACTIONS.md)
- [部署文档](DEPLOYMENT.md)

---

**维护者**: GLpoint Team  
**更新时间**: 2026-02-04
