<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use app\common\controller\Admin;
use woo\common\facade\Cache;
use think\facade\Db;
use woo\common\helper\Arr;
use woo\common\helper\Str;
use woo\common\install\AppDriver;
use woo\common\annotation\Ps;

class Application extends  Admin
{

    public function index()
    {
        $this->assign->installed = get_app();
        $this->assign->noinstall = $this->getAppDirs();

        $this->setHeaderInfo('title', '应用中心');
        if ($this->app->isDebug()) {
            $this->addAction('create', '新增应用', (string)url('create'), 'woo-theme-btn', 'layui-icon-add-1', 10);
        }
        return $this->fetch();
    }

    /**
     * 获取未安装应用
     * @return array
     */
    protected function getAppDirs()
    {
        $dir = opendir(base_path());
        $installed = get_app();
        $list = [];
        while (($reader = readdir($dir)) !== false) {
            if ($reader == '.' || $reader == '..') {
                continue;
            }
            if (is_dir(base_path() . $reader)) {
                if (array_key_exists($reader, $installed) || in_array($reader, ['admin','common','install'])) {
                    continue;
                }
                $class = "\\app\\" . $reader . "\\Install";
                if (class_exists($class)) {
                    $class = new $class($reader);
                    $list[$reader] = $class->getConfig();
                } else {
                    $list[$reader] = [
                        "title" => Str::studly($reader)
                    ];
                }
            }
        }
        return $list;
    }

    /**
     * @Ps(true,name="安装应用")
     */
    public function install()
    {
        $name = trim($this->args['name'] ?? '');
        if (empty($name)) {
            return $this->message('参数错误', 'error');
        }
        $driver = new AppDriver(Str::snake($name));
        //Db::startTrans();
        try {
             $driver->install();
            //Db::commit();
        } catch (\Exception $e) {
            //Db::rollback();
            return $this->message($e->getMessage(), 'error');
        }
        Cache::tag('Statistics')->clear();
        return $this->message('应用【'. $name .'】安装成功', 'success');
    }

    /**
     * @Ps(true,name="卸载应用")
     */
    public function uninstall()
    {
        if (!$this->app->isDebug()) {
            return $this->message('部署模式下不允许卸载应用', 'error');
        }
        $name = trim($this->args['name'] ?? '');
        if (empty($name)) {
            return $this->message('参数错误', 'error');
        }
        $driver = new AppDriver(Str::snake($name));
        Db::startTrans();
        try {
            $driver->uninstall();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $this->message($e->getMessage(), 'error');
        }

        return $this->message('应用【'. $name .'】卸载成功', 'success');
    }

    protected function beforeFormAssign()
    {
        if (!$this->request->isPost()) {
            if (!empty($this->local['data']['is_disuninstall'])) {
                $this->formPage->removeFormItem('is_disuninstall');
            }
        }
    }

    public function create()
    {
        if (!$this->app->isDebug()) {
            return $this->message('部署模式下不允许创建应用', 'error');
        }
        return parent::create();
    }

    public function modify()
    {
        $this->mdl->form['name']['elem'] = 'format';
        return parent::modify();
    }

    /**
     * @Ps(false)
     */
    public function delete()
    {

    }
    /**
     * @Ps(false)
     */
    public function batchDelete()
    {

    }
}