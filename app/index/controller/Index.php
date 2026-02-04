<?php
declare (strict_types = 1);

namespace app\index\controller;


class Index extends \app\common\controller\Index
{
    public function index()
    {
        return view('index/index');
    }
}
