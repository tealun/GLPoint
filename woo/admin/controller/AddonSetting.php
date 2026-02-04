<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use app\common\controller\Admin;
use woo\common\facade\Cache;
use think\facade\Db;
use think\facade\Env;
use app\common\builder\FormPage;
use woo\common\annotation\Ps;

class AddonSetting extends  Admin
{
    /**
     * @Ps(name="配置")
     */
    public function set()
    {
        $parent_id = intval($this->args['parent_id'] ?? 0);
        $addon = model('Addon')->where('id', '=', $parent_id)->find();
        if (empty($addon)) {
            return $this->message('插件不存在', 'error');
        }
        $addon = $addon->toArray();
        try {
            $setting = model('AddonSetting')
                ->where('addon_id', '=', $parent_id)
                ->order(model('AddonSetting')->getDefaultOrder())
                ->select()
                ->toArray();
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
        if (empty($setting)) {
            return $this->message('插件【' . $addon['title'] . '】没有找到相关的配置', 'error', ['马上添加' => url('create', ['parent_id'=> $this->args['parent_id'] ?? null])]);
        }

        $changeData = [];
        if ($this->request->isPost()) {
            $post = $this->request->post();
        }
        $form = new FormPage();
        foreach ($setting as $item) {
            $form->addFormItem(
                $item['var'],
                $item['type'],
                [
                    'label' => $item['title'],
                    'options' => $item['options'] && is_json($item['options']) ? json_decode($item['options'], true) : [],
                    'tip' => $item['tip'] ?? ''
                ]
            );
            if (!empty($post)) {
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

        if ($this->request->isPost()) {
            if (!empty($changeData)) {
                try {
                    foreach ($changeData as $item) {
                        Db::name('AddonSetting')->save($item);
                    }
                    Cache::tag('AddonSetting')->clear();
                } catch (\Exception $e) {
                    return $this->message($e->getMessage(), 'error');
                }
                return $this->message('插件配置修改提交成功', 'success', ['back' => url('set', ['parent_id' => $parent_id])]);
            } else {
                return $this->message('数据未做任何改动，无需提交', 'warn', ['back' => url('set', ['parent_id' => $parent_id])]);
            }
        }

        $this->assign->form = $form;
        $this->assign->is_debug = $this->app->isDebug();
        $this->setHeaderInfo('title', '插件配置');
        $this->setHeaderInfo('ex_title', '所属插件：' . $addon['title']);
        $this->addAction('c','配置管理', (string)url('index', ['parent_id' => $parent_id]), 'btn-5', '', 10);
        return $this->fetch();
    }


}