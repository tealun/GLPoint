<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\BaseController;
use app\api\library\Auth;
use think\facade\Log;

class Base extends BaseController
{
    /**
     * 当前登录用户
     */
    protected $user = null;

    /**
     * 初始化
     */
    protected function initialize()
    {
        parent::initialize();
        
        // 获取当前登录用户
        try {
            $this->user = Auth::getUser();
        } catch(\Exception $e) {
            Log::error('获取用户信息失败:' . $e->getMessage());
        }
    }
    
    /**
     * 返回JSON数据
     */
    protected function json($code = 200, $msg = 'ok', $data = null): \think\Response
    {
        $result = [
            'code' => $code,
            'msg' => $msg
        ];

        if (!is_null($data)) {
            $result['data'] = $data;
        }

        return json($result);
    }

    /**
     * 成功返回
     */
    protected function success($msg = 'ok', $data = null): \think\Response 
    {
        return $this->json(200, $msg, $data);
    }

    /**
     * 错误返回
     */
    protected function error($msg = 'error', $code = 400): \think\Response
    {
        return $this->json($code, $msg);
    }
}
