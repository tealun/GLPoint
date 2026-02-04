<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use think\facade\Event;
use woo\common\annotation\Except;
use woo\common\annotation\Forbid;
use woo\common\annotation\Ps;
use woo\common\Auth;

/**
 * @Ps(name="管理员")
 * @\woo\common\annotation\Log(remove={"password","salt"})
 */
class Admin extends \app\common\controller\Admin
{
    /**
     * @return mixed|string|\think\response\Redirect|void
     * @throws \Exception
     * @Ps(false)
     */
    public function login()
    {
        if ($this->login) {
            return $this->redirect(url('index/index'));
        }
        $this->assign->addCss('/woo/css/admin/login');
        $this->assign->addCss('/files/loaders/loaders');
        $this->assign->addJs('jquery.easing.1.3', true);
        $this->assign->addJs('/files/loaders/loaders.css.js', true);
        $redirect = $this->request->get('url', (string)url('index/index'));
        $this->assign->redirect = $redirect;
        // 如果要使用另外一个登录页面 把login20210706 改为login
        $this->addTitle('登录');
        $this->local['rsa'] = ['password'];
        return $this->fetch($this->local['fetch'] ?? 'login20210706');
    }

    /**
     * @return string|\think\response\Json|\think\response\Redirect|void
     * @throws \think\Exception
     * @Ps(false)
     */
    public function ajaxLogin()
    {
        if (!$this->request->isAjax()) {
            return $this->message('不是一个正确的请求方式', 'error');
        }
        if (!captcha_check($this->request->post('captcha', '')) && !$this->assign->isDebug) {
            return $this->ajax('error', '亲！验证码错误了哦');
        }
        $auth = new Auth();
        $logined = $auth->login();
        if ($logined) {
            admin_menu();
            $login = $auth->user();

            // 必须有角色的管理员才能登录
            if (empty($login['AdminGroup'])) {
                $auth->logout();
                return $this->ajax('error', '暂不允许登录，请联系管理员为您设置角色');
            }
            $allow = true;
            if (!empty($login['Department']) && empty($login['Department']['is_admin'])) {
                $allow = false;
            }
            foreach ($login['AdminGroup'] as $group) {
                if (empty($group['is_admin'])) {
                    $allow = false;
                    break;
                }
            }
            if (!$allow) {
                $auth->logout();
                return $this->ajax('error', '抱歉！您的账号，暂不能登录后台');
            }
            return $this->ajax('success', '<span style="color: #36b368">登录成功，页面即将跳转...</span>');
        } else {
            return $this->ajax('error', $auth->getError());
        }
    }

    /**
     * @return \think\response\Redirect|void
     * @Ps(false)
     */
    public function logout()
    {
        $auth = new Auth();
        $logined = $auth->logout();
        return $this->redirect('login');
    }

    public function index()
    {
        $this->local['item_tool_bar']['delete']['where'] = '{{d.id > 1}}';
        $this->local['afterData'] = 'afterData';
        return parent::{__FUNCTION__}();
    }

    protected function afterData()
    {
        foreach ($this->local['tableData']['data'] as &$item) {
            if (!empty($item['region'])) {
                $item['region'] = $this->mdl->getCascaderText('region', $item['region']);
            }
        }
    }

    protected function detailCallback($data)
    {
        if (!empty($data['region'])) {
            $data['region'] = $this->mdl->getCascaderText('region', $data['region']);
        }
        return $data;
    }

    /**
     * @Ps(true,name="修改密码")
     */
    public function password()
    {
        return parent::scene();
    }

    public function create()
    {
        $this->setFormValue('status', 'verified');
        $this->setFormValue('data_allow', -1);
        $this->mdl->form['password']['require'] = true;
        return parent::{__FUNCTION__}();
    }

    public function modify()
    {
        $this->setFormValue('password', '');
        return parent::{__FUNCTION__}();
    }

    public function detail()
    {
        $this->local['detail_with'] = ['AdminGroup', 'Department', 'AdminLogin' => [
            'limit' => 5,
            'order' => ['id' => 'DESC']
        ]];
        return parent::{__FUNCTION__}();
    }

    public function delete()
    {
        // 不允许删除 ID为1的用户
        $this->local['where'][] = ['id', '<>', 1];
        return parent::{__FUNCTION__}();
    }

    public function batchDelete()
    {
        // 不允许删除 ID为1的用户
        $this->local['where'][] = ['id', '<>', 1];
        return parent::{__FUNCTION__}();
    }

    /**
     * @Ps(as="modify")
     */
    public function setToGroup()
    {
        if (!$this->request->isAjax()) {
            return $this->message('请求方式错误', 'error');
        }
        $post = $this->request->post();
        try {
            $exists = $this->mdl->where('id', '=', intval($post['aid']))->find();
            if (empty($exists)) {
                return $this->ajax('error', '用户已经不存在');
            }
            $exists->admin_group_id = $post['agid'];
            $result = $exists->save();
            if ($result) {
                return $this->ajax('success', '移动成功');
            } else {
                return $this->ajax('error', array_values($exists->getError())[0] ?? '移动失败');
            }

        } catch (\Exception $e) {
            return $this->ajax('error', $e->getMessage());
        }
    }

    /**
     * @Ps(true,name="个人信息")
     * @\woo\common\annotation\Log(only={"ajax"})
     */
    public function home()
    {
        $this->local['tableTab'] = [
            'basic' => [
                'title' => '登录日志',
                'model' => 'AdminLogin',
                'list_fields' => [
                    'id' => [
                        'width' => 90
                    ],
                    'ip' => [
                        'width' => 130,
                        'sort' => true
                    ],
                    'user_agent' => [
                        'width' => 160
                    ],
                    'region' => [
                        'width' => 180
                    ],
                    'summary' => [
                        'width' => 180
                    ],
                    'create_time' => [
                        'width' => 150
                    ]
                ],
                'checkbox' => false,
                'table' => [
                    'height' => 442
                ]
            ],
            'log' => [
                'title' => '操作日志',
                'model' => 'Log',
                'list_fields' => [
                    'id' => [
                        'width' => 90
                    ],
                    'controller' => [
                        'width' => 100
                    ],
                    'url' => [
                        'width' => 200
                    ],
                    'method' => [
                        'width' => 90,
                    ],
                    'ip' => [
                        'minWidth' => 120,
                        'sort' => true
                    ],
                    'region' => [
                        'width' => 180
                    ],
                    'user_agent' => [
                        'width' => 160
                    ],
                    'create_time' => [
                        'width' => 135
                    ]
                ],
                'list_filters' => false,
                'checkbox' => false,
                'table' => [
                    'height' => 442
                ]
            ]
        ];
        if (isset($this->args['tabname']) && $this->args['tabname'] == 'log') {
            $this->local['forceCache'] = 60;
        }

        $this->local['rsa'] = ['password'];
        $this->local['cancelCheckAdmin'] = true;
        $this->local['where']['admin_id'] = $this->login['id'];
        $this->local['header_title'] = '我的信息';
        $this->local['fetch'] = 'home';
        return $this->getIndex();
    }

    /**
     * @Ps(as="home")
     * @Forbid(only={"ajax","post"})
     */
    public function setHome()
    {
        try {
            $admin = $this->mdl->find($this->login['id']);
            if (empty($admin)) {
                return $this->ajax('error', '修改失败');
            }
            $data = $this->request->post('', null, "strip_tags");
            foreach (['email', 'password', 'nickname', 'truename', 'avatar'] as $field) {
                if (isset($data[$field])) {
                    $admin->$field = $data[$field];
                }
            }
            $result = $admin->save();
            if ($result) {
                return $this->ajax('success', '您的信息已经修改成功');
            } else {
                return $this->ajax('error', array_values($admin->getError())[0] ?? '修改失败');
            }
        } catch (\Exception $e) {
            return $this->ajax('error', $e->getMessage());
        }
    }
}