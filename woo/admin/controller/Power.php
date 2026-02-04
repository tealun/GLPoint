<?php
declare (strict_types = 1);

namespace woo\admin\controller;


use think\facade\Config;
use think\facade\Db;
use woo\common\annotation\Ps;
use woo\common\annotation\Forbid;
use woo\common\facade\Auth;
use woo\common\helper\Str;
use woo\common\helper\Tree;

/**
 * @Forbid(true)
 */
class Power extends \app\common\controller\Admin
{
    /**
     * @Ps(name="授权")
     */
    public function index()
    {
        $show = [
            'group_action' => false,
            'group_action_remove' => false,
            'admin_action' => false,
            'admin_action_remove' => false
        ];
        $tab = 0;

        if (isset($this->args['admin_group_id'])) {
            $group = admin_group(intval($this->args['admin_group_id']));
            if (empty($group)) {
                return $this->message('你要授权的角色不存在', 'error');
            }
            if ($group['id'] == Config::get('wooauth.super_group_id')) {
                return $this->message('不支持对超级管理员授权', 'error');
            }
            $this->assign->group = $group;
            $show['group_action'] = true;
            if ($group['parent_id'] != 0) {
                $show['group_action_remove'] = !!$this->mdl->where('admin_group_id', $group['id'])->count();
            }
            $powers = Auth::getAdminGroupPower($group['id']);
        } elseif (isset($this->args['admin_id'])) {
            $tab = 1;
            $admin =  model('Admin')
                ->where(model('Admin')->getCheckAdminWhere())
                ->where('id', '=', intval($this->args['admin_id']))
                ->field(['id', 'username', 'truename', 'mobile'])
                ->find();
            if (empty($admin)) {
                return $this->message('你要授权的用户不存在', 'error');
            }
            $this->assign->admin = $admin;
            $show['admin_action'] = true;
            $show['admin_action_remove'] = !!$this->mdl->where('admin_id', $admin['id'])->count();
            $powers = Auth::getAdminPower($admin['id']);
        }

        $admin_ids = $this->mdl->where('admin_id', '>', 0)->column('admin_id');
        if ($admin_ids) {
            $admin_list =  model('Admin')
                ->where(model('Admin')->getCheckAdminWhere())
                ->where('id', 'IN', $admin_ids)
                ->field(['id', 'username', 'truename', /*'mobile'*/])
                ->select()
                ->toArray();
            $this->assign->admin_list = $admin_list;
        }


        $this->assign->tab = $tab;
        $this->assign->show = $show;
        $this->assign->groupTreeData = $this->getRoleTree();
        $this->assign->treeData = $this->getDtree($powers ?? []);
        $this->local['header_title'] = '后台授权';
        $this->local['header_tip'] = '温馨提示：尽量使用角色授权，特殊用户单独授权，用户独立授权优先级高于角色授权；清除角色授权数据以后，自动使用父角色授权数据，顶级角色授权数据只能修改不能清除；清除用户独立授权数据以后，自动使用其角色的授权数据。';
        $this->addAction('admingroup', '角色组', (string) url('admin_group/index'), 'btn-2', 'layui-icon-user');
        $this->local['topBar'] = true;
        return $this->fetch();
    }

    /**
     * @Ps(as="index")
     */
    public function setPower()
    {
        if (!$this->request->isAjax()) {
            return $this->message('请求方式错误', 'error');
        }
        $data = $this->request->post();
        if (empty($data['admin_group_id']) && empty($data['admin_id'])) {
            return $this->ajax('error', '没有明确的角色或用户');
        }
        if (!empty($data['admin_group_id']) && $data['admin_group_id'] == Config::get('wooauth.super_group_id')) {
            return $this->ajax('error', '不支持对超级管理员授权');
        }

        $content = model('adminRule')->where('id', 'IN', $data['tree_ids'] ?? [])->column('id');

        if (!empty($data['admin_group_id'])) {
            $exists = $this->mdl->where([
                ['admin_group_id', '=', intval($data['admin_group_id'])]
            ])->find();

            if ($exists) {
                $result = $exists->modifyData([
                    'content' => json_encode($content ?? [])
                ]);
            } else {
                $result = $this->mdl->createData([
                    'admin_id' => 0,
                    'is_not_set_login_foreign_key' => 1,
                    'admin_group_id' => intval($data['admin_group_id']),
                    'content' => json_encode($content ?? [])
                ]);
                $exists = $this->mdl;
            }
            if ($result) {
                return $this->ajax('success', '角色授权成功!');
            } else {
                return $this->ajax('error', array_values($exists->getError())[0] ?? '授权失败');
            }
        }

        $exists = $this->mdl->where([
            ['admin_id', '=', intval($data['admin_id'])]
        ])->find();

        if ($exists) {
            $result = $exists->modifyData([
                'content' => json_encode($content ?? [])
            ]);
        } else {
            $result = $this->mdl->createData([
                'admin_id' => intval($data['admin_id']),
                'is_not_set_login_foreign_key' => 1,
                'content' => json_encode($content ?? [])
            ]);
            $exists = $this->mdl;
        }
        if ($result) {
            return $this->ajax('success', '用户授权成功!');
        } else {
            return $this->ajax('error', array_values($exists->getError())[0] ?? '授权失败');
        }
    }

    /**
     * @Ps(as="index")
     */
    public function clearPower()
    {
        if (!$this->request->isAjax()) {
            return $this->message('请求方式错误', 'error');
        }
        $data = $this->request->post();
        if (empty($data['admin_group_id']) && empty($data['admin_id'])) {
            return $this->ajax('error', '没有明确的角色或用户');
        }

        if (!empty($data['admin_group_id'])) {
            $group = admin_group(intval($data['admin_group_id']));
            $exists = $this->mdl->where([
                ['admin_group_id', '=', intval($data['admin_group_id'])]
            ])->find();
            if (!$exists) {
                return $this->ajax('error', '当前角色还没有单独授权，没有可清除的授权数据');
            }
            if ($group['parent_id'] == 0) {
                return $this->ajax('error', '顶级角色授权数据不允许清除');
            }
            $exists->delete();
            return $this->ajax('success', '角色授权数据清除成功');
        }
        $exists = $this->mdl->where([
            ['admin_id', '=', intval($data['admin_id'])]
        ])->find();
        if (!$exists) {
            return $this->ajax('error', '当前用户还没有独立授权，没有可清除的授权数据');
        }
        $exists->delete();
        return $this->ajax('success', '用户授权数据清除成功');
    }

    /**
     * @Ps(as="index")
     */
    public function searchAdmin()
    {
        $kw = $this->args['kw'] ?? '';
        if (empty($kw)) {
            return $this->error('请输入搜索用户名/姓名/手机"');
        }

        $list = model('Admin')
            ->where(model('Admin')->getCheckAdminWhere())
            ->where(function ($query) use ($kw){
                $query->whereOr([
                    ['id', '=', $kw],
                    ['username', 'LIKE', '%' .$kw .'%'],
                    ['mobile', 'LIKE', '%' .$kw .'%']
                ]);
            })
            ->with(['AdminGroup'])
            ->field(['id', 'username', 'truename', 'mobile'])
            ->limit(10)
            ->select()
            ->toArray();
        if (empty($list)) {
            return $this->error('您要搜索的用户不存在');
        }
        $data = [];
        foreach ($list as $item) {
            if (!isset($item['AdminGroup'])) {
                continue;
            }
            if (!empty($item['AdminGroup']) && $item['AdminGroup'][0]['id'] == Config::get('wooauth.super_group_id')) {
                continue;
            }
            array_push($data, $item);
        }
        if (empty($data)) {
            return $this->error('您要搜索的用户不存在');
        }
        return $this->success('搜索成功', $data);
    }


    protected function getRoleTree($default = [])
    {
        return $this->deepRoleTreeData(admin_group('children', 0),1,$default);
    }

    protected function deepRoleTreeData($children, $nowLevel = 1, $default = [])
    {
        $list = [];
        foreach ($children as $id) {
            if ($id == Config::get('wooauth.super_group_id')) {
                continue;
            }
            $item = admin_group($id);

            $my = [
                "title" => $item['title'],
                "id" => $id,
                "href" => (string) url('index', ['admin_group_id' => $id]),
                "spread" => $nowLevel > 1 ? false : true
            ];
            if (admin_group('children', $id)) {
                $my['children'] = $this->deepRoleTreeData(admin_group('children', $id), $nowLevel + 1, $default);
            }
            $list[] = $my;
        }
        return $list;
    }

    protected function getDtree($default = [])
    {
        return $this->deepDtreeData(admin_rule('children', 0),1,$default);
    }

    protected function deepDtreeData($children, $nowLevel = 1, $default = [])
    {
        $list = [];
        $i  = 0;
        foreach ($children as $id) {
            $i++;
            $item = admin_rule($id);

            $check = in_array($id, $default) ? "1" :"0";
            if (empty(admin_rule('children', $id)) && $item['type'] != 'button' && empty($item['url'])) {
                $check = false;
            }
            $my = [
                "id" => $id,
                'title' => $item['title'],
                'parentId' => $item['parent_id'],
                'level' => $item['level'],
                'checkArr' => $check,
                'rule' => $item['rule'] && $item['type'] == 'button' ? $item['rule'] : ""
            ];
            if (admin_rule('children', $id)) {
                $my['children'] = $this->deepDtreeData(admin_rule('children', $id), $nowLevel + 1, $default);
            }
            $list[] = $my;
        }
        return $list;
    }
}