<?php
namespace app\common\model;

use think\Model;

/**
 * 订阅消息授权模型
 */
class SubscribeAuth extends Model
{
    protected $name = 'subscribe_auth';
    
    // 设置字段信息
    protected $schema = [
        'id'            => 'int',
        'user_id'       => 'int',
        'template_id'   => 'string',
        'template_name' => 'string',
        'status'        => 'string',
        'create_time'   => 'int',
        'update_time'   => 'int',
    ];

    // 关联用户
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 检查用户是否已订阅某个模板
     * @param int $user_id 用户ID
     * @param string $template_id 模板ID
     * @return bool
     */
    public static function hasSubscribed($user_id, $template_id)
    {
        $record = self::where('user_id', $user_id)
            ->where('template_id', $template_id)
            ->where('status', 'accept')
            ->find();
        
        return $record !== null;
    }

    /**
     * 获取用户所有订阅记录
     * @param int $user_id 用户ID
     * @return array
     */
    public static function getUserSubscriptions($user_id)
    {
        return self::where('user_id', $user_id)
            ->where('status', 'accept')
            ->select()
            ->toArray();
    }
}
