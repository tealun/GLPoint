<?php
declare (strict_types=1);

namespace woo\common\model\traits;

use think\facade\Db;
use woo\common\facade\Auth;

trait Scope
{
    protected $isBusinessScope = false;

    public function scopeBusiness($query)
    {
        if (!empty(app('request')->business_id)) {
            $business_id = app('request')->business_id;
            if (isset($this->form['business_id'])) {
                $query->where('business_id', '=', $business_id);
            } elseif (isset($this->form['business_member_id'])) {
                $ids = Db::name('BusinessMember')
                    ->where('business_id', '=', $business_id)
                    ->column('id');
                $query->where('business_member_id', 'IN', $ids);
            } elseif ($this->name == 'Business') {
                $query->where('id', '=', $business_id);
            }
        }
    }
}