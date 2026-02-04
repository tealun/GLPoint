<?php
namespace woo\common\auth\driver;

use think\facade\Cookie;
use woo\common\auth\Driver;
use thans\jwt\facade\JWTAuth;

class Jwt extends  Driver
{
    /**
     * 登录存储
     * @param int $id
     * @return mixed|string
     */
    public  function loginStorage(array $login)
    {
        $this->logined = $login;
        $token = JWTAuth::builder([$this->config['session_key'] => $login]);
        Cookie::set('token', $token);
        return $token;
    }

    /**
     * 获取登录存储信息
     */
    public function getLoginStorage()
    {
        if ($this->logined) {
            return $this->logined;
        }
        try {
            $jwt = JWTAuth::auth();
            $key = $this->getEffectiveKey();
            if (!empty($jwt[$key])) {
                $this->logined = (array)$jwt[$key]->getValue();
                return $this->logined;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 判断登录状态
     */
    public function isLogined()
    {
        if ($this->logined) {
            return true;
        }
        try {
            $jwt = JWTAuth::auth();
            if (!empty($jwt[$this->getEffectiveKey()])) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除登录状态
     * @return mixed|void
     */
    public function removeLoginStorage()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return true;
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * 刷新Token
     * @return bool|string
     */
    public function refreshToken()
    {
        try {
            return JWTAuth::refresh();
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function isKey($key)
    {
        if (isset($this->checkResult[$key])) {
            return $this->checkResult[$key];
        }
        if ($this->logined) {
            return $this->checkResult[$key] = true;
        }
        try {
            $jwt = JWTAuth::auth();
            if (!empty($jwt[$key])) {
                return $this->checkResult[$key] = true;
            }
            return $this->checkResult[$key] =  false;
        } catch (\Exception $e) {
            return $this->checkResult[$key] =  false;
        }
        return $this->checkResult[$key];
    }
}