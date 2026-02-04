<?php
declare (strict_types = 1);

namespace app\index\controller;


class Index extends \app\common\controller\Index
{
    public function index()
    {
        return "测试应用，可以删除或自行开发！";
    }
}
