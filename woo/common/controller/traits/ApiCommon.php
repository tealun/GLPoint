<?php
declare (strict_types = 1);

namespace woo\common\controller\traits;

use think\App;

trait ApiCommon
{
    protected  $smsDriver = null;

    public function __construct(App $app)
    {
        parent::__construct($app);
        array_push($this->middleware, \woo\common\middleware\ApiCheck::class);
    }

    protected function create()
    {
        $data = $this->request->post();

        // 可以通过$this->local['data']传入一些固定或自定义数据
        $data = array_merge($data, $this->local['data'] ?? []);

        // 默认值处理
        if (!empty($this->local['default'])) {
            foreach ((array)$this->local['default'] as $field => $value) {
                if (empty($data[$field])) {
                    $data[$field] = $value;
                }
            }
        }


        foreach ($this->args as $field => $value) {
            if ($field == 'parent_id') {
                if (isset($this->mdl->parentModel) && isset($this->mdl->form[$this->local['parent_id']])) {
                    $data[$this->local['parent_id']] = intval($this->args['parent_id']);
                }
            } else if (isset($this->mdl->form[$field])) {
                $data[$field] = $value;
            }
        }

        // 自动识别用户字段
        if ($this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $data[$login_foreign_key] = $this->login['login_foreign_value'];
                if (!empty($this->local['allowField']) && !in_array($login_foreign_key, $this->local['allowField'])) {
                    $this->local['allowField'][] = $login_foreign_key;
                }
            }
        }

        // 审核
        if (isset($this->mdl->form['is_verify']) && !isset($data['is_verify'])) {
            $data['is_verify'] = setting('user_default_verify', 0);
        }
        if (!empty($this->local['allowField'])) {
            // 减去不允许投稿的字段列表
            $this->local['allowField'] = array_diff($this->local['allowField'], $this->mdl->getContributeFields(false));
        } else {
            // 自动获取允许投稿的字段列表
            $this->local['allowField'] = $this->mdl->getContributeFields(true);
        }
        if (isset($this->mdl->form['list_order']) && empty($this->local['allowField']['list_order'])) {
            $this->local['allowField'][]= 'list_order';
        }

        $result = $this->mdl->createData($data, ['allowField' => $this->local['allowField'] ?? []]);

        if ($result) {
            return $this->ajax('success', $this->local['success_message'] ?? $this->mdl->cname . '添加成功', ['id' => $result]);
        }
        return $this->ajax('error', $this->local['error_message'] ?? $this->mdl->cname . '添加失败', $this->mdl->getError());
    }

    protected function modify()
    {
        if (!isset($this->local['id'])) {
            $id = isset($this->args['id']) ? intval($this->args['id']) : intval($this->request->post($this->mdlPk, 0));
        } else {
            $id = intval($this->local['id']);
        }
        if ($id <= 0) {
            return $this->ajax('error', '缺少参数[id]');
        }
        // 自动识别用户
        // 默认只有自己才能改自己的  如果希望都可以 $this->local['all_user_allow'] = true;
        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }

        $model = $this->mdl->where($this->mdlPk, '=', $id)->where($this->local['where'] ?? [])->find();
        if (empty($model)) {
            return $this->ajax('error', '需要修改的数据不存在');
        }

        if (!empty($this->local['allowField'])) {
            // 减去不允许投稿的字段列表
            $this->local['allowField'] = array_diff($this->local['allowField'], $this->mdl->getContributeFields(false));
        } else {
            // 自动获取允许投稿的字段列表
            $this->local['allowField'] = $this->mdl->getContributeFields(true);
        }

        $data = $this->request->post();
        // 默认值处理
        if (!empty($this->local['default'])) {
            foreach ((array)$this->local['default'] as $field => $value) {
                if (empty($data[$field])) {
                    $data[$field] = $value;
                }
            }
        }

        $result = $model->modifyData($data, ['allowField' => $this->local['allowField'] ?? []]);

        if ($result) {
            return $this->ajax('success', $this->local['success_message'] ?? $this->mdl->cname . '修改成功', ['id' => $result]);
        }
        return $this->ajax('error', $this->local['error_message'] ?? $this->mdl->cname . '修改失败', $model->getError());
    }

    protected function delete()
    {
        if (!isset($this->local['id'])) {
            $id = $this->request->param('id', 0, 'intval');
        } else {
            $id = intval($this->local['id']);
        }
        if ($id <= 0) {
            return $this->ajax('error', '缺少参数[id]');
        }
        // 自动识别用户
        // 默认只有自己才能改自己的  如果希望都可以 $this->local['all_user_allow'] = true;
        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }
        // 如果是软删除模型 默认进回收站  $this->local['force'] = true 可以完全删除
        $result = $this->mdl->deleteData($id, $this->local['where'] ?? [], $this->local['force'] ?? false);
        if (false === $result) {
            return $this->ajax('error', array_values($this->mdl->getError())[0] ?? '删除失败');
        }
        $this->local['delete_result'] =  $result;
        return $this->ajax('success', $this->local['success_message'] ?? '删除成功', $result);
    }

    protected function forceDelete()
    {
        if (!$this->mdl->isSoftDelete()) {
            return $this->ajax('error', '当前模型非软删除模型，不支持该功能');
        }
        $this->local['force'] = true;
        return $this->delete();
    }

    protected function batchDelete()
    {
        $selected_ids = $this->request->param('ids', []);
        if (is_string($selected_ids)) {
            $selected_ids = explode(',', $selected_ids);
        }
        if (empty($selected_ids)) {
            return $this->ajax('error', '使用POST提交ids参数');
        }
        // 自动识别用户
        // 默认只有自己才能改自己的  如果希望都可以 $this->local['all_user_allow'] = true;
        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }
        $result = $this->mdl->deleteData($selected_ids, $this->local['where'] ?? [], $this->local['force'] ?? false);
        if (false === $result) {
            return $this->ajax('error', array_values($this->mdl->getError())[0] ?? '删除失败');
        }
        $this->local['delete_result'] =  $result;
        if ($result['delete_count'] == $result['count']) {
            $msg = "批量删除成功，{$result['delete_count']}条数据被成功删除";
        } else {
            $msg = "批量删除成功，{$result['delete_count']}条数据被成功删除，"  . ($result['count'] - $result['delete_count']) . "条数据因权限不足删除失败";
        }
        return $this->ajax('success', $msg, $result);
    }

    protected function forceBatchDelete()
    {
        if (!$this->mdl->isSoftDelete()) {
            return $this->ajax('error', '当前模型非软删除模型，不支持该功能');
        }
        $this->local['force'] = true;
        return $this->batchDelete();
    }

    protected function page()
    {
        // 自动识别用户
        // 默认只有自己才能改自己的  如果希望都可以 $this->local['all_user_allow'] = true;
        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }

        if (isset($this->local['limit'])) {
            $this->local['limit'] = intval($this->local['limit']) > 0 ?  intval($this->local['limit']) : 10;
        }


        $data = $this->mdl->getPage([
            'withTrashed' => $this->local['withTrashed'] ?? false,// 查询包含删除的数据
            'onlyTrashed' => $this->local['onlyTrashed'] ?? false,// 只查询删除的数据
            'with' => $this->local['with'] ?? [],
            'withJoin' => $this->local['withJoin'] ?? [],
            'where' => $this->local['where'] ?? [],
            'whereOr' => $this->local['whereOr'] ?? [],
            'whereCallback' => $this->local['whereCallback'] ?? '',
            'whereColumn' => $this->local['whereColumn'] ?? [],
            'whereTime' => $this->local['whereTime'] ?? [],
            'whereBetweenTime' => $this->local['whereBetweenTime'] ?? [],
            'whereNotBetweenTime' => $this->local['whereNotBetweenTime'] ?? [],
            'whereYear' => $this->local['whereYear'] ?? [],
            'whereMonth' => $this->local['whereMonth'] ?? [],
            'whereWeek' => $this->local['whereWeek'] ?? [],
            'whereDay' => $this->local['whereDay'] ?? [],
            'whereBetweenTimeField' => $this->local['whereBetweenTimeField'] ?? [],
            'field' => $this->mdl->selectField($this->local['field'] ?? true, $this->local['except_field'] ?? []),
            'order' => $this->local['order'] ?? $this->mdl->getDefaultOrder(),
            'limit' => $this->local['limit'] ?? 10,
            'paginate' => $this->local['paginate'] ?? []
        ]);
        if (false === $data) {
            return $this->ajax('error', '获取失败：' . array_values($this->mdl->getError())[0] ?? '');
        }
        unset($data['render']);
        if (isset($this->local['callback'])) {
            if (is_callable($this->local['callback'])) {
                $data = $this->local['callback']($data);
            } elseif (is_string($this->local['callback']) && method_exists($this, $this->local['callback'])) {
                $data = $this->{$this->local['callback']}($data);
            }
        }
        return $this->ajax('success', '获取成功', $data);
    }

    protected function get()
    {
        $by = $this->local['by'] ?? $this->mdlPk;
        $condition = $this->local['condition'] ?? '=';
        $value = $this->local['value'] ?? $this->request->param('value', '', 'trim,strip_tags');

        // 自动识别用户
        // 默认只有自己才能改自己的  如果希望都可以 $this->local['all_user_allow'] = true;
        if (empty($this->local['all_user_allow']) && $this->login && isset($this->login['login_foreign_key'])) {
            $login_foreign_key = $this->login['login_foreign_key'];
            if (isset($this->mdl->form[$login_foreign_key])) {
                $this->local['where'][] = [$login_foreign_key, '=', $this->login['login_foreign_value']];
            }
        }
        if (!empty($this->local['field']) && !in_array($this->mdlPk, $this->local['field'])) {
            array_unshift($this->local['field'], $this->mdlPk);
        }

        try {
            $data = $this->mdl
                ->with($this->local['with'] ?? [])
                ->field($this->mdl->selectField($this->local['field'] ?? true, $this->local['except_field'] ?? []))
                ->where($by, $condition, $value)
                ->where($this->local['where'] ?? [])
                ->whereOr($this->local['whereOr'] ?? [])
                ->find();
        } catch (\Exception $e) {
            return $this->ajax('error', $e->getMessage());
        }

        if (empty($data)) {
            return $this->ajax('error', '数据不存在');
        }
        $data = $data->toArray();
        if (!empty($this->local['neighbor'])) {
            // 查询上一条 、 下一条
            $prev = $this->mdl->getPrev($data[$this->mdlPk], [
                'with' => $this->local['with'] ?? [],
                'field' => $this->mdl->selectField($this->local['field'] ?? true, $this->local['except_field'] ?? [])
            ]);
            $data['prevData'] = $prev ?? null;
            $next = $this->mdl->getNext($data[$this->mdlPk], [
                'with' => $this->local['with'] ?? [],
                'field' => $this->mdl->selectField($this->local['field'] ?? true, $this->local['except_field'] ?? [])
            ]);
            $data['nextData'] = $next ?? null;
        }
        if (isset($this->local['callback'])) {
            if (is_callable($this->local['callback'])) {
                $data = $this->local['callback']($data);
            } elseif (is_string($this->local['callback']) && method_exists($this, $this->local['callback'])) {
                $data = $this->{$this->local['callback']}($data);
            }
        }
        return $this->ajax('success', '查询成功', $data);
    }

    /**
     * 获取引擎对象
     * @return \addons\sms\Manager
     * @throws \think\Exception
     */
    protected function getSmsDriver($driver = '')
    {
        if (!class_exists('addons\sms\Manager')) {
            throw new \Exception('未安装sms插件');
        }
        if (empty($driver)) {
            $driver = $this->app->config->get('api.sms_driver');
        }
        // api配置文件中自行指定引擎
        return new \addons\sms\Manager($driver);
    }


    /**
     * 验证短信/邮件验证码
     * @param string $touser  手机或邮箱号
     * @param string $code  被验证的验证码
     * @param string $scence  场景 如果有场景一定对应才能验证上
     * @return mixed
     * @throws \think\Exception
     */
    protected function verifyCode(string $touser, string $code, string $scence= '', $driver = '')
    {
        return call_user_func_array([$this->getSmsDriver($driver), 'verifyCode'], [$touser, $code, $scence]);
    }

    /**
     * 自行验证码验证码
     * @param null $captcha  验证码字符
     * @param null $key    验证码key
     * @param bool $snapchat 阅后即焚
     */
    protected function checkCaptcha($captcha = null, $key = null, $snapchat = true)
    {
        return api_check_captcha($captcha, $key, $snapchat);
    }

    /**
     * 操作成功
     * @param string $message
     * @param array $data
     * @return mixed
     */
    protected function success(string $message = '成功', $data = [])
    {
        return $this->ajax('success', $message, $data);
    }

    /**
     * 操作失败
     * @param string $message
     * @param array $data
     * @return mixed
     */
    protected function error(string $message = '失败', $data = [])
    {
        return $this->ajax('error', $message, $data);
    }
}