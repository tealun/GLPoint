<?php
declare (strict_types=1);

namespace woo\common\install;

use woo\common\facade\Cache;
use think\facade\Db;
use woo\common\Auth;
use woo\common\helper\Str;
use woo\common\helper\CreateFile;

class AddonsDriver
{
    protected $addonName = '';
    protected $addonPath = '';
    protected $nameSpace = '';

    public function __construct(string $addonName)
    {
        $this->addonName = $addonName;
        $this->addonPath = app()->addons->getAddonPath($addonName);
        $this->nameSpace = "\\addons\\" . $addonName;
    }

    protected function check()
    {
        if (!is_dir($this->addonPath)) {
            throw new \Exception('插件目录' . $this->addonPath . '不存在');
        }
        $install = $this->nameSpace . "\\Install";
        if (!class_exists($install)) {
            throw new \Exception('插件主安装程序' . $install . '不存在');
        }
        if (get_installed_addons($this->addonName)) {
            throw new \Exception('当前插件' . $this->addonName . '已经存在，无需安装');
        }
        return true;
    }

    protected function importSql()
    {
        $file = $this->addonPath . 'install' . DIRECTORY_SEPARATOR . 'install.sql';
        if (is_file($file)) {
            $sqls = get_file_sql($file, get_db_config('prefix'), get_db_config('charset'), '__PREFIX__');
            foreach ($sqls as $sql) {
                if (empty($sql)) {
                    continue;
                }
                try {
                    Db::execute($sql);
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }
        }
        return true;
    }

    public function install()
    {
        try {
            $this->check();
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        foreach ($this->getInstallDirs() as $dir) {
            if (is_dir($this->addonPath . 'install' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR)) {
                copydirs($this->addonPath . 'install' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR, root_path() . $dir);
            }
        }
        try {
            $install = $this->nameSpace . "\\Install";
            $class = new $install($this->addonName);
            $config = $class->getConfig();
            $class->install();
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        try {
            $this->importSql();
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $config['name'] = $this->addonName;
        $config['title'] = !empty($config['title']) ? $config['title'] : Str::studly($config['name']);
        $config['version'] = !empty($config['version']) ? $config['version'] : '1.0.0';
        $config['is_verify'] = 1;
        $config['admin_id'] = (new Auth())->user('id', ['withJoin' => ['AdminGroup', 'Department']]);
        $config['describe'] = $config['describe'] ?? '';
        try {
            $addonModel = model('Addon');
            $addon_id = $addonModel->createData($config);
            if (!$addon_id) {
                throw new \Exception(array_values($addonModel->getError())[0] ?? 'addon表写入失败');
            }
            Db::name('AddonSetting')
                ->where('addon_id', '=', -1)
                ->update(['addon_id' => $addon_id]);
            Cache::tag('AddonSetting')->clear();
            $models = model('Model')->where('addon', '=', $this->addonName)->column('id');
            foreach ($models as $model_id) {
                (new CreateFile)->createModel($model_id);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        Cache::tag('AdminRule')->clear();
        Cache::tag('Model')->clear();
        Cache::tag('Field')->clear();
        return true;
    }

    public function uninstall()
    {
        $appinfo  = get_installed_addons($this->addonName);
        if (!$appinfo) {
            throw new \Exception('当前插件' . $this->addonName . '不存在');
        }
        if (!is_dir($this->addonPath)) {
            model('Addon')->destroy($appinfo['id']);
            return false;
        }
        if (!empty($appinfo['is_disuninstall'])) {
            throw new \Exception('当前插件' . $this->addonName . '已被禁止卸载');
        }

        // 卸载默认不会做太多事情 只会走addon表删除记录而已  需要删除目录、数据等 自行在应用的uninstall方法定义
        try {
            $install = $this->nameSpace . "\\Install";
            if (class_exists($install)) {
                $class = new $install($this->addonName);
                $config = $class->getConfig();
                $class->uninstall();
            }
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        try {
            $appinfo = get_installed_addons($this->addonName);
            Db::name('AddonSetting')
                ->where('addon_id', '=', $appinfo['id'])
                ->delete();
            model('Addon')->destroy($appinfo['id']);
            Cache::tag('AddonSetting')->clear();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        Cache::tag('AdminRule')->clear();
        Cache::tag('Model')->clear();
        Cache::tag('Field')->clear();
        return true;
    }

    protected function getInstallDirs()
    {
        return [
            'app',
            'public',
            'woo'
        ];
    }
}