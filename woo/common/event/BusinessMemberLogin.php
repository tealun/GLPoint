<?php
declare (strict_types = 1);

namespace woo\common\event;

use think\facade\Db;
use think\facade\Session;

class BusinessMemberLogin
{
    public function handle($data)
    {
        $request = app('request');
        $data['user_agent'] = $request->header('user-agent');
        $data['ip'] = $request->ip();
        $ipinfo = get_ip_info($data['ip']);
        $data['region'] = $ipinfo['region'];
        $data['is_success'] = !empty($data['success']) ? 1 : 0;
        try {
            model('BusinessMemberLogin')->save($data);
            if (!empty($data['success'])) {
                Db::name('BusinessMember')->where('id', '=', $data['business_member_id'])->update([
                    'login_time' => time(),
                    'login_ip' => $data['ip'],
                    'login_id' => Session::getId()
                ]);
            }
        } catch (\Exception $e) {

        }
    }
}
