<?php

namespace woo\common;

use ArrayAccess;
use woo\common\controller\Controller;
use woo\common\helper\Str;
use think\facade\Config;
use think\facade\View as ThinkView;

#[\AllowDynamicProperties]
final class View implements ArrayAccess
{
    protected $template = null;
    protected $request;
    protected $scriptVars = [];
    protected $assignFile = [
        'css'     => [],
        'js'      => [],
        'deferjs' => []
    ];

    protected $config = [
        'auto_rule'     => 1,
        'view_dir_name' => 'view',
        'view_path'     => '',
        'view_suffix'   => 'html',
        'view_depr'     => DIRECTORY_SEPARATOR,
        'tpl_cache'     => true
    ];

    public function __construct()
    {
        $this->request = app()->request;
        $this->config = array_merge($this->config, Config::get('view'));
    }

    /**
     * 统一获取到所有静态文件连接
     * @return array
     */
    public function getStaticLinks()
    {
        $css = [];
        foreach ($this->assignFile['css'] as $file) {
            array_push($css, $this->createLink($file));
        }
        $js = [];
        foreach ($this->assignFile['js'] as $file) {
            array_push($js, $this->createScript($file));
        }
        $deferjs = [];
        foreach ($this->assignFile['deferjs'] as $file) {
            array_push($deferjs, $this->createScript($file));
        }
        return [
            'css' => $css ? implode(PHP_EOL, $css) . PHP_EOL : '',
            'js' => $js ? implode(PHP_EOL, $js) . PHP_EOL : '',
            'deferjs' => $deferjs ? implode(PHP_EOL, $deferjs) . PHP_EOL : ''
        ];
    }

    /**
     * 生成css文件连接
     * @param string $file
     * @return string
     */
    public function createLink(string $file)
    {
        return sprintf('<link type="text/css" rel="stylesheet" href="%s"/>', $this->appendVersion($this->parseFile($file, 'css')));
    }

    /**
     * 生成js文件连接
     * @param string $file
     * @return string
     */
    public function createScript(string $file)
    {
        return sprintf('<script type="text/javascript" src="%s"></script>', $this->appendVersion($this->parseFile($file, 'js')));
    }

    protected function appendVersion($file)
    {
        if (!Str::contains($file, '?')) {
            $file .= '?v=' . Controller::version();
        }
        return $file;
    }

    /**
     * 解析静态文件路径
     * @param string $file
     * @param string $extension
     * @param string $basedir
     * @return bool|string
     */
    public function parseFile(string $file, string $extension, string $basedir = '')
    {
        if (strtolower(substr($file, -(strlen($extension) + 1))) != '.' . $extension) {
            $file .= '.' . $extension;
        }
        if (0 === stripos($file, 'http://') || 0 === stripos($file, 'https://')) {
            return $file;
        }
        if (empty($basedir)) {
            $basedir = $extension;
        }
        if (0 !== strpos($file, '/') && 0 !== strpos($file, 'static') && 0 !== strpos($file, '..')) {
            return $this->request->root() . 'static/' . $basedir . '/' . $file;
        }
        $file = 0 !== strpos($file, '/') ? $file : substr($file, 1);
        if (0 !== strpos($file, 'static') && 0 !== strpos($file, '..')) {
            return $this->request->root() . 'static' . '/' . $file;
        }
        if (0 === strpos($file, '..')) {
            $file = substr($file, 3);
        }
        return $this->request->root() . $file;
    }

    /**
     * 添加js文件
     * @param $file
     * @param bool|null $defer
     */
    public function addJs($file, ?bool $defer = null)
    {
        if (is_null($defer)) {
            $defer = Config::get('woo.script_default_defer');
        }
        $key = $defer ? 'deferjs' : 'js';
        if (is_array($file)) {
            $this->assignFile[$key] = array_merge($this->assignFile[$key], $file);
        } else {
            array_push($this->assignFile[$key], $file);
        }
        $this->assignFile[$key] = array_unique($this->assignFile[$key]);
    }

    /**
     * 删除js文件
     * @param $file
     * @return bool
     */
    public function removeJs($file = null)
    {
        if (is_null($file)) {
            $this->assignFile['js'] = [];
            $this->assignFile['deferjs'] = [];
            return  true;
        }
        settype($file, 'array');
        $this->assignFile['js'] = array_filter($this->assignFile['js'], function ($path) use ($file){
            return !in_array($path, $file);
        });
        $this->assignFile['deferjs'] = array_filter($this->assignFile['deferjs'], function ($path) use ($file){
            return !in_array($path, $file);
        });
        return true;
    }

    /**
     * 添加css文件
     * @param $file
     */
    public function addCss($file)
    {
        if (is_array($file)) {
            $this->assignFile['css'] = array_merge($this->assignFile['css'], $file);
        } else {
            array_push($this->assignFile['css'], $file);
        }
        $this->assignFile['css'] = array_unique($this->assignFile['css']);
    }

    /**
     * 删除css文件
     * @param $file
     * @return bool
     */
    public function removeCss($file = null)
    {
        if (is_null($file)) {
            $this->assignFile['css']  = [];
            return true;
        }
        settype($file, 'array');
        $this->assignFile['css'] = array_filter($this->assignFile['css'], function ($path) use ($file){
            return !in_array($path, $file);
        });
        return true;
    }

    /**
     * 设置JS中的变量
     * @param string $name
     * @param string $value
     * @param bool $replace
     */
    public function setScriptData(string $name, $value = '', $replace = false)
    {
        if (isset($this->scriptVars[$name]) && is_array($this->scriptVars[$name]) && false === $replace) {
            $this->scriptVars[$name] = array_merge($this->scriptVars[$name], is_array($value) ? $value : [$value]);

        }  else {
            $this->scriptVars[$name] = $value;
        }
    }

    /**
     * 获取JS中的变量
     * @param string $name
     * @return array|mixed|string
     */
    public function getScriptData(string $name = '')
    {
        if (!empty($name)) {
            return isset($this->scriptVars[$name]) ? $this->scriptVars[$name] : '';
        }

        return $this->scriptVars;
    }

    /**
     * 添加alert提示消息
     * @param $message
     * @param string $type
     * @return $this
     */
    public function addAlert($message, $type = 'info')
    {
        $this->setScriptData('alert',[
            [
                'type' => $type,
                'msg' => $message
            ]
        ]);
        return $this;
    }


    /**
     * 设置需要渲染的页面文件
     * @param string $template
     * @param array $vars
     */
    public function fetch(string $template = '', array $vars = [])
    {
        $this->template = $this->parseTemplate($template);
        if (!empty($vars)) {
            ThinkView::assign($vars);
        }
    }

    public function getViewPath()
    {
        return !$this->config['view_path']
            ? app()->getAppPath() . $this->config['view_dir_name'] . $this->config['view_depr'] : $this->config['view_path'];
    }

    /**
     * 重构模板定位
     * @param string $template
     * @return mixed|string
     */
    public function parseTemplate(string $template = '')
    {
        if ($template && false !== strpos($template, '@')) {
            return $template;
        }
        if ('' != pathinfo($template, PATHINFO_EXTENSION)) {
            return $template;
        }

        $original = $template;
        $depr = $this->config['view_depr'];

        $path = $this->getViewPath();
        $woo_path = woo_path() . app('http')->getName() . $depr . $this->config['view_dir_name'] . $depr;

        if (0 !== strpos($template, '/')) {
            $template   = str_replace(['/', ':'], $depr, $template);
            $controller = $this->request->controller();

            if (strpos($controller, '.')) {
                $pos        = strrpos($controller, '.');
                $addon      = substr($controller, 0, $pos);
                $controller = $addon . '.' . Str::snake(substr($controller, $pos + 1));
            } else {
                $controller = Str::snake($controller);
            }
            if ($controller) {
                if ('' == $template) {
                    if (2 == $this->config['auto_rule']) {
                        $template = $this->request->action(true);
                    } elseif (3 == $this->config['auto_rule']) {
                        $template = $this->request->action();
                    } else {
                        $template = Str::snake($this->request->action());
                    }
                    $original = $template;
                    $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
                } elseif (false === strpos($template, $depr)) {
                    $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
                }
                if (isset($addon)) {
                    $addon_template = $addon . $depr . $original;
                }
            }
        } else {
            $template = str_replace(['/', ':'], $depr, substr($template, 1));
        }

        $file = $path . $template . '.' . $this->config['view_suffix'];
        if (is_file($file)) {
            return $file;
        }
        if (isset($addon_template)) {
            $file = $path . $addon_template . '.' . $this->config['view_suffix'];
            if (is_file($file)) {
                return $file;
            }
        }
        $woo_file = $woo_path . $template . '.' . $this->config['view_suffix'];
        if (is_file($woo_file)) {
            return $woo_file;
        }
        if (false === strpos($original, '/')) {
            $file = $path . $original . '.' . $this->config['view_suffix'];
            if (is_file($file)) {
                return $file;
            }
            $woo_file = $woo_path . $original . '.' . $this->config['view_suffix'];
            if (is_file($woo_file)) {
                return $woo_file;
            }
        }
        return '/' . $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    // ArrayAccess
    #[\ReturnTypeWillChange]
    public function offsetSet($name, $value)
    {
        if (is_string($name)) {
            $this->$name = $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($name): bool
    {
        return isset($this->$name);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($name)
    {
        unset($this->$name);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($name)
    {
        return $this->$name;
    }
}