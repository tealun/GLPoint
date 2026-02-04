<?php
declare (strict_types=1);

namespace woo\common\helper;

use think\facade\Cache;
use woo\common\facade\Auth;

class Tree
{
    protected $model;
    protected $pk;
    protected $list;
    protected $cache = [];
    protected $children = [];
    protected $staticCache = [];
    protected $tempData = [];

    public function __construct(\think\Model $model)
    {
        $this->model = $model;
        $this->pk = $this->model->getPk();
        if (!isset($this->model->form['parent_id'])) {
            throw  new \Exception(get_class($this->model) . '中不存在必要字段"parent_id"');
        }
    }


    /**
     * 获取指定id是第多少代
     * @param $id
     * @return array|mixed
     */
    public function getDeepLevel($id)
    {
        if (isset($this->model->form['level'])) {
            return $this->get($id, 'level', 0);
        }
        return count($this->getDeepParents($id));
    }

    /**
     * 获取获取指定ID的 顶级父辈ID
     * @param $child_id
     * @return mixed
     */
    public function getTopId($child_id)
    {
        $top_id = $child_id;
        if ($parent_id = $this->get($child_id, 'parent_id')) {
            $top_id = $this->getTopId($parent_id);
        }
        return $top_id;
    }


    /**
     * 获取指定ID的所有 父辈ID
     * @param $child_id
     * @return array\
     */
    public function getDeepParents($child_id)
    {
        $list = [];
        $parent_id = $this->get($child_id, 'parent_id');
        if ($parent_id || $parent_id === 0) {
            $list = array_merge([$parent_id], $this->getDeepParents($parent_id));
        }
        return $list;
    }

    /**
     * 获取指定ID下的 所有后代ID
     * @param $parent_id
     * @return array|mixed
     */
    public function getDeepChildren($parent_id) {
        $list = [];
        if ($children = $this->get('children', $parent_id)) {
            $list = $children;
            foreach ($children as $id) {
                $list = array_merge($list, $this->getDeepChildren($id));
            }
        }
        return $list;
    }


    /**
     * 获取值
     * @param string|integer $key  可以是缓存标识 也看是整数 表示id为多少的具体数据
     * @param string $index $key下指定索引的值
     * @param array $defualt  默认值
     * @return array|mixed
     */
    public function get($key = null, $index = null, $defualt = [])
    {
        if (empty($this->staticCache)) {
            if (!$this->hasCache()) {
                $this->setCache();
            }
            $cache = $this->getCache();

            $this->staticCache = $cache;
        } else {
            $cache = $this->staticCache;
        }
        if (!isset($key)) {
            return $cache;
        }

        if (is_numeric($key)) {
            if (empty($cache['list'][$key])) {
                return $defualt ?: '';
            }
            if (!isset($index)) {
                return $cache['list'][$key];
            } elseif (isset($cache['list'][$key][$index])) {
                return $cache['list'][$key][$index];
            }
        } elseif (array_key_exists($key, $cache)) {
            if (!isset($index)) {
                return $cache[$key];
            } elseif (isset($cache[$key][$index])) {
                return $cache[$key][$index];
            }
        }
        return $defualt;
    }

    /**
     * 获取到缓存所有数据
     * @return mixed
     */
    public function getCache()
    {
        return $this->cache ? $this->cache : Cache::get($this->getCacheName());
    }

    /**
     * 设置缓存
     * @return bool
     */
    public function setCache()
    {
        $full_list = empty($this->tempData)  ? Arr::combine($this->queryData(), $this->pk) : Arr::combine($this->tempData, $this->pk);
        if (empty($full_list)) {
            Cache::tag(model_cache_tag($this->getModelName()))->set($this->getCacheName(), []);
            return true;
        }
        $this->list = $full_list;
        $this->cache['threaded'] = $this->getThreaded();
        $this->getChildren(0, $this->cache['threaded']);
        $this->cache['children'] = $this->children;

        // 自定义缓存
        if (method_exists($this->model, 'getCustomCache') && empty($this->tempData)) {
            $custom = $this->model->getCustomCache();
            if (!empty($custom) && is_array($custom)) {
                foreach ($custom as $name => $options) {
                    $this->list = $this->queryData($options);
                    if (!empty($this->list)) {
                        $this->cache[$name] = $this->getThreaded();
                        $this->getChildren(0, $this->cache[$name]);
                        $this->cache[$name . '_children'] = $this->children;
                    } else {
                        $this->cache[$name] = [];
                        $this->cache[$name . '_children'] = [];
                    }
                }
            }
        }
        $this->cache['list'] = $full_list;
        if (empty($this->tempData)) {
            Cache::tag(model_cache_tag($this->getModelName()))->set($this->getCacheName(), $this->cache);
        }
    }

    /**
     * 判断是否有缓存
     * @return bool
     */
    public function hasCache()
    {
        return Cache::has($this->getCacheName()) && empty($this->tempData);
    }

    /**
     * 删除缓存数据
     * @return $this
     */
    public function deleteCache()
    {
        Cache::delete($this->getCacheName());
        return $this;
    }

    public function setTempData($data = [])
    {
        $this->tempData = $data;
        return $this;
    }

    public function getOptions(string $delimiter = '　　')
    {
        $cache = $this->get();
        if (empty($cache)) {
            return [];
        }
        $data = $this->deepOptionsData($cache['threaded'], 1, $delimiter);
        return [0 => '顶级分类'] + $data;
    }

    public function getXmOptions($level = 0, $value = '')
    {
        $value = explode(',', (string) $value);
        return  [
            [
                "name" => "顶级分类",
                "value" => 0,
                "children" => $this->deepXmOptionsData($this->get('children', 0), $level, 1, $value)
            ]
        ];
    }

    public function getCascaderOptions($value = '', $data = [])
    {
        $value = is_array($value) ? $value : explode(',', (string) $value);

        $cache = $this->get();
        $title = $this->model->display;
        if (empty($cache['children'])) {
            return [];
        }
        $options = [];
        foreach ($cache['children'] as $parent_id => $children) {
            $options[$parent_id] = ['parent_id' => $cache['list'][$parent_id]['parent_id'] ?? 0, 'children' => []];
            foreach ($children as $child_id) {
                array_push($options[$parent_id]['children'], [
                    'id' => $child_id,
                    'title' => $cache['list'][$child_id][$title],
                    //'selected' => !in_array($child_id, $value) ? false : true,
                    'is_children' => $cache['children'][$child_id] ? true : false
                ]);
            }
        }
        return $options;
    }

    public function getLayuiTree($level = 0)
    {
        return $this->deepLayuiTreeData($this->get('children', 0), $level);
    }

    protected function deepLayuiTreeData($children, $level = 0, $nowLevel = 1)
    {
        $list = [];
        $title = $this->model->display;
        foreach ($children as $id) {
            $item = $this->get($id);
            $my = [
                "title" => $item[$title],
                "id" => $id,
                "href" => (string) url(app('request')->getParams()['action'], [Str::snake($this->getModelName()) .'_id' => $id]),
                "spread" => $nowLevel > 1 ? false : true
            ];
            if ((($level && $nowLevel < $level) || !$level) && $this->get('children', $id)) {
                $my['children'] = $this->deepLayuiTreeData($this->get('children', $id), $level, $nowLevel + 1);
            }
            $list[] = $my;
        }
        return $list;
    }

    protected function deepXmOptionsData($children, $level = 0, $nowLevel = 1, array $value = [])
    {
        $list = [];
        $title = $this->model->display;
        foreach ($children as $id) {
            $item = $this->get($id);
            $my = [
                "name" => $item[$title],
                "value" => $id,
            ];
            if ($value && in_array($id, $value)) {
                $my['selected'] = true;
            }
            if ((($level && $nowLevel < $level) || !$level) && $this->get('children', $id)) {
                $my['children'] = $this->deepXmOptionsData($this->get('children', $id), $level, $nowLevel + 1, $value);
            }
            $list[] = $my;
        }
        return $list;
    }

    protected function deepOptionsData(array $children, int $level = 1, string $delimiter = '　　')
    {
        $list = [];
        $title = $this->model->display;
        $i = 1;
        foreach ($children as $id => $next) {
            $my = $this->get($id, $title);
            $prefix = str_repeat($delimiter,  $level - 1);
            if ($level > 0) {
                if ($i < count($children) || !empty($next)) {
                    $prefix .= "├─ ";
                } else {
                    $prefix .= "└─ ";
                }
            }
            $my = $prefix . '<span class="t">' . $my . '</span>';
            $list[$id] = $my;
            if ($next) {
                $list = $list + $this->deepOptionsData($next, $level + 1, $delimiter);
            }
            $i++;
        }
        return $list;
    }

    /**
     * 获取列表数据
     * @return array
     */
    public function getListData($tempData = [])
    {
        if ($tempData) {
            $this->setTempData($tempData);
        }
        $cache = $this->get();
        if (empty($cache)) {
            return [];
        }
        $data = $this->deepListData($cache['threaded']);
        return $data;
    }

    protected function deepListData($children, $level = 1)
    {
        $list = [];
        $title = $this->model->display;
        $i = 1;
        foreach ($children as $id => $next) {
            $my = $this->get($id);
            $prefix = "<div class='woo-tree-bind clearfix' data-id='" . $id . "' data-parent='" . $my['parent_id'] . "' data-level='" . $level ."'>";
            $prefix .= str_repeat("<span class='woo-tree-holder'></span>",  $level - 1);
            if ($next) {
                $prefix .= "";
            }
            if ($level > 0 && !$next) {
                if ($i < count($children) || !empty($next)) {
                    $prefix .= "<span class='woo-tree-child'>├─</span>";
                } else {
                    $prefix .= "<span class='woo-tree-child'>└─</span>";
                }
            }
            $prefix .= "<span class='woo-tree-display'>";
            if ($next) {
                $prefix .= "<i class='layui-icon layui-icon-reduce-circle'></i>";
            }
            $prefix .=  $my[$title] ."</span></div>";
            $my[$title] = $prefix;
            $list[] = $my;
            if ($next) {
                $list = array_merge($list, $this->deepListData($next, $level + 1));
            }
            $i++;
        }
        return $list;
    }

    protected function getThreaded(int $parent_id = 0)
    {
        $cache = [];
        foreach ($this->list as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                unset($this->list[$key]);
                $cache[$item[$this->pk]] = $this->getThreaded(intval($item[$this->pk]));
            }
        }
        return $cache;
    }


    protected function getChildren(int $parent_id = 0, array $threaded = [])
    {
        if ($parent_id == 0) {
            $this->children = [];
        }
        $this->children[$parent_id] = array_keys($threaded);
        foreach ($threaded as $id => $children) {
            $this->getChildren($id, $children);
        }
    }

    protected function queryData(array $options = [])
    {
        return $this->model
            ->where($this->model->getCheckAdminWhere())
            ->where($options['where'] ?? [])
            ->whereOr($options['whereOr'] ?? [])
            ->order($options['order'] ?? $this->model->getDefaultOrder())
            ->select()
            ->toArray();
    }

    protected function getModelName()
    {
        return get_base_class($this->model);
    }

    public function getCacheName()
    {
        $login_id = Auth::user('id');
        return 'woo_' . app('http')->getName() . Str::snake($this->getModelName()) .  ($login_id ? '_' . $login_id : '')  . '_tree';
    }
}