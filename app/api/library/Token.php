<?php
declare (strict_types = 1);
namespace app\api\library;

use think\facade\Cache;
use think\facade\Log;

class Token 
{
    /**
     * 生成访问令牌
     */
    public static function generateAccessToken(string $type, array $data): string 
    {
        $token = md5(uniqid($type, true));
        Cache::set("token_{$type}_{$token}", $data, 7200); // 2小时过期
        return $token;
    }

    /**
     * 验证访问令牌
     */
    public static function verifyAccessToken(string $type, string $token): ?array
    {
        $key = "token_{$type}_{$token}";
        $data = Cache::get($key);
        return $data ?: null;
    }

    /**
     * 删除访问令牌
     */
    public static function removeAccessToken(string $type, string $token): bool
    {
        return Cache::delete("token_{$type}_{$token}");
    }
}
