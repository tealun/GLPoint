<?php
declare(strict_types=1);

namespace woo\admin\controller;

use think\facade\Cache;
use think\facade\Db;
use woo\common\annotation\Forbid;
use woo\common\annotation\Ps;
use app\common\builder\Table;
use woo\common\helper\Excel;
use woo\common\helper\Str;
use woo\common\model\Forge;

class Import extends \app\common\controller\Admin
{
    public function index()
    {
        $this->mdl->tableTab['basic']['item_tool_bar'] = [
            [
                'name' => 'preview',
                'title' => '',
                'sort' => 100,
                'icon' => 'woo-icon-liebiao-o',
                'hover' => '执行导入',
                'class' => 'btn-26',
                'url' => (string) url('preview', ['id' => '{{d.id}}']),
                'where' => "{{d.is_import==0}}",
                'power' => 'preview'
            ]
        ];
        $this->local['where'][] = ['business_id', '=', 0];
        return parent::{__FUNCTION__}();
    }

    /**
     * @Ps(name="导入")
     */
    public function preview()
    {
        $id = intval($this->args['id']) ?? 0;
        $import = $this->mdl->find($id);
        if (empty($import)) {
            return $this->message("ID为[{$id}]的数据不存在", 'error');
        }

        try {
            $excel = new Excel();
            $data = $excel->readExcel($import->file);
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
        if (empty($data['data'])) {
            return $this->message('文件中没有获取到数据', 'error');
        }

        $header = [];
        foreach ($data['map'] as $field => $title) {
            $header[$field] = [
                'name' => $title,
                'list' => [
                    'edit' => 'text'
                ]
            ];
        }
        $basic = new Forge();
        $basic->setTableFields($header);
        $tableTab = [
            'basic' => [
                'title' => '数据结构',
                'table' => [
                    'data' => $data['data']
                ],
                'tool_bar' => [
                    [
                        'name' => 'preview',
                        'title' => '执行导入',
                        'sort' => 0,
                        'class' => 'btn-8',
                        'js_func' => 'woo_execute_import',
                        'icon' => 'layui-icon-upload',
                        'url' => (string) url('executeImport')
                    ]
                ]
                //'is_remove_pk' => true
            ]
        ];
        $table = new Table($basic, $tableTab);
        $this->assign->table = $table;
        $this->addAction('return', '返回列表', (string)url('index'), 'btn-2');
        $this->local['header_title'] = '数据预览';
        $this->local['header_tip'] = '可以自行切换翻页条数，控制每次导入数量（一次性导入不建议超过200条数据）';
        return $this->fetch('list');
    }

    /**
     * @Ps(as="preview")
     * @Forbid(only={"ajax","post"})
     */
    public function executeImport()
    {
        $post = $this->request->post();
        $data = $post['data'];
        $start = count($data);
        if ($start <= 0) {
            return $this->message("没有找到要导入的数据", 'error');
        }
        $id = intval($post['id']) ?? 0;
        $import = $this->mdl->find($id);
        if (empty($import)) {
            return $this->message("ID为[{$id}]的数据不存在", 'error');
        }
        $modelData = model('Model')->where('id', '=', $import['model_id'])->find();
        if (empty($modelData)) {
            return $this->message("模型管理中没有找到ID为{$import['model_id']}的模型", 'error');
        }
        if (empty($modelData['is_import'])) {
            return $this->message("模型管理中ID为{$import['model_id']}的模型不支持使用导入功能", 'error');
        }

        $import = $import->toArray();
        $type = $import['type'] ?: 'db';
        $modelName = (!empty($modelData['addon']) ? $modelData['addon'] . '.' : '') . $modelData['model'];
        $model = get_model_name($modelName);
        if (empty($model)) {
            return $this->message("模型检测失败", 'error');
        }
        $model = model($model);
        try
        {
            foreach ($data as $key => &$item)
            {
                // 如果需要自行给导入数据值处理 或者增减字段 对应模型中自行定义setImportItemAttr方法 会将当条数据传入 如果返回空 会将该条数据去除
                $callback = "setImportItemAttr";
                if (method_exists($model, $callback)) {
                    $item = $model->$callback($item);
                }
                if (empty($item) || !is_array($item)) {
                    unset($data[$key]);
                    continue;
                }

                if ($type == 'db') {
                    // 如果是db操作 维护时间戳
                    if (isset($model->form['create_time'])) {
                        $item['create_time'] = time();
                    }
                    if (isset($model->form['update_time'])) {
                        $item['update_time'] = time();
                    }
                    if (isset($model->form['delete_time'])) {
                        $item['delete_time'] = 0;
                    }
                }
            }
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
        $execute = "executeBy" . Str::studly($type);

        try {
            $error = [];
            $result = $this->$execute($model, $data, $error);
            $msg = sprintf("本次共需导入%d条数，成功%d条，失败%d条", $start, $result, ($start - $result));
            if ($type == 'db') {
                Cache::tag($modelName)->clear();
            }
            return $this->ajax($result ? 'success' : 'error', $msg . ($error ? '：<br>' . implode('|', $error) : ''));
        } catch (\Exception $e) {
            return $this->ajax('error',  "导入失败：" . $e->getMessage());
        }
    }

    protected function executeByDb($model, $data, &$error = [])
    {
        return Db::table($model->getTable())->limit(100)->insertAll($data);
    }

    protected function executeByModel($model, $data, &$error = [])
    {
        $result = $model->saveAll($data);
        $pk = $model->getPk();
        $count = 0;
        foreach ($result as $mdl) {
            if (!is_object($mdl)) {
                continue;
            }
            if (isset($mdl[$pk]) && !$mdl->getError()) {
                $count++;
                continue;
            }
            if ($error) {
                continue;
            }
            $error = $mdl->getError();
        }
        return $count;
    }
}
