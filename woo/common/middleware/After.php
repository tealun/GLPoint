<?php
declare (strict_types=1);

namespace woo\common\middleware;

use woo\common\Callback;
use think\facade\Session;
use think\facade\View;
use think\facade\Env;
use think\facade\Config;

class After
{
    protected $sensitiveData = [];

    protected $isSensitive = false;

    protected $sensitiveReplace = '';

    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        // 最后回调
        if (app()->exists(Callback::class)) {
            app(Callback::class)->after();
        }

        $this->sensitiveData = get_sensitive();
        $this->isSensitive = setting('do_is_sensitive', false);
        $this->sensitiveReplace = setting('do_sensitive_replace', '');

        if ($response instanceof \think\response\Html) {
            if ($request->isGet() && !$request->isAjax() && !isset($request->isNotStore)) {
                Session::set(app('http')->getName()  . '_last_url', $request->url(true));
            }
            $assign = app(\woo\common\View::class);
            View::assign('woo_static_links', $assign->getStaticLinks());
            $woo_script_vars = $assign->getScriptData();
            View::assign('woo_script_vars', $woo_script_vars ? json_encode($woo_script_vars) : '{}');

            $template = $assign->getTemplate();
            if (!(null === $template)) {
                $old_data = $response->getData();
                $fetch = $this->replaceSensitive(View::fetch($template, get_object_vars($assign)));
                $response->content((is_string($old_data) && Env::get('app_debug') ? $old_data : '')
                    . $fetch);

                if (!empty($request->isWriteStatic) && $request->isGet()) {
                    $this->writeStatic($fetch, $request);
                }
            } else {
                $response->content($this->replaceSensitive($response->getData()));
            }
        } elseif (
            $response instanceof \think\response\Json ||
            $response instanceof \think\response\Jsonp ||
            $response instanceof \think\response\View
        ) {
            $response->data($this->replaceSensitive($response->getData()));
        }

        if (is_woo_installed() && Config::get('wooauth.is_request_log', false)) {
            app(Config::get('wooauth.handler'))->writeRequestLog($response->getCode());
        }
        return $response;
    }

    protected function replaceSensitive($content)
    {
        if (!$this->isSensitive || app('http')->getName() == 'admin') {
            return $content;
        }
        if (!is_string($content) && !is_array($content)) {
            return $content;
        }
        if (is_array($content)) {
            foreach ($content as &$item) {
                $item = $this->replaceSensitive($item);
            }
            return $content;
        }
        return preg_replace($this->sensitiveData, (string)$this->sensitiveReplace, $content);
    }

    protected function writeStatic($content, $request)
    {
        if (Env::get('app_debug')) {
            return false;
        }
        if (strpos($content, '</head>') === false) {
            return false;
        }
        $filename = ($request->isMobile ? 'wap_' : 'pc_') . md5($request->url(true)) . '.html';
        $dirpath = runtime_path() . 'static' . DIRECTORY_SEPARATOR;
        if (is_file($dirpath . $filename)) {
            $static = file_get_contents($dirpath . $filename);
            preg_match('/<!--<<<URL:(.*?)\|\|EXPIRE:(.*?)>>>-->/', $static, $cache);
            if ($cache[2] > time()) {
                return false;
            }
        }
        if (!is_dir($dirpath)) {
            @mkdir($dirpath, 0777, true);
        }
        @file_put_contents($dirpath . $filename,
            '<!--<<<URL:' . $request->url(true) . '||EXPIRE:' . (time() + intval(cms_setting('default_static_expire', 600))) . '>>>-->' . $content);
        if (run_mode() === 'swoole') {
            app('swoole.server')->reload();
        }
    }
}