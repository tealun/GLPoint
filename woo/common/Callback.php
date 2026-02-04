<?php
declare (strict_types=1);

namespace woo\common;

class Callback
{
    /**
     * 当前控制器对象
     */
    protected $controller;// 请求的控制器实例
    protected $c;
    protected $a;
    protected $ca;
    protected $appCallback;

    public function __construct($controller)
    {
        // $this->controller 就是你控制器写的 $this
        $this->controller = $controller;
        $this->c          = $this->controller->params['controller'] ?? '';
        $this->a          = $this->controller->params['action'] ?? '';
        $this->ca         = $this->c . '::' . $this->a;
        $this->appCallback = "\\app\\" . app('http')->getName() . "\\Callback";
    }

    public function before()
    {
        // 执行全局before
        $this->globalBefore();

        // 执行当前应用before
        $appbefore = app('http')->getName() . 'Before';
        if (method_exists($this, $appbefore)) {
            $this->$appbefore();
        }
        if (class_exists($this->appCallback)) {
            app($this->appCallback, [$this->controller])->before();
        }
    }

    // 每个应用都可以会执行
    protected function globalBefore()
    {

    }

    // 当前请求admin应用才会执行
    protected function adminBefore()
    {
        /**
         * 被继承模板文件（父模板文件）定位并assign到页面中
         * 我也很无奈，把父模板文件直接继承[../woo/admin/view/xx.html]吧，虽然可以，但如果有人需要改根目录需要全部重新修改路径
         * 只能先这样变向解决了，虽然麻烦点....
         */
        $this->controller->assign['extend_global'] = $this->controller->assign->parseTemplate('/global');
        $this->controller->assign['extend_base'] = $this->controller->assign->parseTemplate('/base');
        $this->controller->assign['extend_list'] = $this->controller->assign->parseTemplate('/list');
        $this->controller->assign['extend_form'] = $this->controller->assign->parseTemplate('/form');
        $this->controller->assign['extend_detail'] = $this->controller->assign->parseTemplate('/detail');
        $this->controller->assign['extend_tree'] = $this->controller->assign->parseTemplate('/tree');
        $this->controller->assign['common_header'] = $this->controller->assign->parseTemplate('/common/header');

        // 全局js
        $this->controller->assign->addJs([
            'jquery-3.4.1.min',
        ]);

        $this->controller->assign->addJs([
            '/layui/layui',
            '/woo/pear/component/pear/pear.js',
            '/woo/woo',
            '/files/sortable/Sortable.min.js',
            'touch-0.2.14.min',
            '/woo/js/admin',
            'admin/common'
        ], true);

        // 全局css
        $this->controller->assign->addCss([
            '/layui/css/layui',
            '/woo/pear/component/pear/css/pear.css',
            '/woo/pear/admin/css/loader.css',
            '/woo/pear/admin/css/admin.css',
           '/woo/css/admin/global',
            'animate',
            '/woo/iconfonts/iconfont.css',
            '/woo/css/woo'
        ]);

        $this->controller->assign->is_pear = true;
    }

    // +--------------------------------------------分界线--------------------------------------------------------------
    // 上面是调用当前请求方法之前执行
    // 下面是即将渲染页面之前（逻辑业务已经完成）执行 可以拦截到所有assign出去的数据
    // +--------------------------------------------分界线--------------------------------------------------------------

    public function after()
    {
        // 执行全局after
        $this->globalAfter();

        // 执行当前应用before
        $appafter= app('http')->getName() . 'After';
        if (method_exists($this, $appafter)) {
            $this->$appafter();
        }

        if (class_exists($this->appCallback)) {
            app($this->appCallback, [$this->controller])->after();
        }

    }

    // 每个应用都可以会执行
    protected function globalAfter()
    {

    }

    // 当前请求admin应用才会执行
    protected function adminAfter()
    {
        // 全局css
        $this->controller->assign->addCss([
            'admin/common'
        ]);
    }
}