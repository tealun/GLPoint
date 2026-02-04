<?php
declare (strict_types=1);

namespace woo\common\install;

use think\facade\Cache;
use think\facade\Db;
use woo\common\Auth;
use woo\common\helper\Str;
use woo\common\helper\CreateFile;

class AppDriver
{
    protected $appName = '';
    protected $appPath = '';
    protected $nameSpace = '';

    public function __construct(string $appName)
    {
        $this->appName = $appName;
        $this->appPath = base_path() . $appName . DIRECTORY_SEPARATOR;
        $this->nameSpace = "\\app\\" . $appName;
    }

    protected function check()
    {
        if (!is_dir($this->appPath)) {
            throw new \Exception('应用目录' . $this->appPath . '不存在');
        }
        $install = $this->nameSpace . "\\Install";
        if (!class_exists($install)) {
            throw new \Exception('应用主安装程序' . $install . '不存在');
        }
        if (get_app($this->appName)) {
            throw new \Exception('当前应用' . $this->appName . '已经存在，无需安装');
        }
        return true;
    }

    protected function importSql()
    {
        $file = $this->appPath . 'install' . DIRECTORY_SEPARATOR . 'install.sql';
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
            if (is_dir($this->appPath . 'install' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR)) {
                copydirs($this->appPath . 'install' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR, root_path() . $dir);
            }
        }

        try {
            $install = $this->nameSpace . "\\Install";
            $class = new $install($this->appName);
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

        $config['name'] = $this->appName;
        $config['title'] = !empty($config['title']) ? $config['title'] : Str::studly($config['name']);
        $config['version'] = !empty($config['version']) ? $config['version'] : '1.0.0';
        $config['is_verify'] = 1;
        $config['is_api'] = $config['is_api'] ?? 0;
        $config['admin_id'] = (new Auth())->user('id', ['withJoin' => ['AdminGroup', 'Department']]);
        $config['describe'] = $config['describe'] ?? '';
        try {
            $appModel = model('Application');
            $app_id = $appModel->createData($config);
            $models = model('Model')->where('addon', '=', $this->appName)->column('id');
            foreach ($models as $model_id) {
                (new CreateFile)->createModel($model_id);
            }
            if (!$app_id) {
                throw new \Exception(array_values($appModel->getError())[0] ?? 'application表写入失败');
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
        $appinfo  = get_app($this->appName);
        if (!$appinfo) {
            throw new \Exception('当前应用' . $this->appName . '不存在');
        }
        if (!is_dir($this->appPath)) {
            model('Application')->destroy($appinfo['id']);
            return false;
        }
        if (!empty($appinfo['is_disuninstall'])) {
            throw new \Exception('当前应用' . $this->appName . '已被禁止卸载');
        }

        // 卸载默认不会做太多事情 只会走application表删除记录而已  需要删除目录、数据等 自行在应用的uninstall方法定义
        try {
            $install = $this->nameSpace . "\\Install";
            if (class_exists($install)) {
                $class = new $install($this->appName);
                $config = $class->getConfig();
                $class->uninstall();
            }
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        try {
            $appinfo = get_app($this->appName);
            model('Application')->destroy($appinfo['id']);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return true;
    }

    protected function getInstallDirs()
    {
        return [
            'app',
            'woo',
            'public'
        ];
    }
}