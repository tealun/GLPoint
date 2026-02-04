<?php
declare (strict_types = 1);

namespace woo\common\addons;

abstract class Addons
{
    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();
}