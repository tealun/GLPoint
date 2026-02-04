<?php
declare (strict_types=1);


namespace woo\common\model;

use app\common\model\App;
use woo\common\helper\Str;
use woo\common\helper\Pinyin;
use woo\common\facade\Auth;

class AdminRule extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
        $this->form['parent_id']['optionsCallback'] = function ($level = 0, $value = '') {
            $top = [
                "name" => '顶级分类',
                "value" => 0,
            ];
            if ($value == 0) {
                $top['selected'] = true;
            }
            $top['children'] = $this->deepParentIdXmOptionsData(admin_rule('children', 0), $level, 1, $value);
            return [$top];
        };
    }

    public function getCustomCache()
    {
        return [
            'nav' => [
                'where' => [
                    ['type', 'IN', ['menu', 'directory']],
                    ['is_nav', '=', 1]
                ]
            ]
        ];
    }

    public function beforeWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $data = $this->getData();
        if (empty($data['type'])) {
            $this['type'] = empty($this['controller']) ? 'directory': 'menu';
        }
        if (!empty($this['addon'])) {
            $this['addon'] = strtolower(trim($this['addon']));
        }
        if (!empty($this['controller'])) {
            $this['controller'] = Str::snake(trim($this['controller']));
        }
        if (!empty($this['action'])) {
            $this['action'] = strtolower(trim($this['action']));
        }
        if (!empty($data['title']) && empty($data['pinyin'])) {
            $this['pinyin'] = strtolower(Pinyin::get($data['title']));
        }
        if (!empty($data['title']) && empty($data['jianpin'])) {
            $this['jianpin'] = strtolower(Pinyin::get($data['title'], true));
        }
        if (!empty($this['controller']) && !empty($this['action'])) {
            $this['rule'] = (strtolower($this['addon'] ? $this['addon'] . "." : "")
                . Str::snake($this['controller']) . "/" . $this['action']);
        }
        return $parent_return;
    }

    public function checkController($value, $rule, $data)
    {
        if (!empty($data['type']) && empty($data['url']) && in_array($data['type'], ['menu', 'button']) && empty($value)) {
            return '控制器不能为空';
        }
        return true;
    }

    public function checkType($value, $rule, $data)
    {
        if (admin_rule($value, 'type') == 'button') {
            return '父菜单的类型不能是"按钮"';
        }
        return true;
    }

    public function checkAction($value, $rule, $data)
    {
        if (!empty($data['type']) && empty($data['url']) && in_array($data['type'], ['menu', 'button']) && empty($value)) {
            return '方法不能为空';
        }
        return true;
    }

    public function getPearMenu()
    {
        $allows = Auth::getAdminPower();
        return $this->getPearMenuForParent(0, $allows);
    }

    public function getPearMenuForParent($parent_id = 0, $allows = [])
    {
        $ids = admin_rule('nav_children', $parent_id);
        if (empty($ids)) {
            return [];
        }
        $ids = array_intersect($ids, $allows);

        $data = [];

        foreach ($ids as $id) {
            array_push($data, [
                'id' => $id,
                'title' => admin_rule($id, 'title'),
                'type' => admin_rule($id, 'type') == 'menu' ? 1 : 0,
                'icon' => trim(admin_rule($id, 'icon') ? (strpos(admin_rule($id, 'icon'), 'layui-icon') !== false ? 'layui-icon ' . admin_rule($id, 'icon') : admin_rule($id, 'icon')) : ''),
                'href' => admin_rule_link($id),
                'openType' => admin_rule($id, 'open_type') ? admin_rule($id, 'open_type') :'_iframe',
                'children' => $this->getPearMenuForParent($id, $allows),
                'pinyin' => admin_rule($id, 'pinyin'),
                'jianpin' => admin_rule($id, 'jianpin'),
                'func' => admin_rule($id, 'js_func'),
            ]);
        }
        return $data;
    }

    public function deepParentIdXmOptionsData($children, $level = 0, $nowLevel = 1, $value = 0)
    {
        $list = [];
        $title = $this->display;
        foreach ($children as $id) {
            $item = admin_rule($id);
            if (admin_rule($id, 'type') == 'button') {
                continue;
            }
            $my = [
                "name" => $item[$title],
                "value" => $id,
            ];
            if ($value == $id) {
                $my['selected'] = true;
            }
            if (admin_rule( 'children', $id)) {
                $my['children'] = $this->deepParentIdXmOptionsData(admin_rule('children', $id), $level, $nowLevel + 1, $value);
            }
            $list[] = $my;
        }
        return $list;
    }
}