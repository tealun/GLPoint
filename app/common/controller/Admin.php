<?php
declare (strict_types = 1);

namespace app\common\controller;

use woo\common\controller\AdminController;

/**
 * 开发者自己的后台应用控制器基类
 * 用于定义你自己的后台应用公共方法
 * 后台控制器都继承它或子类
 * 尽可能的不要改变原有的继承关系
 */
abstract class Admin extends AdminController
{

}