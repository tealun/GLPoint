<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\App;
use think\facade\Config;
use woo\common\View;
use think\facade\Env;
use think\Exception;

class Error
{
    /**
     * Request实例
     * @var \think\Request
     */
    public $request;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    public $params = [];
    /**
     * 系统View对象
     */
    public $assign;



    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        array_unshift($this->middleware, \woo\common\middleware\AuthCheck::class);
        $this->assign = app(View::class);
    }

    public function __call($name, $args)
    {
        $this->params = $this->request->getParams();
        $controller = sprintf(
            "woo\\%s\\controller\\%s",
            $this->params['app_name'],
            empty($this->params['addon_name']) ? $this->params['controller']
                : $this->params['addon_name'] . "\\" . $this->params['controller']
        );
        // 可以实现无控制器也可以继续访问 但不建议
//        if (!class_exists($controller)) {
//            $controller = \woo\common\controller\AdminController::class;
//        }
        if (class_exists($controller)) {
            $object = new $controller($this->app);
            if (method_exists($object, $this->params['action'])) {
                return $object->{$this->params['action']}();
            } else {
                if (!Env::get('APP_DEBUG')) {
                    return $this->fetch(Config::get('app.http_exception_template')[404]);
                } else {
                    throw new Exception('method not exists:' . str_replace('woo\\', 'app\\', $controller) . '->' . $this->params['action'] . '()');
                }
            }
        } else {
            if (!Env::get('app_debug')) {
                return $this->fetch(Config::get('app.http_exception_template')[404]);
            } else {
                throw new Exception('controller not exists:' . str_replace('woo\\', 'app\\', $controller));
            }
        }
    }

    protected function fetch(string $tempate = '', array $vars = [])
    {
        $this->assign->fetch($tempate, $vars);
    }
}
