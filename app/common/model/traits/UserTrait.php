<?php
namespace app\common\model\traits;

trait UserTrait
{
    /**
     * 关联微信用户信息
     */
    public function wechatUser()
    {
        return $this->hasOne('WechatUser', 'user_id', 'id');
    }

    /**
     * 获取用户微信绑定信息
     * @return array|null 
     */
    public function getWechatInfo()
    {
        return $this->wechatUser ? $this->wechatUser->toArray() : null;
    }

    /**
     * 判断是否绑定微信
     * @return bool
     */
    public function isWechatBind()
    {
        return (bool)$this->wechatUser;
    }

    /**
     * 解绑微信
     * @return bool
     */
    public function unbindWechat()
    {
        if($this->wechatUser) {
            return $this->wechatUser->delete();
        }
        return true;
    }

    /**
     * 绑定微信账号
     * @param array $wechatData 微信数据
     * @return bool
     */
    public function bindWechat(array $wechatData)
    {
        if($this->isWechatBind()) {
            return false;
        }
        
        $wechatData['user_id'] = $this->id;
        return (bool)\app\common\model\traits\WechatUserTrait::create($wechatData);
    }

	protected function afterStart()
	{
		parent::{__FUNCTION__}();
		// 代码执行到这里的时候已经 直接执行过了start方法 所以start定义的属性都可以获取到 当然也可以在该文件定义更多的自定义属性和方法
		// $this->form[字段名] =  动态修改字段的某个属性;
	}
}
