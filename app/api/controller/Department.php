<?php
declare (strict_types = 1);
namespace app\api\controller;

use app\common\controller\Api;
use think\facade\Log;
use app\api\library\Auth;
use app\api\library\ListToTree;

/**
 * 部门管理控制器
 * @Controller("部门管理",module="组织",desc="部门列表、用户列表")
 */
class Department extends Api
{
    /**
     * 获取部门列表
     */
    public function index()
    {
        $login = Auth::checkLogin();
        if (!$login) {
            return $this->error('未登录或登录已过期');
        }

        try {

            // 获取所有部门数据
            $departments = model('Department')
                ->field(['id', 'parent_id', 'title'])
                ->select()
                ->toArray();

            // 构建id到部门的映射
            $deptMap = [];
            foreach ($departments as $dept) {
                $deptMap[$dept['id']] = $dept;
            }

            // 构建树形结构
            $tree = ListToTree::toTree($departments, 'id', 'parent_id', 'children', 0);

            return $this->success('获取成功', [
                'department_tree' => $tree
            ]);
        } catch (\Exception $e) {
            Log::error('获取部门树失败: ' . $e->getMessage());
            return $this->error('获取部门树失败');
        }
    }

    /**
     * 获取部门用户列表
     */
    public function users()
    {
        $login = Auth::checkLogin();
        if (!$login) {
            return $this->error('未登录或登录已过期');
        }
        // 获取部门ID
        $id = $this->request->param('department_id', 0, 'intval');
        if ($id <= 0) {
            return $this->error('部门ID无效');
        }

        try {
            $department = model('Department')->find($id);
            if(!$department) {
                return $this->error('部门不存在');
            }

            // 获取所有子部门ID（包括自身）
            $departments = model('Department')->field(['id', 'parent_id'])->select()->toArray();
            $allIds = [$id];
            $queue = [$id];
            while ($queue) {
                $current = array_shift($queue);
                foreach ($departments as $dept) {
                    if ($dept['parent_id'] == $current) {
                        $allIds[] = $dept['id'];
                        $queue[] = $dept['id'];
                    }
                }
            }

            $users = model('user')
                ->field([
                    'id','username','nickname'
                ])
                ->whereIn('department_id', $allIds)
                ->where('status', 'verified')
                ->order('id DESC')
                ->select();

            return $this->success('获取成功', $users->toArray());

        } catch(\Exception $e) {
            Log::error('获取部门用户列表失败: ' . $e->getMessage());
            return $this->error('获取部门用户列表失败');
        }
    }

    /**
     * 获取部门详情
     */
    public function detail($id)
    {
        try {
            $department = model('Department')->find($id);
            if(!$department) {
                return $this->error('部门不存在');
            }

            // 获取部门领导信息
            if(!empty($department->leader_ids)) {
                $department->leaders = model('Admin')
                    ->field(['id', 'nickname', 'avatar'])
                    ->whereIn('id', explode(',', $department->leader_ids))
                    ->select();
            }

            // 获取上级部门信息
            if($department->parent_id > 0) {
                $department->parent = model('Department')
                    ->field(['id', 'title'])
                    ->find($department->parent_id);
            }

            // 获取下级部门
            $department->children = model('Department')
                ->field(['id', 'title'])
                ->where('parent_id', $id)
                ->select();

            return $this->success('获取成功', [
                'department' => $department
            ]);

        } catch(\Exception $e) {
            Log::error('获取部门详情失败: ' . $e->getMessage());
            return $this->error('获取部门详情失败');
        }
    }
}
