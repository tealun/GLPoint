<?php
declare(strict_types=1);

use think\facade\Event;
use think\facade\Route;
use think\facade\Cache;
use think\facade\Db;
use woo\common\helper\{
    Str, Arr
};

// 插件类库自动载入
spl_autoload_register(function ($class) {
    $class = ltrim($class, '\\');
    $dir = app()->getRootPath();
    $namespace = 'addons';

    if (strpos($class, $namespace) === 0) {
        $class = substr($class, strlen($namespace));
        $path = '';
        if (($pos = strripos($class, '\\')) !== false) {
            $path = str_replace('\\', '/', substr($class, 0, $pos)) . '/';
            $class = substr($class, $pos + 1);
        }
        $path .= str_replace('_', '/', $class) . '.php';
        $dir .= $namespace . $path;
        if (file_exists($dir)) {
            include $dir;
            return true;
        }
        return false;
    }
    return false;

});

if (!function_exists('hook')) {
    /**
     * 处理插件钩子
     * @param string $event 钩子名称
     * @param array|null $params 传入参数
     * @param bool $once 是否只返回一个结果
     * @return mixed
     */
    function hook($event, $params = null, bool $once = false)
    {
        $result = Event::trigger($event, $params, $once);

        return join('', $result);
    }
}

if (!function_exists('get_addons_info')) {
    /**
     * 读取插件的基础信息
     * @param string $name 插件名
     * @return array
     */
    function get_addons_info($name)
    {
        $addon = get_addons_instance($name);
        if (!$addon) {
            return [];
        }

        return $addon->getInfo();
    }
}

if (!function_exists('get_addons_instance')) {
    /**
     * 获取插件的单例
     * @param string $name 插件名
     * @return mixed|null
     */
    function get_addons_instance($name)
    {
        static $_addons = [];
        if (isset($_addons[$name])) {
            return $_addons[$name];
        }
        $class = get_addons_class($name);
        if (class_exists($class)) {
            $_addons[$name] = new $class(app());

            return $_addons[$name];
        } else {
            return null;
        }
    }
}

if (!function_exists('get_addons_class')) {
    /**
     * 获取插件类的类名
     * @param string $name 插件名
     * @param string $type 返回命名空间类型
     * @param string $class 当前类名
     * @return string
     */
    function get_addons_class($name, $type = 'hook', $class = null)
    {
        $name = trim($name);
        // 处理多级控制器情况
        if (!is_null($class) && strpos($class, '.')) {
            $class = explode('.', $class);

            $class[count($class) - 1] = Str::studly(end($class));
            $class = implode('\\', $class);
        } else {
            $class = Str::studly(is_null($class) ? $name : $class);
        }
        switch ($type) {
            case 'controller':
                $namespace = '\\addons\\' . $name . '\\controller\\' . $class;
                break;
            default:
                $namespace = '\\addons\\' . $name . '\\Plugin';
        }

        return class_exists($namespace) ? $namespace : '';
    }
}

if (!function_exists('addons_url')) {
    /**
     * 插件显示内容里生成访问插件的url
     * @param $url
     * @param array $param
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     * @return bool|string
     */
    function addons_url($url = '', $param = [], $suffix = true, $domain = false)
    {
        $request = app('request');
        if (empty($url)) {
            // 生成 url 模板变量
            $addons = $request->addon;
            $controller = $request->controller();
            $controller = str_replace('/', '.', $controller);
            $action = $request->action();
        } else {
            $url = Str::studly($url);
            $url = parse_url($url);
            if (isset($url['scheme'])) {
                $addons = strtolower($url['scheme']);
                $controller = $url['host'];
                $action = trim($url['path'], '/');
            } else {
                $route = explode('/', $url['path']);
                $addons = $request->addon;
                $action = array_pop($route);
                $controller = array_pop($route) ?: $request->controller();
            }
            $controller = Str::snake((string)$controller);

            /* 解析URL带的参数 */
            if (isset($url['query'])) {
                parse_str($url['query'], $query);
                $param = array_merge($query, $param);
            }
        }

        $route_file = app()->addons->getAddonsPath() . $addons .DIRECTORY_SEPARATOR . 'route.php';
        if (is_file($route_file)) {
            $route_reault = include $route_file;
            $addons = empty($route_reault['route_prefix']) ? $addons : trim($route_reault['route_prefix']);
        }
        $addons_prefix = \think\facade\Config::get('addons.addons_prefix', 'addons');
        return url("@{$addons_prefix}/{$addons}/{$controller}/{$action}", $param, $suffix, $domain);
    }
}

if (!function_exists('get_installed_addons')) {
    /**
     * 获取已安装的插件
     * @param string $name
     * @return array|bool
     */
    function get_installed_addons(string $name = '')
    {
        if (isset(app()->request->addonsDataCache)) {
            $addons = app()->request->addonsDataCache;
        } else {
            if (Cache::has('addon_static_cache')) {
                $addons = app()->request->addonsDataCache = Cache::get('addon_static_cache');
            } else {
                $addons = app()->request->addonsDataCache = Arr::combine(model('Addon')->select()->toArray(), 'name');
                Cache::tag('Addon')->set('addon_static_cache', $addons);
            }
        }
        if ($name) {
            return $addons[$name] ?? false;
        }
        return $addons ?:[];
    }
}

function addons_setting(string $var = '', $default = '')
{
    if (!is_woo_installed()) {
        return [];
    }
    if (Cache::has('addon_setting_static_cache')) {
        $list = Cache::get('addon_setting_static_cache');
    } else {
        try {
            $list = Db::name('AddonSetting')
                ->field(['var', 'value'])
                ->select()
                ->toArray();
            $list = Str::deepJsonDecode(Arr::combine($list, 'var', 'value'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        Cache::tag('AddonSetting')->set('addon_setting_static_cache', $list);
    }
    if (empty($var)) {
        return $list;
    }
    return !empty($list[$var]) ? $list[$var]: $default;
}

function get_addons_setting(string $addon)
{
    if (!is_woo_installed()) {
        return [];
    }
    if (Cache::has('addon_setting_static_cache_for_' . $addon)) {
        return Cache::get('addon_setting_static_cache_for_' . $addon);
    } else {
        try {
            $id = Db::name('Addon')->where('name', '=', $addon)->value('id');
            if ($id) {
                $list = Db::name('AddonSetting')
                    ->where('addon_id', '=', $id)
                    ->field(['var', 'value'])
                    ->select()
                    ->toArray();
                $list = Str::deepJsonDecode(Arr::combine($list, 'var', 'value'));
            } else {
                $list = [];
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        Cache::tag('AddonSetting')->set('addon_setting_static_cache_for_' . $addon, $list);
        return $list;
    }
}

function load_addons_config(string $addon, array $config = [])
{
    $addonPath = app()->addons->getAddonsPath() . $addon . DIRECTORY_SEPARATOR;
    if (empty($config)) {
        $config = glob($addonPath . 'config' . DIRECTORY_SEPARATOR . '*' . app()->getConfigExt());
    } else {
        foreach ($config as &$file) {
            $file = $addonPath . 'config' . DIRECTORY_SEPARATOR . $file . app()->getConfigExt();
        }
    }
    foreach ($config as $file) {
        if (is_file($file)) {
            app()->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }
    }
    return true;
}