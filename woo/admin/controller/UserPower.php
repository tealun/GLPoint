<?php

declare(strict_types=1);

namespace woo\admin\controller;

use woo\common\annotation\Ps;

class UserPower extends \app\common\controller\Admin
{

    /**
     * @Ps(name="授权")
     */
    public function index()
    {
        $tree = $this->getLayTree();

        $list = model('UserGroup')->order(model('UserGroup')->getDefaultOrder())->select()->toArray();

        $this->assign->list = $list;
        $this->assign->layui_tree = $tree;

        $this->local['header_title'] = '会员授权';
        $this->local['header_tip'] = '点击"会员分类"名进行会员分组授权';
        return $this->fetch();
    }

    /**
     * @Ps(as="index")
     */
    public function  setPower()
    {
        if (!$this->request->isAjax()) {
            return $this->message('请求方式错误', 'error');
        }
        $data = $this->request->post();
        if (empty($data['id'])) {
            return $this->ajax('error', '没有明确的会员分类');
        }
        $conent = model('UserMenu')->where([
            ['url', '<>', ''],
            ['id', 'IN', $data['tree_ids'] ?? []]
        ])->column('url', 'id');


        $exists = $this->mdl->where([
            ['user_group_id', '=', intval($data['id'])]
        ])->find();

        if ($exists) {
            $result = $exists->modifyData([
                'content' => json_encode($conent ?? [])
            ]);
        } else {
            $result = $this->mdl->createData([
                'user_group_id' => intval($data['id']),
                'content' => json_encode($conent ?? [])
            ]);
            $exists = $this->mdl;
        }

        if ($result) {
            return $this->ajax('success', '授权成功!');
        } else {
            return $this->ajax('error', array_values($exists->getError())[0] ?? '授权失败');
        }
    }

    /**
     * @Ps(as="index")
     */
    public function getUserGroupInfo()
    {
        if (!$this->request->isAjax()) {
            return $this->message('请求方式错误', 'error');
        }
        $id = intval($this->request->get('id', 0));
        $exists = $this->mdl->where([
            ['user_group_id', '=', $id]
        ])->find();
        if (empty($exists)) {
            return $this->ajax('error', '当前会员分类还没有授权');
        }
        $content = json_decode($exists['content'] ?: '[]', true);
        if (empty($content)) {
            return $this->ajax('error', '当前会员分类授权为空');
        }
        return  $this->ajax('success', '获取成功', $this->getLayTree(0, $content));
    }

    protected function getLayTree($level = 0, $default = [])
    {
        return $this->deepLayTreeData(user_menu('children', 0), $level,1,$default);
    }

    protected function deepLayTreeData($children, $level = 0, $nowLevel = 1, $default = [])
    {
        $list = [];
        $i  = 0;
        foreach ($children as $id) {
            $i++;
            $item = user_menu($id);
            if (!empty($item['is_not_power'])) {
                continue;
            }
            $my = [
                "title" => $item['title']  . ($item['url'] ? "<span class='woo-route'>{$item['url']}</span>" : ""),
                "id" => $id,
                "spread" => $nowLevel <= 1 && $i <= 2? true : false,
                "checked" => in_array($item['url'], $default) &&  $item['url'] ? true : false
            ];
            if ((($level && $nowLevel < $level) || !$level) && user_menu('children', $id)) {
                $my['children'] = $this->deepLayTreeData(user_menu('children', $id), $level, $nowLevel + 1, $default);
            }
            $list[] = $my;
        }
        return $list;
    }


    /**
     * @Ps(false)
     */
    public function create()
    {
    }


    /**
     * @Ps(false)
     */
    public function modify()
    {
    }


    /**
     * @Ps(false)
     */
    public function delete()
    {
    }


    /**
     * @Ps(false)
     */
    public function batchDelete()
    {
    }


    /**
     * @Ps(false)
     */
    public function detail()
    {
    }


    /**
     * @Ps(false)
     */
    public function ajaxSwitch()
    {
    }


    /**
     * @Ps(false)
     */
    public function deleteIndex()
    {
    }


    /**
     * @Ps(false)
     */
    public function restore()
    {
    }


    /**
     * @Ps(false)
     */
    public function batchRestore()
    {
    }


    /**
     * @Ps(false)
     */
    public function forceDelete()
    {
    }


    /**
     * @Ps(false)
     */
    public function forceBatchDelete()
    {
    }


    /**
     * @Ps(false)
     */
    public function sort()
    {
    }


    /**
     * @Ps(false)
     */
    public function updateSort()
    {
    }

    /**
     * @Ps(false)
     */
    public function resetSort()
    {
    }
}
