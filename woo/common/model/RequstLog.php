<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\facade\Auth;

class RequstLog extends App
{
    public function addLog(array $data = [], $remove = [])
    {
        $request = app()->request;
        $params = $request->getParams();
        $map = [
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
        $data['appname'] = !$request->isAddon ? app('http')->getName() : $request->addon;
        if (empty($data['args'])) {
            $data['args'] = $request->param('', '', "htmlspecialchars");
        }
        if ($remove && $data['args']) {
            $data['args'] = array_diff_key($data['args'], array_flip($remove));
        }

        $data['args'] = json_encode($data['args'], JSON_UNESCAPED_UNICODE);
        $data['ip'] = $request->ip();
        $data['user_agent'] = $request->header('user_agent', '');
        $data['referer'] = strip_tags($request->header('referer', ''));
        $ipinfo = get_ip_info($data['ip']);
        $data['region'] = $ipinfo['region'];
        $data['isp'] = $ipinfo['isp'];

        $login = Auth::user();
        if (!empty($login) && isset($login['login_foreign_key'])) {
            $data[$login['login_foreign_key']] = $login['login_foreign_value'];
        }
        try{
            $this->save($data);
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}