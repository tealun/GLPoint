<?php
declare (strict_types=1);

namespace woo\common\model\traits;

use think\facade\Db;

trait Tree
{
    protected $treeLevel = 0;

    /**
     * 父级ID 的验证方法 'rule' => ['call', 'checkParent']
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    public function checkParent($value, $rule, $data)
    {
        $tree = app(\woo\common\helper\Tree::class, [$this]);
        if ($this->isUpdate) {
            if ($data[$this->getPk()] == $value || in_array($value, $tree->getDeepChildren($data[$this->getPk()]))) {
                return "不能选择自己或后代为父级";
            }
        }
        if ($tree->getDeepLevel(intval($value)) >= $this->treeLevel) {
            return "最多可以添加" . $this->treeLevel . '级' . $this->cname;
        }
        return true;
    }

    protected function afterWriteTree()
    {
        $origin = $this->getOrigin();
        $pk = $this->getPk();
        if ($this->isInsert) {
            if ($family = $this->getFamilyValue()) {
                $self_data['family'] = $family;
            }
            if ($level = $this->getLevelValue()) {
                $self_data['level'] = $level;
            }
            if (isset($this->form['children_count']) && $this['parent_id'] > 0) {
                $parent_data[$pk] = $this['parent_id'];
                $parent_data['children_count'] =  Db::raw("`children_count`+1");
            }
        } else if (isset($origin['parent_id']) && isset($this['parent_id'])) {
            if ($origin['parent_id'] != $this['parent_id']) {
                if ($family = $this->getFamilyValue()) {
                    $self_data['family'] = $family;
                }
                if ($level = $this->getLevelValue()) {
                    $self_data['level'] = $level;
                }
                if (isset($this->form['children_count']) && $this['parent_id'] > 0) {
                    $parent_data[$pk] = $this['parent_id'];
                    $parent_data['children_count'] =  Db::raw("`children_count`+1");
                }
                if (isset($this->form['children_count']) && $origin['parent_id'] > 0) {
                    $origin_data[$pk] = $origin['parent_id'];
                    $origin_data['children_count'] =  Db::raw("`children_count`-1");
                }
                if (isset($this->form['family']) || isset($this->form['level'])) {
                    $change_children = true;
                }
            }
        }

        if (!empty($self_data)) {
            $self_data[$pk] = $this[$pk];
            Db::connect($this->getConnection())->table($this->getTable())->update($self_data);
        }

        if (!empty($parent_data)) {
            Db::connect($this->getConnection())->table($this->getTable())->update($parent_data);
        }

        if (!empty($origin_data)) {
            Db::connect($this->getConnection())->table($this->getTable())->update($origin_data);
        }
        if (isset($change_children) && $change_children) {
            $this->changeChildren($this[$pk]);
        }
    }

    protected function afterDeleteTree()
    {
        $pk = $this->getPk();
        $origin = $this->getData();
        if (isset($this->form['children_count']) && $origin['parent_id'] > 0) {
            $origin_data[$pk] = $origin['parent_id'];
            $origin_data['children_count'] =  Db::raw("`children_count`-1");
            Db::connect($this->getConnection())->table($this->getTable())->update($origin_data);
        }
        $this->childrenDelete($origin[$pk]);
    }

    protected function childrenDelete($parent_id)
    {
        $pk = $this->getPk();
        $children = $this->where('parent_id', '=', $parent_id)->column($pk);
        if ($children) {
            foreach ($children as $id) {
                $this->childrenDelete($id);
            }
            if (!isset($this->form['delete_time'])) {
                Db::connect($this->getConnection())->table($this->getTable())->where($pk, 'IN', $children)->delete();
            } else {
                Db::connect($this->getConnection())->table($this->getTable())->where($pk, 'IN', $children)->update([
                    'delete_time' => time()
                ]);
            }
        }
    }

    /**
     * 不建议频繁修改父级id值，可能会引起较多的连贯修改
     * @param $parent_id
     * @throws \think\db\exception\DbException
     */
    protected function changeChildren($parent_id)
    {
        $pk = $this->getPk();
        $children = $this->where('parent_id', '=', $parent_id)->column($pk);
        if (empty($children)) {
            return;
        }
        $parent = $this->find($parent_id);
        if (empty($parent)) {
            return;
        }
        foreach ($children as $id) {
            $change = [];
            if (isset($this->form['family'])) {
                $change['family'] = $parent['family'] . $id . ',';
            }
            if (isset($this->form['level'])) {
                $change['level'] = $parent['level'] + 1;
            }
            if (!empty($change)) {
                $change[$pk] = $id;
                Db::connect($this->getConnection())->table($this->getTable())->update($change);
            }
            $this->changeChildren($id);
        }
    }

    protected function getFamilyValue()
    {
        if (!isset($this->form['family'])) {
            return false;
        }
        if ($this['parent_id'] == 0) {
            return ",{$this[$this->getPk()]},";
        }
        $parent_family = $this->where($this->getPk(), '=', $this['parent_id'])->value('family');
        return $parent_family ? $parent_family . $this[$this->getPk()] . ',' : ",{$this[$this->getPk()]},";
    }

    protected function getLevelValue()
    {
        if (!isset($this->form['level'])) {
            return false;
        }
        if ($this['parent_id'] == 0) {
            return 1;
        }
        $parent_level = $this->where($this->getPk(), '=', $this['parent_id'])->value('level');
        return $parent_level ? $parent_level + 1 : 1;
    }

    public function getTreeLevel() {
        return $this->treeLevel;
    }
}