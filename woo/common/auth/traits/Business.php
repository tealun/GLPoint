<?php
declare (strict_types=1);

namespace woo\common\auth\traits;


use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use woo\common\helper\Str;
use woo\common\helper\Tree;

trait Business
{
    /**
     * 中台指定控制器\方法权限判断
     * @param array $request
     * @return bool
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    protected function businessPower(array $request = [])
    {
        $request['addon'] = isset($request['addon']) ? strtolower($request['addon']) : strtolower($this->request->getParams()['addon_name']);
        $request['controller'] = !empty($request['controller']) ? Str::studly($request['controller']) : Str::studly($this->request->getParams()['controller']);
        $request['action'] = !empty($request['action']) ? strtolower($request['action']) : strtolower($this->request->getParams()['action']);

        $cache_result = $this->getBusinessPowerStore($request);
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
            $this->businessPowerStore($request, true);
            return true;
        }

        $result = $this->getMethodPs($reflection, $request['action']);
        if (true === $result) {
            $this->businessPowerStore($request, true);
            return true;
        }

        $route = ($request['addon'] ? $request['addon'] . '.' : '')
            . Str::snake($request['controller']) . '/' . strtolower($result);

        $rules = Db::name('BusinessMenu')
            ->where([
                ['rule', '=', $route],
                ['is_open', '=', 1]
            ])
            ->column('id');
        // 路由没有在节点中
        if (empty($rules)) {
            return !Config::get('wooauth.force_check_power', false);
        }

        $allows = $this->getBusinessMemberPower();
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
        $this->businessPowerStore($request, $return);
        return $return;
    }

    protected function getBusinessPowerStore($request)
    {
        $route = ($request['addon'] ? $request['addon'] . '.' : '')
            . Str::snake($request['controller']) . '/' . strtolower($request['action']);
        $nokey = 'business_power_store_for_' . $this->user('id') . '_no';
        $yeskey = 'business_power_store_for_' . $this->user('id') . '_yes';
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

    protected function businessPowerStore($request, bool $result)
    {
        $route = ($request['addon'] ? $request['addon'] . '.' : '')
            . Str::snake($request['controller']) . '/' . strtolower($request['action']);

        $key = 'business_power_store_for_' . $this->user('id') . '_' . ($result ? 'yes' : 'no');

        if (Cache::has($key)) {
            Cache::push($key, $route);
        } else {
            Cache::tag(model_cache_tag(['BusinessMenu', 'BusinessMenuPower', 'BusinessRole', 'BusinessRolePower']))->set($key, [$route]);
        }
    }

    /**
     * 获取指定商家用户的权限（不允许跨商家）
     * @param null $member_id
     * @return array
     * @throws \think\Exception
     */
    public function getBusinessMemberPower($member_id = null) :array
    {
        // 如果当前商家没有进行角色授权，将默认开启全部权限
        $count = model('BusinessRolePower')->count();
        if (!$count) {
            return $this->getBusinessMenuPower();
        }
        if (empty($member_id)) {
            if (!$this->isLogined()) {
                return [];
            }
            $member = $this->user();
        } else {
            $member = model('BusinessMember')
                ->where('id', $member_id)
                ->with(['Business', 'BusinessRole'])
                ->find();
            if (empty($member)) {
                return [];
            }
            $member = $member->toArray();
        }
        if (empty($member['BusinessRole'])) {
            return [];
        }
        $powers = [];
        foreach ($member['BusinessRole'] as $group) {
            $powers = array_merge($powers, $this->getBusinessRolePower($group['id']));
        }
        return array_intersect($powers, $this->getBusinessMenuPower());
    }

    /**
     * 获取指定商家角色组权限
     * @param $group_id
     * @return array
     * @throws \think\Exception
     */
    public function getBusinessRolePower($group_id) :array
    {
        $group_id = (int) $group_id;
        if ($group_id <= 0) {
            return [];
        }
        $exists = model('BusinessRolePower')
            ->where([
                ['business_role_id', '=', $group_id]
            ])
            ->cache('BusinessRolePower_pk_' . $group_id, 86400, model_cache_tag('BusinessRolePower'), true)
            ->find();
        if (empty($exists)) {
            $business_group = (new Tree(model('BusinessRole')))->get($group_id);
            // 如果当前组没有授权 就返回父的授权
            return $business_group ? $this->getBusinessRolePower($business_group['parent_id']) : [];
        }
        return !empty($exists['content']) ? json_decode($exists['content'], true) : [];
    }

    /**
     * 获取指定商家对应的菜单权限id集合
     * @param null $business_id
     * @return array
     * @throws \think\Exception
     */
    public function getBusinessMenuPower($business_id = null) :array
    {
        if (empty($business_id)) {
            if (!$this->isLogined()) {
                return [];
            }
            $business = (array) $this->user()['Business'] ?? [];
            if (empty($business)) {
                return [];
            }
        } else {
            $business = model('Business')
                ->withoutGlobalScope(['business'])
                ->where([
                    ['id', '=', (int) $business_id]
                ])
                ->with('BusinessGroup')
                ->find();
            if (empty($business)) {
                return [];
            }
            $business = $business->toArray();
        }
        if (empty($business['BusinessGroup'])) {
            return [];
        }
        $powers = [];
        foreach ($business['BusinessGroup'] as $group) {
            $powers = array_merge($powers, $this->getBusinessGroupMenuPower($group['id']));
        }
        // 没有权限 是否全部开启
        if (empty($powers) && Config::get('wooauth.business_no_power_is_all', false)) {
            $powers = Db::name('BusinessMenu')
                ->where([
                    ['is_open', '=', 1]
                ])
                ->column('id');
        }
        return array_unique($powers);
    }

    /**
     * 获取指定商家组下的菜单权限id集合
     * @param $group_id
     * @return array|mixed
     * @throws \think\Exception
     */
    public function getBusinessGroupMenuPower($group_id) :array
    {
        $group_id = (int) $group_id;
        if ($group_id <= 0) {
            return [];
        }
        $exists = model('BusinessMenuPower')
            ->where([
                ['business_group_id', '=', $group_id]
            ])
            ->cache('BusinessMenuPower_pk_' . $group_id, 86400, model_cache_tag('BusinessMenuPower'), true)
            ->find();
        if (empty($exists)) {
            $business_group = (new Tree(model('BusinessGroup')))->get($group_id);
            // 如果当前组没有授权 就返回父的授权
            return $business_group ? $this->getBusinessGroupMenuPower($business_group['parent_id']) : [];
        }
        return !empty($exists['content']) ? json_decode($exists['content'], true) : [];
    }

    /**
     * 商家拦截
     */
    public function intercept($business_id = 0)
    {
        if (empty($business_id)) {
            $business_id = $this->user('business_id');
            if (empty($business_id)) {
                return false;
            }
        }

        $business = Db::name('Business')
            ->cache('business_static_cache_' . $business_id, 3600, 'Business')
            ->find((int) $business_id);
        if (empty($business)) {
            return false;
        }

        if (
            empty($business['is_verify'])
            || $business['start_time'] > time()
            || ($business['end_time'] && $business['end_time'] <= time())
        ) {
            return $business;
        }
        return true;
    }
}