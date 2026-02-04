<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use think\facade\Config;
use think\facade\Db;
use woo\common\Auth;
use woo\common\helper\Str;

class Admin extends App
{
    protected function afterStart()
    {
        // 自定义侧边栏数据测试
        //$data = $this->getLayuiTree();
        $this->tableTab['basic']['siderbar'] = [
            [
               'foreign' => 'Department',
               //'data' => $data
            ],
            [
                'foreign' => 'AdminGroup',
            ]
        ];
        //$this->tableTab['basic']['table']['skin'] = 'line';
        //$this->tableTab['basic']['table']['even'] = true;

        $this->form['custom_data_allow']['optionsCallback'] = function ($level = 0, $value = '', $data = []) {
            $value = explode(',', (string)$value);
            return $this->deepOptionsData(tree('Department','children', 0), $level, 1, $value);
        };
        if (!isset($this->form['custom_data_allow']['attrs']['data-max'])) {
            $this->form['custom_data_allow']['attrs']['data-max'] = 99;
        }

        parent::{__FUNCTION__}();
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

    protected function getLayuiTree($level = 0)
    {
        return $this->deepLayuiTreeData(tree('Department','children', 0), $level);
    }

    protected function deepLayuiTreeData($children, $level = 0, $nowLevel = 1)
    {
        $list = [];
        $title = 'title';
        foreach ($children as $id) {
            $item = tree('Department', $id);
            // 这里就是你自己的条件  排除 我只是随便测试一个条件
            if ($item['id'] < 1) {
                continue;
            }
            $my = [
                "title" => $item[$title],
                "id" => $id,
                "href" => (string) url(app('request')->getParams()['action'], [Str::snake($this->name) .'_id' => $id]),
                "spread" => $nowLevel > 1 ? false : true
            ];
            if ((($level && $nowLevel < $level) || !$level) && tree('Department', 'children', $id)) {
                $my['children'] = $this->deepLayuiTreeData(tree('Department', 'children', $id), $level, $nowLevel + 1);
            }
            $list[] = $my;
        }
        return $list;
    }

    public function beforeInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $this->salt = Str::random(mt_rand(4, 8), 0);
        return $parent_return;
    }

    public function afterWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!empty($this->password)) {
            $pk = $this->getPk();
            $salt = $this->salt ?: $this->where($pk, '=', $this[$pk])->value('salt');
            Db::name($this->name)
                ->where($pk, '=', $this[$pk])
                ->update([
                    'password' => Auth::password($this->password, $salt)
                ]);
        }
        return $parent_return;
    }

    public function beforeUpdateCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (isset($this->password)) {
            if (empty(trim($this->password)) || strlen($this->password) == 32) {
                unset($this->password);
            }
        }
        return $parent_return;
    }

    public function checkPwd($val)
    {
        if (empty($val)) {
            return true;
        }
        $count = ['upper' => 0, 'lower' => 0, 'number' => 0, 'else' => 0];
        for ($i = 0; $i <= strlen($val) - 1; $i++) {
            $ord = ord($val[$i]);
            if ($ord >= 48 && $ord <= 57) {
                $count['number'] = 1;
            } elseif ($ord >= 65 && $ord <= 90) {
                $count['upper'] = 1;
            } elseif ($ord >= 97 && $ord <= 122) {
                $count['lower'] = 1;
            } else {
                $count['else'] = 1;
            }
        }

        if ($count['upper'] + $count['lower'] + $count['number'] + $count['else'] <= 2) {
            return '密码需含大写、小写、数字、符号中任意三种';
        }
        return true;
    }

    public function checkDepartment($value)
    {
        if (!empty($value)) {
            $count = model('Department')->where('parent_id', '=', $value)->count();
            if ($count) {
                return '请选择到具体下级部门';
            }
        }
        return true;
    }

    public function checkAdminGroup($value)
    {
        if (!empty($value)) {
            $groups = array_diff(explode(',', (string) $value), ['']);
            if (in_array(Config::get('wooauth.super_group_id'), $groups) && count($groups) > 1) {
                return '拥有"超级管理"角色用户不能再选择其他角色';
            }
        }
        return true;
    }
}