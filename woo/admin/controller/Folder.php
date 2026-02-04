<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use app\common\controller\Admin;

class Folder extends Admin
{
    use \woo\common\controller\traits\Tree;

    public function index()
    {
        return $this->showTree();
    }
}