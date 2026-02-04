<?php
declare (strict_types=1);

namespace woo\common\auth\traits;

use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Env;
use woo\common\helper\Str;

trait Admin
{

    /**
     * 后台指定控制器\方法权限判断
     * @param array $request
     * @return bool
     * @throws \ReflectionException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function adminPower(array $request = [])
    {
        $admin = $this->user();// 当前登录者
        // 后台超级权限
        if (!empty($admin['AdminGroup']) && $admin['AdminGroup'][0]['id'] == Config::get('wooauth.super_group_id')) {
            return true;
        }

        $request['addon'] = isset($request['addon']) ? strtolower($request['addon']) : strtolower($this->request->getParams()['addon_name']);
        $request['controller'] = !empty($request['controller']) ? Str::studly($request['controller']) : Str::studly($this->request->getParams()['controller']);
        $request['action'] = !empty($request['action']) ? strtolower($request['action']) : strtolower($this->request->getParams()['action']);

        $cache_result = $this->getAdminPowerStore($request);
        if (is_bool($cache_result)) {
            return $cache_result;
        }
        $namespace = "app\\" . app('http')->getName() . "\\" . "controller\\" . (!$request['addon'] ? "" : $request['addon'] . "\\") . $request['controller'];
        $reflection = reflect($namespace);
        if (!$reflection) {
            return  app()->isDebug() ? true : false;
        }

        $classPs = $this->getReader()->getClassAnnotation($reflection, 'Ps');
        if ($classPs && false === $classPs->value) {
            $this->adminPowerStore($request, true);
            return true;
        }
        $result = $this->getMethodPs($reflection, $request['action']);
        if (true === $result) {
            $this->adminPowerStore($request, true);
            return true;
        }
        $route = ($request['addon'] ? $request['addon'] . '.' : '')
            . Str::snake($request['controller']) . '/' . strtolower($result);

        $rules = Db::name('AdminRule')
            ->where([
                ['rule', '=', $route],
                ['type', '=', 'button']
            ])
            ->column('id');
        // 路由没有在菜单规则中
        if (empty($rules)) {
            return !Config::get('wooauth.force_check_power', false);
        }
        $allows = $this->getAdminPower();
        if (empty($allows)) {
            return false;
        }
        $return = true;
        foreach ($rules as $id) {
            if (!in_array($id, $allows, true)) {
                $return = false;
                break;
            }
        }
        $this->adminPowerStore($request, $return);
        return $return;
    }

    /**
     * 获取指定角色的授权数据
     * @param $group_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminGroupPower($group_id) :array
    {
        $group_id = (int) $group_id;
        if ($group_id <= 0) {
            return [];
        }
        if ($group_id == Config::get('wooauth.super_group_id')) {
            return model('AdminRule')->column('id');
        }
        if (Env::get('APP_DEBUG')) {
            $count = model('Power')
                ->cache('AdminGroupPowerExists', 86400, ['Power'], true)
                ->value('id');
            // 如果一条数据也没有 开启全部权限
            if (empty($count)) {
                return model('AdminRule')->column('id');
            }
        }

        $exists = model('Power')
            ->where([
                ['admin_group_id', '=', $group_id]
            ])
            ->cache('AdminGroupPower_pk_' . $group_id, 86400, ['Power', 'AdminGroup'], true)
            ->find();
        if (empty($exists)) {
            $group = admin_group($group_id);
            // 如果当前角色没有授权 就返回父角色的授权
            return $group ? $this->getAdminGroupPower($group['parent_id']) : [];
        }
        return !empty($exists['content']) ? json_decode($exists['content'], true) : [];
    }

    /**
     * 获取指定用户id权限 默认获取当前登录者
     * @param null $admin_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminPower($admin_id = null) :array
    {
        if (empty($admin_id)) {
            if (!$this->isLogined()) {
                return [];
            }
            $admin = $this->user();
        } else {
            $admin = model('Admin')
                ->where('id', $admin_id)
                ->with(['AdminGroup'])
                ->find();
            if (empty($admin)) {
                return [];
            }
            $admin = $admin->toArray();
        }

        // 先检查是否有独立授权
        $exists = model('Power')
            ->where([
                ['admin_id', '=', $admin['id']]
            ])
            ->cache('AdminPowerIndependent_pk_' . $admin['id'], 86400, ['Power'], true)
            ->find();
        if (!empty($exists)) {
            return !empty($exists['content']) ? json_decode($exists['content'], true) : [];
        }

        if (empty($admin['AdminGroup'])) {
            return [];
        }
        // 获取用户对应角色的权限
        $powers = [];
        foreach ($admin['AdminGroup'] as $group) {
            $powers = array_merge($powers, $this->getAdminGroupPower($group['id']));
        }
        return array_unique($powers);
    }

    protected function getAdminPowerStore($request)
    {
        $route = ($request['addon'] ? $request['addon'] . '.' : '')
            . Str::snake($request['controller']) . '/' . strtolower($request['action']);
        $nokey = 'admin_power_store_for_' . $this->user('id') . '_no';
        $yeskey = 'admin_power_store_for_' . $this->user('id') . '_yes';
        if (Cache::has($nokey)) {
            $cache = Cache::get($nokey);
            if (in_array($route, (array)$cache)) {
                return false;
            }
        }
        if (Cache::has($yeskey)) {
            $cache = Cache::get($yeskey);
            if (in_array($route, (array)$cache)) {
                return true;
            }
        }
        return null;
    }

    protected function adminPowerStore($request, bool $result)
    {
        $route = ($request['addon'] ? $request['addon'] . '.' : '')
            . Str::snake($request['controller']) . '/' . strtolower($request['action']);

        $key = 'admin_power_store_for_' . $this->user('id') . '_' . ($result ? 'yes' : 'no');
        try {
            if (Cache::has($key)) {
                Cache::push($key, $route);
            } else {
                Cache::tag(['Power', 'AdminRule', 'AdminGroup', 'PowerStore'])->set($key, [$route]);
            }
        } catch (\Exception $e) {
            Cache::tag('PowerStore')->clear();
        }
    }
}