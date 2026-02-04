<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use think\facade\Cache;
use woo\common\annotation\Forbid;
use woo\common\annotation\Ps;
use woo\common\Auth;
use app\common\builder\FormPage;
use woo\common\helper\Arr;
use woo\common\helper\CreateFile;
use woo\common\helper\Str;

/**
 * Class Tool
 * @Ps(name="工具")
 * @Forbid(true)
 */
class Tool extends \app\common\controller\Admin
{

    /**
     * @Ps(false)
     */
    public function index()
    {}

    /**
     * @Ps(name="清除缓存")
     * @Forbid(only={"ajax"})
     */
    public function clearCache()
    {
        try {
            Cache::clear();
        } catch (\Exception $e) {
            return $this->ajax('error', '操作过快，缓存清除失败！请稍后再试');
        }
        return $this->ajax('success', '缓存清除成功！');
    }

    /**
     * @Ps(name="日志下载")
     * @Forbid(only={"get"})
     */
    public function getLog()
    {
        if (!extension_loaded('fileinfo')) {
            return $this->message('error', '当前服务器没有"fileinfo"扩展，下载失败');
        }
        try {
            $name = 'log_' . date('Ymd');
            $file = zip_dir(runtime_path() . 'log', $name);
            if ($file && is_file($file)) {
                return download($file, $name . '.zip');
            } else {
                return $this->message('error', '目前还没有日志文件，请稍后再试');
            }
        } catch (\Exception $e) {
            return $this->message( $e->getMessage(), 'error');
        }
    }
    /**
     * @Ps(name="清临时文件")
     * @Forbid(only={"ajax","get"})
     */
    public function removeTemp()
    {
        if (run_mode() === 'swoole') {
            return $this->ajax('error', '由于swoole模式是常驻内存的，删除临时文件会导致文件错误，请手动在runtime下删除');
        }
        try {
            rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'tempfile', true);
            $apps = [];
            foreach (get_app() as $item) {
                $apps[] = $item['name'];
            }
            foreach (array_merge(['admin', 'index', 'api', 'install', 'home', 'model_update', 'log'], $apps) as $app) {
                rmdirs(root_path() . 'runtime' . DIRECTORY_SEPARATOR . $app, true);
            }
            if (is_file(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update.zip')) {
                unlink(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'model_update.zip');
            }
        } catch (\Exception $e) {
            return $this->ajax('error',  $e->getMessage());
        }
        return $this->ajax('success', '临时文件全部清除成功');
    }

    /**
     * @Ps(false,name="获取大小")
     * @Forbid(only={"ajax","get"})
     */
    public function getSiteSize()
    {
        set_time_limit(0);
        return $this->ajax('success',return_size(get_dir_size(root_path())));
    }

    /**
     * @Ps(false)
     * @Forbid(only={"ajax","post"})
     * @\woo\common\annotation\Log(remove={"pwd"})
     */
    public function relieveScreen()
    {
        $data = $this->request->post();
        if (trim($data['pwd'])) {

            $lock_pwd = Auth::password($data['pwd'], (new Auth())->user('salt'));

            if ($lock_pwd == (new Auth())->user('password')) {
                return $this->ajax('success', '解屏成功');
            } else {
                return $this->ajax('error', '密码输入不一致');
            }
        } else {
            return $this->ajax('error', '请输入密码');
        }
    }

    /**
     * @Ps(name="创建模板")
     * @Forbid(nodebug=true)
     */
    public function makeTemplate()
    {
        $form = new FormPage();
        $applist = ['admin' => '后台[admin]'];
        if (get_app('business')) {
            $applist['business'] = '中台[business]';
        }

        $form
            ->addFormItem('app', 'radio')
            ->setLabelAttr('应用')
            ->setOptionsAttr($applist)
            ->setTipAttr('不勾选就生成到admin')
            ->setItemValue('admin');

        $form->addFormItem('addon', 'text')->setLabelAttr('二级目录')->setTipAttr('没有请保持为空');
        $form->addFormItem('controller', 'text')->setLabelAttr('控制器名');
        $form->addFormItem('template', 'text', [
            'options' => [
                'list' => '列表模板[list]',
                'form' => '表单模板[form]',
            ]
        ])->setLabelAttr('模板名称')->setTipAttr('不需要写.html后缀');

        if ($this->request->isPost()) {
            $data = $form->getData();
            if (empty($data['app'])) {
                $data['app'] = 'admin';
            }
            if (empty(trim($data['controller']))) {
                $form->forceError('controller', '控制器名不能为空');
            }
            if (empty(trim($data['template']))) {
                $form->forceError('template', '模板名不能为空');
            }

            if (!$form->getError()) {
                $path = base_path() . trim(Str::snake($data['app'])) . DIRECTORY_SEPARATOR  . 'view' . DIRECTORY_SEPARATOR;

                if (!empty(trim($data['addon']))) {
                    $path .= trim($data['addon']) . DIRECTORY_SEPARATOR;
                }
                $path .= Str::snake(trim($data['controller'])) . DIRECTORY_SEPARATOR;
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                $path .= trim($data['template']) . '.html';
                if (!is_file($path)) {
                    $name = trim($data['template']);
                    if ($name == 'list') {
                        $template = <<<DOC
{extend name="\$extend_list"/}

{block name="script"}
    {// 一般就在该block中自定义一些 列表、按钮等模板，如需重写其他block，请自行添加对应block，本句可以删除}
    <script type="text/html" id="xxx">
    // 自定义某种模板，支持layui模板引擎语法，参数通过d变量获取{{console.log(d)}}，也可以直接获取全局变量
    </script>
    
    <script>
    // 自定义JS代码
    </script>
{/block}
DOC;

                    } elseif ($name == 'form') {
                        $template = <<<DOC
{extend name="\$extend_form"/}

{block name="script"}
    {// 一般就在该block中自定义一些 列表、按钮等模板，如需重写其他block，请自行添加对应block，本句可以删除}
    <script>
    // layui.form 已完成 通过layForm获取
    function mytest()
    {
        // layForm 已准备好 获取到layui.form实例 一般在该函数中做一些初始化任务         
    }   
    
    // 监听表单提交
    function formSubmitCallback(data)
    {
        // console.log(data.elem) //被执行事件的元素DOM对象，一般为button对象
        // console.log(data.form) //被执行提交的form对象，一般在存在form标签时才会返回
        // console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
        // 自定义回调中return false 可以阻止提交
        return true;
    }
    </script>
{/block}
DOC;
                    } else {
                        $template = <<<DOC
{extend name="\$extend_global"/}
{block name="headscript"}
{/block}
{block name="header"}{/block}
{block name="content"}
<div class="woo-main">
    <div class="woo-main-container">
        {include file="\$common_header"/}
        你自定义的代码
    </div>
</div>
{/block}
{block name="footer"}{/block}
{block name="script"}{/block}
DOC;
                    }
                    try{
                        file_put_contents($path, $template);
                        return $this->message('模板创建成功：' . $path, 'success');
                    } catch (\Exception $e) {
                        $form->forceError('', $e->getMessage());
                    }
                }
                $form->forceError('', '模板名文件已存在：' . $path);
            }
        }

        $this->assign->form = $form;
        $this->setHeaderInfo('title', '创建后台模板');
        return $this->fetch('form');
    }

    /**
     * @Ps(name="创控制器")
     * @Forbid(nodebug=true)
     */
    public function makeController()
    {
        $form = new FormPage();
        $apps = get_app();
        $applist = ['admin' => '后台[admin]'];
        if ($apps) {
            foreach ($apps as $name => $item) {
                $applist[$name] = $item['title'] ? $item['title'] . '[' . $name .']' : $name;
            }
        }

        $addons = get_installed_addons();
        $addonlist = [];
        if ($addons) {
            foreach ($addons as $name => $item) {
                $addonlist[$name] = $item['title'] ? $item['title'] . '[' . $name .']' : $name;
            }
        }

        $form
            ->addFormItem('type', 'radio')
            ->setLabelAttr('控制器类型')
            ->setOptionsAttr(['app' => '创建应用控制器[app]', 'addons' => '创建插件控制器[addons]'])
            ->setItemValue('app');
        $form
            ->addFormItem('addon_name', 'select')
            ->setLabelAttr('插件名称')
            ->setOptionsAttr($addonlist)
            ->setTipAttr('必须选择指定的插件');

        $form->addFormItem('app', 'checkbox')->setLabelAttr('应用')->setOptionsAttr($applist)->setTipAttr('不勾选就生成到admin');
        $form->addFormItem('addon', 'text')->setLabelAttr('二级目录')->setTipAttr('没有请保持为空');
        $form->addFormItem('controller', 'text')->setLabelAttr('控制器名');

        $form->addTrigger('type', [
            'app' => ['app', 'addon'],
            'addons' => ['addon_name']
        ]);

        if ($this->request->isPost()) {
            $data = $form->getData();

            if ($data['type'] == 'app') {
                if (empty($data['app'])) {
                    $data['app'] = ['admin'];
                }
                if (!empty(trim($data['controller']))) {
                    try {
                        $result = [];
                        foreach ($data['app'] as $app) {
                            $result[] = (new CreateFile)->createController($data['controller'], $data['addon'], $app);
                        }
                        if ($result) {
                            return $this->message("应用控制器" . implode(',', $result) . "创建成功", 'success');
                        }
                        return $this->message("创建失败：应用控制器已存在或写入失败，请手动处理", 'error');
                    } catch (\Exception $e) {
                        return $this->message("创建失败：" . $e->getMessage(), 'error');
                    }
                } else {
                    $form->forceError('controller', '控制器名不能为空');
                }
            } elseif ($data['type'] == 'addons') {
                if (empty(trim($data['controller']))) {
                    $form->forceError('controller', '控制器名不能为空');
                }
                if (empty(trim($data['addon_name']))) {
                    $form->forceError('addon_name', '插件目录不能为空');
                }
                if (!empty(trim($data['controller'])) && !empty(trim($data['addon_name']))) {
                    try {
                        $result = (new CreateFile)->createAddonController($data['controller'], $data['addon_name']);

                        if ($result) {
                            return $this->message("插件控制器" . $result . "创建成功", 'success');
                        }
                        return $this->message("创建失败：插件控制器已存在或写入失败，请手动处理", 'error');
                    } catch (\Exception $e) {
                        return $this->message("创建失败：" . $e->getMessage(), 'error');
                    }
                }
            }
        }

        $this->assign->form = $form;
        $this->setHeaderInfo('title', '创控制器');
        return $this->fetch('form');
    }
}