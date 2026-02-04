<?php

declare(strict_types=1);

namespace app\admin\controller;

use woo\common\annotation\Ps;

class Setting extends \woo\admin\controller\Setting
{
    /**
     * @Ps(true,as="set",name="支付设置")
     */
    protected function paySetting()
    {
        // 复制该方法，做很多系统设置的方法，满足系统设置多菜单、多场景、多权限的业务需求
        // 1、复制以后把protected改public，不然请求不到
        // 2、如果要单独授权 就把注解改为 @Ps(true,name="支付设置") 不要as即可；加了as就和默认的系统设置权限一致，无需单独授权

        $this->local['group_var'] = ['admin'];// 配置好当前设置的"组变量"（系统设置组中自行查看）列表

        $this->local['header_title'] = '支付配置';// 当前组的标题

        return parent::set();// 调用即可
    }
}
