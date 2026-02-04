<?php
declare (strict_types=1);

namespace woo\common\model\traits;

use woo\common\facade\Cache;
use woo\common\helper\Tree;

trait DataAllow
{
    public function cancelCheckAdmin()
    {
        $this->customData['cancel_check_admin'] = true;
        return $this;
    }

    public function cancelCheckBusiness()
    {
        $this->customData['cancel_check_business'] = true;
        return $this;
    }

    /**
     *  后台/中台数据权限条件
     * @return array
     */
    public function getCheckAdminWhere($forceCheck = false)
    {
        // 中台
        if ('business' == app('http')->getName() && !$forceCheck) {
            return $this->getCheckBusinessWhere($forceCheck);
        }
        // 没有admin_id字段
        if (!array_key_exists('admin_id', $this->form) && $this->name != 'Admin') {
            return [];
        }
        // 有admin_id字段的模型 但不希望验证数据权限的模型 可以给模型自定义一个属性叫cancel_check_admin
        if (!empty($this->customData['cancel_check_admin'])) {
            return [];
        }
        // 非后台
        if ('admin' != app('http')->getName() && !$forceCheck) {
            return [];
        }
        $login = \woo\common\facade\Auth::user();
        // 未登录
        if (empty($login)) {
            return [];
        }
        if (isset($login['data_allow']) && $login['data_allow'] < 0 && !empty($login['AdminGroup'])) {
            $data_allow =  [];
            foreach ($login['AdminGroup'] as $role) {
                $role_data_allow = $this->getAdminGroupDataAllow($role['id']);
                if ($role_data_allow) {
                    array_push($data_allow, $role_data_allow);
                }
            }
        } else {
            $data_allow = [
                [
                    'data_allow' => $login['data_allow'] ?? -1,
                    'custom_data_allow' => $login['custom_data_allow'] ?? '',
                ]
            ];
        }
        $foreignKey = $this->name != 'Admin' ? 'admin_id' :$this->getPk();
        // 找不到数据权限 不能查看数据
        if (empty($data_allow)) {
            return [[$foreignKey, '=', -1]];
        }

        $ids = [];
        foreach ($data_allow as $allow) {
            if ($allow['data_allow'] < 0) {
                continue;
            }
            // 表示全部全部数据权限 只要有一个全部数据就一定是全部数据了
            if ($allow['data_allow'] == 0) {
                return [];
            }
            // 仅本人数据权限
            if ($allow['data_allow'] == 1) {
                array_push($ids, $login['id']);
                continue;
            }

            if ($allow['data_allow'] == 2) {
                // 本部门数据权限
                $department = $login['department_id'];
            } elseif ($allow['data_allow'] == 3) {
                // 部门及以下数据权限
                $department = (new Tree(model('Department')))->getDeepChildren($login['department_id']);
                array_push($department, $login['department_id']);
            } elseif ($allow['data_allow'] == 4) {
                // 自定义数据权限
                $department = explode(',', (string) $allow['custom_data_allow']);
            } elseif ($allow['data_allow'] == 5) {
                // 所在顶级部门及以下数据权限
                $departmentTree = new Tree(model('Department'));
                $top_id = $departmentTree->getTopId($login['department_id']);
                $department = $departmentTree->getDeepChildren($top_id);
                array_push($department, $top_id);
            }
            array_push($ids, ...$this->getAdminByDepartment($department));
        }
        if (empty($ids)) {
            return [[$foreignKey, '=', -1]];
        }
        $ids = array_unique($ids);
        return count($ids) != 1 ? [[$foreignKey, 'IN', $ids]]: [[$foreignKey, '=', $ids[0]]];
    }

    public function getCheckBusinessWhere($forceCheck = false)
    {
        // 没有business_member_id字段
        if (!array_key_exists('business_member_id', $this->form) && $this->name != 'BusinessMember') {
            return [];
        }
        // 有business_member_id字段的模型 但不希望验证数据权限的模型 可以给模型自定义一个属性叫cancel_check_business
        if (!empty($this->customData['cancel_check_business'])) {
            return [];
        }
        // 非后台
        if ('business' != app('http')->getName() && !$forceCheck) {
            return [];
        }
        $login = \woo\common\facade\Auth::user();
        if (empty($login)) {
            return [];
        }

        if (isset($login['data_allow']) && $login['data_allow'] < 0 && !empty($login['BusinessRole'])) {
            $data_allow =  [];
            foreach ($login['BusinessRole'] as $role) {
                $role_data_allow = $this->getBusinessRoleDataAllow($role['id']);
                if ($role_data_allow) {
                    array_push($data_allow, $role_data_allow);
                }
            }
        } else {
            $data_allow = [
                [
                    'data_allow' => $login['data_allow'] ?? -1,
                    'custom_data_allow' => $login['custom_data_allow'] ?? '',
                ]
            ];
        }
        $foreignKey = $this->name != 'BusinessMember' ? 'business_member_id' :$this->getPk();
        // 找不到数据权限 不能查看数据
        if (empty($data_allow)) {
            return [[$foreignKey, '=', -1]];
        }
        $ids = [];
        foreach ($data_allow as $allow) {
            if ($allow['data_allow'] < 0) {
                continue;
            }
            // 表示全部全部数据权限 只要有一个全部数据就一定是全部数据了
            if ($allow['data_allow'] == 0) {
                return [];
            }
            // 仅本人数据权限
            if ($allow['data_allow'] == 1) {
                array_push($ids, $login['id']);
                continue;
            }

            if ($allow['data_allow'] == 2) {
                // 本部门数据权限
                $department = $login['business_department_id'];
            } elseif ($allow['data_allow'] == 3) {
                // 部门及以下数据权限
                $department = (new Tree(model('BusinessDepartment')))->getDeepChildren($login['business_department_id']);
                array_push($department, $login['business_department_id']);
            } elseif ($allow['data_allow'] == 4) {
                // 自定义数据权限
                $department = explode(',', (string) $allow['custom_data_allow']);
            } elseif ($allow['data_allow'] == 5) {
                // 所在顶级部门及以下数据权限
                $departmentTree = new Tree(model('BusinessDepartment'));
                $top_id = $departmentTree->getTopId($login['business_department_id']);
                $department = $departmentTree->getDeepChildren($top_id);
                array_push($department, $top_id);
            }
            array_push($ids, ...$this->getBusinessMemberByDepartment($department));
        }
        if (empty($ids)) {
            return [[$foreignKey, '=', -1]];
        }
        $ids = array_unique($ids);

        return count($ids) != 1 ? [[$foreignKey, 'IN', $ids]]: [[$foreignKey, '=', $ids[0]]];
    }

    protected function getAdminGroupDataAllow($role_id)
    {
        $role = admin_group($role_id);
        if (empty($role)) {
            return false;
        }
        if ($role['data_allow'] >= 0) {
            return [
                'data_allow' => $role['data_allow'],
                'custom_data_allow' => $role['custom_data_allow'],
            ];
        }
        return $this->getAdminGroupDataAllow($role['parent_id']);
    }

    protected function getBusinessRoleDataAllow($role_id)
    {
        $role = (new Tree(model('BusinessRole')))->get($role_id);
        if (empty($role)) {
            return false;
        }
        if ($role['data_allow'] >= 0) {
            return [
                'data_allow' => $role['data_allow'],
                'custom_data_allow' => $role['custom_data_allow'],
            ];
        }
        return $this->getBusinessRoleDataAllow($role['parent_id']);

    }

    protected function getAdminByDepartment($department): array
    {
        if (!is_array($department)) {
            $department = [$department];
        }
        $cacheKey = 'AdminByDepartment_for_' . implode('_', $department);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        $ids = model('Admin')->where('department_id', 'IN', $department)->column('id');
        if (empty($ids)) {
            $ids = [-1];
        }
        Cache::tag('Admin')->set($cacheKey, $ids);
        return $ids;
    }

    protected function getBusinessMemberByDepartment($department): array
    {
        if (!is_array($department)) {
            $department = [$department];
        }
        $cacheKey = 'BusinessByDepartment_for_' . implode('_', $department);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        $ids = model('BusinessMember')->where('business_department_id', 'IN', $department)->column('id');
        if (empty($ids)) {
            $ids = [];
        }
        Cache::tag(model_cache_tag('BusinessMember'))->set($cacheKey, $ids);
        return $ids;
    }
}