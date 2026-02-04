<?php
declare (strict_types = 1);

namespace woo\common\addons;

use think\Route;
use think\facade\Config;
use think\facade\Lang;
use think\facade\Cache;
use think\facade\Event;

class Service extends \think\Service
{
    protected $addons_path;
    /**
     * 注册服务
     * @return mixed
     */
    public function register()
    {
        $this->addons_path = $this->getAddonsPath();
        // hooks 新版暂时没有做hooks功能 一般用于定义模板片段 方便其他模板共享它们；感觉实用性不强，后期有需要可以联系作者加上该功能
        $this->app->bind('addons', Service::class);
    }

    /**
     * 执行服务
     *
     * @return mixed
     */
    public function boot()
    {
        $this->registerRoutes(function (Route $route) {
            if (!is_woo_installed()) {
                return true;
            }
            // 路由脚本
            $execute = '\\woo\\common\\addons\\Route::execute';
            // 加载插件函数
            include_once woo_path() . 'common' . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . 'helper.php';

            // 注册插件公共中间件
            if (is_file($this->app->addons->getAddonsPath() . 'middleware.php')) {
                $this->app->middleware->import(include $this->app->addons->getAddonsPath() . 'middleware.php', 'route');
            }

            $addons_prefix = $this->app->config->get('addons.addons_prefix', 'addons');

            // 路由解析
            foreach (glob($this->app->addons->getAddonsPath() . '*' . DIRECTORY_SEPARATOR . 'route.php') as $route_file) {
                $info = pathinfo($route_file);
                // 获取插件名
                $addon = basename($info['dirname']);
                // 没有安装的插件不解析路由
                if (!get_installed_addons($addon)) {
                    continue;
                }
                $route_reault = include $route_file;
                if (is_array($route_reault)) {
                    $prefix = empty($route_reault['route_prefix']) ? $addon : trim($route_reault['route_prefix']);

                    foreach ($route_reault['rules']?? [] as $key => $item) {
                        if (is_string($item)) {
                            $item = ['rule' => $item];
                        }
                        if (!is_array($item)) {
                            continue;
                        }
                        try {
                            list($controller, $action) = explode('/', $item['rule'] ?? '');
                        } catch (\Exception $e) {
                            continue;
                        }
                        $each = $route->rule($addons_prefix . '/' . $prefix . '/' . $key, $execute)
                            ->name('@' . $addons_prefix . '/' . $prefix .'/'. $controller . '/' . $action)
                            ->completeMatch(true);
                        $append = [
                            'addon' => $addon,
                            'controller' => $controller,
                            'action' => $action
                        ];
                        if (isset($item['append']) && is_array($item['append'])) {
                            $append = array_merge($append, $item['append']);
                        }
                        $each->append($append);
                        if (isset($item['pattern'])) {
                            $each->pattern($item['pattern']);
                        }
                    }
                    $route->rule("{$addons_prefix}/{$prefix}/[:controller]/[:action]", $execute)->append(['addon' => $addon]);
                }
            }
        });
    }

    /**
     * 获取 addons 路径
     * @return string
     */
    public function getAddonsPath()
    {
        return $this->app->getRootPath() . 'addons' . DIRECTORY_SEPARATOR;
    }

    /**
     * 返回指定插件目录
     * @return string
     */
    public function getAddonPath($addonName = '')
    {
        $addonName = $addonName ? $addonName : ($this->app->request->addon ?? '');
        return $this->getAddonsPath() . $addonName . DIRECTORY_SEPARATOR;
    }
}
