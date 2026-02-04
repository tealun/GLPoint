<?php
namespace app\admin\controller;

use think\facade\Db;
use think\facade\Log;
use app\common\controller\AdminController;

/**
 * @Controller("数据库升级",module="系统",desc="数据库升级管理")
 */
class DatabaseUpgrade extends AdminController
{
    /**
     * 升级脚本列表
     */
    private $upgradeScripts = [
        'v1.1.0' => [
            'version' => 'v1.1.0',
            'title' => '订阅消息功能',
            'description' => '添加订阅消息授权表，扩展通知表支持订阅消息',
            'file' => 'v1.1.0_subscribe_message.sql',
            'date' => '2026-02-04'
        ],
        // 未来的升级脚本可以继续添加
    ];

    /**
     * @ApiInfo(value="升级列表页面",method="GET",login=true)
     */
    public function index()
    {
        $currentVersion = $this->getCurrentVersion();
        $upgradeList = [];

        foreach ($this->upgradeScripts as $key => $script) {
            $script['is_installed'] = version_compare($currentVersion, $script['version'], '>=');
            $script['can_upgrade'] = !$script['is_installed'];
            $upgradeList[] = $script;
        }

        $this->assign([
            'current_version' => $currentVersion,
            'upgrade_list' => $upgradeList
        ]);

        return $this->fetch();
    }

    /**
     * @ApiInfo(value="获取当前数据库版本",method="GET",login=true)
     */
    public function getCurrentVersion()
    {
        try {
            // 尝试从配置文件获取版本（优先）
            $versionFile = root_path() . 'data/.db_version';
            if (file_exists($versionFile)) {
                $version = trim(file_get_contents($versionFile));
                if (!empty($version)) {
                    return $version;
                }
            }

            // 尝试从数据库获取版本
            $tables = Db::query("SHOW TABLES LIKE 'woo_system_config'");
            
            if (!empty($tables)) {
                $version = Db::name('system_config')
                    ->where('key', 'db_version')
                    ->value('value');
                
                if ($version) {
                    // 同步到文件
                    file_put_contents($versionFile, $version);
                    return $version;
                }
            }

            return 'v1.0.0';
        } catch (\Exception $e) {
            Log::error('获取数据库版本失败: ' . $e->getMessage());
            return 'v1.0.0';
        }
    }

    /**
     * @ApiInfo(value="检查升级状态",method="GET",login=true)
     */
    public function checkStatus()
    {
        try {
            $currentVersion = $this->getCurrentVersion();
            $hasUpgrade = false;

            foreach ($this->upgradeScripts as $script) {
                if (version_compare($currentVersion, $script['version'], '<')) {
                    $hasUpgrade = true;
                    break;
                }
            }

            return $this->success('检查成功', [
                'current_version' => $currentVersion,
                'has_upgrade' => $hasUpgrade,
                'latest_version' => end($this->upgradeScripts)['version']
            ]);
        } catch (\Exception $e) {
            Log::error('检查升级状态失败: ' . $e->getMessage());
            return $this->error('检查失败');
        }
    }

    /**
     * @ApiInfo(value="执行数据库升级",method="POST",login=true)
     * @Param(name="version", type="string", require=true, desc="升级版本号")
     */
    public function upgrade()
    {
        $version = $this->request->post('version', '');

        if (empty($version)) {
            return $this->error('版本号不能为空');
        }

        if (!isset($this->upgradeScripts[$version])) {
            return $this->error('升级脚本不存在');
        }

        $script = $this->upgradeScripts[$version];
        $currentVersion = $this->getCurrentVersion();

        // 检查是否已升级
        if (version_compare($currentVersion, $version, '>=')) {
            return $this->error('该版本已安装，无需重复升级');
        }

        // 升级脚本文件路径
        $sqlFile = root_path() . 'data/upgrade/' . $script['file'];

        if (!file_exists($sqlFile)) {
            return $this->error('升级脚本文件不存在: ' . $script['file']);
        }

        try {
            // 开启事务
            Db::startTrans();

            // 读取SQL文件
            $sql = file_get_contents($sqlFile);

            // 执行前检查
            $this->preUpgradeCheck($version);

            // 分割SQL语句并执行
            $sqlStatements = $this->parseSqlFile($sql);
            
            foreach ($sqlStatements as $statement) {
                if (!empty(trim($statement))) {
                    try {
                        Db::execute($statement);
                        Log::info('执行SQL: ' . substr($statement ?? '', 0, 100));
                    } catch (\Exception $e) {
                        // 如果是已存在的列或表，记录警告但继续执行
                        if (strpos($e->getMessage(), 'Duplicate column') !== false ||
                            strpos($e->getMessage(), 'already exists') !== false) {
                            Log::warning('SQL已执行过，跳过: ' . $e->getMessage());
                            continue;
                        }
                        throw $e;
                    }
                }
            }

            // 执行后处理
            $this->postUpgradeProcess($version);

            // 提交事务
            Db::commit();

            Log::info('数据库升级成功', [
                'version' => $version,
                'title' => $script['title']
            ]);

            return $this->success('升级成功');

        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            
            Log::error('数据库升级失败: ' . $e->getMessage(), [
                'version' => $version,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->error('升级失败: ' . $e->getMessage());
        }
    }

    /**
     * @ApiInfo(value="批量升级到最新版本",method="POST",login=true)
     */
    public function upgradeAll()
    {
        $currentVersion = $this->getCurrentVersion();
        $upgraded = [];
        $failed = [];

        foreach ($this->upgradeScripts as $version => $script) {
            // 跳过已安装的版本
            if (version_compare($currentVersion, $version, '>=')) {
                continue;
            }

            // 执行升级
            $sqlFile = root_path() . 'data/upgrade/' . $script['file'];

            if (!file_exists($sqlFile)) {
                $failed[] = [
                    'version' => $version,
                    'error' => '升级脚本文件不存在'
                ];
                continue;
            }

            try {
                Db::startTrans();

                $sql = file_get_contents($sqlFile);
                $this->preUpgradeCheck($version);
                
                $sqlStatements = $this->parseSqlFile($sql);
                foreach ($sqlStatements as $statement) {
                    if (!empty(trim($statement))) {
                        try {
                            Db::execute($statement);
                        } catch (\Exception $e) {
                            if (strpos($e->getMessage(), 'Duplicate column') !== false ||
                                strpos($e->getMessage(), 'already exists') !== false) {
                                continue;
                            }
                            throw $e;
                        }
                    }
                }

                $this->postUpgradeProcess($version);
                Db::commit();

                $upgraded[] = $version;
                $currentVersion = $version; // 更新当前版本

            } catch (\Exception $e) {
                Db::rollback();
                $failed[] = [
                    'version' => $version,
                    'error' => $e->getMessage()
                ];
            }
        }

        if (!empty($failed)) {
            return $this->error('部分升级失败', [
                'upgraded' => $upgraded,
                'failed' => $failed
            ]);
        }

        return $this->success('全部升级成功', [
            'upgraded' => $upgraded,
            'current_version' => $currentVersion
        ]);
    }

    /**
     * 解析SQL文件
     */
    private function parseSqlFile($sql)
    {
        // 移除注释
        $sql = preg_replace('/^\s*--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        // 分割SQL语句（以分号结尾）
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) {
                return !empty($stmt);
            }
        );

        return $statements;
    }

    /**
     * 升级前检查
     */
    private function preUpgradeCheck($version)
    {
        // 可以在这里添加升级前的检查逻辑
        // 例如：检查必要的表是否存在，检查数据完整性等

        if ($version === 'v1.1.0') {
            // 检查 woo_notification 表是否存在
            $tables = Db::query("SHOW TABLES LIKE 'woo_notification'");
            if (empty($tables)) {
                throw new \Exception('woo_notification 表不存在，请先执行基础数据库脚本');
            }

            // 检查 woo_user 表是否存在
            $tables = Db::query("SHOW TABLES LIKE 'woo_user'");
            if (empty($tables)) {
                throw new \Exception('woo_user 表不存在，请先执行基础数据库脚本');
            }
        }
    }

    /**
     * 升级后处理
     */
    private function postUpgradeProcess($version)
    {
        // 保存版本号到文件
        $versionFile = root_path() . 'data/.db_version';
        file_put_contents($versionFile, $version);

        // 尝试保存到数据库（如果表存在）
        try {
            $tables = Db::query("SHOW TABLES LIKE 'woo_system_config'");
            if (!empty($tables)) {
                Db::name('system_config')->where('key', 'db_version')->delete();
                Db::name('system_config')->insert([
                    'key' => 'db_version',
                    'value' => $version,
                    'description' => '数据库版本号',
                    'create_time' => time(),
                    'update_time' => time()
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('保存版本号到数据库失败（继续）: ' . $e->getMessage());
        }

        // 可以在这里添加升级后的处理逻辑
        // 例如：数据迁移，缓存清理等

        if ($version === 'v1.1.0') {
            // 清除相关缓存
            cache('notification_types', null);
            cache('subscribe_templates', null);
        }
    }

    /**
     * @ApiInfo(value="备份数据库",method="POST",login=true)
     */
    public function backup()
    {
        try {
            $config = config('database.connections.mysql');
            $database = $config['database'];
            $backupDir = root_path() . 'data/backup/';
            
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $filename = 'backup_' . date('YmdHis') . '.sql';
            $filepath = $backupDir . $filename;

            // 使用 mysqldump 命令备份
            $command = sprintf(
                'mysqldump -h%s -u%s -p%s %s > %s',
                $config['hostname'],
                $config['username'],
                $config['password'],
                $database,
                $filepath
            );

            exec($command, $output, $returnVar);

            if ($returnVar === 0 && file_exists($filepath)) {
                Log::info('数据库备份成功', ['file' => $filename]);
                return $this->success('备份成功', ['file' => $filename]);
            } else {
                throw new \Exception('备份命令执行失败');
            }

        } catch (\Exception $e) {
            Log::error('数据库备份失败: ' . $e->getMessage());
            return $this->error('备份失败: ' . $e->getMessage());
        }
    }
}
