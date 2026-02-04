<?php
declare (strict_types=1);

namespace woo\common\auth\driver;

use woo\common\auth\Driver;
use think\facade\Session as ThinkSession;

class Session extends Driver
{
    /**
     * 登录存储
     * @param int $id
     * @return mixed|string
     */
    public  function loginStorage(array $login)
    {
        ThinkSession::set($this->config['session_key'], $login);
        return ThinkSession::getId();
    }

    /**
     * 获取登录存储信息
     */
    public function getLoginStorage()
    {
        return ThinkSession::get($this->getEffectiveKey());
    }

    /**
     * 判断登录状态
     */
    public function isLogined()
    {
        $logined =  ThinkSession::has($this->getEffectiveKey());
        $this->request->setLogined($logined);
        return $logined;
    }

    /**
     * 删除登录状态
     * @return mixed|void
     */
    public function removeLoginStorage()
    {
        return ThinkSession::delete($this->getEffectiveKey());
    }

    protected function isKey($key)
    {
        if (isset($this->checkResult[$key])) {
            return $this->checkResult[$key];
        }
        $this->checkResult[$key] = ThinkSession::has($key);
        return $this->checkResult[$key];
    }
}