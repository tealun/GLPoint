#!/bin/bash
# JWT PHP8 兼容性修复脚本
# 问题根源: thans/tp-jwt-auth v1.3.1 存在两个动态属性问题
# 1. provider/JWT/Lcobucci.php: 缺少 protected $signer;
# 2. claim/Factory.php: 缺少 protected $request;
# 解决方案: 在类声明中添加对应的属性声明

TARGET_FILE1="vendor/thans/tp-jwt-auth/src/provider/JWT/Lcobucci.php"
TARGET_FILE2="vendor/thans/tp-jwt-auth/src/claim/Factory.php"
PATCH_FILE="patches/jwt-php8-fix.patch"

# 检查目标文件是否存在
if [ ! -f "$TARGET_FILE1" ] && [ ! -f "$TARGET_FILE2" ]; then
    echo "⚠️  目标文件不存在，跳过修复"
    exit 0
fi

# 检查是否已经修复
FIXED1=false
FIXED2=false

if [ -f "$TARGET_FILE1" ]; then
    if grep -q "protected \$signer;" "$TARGET_FILE1" 2>/dev/null; then
        FIXED1=true
    fi
fi

if [ -f "$TARGET_FILE2" ]; then
    if grep -q "protected \$request;" "$TARGET_FILE2" 2>/dev/null; then
        FIXED2=true
    fi
fi

if [ "$FIXED1" = true ] && [ "$FIXED2" = true ]; then
    echo "✅ JWT PHP8兼容性已全部修复"
    exit 0
fi

# 检查补丁文件
if [ ! -f "$PATCH_FILE" ]; then
    echo "❌ 补丁文件不存在: $PATCH_FILE"
    exit 1
fi

# 应用补丁
echo "🔧 正在修复JWT PHP8兼容性..."
if patch -p0 -N < "$PATCH_FILE" > /dev/null 2>&1; then
    echo "✅ JWT PHP8兼容性修复成功"
    exit 0
fi

# patch失败，尝试手动修复
echo "⚠️  patch命令失败，尝试手动修复..."

# 修复Lcobucci.php
if [ -f "$TARGET_FILE1" ] && [ "$FIXED1" = false ]; then
    sed -i '/^class Lcobucci extends Provider$/,/^{$/{
        /^{$/a\    protected $signer;
    }' "$TARGET_FILE1"
    
    if grep -q "protected \$signer;" "$TARGET_FILE1"; then
        echo "✅ Lcobucci.php 修复成功"
    else
        echo "❌ Lcobucci.php 修复失败"
    fi
fi

# 修复Factory.php
if [ -f "$TARGET_FILE2" ] && [ "$FIXED2" = false ]; then
    sed -i '/^class Factory$/,/^{$/{
        /^{$/a\    protected $request;
    }' "$TARGET_FILE2"
    
    if grep -q "protected \$request;" "$TARGET_FILE2"; then
        echo "✅ Factory.php 修复成功"
    else
        echo "❌ Factory.php 修复失败"
    fi
fi
