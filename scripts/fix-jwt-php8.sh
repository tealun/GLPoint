#!/bin/bash
# JWT PHP8 兼容性修复脚本
# 问题根源: thans/tp-jwt-auth v1.3.1 在构造函数中使用 $this->signer
# 但类中没有声明 protected $signer 属性，导致PHP8报动态属性警告
# 解决方案: 在类声明中添加 protected $signer; 属性

TARGET_FILE="vendor/thans/tp-jwt-auth/src/provider/JWT/Lcobucci.php"
PATCH_FILE="patches/jwt-php8-fix.patch"

# 检查目标文件是否存在
if [ ! -f "$TARGET_FILE" ]; then
    echo "⚠️  目标文件不存在: $TARGET_FILE"
    exit 0
fi

# 检查是否已经修复
if grep -q "protected \$signer;" "$TARGET_FILE" 2>/dev/null; then
    echo "✅ JWT PHP8兼容性已修复"
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
else
    # patch失败，尝试手动修复
    echo "⚠️  patch命令失败，尝试手动修复..."
    # 在 class Lcobucci extends Provider 之后的第一个 { 后插入
    sed -i '/^class Lcobucci extends Provider$/,/^{$/{
        /^{$/a\    protected $signer;
    }' "$TARGET_FILE"
    
    if grep -q "protected \$signer;" "$TARGET_FILE"; then
        echo "✅ 手动修复成功"
    else
        echo "❌ 修复失败，请手动编辑: $TARGET_FILE"
        echo "   在 'class Lcobucci extends Provider {' 后添加: protected \$signer;"
        exit 1
    fi
fi
