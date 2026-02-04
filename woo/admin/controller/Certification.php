<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use woo\common\annotation\Ps;
use woo\common\helper\ThinkApi;

class Certification extends \app\common\controller\Admin
{
    public function index()
    {
        $this->mdl->tableTab['basic']['item_tool_bar'][] = [
            'name' => 'create_item',
            'title' => '认证',
            'sort' => 100,
            'class' => 'btn-9',
            'icon' => '',
            'templet' => '#aaa',
        ];
        return parent::{__FUNCTION__}();
    }

    /**
     * @Ps(name="认证")
     */
    public function cert()
    {
        $id = intval($this->args['id'] ?? 0);
        $data = $this->mdl->with(['User'])->where('id', '=', $id)->find();
        if (empty($data)) {
            return $this->ajax('error', '需要认证的数据不存在');
        }
        if ($data['is_cert']) {
            return $this->ajax('error', '该会员已经通过认证，无需重复提交');
        }
        if ($data['User']['is_bind_mobile'] && $data['User']['mobile'] != $data['mobile']) {
            return $this->ajax('error', '该会员绑定的手机号码与实名提交手机号码不一致，认证失败');
        }
        $result = (new ThinkApi())->telecomQuery($data['truename'], $data['id_card'], $data['mobile']);

        if ($result['code'] == 0) {
            if ($result['data']['res'] == 1) {
                $data->modifyData(['is_cert' => 1]);

                $data['User']->modifyData([
                    'is_bind_mobile' => 1,
                    'mobile' => $data['mobile'],
                    'truename' => $data['truename']
                ]);
                return $this->ajax('success', '认证成功');
            } else {
                return $this->ajax('error', $result['data']['resmsg'] ?? "认证失败");
            }
        } else {
            return $this->ajax('error', $result['message']);
        }
    }

}