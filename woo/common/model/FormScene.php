<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\builder\form\FormConfig;
use think\facade\Env;
use woo\common\helper\CreateFile;

class FormScene extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();

        $config = FormConfig::get('form_item_lists');
        $options['auto'] = '自动获取字段设置类型[auto]';
        foreach ($config as $elem => $info) {
            $options[$elem] = ($info['name'] ?? $elem) . '[' . $elem .']';
        }

        $this->form['fields']['fields'] = [
            'field' => [
                'label' => '字段',
                'elem' => 'text',
                'width' => 150
            ],
            'elem' => [
                'label' => '类型',
                'elem' => 'select',
                'options' => $options,
                'default' => 'auto',
                'width' => 150
            ],
            'more_attrs' => [
                'label' => '更多属性',
                'elem' => 'keyvalue',
            ],
            'validate' => [
                'label' => '验证规则',
                'elem' => 'keyvalue',
            ]
        ];

        $options = ['admin' => '后台应用'];
        if (get_app('business')) {
            $options['business'] = '中台应用';
        }
        $this->form['app']['options'] = $options;
    }

    public function afterWriteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!Env::get('APP_DEBUG')) {
            return $parent_return;
        }
        $data = $this->getData();
        if (!empty($data['model_id']) && empty($this['is_not_create_file'])) {
            try {
                (new CreateFile)->createModel($data['model_id']);
            } catch (\Exception $e) {

            }
        }
        return $parent_return;
    }

    public function afterDeleteCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        if (!Env::get('APP_DEBUG')) {
            return $parent_return;
        }
        if (!empty($data['model_id']) && empty($this['is_not_create_file'])) {
            try {
                (new CreateFile)->createModel($data['model_id']);
            } catch (\Exception $e) {

            }
        }
        return $parent_return;
    }
}