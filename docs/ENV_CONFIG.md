# GLpoint 环境配置指南

## 快速开始

### 1. 后端配置

```bash
# 进入项目根目录
cd /path/to/GLpoint

# 复制环境配置模板
cp .env.example .env

# 编辑配置文件
vim .env  # 或使用其他编辑器
```

### 2. 必填配置项

在 `.env` 文件中填写以下配置：

```ini
# 数据库配置
DATABASE_HOSTNAME = 127.0.0.1
DATABASE_DATABASE = glpoint
DATABASE_USERNAME = root
DATABASE_PASSWORD = your_password

# 微信小程序配置
WECHAT_MINI_APP_ID = wx1234567890abcdef
WECHAT_MINI_APP_SECRET = your_app_secret

# JWT 密钥（必须修改）
JWT_SECRET = your-32-characters-random-string

# API 地址
API_BASE_URL = http://localhost
```

### 3. 小程序配置

#### 方式一：本地配置（推荐开发环境）

```bash
# 进入小程序目录
cd app/mini

# 复制配置模板
cp config/config.example.js config/index.js

# 编辑配置
vim config/index.js
```

修改 `BASE_URL` 为你的后端地址：
```javascript
const BASE_URL = 'http://192.168.1.100:8000'; // 改为你的IP
```

#### 方式二：AppID 配置

创建 `app/mini/project.private.config.json`：
```json
{
  "appid": "wx1234567890abcdef"
}
```

## 配置文件说明

### 后端配置层级

```
.env (主配置，不提交)
  ↓ 读取
config/*.php (应用配置)
  ↓ 使用 env() 函数读取
  ↓
应用运行时配置
```

### 小程序配置层级

```
config/index.js (本地配置，不提交)
  ↓ 加载
utils/api.js
  ↓ 使用
页面和组件
```

## 环境变量说明

### 核心配置

| 变量名 | 说明 | 示例 | 必填 |
|--------|------|------|------|
| `DATABASE_HOSTNAME` | 数据库地址 | `127.0.0.1` | ✅ |
| `DATABASE_DATABASE` | 数据库名 | `glpoint` | ✅ |
| `DATABASE_USERNAME` | 数据库用户 | `root` | ✅ |
| `DATABASE_PASSWORD` | 数据库密码 | `password` | ✅ |
| `WECHAT_MINI_APP_ID` | 小程序AppID | `wx...` | ✅ |
| `WECHAT_MINI_APP_SECRET` | 小程序密钥 | `...` | ✅ |
| `JWT_SECRET` | JWT密钥 | 32位随机字符串 | ✅ |
| `API_BASE_URL` | API地址 | `http://localhost` | ✅ |

### 可选配置

| 变量名 | 说明 | 默认值 |
|--------|------|--------|
| `APP_DEBUG` | 调试模式 | `true` |
| `JWT_TTL` | Token过期时间 | `7200` (秒) |
| `REDIS_HOST` | Redis地址 | `127.0.0.1` |

## 不同环境配置

### 开发环境

```ini
APP_DEBUG = true
DATABASE_HOSTNAME = 127.0.0.1
API_BASE_URL = http://localhost
```

### 测试环境

```ini
APP_DEBUG = true
DATABASE_HOSTNAME = test.db.server
API_BASE_URL = https://test-api.yourdomain.com
```

### 生产环境

```ini
APP_DEBUG = false
DATABASE_HOSTNAME = prod.db.server
API_BASE_URL = https://api.yourdomain.com
JWT_SECRET = super-complex-random-string-32chars
```

## 安全检查清单

部署前确认：

- [ ] `.env` 文件已创建并填写正确
- [ ] `.env` 文件**未**提交到 Git
- [ ] `JWT_SECRET` 已修改为复杂随机字符串
- [ ] 数据库密码已修改为强密码
- [ ] `API_BASE_URL` 配置为正确的域名
- [ ] 生产环境 `APP_DEBUG = false`
- [ ] 小程序 `project.private.config.json` 已配置
- [ ] 小程序 `config/index.js` 已配置正确的 `BASE_URL`

## 常见问题

### Q: 为什么要使用 .env 文件？

A: 
- ✅ 统一管理所有环境配置
- ✅ 避免敏感信息泄露到代码仓库
- ✅ 不同环境使用不同配置，无需改代码
- ✅ 符合行业最佳实践（12 Factor App）

### Q: 小程序配置为什么不放在 .env？

A: 小程序是前端代码，打包后运行在用户设备，无法读取服务器的 .env 文件。所以：
- 小程序 AppID：放在 `project.private.config.json`（微信开发者工具配置）
- API 地址：放在 `config/index.js`（本地配置，不提交）

### Q: 如何生成安全的 JWT_SECRET？

A: 使用以下命令生成随机字符串：

```bash
# Linux/Mac
openssl rand -base64 32

# Windows PowerShell
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Minimum 0 -Maximum 256 }))

# 在线生成
# https://randomkeygen.com/
```

### Q: 团队协作时如何共享配置？

A: 
1. ❌ **不要**共享 `.env` 文件
2. ✅ 共享 `.env.example` 模板
3. ✅ 在团队文档中说明必填配置项
4. ✅ 每个开发者独立创建自己的 `.env`

### Q: 如何验证配置是否生效？

```bash
# 后端：查看应用是否能正常启动
php think run

# 检查数据库连接
php think
>>> \think\facade\Db::query('SELECT 1');

# 检查环境变量
php think
>>> env('WECHAT_MINI_APP_ID');
```

## 故障排除

### 问题：后端提示数据库连接失败

**检查**：
1. `.env` 文件是否存在
2. `DATABASE_*` 配置是否正确
3. 数据库服务是否启动
4. 防火墙是否开放端口

### 问题：小程序提示网络错误

**检查**：
1. `config/index.js` 中 `BASE_URL` 是否正确
2. 手机与电脑是否在同一网络
3. 微信开发者工具是否勾选"不校验合法域名"
4. 后端服务是否启动

### 问题：JWT 认证失败

**检查**：
1. `.env` 中 `JWT_SECRET` 是否已设置
2. 前后端的密钥是否一致
3. Token 是否过期（检查 `JWT_TTL`）

## 相关文档

- [架构文档](ARCHITECTURE.md)
- [开发指南](DEVELOPMENT.md)
- [Git 安全指南](GIT_SECURITY_GUIDE.md)

---

**维护者**: GLpoint Team  
**更新时间**: 2026-02-04
