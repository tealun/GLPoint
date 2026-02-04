<?php
namespace app\api;

use woo\common\abstracts\AppInstall;

class Install extends AppInstall
{
    public function install()
    {
    }
    public function uninstall()
    {
    }
    public function getConfig():array
    {
        return [
            "title" => "api",
            "author" => "System",
            "version" => "0.1",
            "describe" => "系统API应用",
        ];
    }
}