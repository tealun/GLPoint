<?php

return [
    "default"          => "local",//默认上传方式
    "drivers"  => [
        "local" => [
            "driver"   => "Local",//引擎类 如果自定义或者修改 就写你自己的绝对命名空间
            "domain"   => request()->root(),// 访问域名
        ],
        "qiniu" => [
            "driver"   => "Qiniu",// 不需要自行扩展不要修改
            "domain"   => "",//访问域名 /结尾
            "ak"       => "",//accessKey
            "sk"       => "",//secretKey
            "bucket"   => ""//所在空间名
        ],
        "oss"   => [
            "driver"   => "Oss",// 不需要自行扩展不要修改
            "domain"   => "",// 外网访问域名 /结尾
            'endpoint' => '',//endpoint
            "ak"       => "",// accessKeyId
            "sk"       => "", //AccessKeySecret
            "bucket"   => ""//存储空间
        ],
        "cos"   => [
            "driver"   => "Cos",// 不需要自行扩展不要修改
            "domain"   => "", //外网访问域名 /结尾
            "ak"       => "",//"云 API 密钥 SecretId"
            "sk"       => "",//"云 API 密钥 SecretKey"
            "region"   => "",//存储桶地域
            "bucket"   => "",// 存储桶名称 格式：BucketName-APPID
            "schema"   => "http"  // 协议  默认http  https 需要自行处理
        ],
        // ... 自定义
    ]
];