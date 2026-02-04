<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use think\facade\Db;
use woo\common\Auth;
use woo\common\helper\Str;

class ApiDevelopment extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
        $this->form['model_id']['foreign_tab']['list_fields'] = ['id', 'model','addon','cname'];
    }

    public function beforeInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $application = Db::name('Application')->where('id', '=', $this['application_id'] ?? 0)->value('name');
        $model = Db::name('Model')->field(['addon', 'model'])->where('id', '=', $this['model_id'] ?? 0)->find();
        if ($application && $model) {
            $this['name'] = "app\\{$application}\\controller\\" . ($model['addon'] ? $model['addon'] . "\\" : "") . $model['model'];
        }
        return $parent_return;
    }
}