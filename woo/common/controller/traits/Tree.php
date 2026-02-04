<?php
declare (strict_types = 1);

namespace woo\common\controller\traits;

use think\facade\Cache;
use think\facade\Db;
use woo\common\helper\Arr;
use woo\common\annotation\Ps;

/**
 * 单独处理无限极的显示方式 需要使用到的无限极 需要先use trait
 * Trait Tree
 * @package woo\common\controller\traits
 */
trait Tree
{

    protected $treeHelper;

    protected function showTree(string $tabName = '', int $limit = 5000, bool $page = false, bool $isFold = true)
    {
//        if (empty($tabName)) {
//            $tabName = $this->args['tabname'] ?? 'basic';
//        }
//        $tree = app(\woo\common\helper\Tree::class, [$this->mdl]);
//        $this->local['limit'] = $limit;
//        $this->mdl->tableTab[$tabName]['table']['page'] = $page;
//        $this->mdl->tableTab[$tabName]['table']['data'] = $tree->getListData();
//
//        $this->local['limit'] = $limit;
//        if ($isFold) {
//            $this->mdl->tableTab[$tabName]['table']['done'] = "foldTree";
//        }
        //20210809 优化
        if (empty($tabName)) {
            $tabName = $this->args['tabname'] ?? 'basic';
        }
        $this->local['limit'] = $limit;
        $this->mdl->tableTab[$tabName]['table']['page'] = $page;
        $this->mdl->tableTab[$tabName]['table']['showTree'] = true;
        $this->local['field'] = ['parent_id'];
        if ($isFold) {
            $this->mdl->tableTab[$tabName]['table']['done'] = "foldTree";
        }
        if (isset($this->mdl->form[$this->mdl->display])) {
            if (isset($this->mdl->form[$this->mdl->display]['list']) && is_array($this->mdl->form[$this->mdl->display]['list'])) {
                $this->mdl->form[$this->mdl->display]['list']['templet'] = 'html';
            } else {
                $this->mdl->form[$this->mdl->display]['list'] = 'html';
            }
        }
        return parent::index();
    }

    protected function showList()
    {
        $this->treeHelper = new \woo\common\helper\Tree($this->mdl);

        $this->assign->options['id'] = $this->mdlPk;
        $this->assign->options['display'] = $this->mdl->display;
        $this->assign->options = array_merge([
            'indent' => !$this->request->isMobile() ? 30 : 20,
            'is_fold' => true,
            'left_delimiter' => '〖',
            'right_delimiter' => '〗',
            'item_tool_bar' => [],
            'is_ajax' => false
        ], $this->assign->options, $this->local['options'] ?? []);

        $local_fields = [];
        $this->local['fields'] = Arr::normalize($this->local['fields'] ?? []);
        foreach ($this->local['fields'] as $field => $info) {
            if (isset($this->mdl->form[$field])) {
                $local_fields[$field] = $info;
            }
        }
        $this->assign->options['fields'] = array_diff_key($local_fields, [
            $this->assign->options['id'] => '',
            $this->assign->options['display'] => ''
        ]);

        if ($this->local['tool_bar']['create'] ?? true) {
            $this->addAction('create','添加一级' . $this->mdl->cname, (string) url('create', ['parent_id' => 0]), 'woo-layer-load woo-theme-btn', 'layui-icon-add-1', 10);
        }
        if ($this->local['tool_bar']['sortable'] ?? true && isset($this->mdl->form['list_order'])) {
            $this->addAction('sortable','一级' . $this->mdl->cname . '排序', (string) url('sort', ['parent_id' => 0]), 'layui-btn-warm', 'woo-icon-paixu', 10);
        }

        if ($this->local['item_tool_bar']['create_child'] ?? true) {
            $this->assign->options['item_tool_bar'][] = [
                'name' => 'create_child',
                'title' => '新增子' . $this->mdl->cname,
                'sort' => 40,
                'icon' => 'layui-icon-add-circle',
                'class' => 'woo-layer-load',
                'url' => (string) url('create', ['parent_id' => '{{d.' . $this->mdlPk . '}}']),
                'js_func' => false
            ];
        }
        if ($this->local['item_tool_bar']['sort_child'] ?? true) {
            $this->assign->options['item_tool_bar'][] = [
                'name' => 'sort_child',
                'title' => '排序子' . $this->mdl->cname,
                'sort' => 30,
                'icon' => 'woo-icon-paixu',
                'class' => '',
                'url' => (string) url('sort', ['parent_id' => '{{d.' . $this->mdlPk . '}}']),
                'js_func' => false,
                'templet' => '#sortChild'
            ];
        }
        if ($this->local['item_tool_bar']['modify'] ?? true) {
            $this->assign->options['item_tool_bar'][] = [
                'name' => 'modify',
                'title' => '编辑',
                'sort' => 20,
                'icon' => 'layui-icon-edit',
                'class' => 'woo-layer-load',
                'url' => (string) url('modify', ['id' => '{{d.' . $this->mdlPk . '}}']),
                'js_func' => false
            ];
        }
        if ($this->local['item_tool_bar']['delete'] ?? true) {
            $this->assign->options['item_tool_bar'][] = [
                'name' => 'delete',
                'title' => '删除',
                'sort' => 10,
                'icon' => 'layui-icon-delete',
                'class' => '',
                'url' => (string) url('delete', ['id' => '{{d.' . $this->mdlPk . '}}']),
                'js_func' => 'delete_item'
            ];
        }

        $sort_list = array_column($this->assign->options['item_tool_bar'], 'sort');
        array_multisort($sort_list,SORT_DESC, $this->assign->options['item_tool_bar']);
        //pr($this->assign->options['item_tool_bar']);

        if (!$this->assign->options['is_ajax']) {
            $list = $this->getDataForCache(0);
            $this->assign->list = array_values($list);
        } else {
            $this->assign->list = $this->getAjaxData(0);
        }
        $this->assign->common_templet_file =  \think\facade\Config::get('woo.custom_templet_file');
        $this->assign->woo_templet_file = woo_path() . 'common/builder/table/templet/default.html';
        $this->local['header_title'] = $this->local['header_title'] ?? $this->mdl->cname . '列表';
        // 自动设置头部信息
        $this->autoSetHeaderInfo();
        return $this->fetch($this->local['fetch'] ?? 'tree');
    }

    /**
     * @return mixed
     * @Ps(true,as="index")
     */
    public function getTreeList()
    {
        $parent_id = $this->args['parent_id'] ?? -1;
        $level = $this->args['level'] ?? -1;
        if ($parent_id < 0 || $level < 0) {
            return $this->message('参数不正确', 'error');
        }
        try {
            return $this->ajax('success', '' , $this->getAjaxData($parent_id, $level + 1));
        }   catch (\Exception $e) {
            return $this->message($e->getMessage(), 'error');
        }
    }

    protected function getAjaxData($parent_id, $level = 1)
    {
        $isCheckAdmin = $this->local['isCheckAdmin'] ?? true;
        if ($isCheckAdmin) {
            $cacheKey = $this->params['controller'] . '_treedata_' . $parent_id . '_' . $this->login['id'];
        } else {
            $cacheKey = $this->params['controller'] . '_treedata_' . $parent_id;
        }
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        if (!$isCheckAdmin) {
            $this->mdl = $this->mdl->cancelCheckAdmin();
        }
        $check_where = $this->mdl->getCheckAdminWhere();
        try {
            $list =  $this->mdl
                ->where('parent_id', '=', $parent_id)
                ->where($this->local['where'] ?? [])
                ->whereOr($this->local['whereOr'] ?? [])
                ->where($check_where)
                ->order($this->mdl->getDefaultOrder())
                ->select()
                ->toArray();

            foreach ($list as &$item) {
                if (!isset($item['level'])) {
                    $item['level'] = $level;
                }
                if (!isset($item['children_count'])) {
                    $item['children_count'] = $this->mdl->where('parent_id', '=', $item[$this->mdlPk])->where($check_where)->count();
                }
            }
            Cache::tag(model_cache_tag(get_base_class($this->mdl)))->set($cacheKey, $list, 3600);
        } catch (\Exception $e) {
            throw(new \Exception($e->getMessage()));
        }
        return $list;
    }

    protected function getDataForCache($parent_id, $level = 1)
    {
        $list = [];
        foreach ($this->treeHelper->get('children', $parent_id) as $id) {
            $item =  $this->treeHelper->get($id);
            if (!isset($item['level'])) {
                $item['level'] = $level;
            }
            if (!isset($item['children_count'])) {
                $item['children_count'] = count($this->treeHelper->get('children', $id));
            }
            $list[$id] = $item;
            $list = $list  + $this->getDataForCache($id, $level+ 1) ?? [];
        }
        return $list;
    }
}