<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use app\common\controller\Admin;

/**
 * @\woo\common\annotation\Log(remove={"password","pay_password","salt"})
 */
class User extends Admin
{
    public function index()
    {
        // 2021-5-22：自定义删除按钮的提示信息
        $this->local['item_tool_bar']['delete']['tip'] = '请确定删除会员信息？删除以后可在回收站找回';
        $this->local['tool_bar']['batch_delete']['tip'] = '请确定删除选中的会员信息？';

        // 详情新窗口打开示范
        //$this->local['load_type']['detail'] = "load-default";//页面打开方式必须是页面跳转
        //$this->local['item_tool_bar']['detail']['class'] = 'is-blank';// 加一个类名 表示新窗口


        if (isset($this->args['list']) && $this->args['list'] == 'normal') {
            $this->mdl->tableTab['basic']['item_tool_bar'][] = [
                'name' => 'more',
                'title' => '更多',
                'class' => 'btn-23',
                'children' => [
                    [
                        'name' => 'certification',
                        'title' => '实名认证',
                        'sort' => 10,
                        'class' => 'new_tab',
                        'icon' => '',
                        'url' => (string)url('Certification/index', ['parent_id' => '{{d.id}}']),
                    ]
                ]
            ];
            $this->mdl->tableTab['basic']['tool_bar'][] = [
                'name' => 'custon_list',
                'title' => '方格列表',
                'sort' => 10,
                'class' => 'btn-15',
                'icon' => '',
                'url' => (string)url('index', ['list' => 'custom']),
            ];
        } else {
            //2021-08-10新增自定义列表
            $this->local['limit'] = 12;
            $this->mdl->tableTab['basic']['custom'] = [
                'headerSelector' => 'basicCustomHeader'
            ];
            $this->mdl->tableTab['basic']['tool_bar'][] = [
                'name' => 'normal_list',
                'title' => '普通列表',
                'sort' => 10,
                'class' => 'btn-15',
                'icon' => '',
                'url' => (string)url('index', ['list' => 'normal']),
            ];
        }

        // 统计 没有用后台的生成
        $this->mdl->tableTab['basic']['counter'] = [
            [
                'field' => 'id',
                'title' => '总会员数',
                'type' => 'count',
                'where_type' => '',
                'where' => '',
                'callback' => '',
                'templet' => 'counter1',
                'more' => '',
            ],
            [
                'field' => 'id',
                'title' => '男会员数',
                'type' => 'count',
                'where_type' => 'where',
                'where' => [
                    ['sex', '=', 1],
                ],
                'callback' => '',
                'templet' => 'counter2',
                'more' => '',
            ],
            [
                'field' => 'id',
                'title' => '女会员数',
                'type' => 'count',
                'where_type' => 'where',
                'where' => [
                    ['sex', '=', '2'],
                ],
                'callback' => '',
                'templet' => 'counter3',
                'more' => '',
            ],
            [
                'field' => 'money',
                'title' => '会员总余额',
                'type' => 'sum',
                'where_type' => '',
                'where' => '',
                'callback' => '',
                'templet' => 'counter4',
                'more' => '',
            ],
            [
                'field' => '',
                'title' => '今日注册',
                'type' => '',
                'where_type' => 'callback',
                'where' => '',
                'callback' => 'getTodayRegisterNumber',
                'templet' => '',
                'more' => '',
            ],
            [
                'field' => 'id',
                'title' => '今日生日',
                'type' => '',
                'where_type' => 'callback',
                'where' => '',
                'callback' => 'getTodayBirthdayNumber',
                'templet' => '',
                'more' => '',
            ],
            [
                'field' => '',
                'title' => '今日登录人数',
                'type' => '',
                'where_type' => 'callback',
                'where' => '',
                'callback' => 'getTodayLoginNumber',
                'templet' => '',
                'more' => '',
            ],
            [
                'field' => '',
                'title' => '今日充值',
                'type' => '',
                'where_type' => 'callback',
                'where' => '',
                'callback' => 'getTodayRechargeSum',
                'templet' => '',
                'more' => '',
            ],
            [
                'field' => '',
                'title' => '最近一周新增用户量',
                'type' => '',
                'where_type' => 'callback',
                'where' => '',
                'callback' => 'getWeekRegisterData',
                'templet' => 'counterRegister',
                'more' => ['grid' => 'layui-col-lg12 layui-col-md12 layui-col-sm12'],
            ],
        ];

        $this->local['item_tool_bar']['delete']['where'] = '{{d.money }} == 0';
        $parent_return = parent::index();
        if ($this->request->isAjax()) {
            // 级联选择 列表显示中文示范
            foreach ($this->local['tableData']['data'] as &$item) {
                if (empty($item['region'])) {
                    continue;
                }
                $item['region'] = $this->mdl->getCascaderText('region', $item['region']);
            }
            return $this->local['tableData'];
        }

        $this->assign->addJs([
            'admin/echarts.common.min'
        ], true);
        return $parent_return;
    }

    public function modify()
    {
        $this->setFormValue('password', '');
        $this->setFormValue('pay_password', '');
        return parent::modify();
    }

    public function delete()
    {
        $this->local['where'][] = ['money', '=', 0];
        return parent::delete();
    }

    public function batchDelete()
    {
        $this->local['where'][] = ['money', '=', 0];
        return parent::batchDelete();
    }

    public function create()
    {
        return parent::create();
    }

    public function detail()
    {
        $this->local['detail_with'] = ['UserGroup','UserGrade','Certification', 'UserLogin' => [
            'limit' => 5,
            'order' => ['id' => 'DESC']
        ]];
        return parent::{__FUNCTION__}();
    }

    protected function detailCallback($data)
    {
        $data['region'] = $this->mdl->getCascaderText('region', $data['region']);
        return $data;
    }
}
