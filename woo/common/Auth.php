<?php
declare (strict_types=1);

namespace woo\common;

use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Env;
use think\facade\Log;
use think\facade\Session;
use think\facade\Event;
use woo\common\auth\traits\Business;
use woo\common\auth\traits\Admin;
use woo\common\helper\Arr;
use woo\common\helper\Str;

class Auth
{
    use Business;
    use Admin;
    /**
     * 请求对象
     * @var
     */
    protected $request;
    /**
     * 错误信息
     * @var
     */
    protected $error = '';

    /**
     * 默认配置
     * @var array
     */
    protected $defaultConfig = [
        'type' => 'session',// 验证方式 支持session和jwt(待完善)
        'model' => 'User',// 默认名
        'username_field' => 'username',// 登录用户名字段 支持：username|email 实现既可以用户名也可以邮箱登录
        'password_field' => 'password',// 登录密码字段
        'status_field'   => 'status', // 状态字段名
        'salt_field' => 'salt',// 密码加盐字段名
        'response_mode' => 'user/login',// 未登录的响应方式  格式：控制器/方法(ajax请求时自动是json，不会做重定向)、json、jsonp
        'response_json' => ["result" => "nologin", "message" => "未登录"],//未登录响应的json数据
        'forbid_response_json' => [],// 拒绝访问的json数据 如果为空 默认响应 403 错误
        'forbid_resonse_view' => '',// 拒绝访问的模板 如果为空 默认响应 403 错误
        'allow_from_all' => false,// false 表示应用都必须登录才允许访问 ；true 表示应用不登录也允许访问。可通过except排除
        'except' => [],// allow_from_all为false表示不登录也可以访问的方法，为true表示需要登录才可以访问的方法  如：User 表示整个控制器 ；User/login 表示指定方法；User/login:get 表示指定方法的指定请求方式
        'is_annotation_except' => false,// 登录状态是否允许使用注解
        'cacheExpire' => 600,// 缓存过期时间
        'check_status' => true,
    ];

    /**
     * 配置
     * @var array
     */
    protected $config = [];

    /**
     * 请求规则
     * @var string
     */
    protected $requestRule = '';
    protected $requestController = '';
    protected $requestAction = '';
    protected $requestParams = [];

    /**
     * 验证方式
     * @var
     */
    protected $driver;

    protected $reader;


    public function __construct(array $config = [], string $appName = '')
    {
        $this->request = app()->request;
        $this->requestController = $this->request->controller();
        $this->requestAction = strtolower($this->request->action());
        $this->requestRule = $this->requestController . '/' . $this->requestAction;
        $this->requestParams = $this->request->getParams();
        $this->setConfig($appName, $config);
        $driver = "\\woo\\common\\auth\\driver\\" . Str::studly($this->config['type']);
        $this->driver = new $driver($this->config);
    }

    /**
     * 账号登录
     * @param array $data 登录数据 如果为空 自动从post中获取
     * @return bool
     * @throws \think\Exception
     */
    public function login(array $data = [])
    {
        if (empty($this->config['model'])) {
            $this->error = '登录模型未配置';
            return false;
        }
        if (empty($this->config['username_field']) || empty($this->config['password_field'])) {
            $this->error = '登录模型账号字段或密码未配置';
            return false;
        }
        if (empty($data)) {
            $data = $this->request->post('', 'trim|strip_tags');
        }
        $username = $this->parseUsername($data);
        if (empty($username)) {
            $this->error = '登录账号不能为空';
            return false;
        }
        if (empty($data[$this->config['password_field']])) {
            $this->error = '登录密码不能为空';
            return false;
        }
        $password = $data[$this->config['password_field']];
        $denied = [];
        if (get_model_name('Denied') && empty($this->config['not_denied_check'])) {
            $denied = model('Denied')
                ->withoutGlobalScope(['business'])
                ->whereOr([
                    [
                        ['username', '=', $username],
                        ['ip', '=', $this->request->ip()]
                    ],
                    [
                        ['username', '=', $username],
                        ['ip', '=', '']
                    ]
                ])
                ->where(function ($query) {
                    $query->where([
                        ['model', '=', $this->config['model']],
                        ['is_verify', '=', 1],
                    ]);
                })
                ->order(['id' => 'DESC'])
                ->limit(1)
                ->find();
            if ($denied && $denied['expire'] >= time()) {
                $this->error = '连续登录错误，请休息' . intval(setting('user_denied_interval', 5)) . '分钟吧';
                return false;
            } elseif ($denied && $denied['expire'] == 0) {
                $this->error = '账号已被限制登录，重置密码可解除限制';
                return false;
            }
        }

        $error_count = $this->checkLoginError($username, $denied['expire'] ?? 0);
        if (is_int($error_count) && $error_count >= 5 && empty($this->config['not_denied_check'])) {
            $this->error = '连续登录错误，请休息' . intval(setting('user_denied_interval', 5)) . '分钟吧';
            return false;
        }

        if (empty($username)) {
            $this->error = '请输入用户名';
            $this->triggerLoginEvent('error', '未输入用户名', [
                'username' => $username,
                Str::snake($this->config['model']) . '_id' => 0
            ]);
            return false;
        }
        if (empty($password)) {
            $this->error = '请输入密码';
            $this->triggerLoginEvent('error', '未输入密码', [
                'username' => $username,
                Str::snake($this->config['model']) . '_id' => 0
            ]);
            return false;
        }
        try {
            $where = [
                [$this->config['username_field'], '=', $username]
            ];
            // 商家检查 非当前商家的账号无法登录
            if (!empty($this->request->business_id) && isset(model($this->config['model'])->form['business_id'])) {
                $where[] = ['business_id', '=', $this->request->business_id];
            }

            $find = model($this->config['model'])
                ->withoutGlobalScope(['business'])
                ->where($where)
                ->find();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->triggerLoginEvent('error', $this->error, [
                'username' => $username,
                Str::snake($this->config['model']) . '_id' => 0
            ]);
            return false;
        }
        if (empty($find)) {
            $this->error = '用户名或密码错误';
            $this->triggerLoginEvent('error', $this->error, [
                'username' => $username,
                Str::snake($this->config['model']) . '_id' => 0
            ]);
            if (is_int($error_count) && $error_count >= 1) {
                $this->error .= '，还有' . (5 - $error_count - 1) . '次机会';
            }
            return false;
        }
        if ($this->config['check_status'] && isset($find[$this->config['status_field']]) && $find[$this->config['status_field']] != 'verified') {
            if ($find[$this->config['status_field']] == 'unverified') {
                $this->error = '账号未激活';
            } elseif ($find[$this->config['status_field']] == 'banned') {
                $this->error = '账号已禁用';
            } else {
                $this->error = '未知状态';
            }
            $this->triggerLoginEvent('error', $this->error, [
                'username' => $username,
                Str::snake($this->config['model']) . '_id' => $find['id'] ?? 0
            ]);
            return false;
        }
        $salt = '';
        if (!empty($this->config['salt_field'])) {
            $salt = $find[$this->config['salt_field']] ?? '';
        }
        if ($find[$this->config['password_field']] === self::password($password, $salt)) {
            $this->triggerLoginEvent('success', '登录成功', [
                'username' => $username,
                Str::snake($this->config['model']) . '_id' => $find['id'] ?? 0
            ]);
            $this->request->setLogined(true);
            return $this->loginStorage([
                'id' => $find[model($this->config['model'])->getPk()],
                'model' => $this->config['model']
            ]);
        }
        $this->error = '用户名或密码错误';
        $this->triggerLoginEvent('error', $this->error, [
            'username' => $username,
            Str::snake($this->config['model']) . '_id' => 0
        ]);
        if (is_int($error_count) && $error_count >= 1) {
            $this->error .= '，还有' . (5 - $error_count - 1) . '次机会';
        }
        return false;
    }

    /**
     * 强制登录
     * 用于特殊场景你已自行验证允许登录了（你必须自行确定可以登录了），然后把主键id传入即可
     * @param int $id
     * @return bool
     */
    public function forceLogin(int $id, $type = '')
    {
        if (empty($this->config['model'])) {
            $this->error = '登录模型未配置';
            return false;
        }
        if (empty($this->config['username_field']) || empty($this->config['password_field'])) {
            $this->error = '登录模型账号字段或密码未配置';
            return false;
        }
        try {
            $model = model($this->config['model']);
            $find = $model
                ->withoutGlobalScope(['business'])
                ->where($model->getPk(), '=', $id)
                ->find();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
        if (empty($find)) {
            $this->error = '登录账号错误';
            return false;
        }
        if ($this->config['check_status'] && isset($find[$this->config['status_field']]) && $find[$this->config['status_field']] != 'verified') {
            if ($find[$this->config['status_field']] == 'unverified') {
                $this->error = '账号未激活';
            } elseif ($find[$this->config['status_field']] == 'banned') {
                $this->error = '账号已禁用';
            } else {
                $this->error = '未知状态';
            }
            $this->triggerLoginEvent('error', $this->error, [
                'username' => $find[$this->config['username_field']],
                Str::snake($this->config['model']) . '_id' => $find[$model->getPk()] ?? 0
            ]);
            return false;
        }

        $this->triggerLoginEvent('success', '登录成功', [
            'username' => $find[$this->config['username_field']],
            Str::snake($this->config['model']) . '_id' => $find[$model->getPk()] ?? 0,
            'type' => $type
        ]);
        $this->request->setLogined(true);
        return $this->loginStorage([
            'id' => $find[$model->getPk()],
            'model' => $this->config['model']
        ]);
    }

    /**
     * 同账号连续登陆失败统计
     * @param $username
     * @param $expire
     */
    protected function checkLoginError($username, $expire = 0)
    {
        $loginModel = $this->config['model'] . 'Login';
        if (!get_model_name($loginModel)) {
            return true;
        }
        $list = model($loginModel)
            ->withoutGlobalScope(['business'])
            ->where([
                ['username', '=', $username],
                ['create_time', '>=', max(time() - 600, $expire)],
            ])
            ->field(['id', 'username', 'is_success'])
            ->order(['id' => 'DESC'])
            ->limit(10)
            ->select()
            ->toArray();
        $count = 0;
        foreach ($list as $item) {
            if ($item['is_success']) {
                break;
            }
            $count++;
        }
        if ($count >= 5 && get_model_name('Denied')) {

            model('Denied')->save([
                'model' => $this->config['model'],
                'username' => $username,
                'ip' => $count <= 6 ? $this->request->ip() : '',
                'is_verify' => 1,
                'expire' => $count <= 8 ? time() + intval(setting('user_denied_interval', 5)) * 60 : 0
            ]);
        }
        return $count;
    }

    protected function triggerLoginEvent($type, $message, $data)
    {
        $data['summary'] = $type === 'success' ? $message : '失败：' . $message;
        $data['success'] = $type === 'success' ? true : false;
        Event::trigger($this->config['model'] . 'Login', $data);
    }

    /**
     * 退出登录
     * @return $this
     */
    public function logout()
    {
        $is = $this->isLogined();
        $this->request->setLogined(false);
        if (!$is) {
            $this->removeLoginStorage();
            return $this;
        }
        $storage = $this->getLoginStorage();

        $cacheKey = $storage['model'] . '_for_id_' . $storage['id'];
        if (empty($this->request->isAddon)) {
            $cacheKey = app('http')->getName() . '_' . $cacheKey;
        } else {
            $cacheKey = $this->request->addon . '_' .  $cacheKey;
        }
        Cache::delete($cacheKey);
        $this->removeLoginStorage();
        return $this;
    }

    /**
     * 获取登录用户信息
     * @param string $field
     * @param array $options
     * @param bool $force 是否强制查询数据库
     * @return bool
     * @throws \Exception
     */
    public function user(string $field = '', array $options = [], bool $force = false)
    {
        $is = $this->isLogined();
        if (!$is) {
            return false;
        }
        $storage = $this->getLoginStorage();
        if (!is_array($storage) || !isset($storage['id']) || !isset($storage['model'])) {
            return false;
        }
        $model = $storage['model'];// 登录的模型
        $id = $storage['id'];// 模型ID
        $allow_login_model = $this->config['allow_login_model'];
        $cacheKey = $model . '_for_id_' . $id;
        if (empty($this->request->isAddon)) {
            $cacheKey = app('http')->getName() . '_' . $cacheKey;
        } else {
            $cacheKey = $this->request->addon . '_' .  $cacheKey;
        }
        try {
            if (Cache::has($cacheKey) && !$force) {
                $user = Cache::get($cacheKey);
            } else {
                if (empty($options)) {
                    $options = $allow_login_model[$model] ?? [];
                }
                // AdminGroup改为对多对以后 为了兼容之前版本的处理
                if ($model == 'Admin' && !empty($options['withJoin'])) {
                    $options['with'] = $options['with'] ?? [];
                    if (in_array('AdminGroup', $options['withJoin'])) {
                        array_push($options['with'], 'AdminGroup');
                        $options['withJoin'] = array_diff($options['withJoin'], ['AdminGroup']);
                    } elseif (array_key_exists('AdminGroup', $options['withJoin'])) {
                        $options['with']['AdminGroup'] = $options['withJoin']['AdminGroup'];
                        unset($options['withJoin']['AdminGroup']);
                    }
                }
                $user = model($model)
                    ->withoutGlobalScope(['business'])
                    ->alias(Str::camel($model))
                    ->with(model($model)->parseWith($options['with'] ?? []))
                    ->withJoin(model($model)->parseWith($options['withJoin'] ?? []), 'LEFT')
                    ->where(Str::camel($model) . '.' . model($model)->getPk(), '=', $id)
                    ->find();

                if (!empty($user)) {
                    $user = $user->toArray();
                    $user['login_model'] = $model;
                    $user['login_foreign_key'] = Str::snake($model) . '_id';
                    $user['login_foreign_value'] = $id;
                    $tag = [$model];
                    if (!empty($options['withJoin'])) {
                        foreach ($options['withJoin'] as $key => $value) {
                            array_push($tag, is_numeric($key) ? (string)$value : $key);
                        }
                    }
                    if (!empty($user['business_id']) && empty($this->request->business_id)) {
                        $this->request->business_id = $user['business_id'];
                    }
                    if ($model == 'Admin') {
                        $user['admin_group_id'] = '';
                        if (!empty($user['AdminGroup'])) {
                            $user['admin_group_id'] = implode(',', Arr::combine($user['AdminGroup'], 'id', 'id'));
                        }
                    }
                    Cache::tag(model_cache_tag($tag))->set($cacheKey, $user, $this->config['cacheExpire'] ?? 60);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if (empty($user)) {
            return false;
        }

        if ($field) {
            return $user[$field] ?? '';
        }
        if ($this->config['password_field'] && isset($user[$this->config['password_field']])) {
            unset($user[$this->config['password_field']]);
        }
        if ($this->config['salt_field'] && isset($user[$this->config['salt_field']])) {
            unset($user[$this->config['salt_field']]);
        }

        return $user;
    }

    public function clearLoginCache()
    {
        $is = $this->isLogined();
        if (!$is) {
            return true;
        }
        $storage = $this->getLoginStorage();
        if (!is_array($storage) || !isset($storage['id']) || !isset($storage['model'])) {
            return true;
        }
        $model = $storage['model'];// 登录的模型
        $id = $storage['id'];// 模型ID
        $cacheKey = $model . '_for_id_' . $id;
        if (empty($this->request->isAddon)) {
            $cacheKey = app('http')->getName() . '_' . $cacheKey;
        } else {
            $cacheKey = $this->request->addon . '_' .  $cacheKey;
        }
        Cache::delete($cacheKey);
        return true;
    }

    public function writeRequestLog($code)
    {
        // 后台暂不写请求日志
        if (app('http')->getName() === 'admin') {
            return false;
        }
        if (empty(setting('do_is_request_log', false))) {
            return false;
        }

        $appName = !$this->request->isAddon ? app('http')->getName() : $this->request->addon;
        $writeList = setting('do_request_log_app_list', []);
        if (!in_array($appName, $writeList)) {
            return false;
        }
        if (empty($this->request->isAddon)) {
            $request['addon'] = strtolower($this->request->getParams()['addon_name']);
            $request['controller'] = Str::studly($this->request->getParams()['controller']);
            $request['action'] = strtolower($this->request->getParams()['action']);
            $namespace = "app\\" . app('http')->getName() . "\\" . "controller\\" . (!$request['addon'] ? "" : $request['addon'] . "\\") . $request['controller'];
        } else {
            $request['addon'] = $this->request->addon;
            $request['controller'] = $this->request->controller();
            $request['action'] = strtolower($this->request->action());
            $namespace = get_addons_class($request['addon'] , 'controller', $request['controller']);
        }
        $reflection = reflect($namespace);
        if ($reflection) {
            $classLog = $this->getReader()->getClassAnnotation($reflection, 'Log', false);
            if ($classLog) {
                // 整个控制器请求都不需要
                if (false === $classLog->value) {
                    return false;
                }
                if ($classLog->except && in_array($request['action'], array_map('strtolower', $classLog->except))) {
                    return false;
                }
                if ($classLog->only && !in_array($request['action'], array_map('strtolower', $classLog->only))) {
                    return false;
                }
                if ($classLog->remove) {
                    $remove = $classLog->remove;
                }
            }

            $methodLog = $this->getReader()->getMethodAnnotation($reflection, 'Log', $request['action'], true);
            if ($methodLog) {
                if (false === $methodLog->value) {
                    return false;
                }
                $methodLog->only = array_map('strtolower', $methodLog->only);
                $methodLog->except = array_map('strtolower', $methodLog->except);
                if ($methodLog->only && in_array('ajax', $methodLog->only) && !$this->request->isAjax()) {
                    return false;
                }
                if (in_array('ajax', $methodLog->only)) {
                    $key = array_search('ajax', $methodLog->only);
                    if (isset($methodLog->only[$key])) {
                        unset($methodLog->only[$key]);
                    }
                }
                $md = strtolower($this->request->method());
                if ($methodLog->only && !in_array($md, $methodLog->only)) {
                    return false;
                }
                if ($methodLog->except && in_array($md, $methodLog->except)) {
                    return false;
                }
                if ($methodLog->remove) {
                    $remove = array_merge($methodLog->remove, $remove ?? []);
                }
            }
        }

        try {
            model('RequestLog')->addLog(['code' => $code], $remove ?? []);
        } catch (\Exception $e) {
            Log::write('请求日志写入时：' . $e->getMessage(), 'error');
        }
    }

    /**
     * 后台日志记录
     */
    public function writeLog()
    {
        if (!in_array(app('http')->getName(), ['admin', 'business']) || !$this->user('id') || !Config::get('wooauth.is_log')) {
            return false;
        }
        $request['addon'] = strtolower($this->request->getParams()['addon_name']);
        $request['controller'] = Str::studly($this->request->getParams()['controller']);
        $request['action'] = strtolower($this->request->getParams()['action']);
        $namespace = "app\\" . app('http')->getName() . "\\" . "controller\\" . (!$request['addon'] ? "" : $request['addon'] . "\\") . $request['controller'];
        $reflection = reflect($namespace);
        if (!$reflection) {
            return false;
        }
        $classLog = $this->getReader()->getClassAnnotation($reflection, 'Log', false);
        if ($classLog) {
            // 整个控制器请求都不需要
            if (false === $classLog->value) {
                return false;
            }
            if ($classLog->except && in_array($request['action'], array_map('strtolower', $classLog->except))) {
                return false;
            }
            if ($classLog->only && !in_array($request['action'], array_map('strtolower', $classLog->only))) {
                return false;
            }
            if ($classLog->remove) {
                $remove = $classLog->remove;
            }
        }

        $methodLog = $this->getReader()->getMethodAnnotation($reflection, 'Log', $request['action'], true);
        if ($methodLog) {
            if (false === $methodLog->value) {
                return false;
            }
            $methodLog->only = array_map('strtolower', $methodLog->only);
            $methodLog->except = array_map('strtolower', $methodLog->except);
            if ($methodLog->only && in_array('ajax', $methodLog->only) && !$this->request->isAjax()) {
                return false;
            }
            if (in_array('ajax', $methodLog->only)) {
                $key = array_search('ajax', $methodLog->only);
                if (isset($methodLog->only[$key])) {
                    unset($methodLog->only[$key]);
                }
            }
            $md = strtolower($this->request->method());
            if ($methodLog->only && !in_array($md, $methodLog->only)) {
                return false;
            }
            if ($methodLog->except && in_array($md, $methodLog->except)) {
                return false;
            }
            if ($methodLog->remove) {
                $remove = array_merge($methodLog->remove, $remove ?? []);
            }
        }
        try {
            model('Log')->addLog([], $remove ?? []);
        } catch (\Exception $e) {
            Log::write('日志写入时：' . $e->getMessage(), 'error');
        }
    }

    /**
     * 禁止请求处理
     */
    public function forbidCheck()
    {
        if (empty($this->request->isAddon)) {
            $request['addon'] = strtolower($this->request->getParams()['addon_name']);
            $request['controller'] = Str::studly($this->request->getParams()['controller']);
            $request['action'] = strtolower($this->request->getParams()['action']);
            $namespace = "app\\" . app('http')->getName() . "\\" . "controller\\" . (!$request['addon'] ? "" : $request['addon'] . "\\") . $request['controller'];
        } else {
            $request['addon'] = $this->request->addon;
            $request['controller'] = $this->request->controller();
            $request['action'] = strtolower($this->request->action());
            $namespace = get_addons_class($request['addon'] , 'controller', $request['controller']);
        }
        $reflection = reflect($namespace);

        if (!$reflection) {
            return false;
        }
        $classFor = $this->getReader()->getClassAnnotation($reflection, 'Forbid', false);

        if ($classFor) {
            $main_methods = [
                'index', 'create', 'modify', 'delete', 'batchDelete','sort','updateSort','resetSort','detail','deleteIndex','restore','batchRestore','forceDelete', 'forceBatchDelete','ajaxSwitch','antispam'
            ];
            $classFor->only = array_map('strtolower', $classFor->only);
            $main_methods = array_diff($main_methods, $classFor->only);

            $main_methods = array_map('strtolower', $main_methods);
            if ($classFor->nodebug && !Env::get('APP_DEBUG')) {
                return true;
            }
            if ($classFor->value && in_array($request['action'], $main_methods) && $request['action'] != 'index') {
                return true;
            }
            if ($classFor->only && !in_array($request['action'], $classFor->only)) {
                return true;
            }
            if ($classFor->except && in_array($request['action'], array_map('strtolower', $classFor->except))) {
                return true;
            }
        }
        $methodFor = $this->getReader()->getMethodAnnotation($reflection, 'Forbid', $request['action'], false);

        if (!$methodFor) {
            return false;
        }
        $methodFor->only = array_map('strtolower', $methodFor->only);
        $methodFor->except = array_map('strtolower', $methodFor->except);
        if ($methodFor->only && in_array('ajax', $methodFor->only) && !$this->request->isAjax()) {
            return true;
        }
        if (in_array('ajax', $methodFor->only)) {
            $key = array_search('ajax', $methodFor->only);
            if (isset($methodFor->only[$key])) {
                unset($methodFor->only[$key]);
            }
        }
        $md = strtolower($this->request->method());
        if ($methodFor->nodebug && !Env::get('APP_DEBUG')) {
            return true;
        }
        if ($methodFor->only && !in_array($md, $methodFor->only)) {
            return true;
        }
        if ($methodFor->except && in_array($md, $methodFor->except)) {
            return true;
        }
        return false;
    }

    /**
     * 拒绝响应
     */
    public function forbidResponse()
    {
        $reponse_type = $this->config['response_mode'];
        if (empty($reponse_type)) {
            if ($this->config['type'] == 'jwt') {
                $reponse_type = 'json';
            } else {
                $reponse_type = 'view';
            }
        }
        if ($this->request->isAjax()) {
            $reponse_type = 'json';
        }
        switch ($reponse_type) {
            case 'json':
                if (!empty($this->config['forbid_response_json'])) {
                    return json($this->config['forbid_response_json']);
                }
                break;
            case 'jsonp':
                if (!empty($this->config['forbid_response_json'])) {
                    return jsonp($this->config['forbid_response_json']);
                }
            default:
                if (!empty($this->config['forbid_resonse_view'])) {
                    return view($this->config['forbid_resonse_view']);
                }
        }
    }

    /**
     * 权限判断
     */
    public function checkPower()
    {
        if ('admin' === app('http')->getName()) {
            if (true === $this->adminPower()) {
                return true;
            }
            return $this->request->isAjax() ? $this->powerResponseJson(): $this->powerResponseRedirect();
        }

        if ('business' === app('http')->getName()) {
            if (true === $this->businessPower()) {
                return true;
            }
            return $this->request->isAjax() ? $this->powerResponseJson(): $this->powerResponseRedirect();
        }
        return true;
    }

    protected function getMethodPs(\ReflectionClass $reflection, string $method)
    {
        $result = $this->getReader()->getMethodAnnotation($reflection, 'Ps', $method);
        if ($result && $result->as) {
            return $this->getMethodPs($reflection, $result->as);
        } elseif ($result && false === $result->value) {
            return true;
        }
        return $method;
    }

    public function adminRoutePower($route = '')
    {
        if (!in_array(app('http')->getName(), ['admin', 'business'])) {
            return true;
        }
        if (empty($route)) {
            return true;
        }
        if (is_array($route)) {
            if (empty(trim($route['controller']))) {
                return true;
            }
            if (empty($route['action'])) {
                $route['action'] = 'index';
            }
            if (empty($route['addon'])) {
                $route['addon'] = '';
            }
            return app('http')->getName() == 'admin' ? $this->adminPower($route) : $this->businessPower($route);
        }
        if (Str::startsWith($route, 'http://') || Str::startsWith($route, 'https://')) {
            if (Str::startsWith($route, $this->request->domain())) {
                $route = substr($route, mb_strlen($this->request->domain()));
            } else {
                return true;
            }
        }
        if (Str::startsWith($route, $this->request->appRoot())) {
            $route = substr($route, mb_strlen($this->request->appRoot()));
        }
        if ($route[0] == '/') {
            $route = substr($route, 1);
        }
        if (strpos($route, '.' . Config::get('route.url_html_suffix')) !== false) {
            $route = substr($route, 0, strpos($route, '.' . Config::get('route.url_html_suffix')));
        }

        $array = array_diff(explode('/', $route), ['']);
        if (empty($array)) {
            return true;
        }
        $route = [];
        if (false === strpos($array[0], '.')) {
            $route['addon'] = '';
            $route['controller'] = $array[0];
        } else {
            list($addon, $controller) = explode('.', $array[0]);
            $route['addon'] = $addon;
            $route['controller'] = $controller;
        }
        $route['action'] = $array[1] ?? 'index';
        return  app('http')->getName() == 'admin' ? $this->adminPower($route) : $this->businessPower($route);
    }

    /**
     * 判断登录状态是否可以访问
     * @return bool
     */
    public function loginRequired($config = [])
    {
        $this->config = array_merge($this->config, $config);
        $is = $this->isLogined();
        if ($is) {
            $storage = $this->getLoginStorage();
            $allow_login_model = array_keys($this->config['allow_login_model']);
            if (app('http')->getName() === 'admin') {
                $allow_login_model = ['Admin'];
            }
            if (in_array($storage['model'], $allow_login_model)) {
                return true;
            }
            if (app('http')->getName() === 'admin') {
                $this->logout();
            }
        }
        if (!$this->config['allow_from_all']) {
            $result = $this->loginExcept();

            if (true === $result) {
                return true;
            }
            if ($this->config['is_annotation_except']) {
                $result = $this->loginAnnotationExcept();
                if (true === $result) {
                    return true;
                }
            }
        } else {
            $result = $this->loginExcept();
            if (false === $result) {
                if ($this->config['is_annotation_except']) {
                    $result = $this->loginAnnotationExcept();
                    if (false === $result) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }

        $reponse_type = $this->config['response_mode'];
        if (empty($reponse_type)) {
            if ($this->config['type'] == 'jwt') {
                $reponse_type = 'json';
            } else {
                $reponse_type = 'redirect';
            }
        }

        switch ($reponse_type) {
            case 'json':
                return $this->loginResponseJson();
                break;
            case 'jsonp':
                return $this->loginResponseJsonp();
            default:
                if ($this->request->isAjax()) {
                    return $this->loginResponseJson();
                } else {
                    return $this->loginResponseRedirect();
                }
        }
    }

    protected function loginAnnotationExcept()
    {
        if (empty($this->request->isAddon)) {
            $controller = 'app\\' . app('http')->getName() . '\\' . 'controller\\' . str_replace('.', '\\', $this->request->controller());
        } else {
            $controller = get_addons_class($this->request->addon, 'controller', $this->request->controller());
        }

        $reflection = reflect($controller);
        if ($reflection) {
            $reader = new Annotation();
            $annotation = $reader->getClassAnnotation($reflection, 'Except');
            if ($annotation) {
                if (empty($annotation->action)) {
                    return true;
                }
                if (in_array($this->requestAction, $annotation->action)) {
                    return true;
                }
            }
            $annotation = $reader->getMethodAnnotation($reflection, 'Except', $this->requestAction);
            if ($annotation) {
                if (empty($annotation->method)) {
                    return true;
                }
                if (in_array(strtolower($this->request->method()), $annotation->method)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function loginExcept()
    {
        if (
            in_array(strtolower($this->requestRule), $this->config['except']) ||
            in_array(strtolower($this->requestController), $this->config['except']) ||
            in_array(strtolower($this->requestRule . ':' . strtolower($this->request->method())), $this->config['except'])
        ) {
            return true;
        }
        return  false;
    }

    protected function loginResponseRedirect()
    {
        if (empty($this->request->isAddon)) {
            return redirect((string)url($this->config['response_mode'], ['url' => $this->request->url()]));
        } else {
            if (0 === stripos($this->config['response_mode'], 'http')) {
                return redirect((string)url($this->config['response_mode'], ['url' => $this->request->url()]));
            } elseif (false !== stripos($this->config['response_mode'], '://')) {
                return redirect((string)addons_url($this->config['response_mode'], ['url' => $this->request->url()]));
            }
        }
        return redirect((string)url($this->config['response_mode'], ['url' => $this->request->url()]));
    }

    protected function loginResponseJson()
    {
        return json($this->config['response_json']);
    }

    protected function loginResponseJsonp()
    {
        return jsonp($this->config['response_json']);
    }

    protected function powerResponseRedirect()
    {
        Session::set('message', [
            'type' => 'error',
            'redirect' => ['close' => ['title' => '关闭窗口', 'class' => 'layui-btn-danger']],
            'message' => '您当前没有权限访问该页面',
            'auto' => 3,
        ]);
        return redirect((string)url('redirectMessage'));
    }

    protected function powerResponseJson()
    {
        return json(["result" => "nopower", "message" => "您当前没有权限访问该操作"]);
    }

    protected function parseUsername(array $data)
    {
        if (false === strpos($this->config['username_field'], '|')) {
            return $data[$this->config['username_field']] ?? '';
        }

        $fields = explode('|', $this->config['username_field']);

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                return $data[$field];
            }
        }
        return '';
    }

    protected function getReader()
    {
        if ($this->reader) {
            return $this->reader;
        }
        $this->reader = new Annotation();
        return $this->reader;
    }

    /**
     * 处理当前配置
     * @param string $appName
     * @param array $config
     */
    protected function setConfig(string $appName = '', array $config = [])
    {
        $appName = strtolower($appName ?: app('http')->getName());
        if (empty($this->request->isAddon)) {
            if (!empty($this->request->getParams()['addon_name']) && !empty(Config::get('wooauth.apps')[$appName][$this->request->getParams()['addon_name']])) {
                $this->config = array_merge($this->defaultConfig, Config::get('wooauth.apps')[$appName][$this->request->getParams()['addon_name']] ?? [], $config);
            } else {
                $this->config = array_merge($this->defaultConfig, Config::get('wooauth.apps')[$appName] ?? [], $config);
            }
        } else {
            $this->config = array_merge($this->defaultConfig, Config::get('wooauth.apps')['addon.' . $this->request->addon] ?? [], $config);
        }
        $this->config['model'] = Str::studly($this->config['model']);

        $this->config['session_key'] = $this->getSessionKey();

        $this->config['allow_login_model'] = Arr::normalize(array_merge([$this->config['model'] => $this->config['user_options'] ?? []], $this->config['allow_login_model'] ?? []));

        settype($this->config['except'], 'array');
        $this->config['except'] = array_map('strtolower', $this->config['except']);

        if (!$this->config['allow_from_all']) {
            $mode = strtolower($this->config['response_mode']);
            if (!in_array($mode, $this->config['except']) && !in_array($mode, ['json', 'jsonp'])) {
                array_push($this->config['except'], $mode);
            }
        }
    }

    protected function getSessionKey($model = '')
    {
        if (empty($model)) {
            $model = $this->config['model'];
        }
        return md5($model . (Config::get('wooauth')['session_key'] ?? 'abcxyz666'));
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 获取配置
     * @param string $key
     * @return array|mixed|string
     */
    public function getConfig(string $key = '')
    {
        if ($key) {
            return $this->config[$key] ?? '';
        }
        return $this->config;
    }

    /**
     * 加密算法
     * @param string $password 加密字符串
     * @param string $salt 加盐字符串
     * @return string
     */
    public static function password(string $password, string $salt = '')
    {
        return md5(substr($password, 0, 2) . $salt . md5($password) . substr($password, -2));
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this->driver, $method)) {
            return $this->driver->$method(...$arguments);
        }
    }
}