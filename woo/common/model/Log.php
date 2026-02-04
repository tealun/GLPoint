<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\Auth;

class Log extends App
{
    public function addLog(array $data = [], $remove = [])
    {
        $request = app()->request;
        $params = $request->getParams();
        $map = [
            'addon'      => 'addon_name',
            'controller' => 'controller',
            'action'     => 'action',
            'method'     => 'method',
            'url'        => 'url'
        ];
        foreach ($map as $key => $value) {
            if (!isset($data[$key])) {
                if (isset($params[$value])) {
                    $data[$key] = $params[$value];
                    continue;
                }
                if (method_exists($request, $value)) {
                    $data[$key] = $request->$value();
                    continue;
                }
                $data[$key] = '';
            }
        }

        if (empty($data['args'])) {
            $data['args'] = $request->param('', '', "htmlspecialchars");
        }
        if ($remove && $data['args']) {
            $data['args'] = array_diff_key($data['args'], array_flip($remove));
        }
        $data['args'] = json_encode($data['args'], JSON_UNESCAPED_UNICODE);
        $data['ip'] = $request->ip();
        $data['user_agent'] = $request->header('user_agent', '');
        $ipinfo = get_ip_info($data['ip']);
        $data['region'] = $ipinfo['region'];
        $data['isp'] = $ipinfo['isp'];
        $data['appname'] = app('http')->getName();

        if (empty($data['admin_id']) && app('http')->getName() == 'admin') {
            $admin = (new Auth())->user();
            $data['admin_id'] = $admin['id'];
            $data['username'] = $admin['username'];
        } elseif (app('http')->getName() == 'business') {
            $admin = (new Auth())->user();
            $data['business_member_id'] = $admin['id'];
            $data['username'] = $admin['username'];
            $data['business_id'] = $admin['business_id'] ?? 0;
        }
        try{
            $this->save($data);
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}