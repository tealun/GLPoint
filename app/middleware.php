<?php
// 全局中间件定义文件
return [
    // 全局请求缓存
    // \think\middleware\CheckRequestCache::class,
    // 多语言加载
    // \think\middleware\LoadLangPack::class,
    // Session初始化
    \think\middleware\SessionInit::class,

    \woo\common\middleware\Before::class,
    //系统View输出 建议放最后一个
    \woo\common\middleware\After::class,

    // API认证中间件
    \app\api\middleware\ApiAuth::class,
];
