<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use app\common\controller\Admin;
use think\facade\Db;
use think\facade\Env;
use woo\common\annotation\Forbid;
use woo\common\builder\form\FormConfig;
use woo\common\helper\Arr;
use woo\common\helper\CreateFile;
use woo\common\helper\Str;
use woo\common\annotation\Ps;

/**
 * Class Field
 * @package woo\admin\controller
 * @Forbid(nodebug=true)
 */
class Field extends Admin
{

    public function index()
    {
        if (empty($this->args['parent_id'])) {
            return $this->redirect('Model/index');
        }

        $children = [
            [
                'name' => 'contribute',
                'title' => '批量设置投稿',
                'sort' => 0,
                'js_func' => 'woo_batch_switch',
                'url' => (string)url('batchSwitch', ['field' => 'is_contribute','value' => 1]),
                'power' => 'modify',
                'check' => true
            ],
            [
                'name' => 'system',
                'title' => '批量系统字段',
                'sort' => 0,
                'js_func' => 'woo_batch_switch',
                'url' => (string)url('batchSwitch', ['field' => 'is_system','value' => 1]),
                'power' => 'modify',
                'check' => true
            ]
        ];
        if (get_app('business')) {
            array_unshift($children, [
                'name' => 'copy_from_admin',
                'title' => '批量复制到中台',
                'sort' => 0,
                'js_func' => 'woo_batch_switch',
                'url' => (string)url('copyFromAdmin', ['parent_id' => $this->args['parent_id']]),
                'power' => 'modify',
                'check' => true
            ]);
        } else {
            $this->mdl->form['business_form']['list'] = 0;
        }

        $this->mdl->tableTab['basic']['tool_bar'][] = [
            'name' => 'more',
            'title' => '批量操作',
            'sort' => 0,
            'icon' => 'layui-icon-ok-circle',
            'class' => 'btn-2',
            'power' => 'modify',
            'check' => true,
            'children' => $children
        ];

        $this->mdl->tableTab['basic']['tool_bar'][] = [
            'name' => 'more2',
            'title' => '批量取消',
            'sort' => 0,
            'icon' => 'layui-icon-reduce-circle',
            'class' => 'btn-7',
            'power' => 'modify',
            'check' => true,
            'children' => [
                [
                    'name' => 'contribute',
                    'title' => '批量取消投稿',
                    'sort' => 0,
                    'js_func' => 'woo_batch_switch',
                    'url' => (string)url('batchSwitch', ['field' => 'is_contribute','value' => 0]),
                    'power' => 'modify',
                    'check' => true
                ],
                [
                    'name' => 'system',
                    'title' => '取消系统字段',
                    'sort' => 0,
                    'js_func' => 'woo_batch_switch',
                    'url' => (string)url('batchSwitch', ['field' => 'is_system','value' => 0]),
                    'power' => 'modify',
                    'check' => true
                ]
            ]
        ];


        $this->local['limit'] = 1000;
        $this->local['not_parent_return'] = true;
        $this->local['forceCache'] = false;
        //$this->local['load_type'] = 'load-default';
        $parent_return = parent::index();
        if ($this->request->isAjax()) {
            try {
                $db_list = get_table_columns(intval($this->args['parent_id']), [], false);
            } catch (\Exception $e) {
                return $this->message($e->getMessage(), 'error');
            }

            $list = Arr::combine($this->local['tableData']['data'], 'field');
            $data = [];
            foreach ($db_list as $item) {
                $f = $item['Field'];
                if (array_key_exists($f, $list)) {
                    continue;
                } else {
                    $model = new \woo\common\model\Field();
                    try {
                        $fieldItem = [
                            'is_exists' => 1,
                            'is_field' => 1,
                            'model_id' => intval($this->args['parent_id'])
                        ];
                        $fieldItem = array_merge($fieldItem, \woo\common\helper\Model::getItemFromDb($item));
                        $result = $model->createData($fieldItem);
                        if (!$result) {
                            return $this->message($fieldItem['field'] . (array_values($model->getError())[0] ?? $f . '添加失败'), 'error');
                        }
                    } catch (\Exception $e) {
                        return $this->message($e->getMessage(), 'error');
                    }
                    $data[] = $model->toArray();
                }
            }
            if ($data) {
                foreach ($data as $item) {
                    array_push($this->local['tableData']['data'], $item);
                }
            }
            return $this->local['tableData'];
        }
        return $parent_return;
    }

    public function create()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作字段', 'warn');
        }

        if (isset($this->args['parent_id'])) {
            $model = model('Model')->find(intval($this->args['parent_id']));
            if (empty($model)) {
                return $this->redirect('model/index');
            }
            try {
                $list = get_table_columns(0, $model->toArray());
            } catch(\Exception $e) {
                return $this->message($e->getMessage(), 'error');
            }
            $this->mdl->form['after']['elem'] = 'xmselect';
            $this->mdl->form['after']['attrs']['data-max'] = 1;
            $this->mdl->form['after']['options'] = $list;
        }

        $this->setFormValue('is_not_null', 1);
        if (get_app('business')) {
            $this->setFormValue('is_business_copy_admin', 1);
        }

        $this->setFormValue('default', 'none');
        $this->setFormValue('is_field', 1);
        $this->mdl->formTrigger['form']['callback'] = 'field_form_change';
        $config = FormConfig::get();
        $this->assign->setScriptData('form_item_lists', $config['form_item_lists']);
        //$this->addAction('quick','快速字段', '', '', '', 8, 'quick_create_item');
        return parent::{__FUNCTION__}();
    }

    public function modify()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作字段', 'warn');
        }
        $this->mdl->form['is_system']['elem'] = 0;
        $this->mdl->form['is_ai']['elem'] = 0;
        $this->mdl->form['is_field']['is_show'] = true;
        if (!empty($this->args['id'])) {
            $model_id = $this->mdl->where('id', '=', intval($this->args['id']))->value('model_id');
            try {
                $list = get_table_columns($model_id);
                if ($list) {
                    $this->mdl->form['after']['elem'] = 'xmselect';
                    $this->mdl->form['after']['attrs']['data-max'] = 1;
                    $this->mdl->form['after']['options'] = $list;
                }
            } catch(\Exception $e) {
                return $this->message($e->getMessage(), 'error');
            }
        }
        return parent::{__FUNCTION__}();
    }

    /**
     * @Ps(true,name="一键复制",as="modify")
     */
    public function copyFromAdmin()
    {
        $selected_ids = $this->request->post('selected_id', []);
        if (empty($selected_ids)) {
            return $this->message('没有找到需要被批量设置的数据', 'error');
        }

        $list = $this->mdl
            ->where($this->mdlPk, 'IN', $selected_ids)
            ->select();

        $count = 0;
        foreach ($list as $item) {
            $res = $item->isValidate(false)->modifyData([
                'business_form' => $item['form'],
                'business_modify_form' => $item['modify_form']  ?? '',
                'business_form_foreign' => $item['form_foreign'] ?? '',
                'business_form_options' => $item['form_options'] ?? '',
                'business_form_item_attrs' => $item['form_item_attrs'] ?? '',
                'business_form_tag_attrs' => $item['form_tag_attrs'] ?? '',
                'business_form_upload' => $item['form_upload'] ?? '',
                'business_form_trigger' => $item['form_trigger'] ?? '',
                'business_list' => $item['list'] ?? '',
                'business_list_attrs' => $item['list_attrs'] ?? '',
                'business_detail' => $item['detail'] ?? '',
                'business_detail_attrs' => $item['detail_attrs'] ?? '',
                'business_list_filter' => $item['list_filter'] ?? '',
                'business_list_filter_attrs' => $item['list_filter_attrs'] ?? '',
                'business_list_filter_tag_attrs' => $item['list_filter_tag_attrs'] ?? '',
                'business_validate' => $item['validate'] ?? ''
            ]);
            if ($res) {
                $count++;
            }
        }
        if ($count == count($selected_ids)) {
            $msg = "{$count}个字段的后台配置被成功复制到中台";
        } else {
            $msg = "{$count}个字段的后台配置被成功复制到中台，"  . (count($selected_ids) - $count) . "个字段设置失败";
        }
        return $this->message($msg, 'success');

        /*
        $parent_id = $this->args['parent_id'] ?? 0;
        $list = $this->mdl
            ->where('model_id', '=', (int) $parent_id)
            ->select()
            ->toArray();
        foreach ($list as $item) {
            Db::name('Field')
                ->where('id', '=', $item['id'])
                ->save([
                    'business_form' => $item['form'],
                    'business_modify_form' => $item['modify_form']  ?? '',
                    'business_form_foreign' => $item['form_foreign'] ?? '',
                    'business_form_options' => $item['form_options'] ?? '',
                    'business_form_item_attrs' => $item['form_item_attrs'] ?? '',
                    'business_form_tag_attrs' => $item['form_tag_attrs'] ?? '',
                    'business_form_upload' => $item['form_upload'] ?? '',
                    'business_form_trigger' => $item['form_trigger'] ?? '',
                    'business_list' => $item['list'] ?? '',
                    'business_list_attrs' => $item['list_attrs'] ?? '',
                    'business_detail' => $item['detail'] ?? '',
                    'business_detail_attrs' => $item['detail_attrs'] ?? '',
                    'business_list_filter' => $item['list_filter'] ?? '',
                    'business_list_filter_attrs' => $item['list_filter_attrs'] ?? '',
                    'business_list_filter_tag_attrs' => $item['list_filter_tag_attrs'] ?? '',
                    'business_validate' => $item['validate'] ?? '',
                    'update_time' => time()
                ]);
        }
        return $this->message('成功将字段的后台配置信息一键复制到中台配置');
        */
    }

    protected function setFormGrid()
    {
        $this->formPage->setTab('basic', '基本信息');
        $this->formPage->setTab('admin', '后台配置');
        if (get_app('business')) {
            $this->formPage->setTab('business', '中台配置');
        }

        $this->formPage->switchTab('basic')->setGrid('a', '基本信息', 6, [
            'field',
            'name',
            'list_order',
            'model_id',
            [
                'is_system',
                'is_contribute',
            ]
        ])->setGrid('b', '数据表结构', 6, [
            'is_field',
            'type',
            'length',
            'default',
            'is_not_null',
            'is_unsigned',
            'is_ai',
            'index',
            'after'
        ]);

        $this->formPage->switchTab('admin')->setGrid('admin_a', '表单信息', 6, [
            'form',
            'modify_form',
            'form_foreign',
            'form_options',
            'form_item_attrs',
            'form_tag_attrs',
            'form_upload',
            'form_trigger'
        ])->setGrid('admin_b', '列表、详情配置', 6, [
            '列表信息' => [
                'list',
                'list_attrs'
            ],
            '详情信息' => [
                'detail',
                'detail_attrs'
            ],
            '列表搜索信息' => [
                'list_filter',
                'list_filter_attrs',
                'list_filter_tag_attrs'
            ]
        ])->setGrid('admin_c', '数据验证', 12, [
            'validate' => [
                'is_not_label' => true
            ]
        ]);


        if (get_app('business')) {
            $this->formPage->switchTab('business')->setGrid('business_is', '', 12, [
                'is_business_copy_admin'
            ]);
            $this->formPage->switchTab('business')->setGrid('business_a', '表单信息', 6, [
                'business_form',
                'business_modify_form',
                'business_form_foreign',
                'business_form_options',
                'business_form_item_attrs',
                'business_form_tag_attrs',
                'business_form_upload',
                'business_form_trigger'
            ])->setGrid('business_b', '列表、详情配置', 6, [
                '列表信息' => [
                    'business_list',
                    'business_list_attrs'
                ],
                '详情信息' => [
                    'business_detail',
                    'business_detail_attrs'
                ],
                '列表搜索信息' => [
                    'business_list_filter',
                    'business_list_filter_attrs',
                    'business_list_filter_tag_attrs'
                ]
            ])->setGrid('business_c', '数据验证', 12, [
                'business_validate' => [
                    'is_not_label' => true
                ]
            ]);
        } else {
            $this->formPage->removeFormItem('is_business_copy_admin');
            $this->formPage->removeFormItem('business_form');
            $this->formPage->removeFormItem('business_modify_form');
            $this->formPage->removeFormItem('business_form_foreign');
            $this->formPage->removeFormItem('business_form_options');
            $this->formPage->removeFormItem('business_form_item_attrs');
            $this->formPage->removeFormItem('business_form_tag_attrs');
            $this->formPage->removeFormItem('business_form_upload');
            $this->formPage->removeFormItem('business_form_trigger');
            $this->formPage->removeFormItem('business_list');
            $this->formPage->removeFormItem('business_list_attrs');
            $this->formPage->removeFormItem('business_detail');
            $this->formPage->removeFormItem('business_detail_attrs');
            $this->formPage->removeFormItem('business_list_filter');
            $this->formPage->removeFormItem('business_list_filter_attrs');
            $this->formPage->removeFormItem('business_list_filter_tag_attrs');
            $this->formPage->removeFormItem('business_validate');
        }

    }

    public function delete()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作字段', 'warn');
        }
        $this->local['where'][] = ['is_system', '=', 0]; // 强行限制 “系统字段” 打勾的不能删除
        return parent::{__FUNCTION__}();
    }

    /**
     * @Ps(as="delete")
     */
    public function batchDelete()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作字段', 'warn');
        }
        $this->local['where'][] = ['is_system', '=', 0];
        return parent::{__FUNCTION__}();
    }

    public function updateSort()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作字段', 'warn');
        }
        $parent_return =  parent::updateSort();
        if (isset($this->args['parent_id'])) {
            (new CreateFile)->createModel(intval($this->args['parent_id']));
        }
        return $parent_return;
    }

    public function resetSort()
    {
        if (!Env::get('APP_DEBUG')) {
            return $this->message('非开发调试模式下，禁止操作字段', 'warn');
        }
        $parent_return =  parent::resetSort();
        if (isset($this->args['parent_id'])) {
            (new CreateFile)->createModel(intval($this->args['parent_id']));
        }
        return $parent_return;
    }
}