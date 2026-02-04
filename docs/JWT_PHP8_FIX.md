# JWT PHP8 兼容性修复说明

## 问题根源

第三方库 `thans/tp-jwt-auth` v1.3.1 存在PHP8兼容性问题，发现**三处**动态属性创建：

### 问题1: Lcobucci.php

**原始代码**（GitHub: QThans/jwt-auth@ab5efcc）：
```php
class Lcobucci extends Provider
{
    protected $signers = [...];  // ✅ 已声明
    protected $builder;          // ✅ 已声明
    protected $parser;           // ✅ 已声明
    // ❌ 缺少 $signer 声明

    public function __construct(Builder $builder, Parser $parser, $algo, $keys)
    {
        $this->signer  = $this->getSign();  // ❌ 动态创建属性
    }
}
```

**PHP8 警告**：
```
Creation of dynamic property thans\jwt\provider\JWT\Lcobucci::$signer is deprecated
```

### 问题2: Factory.php

**原始代码**（GitHub: QThans/jwt-auth@v1.3.1）：
```php
class Factory
{
    protected $classMap = [...];  // ✅ 已声明
    protected $ttl;               // ✅ 已声明
    protected $claim = [];        // ✅ 已声明
    protected $refreshTtl;        // ✅ 已声明
    // ❌ 缺少 $request 声明

    public function __construct(Request $request, $ttl, $refreshTtl)
    {
        $this->request    = $request;  // ❌ 动态创建属性
        $this->ttl        = $ttl;
        $this->refreshTtl = $refreshTtl;
    }
}
```

**PHP8 警告**：
```
Creation of dynamic property thans\jwt\claim\Factory::$request is deprecated
```

### 问题3: Manager.php

**原始代码**（GitHub: QThans/jwt-auth@v1.3.1）：
```php
class Manager
{
    protected $blacklist;    // ✅ 已声明
    protected $payload;      // ✅ 已声明
    protected $refresh;      // ✅ 已声明
    protected $validate = true;  // ✅ 已声明
    // ❌ 缺少 $provider 声明

    public function __construct(
        Blacklist $blacklist,
        Payload $payload,
        Provider $provider
    ) {
        $this->blacklist = $blacklist;
        $this->payload   = $payload;
        $this->provider  = $provider;  // ❌ 动态创建属性
    }
}
```

**PHP8 警告**：
```
Creation of dynamic property thans\jwt\Manager::$provider is deprecated
```

## 修复方案

在三个类的声明中分别添加缺失的属性：

**Lcobucci.php:**
```php
class Lcobucci extends Provider
{
    protected $signer;   // ✅ 新增声明
    protected $signers = [...];
}
```

**Factory.php:**
```php
class Factory
{
    protected $request;  // ✅ 新增声明
    protected $classMap = [...];
}
```

**Manager.php:**
```php
class Manager
{
    protected $blacklist;
    protected $payload;
    
    protected $provider;  // ✅ 新增声明
    
    protected $refresh;
}
```

## 自动化修复

### 方式1：Composer自动修复（推荐）

每次 `composer install` 或 `composer update` 后自动应用修复：

```bash
# Linux/Mac
composer install

# Windows
composer install
```

脚本会自动检测操作系统并执行相应的修复脚本。

### 方式2：手动执行脚本

**Linux/Mac：**
```bash
bash scripts/fix-jwt-php8.sh
```

**Windows PowerShell：**
```powershell
powershell -ExecutionPolicy Bypass -File scripts/fix-jwt-php8.ps1
```

### 方式3：应用patch文件

```bash
patch -p0 < patches/jwt-php8-fix.patch
```

### 方式4：手动编辑

如果自动化脚本失败，手动编辑：
```
vendor/thans/tp-jwt-auth/src/provider/JWT/Lcobucci.php
```

在 `class Lcobucci extends Provider {` 后添加：
```php
    protected $signer;
```

## 验证修复

检查文件是否包含属性声明：

**Linux/Mac：**
```bash
grep "protected \$signer;" vendor/thans/tp-jwt-auth/src/provider/JWT/Lcobucci.php
grep "protected \$request;" vendor/thans/tp-jwt-auth/src/claim/Factory.php
grep "protected \$provider;" vendor/thans/tp-jwt-auth/src/Manager.php
```

**Windows PowerShell：**
```powershell
Select-String "protected \`$signer;" vendor\thans\tp-jwt-auth\src\provider\JWT\Lcobucci.php
Select-String "protected \`$request;" vendor\thans\tp-jwt-auth\src\claim\Factory.php
Select-String "protected \`$provider;" vendor\thans\tp-jwt-auth\src\Manager.php
```

## 部署环境

自动部署流程已集成此修复，GitHub Actions会在每次部署时自动应用。

## 技术细节

- **受影响版本**: thans/tp-jwt-auth v1.3.1 及更早版本
- **PHP版本**: PHP 8.0+
- **修复位置**: 
  - vendor/thans/tp-jwt-auth/src/provider/JWT/Lcobucci.php 第26行
  - vendor/thans/tp-jwt-auth/src/claim/Factory.php 第10行
  - vendor/thans/tp-jwt-auth/src/Manager.php 第15行
- **根本原因**: 构造函数中动态赋值 `$this->signer`、`$this->request` 和 `$this->provider` 但类中未声明这些属性
- **影响**: PHP8弃用动态属性，未声明的属性赋值会触发 Deprecated 警告

## 相关文件

- `patches/jwt-php8-fix.patch` - patch补丁文件
- `scripts/fix-jwt-php8.sh` - Linux/Mac自动修复脚本
- `scripts/fix-jwt-php8.ps1` - Windows自动修复脚本
- `composer.json` - 已配置自动修复钩子
- `.github/workflows/deploy.yml` - 部署流程已集成修复
