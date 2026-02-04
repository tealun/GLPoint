<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\facade\Auth;

class RequestLog extends App
{
    /** 模型名称 */
    public $cname = '请求日志';

    /** 主显字段信息 */
    public $display = 'appname';

    /** 自定义数据 */
    public $customData = [
        'batch_delete' => true,
        'delete' => true,
        'detail' => true,
    ];

    /** 模型关联信息 */
    public $relationLink = [];


    protected function start()
    {
        parent::{__FUNCTION__}();

        /** 表单form属性 */
        $this->form = [
            'id' => [
                'type' => 'integer',
                'name' => 'ID',
                'elem' => 'hidden',
                'is_contribute' => false,
            ],
            'appname' => [
                'type' => 'string',
                'name' => '应用/插件名',
                'elem' => 0,
                'is_contribute' => false,
                'list_filter' => true,
            ],
            'admin_id' => [
                'type' => 'integer',
                'name' => '管理员ID',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 'show',
            ],
            'user_id' => [
                'type' => 'integer',
                'name' => '会员ID',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 'show',
            ],
            'business_member_id' => [
                'type' => 'integer',
                'name' => '商家会员ID',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 'show',
            ],
            'controller' => [
                'type' => 'string',
                'name' => '控制器',
                'elem' => 0,
                'is_contribute' => false,
            ],
            'action' => [
                'type' => 'string',
                'name' => '方法',
                'elem' => 0,
                'is_contribute' => false,
            ],
            'url' => [
                'type' => 'string',
                'name' => 'URL地址',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 'url',
            ],
            'method' => [
                'type' => 'string',
                'name' => '请求方法',
                'elem' => 0,
                'is_contribute' => false,
                'options' => [
                    'GET' => 'GET',
                    'POST' => 'POST',
                    'PUT' => 'PUT',
                    'DELETE' => 'DELETE',
                ],
                'list_filter' => [
                    'templet' => 'select',
                ],
            ],
            'args' => [
                'type' => 'string',
                'name' => '数据',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 0,
            ],
            'ip' => [
                'type' => 'string',
                'name' => 'IP地址',
                'elem' => 0,
                'is_contribute' => false,
                'list_filter' => true,
            ],
            'region' => [
                'type' => 'string',
                'name' => '请求地址',
                'elem' => 0,
                'is_contribute' => false,
            ],
            'isp' => [
                'type' => 'string',
                'name' => '网络ISP',
                'elem' => 0,
                'is_contribute' => false,
            ],
            'user_agent' => [
                'type' => 'string',
                'name' => '客户端',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 'ua',
            ],
            'referer' => [
                'type' => 'string',
                'name' => '来源',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 0,
            ],
            'code' => [
                'type' => 'integer',
                'name' => '状态码',
                'elem' => 0,
                'is_contribute' => false,
                'list_filter' => true,
            ],
            'create_time' => [
                'type' => 'integer',
                'name' => '请求日期',
                'elem' => 0,
                'is_contribute' => false,
                'list' => [
                    'width' => '140',
                ],
                'list_filter' => 'date_range',
            ],
            'update_time' => [
                'type' => 'integer',
                'name' => '修改日期',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 0,
            ],
        ];

        /** 表单分组属性 */
        $this->formGroup = [];

        /** 表单触发器属性 */
        $this->formTrigger = [];

        /** 表单验证属性 */
        $this->validate = [];

    }

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