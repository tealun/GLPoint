<?php
declare (strict_types = 1);

namespace woo\common\addons;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use think\helper\Str;
use think\facade\Event;
use think\Response;
use think\exception\HttpException;
use think\exception\ClassNotFoundException;

class Route
{
    protected $app;
    protected $request;
    protected $addon;
    protected $controller;
    protected $action;

    public function __construct()
    {
        $this->app = app();
        $this->request = $this->app->request;
    }

    /**
     * 返回插件目录
     * @return string
     */
    protected function getAddonPath()
    {
        return $this->app->addons->getAddonsPath() . $this->addon . DIRECTORY_SEPARATOR;
    }

    /**
     * 设置插件
     * @param string $addon
     */
    protected function setAddon($addon = '')
    {
        $this->addon = $addon ?? $this->app->config->get('addons.default_addon');
        if (empty($this->addon) || !is_dir($this->getAddonPath())) {
            throw new HttpException(500, __('addon %s not found', $this->addon));
        }
        $this->request->addon = $addon;
        $this->request->isAddon = true;
        $this->app->setRuntimePath($this->app->getRuntimePath() . 'addons' . DIRECTORY_SEPARATOR . $this->addon . DIRECTORY_SEPARATOR);
        $this->loadAddon($this->addon, $this->getAddonPath());
    }

    /**
     * 加载插件
     * @param $addon
     * @param $addonPath
     */
    protected function loadAddon($addon, $addonPath)
    {
        // 加载插件自己的公共函数库
        if (is_file($addonPath . 'common.php')) {
            include_once $addonPath . 'common.php';
        }

        // 加载插件自己的配置文件
        $files = [];
        $files = array_merge($files, glob($addonPath . 'config' . DIRECTORY_SEPARATOR . '*' . $this->app->getConfigExt()));
        foreach ($files as $file) {
            $this->app->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }

        // 加载插件事件注册文件
        if (is_file($addonPath . 'event.php')) {
            $this->app->loadEvent(include $addonPath . 'event.php');
        }

        // 加载插件中间件注册文件
        if (is_file($addonPath . 'middleware.php')) {
            $this->app->middleware->import(include $addonPath . 'middleware.php', 'controller');
        }

        // 加载插件语言包
        $langset = $this->app->lang->defaultLangSet();
        if (!empty($langset)) {
            $files = glob($addonPath . 'lang' . DIRECTORY_SEPARATOR . $langset . '.*');
            $this->app->lang->load($files);
        }
    }

    /**
     * 设置控制器
     * @param $controller
     */
    protected function setController($controller)
    {
        $this->controller = $controller ?? $this->app->config->get('route.default_controller');
        if (empty($this->controller)) {
            throw new HttpException(404, __('addon controller %s not found', $this->controller));
        }
        $this->request->setController($this->controller);
    }

    /**
     * 设置方法
     * @param $action
     */
    protected function setAction($action)
    {
        $this->action = $action ?? $this->app->config->get('route.default_action');
        if (empty($this->controller)) {
            throw new HttpException(404, __('addon action %s not found', $this->action));
        }
        $this->request->setAction($this->action);
    }

    /**
     * 获取控制器实例
     * @param string $name
     * @return mixed|object
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function controller(string $name)
    {
        $emptyController = $this->app->config->get('route.empty_controller') ?: 'Error';

        $class = get_addons_class($this->addon, 'controller', $this->controller);

        if (class_exists($class)) {
            return $this->app->make($class, [], true);
        } elseif ($emptyController && class_exists($emptyClass = get_addons_class($this->addon, 'controller', $emptyController))) {
            return $this->app->make($emptyClass, [], true);
        }
        throw new ClassNotFoundException('class not exists:' . $class, $class);
    }

    /**
     * 使用反射机制注册控制器中间件
     * @access public
     * @param object $controller 控制器实例
     * @return void
     */
    protected function registerControllerMiddleware($controller): void
    {
        $class = new ReflectionClass($controller);

        if ($class->hasProperty('middleware')) {
            $reflectionProperty = $class->getProperty('middleware');
            $reflectionProperty->setAccessible(true);

            $middlewares = $reflectionProperty->getValue($controller);

            foreach ($middlewares as $key => $val) {
                if (!is_int($key)) {
                    if (isset($val['only']) && !in_array($this->request->action(true), array_map(function ($item) {
                            return strtolower($item);
                        }, is_string($val['only']) ? explode(",", $val['only']) : $val['only']))) {
                        continue;
                    } elseif (isset($val['except']) && in_array($this->request->action(true), array_map(function ($item) {
                            return strtolower($item);
                        }, is_string($val['except']) ? explode(',', $val['except']) : $val['except']))) {
                        continue;
                    } else {
                        $val = $key;
                    }
                }

                if (is_string($val) && strpos($val, ':')) {
                    $val = explode(':', $val);
                    if (count($val) > 1) {
                        $val = [$val[0], array_slice($val, 1)];
                    }
                }
                $this->app->middleware->controller($val);
            }
        }
    }

    protected function autoResponse($data): Response
    {
        if ($data instanceof Response) {
            $response = $data;
        } elseif (!is_null($data)) {
            // 默认自动识别响应输出类型
            $type     = $this->request->isJson() ? 'json' : 'html';
            $response = Response::create($data, $type);
        } else {
            $data = ob_get_clean();

            $content  = false === $data ? '' : $data;
            $status   = '' === $content && $this->request->isJson() ? 204 : 200;
            $response = Response::create($content, 'html', $status);
        }

        return $response;
    }

    /**
     * 执行请求
     */
    public function execute($addon = null, $controller = null, $action = null)
    {
        Event::trigger('addons_begin', $this->request);
        $this->setAddon($addon);
        $addon = get_installed_addons($this->addon);
        if (empty($addon)) {
            throw new HttpException(404, __("addon %s not installed", [$this->addon]));
        }
        if (empty($addon['is_verify'])) {
            throw new HttpException(404, __("addon %s not enabled", [$this->addon]));
        }
        $this->setController($controller);

        $this->setAction($action);

        $class = get_addons_class($this->addon, 'controller', $this->controller);
        if (!$class) {
            throw new HttpException(404, __('addon controller %s not found', [Str::studly($this->controller)]));
        }

        // 重写视图基础路径
        $config = $this->app->config->get('view');
        $config['view_path'] = $this->getAddonPath() . 'view' . DIRECTORY_SEPARATOR;
        $this->app->config->set($config, 'view');

        try {
            // 实例化控制器
            $instance = $this->controller($this->controller);
        } catch (ClassNotFoundException $e) {
            throw new HttpException(404, 'controller not exists:' . $e->getClass());
        }

        // 注册控制器中间件
        $this->registerControllerMiddleware($instance);

        return $this->app->middleware->pipeline('controller')
            ->send($this->request)
            ->then(function () use ($instance) {
                $action = $this->action;
                if (is_callable([$instance, $action])) {
                    $vars = $this->request->param();
                    try {
                        $reflect = new ReflectionMethod($instance, $action);
                        // 严格获取当前操作方法名
                        $actionName = $reflect->getName();
                        $this->request->setAction($actionName);
                    } catch (ReflectionException $e) {
                        $reflect = new ReflectionMethod($instance, '__call');
                        $vars    = [$action, $vars];
                        $this->request->setAction($action);
                    }
                } else {
                    // 操作不存在
                    throw new HttpException(404, 'method not exists:' . get_class($instance) . '->' . $action . '()');
                }

                $data = $this->app->invokeReflectMethod($instance, $reflect, $vars);

                return $this->autoResponse($data);

            });
    }
}