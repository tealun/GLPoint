<?php
declare (strict_types=1);

namespace addons\ueditor;

use woo\common\abstracts\AddonsInstall;

class Install extends AddonsInstall
{
    /**
    * 安装程序
    */
    public function install()
    {
    }
    
    /**
    * 卸载程序
    */
    public function uninstall()
    {
    }
    
    /**
    * 查询配置
    */
    public function getConfig():array
    {
        return [
            "title" => "ueditor富文本",
            "author" => "WOO官方",
            "version" => "2.0.1",
            "describe" => "Ueditor的上传服务插件",
        ];
    }
}