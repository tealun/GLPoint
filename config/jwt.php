<?php


return [
    // JWT 密钥 - 使用复杂的随机字符串
    'secret' => env('JWT_SECRET', 'p8eK#mN9q@vR4$xL7wS2^hA5tF3*yU6cB'),  
    
    // token 过期时间
    'ttl' => env('JWT_TTL', 7200),                            
    
    // 签发者
    'iss' => env('JWT_ISS', 'api.yuanyin.com'),              
    // 接收者
    'aud' => env('JWT_AUD', 'yuanyin.com'),                  

    // 黑名单存储
    'blacklist_storage' => thans\jwt\provider\storage\Tp6::class,      

    // 刷新Token有效期(分钟)
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),         
    
    // 黑名单开关
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true), 
    
    // 黑名单宽限期
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),  
    
    // 签名算法
    'signer' => env('JWT_SIGNER', 'HS256'),                  
    
    // Token 前缀
    'prefix' => 'Bearer ',
];
