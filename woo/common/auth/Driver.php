<?php
declare (strict_types=1);

namespace woo\common\auth;

use think\facade\Config;

abstract class Driver
{
    /**
     * 当前配置
     * @var array
     */
    protected $config;
    /**
     * 请求
     * @var \think\Request
     */
    protected $request;

    protected $logined = [];

    protected $sessionKey = null;

    protected $checkResult = [];

    public function __construct(array $config)
    {
        $this->request = app()->request;
        $this->config = $config;
    }

    /**
     * 登录存储
     * @param int $id
     * @return mixed
     */
    public abstract function loginStorage(array $login);

    /**
     * 获取登录存储信息
     */
    public abstract function getLoginStorage();

    /**
     * 判断登录状态
     */
    public abstract function isLogined();

    /**
     * 删除登录状态
     * @return mixed
     */
    public abstract function removeLoginStorage();

    /**
     * 判断指定键是否登录
     * @param $key
     * @return mixed
     */
    protected abstract function isKey($key);

    protected function getEffectiveKey()
    {
        if (!empty($this->sessionKey)) {
            return $this->sessionKey;
        }
        $checkList = [$this->config['model']];
        if ($this->isKey($this->config['session_key'])) {
            return $this->sessionKey = $this->config['session_key'];
        }
        foreach ($this->config['allow_login_model'] as $model => $info) {
            if (in_array($model, $checkList)) {
                continue;
            }
            $checkList[] = $model;
            $key = $this->getSessionKey($model);
            if ($this->isKey($key)) {
                return $this->sessionKey = $key;
            }
        }
        return $this->sessionKey = $this->config['session_key'];
    }

    protected function getSessionKey($model = '')
    {
        if (empty($model)) {
            $model = $this->config['model'];
        }
        return md5($model . (Config::get('wooauth')['session_key'] ?? 'abcxyz666'));
    }
}