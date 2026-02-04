<?php
declare (strict_types = 1);

namespace app\common\model\traits;

use think\facade\Log;

trait WechatUserTrait
{

    /** 通过openid查询一条记录 */
    public function getByOpenid(string $openid): ?array
    {
        Log::debug('模型文件里查询微信用户: openid=' . $openid);
        $result = $this->where('openid', $openid)->find();
        Log::debug('模型文件里查询结果: ' . ($result ? '找到用户' : '未找到用户'));
        return $result;
    }
    /** 通过用户ID查询一条记录 */
    public function getByUserId(int $userId): ?array
    {
        Log::debug('模型文件里查询微信用户: user_id=' . $userId);
        $result = $this->where('user_id', $userId)->find();
        Log::debug('模型文件里查询结果: ' . ($result ? '找到用户' : '未找到用户'));
        return $result;
    }
}
