<?php
declare (strict_types = 1);

namespace woo\common\controller\traits;

use think\App;
use think\facade\Session;
use think\facade\Env;
use woo\common\View;
use woo\common\annotation\Ps;


trait Stand
{
    /**
     * Request实例
     * @var \think\Request
     */
    public $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 系统View对象
     */
    public $assign;

    /**
     * 当前请求url相关信息 app 控制器 插件名 方法 参数
     */
    public $params = [];

    /**
     * 当前请求url参数
     */
    public $args = [];

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        $this->assign = app(View::class);
        $this->initialize();
    }

    protected function initialize()
    {}

    /**
     * 消息提示
     * @param string $msg 消息
     * @param string $type  类型 success error info warm
     * @param array $redirects 跳转连接
     * @param int $auto 自动跳转 秒数
     * @return string|\think\response\Json|\think\response\Redirect|void
     * @throws \Exception
     */
    protected function message(string $msg = '',string $type = 'success', array $redirects = [],int $auto = 3)
    {
        if (in_array($msg,  ['error', 'success', 'warn', 'info', 'warm'])) { // warm 历史错误 不想改了
            list($msg, $type) = [$type, $msg];
        }

        if ($this->request->isAjax()) {
            return $this->ajax($type, $msg, $redirects);
        }

        if (empty($redirects)) {
            $redirects = [
                'back' => true
            ];
        }

        if (!array_key_exists('back', $redirects)) {
            $redirects['back'] = true;
        }

        if (false === $redirects['back']) {
            unset($redirects['back']);
        }

        $assign_redirects = [];
        foreach ($redirects as $title => $url) {
            if (is_string($url) || is_bool($url) || is_object($url) || (is_array($url) && !isset($url['url']))) {
                $url = [
                    'url' => $url
                ];
            }
            if (empty($url['url'])) {
                continue;
            }
            if (is_array($url['url'])) {
                $url['url'] = call_user_func_array('url', $url['url']);
            }
            if (is_object($url['url'])) {
                $url['url'] = (string) $url['url'];
            }
            if ($title == 'close') {
                if (empty($url['class'])) {
                    $url['class'] = 'btn-25';
                }
                if (!isset($url['title'])) {
                    $url['title'] = '关闭窗口';
                }
            }
            if ($title == 'back') {
                if (true === $url['url']) {
                    $url['url'] = Session::has(app('http')->getName()  . '_last_url') ? Session::get(app('http')->getName()  . '_last_url') : "javascript:window.history.go(-1);";
                }
                if (empty($url['class'])) {
                    $url['class'] = 'btn-23';
                }
                if (!isset($url['title'])) {
                    $url['title'] = '返回上页';
                }
            }
            if (empty($url['class'])) {
                $url['class'] =  !$this->assign->darkMode? 'btn-38': 'btn-35';
            }
            if (!isset($url['title'])) {
                $url['title'] = $title;
            }

            $assign_redirects[$title] = $url;
        }
        $this->assign->data = array(
            'type' => $type,
            'redirect' => $assign_redirects,
            'message' => $msg,
            'auto' => $auto,
            'args' => $this->args
        );

        if (ENV::get('APP_DEBUG')) {
            return $this->fetch('message');
        }
        Session::set('message', $this->assign->data);
        return redirect((string) url('redirectMessage'));
    }

    protected function success(string $message = '成功', $redirects = [])
    {
        return $this->message($message, 'success', $redirects);
    }

    protected function error(string $message = '失败', $redirects = [])
    {
        return $this->message($message, 'error', $redirects);
    }


    protected function redirect($url)
    {
        if (!is_object($url)) {
            $url = is_array($url) ? $url : func_get_args();
            $url = url(...$url);
        }
        return redirect((string) $url);
    }

    /**
     * @Ps(false)
     */
    public function redirectMessage()
    {
        $this->addTitle('系统消息');
        $this->assign->data = Session::get('message');
        return $this->fetch('message');
    }

    /**
     * json返回数据
     * @param string $result
     * @param string $message
     * @param array $data
     * @return \think\response\Json
     */
    protected function ajax($result = 'success', string $message = '', $data = [])
    {
        return ajax($result, $message, $data);
    }

    /**
     * jsonp返回数据
     * @param string $result
     * @param string $message
     * @param array $data
     * @return \think\response\Json
     */
    protected function jsonp(string $result = 'error', string $message = '', $data = [])
    {
        settype($data, 'array');
        return jsonp([
            'result' => $result,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 加载模型
     */
    protected function loadModel($model)
    {
        return model($model, $this->params['app_name']);
    }

    /**
     * 追加网页标题
     */
    protected function addTitle($title)
    {
        if (!$title) {
            return;
        }
        settype($this->assign->meta['title'], 'array');
        array_unshift($this->assign->meta['title'], $title);
    }

    /**
     * 追加网页关键词
     */
    protected function addKeywords($title)
    {
        if (!$title) {
            return;
        }
        settype($this->assign->meta['keywords'], 'array');
        array_unshift($this->assign->meta['keywords'], $title);
    }

    /**
     * 追加网页网站描述
     */
    protected function addDescription($title)
    {
        if (!$title) {
            return;
        }
        settype($this->assign->meta['description'], 'array');
        array_unshift($this->assign->meta['description'], $title);
    }

    /**
     * 重置网页标题
     */
    protected function setTitle($title, $type = 'title')
    {
        switch ($type) {
            case 'title':
                $this->assign->meta['title'] = [$title];
                break;
            case 'operation' || $type === true:
                $this->assign->title[$type] = $title;
                $this->addTitle($title);
                break;
            default:
                $this->assign->title[$type] = $title;
                break;
        }
    }

    /**
     * 重置网页关键词
     */
    protected function setKeywords($title)
    {
        $this->assign->meta['keywords'] = [$title];
    }

    /**
     * 重置网页关键词
     */
    protected function setDescription($title)
    {
        $this->assign->meta['description'] = [$title];
    }

    /**
     * 解析和获取模板内容 用于输出
     * @access public
     * @param string $template 模板文件名或者内容
     * @param array $vars 模板变量
     * @return string
     * @throws \Exception
     */
    protected function fetch(string $tempate = '', array $vars = [])
    {
        $this->assign->args = $this->args ?? [];
        if (!isset($this->assign->topBar)) {
            $this->assign->topBar = $this->local['topBar'] ?? false;
        }
        if (!isset($this->assign->watermark)) {
            $this->assign->watermark = $this->local['watermark'] ?? false;
        }

        if (
            !empty($this->local['rsa'])
            && setting('do_is_rsa', true)
            && !empty($this->app->config->get('woo.rsa_public'))
            && extension_loaded('openssl')
        ) {
            $this->assign->addJs('jsencrypt.min.js', true);
            $this->assign->setScriptData('rsa_public', $this->app->config->get('woo.rsa_public'));
            $this->assign->setScriptData('rsa_field', $this->local['rsa']);
        }
        $this->assign->fetch($tempate, $vars);
        return true;
    }

    /**
     * 模板变量赋值
     * @access public
     * @param string|array $name 模板变量
     * @param mixed $value 变量值
     * @return $this
     */
    protected function assign($name, $value = null)
    {
        $this->assign[$name] = $value;
    }
}