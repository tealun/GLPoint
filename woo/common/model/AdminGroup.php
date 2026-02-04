<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use think\facade\Config;

class AdminGroup extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
        $this->form['dashboard']['filter'] = 'trim';

        $this->form['parent_id']['optionsCallback'] = function ($level = 0, $value = '', $data = []) {
            $value = explode(',', (string)$value);
            return [
                [
                    'value' => 0,
                    'name' => '顶级分类',
                    'selected' => !empty($value) && $value[0]== 0,
                    'children' => $this->deepOptionsData2(tree('AdminGroup','children', 0), $level, 1, $value)
                ]
            ];
        };

        $this->form['custom_data_allow']['optionsCallback'] = function ($level = 0, $value = '', $data = []) {
            $value = explode(',', (string)$value);
            return $this->deepOptionsData(tree('Department','children', 0), $level, 1, $value);
        };
        if (!isset($this->form['custom_data_allow']['attrs']['data-max'])) {
            $this->form['custom_data_allow']['attrs']['data-max'] = 99;
        }
    }


    public function deepOptionsData2($children, $level = 0, $nowLevel = 1, array $value = [])
    {
        $list = [];
        $title = 'title';
        foreach ($children as $id) {
            $item = tree('AdminGroup', $id);
            $my = [
                "name" => $item[$title],
                "value" => $id,
            ];

            if ($id == Config::get('wooauth.super_group_id')) {
                $my['disabled'] = true;
            }

            if ($value && in_array($id, $value)) {
                $my['selected'] = true;
            }
            if (tree('AdminGroup', 'children', $id)) {
                $my['children'] = $this->deepOptionsData2(tree('AdminGroup', 'children', $id), $level, $nowLevel + 1, $value);
                //$my['disabled'] = true;
            }

            $list[] = $my;
        }
        return $list;
    }

    public function deepOptionsData($children, $level = 0, $nowLevel = 1, array $value = [])
    {
        $list = [];
        $title = 'title';
        foreach ($children as $id) {
            $item = tree('Department', $id);
            $my = [
                "name" => $item[$title],
                "value" => $id,
            ];

            if ($value && in_array($id, $value)) {
                $my['selected'] = true;
            }
            if (tree('Department', 'children', $id)) {
                $my['children'] = $this->deepOptionsData(tree('Department', 'children', $id), $level, $nowLevel + 1, $value);
                //$my['disabled'] = true;
            }

            $list[] = $my;
        }
        return $list;
    }

    public function checkDataAllow($value, $rule, $data)
    {
        if (isset($data['parent_id']) && $data['parent_id'] == 0 && $value == -1) {
            return '顶级角色不能选择继承';
        }
        return true;
    }
}