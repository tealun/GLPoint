<?php
namespace woo\admin\controller;

use app\common\controller\Admin;

class Region extends Admin
{
    use \woo\common\controller\traits\Tree;

    public function index()
    {
        $this->local['fields'] = [
            'code' => [
                'title' => '编号',
                'templet' => '{{d.code}}',
                'style' => 'color:#01AAED;'
            ],
            'children_count' => [
                'title' => '子' . $this->mdl->cname . '数',
                'templet' => '{{# if (d.children_count> 0){ }}{{d.children_count}}{{#} }}',
                'style' => 'color:#36b368;'
            ],

        ];
        $this->local['options']['is_ajax'] = true;
        return $this->showList();
    }

}