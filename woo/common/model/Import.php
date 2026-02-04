<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\helper\Arr;

class Import extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
        if (!get_app('business') && isset($this->relationLink['Business'])) {
            unset($this->relationLink['Business']);
        }
        $this->form['file']['upload'] = array_merge($this->form['file']['upload'] ?? [], ['type' => 'local']);
        $this->form['model_id']['elem'] = 'select';
        $cache = [];
        if (app('http')->getName() == 'admin') {
            $cache = model_cache('', [['is_import', '=', 1]]);
        } elseif (app('http')->getName() == 'business') {
            $cache = model_cache('', [['is_business_import', '=', 1]]);
        }

        if ($cache) {
            foreach ($cache as $item) {
                $this->form['model_id']['options'][$item['id']] = $item['cname'] . '【' . ($item['addon'] ? $item['addon'] . '.' : '') . $item['model'] . '】';
            }
        }
    }

}