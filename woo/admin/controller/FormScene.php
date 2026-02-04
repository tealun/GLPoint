<?php

declare(strict_types=1);

namespace woo\admin\controller;

use think\facade\Db;
use think\facade\Env;

class FormScene extends \app\common\controller\Admin
{
	public function index()
	{
        if (empty($this->args['parent_id'])) {
            return $this->redirect('Model/index');
        }
        $this->local['not_parent_return'] = true;
		return parent::{__FUNCTION__}();
	}

	public function create()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作', 'warn');
        }
        if (empty($this->args['parent_id'])) {
            return $this->message('缺少模型参数，请新返回模型列表', 'error',['返回模型' => (string) url('model/index')]);
        }
        $this->setFormValue('is_btn', 1);
        $this->setFormValue('app', ['admin']);
        $fieldsList = Db::name('Field')->where('model_id', '=', intval($this->args['parent_id'] ?? 0))->order(['list_order' => 'ASC','id' => 'ASC'])->field(['id', 'field', 'name'])->cache(60)->select();;
        $fields = [];
        foreach ($fieldsList as $item) {
            $fields[$item['field']] = $item['name'] . '[' . $item['field'] . ']';
        }
        $this->mdl->form['fields']['fields']['field']['options'] = $fields;
        return parent::{__FUNCTION__}();
    }

    public function modify()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作', 'warn');
        }
        $model_id = $this->mdl->where('id', '=', intval($this->args['id'] ?? 0))->value('model_id');
        if ($model_id) {
            $fieldsList = Db::name('Field')->where('model_id', '=', $model_id)->order(['list_order' => 'ASC','id' => 'ASC'])->field(['id', 'field', 'name'])->cache(60)->select();;
            $fields = [];
            foreach ($fieldsList as $item) {
                $fields[$item['field']] = $item['name'] . '[' . $item['field'] . ']';
            }
            $this->mdl->form['fields']['fields']['field']['options'] = $fields;
        }
        return parent::{__FUNCTION__}();
    }
    public function delete()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作', 'warn');
        }
        return parent::{__FUNCTION__}();
    }

    public function batchDelete()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作', 'warn');
        }
        return parent::{__FUNCTION__}();
    }

    public function updateSort()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作', 'warn');
        }
        return parent::{__FUNCTION__}();
    }

    public function resetSort()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作', 'warn');
        }
        return parent::{__FUNCTION__}();
    }
}
