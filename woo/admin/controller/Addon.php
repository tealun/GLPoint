<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use app\common\controller\Admin;
use think\facade\Cache;
use think\facade\Db;
use woo\common\helper\Arr;
use woo\common\helper\Str;
use woo\common\install\AddonsDriver;
use woo\common\annotation\Ps;

class Addon extends Admin
{
    public function index()
    {
        $this->assign->installed = get_installed_addons();
        $this->assign->noinstall = $this->getAppDirs();
        $this->setHeaderInfo('title', '插件管理');
        if ($this->app->isDebug()) {
            $this->addAction('create', '新增插件', (string)url('create'), 'woo-theme-btn', 'layui-icon-add-1', 10);
        }
        return $this->fetch();
    }

    protected function getAppDirs()
    {
        $addons_path = $this->app->addons->getAddonsPath();
        $dir = opendir($addons_path);
        $installed = get_installed_addons();
        $list = [];
        while (($reader = readdir($dir)) !== false) {
            if ($reader == '.' || $reader == '..') {
                continue;
            }
            if (is_dir($addons_path . $reader)) {
                if (array_key_exists($reader, $installed)) {
                    continue;
                }
                $class = "\\addons\\" . $reader . "\\Install";
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
     * @Ps(true,name="安装插件")
     */
    public function install()
    {
        $name = trim($this->args['name'] ?? '');
        if (empty($name)) {
            return $this->message('参数错误', 'error');
        }
        $driver = new AddonsDriver(Str::snake($name));
        //Db::startTrans();
        try {
            $driver->install();
            //Db::commit();
        } catch (\Exception $e) {
            //Db::rollback();
            return $this->message($e->getMessage(), 'error');
        }
        Cache::tag('Statistics')->clear();
        return $this->message('插件【'. $name .'】安装成功', 'success');
    }

    /**
     * @Ps(true,name="卸载插件")
     */
    public function uninstall()
    {
        if (!$this->app->isDebug()) {
            return $this->message('部署模式下不允许卸载插件', 'error');
        }
        $name = trim($this->args['name'] ?? '');
        if (empty($name)) {
            return $this->message('参数错误', 'error');
        }
        $driver = new AddonsDriver(Str::snake($name));
        Db::startTrans();
        try {
            $driver->uninstall();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $this->message($e->getMessage(), 'error');
        }

        return $this->message('插件【'. $name .'】卸载成功', 'success');
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
            return $this->message('部署模式下不允许创建插件', 'error');
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