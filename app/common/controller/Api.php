<?php
declare (strict_types = 1);

namespace app\common\controller;

use woo\common\controller\Controller;
use woo\common\controller\traits\ApiCommon;

/**
 * 开发者自己的api应用控制器基类
 * 具体应用控制器都继承它或子类
 */
abstract class Api extends Controller
{
    use ApiCommon;

}