<?php
declare (strict_types=1);

namespace woo\common;

use think\Request as ThinkRequest;
use think\file\UploadedFile;

class Request extends ThinkRequest
{
    protected $wooParams = [];

    protected $isLogined = false;

    public function getParams(bool $force = false)
    {
        if (!empty($this->wooParams) && !$force && !empty($this->wooParams['controller'])) {
            return $this->wooParams;
        }
        $urlArgs = $this->getArgs();
        if (isset($urlArgs['addon'])) {
            unset($urlArgs['addon']);
        }
        if (isset($urlArgs['controller'])) {
            unset($urlArgs['controller']);
        }
        $controller_part = explode('.', $this->controller());
        if (empty($controller_part[1])) {
            $controller_part[1] = $controller_part[0];
            $controller_part[0] = '';
        }
        $controller = parse_name(array_pop($controller_part), 1);

        // url相关信息
        $this->wooParams = [
            'app_name' => app('http')->getName(),// 当前应用名
            'controller' => $controller, // 当前控制器名
            'addon_name' => $this->addon ?? parse_name(trim($controller_part[0])), // 当前插件名
            'action' => $this->action(),// 当前方法名
            'args' => $urlArgs // 当前url参数
        ];
        return $this->wooParams;
    }


    /**
     * 获取URL上的参数，包含Pathinfo的和？的
     */
    public function getArgs()
    {
        $filter = ['trim', 'strip_tags'];
        $url_args = array_merge(
            $this->route('', '', $filter),
            $this->get('', '', $filter)
        );
        return $url_args;
    }

    /**
     * 获取URL访问根地址 重写核心root方法
     * @access public
     * @param  bool $complete 是否包含完整域名
     * @return string
     */
    public function root(bool $complete = false): string
    {
        $file = $this->baseFile();

        if ($file && 0 !== strpos($this->url(), $file)) {
            $file = str_replace('\\', '/', dirname($file));
        }
        if (strtolower(substr($file, -4)) == '.php') {
            $file = dirname($file);
        }
        if (substr($file, -1) != '/') {
            $file = $file . '/';
        }
        if (strpos($file, "\\") === 0) {
            $file = substr($file,1);
        }
        return $complete ? $this->domain() . $file : $file;
    }

    public function appRoot(bool $complete = false): string
    {
        return parent::root($complete);
    }

    public function isMicroMessenger(): bool
    {
        return strpos($this->header('user_agent', ''), 'MicroMessenger') !== false;
    }

    /**
     * 覆盖官方的dealUploadFile方法 修复swoole下批量上传的问题
     * @param array $files
     * @param string $name
     * @return array
     * @throws \think\Exception
     */
    protected function dealUploadFile(array $files, string $name): array
    {
        $array = [];
        foreach ($files as $key => $file) {
            if (isset($file['name']) && is_array($file['name'])) {
                $item  = [];
                $keys  = array_keys($file);
                $count = count($file['name']);

                for ($i = 0; $i < $count; $i++) {
                    if ($file['error'][$i] > 0) {
                        if ($name == $key) {
                            $this->throwUploadFileError($file['error'][$i]);
                        } else {
                            continue;
                        }
                    }

                    $temp['key'] = $key;

                    foreach ($keys as $_key) {
                        $temp[$_key] = $file[$_key][$i];
                    }

                    $item[] = new UploadedFile($temp['tmp_name'], $temp['name'], $temp['type'], $temp['error']);
                }
                $array[$key] = $item;
            } elseif (is_numeric(array_keys($file)[0])) {
                $item = [];
                foreach ($file as $temp) {
                    $item[] = new UploadedFile($temp['tmp_name'], $temp['name'], $temp['type'], $temp['error']);
                }
                $array[$key] = $item;
            } else {
                if ($file instanceof File) {
                    $array[$key] = $file;
                } else {
                    if ($file['error'] > 0) {
                        if ($key == $name) {
                            $this->throwUploadFileError($file['error']);
                        } else {
                            continue;
                        }
                    }

                    $array[$key] = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['error']);
                }
            }
        }
        return $array;
    }

    public function setLogined($is = false)
    {
        $this->isLogined = $is;
    }

    public function getLogined()
    {
        return $this->isLogined;
    }
}