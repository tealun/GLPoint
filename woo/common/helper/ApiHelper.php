<?php
declare (strict_types=1);

namespace woo\common\helper;

use woo\common\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\Validate;
use woo\common\facade\Auth;

class ApiHelper
{
    protected $request;
    protected $params;
    protected $namespace;
    protected $powerRule;
    protected $reader;
    protected $store = [];
    protected $error = [];

    public function __construct()
    {
        $this->request = app()->request;
        $this->params = $this->request->getParams();
        if (empty($this->request->isAddon)) {
            $this->namespace = "app\\{$this->params['app_name']}\\controller\\" . (!$this->params['addon_name'] ? "" : $this->params['addon_name'] . "\\") . $this->params['controller'];
            $this->powerRule = strtolower(implode('_', [$this->params['app_name'], $this->request->controller(), $this->request->action()]));
        } else {
            $this->namespace = get_addons_class($this->params['addon_name'] , 'controller', $this->params['controller']);
            $this->powerRule = strtolower(implode('_', [$this->params['addon_name'], $this->params['controller'], $this->params['action']]));
        }
        $this->reader = new ApiReader($this->namespace);
    }

    /**
     * 验证参数规则 -- 只检测一级参数
     * @return bool
     */
    public function checkParam() :bool
    {
        if (empty($this->getActionParam())) {
            return true;
        }
        $apiInfo = $this->getActionApiInfo();
        if (empty($apiInfo['validate'])) {
            return true;
        }
        $data = array_merge($this->request->param() ?: [], $this->request->file() ?: []);
        if (true === $apiInfo['validate']) {
            $rules = [];
            $fields = [];
            $message = [];

            foreach ($this->getActionParam() as $item) {
                $rule = [];
                if (empty($item['name'])) {
                    continue;
                }
                if (!empty($item['validate'])) {
                    $rule = $item['validate'];
                }
                if (!empty($item['rsa']) && !empty($data[$item['name']])) {
                    $data[$item['name']] = Str::setDecrypt($data[$item['name']]);
                }
                if (!empty($item['require'])) {
                    if ($rule) {
                        if (is_array($rule) && !in_array('require', $rule)) {
                            array_unshift($rule, 'require');
                        } elseif (is_string($rule) && false === strpos($rule, 'require')) {
                            $rule = 'require|' . $rule;
                        }
                    } else {
                        $rule = ['require'];
                    }
                }
                if (empty($rule)) {
                    continue;
                }
                if (!empty($item['message'])) {
                    if (is_array($item['message'])) {
                        foreach ($item['message'] as $r => $m) {
                            $message[$item['name'] . '.' . $r] = $m;
                        }
                    } else {
                        $message[$item['name']] = $item['message'];
                    }
                }
                /*
                if ($item['type'] != 'file') {
                    $data[$item['name']] = $this->request->param($item['name']);
                }  else {
                    $data[$item['name']] = $this->request->file($item['name']);
                }
                */
                $rules[$item['name']] = $rule;
                $fields[$item['name']] = $item['title'] ?? Str::studly($item['name']);
            }
            if (empty($rules)) {
                return true;
            }
            $validate = new Validate();
            $validate->rule($rules, $fields)->message($message);
        } else {
            if (class_exists($apiInfo['validate'])) {
                $validate = new $apiInfo['validate']();
            } else {
                $this->forceError(__('class not exists') . ":" . $apiInfo['validate']);
                return false;
            }
        }

        $validate->extend('captcha', function ($value) use ($data){
            if (!isset($data['captcha_key'])) {
                return captcha_check($value);
            }
            return api_check_captcha($value, $data['captcha_key'] ?? '');
        });
        if ($validate->batch(true)->check($data)) {
            return true;
        }
        $this->forceError($validate->getError());
        return false;
    }

    /**
     * 检测接口的登录权限
     * @return bool
     */
    public function checkLogin() :bool
    {
        $info = $this->getActionApiInfo();
        
        // 调试日志：记录注解信息
        \think\facade\Log::info('ApiHelper.checkLogin() 检查:', [
            'controller' => $this->request->controller(),
            'action' => $this->request->action(),
            'info' => $info,
            'login_value' => $info['login'] ?? 'not_set',
            'login_type' => isset($info['login']) ? gettype($info['login']) : 'not_set'
        ]);
        
        // 修复：empty(false) 返回 true 的问题，需要严格检查 login 属性
        // 如果没有注解信息，或者 login 明确设置为 false，则不需要登录
        if (empty($info) || (isset($info['login']) && $info['login'] === false)) {
            \think\facade\Log::info('ApiHelper.checkLogin() 结果: 不需要登录');
            return true;
        }
        // 如果没有设置 login 属性，默认为 true（需要登录）
        // 如果设置为 true，也需要登录
        if (Auth::user()) {
            \think\facade\Log::info('ApiHelper.checkLogin() 结果: 已登录，允许访问');
            return true;
        }
        \think\facade\Log::info('ApiHelper.checkLogin() 结果: 未登录，拒绝访问');
        $this->forceError(['nologin' => '未登录']);
        return false;
    }

    /**
     * 检测接口的权限
     * @return bool
     */
    public function checkPower()
    {
        $info = $this->getActionApiInfo();
        if (empty($info) || empty($info['login']) || empty($info['power']) || !get_model_name('mapi.Power') || !get_installed_addons('mapi')) {
            return true;
        }

        $appList = $this->getAppList();
        if (!in_array(app('http')->getName() ,$appList)) {
            return true;
        }
        if(app()->isDebug()) {
            // 可以在调试阶段关闭权限验证
            if (!Config::get('wooauth.is_api_debug_power', true)) {
                return true;
            }
            $power = Db::name('mapiApi')
                ->where([
                    ['power_rule', '=', $this->powerRule]
                ])->find();
            if (empty($power)) {
                return true;
            }
        }
        $login = Auth::user();

        $group_id = $login[Str::snake($login['login_model']) . '_group_id'] ?? 0;

        if (Cache::has('mapi_power_content_for_' . $group_id)) {
            $content = Cache::get('mapi_power_content_for_' . $group_id);
        } else {
            $content = Db::name('mapiPower')
                ->where([
                    ['user_group_id', '=', $group_id]
                ])
                ->find();
            if ($content) {
                Cache::tag('mapi.Power')->set('mapi_power_content_for_' . $group_id, $content, 86400);
            }
        }
        if (empty($content)) {
            $this->forceError(['nopower' => '未授权']);
            return false;
        }
        $content = json_decode($content['content'], true);
        if (!in_array($this->powerRule, $content)) {
            $this->forceError(['nopower' => '权限不足']);
            return false;
        }
        return true;
    }

    protected function getAppList()
    {
        if (Cache::has('mapi_app_list')) {
            return Cache::get('mapi_app_list');
        }
        $list =  Db::name('mapiProject')->field(['id', 'app_name'])->select()->toArray();
        $list = Arr::combine($list, 'id', 'app_name');
        Cache::tag('mapi.Project')->set('mapi_app_list', $list);
        return $list;
    }

    /**
     * 检测接口的请求类型是否正确
     * @return bool
     */
    public function checkMethod() :bool
    {
        $method = trim($this->getActionApiInfo()['method'] ?? '');
        if (empty($method)) {
            return true;
        }
        if (strtoupper($method) === strtoupper($this->request->method())) {
            return true;
        }
        $this->forceError(['badMethod' => "请求方式错误：应该【{$method}】请求，实际【{$this->request->method()}】请求"]);
        return false;
    }

    /**
     * 检测接口是否拒绝访问
     * @return bool
     */
    public function checkForbidden() :bool
    {
        return boolval($this->getActionApiInfo()['isForbidden'] ?? false);
    }

    protected function getActionApiInfo(string $action = '') :array
    {
        if (!$action) {
            $action = $this->params['action'];
        }
        if (isset($this->store['apiInfo'][$action])) {
            return $this->store['apiInfo'][$action];
        }
        $info = $this->reader->getActionApiInfo($action);
        
        // 🔍 调试：记录读取的注解信息
        \think\facade\Log::info('ApiHelper.getActionApiInfo() 读取注解:', [
            'action' => $action,
            'controller' => $this->params['controller'] ?? 'unknown',
            'raw_info' => $info,
            'login_field' => $info['login'] ?? 'NOT_FOUND',
            'login_type' => isset($info['login']) ? gettype($info['login']) : 'NOT_SET'
        ]);
        
        return $this->store['apiInfo'][$action] = $info ? $info : [];
    }

    protected function getActionParam(string $action = '')
    {
        if (!$action) {
            $action = $this->params['action'];
        }
        if (isset($this->store['param'][$action])) {
            return $this->store['param'][$action];
        }
        $param = $this->reader->getActionParam($action);
        return $this->store['param'][$action] = $param ? $param : [];
    }

    protected function forceError($field, $error = '')
    {
        if (is_string($field)) {
            if (!empty($error)) {
                $this->error[$field] = $error;
            } else {
                $this->error[] = $field;
            }
        } elseif (is_array($field)) {
            $this->error = array_merge($this->error, $field);
        }
        return $this;
    }

    /**
     * 获取错误信息
     * @param string $field
     * @return array|mixed|string
     */
    public function getError(string $field = '')
    {
        if ($field) {
            return $this->error[$field] ?? '';
        }
        return $this->error;

    }
}