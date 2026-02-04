<?php
declare(strict_types=1);

namespace app\install\controller;

use think\App;
use think\facade\Db;
use think\facade\Session;

class Index
{
    use \woo\common\controller\traits\Stand;

    protected function initialize()
    {
        $this->params = $this->request->getParams();
        $this->args = $this->params['args'];
        $this->assign->root = $this->request->root();
        $this->assign->absroot = $this->request->root(true);
        $this->assign->approot = $this->request->appRoot();
        $this->assign->params = $this->params;
        $this->assign->setScriptData('wwwroot', $this->assign->root);
        $this->assign->setScriptData('absroot', $this->assign->absroot);
        $this->assign->setScriptData('approot', $this->assign->approot);
        $this->assign->addJs([
            'jquery-3.4.1.min',
            '/layui/layui',
        ], true);
        $this->assign->addCss([
            '/layui/css/layui',
            'install/global'
        ]);
        $this->assign->foot_link = [

        ];
    }

    protected function checkInstall()
    {
        if (is_woo_installed()) {
            return $this->redirect($this->assign->absroot);
        }
        return false;
    }

    public function index()
    {
        if ($result = $this->checkInstall()) {
            return $result;
        }
        $this->addTitle('软件安装向导-阅读系统使用协议');
        return $this->fetch();
    }

    public function step2()
    {
        if ($result = $this->checkInstall()) {
            return $result;
        }
        if (!Session::get('agree')) {
            return $this->redirect($this->assign->root . '?s=install/index');
        }
        $error_count = 0;
        $env['os'][1] = '操作系统';
        $env['os'][2] = '类UNIX';
        $env['os'][3] = true;
        $env['os'][4] = PHP_OS;
        $env['os'][5] = '不限制';

        $env['version'][1] = 'PHP版本';
        $env['version'][2] = '>7.4.0';
        $env['version'][3] = true;
        $env['version'][4] = PHP_VERSION;
        $env['version'][5] = '7.2.5';


        $env['pdo'][1] = 'PDO';
        $env['pdo'][2] = '开启';
        $env['pdo'][5] = '开启';
        $env['pdo']['link'] = 'https://www.baidu.com/s?wd=开启PDO,PDO_MYSQL扩展';
        if (class_exists('pdo')) {
            $env['pdo'][3] = true;
            $env['pdo'][4] = '已开启';
        } else {
            $env['pdo'][3] = false;
            $env['pdo'][4] = '未开启';
            $error_count++;
        }

        $env['pdo_mysql'][1] = 'PDO_MySQL';
        $env['pdo_mysql'][2] = '开启';
        $env['pdo_mysql'][5] = '开启';
        $env['pdo_mysql']['link'] = 'https://www.baidu.com/s?wd=开启PDO,PDO_MYSQL扩展';
        if (extension_loaded('pdo_mysql')) {
            $env['pdo_mysql'][3] = true;
            $env['pdo_mysql'][4] = '已开启';
        } else {
            $env['pdo_mysql'][3] = false;
            $env['pdo_mysql'][4] = '未开启';
            $error_count++;
        }

        $env['curl'][1] = 'CURL';
        $env['curl'][2] = '开启';
        $env['curl'][5] = '不限制';
        $env['curl']['link'] = 'https://www.baidu.com/s?wd=开启PHP CURL扩展';
        if (extension_loaded('curl')) {
            $env['curl'][3] = true;
            $env['curl'][4] = '已开启';
        } else {
            $env['curl'][3] = false;
            $env['curl'][4] = '未开启';
        }

        $env['fileinfo'][1] = 'Fileinfo';
        $env['fileinfo'][2] = '开启';
        $env['fileinfo'][5] = '不限制';
        $env['fileinfo']['link'] = 'https://www.baidu.com/s?wd=开启PHP fileinfo扩展';
        if (extension_loaded('fileinfo')) {
            $env['fileinfo'][3] = true;
            $env['fileinfo'][4] = '已开启';
        } else {
            $env['fileinfo'][3] = false;
            $env['fileinfo'][4] = '未开启';
        }

        $env['gd'][1] = 'GD';
        $env['gd'][2] = '开启';
        $env['gd'][5] = '开启';
        $env['gd']['link'] = 'https://www.baidu.com/s?wd=开启PHP GD扩展';
        if (extension_loaded('gd')) {
            $env['gd'][3] = true;
            $env['gd'][4] = '已开启';
        } else {
            $env['gd'][3] = false;
            $env['gd'][4] = '未开启';
            $error_count++;
        }

        $env['mbstring'][1] = 'MBstring';
        $env['mbstring'][2] = '开启';
        $env['mbstring'][5] = '开启';
        $env['mbstring']['link'] = 'https://www.baidu.com/s?wd=开启PHP MBstring扩展';
        if (extension_loaded('MBstring')) {
            $env['mbstring'][3] = true;
            $env['mbstring'][4] = '已开启';
        } else {
            $env['mbstring'][3] = false;
            $env['mbstring'][4] = '未开启';
            $error_count++;
        }

        $env['a']['is_title'] = true;
        $env['a']['title'] = '环境配置检测';

        $env['execution'][1] = '最大执行时间';
        $env['execution'][2] = '>=30s';
        $env['execution'][5] = '无限制';
        $env['execution'][3] = true;
        $env['execution'][4] = ini_get('max_execution_time') . 's';

        $env['filesize'][1] = '文件上传大小';
        $env['filesize'][2] = '>2M';
        $env['filesize'][5] = '无限制';
        $env['filesize'][3] = true;
        $env['filesize'][4] = ini_get('upload_max_filesize');

        $env['rewrite'][1] = 'URL重写';
        $env['rewrite'][2] = '开启';
        $env['rewrite'][5] = '开启';
        $env['rewrite'][3] = false;
        $env['rewrite'][4] = '检测中...';
        $env['rewrite']['id'] = 'rewrite';
        $env['rewrite']['link'] = 'https://www.kancloud.cn/manual/thinkphp5/177576';

        $folders    = [
            root_path() . 'data',
            root_path() . 'runtime',
            public_path() . 'uploads'
        ];

        $fresult = [];
        foreach ($folders as $dir) {
            if (is_dir($dir)) {
                if (test_write_dir($dir)) {
                    $fresult[$dir]['w'] = true;
                } else {
                    $fresult[$dir]['w'] = false;
                    $error_count++;
                }
                if (is_readable($dir)) {
                    $fresult[$dir]['r'] = true;
                } else {
                    $fresult[$dir]['r'] = false;
                    $error_count++;
                }
            } else {
                $fresult[$dir]['w'] = false;
                $fresult[$dir]['r'] = false;
                $error_count++;
            }
        }

        $this->assign->fresult = $fresult;
        $this->assign->env = $env;
        $this->assign->error_count = $error_count;
        $this->addTitle('安装向导-环境检测');
        return $this->fetch();
    }

    public function step3()
    {
        if ($result = $this->checkInstall()) {
            return $result;
        }
        $this->addTitle('安装向导-数据库和授权验证');
        return $this->fetch();
    }

    public function step4()
    {
        if ($result = $this->checkInstall()) {
            return $result;
        }
        if (!Session::has('install.sql')) {
            return  $this->redirect('install');
        }
        $this->addTitle('安装向导-数据安装');
        return $this->fetch();
    }


    public function step5()
    {
        if ($result = $this->checkInstall()) {
            return $result;
        }
        if (session("install.finish")) {
            @touch(root_path() . 'data' . DIRECTORY_SEPARATOR . 'install.lock');
            Session::clear();
            if (run_mode() === 'swoole') {
                app('swoole.server')->reload();
            }
            $this->addTitle('安装向导-安装完成');
            return $this->fetch();
        } else {
            return '非法安装';
        }
    }


    public function setAdminUser()
    {
        if ($result = $this->checkInstall()) {
            return $this->ajax('error', '失败');
        }
        $data = Session::get('install.admin_user');
        if (empty($data)) {
            return $this->ajax('error',"非法安装！");
        }
        if (get_db_config('type') == 'mysql') {
            // 优化数据
            try {
                $sql  = "ALTER TABLE `". get_db_config('prefix') ."model` AUTO_INCREMENT =1000;";
                Db::query($sql);
                $sql  = "ALTER TABLE `". get_db_config('prefix') ."field` AUTO_INCREMENT =10000;";
                Db::query($sql);
            } catch (\Exception $e) {
            }
        }

        $data['admin_group_id'] = 1;
        $data['status'] = 'verified';
        $data['department_id'] = 3;
        $adminModel = model('Admin');
        try{
            $result = $adminModel->createData($data);
            if ($result) {
                Session::set("install.finish", true);
                if (run_mode() === 'swoole') {
                    app('swoole.server')->reload();
                }
                return $this->ajax('success', "后台管理员创建完成！");
            } else {
                return $this->ajax('error', '管理员创建失败：' . (array_values($adminModel->getError())[0] ?? '未知错误'));
            }
        } catch (\Exception $e) {
            return $this->ajax('error', '管理员创建失败：' . $e->getMessage());
        }
    }

    public function install()
    {
        if ($result = $this->checkInstall()) {
            return $this->ajax(-1, '失败');
        }
        if (!Session::has('install.sql')) {
            return  $this->ajax(-1, '非法安装');
        }
        $sql = Session::get('install.sql');
        $index = isset($this->args['index']) ? intval($this->args['index']) : 0;
        if ($index == 0) {
            Session::set('install.error', 0);
        }
        if ($index >= count($sql)) {
            $installError = Session::get('install.error');
            return $this->ajax(2, "安装完成！", ['error' => $installError]);
        }
        $sqlExec = $sql[$index] . ';';
        $result = execute_sql($sqlExec);
        if (!empty($result['error'])) {
            $installError = Session::get('install.error');
            $installError = empty($installError) ? 0 : $installError;
            Session::set('install.error', $installError + 1);
            return $this->ajax(0, $result['message'], [
                'sql'       => $sqlExec,
                'exception' => $result['exception']
            ]);
        } else {
            return $this->ajax(1, $result['message'], '', [
                'sql' => $sqlExec
            ]);
        }
    }

    public function storeUser()
    {
        if ($result = $this->checkInstall() || !Session::has('is_checked_domain')) {
            return $this->ajax('error', '失败');
        }
        $post = $this->request->post('','','trim');
        if (empty($post['username'])) {
            return $this->ajax('error', '请填写管理员用户名');
        }
        if (strlen($post['password']) < 5 || strlen($post['password']) >16) {
            return $this->ajax('error', '管理员密码只能5-16位');
        }
        if ($post['password'] != $post['repassword']) {
            return $this->ajax('error', '两次密码输入不一致');
        }
        if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->ajax('error', '管理员邮箱格式错误');
        }
        unset($post['repassword']);

        $databaseFile = root_path() . 'data' . DIRECTORY_SEPARATOR . 'database.sql';
        if (!file_exists($databaseFile)) {
            return $this->ajax('error', "数据库文件{$databaseFile}不存在");
        }
        $dbConfig = Session::get('install_db_config');

        $sql = get_file_sql($databaseFile, $dbConfig['prefix'], $dbConfig['charset']);
        Session::set('install.sql', $sql);
        //$this->assign('sql_count', count($sql));
        Session::set('install.error', 0);
        Session::set('install.admin_user', $post);

        return $this->ajax('success', '');
    }

    public function checkDomain()
    {
        if ($result = $this->checkInstall()) {
            return $this->ajax('error', '失败');
        }
        $post = $this->request->post('','','trim,addslashes,htmlspecialchars');

        if (!$post['domain'] || !$post['code']) {
            return $this->ajax('error', '请填写完善授权信息');
        }
        $config_dir = root_path() . 'data' . DIRECTORY_SEPARATOR . 'config';
        if (!is_dir($config_dir)) {
            mkdir($config_dir, 0777, true);
        }
        file_put_contents($config_dir . DIRECTORY_SEPARATOR . 'woo.php', "<?php\nreturn " . var_export($post, true) . "\n?>");
        Session::set('is_checked_domain', true);
        if (run_mode() === 'swoole') {
            app('swoole.server')->reload();
        }
        return $this->ajax('success', "域名授权信息验证成功");
    }

    public function testQuery()
    {
        if ($result = $this->checkInstall() || !Session::has('install_db_config')) {
            return $this->ajax('error', '失败');
        }
        try {
            Db::query('SELECT VERSION();');
            return $this->ajax('success', "数据库连接测试成功！");
        } catch (\Exception $e) {
            return $this->ajax('error', "数据库连接失败：" . $e->getMessage());
        }
    }

    public function writeDbConfig()
    {
        if ($result = $this->checkInstall()) {
            return '';
        }
        if ($this->request->isPost() && $this->request->isAjax()) {
            $post         = $this->request->post('','','trim,addslashes,htmlspecialchars');
            $post['type'] = 'mysql';
            if (empty($post['hostname']) || empty($post['username']) || empty($post['hostport']) ||empty($post['charset']) || empty($post['database'])) {
                return $this->ajax('error', '请填写完善数据库配置信息');
            }
            if (in_array($post['database'], ['performance_schema', 'mysql', 'information_schema'])) {
                return $this->ajax('error', '数据库名有误');
            }
            try {
                $config_dir = root_path() . 'data' . DIRECTORY_SEPARATOR . 'config';
                if (!is_dir($config_dir)) {
                    mkdir($config_dir, 0777, true);
                }
                file_put_contents($config_dir . DIRECTORY_SEPARATOR . 'database.php', "<?php\nreturn " . var_export($post, true) . "\n?>");
                Session::set('install_db_config', $post);
                if (run_mode() === 'swoole') {
                    app('swoole.server')->reload();
                }
                return $this->ajax('success', '数据库配置文件生成成功！');

            } catch (\Exception $e) {
                return $this->ajax('error', '数据库配置文件写入失败：' . $e->getMessage());
            }
        }
    }

    public function testRewrite()
    {
        if ($result = $this->checkInstall()) {
            return '';
        }
        return $this->ajax('success', '支持');
    }

    public function agree()
    {
        if ($result = $this->checkInstall()) {
            return '';
        }
        Session::set('agree', 1);
        return $this->ajax('success', '成功');
    }
    public function disagree()
    {
        if (!$result = $this->checkInstall()) {
            return '';
        }
        try{
            rmdirs(root_path() . 'woo', true);
            rmdirs(root_path() . 'data', true);
            rmdirs(root_path() . 'app' . DIRECTORY_SEPARATOR . 'install', true);
            rmdirs(root_path() . 'public' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'woo', true);
        } catch (\Exception $e) {

        }
    }
}
