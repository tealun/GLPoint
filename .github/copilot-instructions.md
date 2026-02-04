# GLpoint AI 编码指南

> 企业员工积分管理系统 | PHP 7.2+ | ThinkPHP 6.0 | wooAdmin 2.3.4 | 微信小程序

**项目仓库**：
- 私密仓库：https://github.com/tealun/GLPoint.git

**开发文档**：架构和指南在 `docs/` 目录下（必读）

## 核心模式（从实际代码中发现）

### 1. 严格分层架构
```php
// 每层职责清晰，禁止跨层调用
微信小程序 (app/mini)
  ↓ HTTP请求
API控制器 (app/api/controller)
  ↓ 调用服务
公共服务层 (app/common/service)
  ↓ 调用模型
模型层 (app/common/model)
  ↓ ORM操作
数据库层 (MySQL)

// 实例：所有API控制器必须继承基类并使用JWT认证
use app\common\controller\Api;
use app\api\library\Auth;

class Points extends Api
{
    public function list() {
        // 验证登录
        if (!Auth::checkLogin()) {
            return $this->error('请先登录');
        }
        
        // 获取用户ID
        $user_id = Auth::getUserIdFromToken();
        
        // 调用模型查询（必须过滤user_id）
        $records = (new UserScore())
            ->where('user_id', $user_id)
            ->where('status', 1)
            ->select();
            
        return $this->success('成功', $records);
    }
}
```

### 2. 注解驱动开发（wooAdmin特性）
```php
// 在 app/api/controller 中发现的注解框架
// 所有控制器必须使用注解声明

// ✅ 正确：使用注解定义API信息
/**
 * @Controller("积分管理",module="积分",desc="积分相关接口")
 */
class Points extends Api
{
    /**
     * @ApiInfo(value="获取积分列表",method="POST",login=true)
     * @Param(name="type", type="string", require=false, desc="类型")
     * @Returns(name="list", type="array", desc="积分列表")
     */
    public function list() {
        // 实现逻辑
    }
}

// ❌ 错误：缺少注解
class Points extends Api
{
    public function list() { }  // 缺少API文档
}
```

### 3. 统一返回格式
```php
// 在 app/common/controller/Api.php 中发现的统一返回模式
// 所有API必须使用 success() 和 error() 方法

// ✅ 正确：使用统一返回
return $this->success('操作成功', $data);
return $this->error('操作失败', [], 400);

// ❌ 错误：自定义返回格式
return json(['code' => 0, 'msg' => '成功']);  // 不一致
```

### 4. 配置层级化获取
```php
// config.php 中发现的配置获取模式
// 配置文件存放在 /config 目录

use think\facade\Config;

// 获取微信配置（注意：实际代码中使用'appid'键名，但配置文件定义为'app_id'）
$config = Config::get('wechat.mini');
$appid = $config['appid'] ?? '';  // 实际代码使用方式
$secret = $config['secret'] ?? '';

// 获取数据库配置
$dbConfig = Config::get('database.connections.mysql');
```

### 5. 模型关联查询
```php
// 在 app/common/model 中发现的关联查询模式
// 使用ThinkPHP ORM的关联功能

class UserScore extends Model
{
    // 关联用户
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // 关联积分规则
    public function scoreRule()
    {
        return $this->belongsTo(ScoreRule::class, 'rule_id');
    }
}

// 使用关联查询
$records = (new UserScore())
    ->with(['user', 'scoreRule'])
    ->where('status', 1)
    ->select();
```

## 关键约定

### 文件命名（从项目结构发现）
- Controller: `*Controller.php` 或直接类名（如 Points.php, User.php）
- Model: 大驼峰命名（如 UserScore.php, ScoreRule.php）
- Service: `*Service.php`（如 Wechat.php, AuthService.php）
- 配置文件: 小写+下划线（如 database.php, wechat.php）

### 错误处理模式
```php
// 在所有 Controller 和 Service 中发现的统一模式
use think\facade\Log;

try {
    $result = $model->save($data);
    Log::info('操作成功: ' . json_encode($result));
    return $this->success('操作成功', $result);
} catch (\Exception $e) {
    Log::error('操作失败: ' . $e->getMessage(), [
        'trace' => $e->getTraceAsString()
    ]);
    return $this->error('操作失败');
}
```

### 用户隔离设计
```php
// 当前：多用户企业系统（必须做数据隔离）
// 所有查询必须过滤 user_id

// ✅ 正确：带用户过滤
$records = (new UserScore())
    ->where('user_id', $user_id)
    ->select();

// ❌ 错误：未过滤用户
$records = (new UserScore())->select();  // 会查到所有用户数据
```

### JWT认证模式
```php
// 在 app/api/library/Auth.php 中的认证模式
use app\api\library\Auth;

// 检查登录状态
if (!Auth::checkLogin()) {
    return $this->error('请先登录');
}

// 获取当前用户ID
$user_id = Auth::getUserIdFromToken();

// 注意：Auth类只提供getUserIdFromToken()方法
// 获取完整用户信息需通过User控制器的getUser()方法
```

## 开发工作流（必须遵循）

1. **读文档** → `docs/ARCHITECTURE.md`, `docs/DEVELOPMENT.md`
2. **全局搜索** → 找已有工具/模式，优先复用
3. **提出方案** → 分析合理性 → **等待确认**
4. **实施开发** → 分层设计 → 复用框架 → 配置化
5. **深度检查** → 语法错误 → 调用关系 → 删除冗余
6. **Git提交** → 描述清晰 → 不要push

## 参考关键文件

### 后端核心
- `app/common/controller/Api.php` - API控制器基类
- `app/api/library/Auth.php` - JWT认证实现
- `app/common/model/UserScore.php` - 积分记录模型
- `woo/common/controller/Controller.php` - wooAdmin控制器基类
- `config/database.php` - 数据库配置
- `config/jwt.php` - JWT配置

### 前端核心
- `app/mini/utils/api.js` - API地址定义
- `app/mini/utils/request.js` - 请求封装
- `app/mini/app.json` - 小程序配置

### 数据库设计
- `data/database.sql` - 主数据库结构
- `data/point.sql` - 积分系统表结构
- `data/region.sql` - 地区数据

### 开发文档
- `docs/ARCHITECTURE.md` - 完整架构文档
- `docs/DEVELOPMENT.md` - 开发指南和规范

