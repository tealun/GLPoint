<?php
declare (strict_types=1);

namespace woo\common\model\traits;

use think\Exception;
use think\facade\Cache;
use think\facade\Db;
use woo\common\facade\Auth;
use woo\common\helper\Arr;
use woo\common\helper\Str;

trait Event
{
    /**
     * 是否是强制删除  在删除事件中 可以判断当前是软删除 还是真实删除  true表示是真删除  false表示是软删除
     * @var bool
     */
    protected $isForceDelete = false;


    // 查询后
    public function afterReadCall()
    {}

    // 新增前
    public function beforeInsertCall()
    {}

    // 新增后
    public function afterInsertCall()
    {}

    // 更新前
    public function beforeUpdateCall()
    {}

    // 更新后
    public function afterUpdateCall()
    {}

    // 写入前
    public function beforeWriteCall()
    {}

    // 写入后
    public function afterWriteCall()
    {}

    // 删除前
    public function beforeDeleteCall()
    {}

    // 删除后
    public function afterDeleteCall()
    {}

    // 恢复前
    public function beforeRestoreCall()
    {}

    // 恢复后
    public function afterRestoreCall()
    {}

    // 模型事件静态方法改造为 普通方法定义 以下代码不动
    public static function onAfterRead($object)
    {
        if (method_exists($object, 'afterReadCall')) {
            return call_user_func([$object, 'afterReadCall']);
        }
    }
    public static function onBeforeInsert($object)
    {
        $object->isInsert = true;
        $callReturn = true;
        if (isset($object->form['list_order']) && empty($object['list_order'])) {
            $max = $object->max('list_order');
            $object['list_order'] = intval($max) + 1;
        }
        if (app('http')->getName() == 'business') {
            $auth = Auth::user();
            if (isset($object->form['business_id']) && empty($object['business_id'])) {
                $object['business_id'] = $auth['business_id'] ?? 0;
            }
            if (isset($object->form['business_member_id']) && empty($object['business_member_id'])) {
                $object['business_member_id'] = $auth['login_foreign_value'];
            }
        }
        if (method_exists($object, 'beforeInsertCall')) {
            $callReturn = call_user_func([$object, 'beforeInsertCall']);
        }
        $result = $object->setValidate('scene', 'add')->validate();
        return (false === $callReturn ? false : true) && $result;
    }
    public static function onAfterInsert($object)
    {
        if (method_exists($object, 'afterInsertCall')) {
            return call_user_func([$object, 'afterInsertCall']);
        }
    }

    public static function onBeforeUpdate($object)
    {
        $object->isUpdate = true;
        $callReturn = true;
        if (method_exists($object, 'beforeUpdateCall')) {
            $callReturn = call_user_func([$object, 'beforeUpdateCall']);
        }
        $result = $object->setValidate('scene', 'edit')->validate();
        return (false === $callReturn ? false : true) && $result;
    }
    public static function onAfterUpdate($object)
    {
        if (method_exists($object, 'afterUpdateCall')) {
            return call_user_func([$object, 'afterUpdateCall']);
        }
    }
    public static function onBeforeWrite($object)
    {
        if (method_exists($object, 'beforeWriteCall')) {
            return call_user_func([$object, 'beforeWriteCall']);
        }
    }
    public static function onAfterWrite($object)
    {
        if (isset($object->treeLevel) && $object->treeLevel > 0) {
            $object->afterWriteTree();
        }
        // 多对多的更新
        $pk = $object[$object->getPk()] ?? 0;
        if (!empty($object->belongsToManyForeign) && $pk) {
            foreach ($object->belongsToManyForeign as $field => $info) {
                if (!isset($object[$field])) {
                    continue;
                }
                $values = is_array($object[$field]) ? $object[$field] : (is_json($object[$field]) ? is_json($object[$field]) : explode(',', (string) $object[$field]));
                $values = array_diff($values, ['']);
                $delete = [];
                if ($object->isInsert) {
                    $insert = $values;
                } else {
                    $exists = model($info['middle'])->where($info['localKey'], $pk)->column($info['foreignKey']);
                    $insert = array_diff($values, $exists);
                    $delete = array_diff($exists, $values);
                }
                if ($insert) {
                    $object->{$info['key']}()->saveAll($insert);
                }
                if ($delete) {
                    $object->{$info['key']}()->detach($delete);
                }
            }
        }

        $object->clearModelTagCache();
        $object->setCounterCache();
        $object->setSumCache();
        if (method_exists($object, 'afterWriteCall')) {
            return call_user_func([$object, 'afterWriteCall']);
        }
    }
    public static function onBeforeDelete($object)
    {
        if ($object->deleteTime) {
            if (isset($object->delete_time) && $object->delete_time === 0) {
                $object->isForceDelete = false;
            } else {
                $object->isForceDelete = true;
            }
        } else {
            $object->isForceDelete = true;
        }
        if (method_exists($object, 'beforeDeleteCall')) {
            return call_user_func([$object, 'beforeDeleteCall']);
        }
    }
    public static function onAfterDelete($object)
    {
        if (isset($object->treeLevel) && $object->treeLevel > 0) {
            $object->afterDeleteTree();
        }
        $object->clearModelTagCache();
        $object->setCounterCache();
        $object->setSumCache();
        $object->deleteWith();
        if (method_exists($object, 'afterDeleteCall')) {
            return call_user_func([$object, 'afterDeleteCall']);
        }
    }

    public static function onBeforeRestore($object)
    {
        if (method_exists($object, 'beforeRestoreCall')) {
            return call_user_func([$object, 'beforeRestoreCall']);
        }
    }
    public static function onAfterRestore($object)
    {
        $object->clearModelTagCache();
        $object->setCounterCache();
        $object->setSumCache();
        if (method_exists($object, 'afterRestoreCall')) {
            return call_user_func([$object, 'afterRestoreCall']);
        }
    }

    /**
     * 清除模型关联缓存
     */
    public function clearModelTagCache()
    {
        if (empty(app('request')->business_id)) {
            Cache::tag(get_base_class($this))->clear();
        } else {
            Cache::tag(get_base_class($this) . '_for_business_' . app('request')->business_id)->clear();
        }
    }

    /**
     * 关联删除
     */
    protected function deleteWith()
    {
        foreach ($this->relationLink as $key => $relation) {
            if (!in_array($relation['type'], ['hasOne', 'hasMany', 'belongsToMany']) || !isset($relation['deleteWith'])) {
                continue;
            }
            if (true === $relation['deleteWith']) {
                if (in_array($relation['type'], ['hasOne', 'hasMany'])) {
                    $foreign_key = $relation['foreignKey'] ?? Str::snake($this->name) . '_id';
                    $foreign = model($relation['foreign']);
                    try {
                        $delete_ids = $foreign->where($foreign_key, '=', $this[$this->getPk()])->column($foreign->getPk());
                        if ($delete_ids) {
                            $foreign->destroy($delete_ids);
                        }
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                } elseif (in_array($relation['type'], ['belongsToMany'])) {
                    // 多对多只删除中间表数据 关联表的数据不会删除
                    $local_key = $relation['localKey'] ?? Str::snake($this->name) . '_id';
                    if (get_model_name($relation['middle'])) {
                        $middle = model($relation['middle']);
                        try {
                            $delete_ids = $middle->where($local_key, '=', $this[$this->getPk()])->column($middle->getPk());
                            if ($delete_ids) {
                                $middle->destroy($delete_ids);
                            }
                        } catch (\Exception $e) {
                            throw new \Exception($e->getMessage());
                        }
                    }
                }
            }
        }
    }

    /**
     * 关联计数统计
     */
    protected function setCounterCache()
    {
        foreach ($this->relationLink as $key => $relation) {
            if ($relation['type'] != 'belongsTo') {
                continue;
            }
            if (empty($relation['counterCache'])) {
                continue;
            }
            $foreign_key = $relation['foreignKey'] ?? Str::snake($key) . '_id';
            if (!isset($this[$foreign_key])) {
                continue;
            }
            $counter_field = is_bool($relation['counterCache']) ? Str::snake($this->name) . '_count' : $relation['counterCache'];
            try {
                $foreignModel = model($relation['foreign']);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            // 父模型没有计数写入字段
            if (empty($foreignModel->form[$counter_field])) {
                continue;
            }
            $origin = $this->getOrigin();

            if ($this->isUpdate && $origin) {
                if ($origin[$foreign_key] == $this[$foreign_key]) {
                    continue;
                }
                try {
                    $foreignModel = $foreignModel->find($origin[$foreign_key]);
                    if (empty($foreignModel)) {
                        continue;
                    }
                    $foreignModel->isValidate(false)->save([
                        $counter_field => $this
                            ->where($foreign_key,  '=', $origin[$foreign_key])
                            ->where($relation['counterWhere'] ?? [])
                            ->count()
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }
            try {
                $foreignModel = $foreignModel->find($this[$foreign_key]);
                if (empty($foreignModel)) {
                    continue;
                }
                $foreignModel->isValidate(false)->save([
                    $counter_field => $this
                        ->where($foreign_key,  '=', $this[$foreign_key])
                        ->where($relation['counterWhere'] ?? [])
                        ->count()
                ]);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

    /**
     * 关联求和统计
     */
    protected function setSumCache()
    {
        foreach ($this->relationLink as $key => $relation) {
            if ($relation['type'] != 'belongsTo') {
                continue;
            }
            // 当前模型中必须有sumCache 的求和字段
            if (empty($relation['sumCache'])) {
                continue;
            }
            $foreign_key = $relation['foreignKey'] ?? Str::snake($key) . '_id';
            if (!isset($this[$foreign_key])) {
                continue;
            }

            try {
                $foreignModel = model($relation['foreign']);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $origin = $this->getOrigin();

            $sum_field = explode(',', $relation['sumCache']);// 当前模型中需要关联求和统计的字段，多个逗号分隔
            $sum_to_field = !empty($relation['sumCacheTo'])? explode(',', $relation['sumCacheTo']) : [];// 关联模型写入统计结果的字段，多个逗号分隔


            foreach ($sum_field as $index => $field) {
                if (!isset($this->form[$field])) {
                    continue;
                }
                $to_field = $sum_to_field[$index] ?? $field . '_sum';// 写入字段默认是当前统计`字段_sum`命名

                // 父模型没有求和写入字段
                if (!isset($foreignModel->form[$to_field])) {
                    continue;
                }

                if ($this->isUpdate && $origin) {
                    try {
                        $foreignModel = $foreignModel->find($origin[$foreign_key]);
                        if (empty($foreignModel)) {
                            continue;
                        }
                        $foreignModel->isValidate(false)->save([
                            $to_field => $this
                                ->where($foreign_key,  '=', $origin[$foreign_key])
                                ->where($relation['sumWhere'] ?? [])
                                ->sum($field)
                        ]);
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                }
                try {
                    $foreignModel = $foreignModel->find($this[$foreign_key]);
                    if (empty($foreignModel)) {
                        continue;
                    }

                    $foreignModel->isValidate(false)->save([
                        $to_field => $this
                            ->where($foreign_key,  '=', $this[$foreign_key])
                            ->where($relation['sumWhere'] ?? [])
                            ->sum($field)
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }
        }
    }
}