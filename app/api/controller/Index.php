<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\controller\Api;
use woo\common\annotation\{Controller,ApiInfo,Param,Header,Returns};

/**
 * @Controller("首页",module="首页",desc="控制器作用描述")
 */
class Index extends Api
{
    /**
     * @ApiInfo(value="获取首页数据",method="GET",login=false)
     */
    public function index()
    {               
        return $this->ajax('success', '[api]应用请求成功');
    }
}