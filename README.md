# GLpoint - 企业员工积分管理系统

> 基于 ThinkPHP 6.0 + wooAdmin 2.3.4 + 微信小程序

[![PHP Version](https://img.shields.io/badge/PHP-7.2+-blue.svg)](https://www.php.net)
[![ThinkPHP](https://img.shields.io/badge/ThinkPHP-6.0-green.svg)](https://www.thinkphp.cn)
[![License](https://img.shields.io/badge/license-Apache%202-blue.svg)](LICENSE.txt)

## 项目简介

GLpoint 是一个企业员工积分管理系统，通过积分机制实现员工激励管理。系统支持积分发放、申诉处理、排行榜展示、部门管理等完整业务流程。

### 核心功能

- 🎯 **积分管理**：积分奖励、扣除、记录查询
- 🏆 **排行榜**：周榜、月榜、年榜、总榜
- 📝 **申诉处理**：积分申诉提交与审核
- 🏢 **部门管理**：无限级部门结构
- 👥 **用户管理**：微信用户、用户等级
- 📊 **数据分析**：Dashboard图表统计
- 🔐 **权限系统**：RBAC权限控制

## 技术栈

### 后端
- **框架**：ThinkPHP 6.0 + wooAdmin 2.3.4
- **语言**：PHP 7.2+
- **数据库**：MySQL 5.7+
- **认证**：JWT (API端) + Session (管理端)
- **依赖管理**：Composer

### 前端
- **小程序**：微信原生小程序
- **语言**：JavaScript (ES6+)

## 快速开始

### 环境要求

- PHP >= 7.2
- MySQL >= 5.7
- Composer
- Nginx/Apache
- 微信开发者工具

### 安装步骤

```bash
# 1. 克隆项目
git clone https://github.com/tealun/GLPoint.git
cd GLpoint

# 2. 安装依赖
composer install

# 3. 配置环境
cp .env.example .env
vim .env  # 修改数据库配置

# 4. 导入数据库
mysql -u root -p glpoint < data/database.sql
mysql -u root -p glpoint < data/point.sql
mysql -u root -p glpoint < data/region.sql

# 5. 设置权限
chmod -R 755 runtime/
chmod -R 755 public/uploads/

# 6. 启动服务
php think run  # 开发环境
# 或配置Nginx/Apache
```

### 访问系统

- **后台管理**：http://localhost/admin
- **API接口**：http://localhost/api
- **微信小程序**：使用微信开发者工具导入 `app/mini` 目录

## 文档

- [系统架构文档](docs/ARCHITECTURE.md) - 完整的架构设计说明
- [开发指南](docs/DEVELOPMENT.md) - 开发环境搭建、编码规范
- [AI编码指南](.github/copilot-instructions.md) - AI辅助开发规范

## 目录结构

```
GLpoint/
├── app/                    # 应用目录
│   ├── api/               # API接口
│   ├── admin/             # 后台管理
│   ├── common/            # 公共模块
│   └── mini/              # 微信小程序
├── config/                # 配置文件
├── data/                  # 数据文件
├── docs/                  # 文档
├── public/                # Web根目录
├── runtime/               # 运行时文件
├── vendor/                # Composer依赖
├── woo/                   # wooAdmin框架
└── composer.json          # Composer配置
```

## 开发规范

- 遵循 PSR-12 编码规范
- 使用 ThinkPHP 6.0 ORM
- API遵循 RESTful 设计
- Git提交遵循 Conventional Commits

详见 [开发指南](docs/DEVELOPMENT.md)

## 许可证

本项目基于 Apache 2.0 许可证开源。

ThinkPHP遵循Apache2开源协议发布，并提供免费使用。

版权所有 Copyright © 2024-2026

ThinkPHP® 商标和著作权所有者为上海顶想信息科技有限公司。

更多细节参阅 [LICENSE.txt](LICENSE.txt)
