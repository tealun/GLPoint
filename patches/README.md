# 第三方库兼容性修复

## 概述

本目录包含对第三方依赖包兼容性问题的自动化修复方案。

## JWT PHP8 兼容性修复

### 问题描述

**受影响组件**: `thans/tp-jwt-auth` v1.3.1  
**问题根源**: 原始代码在构造函数中动态创建 `$signer` 属性，但未在类中声明  
**PHP版本**: PHP 8.0+  
**错误信息**:
```
Deprecated: Creation of dynamic property thans\jwt\provider\JWT\Lcobucci::$signer is deprecated
```

### 根本原因分析

查看第三方库原始代码（GitHub: QThans/jwt-auth@ab5efcc）：

```php
class Lcobucci extends Provider
{
    // ❌ 缺少 $signer 属性声明
    protected $signers = [...];  // ✅ 已声明
    protected $builder;          // ✅ 已声明
    protected $parser;           // ✅ 已声明

    public function __construct(Builder $builder, Parser $parser, $algo, $keys)
    {
        $this->builder = $builder;
        $this->parser  = $parser;
        $this->algo    = $algo;
        $this->keys    = $keys;
        $this->signer  = $this->getSign();  // ❌ 动态创建属性（PHP8 Deprecated）
    }
}
```

PHP8 开始弃用动态属性（Dynamic Properties），所有类属性必须显式声明。

### 修复方案

在类声明中添加 `protected $signer;` 属性：

```php
class Lcobucci extends Provider
{
    protected $signer;   // ✅ 新增属性声明
    protected $signers = [...];
    protected $builder;
    protected $parser;
```

### 自动化实现

#### 1. Composer 自动修复（推荐）

在 `composer.json` 中配置：

```json
{
  "scripts": {
    "post-install-cmd": ["自动检测并修复"],
    "post-update-cmd": ["自动检测并修复"]
  }
}
```

每次 `composer install` 或 `composer update` 后自动执行修复。

#### 2. 修复脚本

**Linux/Mac**: `scripts/fix-jwt-php8.sh`
- 尝试应用 patch 文件
- 失败则使用 sed 手动修复
- 验证修复结果

**Windows**: `scripts/fix-jwt-php8.ps1`
- PowerShell 脚本
- 解析文件并插入属性声明
- UTF-8 编码兼容

#### 3. Patch 文件

`patches/jwt-php8-fix.patch` - 标准 unified diff 格式补丁。

#### 4. CI/CD 集成

GitHub Actions 部署流程自动执行修复：

```yaml
- name: 应用JWT PHP8兼容性修复
  run: bash scripts/fix-jwt-php8.sh
```

### 使用方法

**自动修复（推荐）**:
```bash
composer install  # 自动触发修复
```

**手动修复（开发环境）**:
```bash
# Linux/Mac
bash scripts/fix-jwt-php8.sh

# Windows
powershell -ExecutionPolicy Bypass -File scripts\fix-jwt-php8.ps1
```

**验证修复**:
```bash
# Linux/Mac
grep "protected \$signer;" vendor/thans/tp-jwt-auth/src/provider/JWT/Lcobucci.php

# Windows
Select-String "protected \`$signer;" vendor\thans\tp-jwt-auth\src\provider\JWT\Lcobucci.php
```

### 文件清单

```
patches/
  └── jwt-php8-fix.patch           # 补丁文件

scripts/
  ├── fix-jwt-php8.sh              # Linux/Mac 修复脚本
  └── fix-jwt-php8.ps1             # Windows 修复脚本

docs/
  └── JWT_PHP8_FIX.md              # 详细技术文档

composer.json                      # 自动修复配置
.github/workflows/deploy.yml      # CI/CD 集成
```

### 技术细节

- **检测逻辑**: 检查 `vendor/thans/tp-jwt-auth/src/provider/JWT/Lcobucci.php` 是否存在 `protected $signer;` 声明
- **幂等性**: 多次执行安全，已修复则跳过
- **错误处理**: 修复失败时提供清晰的手动操作指南
- **跨平台**: 支持 Linux/Mac/Windows
- **编码兼容**: UTF-8 编码，避免乱码

### 为什么不升级库？

1. **官方未修复**: thans/tp-jwt-auth v1.3.1 是最新稳定版本，官方未发布PHP8兼容版本
2. **依赖锁定**: 项目使用 composer.lock 锁定版本，升级可能引入不兼容变更
3. **最小改动**: 仅添加一行属性声明，影响最小，风险可控

### 长期方案

监控官方仓库更新：
- GitHub: https://github.com/QThans/jwt-auth
- 发现新版本后评估升级可行性
- 升级后可移除此修复方案

## 其他兼容性修复

可在此目录添加其他第三方库兼容性修复方案。

### 命名规范

- Patch文件: `patches/<package>-<issue>.patch`
- Linux脚本: `scripts/fix-<package>-<issue>.sh`
- Windows脚本: `scripts/fix-<package>-<issue>.ps1`
- 文档: `docs/<PACKAGE>_<ISSUE>.md`

## 贡献指南

添加新的兼容性修复时：

1. 深入分析问题根源（不要武断认为是第三方问题）
2. 创建 patch 文件和修复脚本
3. 编写详细的技术文档
4. 更新 composer.json 自动修复配置
5. 在 CI/CD 流程中集成
6. 提供验证方法
