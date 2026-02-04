# GLpoint 小程序开发配置指南

## 🚀 快速开始

### 第一步：配置后端 API 地址

```bash
# 进入小程序目录
cd app/mini

# 复制配置模板
cp config/index.example.js config/index.js

# 编辑配置文件
vim config/index.js  # 或使用其他编辑器
```

修改 `BASE_URL` 为你的后端服务器地址：
```javascript
const BASE_URL = 'http://192.168.1.100:8000'; // 改为你的后端IP和端口
```

### 第二步：配置微信开发者工具

```bash
# 复制私有配置模板
cp project.private.config.example.json project.private.config.json

# 编辑配置文件
vim project.private.config.json
```

修改 `appid` 为你的小程序 AppID：
```json
{
  "appid": "wx1234567890abcdef"  // 改为真实的 AppID
}
```

### 第三步：用微信开发者工具打开

1. 打开微信开发者工具
2. 选择"导入项目"
3. 选择 `app/mini` 目录
4. 开发者工具会自动读取 `project.config.json` 和 `project.private.config.json`

## 📁 配置文件说明

### 必须配置的文件

| 文件名 | 用途 | 是否提交Git | 如何创建 |
|--------|------|------------|----------|
| `config/index.js` | API 地址配置 | ❌ 不提交 | 从 `index.example.js` 复制 |
| `project.private.config.json` | AppID 和工具配置 | ❌ 不提交 | 从 `.example.json` 复制 |

### 模板文件（提交到Git）

| 文件名 | 说明 |
|--------|------|
| `config/index.example.js` | API 配置模板 |
| `project.private.config.example.json` | 私有配置模板 |

### 公共配置文件（提交到Git）

| 文件名 | 说明 |
|--------|------|
| `project.config.json` | 项目公共配置 |
| `app.json` | 小程序页面路由配置 |
| `sitemap.json` | 搜索优化配置 |

## 🔧 详细配置说明

### config/index.js

```javascript
/**
 * 小程序 API 地址配置
 */

const BASE_URL = 'http://localhost'; // 修改此处

module.exports = {
  BASE_URL,
  APP_NAME: 'GLpoint积分系统'
};
```

**配置选项**：

| 选项 | 说明 | 示例 |
|------|------|------|
| `BASE_URL` | 后端 API 地址 | `http://192.168.1.100:8000` |
| `APP_NAME` | 应用名称 | `GLpoint积分系统` |

**常见配置**：

```javascript
// 本地开发（手机和电脑在同一WiFi）
const BASE_URL = 'http://192.168.1.100:8000';

// 局域网测试
const BASE_URL = 'http://10.0.0.50:8000';

// 使用域名（需配置域名解析）
const BASE_URL = 'https://api.glpoint.com';
```

### project.private.config.json

```json
{
  "description": "项目私有配置文件（本地开发配置，不提交到Git）",
  "projectname": "GLpoint积分系统",
  "appid": "your_appid_here",  // ⚠️ 必须修改
  "setting": {
    "urlCheck": false,  // 本地开发关闭域名校验
    "compileHotReLoad": true  // 启用热重载
  }
}
```

**重要字段**：

| 字段 | 说明 | 示例 |
|------|------|------|
| `appid` | 微信小程序 AppID | `wx1234567890abcdef` |
| `projectname` | 项目名称 | `GLpoint积分系统` |
| `urlCheck` | 是否校验合法域名 | `false`（开发）/ `true`（生产） |

## 🌐 网络配置

### 本地开发

**前提条件**：
- 手机和电脑在同一局域网
- 后端服务已启动
- 防火墙允许访问

**步骤**：

1. 获取电脑 IP 地址：
```powershell
# Windows
ipconfig

# 查找 "IPv4 地址"，例如：192.168.1.100
```

2. 配置 `config/index.js`：
```javascript
const BASE_URL = 'http://192.168.1.100:8000';
```

3. 微信开发者工具设置：
   - 详情 → 本地设置
   - ✅ 不校验合法域名、web-view（业务域名）、TLS 版本以及 HTTPS 证书

### 远程调试

如果使用线上测试环境：

```javascript
// config/index.js
const BASE_URL = 'https://test-api.glpoint.com';
```

⚠️ **注意**：生产域名需在微信公众平台配置合法域名。

## 📱 微信开发者工具设置

### 推荐设置

**详情 → 本地设置**：
- ✅ 不校验合法域名（开发模式）
- ✅ 启用调试
- ✅ 不校验 TLS 版本
- ✅ 开启热重载

**详情 → 项目配置**：
- ES6 转 ES5：✅
- 上传代码时自动压缩：✅
- 启用代码保护：✅（生产）

## 🐛 常见问题

### Q1: 导入项目后提示 "未找到 appid"

**原因**：缺少 `project.private.config.json` 或 `appid` 未配置

**解决**：
```bash
cp project.private.config.example.json project.private.config.json
vim project.private.config.json  # 修改 appid
```

### Q2: 网络请求失败

**检查清单**：
- [ ] `config/index.js` 中 `BASE_URL` 是否正确
- [ ] 后端服务是否启动（访问 `http://BASE_URL` 验证）
- [ ] 手机和电脑是否在同一网络
- [ ] 开发者工具是否勾选"不校验合法域名"
- [ ] 防火墙是否开放端口

**测试命令**：
```bash
# 测试后端是否可访问
curl http://192.168.1.100:8000

# 或在浏览器打开
http://192.168.1.100:8000
```

### Q3: 真机调试无法连接

**解决方案**：

1. **确认网络连通**：
```bash
# 在电脑上启动后端服务
php think run -H 0.0.0.0 -p 8000

# 手机浏览器访问
http://192.168.1.100:8000
```

2. **检查防火墙**：
```powershell
# Windows 防火墙添加入站规则
netsh advfirewall firewall add rule name="PHP Server" dir=in action=allow protocol=TCP localport=8000
```

3. **使用调试工具**：
   - 开发者工具 → 调试 → Network
   - 查看请求是否发出
   - 查看响应状态码

### Q4: 修改配置后不生效

**解决方案**：
- 保存配置文件后
- 重新编译（Ctrl/Cmd + B）
- 或重启开发者工具

### Q5: Git 提示配置文件冲突

**原因**：误提交了本地配置文件

**解决**：
```bash
# 从 Git 跟踪中移除（保留本地文件）
git rm --cached app/mini/config/index.js
git rm --cached app/mini/project.private.config.json

# 确认 .gitignore 正确配置
cat .gitignore
```

## 📚 相关文档

- [环境配置指南](../../docs/ENV_CONFIG.md) - 后端环境配置
- [架构文档](../../docs/ARCHITECTURE.md) - 系统整体架构
- [开发指南](../../docs/DEVELOPMENT.md) - 开发规范

## 🔗 外部资源

- [微信小程序开发文档](https://developers.weixin.qq.com/miniprogram/dev/framework/)
- [项目配置说明](https://developers.weixin.qq.com/miniprogram/dev/devtools/projectconfig.html)
- [网络请求 API](https://developers.weixin.qq.com/miniprogram/dev/api/network/request/wx.request.html)

---

**维护者**: GLpoint Team  
**更新时间**: 2026-02-04
