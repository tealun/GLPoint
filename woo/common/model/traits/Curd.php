<?php
declare (strict_types=1);

namespace woo\common\model\traits;

use think\facade\Config;
use woo\common\facade\Auth;
use woo\common\helper\Arr;
use woo\common\helper\Str;

trait Curd
{
    /**
     * 写入数据
     * @param array $data 写入数据
     * @param array $options 选项
     * @return bool  失败返回false  成功返回主键值
     */
    public function createData(array $data, array $options = [])
    {
//        if (app('http')->getName() != 'admin' && Config::get('woo.strict_check_contribute_fields')) {
//            if (!empty($options['allowField']) && is_array($options['allowField'])) {
//                // 减去不允许投稿的字段列表
//                $options['allowField'] = array_diff($options['allowField'], $this->getContributeFields(false));
//            } else {
//                // 自动获取允许投稿的字段列表
//                $options['allowField'] = $this->getContributeFields(true);
//            }
//            if (empty($options['allowField'])) {
//                $this->forceError('当前操作没有允许投稿的字段');
//                return false;
//            }
//        }
        $appName = app('http')->getName();
        $timestampMap = ['createTime', 'updateTime', 'deleteTime'];
        foreach ($timestampMap as $timeField) {
            if ($this->$timeField) {
                if (isset($data[$this->$timeField])) {
                    unset($data[$this->$timeField]);
                }
                if (
                    !empty($options['allowField'])
                    && is_array($options['allowField'])
                    && !in_array($this->$timeField, $options['allowField'])
                ) {
                    array_push($options['allowField'], $this->$timeField);
                }
            }
        }
        $login = Auth::user();
        if (!isset($data['is_not_set_login_foreign_key']) && $login && isset($login['login_foreign_key'])) {
            $login_foreign_key = $login['login_foreign_key'];
            if (isset($this->form[$login_foreign_key]) && empty($data[$login_foreign_key])) {
                $data[$login_foreign_key] = $login['login_foreign_value'];
                if (!empty($options['allowField']) && !in_array($login_foreign_key, $options['allowField'])) {
                    $options['allowField'][] = $login_foreign_key;
                }
            }
        }
        if (isset($data['is_not_set_login_foreign_key'])) {
            unset($data['is_not_set_login_foreign_key']);
        }

        if (!empty($options['allowField'])) {
            $app_fields_map = [
                'admin' => ['admin_id'],
                'business' => ['business_id', 'business_member_id'],
            ];
            if (array_key_exists($appName, $app_fields_map)) {
                foreach ($app_fields_map[$appName] as $field) {
                    if (!in_array($field, $options['allowField']) && isset($this->form[$field])) {
                        $options['allowField'][] = $field;
                    }
                }
            }
            $options['allowField'] = array_intersect($options['allowField'], $this->getTableFields());
            $this->allowField($options['allowField']);
        }

        try {
            $pk = $this->getPk();
            if (isset($data[$pk]) && empty($data[$pk])) {
                unset($data[$pk]);
            }
            if (isset($data[$pk])) {
                $count = $this->where($pk, '=', $data[$pk])->count();
                if ($count) {
                    $this->forceError('需要新增的数据ID：' .  $data[$pk] . '已存在');
                    return false;
                }
            }
            if ($this->save($data, $options['sequence'] ?? null)) {
                return $this->$pk;
            }
        } catch (\Exception $e) {
            $this->forceError($e->getMessage());
        }
        return false;
    }

    /**
     * 更新数据
     * @param array $data 要更新的数据
     * @param array $options 选项
     * @return bool 失败返回false  成功返回主键值
     * 估计较多人会使用错误，必须自己先查询以后再调用modifyData
     * eg. 比如要更新id为1的用户信息，必须先自己查询一次，再通过查询返回的模型对象进行modifyData操作 没法tp6就是这样搞的
     * $model = model('User')->find(1);
     * $model->modifyData($data);
     */
    public function modifyData(array $data, array $options = [])
    {
//        if (app('http')->getName() != 'admin' && Config::get('woo.strict_check_contribute_fields')) {
//            if (!empty($options['allowField']) && is_array($options['allowField'])) {
//                // 减去不允许投稿的字段列表
//                $options['allowField'] = array_diff($options['allowField'], $this->getContributeFields(false));
//            } else {
//                // 自动获取允许投稿的字段列表
//                $options['allowField'] = $this->getContributeFields(true);
//            }
//            if (empty($options['allowField'])) {
//                $this->forceError('当前操作没有允许投稿的字段');
//                return false;
//            }
//        }
        $appName = app('http')->getName();
        $timestampMap = ['createTime', 'updateTime', 'deleteTime'];
        foreach ($timestampMap as $timeField) {
            if ($this->$timeField) {
                if (isset($data[$this->$timeField])) {
                    unset($data[$this->$timeField]);
                }
                if (
                    !empty($options['allowField'])
                    && is_array($options['allowField'])
                    && !in_array($this->$timeField, $options['allowField'])
                ) {
                    array_push($options['allowField'], $this->$timeField);
                }
            }
        }
        if (!empty($options['allowField'])) {
            $app_fields_map = [
                'admin' => ['admin_id'],
                'business' => ['business_id', 'business_member_id'],
            ];
            if (array_key_exists($appName, $app_fields_map)) {
                foreach ($app_fields_map[$appName] as $field) {
                    if (!in_array($field, $options['allowField']) && isset($this->form[$field])) {
                        $options['allowField'][] = $field;
                    }
                }
            }
            $options['allowField'] = array_intersect($options['allowField'], $this->getTableFields());
            $this->allowField($options['allowField']);
        }

        try {
            $pk = $this->getPk();
            if (isset($data[$pk])) {
                unset($data[$pk]);
            }
            foreach ($data as $field => $value) {
                $this[$field] = $value;
            }
            if ($this->save()) {
                return $this->$pk;
            }
        } catch (\Exception $e) {
            $this->forceError($e->getMessage());
        }
        return false;
    }

    /**
     * 恢复数据
     * @param $id  需要恢复的id 可以是数组
     * @param array $where  额外的条件
     * @return array|bool
     */
    public function restoreData($id, array $where = [])
    {
        $pk = $this->getPk();
        try {
            $list = $this->onlyTrashed()->where($pk, is_array($id) ? 'IN' : '=', $id)->where($this->getCheckAdminWhere())->where($where)->select();
        } catch (\Exception $e) {
            $this->forceError($e->getMessage());
            return false;
        }
        if ($list->isEmpty()) {
            $this->forceError('要恢复的数据不存在或权限不足');
            return false;
        }
        $restore_id = [];
        foreach ($list as $item) {
            $restore_id[] = $item[$pk];
        }
        try {
            $this->restore([
                [$pk, 'IN', $restore_id]
            ]);
        } catch (\Exception $e) {
            $this->forceError($e->getMessage());
            return false;
        }
        return [
            'list' => $list->toArray(), // 被恢复的数据列表
            'count' => is_array($id) ? count($id) : 1,// 准备恢复的数据条数
            'restore_count' => count($restore_id) //成功恢复数据条数
        ];
    }

    /**
     * @param $id 需要删除数据的主键值 可以是一个数字 也可以是数组删除多个
     * @param array $where 被删除数据额外的条件
     * @param bool $force  是否强行删除
     * @return array|bool  失败返回false 成功返回数组（包含删除的数据、删除条数、成功删除条数）
     */
    public function deleteData($id, array $where = [], bool $force = false)
    {
        $pk = $this->getPk();
        try {
            $delete_list = $this->withTrashed()->where($pk, is_array($id) ? 'IN' : '=', $id)->where($this->getCheckAdminWhere())->where($where)->select();
        } catch (\Exception $e) {
            $this->forceError($e->getMessage());
            return false;
        }
        if ($delete_list->isEmpty()) {
            $this->forceError('要删除的数据不存在或权限不足');
            return false;
        }

        $delete_id = [];
        foreach ($delete_list as $item) {
            $delete_id[] = $item[$pk];
        }

        if (empty($delete_id)) {
            $this->forceError('没有找到需要被删除的数据');
        }
        try {
            $this->destroy($delete_id, $force);
        } catch (\Exception $e) {
            $this->forceError($e->getMessage());
            return false;
        }
        return [
            'list' => $delete_list->toArray(), // 被删除的数据列表
            'count' => is_array($id) ? count($id) : 1,// 准备删除数据条数
            'delete_count' => count($delete_id) //成功删除数据条数
        ];
    }

    /**
     * 大数据分页 提升效率 但是数据特点要求很高 ...
     * 无搜索 无其他字段排序 单机可以支持千万以上
     * @param array $options
     * @return array|bool
     */
    public function getPageX(array $options)
    {
        $options = $options + [
                'where' => [],
                'whereCallback' => '',//用于传递闭包
                'order' => [],
                'with' => [],
                'withJoin' => [],
                'whereOr' => [],
                'whereColumn' => [],
                'whereTime' => [],
                'field' => true,
                'limit' => 15,
                'group' => [],
                'having' => '',
                'cancelCheckAdmin' => false,
                'forceCheckAdmin' => false,
            ];
        if (!isset($options['paginate']['simple'])) {
            $options['paginate']['simple'] = false;
        }
        $options['paginate']['list_rows'] = $options['limit'];
        if (empty($options['order'])) {
            $options['order'] = $this->getDefaultOrder();
        }
        if (!isset($options['paginate']['query'])) {
            $options['paginate']['query'] = request()->getParams()['args'] ?? [];
        }
        if (!empty($options['with'])) {
            $options['with'] = $this->parseWith($options['with']);
        }
        if (!empty($options['withJoin'])) {
            $options['withJoin'] = $this->parseWith($options['withJoin']);
        }
        try {
            $model = $this;
            if ($options['cancelCheckAdmin']) {
                $model = $model->cancelCheckAdmin();
            }
            $check_where = $model->getCheckAdminWhere($options['forceCheckAdmin'] ?? false);
            if (isset($options['withTrashed']) && true === $options['withTrashed']) {
                $model = $model->withTrashed();
            }
            if (isset($options['onlyTrashed']) && true === $options['onlyTrashed']) {
                $model = $model->onlyTrashed();
            }
            if (!empty($options['with'])) {
                $model = $model->with($options['with']);
            }
            if (!empty($options['withJoin'])) {
                $model = $model->withJoin($options['withJoin']);
            }
            if (!empty($options['group'])) {
                $model = $model->group($options['group']);
                if (!empty($options['having']) && is_string($options['having'])) {
                    $model = $model->having($options['having']);
                }
            }
            $model = $model
                ->where($options['where'])
                ->whereOr($options['whereOr'])
                ->where(function ($query) use ($options) {
                    // 必须是二维数组 格式如：
//                [
//                    ['birthday', '>=', '1970-10-1'],
//                    ['birthday', 'between', ['1970-10-1', '2000-10-1']]
//                ]
                    $timeMethod = [
                        'whereTime',
                        'whereBetweenTime',
                        'whereNotBetweenTime',
                        'whereYear',
                        'whereMonth',
                        'whereWeek',
                        'whereDay',
                        'whereBetweenTimeField'
                    ];
                    foreach ($timeMethod as $method) {
                        if (!empty($options[$method]) && is_array($options[$method])) {
                            if (is_array($options[$method][0])) {
                                foreach ($options[$method] as $where) {
                                    $query->$method(...$where);
                                }
                            } else {
                                $query->$method(...$options[$method]);
                            }
                        }
                    }

                })->where($check_where);

            if ($options['whereCallback'] && is_callable($options['whereCallback'])) {
                $model = $model->where(function ($query) use ($options) {
                    $options['whereCallback']($query);
                });
            }

            //eg : ['id > :id AND name LIKE :name ', ['id' => 0, 'name' => 'thinkphp%']];
            if (!empty($options['whereRaw']) && is_string($options['whereRaw'][0] ?? false)) {
                $model = $model->whereRaw($options['whereRaw'][0], $options['whereRaw'][1] ?? []);
            }

            if (!empty($options['whereColumn'])) {
                // 必须是数组 格式如：
//                [
//                    ['update_time', '>', 'create_time'],
//                    ['name', '=', 'nickname']
//                ]
                foreach ($options['whereColumn'] as $column) {
                    $model = $model->whereColumn(...$column);
                }
            }
            $list = $model->field($options['field'])
                ->order($options['order'])
                ->paginateX($options['limit']);
            $render = $list->render();
            $list = $list->toArray();
            $pageList = $list['data'];
            unset($list['data']);
            $page = $list;
            // 获取到翻页数据以后，如果希望对每条数据中的某些字段值做进一步的处理 可以定义一个afterPageCall方法；会把每一条数据传给你，你处理完数据再把数据返回回来
            // 当然你也可以使用模型事件 afterReadCall完成
            if (method_exists($this, 'afterPageCall') && $pageList) {
                foreach ($pageList as &$item) {
                    $item = $this->afterPageCall($item);
                }
            }
            return [
                'render' => $render,
                'page' => $page,
                'list' => $pageList
            ];
        }  catch (\Exception $e) {
            $this->forceError($e->getMessage());
        }
        return false;
    }

    /**
     * 普通数据分页  改进版  withJOIN关联有问题 暂时没有好的办法 再想想 暂时不删
     * @param array $options
     * @return array
     */
    protected function getPageBackup(array $options = [])
    {
        $options = $options + [
                'where' => [],
                'whereCallback' => '',//用于传递闭包
                'order' => [],
                'with' => [],
                'withJoin' => [],
                'whereOr' => [],
                'whereRaw' => [],
                'whereColumn' => [],
                'whereTime' => [],
                'field' => true,
                'limit' => 15,
                'group' => [],
                'having' => '',
                'cancelCheckAdmin' => false,
                'forceCheckAdmin' => false,
                'paginate' => [

                ]
                // paginate 下的参数：
                /**
                 * list_rows    每页数量
                 * page    当前页
                 * path    url路径
                 * query    url额外参数
                 * fragment    url锚点
                 * var_page    分页变量
                 * type    分页类名
                 * simple    简洁分页
                 * total    总条数
                 */
            ];
        if (!isset($options['paginate']['simple'])) {
            $options['paginate']['simple'] = false;
        }
        $options['paginate']['list_rows'] = $options['limit'];
        if (empty($options['order']) && $default_order = $this->getDefaultOrder()) {
            $options['order'] = $default_order;
        }
        if (!isset($options['paginate']['query'])) {
            $options['paginate']['query'] = request()->getParams()['args'] ?? [];
        }
        if (!empty($options['with'])) {
            $options['with'] = $this->parseWith($options['with']);
        }
        if (!empty($options['withJoin'])) {
            $options['withJoin'] = $this->parseWith($options['withJoin']);
        }
        try {
            $model = $this;
            if ($options['cancelCheckAdmin']) {
                $model = $model->cancelCheckAdmin();
            }
            $check_where = $model->getCheckAdminWhere($options['forceCheckAdmin'] ?? false);

            if (isset($options['withTrashed']) && true === $options['withTrashed']) {
                $model = $model->withTrashed();
            }
            if (isset($options['onlyTrashed']) && true === $options['onlyTrashed']) {
                $model = $model->onlyTrashed();
            }
            if (!empty($options['with'])) {
                $model = $model->with($options['with']);
            }
            if (!empty($options['withJoin'])) {
                $model = $model->withJoin($options['withJoin']);
            }
            if (!empty($options['group'])) {
                $model = $model->group($options['group']);
                if (!empty($options['having']) && is_string($options['having'])) {
                    $model = $model->having($options['having']);
                }
            }

            $model = $model
                ->where($options['where'])
                ->whereOr($options['whereOr'])
                ->where(function ($query) use ($options) {
                    // 必须是二维数组 格式如：
                    /*
                    [
                        ['birthday', '>=', '1970-10-1'],
                        ['birthday', 'between', ['1970-10-1', '2000-10-1']]
                    ]
                    */
                    $timeMethod = [
                        'whereTime',
                        'whereBetweenTime',
                        'whereNotBetweenTime',
                        'whereYear',
                        'whereMonth',
                        'whereWeek',
                        'whereDay',
                        'whereBetweenTimeField'
                    ];
                    foreach ($timeMethod as $method) {
                        if (!empty($options[$method]) && is_array($options[$method])) {
                            if (is_array($options[$method][0])) {
                                foreach ($options[$method] as $where) {
                                    $query->$method(...$where);
                                }
                            } else {
                                $query->$method(...$options[$method]);
                            }
                        }
                    }


                })->where($check_where);

            if ($options['whereCallback'] && is_callable($options['whereCallback'])) {
                $model = $model->where(function ($query) use ($options) {
                    $options['whereCallback']($query);
                });
            }

            //eg : ['id > :id AND name LIKE :name ', ['id' => 0, 'name' => 'thinkphp%']];
            if (!empty($options['whereRaw']) && is_string($options['whereRaw'][0] ?? false)) {
                $model = $model->whereRaw($options['whereRaw'][0], $options['whereRaw'][1] ?? []);
            }

            if (!empty($options['whereColumn'])) {
                // 必须是数组 格式如：
                /*
                [
                    ['update_time', '>', 'create_time'],
                    ['name', '=', 'nickname']
                ]
                */
                foreach ($options['whereColumn'] as $column) {
                    $model = $model->whereColumn(...$column);
                }
            }
            $result = $model->field([Str::snake($this->name) . '.' . $this->getPk()])
                ->order($options['order'])
                ->paginate($options['paginate']);

            $render = $result->render();
            $page = $result->toArray();
            $ids = array_values(Arr::combine($page['data'], $this->getPk(), $this->getPk()));
            unset($page['data']);

            $model = $this;

            if (!empty($options['with'])) {
                $model = $this->with($options['with']);
            }
            if (!empty($options['withJoin'])) {
                $model = $model->withJoin($options['withJoin']);
            }
            if (isset($options['withTrashed']) && true === $options['withTrashed']) {
                $model = $model->withTrashed();
            }
            if (isset($options['onlyTrashed']) && true === $options['onlyTrashed']) {
                $model = $model->onlyTrashed();
            }

            $pageList = $model
                ->where(Str::snake($this->name) . '.' . $this->getPk(), 'IN', $ids)
                ->field($options['field'])
                ->order($options['order'])
                ->select()
                ->toArray();

            // 获取到翻页数据以后，如果希望对每条数据中的某些字段值做进一步的处理 可以定义一个afterPageCall方法；会把每一条数据传给你，你处理完数据再把数据返回回来
            // 当然你也可以使用模型事件 afterReadCall完成
            if (method_exists($this, 'afterPageCall') && $pageList) {
                foreach ($pageList as &$item) {
                    $item = $this->afterPageCall($item);
                }
            }

            return [
                'render' => $render,
                'page' => $page,
                'list' => $pageList
            ];
        }  catch (\Exception $e) {
            $this->forceError($e->getMessage());
        }
        return false;
    }

    // 老版普通翻页备份  20220809 又换回来了
    // 上面改进版 赞不确定有什么问题 所以原代码先保留几个月
    public function getPage(array $options = [])
    {
        $options = $options + [
                'where' => [],
                'whereCallback' => '',//用于传递闭包
                'order' => [],
                'with' => [],
                'withJoin' => [],
                'whereOr' => [],
                'whereRaw' => [],
                'whereColumn' => [],
                'whereTime' => [],
                'field' => true,
                'limit' => 15,
                'group' => [],
                'having' => '',
                'cancelCheckAdmin' => false,
                'forceCheckAdmin' => false,
                'paginate' => [

                ]
                // paginate 下的参数：
                /**
                 * list_rows    每页数量
                 * page    当前页
                 * path    url路径
                 * query    url额外参数
                 * fragment    url锚点
                 * var_page    分页变量
                 * type    分页类名
                 * simple    简洁分页
                 * total    总条数
                 */
            ];
        if (!isset($options['paginate']['simple'])) {
            $options['paginate']['simple'] = false;
        }
        $options['paginate']['list_rows'] = $options['limit'];
        if (empty($options['order'])) {
            $options['order'] = $this->getDefaultOrder();
        }
        if (!isset($options['paginate']['query'])) {
            $options['paginate']['query'] = request()->getParams()['args'] ?? [];
        }
        if (!empty($options['with'])) {
            $options['with'] = $this->parseWith($options['with']);
        }
        if (!empty($options['withJoin'])) {
            $options['withJoin'] = $this->parseWith($options['withJoin']);
        }
        try {
            $model = $this;
            if ($options['cancelCheckAdmin']) {
                $model = $model->cancelCheckAdmin();
            }
            $check_where = $model->getCheckAdminWhere($options['forceCheckAdmin'] ?? false);

            if (isset($options['withTrashed']) && true === $options['withTrashed']) {
                $model = $model->withTrashed();
            }
            if (isset($options['onlyTrashed']) && true === $options['onlyTrashed']) {
                $model = $model->onlyTrashed();
            }
            if (!empty($options['with'])) {
                $model = $model->with($options['with']);
            }
            if (!empty($options['withJoin'])) {
                $model = $model->withJoin($options['withJoin']);
            }
            if (!empty($options['group'])) {
                $model = $model->group($options['group']);
                if (!empty($options['having']) && is_string($options['having'])) {
                    $model = $model->having($options['having']);
                }
            }
            $model = $model
                ->where($options['where'])
                ->whereOr($options['whereOr'])
                ->where(function ($query) use ($options) {
                    // 必须是二维数组 格式如：
//                [
//                    ['birthday', '>=', '1970-10-1'],
//                    ['birthday', 'between', ['1970-10-1', '2000-10-1']]
//                ]
                    $timeMethod = [
                        'whereTime',
                        'whereBetweenTime',
                        'whereNotBetweenTime',
                        'whereYear',
                        'whereMonth',
                        'whereWeek',
                        'whereDay',
                        'whereBetweenTimeField'
                    ];
                    foreach ($timeMethod as $method) {
                        if (!empty($options[$method]) && is_array($options[$method])) {
                            if (is_array($options[$method][0])) {
                                foreach ($options[$method] as $where) {
                                    $query->$method(...$where);
                                }
                            } else {
                                $query->$method(...$options[$method]);
                            }
                        }
                    }

                })->where($check_where);

            if ($options['whereCallback'] && is_callable($options['whereCallback'])) {
                $model = $model->where(function ($query) use ($options) {
                    $options['whereCallback']($query);
                });
            }

            //eg : ['id > :id AND name LIKE :name ', ['id' => 0, 'name' => 'thinkphp%']];
            if (!empty($options['whereRaw']) && is_string($options['whereRaw'][0] ?? false)) {
                $model = $model->whereRaw($options['whereRaw'][0], $options['whereRaw'][1] ?? []);
            }

            if (!empty($options['whereColumn'])) {
                // 必须是数组 格式如：
//                [
//                    ['update_time', '>', 'create_time'],
//                    ['name', '=', 'nickname']
//                ]
                foreach ($options['whereColumn'] as $column) {
                    $model = $model->whereColumn(...$column);
                }
            }
            $list = $model->field($options['field'])
                ->order($options['order'])
                ->paginate($options['paginate']);
            $render = $list->render();
            $list = $list->toArray();
            $pageList = $list['data'];
            unset($list['data']);
            $page = $list;
            // 获取到翻页数据以后，如果希望对每条数据中的某些字段值做进一步的处理 可以定义一个afterPageCall方法；会把每一条数据传给你，你处理完数据再把数据返回回来
            // 当然你也可以使用模型事件 afterReadCall完成
            if (method_exists($this, 'afterPageCall') && $pageList) {
                foreach ($pageList as &$item) {
                    $item = $this->afterPageCall($item);
                }
            }
            return [
                'render' => $render,
                'page' => $page,
                'list' => $pageList
            ];
        }  catch (\Exception $e) {
            $this->forceError($e->getMessage());
        }
        return false;
    }

    /**
     * 获取下一条数据
     * @param int $id
     * @param array $options
     * @return array
     */
    public function getNext(int $id, array $options = [])
    {
        $order = [];
        if (isset($this->form['list_order'])) {
            $order['list_order'] = 'DESC';
        }
        $order[$this->getPk()] = 'DESC';

        $result = $this->with($options['with'] ?? [])
            ->where($this->getPk(), $this->orderType == 'desc' ? '<' : '>' , $id)
            ->where($options['where'] ?? [])
            ->whereOr($options['whereOr'] ?? [])
            ->field($options['field'] ?? true)
            ->order($order)
            ->find();

        return $result ? $result->toArray() : [];
    }

    /**
     * 获取上一条数据
     * @param int $id
     * @param array $options
     * @return array
     */
    public function getPrev(int $id, array $options = [])
    {
        $order = [];
        if (isset($this->form['list_order'])) {
            $order['list_order'] = 'ASC';
        }
        $order[$this->getPk()] = 'ASC';

        $result = $this->with($options['with'] ?? [])
            ->where($this->getPk(), $this->orderType == 'desc' ? '>' : '<' , $id)
            ->where($options['where'] ?? [])
            ->whereOr($options['whereOr'] ?? [])
            ->field($options['field'] ?? true)
            ->order($order)
            ->find();
        return $result ? $result->toArray() : [];
    }
}