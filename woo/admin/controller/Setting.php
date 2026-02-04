<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use app\common\controller\Admin;
use woo\common\facade\Cache;
use think\facade\Db;
use think\facade\Env;
use app\common\builder\FormPage;
use woo\common\annotation\Ps;

class Setting extends  Admin
{
    /**
     * @Ps(name="配置")
     */
    public function set()
    {
        if (empty($this->local['group_var'])) {
            $this->local['where'] = [
                ['is_scene', '=', 0]
            ];
        } else {
            $this->local['where'] = [
                ['var', is_array($this->local['group_var']) ? 'IN': '=', $this->local['group_var']],
            ];
        }

        $this->local['header_title'] = $this->local['header_title'] ?? '系统设置';

        try {
            $setting = model('SettingGroup')
                ->with([
                    'Setting' => [
                        'order' => model('Setting')->getDefaultOrder()
                    ]
                ])
                ->where($this->local['where'] ?? [])
                ->order(model('SettingGroup')->getDefaultOrder())
                ->select()
                ->toArray();
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
        if (empty($setting)) {
            return $this->message('没有找到任何系统设置项', 'error', ['马上添加' => url('create')]);
        }
        $forms = [];
        $tabs = [];

        $changeData = [];
        if ($this->request->isPost()) {
            $post = $this->request->post();
        }

        foreach ($setting as $group) {
            if (empty($group['Setting'])) {
                continue;
            }
            $form = new FormPage();
            foreach ($group['Setting'] as $item) {
                $form->addFormItem(
                    $item['var'],
                    $item['type'],
                    [
                        'label' => $item['title'],
                        'options' => $item['options'] && is_json($item['options']) ? json_decode($item['options'], true) : [],
                        'tip' => $item['tip'] ?? '',
                        'is_js_var' => $item['is_js_var']
                    ]
                );

                if (isset($this->args['parent_id']) && $this->args['parent_id'] == $group['id'] && !empty($post)) {
                    if (isset($post[$item['var']]) && is_array($post[$item['var']])) {
                        $post[$item['var']] = json_encode($post[$item['var']], JSON_UNESCAPED_UNICODE);
                    }
                    if (isset($post[$item['var']]) && $post[$item['var']] != $item['value']) {
                        $changeData[] = [
                            'id' => $item['id'],
                            'value' => $post[$item['var']],
                            'update_time' => time()
                        ];
                    }
                }

                if (isset($item['value'])) {
                    $form->setItemValue($item['value']);
                }
            }
            //pr($form->getVisibleItems());
            array_push($forms, $form);
            array_push($tabs, $group);
        }

        if ($this->request->isPost()) {
            if (!empty($changeData)) {
                try {
                    foreach ($changeData as $item) {
                        Db::name('Setting')->save($item);
                    }
                    Cache::tag('Setting')->clear();
                } catch (\Exception $e) {
                    return $this->message($e->getMessage(), 'error');
                }
                return $this->message($this->local['header_title'] . '修改提交成功', 'success', ['back' => url('', ['parent_id' => $this->args['parent_id']])]);
            } else {
                return $this->message('数据未做任何改动，无需提交', 'warn', ['back' => url('', ['parent_id' => $this->args['parent_id']])]);
            }
        }
        $this->assign->forms = $forms;
        $this->assign->tabs = $tabs;
        $this->assign->is_debug = Env::get('APP_DEBUG');

        if ($this->assign->is_debug) {
            $this->addAction('c', '配置项', (string)url('index'), 'new_tab btn-5', '', 10);
            $this->addAction('d', '配置组', (string)url('SettingGroup/index'), 'new_tab btn-6', '', 9);
        }
        return $this->fetch('set');
    }

    public function index()
    {
        $this->local['item_tool_bar']['delete']['where'] = '{{d.setting_group}} > 6';
        return parent::{__FUNCTION__}();
    }
}