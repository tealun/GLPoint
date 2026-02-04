<?php
declare (strict_types=1);

namespace woo\common\abstracts;

use think\facade\Db;
use woo\common\Auth;
use app\common\model\AdminRule;
use woo\common\facade\Cache;

abstract class AppInstall
{
    protected $request;
    protected $targetName = '';
    protected $targetPath = '';
    protected $nameSpace = '';

    public function __construct(string $targetName = '')
    {
        $this->request = app()->request;
        if (empty($this->targetName)) {
            $this->targetName = $targetName ?: $this->getName();
        }
        if (empty($this->targetPath)) {
            $this->targetPath = base_path() . $targetName . DIRECTORY_SEPARATOR;
        }
        if (empty($this->nameSpace)) {
            $this->nameSpace = "\\app\\" . $this->targetName;
        }
    }

    /**
     * 获取标识
     * @return mixed|null
     */
    final protected function getName()
    {
        $class = get_class($this);
        list(, $name, ) = explode('\\', $class);
        return $name;
    }


    /**
     * 自定义的安装接口
     * @return mixed
     */
    abstract public function install();

    /**
     * 自定义的卸载接口
     * @return mixed
     */
    abstract public function uninstall();

    /**
     * 获取应用信息
     * @return array
     */
    abstract public function getConfig():array;

    /**
     * 用于执行有关联的父子SQL 比如模型和字段数据
     * @param array $data  一个二维数组
     * @param string $prefix  数据表前缀替换 默认 __PREFIX__
     * @param string $parentReplace  子Sql中父ID的替换字符串  默认 __PARENT_ID__
     */
    protected function executeRelationData(array $data, string $prefix="__PREFIX__", $parentReplace = "__PARENT_ID__")
    {
        foreach ($data as $item) {
            if (empty($item['parent'])) {
                continue;
            }
            $parentSql = str_replace($prefix, get_db_config('prefix'), $item['parent']);
            $parentSql = preg_replace('/,\s\d{10}/i', ', ' . time(), $parentSql);
            try {
                Db::execute($parentSql);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            if (empty($item['children'])) {
                continue;
            }
            $parent_id = Db::getPdo()->lastInsertId();
            $childrenSql = str_replace($prefix, get_db_config('prefix'), $item['children']);
            $childrenSql = str_replace($parentReplace, $parent_id, $childrenSql);
            $childrenSql = preg_replace('/,\s\d{10}/i', ', ' . time(), $childrenSql);
            try {
                Db::execute($childrenSql);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
        return true;
    }

    /**
     * 添加后台栏目
     * @param array $data
     * @param int $parent_id
     * @throws \Exception
     */
    protected function addAdminRule(array $data, int $parent_id = 0)
    {
        if (isset($data['title'])) {
            $data = [$data];
        }
        foreach ($data as $item) {
            $menu = $item;
            if (isset($menu['children'])) {
                unset($menu['children']);
            }
            $menu['parent_id'] = $parent_id;
            $menu['other_name'] = $this->targetName;
            $menu['addon'] = isset($item['addon']) ? $item['addon']: $this->targetName;
            $menu['is_nav'] = $menu['is_nav'] ?? 1;
            $menu['admin_id'] = \woo\common\facade\Auth::user('id');
            $menu['type'] = empty($menu['controller']) || !empty($item['children']) ? 'directory' : 'menu';
            if ($menu['type'] == 'menu' && empty($menu['open_type'])) {
                $menu['open_type'] = '_iframe';
            }
            $model = new AdminRule();
            try {
                $model->save($menu);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            if (!empty($item['children'])) {
                $this->addAdminRule($item['children'], intval($model['id']));
            }
        }
    }

    protected function addAdminMenu(array $data, int $parent_id = 0)
    {
        $result = $this->addAdminRule($data, $parent_id);
        Cache::tag('AdminRule')->clear();
        return $result;
    }

    /**
     * 卸载时 移除模型 但是不会删表（存在风险，后期跟进需求改进）
     */
    protected function removeModel()
    {
        $models = model('Model')->where('addon', '=', $this->targetName)->column('id');
        if (empty($models)) {
            return true;
        }
        model('Model')->destroy($models);
        return true;
    }

    /**
     * 卸载时 移除后台栏目
     */
    protected function removeAdminRule()
    {
        model('AdminRule')->where('other_name', '=', $this->targetName)->delete();
        return true;
    }

    protected function removeAdminMenu()
    {
        $result = $this->removeAdminRule();
        Cache::tag('AdminRule')->clear();
        return $result;
    }
}