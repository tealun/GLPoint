<?php
declare (strict_types=1);

namespace woo\common\helper;

use think\facade\Cache;
use think\facade\Db;
use woo\common\facade\Auth;

class Setting
{

    public static function getAdminSetting($js = false)
    {
        $key = 'setting_static_cache' . ($js ? '_js' : '');

        if (Cache::has($key)) {
            $list = Cache::get($key);
        } else {
            try {
                $where = [];
                if ($js) {
                    $where[] = ['is_js_var', '=', 1];
                }
                $list = Db::name('Setting')
                    ->where($where)
                    ->field(['var', 'value'])
                    ->select()
                    ->toArray();
                $list = Str::deepJsonDecode(Arr::combine($list, 'var', 'value'));

                if (class_exists(\app\common\model\BusinessSetting::class)) {
                    $where[] = ['is_business', '=', 0];
                    $blist = Db::name('BusinessSetting')
                        ->where($where)
                        ->field(['var', 'value'])
                        ->select()
                        ->toArray();
                    $list = array_merge(Str::deepJsonDecode(Arr::combine($blist, 'var', 'value')), $list);
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            Cache::tag(['Setting', 'BusinessSetting'])->set($key, $list);
        }
        return $list;
    }

    public static function getBusinessSetting($js = false)
    {
        $where = [];
        if ($js) {
            $where[] = ['is_js_var', '=', 1];
        }
        if (empty(app('request')->business_id)) {
            $where[] = ['is_business', '=', 0];
            $list = Db::name('BusinessSetting')
                ->where($where)
                ->field(['var', 'value'])
                ->cache('business_setting_static_else_' . ($js ? '_js' : ''), 3600, 'BusinessSetting')
                ->select()
                ->toArray();
            return Str::deepJsonDecode(Arr::combine($list, 'var', 'value'));
        }
        $business_id = app('request')->business_id;
        $loginid = Auth::user('id');

        $list = Db::name('BusinessSetting')
            ->where($where)
            ->field(['id', 'var', 'value', 'is_business', 'is_business_member'])
            ->cache('business_setting_static' . ($js ? '_js' : ''), 3600, model_cache_tag('BusinessSetting'))
            ->select()
            ->toArray();
        if (empty($list)) {
            return [];
        }
        $list = Arr::combine($list, 'id');

        $business_list = model('BusinessSettingValue')
            ->where([
                ['business_setting_id', 'IN', array_keys($list)],
                ['business_member_id', '=', 0]
            ])
            ->field(['business_setting_id', 'value'])
            ->cache('business_setting_value_static' . ($js ? '_js' : '') . '_id_' . $business_id, 3600, model_cache_tag('BusinessSettingValue'), true)
            ->select()
            ->toArray();
        if ($business_list) {
            $business_list = Arr::combine($business_list, 'business_setting_id');
            foreach ($business_list as $id => $item) {
                if (isset($list[$id]) && $list[$id]['is_business']) {
                    $list[$id]['value'] = $item['value'];
                }
            }
        }
        if ($loginid) {
            $business_list = model('BusinessSettingValue')
                ->where([
                    ['business_member_id', '=', $loginid],
                    ['business_setting_id', 'IN', array_keys($list)]
                ])
                ->field(['business_setting_id', 'value'])
                ->cache('business_setting_value_static' . ($js ? '_js' : '') . '_member_id_' . $loginid, 3600, model_cache_tag('BusinessSettingValue'), true)
                ->select()
                ->toArray();
            if ($business_list) {
                $business_list = Arr::combine($business_list, 'business_setting_id');
                foreach ($business_list as $id => $item) {
                    if (isset($list[$id]) && $list[$id]['is_business'] && $list[$id]['is_business_member']) {
                        $list[$id]['value'] = $item['value'];
                    }
                }
            }
        }
        return Str::deepJsonDecode(Arr::combine($list, 'var', 'value'));
    }
}