<?php
declare (strict_types=1);

namespace woo\admin\controller;

use app\common\controller\Admin;
use think\facade\Cache;
use think\facade\Db;
use woo\common\annotation\Ps;
use woo\common\annotation\Forbid;
use app\common\builder\Table;
use woo\common\helper\Arr;
use woo\common\helper\Backup;
use woo\common\model\Forge;

/**
 * Class Demo
 * @package woo\admin\controller
 * @Ps(name="数据库")
 * @Forbid(except={"create","modify","batchDelete","sort","detail"})
 */
class Database extends  Admin
{
    /**
     * @Ps(name="数据管理")
     */
    public function index()
    {
        // 数据表结构
        $data = Db::query("SHOW TABLE STATUS LIKE '" . get_db_config('prefix') ."%'");
        Cache::set('woo_table_list', array_values(Arr::combine($data, 'Name', 'Name')), 60);

        $basic = new Forge();
        $basic->setTableFields([
            'Name' => [
                'name' => '表名',
                'list' => [
                    'width' => 180,
                    'templet' => 'show',
                ],
            ],
            'Rows' => [
                'name' => '记录条数',
                'list' => [
                    'templet' => 'show',
                    'sort' => true
                ],
            ],
            'Data_length' => [
                'name' => '数据大小',
                'list' => [
                    'templet' => 'filesize',
                    'sort' => true
                ],
            ],
            'Index_length' => [
                'name' => '索引大小',
                'list' => [
                    'templet' => 'filesize',
                    'sort' => true
                ],
            ],
            'Engine' => [
                'name' => '引擎',
                'list' => 'show'
            ],
            'Collation' => [
                'name' => '字符集',
                'list' => 'show'
            ],
            'Comment' => [
                'name' => '表注释',
                'list' => 'show'
            ],
            'Create_time' => [
                'name' => '创建时间',
                'list' => [
                    'templet' => 'date',
                    'sort' => true,
                    'style' => 'color:#888;'
                ],
            ],
        ])->setPk('Name')->setDisplay('Name');



        // 备份文件数据
        $file = new Forge();
        $file->setTableFields([
            'name' => [
                'name' => '文件名',
                'list' => 'show'
            ],
            'size' => [
                'name' => '大小',
                'list' => 'filesize'
            ],
            'compress' => [
                'name' => '压缩',
                'list' => 'show'
            ],
            'time' => [
                'name' => '日期',
                'list' => [
                    'templet' => 'datetime',
                    'width' => 152
                ]
            ]
        ])->setPk('time')->setDisplay('name');
        $db = new Backup();
        $filelist = array_reverse(array_values($db->fileList()));

        $tableTab = [
            'basic' => [
                'title' => '数据结构',
                'table' => [
                    'data' => $data,
                    'limit' => 50,
                    'page' => count($data) <= 50 ? false : true
                ],
                'tool_bar' => [
                    [
                        'name' => 'backup',
                        'title' => '备份',
                        'sort' => 10,
                        'js_func' => 'woo_tool',
                        'icon' => 'woo-icon-database',
                        'class' => 'btn-2',
                        'url' => (string) url('backup'),
                        'power' => 'backup',
                        'check' => true
                    ]
                ],
                'item_tool_bar' => [
                    [
                        'name' => 'optimize',
                        'title' => '',
                        'sort' => 10,
                        'js_func' => '',
                        'icon' => 'woo-icon-youhua',
                        'hover' => '优化',
                        'class' => 'btn-22',
                        'url' => (string) url('optimize',['table' => "{{d.Name}}"]),
                        'power' => 'optimize'
                    ],
                    [
                        'name' => 'repair',
                        'title' => '',
                        'sort' => 20,
                        'js_func' => '',
                        'icon' => 'woo-icon-xiufu',
                        'hover' => '修复',
                        'class' => 'btn-27',
                        'url' => (string) url('repair',['table' => "{{d.Name}}"]),
                        'power' => 'repair'
                    ]
                ]
            ],
            'file' => [
                'model' => $file,
                'title' => '备份文件',
                'table' => [
                    'data' => $filelist,
                    'limit' => 10,
                    'page' => count($filelist) <= 10 ? false : true
                ],
                'item_tool_bar' => [
                    [
                        'name' => 'download',
                        'title' => '',
                        'sort' => 10,
                        'js_func' => '',
                        'icon' => 'layui-icon-download-circle',
                        'hover' => '下载',
                        'class' => 'btn-23',
                        'url' => (string) url('download',['time' => "{{d.time}}"]),
                        'power' => 'download'
                    ],
                    [
                        'name' => 'delete',
                        'title' => '',
                        'sort' => 20,
                        'js_func' => 'woo_item_tool',
                        'icon' => 'layui-icon-delete',
                        'hover' => '删除',
                        'class' => 'btn-25',
                        'url' => (string) url('delete',['time' => "{{d.time}}"]),
                        'power' => 'delete'
                    ]
                ],
                'checkbox' => false
            ]
        ];
        $table = new Table($basic, $tableTab);
        $this->assign->table = $table;
        $this->local['header_title'] = '数据管理';
        return $this->fetch('list');
    }

    /**
     * @Ps(name="删除备份")
     */
    public function delete()
    {
        $time = trim($this->args['time']);
        if (!$time) {
            return $this->message('error', '请选择需要删除的文件', ['返回列表' => ['index']]);
        }
        $db = new Backup();
        if ($db->delFile($time)) {
            return $this->message('success', '数据库备份文件删除成功', ['返回列表' => ['index']]);
        }
    }

    /**
     * @Ps(name="备份下载")
     */
    public function download()
    {
        $time = trim($this->args['time']);
        if (!$time) {
            return $this->message('error', '请选择需要下载的文件');
        }
        if (!extension_loaded('fileinfo')) {
            return $this->message('error', '当前服务器没有"fileinfo"扩展，下载失败');
        }
        try {
            $db = new Backup();
            $files = $db->getFile('time', $time);
            $zip = zip_files($files);
            return download($zip, $time . '.zip');
        } catch (\Exception $e) {
            return $this->message('error', $e->getMessage());
        }

    }

    /**
     * @Ps(name="备份")
     * @Forbid(only={"ajax","post"})
     */
    public function backup()
    {
        set_time_limit(0);
        $db = new Backup();
        $selected_data = $this->request->post();
        $tables = $selected_data['selected_id'];
        if (!$tables) {
            return $this->message('error', '没有需要备份的数据表');
        }
        try {
            $fileinfo = $db->getFile();
            $db->backupInit();
            foreach ($tables as $table) {
                $db->setFile($fileinfo['file'])->backup($table, 0);
            }
            return $this->message('success', $fileinfo['file']['name'] . '数据库备份成功');
        }  catch (\Exception $e) {
            return $this->message('error', $e->getMessage());
        }

    }

    /**
     * @Ps(name="修复表")
     */
    public function repair()
    {
        $table = trim(addslashes($this->args['table']));
        if (!$table) {
            return $this->message('error', '请选择需要修复的表');
        }
        if (!Cache::has('woo_table_list')) {
            return $this->message('warm', '数据缓存不存在，请返回列表重新操作', ['返回列表' => url('index')]);
        }
        if (!in_array($table, Cache::get('woo_table_list'))) {
            return $this->message('warm', '没有找到该数据表，可以尝试清空缓存再试', ['返回列表' => url('index')]);
        }
        try {
            Db::query("REPAIR TABLE `{$table}`");
        } catch (\Exception $e) {
            return $this->message('error', $e->getMessage());
        }
        return $this->message('success', "数据表：{$table}修复成功");
    }
    /**
     * @Ps(name="优化表")
     */
    public function optimize()
    {
        $table = trim(addslashes($this->args['table']));
        if (!$table) {
            return $this->message('error', '请选择需要优化的表');
        }
        if (!Cache::has('woo_table_list')) {
            return $this->message('warm', '数据缓存不存在，请返回列表重新操作', ['返回列表' => url('index')]);
        }
        if (!in_array($table, Cache::get('woo_table_list'))) {
            return $this->message('warm', '没有找到该数据表，可以尝试清空缓存再试', ['返回列表' => url('index')]);
        }
        try {
            Db::query("OPTIMIZE TABLE `{$table}`");
        } catch (\Exception $e) {
            return $this->message('error', $e->getMessage());
        }

        return $this->message('success', "数据表：{$table}优化成功");
    }


}