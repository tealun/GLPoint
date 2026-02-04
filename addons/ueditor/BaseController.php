<?php
declare (strict_types=1);

namespace addons\ueditor;

use woo\common\addons\Controller;
/**
 * 插件控制器基类 其他控制器都继承它
 */
class BaseController extends Controller
{
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];
    
    /**
     * 初始化方法
     */
    protected function initialize()
    {
        // 你的代码...
        parent::{__FUNCTION__}();
        // 你的代码...
    }
}