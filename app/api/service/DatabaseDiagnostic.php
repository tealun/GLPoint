<?php
declare(strict_types=1);

namespace app\api\service;

use think\facade\Db;
use PDO;

class DatabaseDiagnostic
{
    /**
     * 诊断数据库连接和权限问题
     */
    public static function diagnose(): array
    {
        try {
            // 1. 测试数据库连接
            $connection = Db::connect()->connect();
            
            // 2. 检查表是否存在和权限
            $tables = ['woo_user', 'woo_wechat_user'];
            $results = [];
            
            foreach($tables as $table) {
                // 检查表是否存在
                $exists = Db::query("SHOW TABLES LIKE '{$table}'");
                $results[$table] = [
                    'exists' => !empty($exists),
                    'writable' => false
                ];
                
                if(!empty($exists)) {
                    // 检查写入权限
                    try {
                        Db::execute("INSERT INTO {$table} () VALUES ()");
                    } catch(\Exception $e) {
                        if(strpos($e->getMessage(), 'denied') !== false) {
                            $results[$table]['writable'] = false;
                        }
                    }
                    // 删除测试数据
                    Db::execute("DELETE FROM {$table} ORDER BY id DESC LIMIT 1");
                    $results[$table]['writable'] = true;
                }
            }

            // 3. 检查数据库用户权限
            $grants = Db::query("SHOW GRANTS");
            
            return [
                'connection' => true,
                'tables' => $results, 
                'grants' => $grants,
                'error' => null
            ];

        } catch(\Exception $e) {
            return [
                'connection' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode()
            ];
        }
    }
}
