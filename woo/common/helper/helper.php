<?php
declare (strict_types=1);

use think\facade\Env;
use think\facade\Lang;
use think\Exception;
use think\facade\Db;
use woo\common\helper\Str;
use woo\common\helper\Arr;
use think\facade\Cache;


// 系统公共函数库
if (!function_exists('pr')) {
    /**
     * 数据调试打印
     * @param $var
     */
    function pr($var)
    {
        if (Env::get('app_debug')) {
            if (function_exists('debug_backtrace')) {
                $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                if (isset($debug[0]['function']) && $debug[0]['function'] == 'pr') {
                    $template = '<pre style="color:blue;">%s</pre>';
                    printf($template, print_r('in ' . $debug[0]['file'] . " line " . $debug[0]['line'] . ' :', true));
                }
            }
            $template = '<pre style="color:red;">%s</pre>';
            printf($template, print_r($var, true));
        }
    }
}
if (!function_exists('woo_path')) {
    /**
     * 获取woo目录
     * @return string
     */
    function woo_path()
    {
        return app()->getRootPath() . 'woo' . DIRECTORY_SEPARATOR;
    }
}
if (!function_exists('run_mode')) {
    /**
     * 获取当前运行模式 结果只会有传统pfm模式和swoole模式
     * @return string  fpm|swoole
     */
    function run_mode()
    {
        if (!class_exists('\\think\\swoole\\App')) {
            return 'fpm';
        }
        if (PHP_SAPI === 'cli' && app() instanceof \think\swoole\App) {
            return 'swoole';
        } else {
            return 'fpm';
        }
    }
}
if (!function_exists('__')) {
    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || empty($name)) {
            return $name;
        }
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return Lang::get($name, $vars, $lang);
    }
}
if (!function_exists('get_model_name')) {
    /**
     * 获取某个模型的命名空间+名称
     * @param string $model
     * @param string $app
     * @return string 如果模型 返回模型命名空间+名称 否则返回 空字符串
     */
    function get_model_name(string $model, string $app = '')
    {
        if (false === strpos($model, '\\')) {
            if (false === strpos($model, '.')) {
                $model = Str::studly($model);
            } else {
                list($addon, $model_name) = explode('.', $model);
                $model = strtolower($addon) . "\\" . Str::studly($model_name);
            }
            if (!$app) {
                $app = app('http')->getName();
            }
            $class = "app\\{$app}\\model\\{$model}";
            if (class_exists($class)) {
                return $class;
            }
            $class = "app\\common\\model\\{$model}";
            if (class_exists($class)) {
                return $class;
            }
            $class = "woo\\{$app}\\model\\{$model}";
            if (class_exists($class)) {
                return $class;
            }
            $class = "woo\\common\\model\\{$model}";
            if (class_exists($class)) {
                return $class;
            }
            return '';
        } else {
            $class = $model;
            if (class_exists($class)) {
                return $class;
            } else {
                return '';
            }
        }
    }
}
if (!function_exists('model')) {
    /**
     * 实例化一个模型对象
     * @param string $model
     * @param string $app
     * @param bool $force 强制实例一个新对象
     * @return mixed|object|\think\App
     * @throws Exception
     */
    function model($model, string $app = '', bool $force = false)
    {
        if (!is_string($model)) {
            return $model;
        }
        $real_model = get_model_name($model, $app);
        if ($real_model) {
            return app($real_model, [], $force);
        } else {
            if (false === strpos($model, '\\')) {
                $model = parse_name($model, 1);
                if (!$app) {
                    $app = app('http')->getName();
                }
                $class = "app\\{$app}\\model\\{$model}";
            } else {
                $class = $model;
            }

            throw new Exception('model class not exists:' . $class);
        }
    }
}
if (!function_exists('is_json')) {
    /**
     * 判断一个字符串是否是json格式
     * @param $string
     * @return bool
     */
    function is_json($data, $assoc = true)
    {
        if (gettype($data) != 'string') {
            return false;
        }
        if (empty($data)) {
            return false;
        }
        //return !!preg_match('/^(\[|\{).*(\}|\])$/', $data);
        $data = json_decode($data, $assoc);
        if (is_object($data) || is_array($data)) {
            return $data;
        }
        return false;
    }
}
if (!function_exists('wooview')) {
    /**
     * 快速获取View对象 在其他类中也可以快速通过 wooview()['a'] = 'aaa'; 传递一个a变量到页面中
     */
    function wooview()
    {
        return app(\woo\common\View::class);
    }
}
if (!function_exists('get_js_links')) {
    /**
     * 自动生成js文件链接
     * @param $file 可以是一个文件  也可以传入多个文件或者数组
     * @return string
     */
    function get_js_links($file)
    {
        $files = is_array($file) ? $file : func_get_args();
        $tags = [];
        foreach ($files as $file) {
            array_push($tags, app(\woo\View::class)->createScript($file));
        }
        return $tags ? implode(PHP_EOL, $tags) . PHP_EOL : '';
    }
}
if (!function_exists('get_css_links')) {
    /**
     * 自动生成css文件链接
     * @param $file 可以是一个文件  也可以传入多个文件或者数组
     * @return string
     */
    function get_css_links($file)
    {
        $files = is_array($file) ? $file : func_get_args();
        $tags = [];
        foreach ($files as $file) {
            array_push($tags, app(\woo\View::class)->createLink($file));
        }
        return $tags ? implode(PHP_EOL, $tags) . PHP_EOL : '';
    }
}
/**
 * 生成栅格类名
 * @param $grid
 * @return string
 */
if (!function_exists('get_grid_class')) {
    function get_grid_class($grid)
    {
        if (empty($grid)) {
            $grid = 12;
        }
        $class = "";
        if (is_numeric($grid)) {
            $grid = ['md' => $grid, 'sm' => 12, 'xs' => 12];
        }
        if (isset($grid['lg'])) {
            $class .= " layui-col-lg" . ($grid['lg'] <= 12 ? $grid['lg'] : 12);
        }
        if (isset($grid['md'])) {
            $class .= " layui-col-md" . ($grid['md'] <= 12 ? $grid['md'] : 12);
        }
        if (isset($grid['sm'])) {
            $class .= " layui-col-sm" . ($grid['sm'] <= 12 ? $grid['sm'] : 12);
        }
        if (isset($grid['xs'])) {
            $class .= " layui-col-xs" . ($grid['xs'] <= 12 ? $grid['xs'] : 12);
        }
        return $class;
    }
}

/**
 * 获取关联模型名和字段
 * @param string $model
 * @return array
 * @throws Exception
 */
if (!function_exists('get_relation')) {
    function get_relation(string $model, $object = [])
    {
        $return  = [];
        if (strpos($model, '-') > 0) {
            $return = explode('-', $model);
        } else {
            $return[0] = $model;
        }
        if ($object) {
            if (!($object instanceof \woo\common\Model)) {
                throw new \Exception('第二个参数非WOO模型对象');
            }
            if (isset($object->relationLink[$return[0]])) {
                //throw new \Exception('模型' . get_class($object) . '的relationLink未定义关联键' . $return[0]);
                $return = array_merge($return, $object->relationLink[$return[0]]);
                $return[0] = $object->relationLink[$return[0]]['foreign'];
            }
        }

        $model_namespace = get_model_name($return[0]);
        if (empty($model_namespace)) {
            throw new \Exception('关联模型' . $return[0] . '不存在');
        }
        if (!isset($return[1])) {
            $return[1] = model($model_namespace)->display;
        }
        return $return;
    }
}

if (!function_exists('get_table_columns')) {
    function get_table_columns(int $model_id = 0, array $model_data = [], bool $is_field = true)
    {
        if (!$model_id && !$model_data) {
            return false;
        }
        if (!$model_data) {
            $model_data = model('Model')->find($model_id);
            if (empty($model_data)) {
                return false;
            }
            $model_data = $model_data->toArray();
        }
//        if (get_model_name($model_data['addon'] ? $model_data['addon'] . '.' . $model_data['model'] : $model_data['model'])) {
//            $table_name = model($model_data['addon'] ? $model_data['addon'] . '.' . $model_data['model'] : $model_data['model'])->getTable();
//        } else {
//            $table_name = empty($model_data['full_table']) ?
//                get_db_config('prefix') . ($model_data['addon'] ? Str::snake($model_data['addon']) . '_' : '') .Str::snake($model_data['model'])
//                :$model_data['full_table'];
//        }
        $table_name = empty($model_data['full_table']) ?
            get_db_config('prefix', $model_data['connection'] ?? '') . ($model_data['addon'] ? Str::snake($model_data['addon']) . '_' : '') . Str::snake($model_data['model'])
            :$model_data['full_table'];

        $sql = "SHOW FULL COLUMNS FROM `{$table_name}`";
        try {
            $list = Db::connect($model_data['connection'] ?? '')->query($sql);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if ($is_field) {
            $fields = [];
            foreach ($list as $item) {
                $fields[$item['Field']] = $item['Field'];
            }
            return $fields;
        }
        return $list;
    }
}

/**
 * 指定无限极数据（缓存）获取
 * @param string $modelName
 * @param null $key
 * @param null $index
 * @param array $default
 * @return object
 * @throws Exception
 */
if (!function_exists('tree')) {
    function tree(string $modelName, $key = null, $index = null, $default = [])
    {
        if ($key === true) {
            return new \woo\common\helper\Tree(model($modelName));
        }
        $treeTag = 'Tree' . $modelName;
        $container = \think\Container::getInstance();
        if ($container->has($treeTag)) {
            $tree =  $container->get($treeTag);
        } else {
            $tree = new \woo\common\helper\Tree(model($modelName));
            $container->bind($treeTag, $tree);
        }
        return $tree->get($key, $index, $default);
    }
}

/**
 * 后台栏目获取函数
 * @param null $key
 * @param null $index
 * @param array $default
 * @return object
 */
if (!function_exists('admin_menu')) {
    function admin_menu($key = null, $index = null, $default = [])
    {
        return tree('AdminRule', $key, $index, $default);
    }
}

if (!function_exists('admin_rule')) {
    function admin_rule($key = null, $index = null, $default = [])
    {
        return tree('AdminRule', $key, $index, $default);
    }
}

if (!function_exists('admin_group')) {
    function admin_group($key = null, $index = null, $default = [])
    {
        return tree('AdminGroup', $key, $index, $default);
    }
}

/***
 * 生成 后台菜单 栏目的地址
 * @param $nav  菜单id
 * @return string
 */
if (!function_exists('admin_menu_link')) {
    function admin_menu_link($nav)
    {
        return admin_rule_link($nav);
    }
}

/**
 * 获取后台规则url地址
 */
if (!function_exists('admin_rule_link')) {
    function admin_rule_link($id)
    {
        if (is_int($id)) {
            $data =  admin_rule($id);
        } else {
            $data = $id;
        }
        if (empty($data) || $data['type'] == 'directory') {
            return '';
        }
        $route = $data['url'];
        if (empty($route)) {
            $route = [];
            if (empty($data['addon'])) {
                array_push($route, $data['controller']);
            } else {
                array_push($route, $data['addon'] . '.' . $data['controller']);
            }
            array_push($route, $data['action']);
            $route = implode('/', $route);
        }
        return (string) url($route, $data['args'] && is_json($data['args']) ? json_decode($data['args'], true) : []);
    }
}

/**
 * 获取会员栏目数据
 */
if (!function_exists('user_menu')) {
    function user_menu($key = null, $index = null, $default = [])
    {
        return tree('UserMenu', $key, $index, $default);
    }
}

/**
 * 判断后台菜单 栏目是否有权限  如果使用了 自定义路由  菜单中需要把对应的真实控制器和方法填写进去 否则权限容易解析错误
 * @param $nav 菜单id
 * @return mixed
 */
if (!function_exists('admin_link_power')) {
    function admin_link_power($nav)
    {
        if (!in_array(app('http')->getName(), ['admin', 'business'])) {
            return true;
        }
        if (is_numeric($nav)) {
            $nav = app('http')->getName() == 'admin' ?  admin_menu($nav) : business_menu($nav);
        } elseif (is_string($nav)) {
            if (strpos($nav, '/') === false) {
                $nav = request()->controller() . '/' . $nav;
            }
            return app(\think\facade\Config::get('wooauth.handler'))->adminRoutePower(trim($nav));
        }
        // 如果使用了 自定义路由  菜单中需要把对应的真实控制器和方法填写进去 否则权限容易解析错误
        if (empty($nav['url']) || $nav['controller']) {
            return app(\think\facade\Config::get('wooauth.handler'))->adminRoutePower([
                'addon' => $nav['addon'],
                'controller' => $nav['controller'],
                'action' => $nav['action']
            ]);
        }
        return app(\think\facade\Config::get('wooauth.handler'))->adminRoutePower(trim($nav['url']));
    }
}

/**
 * 系统设置获取函数
 * @param string $var
 * @return array|mixed|string
 * @throws \Exception
 */
if (!function_exists('setting')) {
    function setting(string $var = '', $default = '', $empty = true)
    {
        if (!is_woo_installed()) {
            return [];
        }
        if (!empty(app()->request->settingData)) {
            $setting = app()->request->settingData;
        } else {
            if (app('http')->getName() != 'business') {
                $setting = app()->request->settingData = \woo\common\helper\Setting::getAdminSetting();
            } else {
                $setting = app()->request->settingData = array_merge(\woo\common\helper\Setting::getAdminSetting(), \woo\common\helper\Setting::getBusinessSetting());
            }
        }
        if (empty($var)) {
            return $setting;
        }
        if ($empty) {
            return isset($setting[$var]) ? $setting[$var]: $default;
        }
        return !empty($setting[$var]) ? $setting[$var]: $default;
    }
}

if (!function_exists('setting_for_js')) {
    function setting_for_js()
    {
        if (!is_woo_installed()) {
            return [];
        }
        if (app('http')->getName() != 'business') {
            $list = \woo\common\helper\Setting::getAdminSetting(true);
        } else {
            $list = array_merge(\woo\common\helper\Setting::getAdminSetting(true), \woo\common\helper\Setting::getBusinessSetting(true));
        }
        return $list;
    }
}

/**
 * 字典获取函数
 * @param string $model
 * @param string $field
 * @param string $key
 * @return array|string
 * @throws Exception
 */
if (!function_exists('dict')) {
    function dict(string $model, string $field = '', $key = '')
    {
        if (Cache::has('dictionary_static_cache')) {
            $cache = Cache::get('dictionary_static_cache');
        } else {
            $cache = model('Dictionary')->resetDictCache();
        }
        if (empty($field) && empty($key)) {
            return $cache[$model] ?? [];
        } elseif (empty($key)) {
            return $cache[$model][$field] ?? [];
        }
        return $cache[$model][$field][$key] ?? '';
    }
}

/**
 * 返回友好的文件大小数
 */
if (!function_exists('return_size')) {
    function return_size($bytes, $separator = '')
    {
        //utility functions
        $kb = 1024;          //Kilobyte
        $mb = 1024 * $kb;    //Megabyte
        $gb = 1024 * $mb;    //Gigabyte
        $tb = 1024 * $gb;    //Terabyte

        if ($bytes < $kb)
            return $bytes . "{$separator}B";
        else if ($bytes < $mb)
            return round($bytes / $kb, 2) . "{$separator}KB";
        else if ($bytes < $gb)
            return round($bytes / $mb, 2) . "{$separator}MB";
        else if ($bytes < $tb)
            return round($bytes / $gb, 2) . "{$separator}GB";
        else
            return round($bytes / $tb, 2) . "{$separator}TB";
    }
}

/**
 * 获取一个路径后缀
 * @param $path
 * @return string
 */
if (!function_exists('get_ext')) {
    function get_ext($path): string
    {
        if (empty($path)) {
            return '';
        }
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }
}

/**
 * 获取反射类
 * @param string $class
 * @return bool|ReflectionClass
 * @throws ReflectionException
 */
if (!function_exists('reflect')) {
    function reflect(string $class)
    {
        if (class_exists($class)) {
            return new \ReflectionClass($class);
        }
        if ('\\' === substr($class, 0, 1)) {
            $class = substr($class, 1);
        }
        if ('app\\' === substr($class, 0, 4)) {
            $class = 'woo\\' . substr($class, 4);
        }
        if (class_exists($class)) {
            return new \ReflectionClass($class);
        }
        return false;
    }
}

if (!function_exists('curl_send')) {
    /**
     * CURL请求函数
     * @param  array $rq http请求信息
     *                   url        : 请求的url地址
     *                   method     : 请求方法，'get', 'post', 'put', 'delete', 'head'
     *                   data       : 请求数据，如有设置，则method为post
     *                   header     : 需要设置的http头部
     *                   host       : 请求头部host
     *                   timeout    : 请求超时时间
     *                   cert       : ca文件路径
     *                   ssl_version: SSL版本号
     * @return string    http请求响应
     */
    function curl_send($rq)
    {
        if (is_string($rq)) {
            $url = $rq;
            $rq = [];
            $rq['url'] = $url;
            $rq['method'] = 'get';
        }

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $rq['url']);
        switch (true) {
            case isset($rq['method']) && in_array(strtolower($rq['method']), array('get', 'post', 'put', 'delete', 'head')):
                $method = strtoupper($rq['method']);
                break;
            case isset($rq['data']):
                $method = 'POST';
                break;
            default:
                $method = 'GET';
        }
        $header = isset($rq['header']) ? $rq['header'] : array();
        $header[] = 'Method:' . $method;

        $header[] = "User-Agent:Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)";
        isset($rq['host']) && $header[] = 'Host:' . $rq['host'];
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
        isset($rq['timeout']) && curl_setopt($curlHandle, CURLOPT_TIMEOUT, $rq['timeout']);
        isset($rq['data']) && in_array($method, array('POST', 'PUT')) && curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $rq['data']);

        $ssl = substr($rq['url'], 0, 8) == "https://" ? true : false;
        if (isset($rq['cert'])) {
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curlHandle, CURLOPT_CAINFO, $rq['cert']);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
            if (isset($rq['ssl_version'])) {
                curl_setopt($curlHandle, CURLOPT_SSLVERSION, $rq['ssl_version']);
            } else {
                curl_setopt($curlHandle, CURLOPT_SSLVERSION, 4);
            }
        } else if ($ssl) {
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);   //true any ca
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST,false);       //check only host
            if (isset($rq['ssl_version'])) {
                curl_setopt($curlHandle, CURLOPT_SSLVERSION, $rq['ssl_version']);
            } else {
                curl_setopt($curlHandle, CURLOPT_SSLVERSION, 4);
            }
        }
        $return['content'] = curl_exec($curlHandle);
        $return['result'] = curl_getinfo($curlHandle);
        curl_close($curlHandle);
        return $return;
    }
}

/**
 * URL生成函数 覆盖框架url函数
 * @param string $url
 * @param array $vars
 * @param bool $suffix
 * @param bool $domain
 * @return string|\think\route\Url
 */
function url(string $url = '', $vars = [], $suffix = true, $domain = false)
{
    if (stripos($url, 'http') === 0) {
        return $url;
    }
    if ($vars && is_string($vars) && is_json($vars)) {
        $vars = json_decode($vars, true);
    }
    if (!is_array($vars)) {
        $vars = [];
    }

    $url_patters = explode('/', $url);
    // 优化跨应用的时候 生成的url
    if (count($url_patters) >= 3) {
        $app_map = \think\facade\Config::get('app.app_map');
        $app = $url_patters[0];
        // 定义了应用映射
        if ($app_map && in_array($app, $app_map)) {
            $url_patters[0] = array_flip($app_map)[$app];
        }
        $domain_bind = \think\facade\Config::get('app.domain_bind');
        // 域名绑定
        if ($domain_bind && array_key_exists($app, $domain_bind)) {
            unset($url_patters[0]);
        }
        $url = implode('/', $url_patters);
    }
    return \think\facade\Route::buildUrl($url, $vars)->suffix($suffix)->domain($domain);
}

if (!function_exists('iurl')) {
    function iurl(string $url = '', array $vars = [], $suffix = true, $domain = false)
    {
        if (run_mode() == 'fpm') {
            return app('request')->root() . '?s=' . (strpos($url, 'install') !== 0 ? 'install/index/' . $url : $url);
        }
        return url($url, $vars, $suffix);
    }
}

/**
 * 获取数据库配置
 * @param string $key
 * @param string $default
 * @return mixed
 */
if (!function_exists('get_db_config')) {
    function get_db_config(string $key = '', string $default = '')
    {
        $config = \think\facade\Config::get('database');
        if (empty($default)) {
            $default = $config['default'];
        }
        return !$key ? $config['connections'][$default] : $config['connections'][$default][$key];
    }
}


/**
 * 文件列表打包为zip
 * @param $files
 * @param string $zip_name
 * @return bool|string
 */
if (!function_exists('zip_files')) {
    function zip_files($files, $zip_name = '')
    {
        $files = is_array($files) ? $files : func_get_args();
        if (empty($files)) {
            return false;
        }
        $zip = new \ZipArchive();
        $zip_path = root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'tempfile' . DIRECTORY_SEPARATOR;
        if (!is_dir($zip_path)) {
            mkdir($zip_path, 0755, true);
        }
        $zip_path .= ($zip_name ? $zip_name : md5((string)rand())) . '.zip';

        $zip->open($zip_path, \ZipArchive::CREATE);
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
        return $zip_path;
    }
}

if (!function_exists('zip_dir')) {
    function zip_dir($dir, $zip_name = '')
    {
        if (!is_dir($dir)) {
            return false;
        }
        $zip = new \ZipArchive();
        $zip_path = root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'tempfile' . DIRECTORY_SEPARATOR;

        if (!is_dir($zip_path)) {
            mkdir($zip_path, 0755, true);
        }
        $zip_path .= ($zip_name ? $zip_name : md5((string)rand())) . '.zip';

        if (true === $zip->open($zip_path, \ZipArchive::CREATE)) {
            addfile_tozip($dir, $zip);
            $zip->close();
            return $zip_path;
        }
        return false;
    }
}

if (!function_exists('addfile_tozip')) {
    function addfile_tozip($path, $zip, $basename = '')
    {
        $handler = opendir($path);
        while (($filename = readdir($handler)) !== false) {
            if ($filename != "." && $filename != "..") {
                if (is_dir($path . "/" . $filename)) {
                    addfile_tozip($path . "/" . $filename, $zip, ($basename ? $basename . '/' : '') . $filename);
                } else { //将文件加入zip对象
                    $zip->addFile($path . "/" . $filename, ($basename ? $basename . '/' : '') . basename($path . "/" . $filename));
                }
            }
        }
        @closedir($handler);
    }
}

/**
 * 获取目录大小
 */
if (!function_exists('get_dir_size')) {
    function get_dir_size($dir)
    {
        if (!is_dir($dir)) {
            return 0;
        }
        $sizeResult = 0;
        $handle = opendir($dir);
        while (false !== ($FolderOrFile = readdir($handle))) {
            if ($FolderOrFile != "." && $FolderOrFile != "..") {
                if (is_dir("$dir/$FolderOrFile")) {
                    $sizeResult += get_dir_size("$dir/$FolderOrFile");
                } else {
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }
        closedir($handle);
        return $sizeResult;
    }
}

/**
 * 删除目录
 */
if (!function_exists('remove_dir')) {
    function remove_dir($dir)
    {
        if (strpos($dir, 'app') !== false || strpos($dir, 'woo') !== false) {
            return false;
        }

        if (!is_dir($dir)) {
            return false;
        }

        $handle = opendir($dir);
        while (false !== ($FolderOrFile = readdir($handle))) {
            if ($FolderOrFile != "." && $FolderOrFile != "..") {
                if (is_dir("$dir/$FolderOrFile")) {
                    remove_dir("$dir/$FolderOrFile");
                } else {
                    @unlink("$dir/$FolderOrFile");
                }
            }
        }
        closedir($handle);
    }
}

/**
 * 从内容中匹配图片src
 */
if (!function_exists('match_src')) {
    function match_src($str, $number = 1, $all = false)
    {
        $result = preg_match_all("/<img[^>]*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i", str_ireplace("\\", "", $str), $arr);
        if ($all === false) {
            if ($result) {
                return $arr[1][$number - 1];
            }
        } else {
            if ($result) {
                return $arr[1];
            }
        }
        return false;
    }
}

if (!function_exists('extract_tag')) {
    function extract_tag($html, $type = 'end')
    {
        $arr_single_tags = array('meta', 'img', 'br', 'link', 'area');
        preg_match_all('#<([a-z]+)(?: .*)?>|</([a-z]+)>#iU', $html, $matches);
        $tags = array('complete' => array(), 'tag' => array());
        foreach ($matches[0] as $index => $tag) {
            if (in_array($matches[1][$index], $arr_single_tags)) {
                continue;
            }
            $tags['complete'][] = $tag;
            $tags['tag'][] = $matches[1][$index] ? $matches[1][$index] : '/' . $matches[2][$index];
        }
        $open_stack = [];
        foreach ($tags['tag'] as $index => $item) {
            if ($item[0] == '/') {
                $temp = substr($item, 1);
                if (count($open_stack) && $tags['tag'][end($open_stack)] == $temp) {
                    array_pop($open_stack);
                }
            } else {
                array_push($open_stack, $index);
            }
        }
        if ($type == 'start') {
            return array_intersect_key($tags['complete'], array_flip($open_stack));
        } else {
            return array_reverse(array_intersect_key($tags['tag'], array_flip($open_stack)));
        }
    }
}

if (!function_exists('close_tag')) {
    function close_tag($html)
    {
        $html = preg_replace('/<[^<>]*$/', '', $html);
        $closing_tags = extract_tag($html);
        return $html . ($closing_tags ? '</' . implode('></', $closing_tags) . '>' : '');
    }
}

if (!function_exists('get_page_count')) {
    function get_page_count($html, $delimiter = '/_ueditor_page_break_tag_/')
    {
        $pages = preg_split($delimiter, $html);
        return count($pages);
    }
}

if (!function_exists('extract_page')) {
    function extract_page($html, $page, $delimiter = '/_ueditor_page_break_tag_/')
    {
        $pages = preg_split($delimiter, $html);

        $conjected_pages = implode('', array_slice($pages, 0, $page));
        $opening_tags = extract_tag($conjected_pages, 'start');
        $conjected_pages = implode('', array_slice($pages, 0, $page + 1));
        $closing_tags = extract_tag($conjected_pages);
        return implode('', $opening_tags) . $pages[$page] . ($closing_tags ? '</' . implode('></', $closing_tags) . '>' : '');
    }
}

if (!function_exists('test_write_dir')) {
    function test_write_dir($dir)
    {
        $tfile = "_test.txt";
        $fp = @fopen($dir . "/" . $tfile, "w");
        if (!$fp) {
            return false;
        }
        fclose($fp);
        $rs = @unlink($dir . "/" . $tfile);
        if ($rs) {
            return true;
        }
        return false;
    }
}

if (!function_exists('get_file_sql')) {
    function get_file_sql($file, $tablePre, $charset = 'utf8', $defaultTablePre = 'woo_', $defaultCharset = 'utf8')
    {
        if (is_file($file)) {
            $sql = file_get_contents($file);
            $sql = str_replace("\r", "\n", $sql);
            $sql = str_replace("BEGIN;\n", '', $sql);//兼容 navicat 导出的 insert 语句
            $sql = str_replace("COMMIT;\n", '', $sql);//兼容 navicat 导出的 insert 语句
            $sql = str_replace('/' . $defaultCharset . '\s+/i', $charset . ' ', $sql);
            $sql = preg_replace('/AUTO_INCREMENT=\d{1,}/i', 'AUTO_INCREMENT=1', $sql);
            $sql = preg_replace('/,\s\d{10}/i', ', ' . time(), $sql);
            $sql = trim($sql);
            $sql = str_replace(" `{$defaultTablePre}", " `{$tablePre}", $sql);
            $sqls = explode(";\n", $sql);
            return $sqls;
        }
        return [];
    }
}

if (!function_exists('execute_sql')) {
    function execute_sql($sql)
    {
        $sql = trim($sql);
        preg_match('/CREATE TABLE .+ `([^ ]*)`/', $sql, $matches);
        if ($matches) {
            $table_name = $matches[1];
            $msg = "创建数据表{$table_name}";
            try {
                Db::query($sql);
                return [
                    'error' => 0,
                    'message' => $msg . ' 成功！'
                ];
            } catch (\Exception $e) {
                return [
                    'error' => 1,
                    'message' => $msg . ' 失败！',
                    'exception' => $e->getMessage()
                ];
            }

        } else {
            try {
                Db::query($sql);
                return [
                    'error' => 0,
                    'message' => 'SQL执行成功!'
                ];
            } catch (\Exception $e) {
                return [
                    'error' => 1,
                    'message' => 'SQL执行失败！',
                    'exception' => $e->getMessage()
                ];
            }
        }
    }
}

/**
 * 图片转base64
 */
if (!function_exists('base64_encode_image')) {
    function base64_encode_image($image_file)
    {
        $image_file = public_path() . $image_file;
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }
}

/**
 * 目录复制
 */
if (!function_exists('copydirs')) {
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        if (!in_array(substr($dest, -1, 1), ['/', '\\'])) {
            $dest .= DIRECTORY_SEPARATOR;
        }
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $sontDir = $dest . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item->getPathName(), $dest . $iterator->getSubPathName());
            }
        }
    }
}

/**
 * 删除目录
 */
if (!function_exists('rmdirs')) {
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname)) {
            return false;
        }
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirname, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }
}

if (!function_exists('get_base_class')) {
    function get_base_class($class, string $type = 'model')
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $type = strtolower($type);
        $start = strpos($class, $type . "\\");
        return str_replace("\\", '.', substr($class, $start + strlen($type) + 1));
    }
}

if (!function_exists('is_woo_installed')) {
    function is_woo_installed()
    {
        return is_file(root_path() . 'data' . DIRECTORY_SEPARATOR . 'install.lock');
    }
}

if (!function_exists('create_statistics_link')) {
    function create_statistics_link($item)
    {
        if (!empty($item['is_virtual'])) {
            return 'javascript:;';
        }
        if (!empty($item['url'])) {
            return $item['url'];
        }
        return url(Str::snake($item['model']) . '/index');
    }
}
/**
 * 图片响应输出
 * @param $image
 * @return \think\Response
 */
if (!function_exists('image_response')) {
    function image_response($image)
    {
        try {
            $imageinfo = getimagesize($image);
            if (false === $imageinfo || (IMAGETYPE_GIF === $imageinfo[2] && empty($info['bits']))) {
                throw new \think\image\Exception('Illegal image file');
            }
            $extension = strtolower(image_type_to_extension($imageinfo[2], false));
            $contentType = $imageinfo['mime'];
        } catch (\Exception $e) {
            $extension = strtolower(get_ext($image));
            $contentType = 'image/' . $extension;
        }
        switch ($extension) {
            case 'png':
                $img = imagecreatefrompng($image);
                break;
            case 'gif':
                $img = imagecreatefromgif($image);
                break;
            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($image);
                break;
            default:
                $img = imagecreatefromstring(file_get_contents($image));
        }
        $imagefunc = 'image' . $extension;
        ob_start();
        $imagefunc($img);
        $content = ob_get_clean();
        imagedestroy($img);
        return response($content, 200, ['Content-Length' => strlen($content), 'Last-modified' => gmdate('D, d M Y H:i:s', filemtime($image)) . ' GMT'])->contentType($contentType);
    }
}

/**
 * 缩略图生成函数
 * @param string $image 图片路径
 * @param int $width 宽度
 * @param int $height 高度
 * @param int $method 方式 只能写 1- 6 和TP的缩略图方式对应
 * @param bool $water 水印 暂时不支持
 * @return string
 */
if (!function_exists('thumb')) {
    function thumb(string $image, $width = 0, $height = 0, $method = 0, bool $water = false)
    {
        // 匹配图片上传位置
        foreach (\think\facade\Config::get('wooupload.drivers') as $type => $upload) {
            if (stripos($image, $upload['domain']) === 0) {
                $driver = $type;
                break;
            }
        }
        if (empty($driver)) {
            return $image;
        }
        $return = (new \woo\common\Upload(['type' => $driver]))->getThumbUrl(...func_get_args());
        return $return ? $return : $image;
    }
}

if (!function_exists('get_upload_domains')) {
    function get_upload_domains()
    {
        $domins = [];
        foreach (\think\facade\Config::get('wooupload.drivers') as $type => $upload) {
            $domins[$type] = $upload['domain'];
        }
        return $domins;
    }
}

if (!function_exists('model_cache')) {
    function model_cache(string $name = '', array $where = [])
    {
        if (Cache::has('model_static_cache')) {
            $list = Cache::get('model_static_cache');
        } else {
            $list = Db::name('Model')->select()->toArray();
            Cache::tag('Model')->set('model_static_cache', $list);
        }
        if (empty($name) && empty($where)) {
            return $list;
        }
        if ($name) {
            array_push($where, ['model', '=', trim($name)]);
        }
        $collection = new  \think\Collection($list);
        foreach ($where as $item) {
            $collection = $collection->where(...$item);
        }
        return $collection->toArray();
    }
}

/**
 * 获取完全绝对url
 */
if (!function_exists('furl')) {
    function furl($url) {
        $url = trim($url);
        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
            if ($url[0] == '/') {
                $url = substr($url, 1);
            }
            $url = request()->root(true) . $url;
        }
        return $url;
    }
}
/**
 * 获取已安装的应用
 */
if (!function_exists('get_app')) {
    function get_app(string $name = '') {
        if (isset(app()->request->applicationDataCache)) {
            $apps = app()->request->applicationDataCache;
        } else {
            if (Cache::has('application_static_cache')) {
                $apps = app()->request->applicationDataCache = Cache::get('application_static_cache');
            } else {
                $apps = app()->request->applicationDataCache = Arr::combine(app('app\common\model\Application')->select()->toArray(), 'name');
                Cache::tag('Application')->set('application_static_cache', $apps);
            }
        }
        if ($name) {
            return $apps[$name] ?? false;
        }
        return $apps ?:[];
    }
}

if (!function_exists('get_sensitive')) {
    function get_sensitive()
    {
        if (Cache::has('sensitive_static_cache')) {
            return Cache::get('sensitive_static_cache');
        }
        try {
            $list = array_map(function ($value) {
                return '/' . $value . '/';
            }, Db::name('Sensitive')->where('is_verify', '=', 1)->column('title'));
        } catch (\Exception $e) {
            $list = [];
        }
        Cache::tag('Sensitive')->set('sensitive_static_cache', $list);
        return $list;
    }
}

if (!function_exists('get_ip_info')) {
    /**
     * 获取ip地址的详细信息
     * @param string $ip
     * @return array|mixed
     */
    function get_ip_info($ip = '')
    {
        if (empty($ip)) {
            $ip = request()->ip();
        }
        $cacheKey = 'ipinfo_' . $ip;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        $url = 'https://ip.taobao.com/outGetIpInfo?ip=' . $ip . '&accessKey=alibaba-inc';
        $result = curl_send(['url' => $url]);
        $ipinfo = [
            'ip' => $ip,
            'country' => '',
            'province' => '',
            'province_id' => '',
            'city' => '',
            'city_id' => '',
            'area' => '',
            'region' => '',
            'isp' => '',
        ];
        if (empty($result['content'])) {
            return $ipinfo;
        }
        $result['content'] = json_decode($result['content'], true);
        if (!empty($result['content']['data']) && $result['content']['code'] == 0) {
            $region = [$result['content']['data']['country'], $result['content']['data']['region'], $result['content']['data']['city']];
            foreach ($region as $k => $v) {
                if ($v == 'XX' || !$v) {
                    unset($region[$k]);
                }
            }
            $ipinfo = [
                'ip' => $ip,
                'country' => $result['content']['data']['country'] ?? '',
                'province' => $result['content']['data']['region'] ?? '',
                'province_id' => $result['content']['data']['region_id'] ?? '',
                'city' => $result['content']['data']['city'] ?? '',
                'city_id' => $result['content']['data']['city_id'] ?? '',
                'area' => $result['content']['data']['area'] ?? '',
                'region' => implode('/', $region) ?? '',
                'isp' => $result['content']['data']['isp'] ?? ''
            ];
        }

        Cache::set($cacheKey, $ipinfo, 86400);
        return $ipinfo;
    }
}

if (!function_exists('get_cascader_value')) {
    function get_cascader_value($model, $value)
    {
        $tree = new \woo\common\helper\Tree(model($model));
        $is_cahce = Cache::has($tree->getCacheName());
        if (false === strpos((string)$value, ',')) {
            if ($is_cahce) {
                $parent = $tree->getDeepParents(intval($value));
                array_pop($parent);
                $value = array_merge(array_reverse($parent), [intval($value)]);
            } else {
                $value = array_reverse(array_merge([$value], get_cascader_query_parents($model, $value)));
            }
        } else {
            $value = explode(',', $value);
        }
        $return = [];
        foreach ($value as $id) {
            if ($is_cahce) {
                array_push($return, $tree->get($id, model($model)->display));
            } else {
                if (Cache::has($model . '_cascader_for_' . $id)) {
                    $v = Cache::get($model . '_cascader_for_' . $id);
                } else {
                    $v = Db::name($model)->where('id', '=', intval($id))->value(model($model)->display);
                    Cache::tag($model)->set($model . '_cascader_for_' . $id, $v);
                }
                array_push($return, $v);
            }
        }
        return $return;
    }
}

if (!function_exists('get_cascader_query_parents')) {
    function get_cascader_query_parents($model, $value)
    {
        $list = [];
        if (Cache::has($model . '_cascader_for_parent_' . $value)) {
            $parent_id = Cache::get($model . '_cascader_for_parent_' . $value);
        } else {
            $parent_id = Db::name($model)->where('id', '=', intval($value))->value('parent_id');
            Cache::tag($model)->set($model . '_cascader_for_parent_' . $value, $parent_id);
        }
        if ($parent_id) {
            $list = array_merge([$parent_id], get_cascader_query_parents($model, $parent_id));
        }
        return $list;
    }
}

if (!function_exists('ajax')) {
    function ajax($result = 'success', string $message = '', $data = []) {
        $config = app()->config->get('api.status', []);
        $code = !is_numeric($result) ? ($config[$result]['code'] ?? 0) :$result;
        settype($data, 'array');
        if ($result != 'success' && empty($data)) {
            $data[] = $message;
        }
        return json([
            'result' => $result,
            'code' => $code,
            'message' => $message ? $message : ($config[$result]['message'] ?? ''),
            'data' => $data,
            'timestamp' => time()
        ]);
    }
}

if (!function_exists('api_check_captcha')) {
    /**
     * API中验证码的验证
     * @param null $captcha  验证码
     * @param null $key  验证码的key值
     * @param bool $snapchat
     * @return bool
     */
    function api_check_captcha($captcha = null, $key = null, $snapchat = true)
    {
        if (is_null($captcha)) {
            $captcha = request()->param('captcha', '');
        }
        if (is_null($key)) {
            $key = request()->param('captcha_key', '');
        }
        if (empty($captcha) || empty($key)) {
            return false;
        }
        if ($snapchat) {
            $code = Cache::pull($key);
        } else {
            $code = Cache::get($key);
        }
        if (empty($code) || strtolower($code) != strtolower($captcha)) {
            return false;
        }
        return true;
    }
}

if (!function_exists('get_pinyin')) {
    function get_pinyin(string $string, $first = false)
    {
        try {
            if ($first) {
                return \woo\common\helper\Pinyin::get($string, true);
            }
            return \woo\common\helper\Pinyin::get($string);
        } catch (\Exception $e) {

        }
        return '';
    }
}

if (!function_exists('sign_give_score')) {
    /**
     * 基础连续签到指定天数赠送的积分
     * @param int $continue
     */
    function sign_give_score(int $continue)
    {
        $scoreMap = setting('user_sign_give_score', []);
        if (!$scoreMap) {
            return 0;
        }
        $keyMap = array_keys($scoreMap);
        sort($keyMap);

        for ($i = 0; $i <= count($keyMap) - 1; $i++) {
            if ($continue >= $keyMap[$i] && isset($keyMap[$i + 1]) && $continue < $keyMap[$i + 1]) {
                return $scoreMap[$keyMap[$i]];
            }
        }
        $max = max($keyMap);
        if ($continue >= $max) {
            return $scoreMap[$max];
        }
        return 0;
    }
}

if (!function_exists('auto_antispam')) {
    /**
     * 自动文本审核 需要调用第三方API
     * @param string $model 被审核的模型名
     * @param int $id 被审核数据的id
     * @param string $content_fields 被审核的字段列表 可以是数组多个字段一起审核
     * @param string $verify_field 审核以后讲哪个字段改为 1
     * @return  只有 === true 表示审核通过  返回 字符串表示审核失败的错误信息
     */
    function auto_antispam(string $model, int $id, $content_fields = 'content', $verify_field = 'is_verify')
    {
        if (!get_model_name($model)) {
            return false;
        }
        $model = model($model);

        $data = $model
            ->where($model->getPk(), '=', $id)
            ->find();
        if (empty($data)) {
            return '需要审核的数据不存在';
        }
        $checkData = $data->toArray();
        if (!empty($checkData[$verify_field])) {
            return true;
        }
        $content = [];
        foreach ((array)$content_fields as $field) {
            array_push($content, $checkData[$field] ?? '');
        }
        $content = trim(preg_replace('/\s/', '', strip_tags(implode('', $content))));

        $result = \woo\common\facade\ThinkApi::thinkAudit($content);

        if ($result['code'] == 0) {
            $store = [
                'title' => get_base_class($model),
                'foreign_id' => $id,
                'content' => mb_substr($content, 0, 5000),
                'is_verify' => !empty($result['data']['pass']) ? 1 : 0,
                'result' => $result,
                'words' => isset($result['data']['result']) ? $result['data']['result'] : '',
                'msg' => isset($result['message']) ? $result['message'] : '',
                'admin_id' => 0
            ];
            model('Antispam')->createData($store);

            if (!empty($result['data']['pass'])) {
                $my_result = null;
                if (!empty($verify_field)) {
                    $my_result = $data->modifyData([$verify_field => 1]);
                }
                if (isset($my_result)) {
                    if ($my_result) {
                        return true;
                    }
                    return '内容审核验证通过，但是' . array_values($data->getError())[0] ?? '数据写入失败';
                } else {
                    return true;
                }
            } else {
                return $result['message'] ?? '内核审核不通过，请查看审核记录了解详情';
            }
        } else {
            return $result['message'];
        }
    }
}

if (!function_exists('get_user_power_rules')) {
    function get_user_power_rules()
    {
        if (Cache::has('check_user_menu_rules')) {
            return Cache::get('check_user_menu_rules');
        }
        $rules = Db::name('UserMenu')
            ->where([
                ['is_not_power', '=', 0],
                ['url', '<>', '']
            ])
            ->field(['id', 'url'])
            ->select()
            ->toArray();
        $rules = Arr::combine($rules, 'id', 'url');
        Cache::tag('UserMenu')->set('check_user_menu_rules', $rules);
        return $rules;
    }
}

if (!function_exists('check_user_power')) {
    function check_user_power(string $rule)
    {
        $login = \woo\common\facade\Auth::user();
        if (!$login || empty($rule)) {
            return true;
        }

        if (app()->isDebug()) {
            // 配置了调试阶段 不验证权限
            if (!\think\facade\Config::get('wooauth.is_user_debug_power', true)) {
                return true;
            }
        }
        if (Cache::has('user_power_content_count')) {
            $count = Cache::get('user_power_content_count');
        } else {
            $count = Db::name('UserPower')->count();
            Cache::set('user_power_content_count', $count);
        }
        if (!$count) {
            return true;
        }
        $rules = get_user_power_rules();
        if (!in_array($rule, $rules)) {
            return true;
        }

        $group_id = $login[Str::snake($login['login_model']) . '_group_id'] ?? 0;

        if (Cache::has('user_power_content_for_' . $group_id)) {
            $content = Cache::get('user_power_content_for_' . $group_id);
        } else {
            $content = Db::name('UserPower')
                ->where([
                    ['user_group_id', '=', $group_id]
                ])
                ->find();
            if ($content) {
                Cache::tag('UserPower')->set('user_power_content_for_' . $group_id, $content, 86400);
            }
        }
        if (empty($content)) {
            return false;
        }
        $content = json_decode($content['content'], true);

        if (!in_array($rule, $content)) {
            return false;
        }
        return true;
    }
}

if (!function_exists('model_cache_tag')) {
    function model_cache_tag($tags = '')
    {
        if (empty(app('request')->business_id)) {
            return $tags;
        }
        if (empty($tags)) {
            return 'CacheTag_business_' . app('request')->business_id;
        }
        if (is_array($tags)) {
            $returns = [];
            foreach ($tags as $tag) {
                array_push($returns, ...(array)model_cache_tag($tag));
            }
            return $returns;
        }
        return [
            $tags,
            $tags . '_for_business_' . app('request')->business_id,
            'CacheTag_business_' . app('request')->business_id,
        ];
    }
}


if (!function_exists('write_file_cache')) {
    /**
     * 写入 php 静态文件 数组缓存  特殊情况会用到
     * @param $name 文件名
     * @param $value 数组值
     * @return array|mixed
     */
    function write_file_cache($name, $value)
    {
        $cache_dir = root_path() . 'data' . DIRECTORY_SEPARATOR . 'cache';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }
        $file = $cache_dir . DIRECTORY_SEPARATOR . $name . '.php';
        return file_put_contents($file, "<?php\nreturn " . var_export($value, true) . "\n?>");
    }
}

if (!function_exists('read_file_cache')) {

    /**
     * 读取静态缓存文件数据
     * @param $name 文件名
     * @return array|mixed
     */
    function read_file_cache($name)
    {
        $file = root_path() . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $name . '.php';
        if (is_file($file)) {
            return include($file);
        } else {
            return [];
        }
    }
}

if (!function_exists('get_age_by_id')) {
    /**
     * 通过字符串获取年龄
     * @param $id string
     * @return float|int|string
     */
    function get_age_by_id(string $id)
    {
        if (empty($id)) return '';
        if (strlen($id) != 15) {
            $birth = substr($id, 6, 8);
        } else {
            $birth = '19' . substr($id, 6, 6);
        }
        $date = strtotime($birth);
        $today = strtotime('today');
        $diff = floor(($today - $date) / 86400 / 365);
        $age = strtotime($birth . ' +' . $diff . 'years') > $today ? ($diff + 1) : $diff;
        return $age;
    }
}

if (!function_exists('show_mobile')) {
    function show_mobile(string $mobile)
    {
        if (!$mobile) {
            return '无';
        }
        return substr($mobile, 0, 3) . str_repeat('*', 6) . substr($mobile, -2);
    }
}

if (!function_exists('show_email')) {
    function show_email(string $email)
    {
        if (!$email) {
            return '无';
        }
        $emailExplode = explode('@', $email);
        $suffix = array_pop($emailExplode);
        return substr($email, 0, 2) . str_repeat('*', mb_strlen($email) - 2 - mb_strlen($suffix)) . $suffix;
    }
}

if (!function_exists('show_truename')) {
    function show_truename(string $name)
    {
        if (!$name) {
            return '无';
        }
        if (mb_strlen($name) <= 2) {
            return '*' . mb_substr($name, 1);
        }
        return mb_substr($name, 0, 1) . str_repeat('*', mb_strlen($name) - 2) . mb_substr($name, -1, 1);
    }
}
if (!function_exists('show_idcard')) {
    function show_idcard(string $idcard)
    {
        if (!$idcard) {
            return '无';
        }
        return mb_substr($idcard, 0, 2) . str_repeat('*', mb_strlen($idcard) - 4) . mb_substr($idcard, -2);
    }
}

if (!function_exists('show_address')) {
    function show_address(string $address)
    {
        if (!$address) {
            return '无';
        }
        return mb_substr($address, 0, 2) . str_repeat('*', min(mb_strlen($address) - 2, 12));
    }
}

if (!function_exists('return_icon_class')) {
    function return_icon_class($icon)
    {
        $prefixs = ['layui-icon', 'woo-icon', 'pear-icon'];
        if (\think\facade\Config::get('woomodel.custom_icons_prefix', '')) {
            array_push($prefixs, \think\facade\Config::get('woomodel.custom_icons_prefix', ''));
        }

        foreach ($prefixs as $prefix) {
            if (preg_match('/' . ' ' . $prefix . ' ' . '/', ' ' . $icon . ' ')) {
                return $icon;
            }
            if (strpos($icon, $prefix) !== false) {
                return $prefix . ' ' . $icon;
            }
        }
        return $icon;
    }
}

if (!function_exists('hex2rgba')) {
    function hex2rgba($color, $opacity = false, $raw = false)
    {
        $default = 'rgb(0,0,0)';
        if (empty($color)) {
            return $default;
        }
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }
        if (strlen($color) == 6) {
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return $default;
        }

        $rgb = array_map('hexdec', $hex);

        if ($raw) {
            if ($opacity) {
                $opacity = abs($opacity) <= 1 ? $opacity : 1.0;
                array_push($rgb, $opacity);
            }
            $output = $rgb;
        } else {
            if ($opacity) {
                $opacity = abs($opacity) <= 1 ? $opacity : 1.0;
                $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
            } else {
                $output = 'rgb(' . implode(",", $rgb) . ')';
            }
        }
        return $output;
    }
}