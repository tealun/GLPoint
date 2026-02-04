<?php
declare (strict_types=1);

namespace woo\common\builder;

use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\Model;
use woo\common\Auth;
use woo\common\helper\Arr;
use woo\common\helper\Str;
use woo\common\helper\Tree;

class Table
{
    use \woo\common\builder\table\traits\ColParse;

    protected $request;

    protected $view;


    protected $data;

    /**
     * 对应模型名称
     */
    protected $model;

    /**
     * 模板对象
     */
    private $template;

    /**
     * Tab 默认一定会有一个叫【basic => 基本信息】的Tab  如果最终只有一个Tab不会显示tab-title
     * @var array
     */
    protected $tab = [];
    /**
     * 当前正在操作的Tab标识
     */
    protected $selectTab = '';

    protected  $error = [];

    protected $autoCheckedIds = [];

    public function __construct($model = '', array $defaultTab = [])
    {
        // 对象
        $this->request = app()->request;
        $this->view = app(\woo\common\View::class);
        $this->template = app('\think\Template');
        $this->template->config([
            'view_path' => '',
            'cache_path' => app()->getRuntimePath() . 'table' . DIRECTORY_SEPARATOR,
            'view_suffix' => ''
        ]);
        // 当前模型
        $this->setModel($model);
        // tab初始化
        $this->autoAddTab($defaultTab);
        // 识别当前tab
        $params = $this->request->getParams();
        if (!empty($params['args']['tabname']) && array_key_exists($params['args']['tabname'], $this->tab)) {
            $this->selectTab = $params['args']['tabname'];
        } else {
            $this->selectTab = array_keys($this->tab)[0];
        }
    }

    /**
     * 设置当前表单操作的模型
     * @param $model
     * @return $this
     * @throws \think\Exception
     */
    public function setModel($model)
    {
        if (empty($model)) {
            $params = $this->request->getParams();
            $model =  $params['addon_name']
                ? $params['addon_name'] . '.' . $this->request->getParams()['controller']
                : $this->request->getParams()['controller'];
        }
        if ($model && is_string($model)) {
            $model = model($model);
        }

        if ($model) {
            $this->model = $model;
        }
        return $this;
    }

    public function addTab(string $tabName, string $title, array $options = [])
    {
        if (empty($title)) {
            $title = $options['title'] ?? Str::studly($tabName);
        }
        $options['name'] = $tabName;
        $options['title'] = $title;
        if (!array_key_exists($tabName, $this->tab)) {
            $this->tab[$tabName] = $options;
        } else {
            $this->setTabAttr($tabName, '', $options);
        }
        $this->switchTab($tabName);
        return $this;
    }

    public function getTab()
    {
        return $this->tab;
    }

    /**
     * 设置某个表格Tab的属性
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setAttr($key, $value)
    {
        if (is_array($key)) {
            $value = $key;
            $key = '';
        }
        if ('' === $key) {
            $this->tab[$this->selectTab] = array_merge($this->tab[$this->selectTab], (array) $value);
        } else {
            $this->tab[$this->selectTab][$key] = $value;
        }
        return $this;
    }

    /**
     * 设置某个表格Tab下table属性 就是设置layui数据表格属性https://www.layui.com/doc/modules/table.html#options
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setTableAttr($key, $value)
    {
        if (is_array($key)) {
            $value = $key;
            $key = '';
        }
        if ('' === $key) {
            $this->tab[$this->selectTab]['table'] = array_merge($this->tab[$this->selectTab]['table'], (array) $value);
        } else {
            $this->tab[$this->selectTab]['table'][$key] = $value;
        }
        return $this;
    }

    /**
     * 添加Table头部按钮
     * @param array $options
     * @return $this
     */
    public function addToolBar(array $options = [])
    {
        if (isset($this->tab[$this->selectTab]['tool_bar']) && false === $this->tab[$this->selectTab]['tool_bar']) {
            return $this;
        }
        if (empty($this->tab[$this->selectTab]['tool_bar'])) {
            $this->tab[$this->selectTab]['tool_bar'] = [];
        }
        if (empty($options['name'])) {
            $options['name'] = count($this->tab[$this->selectTab]['tool_bar']) + 1;
        }
        array_push( $this->tab[$this->selectTab]['tool_bar'], $options);
        return $this;
    }

    /**
     * 设置Table头部按钮
     * @param $name
     * @param $key
     * @param $value
     */
    public function setToolBarInfo($name, $key, $value)
    {
        $this->tab[$this->selectTab]['tool_bar'] = array_map(function ($item) use ($name, $key, $value) {
            if ($item['name'] == $name) {
                $item[$key] = $value;
            }
            return $item;

        }, $this->tab[$this->selectTab]['tool_bar']);
    }

    /**
     * 获取Table头部按钮
     */
    public function getToolBarList()
    {
        $list = $this->tab[$this->selectTab]['tool_bar'] ?? [];
        if (!is_array($list)) {
            return [];
        }
        $set_list = [];
        foreach ($list as $item) {
            $item = array_merge([
                'name'  => '',
                'title' => '',
                'url' => '',
                'class' => '',
                'icon' => '',
                'sort' => 0,
                'tip' =>'',
                'js_func' => false,
                'templet' => false,
                'children' => [],
                'power' => '',
                'hover' => '',
                'check' => false,
                'length' => 0,
                'attrs' => false
            ], $item);
            if (!empty($item['power']) && !admin_link_power($item['power'])) {
                continue;
            }
            array_push($set_list, $item);
        }

        $sort_list = array_column($set_list, 'sort');
        array_multisort($sort_list,SORT_DESC, $set_list);
        return $set_list;
    }

    /**
     * 判断指定头部按钮是否存在
     * @param string $name
     * @return bool
     */
    public function isToolBarExists(string $name)
    {
        if (empty($this->tab[$this->selectTab]['tool_bar'])) {
            return false;
        }
        $exists = false;
        foreach ($this->tab[$this->selectTab]['tool_bar'] as $item) {
            if ($item['name'] == $name) {
                $exists = true;
            }
        }
        return $exists;
    }

    /**
     * 添加Table项目按钮
     * @param array $options
     * @return $this
     */
    public function addItemToolBar(array $options = [])
    {
        if (isset($this->tab[$this->selectTab]['item_tool_bar']) && false === $this->tab[$this->selectTab]['item_tool_bar']) {
            return $this;
        }
        if (empty($this->tab[$this->selectTab]['item_tool_bar'])) {
            $this->tab[$this->selectTab]['item_tool_bar'] = [];
        }
        if (empty($options['name'])) {
            $options['name'] = count($this->tab[$this->selectTab]['item_tool_bar']) + 1;
        }
        array_push( $this->tab[$this->selectTab]['item_tool_bar'], $options);
        return $this;
    }

    /**
     * 设置Table项目按钮
     * @param $name
     * @param $key
     * @param $value
     */
    public function setItemToolBarInfo($name, $key, $value)
    {
        $this->tab[$this->selectTab]['item_tool_bar'] = array_map(function ($item) use ($name, $key, $value) {
            if ($item['name'] == $name) {
                $item[$key] = $value;
            }
            return $item;

        }, $this->tab[$this->selectTab]['item_tool_bar']);
    }

    /**
     * 判断指定项目按钮是否存在
     * @param string $name
     * @return bool
     */
    public function isItemToolBarExists(string $name)
    {
        if (empty($this->tab[$this->selectTab]['item_tool_bar'])) {
            return false;
        }
        $exists = false;
        foreach ($this->tab[$this->selectTab]['item_tool_bar'] as $item) {
            if ($item['name'] == $name) {
                $exists = true;
            }
        }
        return $exists;
    }

    /**
     * 获取Table项目按钮
     */
    public function getItemToolBarList()
    {
        $list = $this->tab[$this->selectTab]['item_tool_bar'] ?? [];
        $table = $this->tab[$this->selectTab]['table'] ?? [];
        if (!is_array($list)) {
            return [];
        }
        $set_list = [];
        foreach ($list as $item) {
            $item = array_merge([
                'name'  => '',
                'title' => '',
                'url' => '',
                'class' => '',
                'icon' => '',
                'sort' => 0,
                'tip' => '',
                'js_func' => false,
                'templet' => false,
                'children' => [],
                'power' => '',
                'hover' => '',
                'where' => '',
                'where_type' => 'disabled', // 支持disabled禁用 和 hidden隐藏
                'length' => 0,
                'attrs' => false
            ], $item);
            if (!empty($item['power']) && !admin_link_power($item['power'])) {
                continue;
            }
            if (isset($table['itemToolbarStyle']) &&  in_array($table['itemToolbarStyle'], ['text', 'text_icon'])) {
                $item['title'] = $item['title'] ?:$item['hover'];
                !empty($item['title']) && $table['itemToolbarStyle'] == 'text' && $item['icon'] = '';
                !empty($item['title']) && $item['hover'] = '';
            }

            array_push($set_list, $item);
        }
        $sort_list = array_column($set_list, 'sort');
        array_multisort($sort_list,SORT_DESC, $set_list);
        return $set_list;
    }

    public function setLocal(string $key, $vlaue, $isPush = false)
    {
        if (is_array($vlaue) && false === $isPush) {
            $this->tab[$this->selectTab]['local'][$key] = array_merge(
                $this->tab[$this->selectTab]['local'][$key] ?? [],
                $vlaue
            );
        } else {
            if (isset($this->tab[$this->selectTab]['local'][$key]) && is_array($this->tab[$this->selectTab]['local'][$key])) {
                array_push($this->tab[$this->selectTab]['local'][$key], $vlaue);
            } else {
                $this->tab[$this->selectTab]['local'][$key] = $vlaue;
            }
        }
        return $this;
    }

    protected function getTableColsAttr()
    {
        $tab = $this->tab[$this->selectTab];
        $model = empty($tab['model']) ? $this->model : $tab['model'];
        if (is_string($model)) {
            $model = model($model);
        }
        $pk = $model->getPk();
        $list_fields = $tab['list_fields'] ?? [];
        // 列统计参数
        $total_rows = [];
        if (!empty($tab['total_row'])) {
            $total_rows = Arr::combine($tab['total_row'], 'field');
        }

        if (empty($list_fields)) {
            $list_fields = array_keys($model->form);
        }

        $list_fields = Arr::normalize($list_fields);
        if (!array_key_exists($pk, $list_fields)) {
            $list_fields = array_merge([$pk => []],$list_fields);
        }
        $cols = [];

        // 真实字段列表
        $tableFields = $model->getTableFields();
        if (!(isset($tab['checkbox']) && false === $tab['checkbox'])) {
            if (isset($tab['checkbox']) && is_array($tab['checkbox'])) {
                if (empty($tab['checkbox']['type'])) {
                    $tab['checkbox']['type'] = 'checkbox';
                }
            } else {
                $tab['checkbox'] = ['type' => isset($tab['checkbox']) && is_string($tab['checkbox']) && in_array($tab['checkbox'], ['checkbox', 'radio']) ? $tab['checkbox'] : 'checkbox', 'width' => 65];
            }
            $tab['checkbox']['field'] = 'item_checkbox';
            $cols[] = $tab['checkbox'];
        }

        if (in_array($tab['is_remove_pk'] ?? 0, [1,2], true)) {
            $cols[] = [
                'field' => 'ROW_INDEX',
                'title' => '序号',
                'width' => 60,
                'align' => 'center',
                'templet' => '{{d.LAY_NUM}}'
            ];
        }

        $toolbar = $tab['toolbar_options'] ?? [];
        $args = $this->request->getParams()['args'];
        if (isset($args['parent_id'])) {
            $parent_id = $model->getParentId();
        }

        $left_fixed_index = -1;
        $right_fixed_index = [];
        foreach ($list_fields as $field => $info) {
            if ($field === 'item_tool_bar') {
                $toolbar = $info;
                continue;
            }

            $info['field'] = $field;
            $info['elem'] = '';
            if (!empty($model->form[$field]['elem'])) {
                $info['elem'] = $model->form[$field]['elem'];
            }

            $list = ['templet' => 'show'];
            if (isset($model->form[$field]['list'])) {
                if (is_scalar($model->form[$field]['list'])) {
                    $list['templet'] = $model->form[$field]['list'];
                } elseif (is_array($model->form[$field]['list'])) {
                    $list = array_merge($list, $model->form[$field]['list']);
                }
            }
            $info = array_merge($list, $info);

            if (empty($info['templet']) && array_key_exists($field, $tab['list_fields'] ?? [])) {
                $info['templet'] = 'show';
            }

            if (is_string($info['templet']) && false === strpos($info['templet'], '<')) {
                $templet = $info['templet'];
                if (strpos($templet, '.') !== false) {
                    $templet = substr($templet, 0, strpos($templet, '.'));
                }
                if (method_exists($this, Str::camel($templet) . 'BeforeParse')) {
                    $result = $this->{Str::camel($templet) . 'BeforeParse'}($info, $model);
                    $info =  $result ?? $info;
                }
            }

            if ($info['templet'] != 'merge') {
                $info = $this->getTableColInfo($info, $model);
                if (!empty($total_rows) && !empty($total_rows[$field])) {
                    $row = [];
                    $row_data = $total_rows[$field];
                    if (!empty($row_data['row_text'])) {
                        $row['totalRowText'] = $row_data['row_text'];
                    }
                    if (!empty($row_data['total_row']) && $row_data['total_row'] != 'none') {
                        $row['totalRow'] = empty($row_data['templet']) ? true : $row_data['templet'];
                    }
                    $info = array_merge($info, $row);
                }
            } else {
                if (isset($info['merge_fields'])) {
                    $merge_fields = [];
                    $merge_title = [];
                    $info['merge_fields'] = Arr::normalize($info['merge_fields']);
                    foreach ($info['merge_fields'] as $f => $finfo) {
                        if (!isset($finfo['field'])) {
                            $finfo['field'] = $f;
                        }
                        $finfo = $this->getTableColInfo($finfo, $model);
                        $merge_fields[] = $finfo;
                        $merge_title[] = $finfo['title'];
                    }
                    if (!isset($info['title'])) {
                        $info['title'] = implode($info['delimiter'] ?? ' / ', $merge_title);
                    }
                    $info['merge_fields'] = $merge_fields;
                    $info['templet'] = '#' . Str::camel($info['templet']);
                }
            }

            if (isset($info['merge_fields'])) {
                foreach ($info['merge_fields'] as $f => $finfo) {
                    $rf = $f;
                    if (is_array($finfo) && isset($finfo['field'])) {
                        $rf = $finfo['field'];
                    }
                    if (in_array($rf, $tableFields, true)) {
                        $this->setLocal('field', [$rf]);
                    }
                }
            }
            if (isset($info['templet']) && in_array($info['templet'], [0, false, '0', ''], true)){
                continue;
            }
            if (isset($parent_id) && $field == $parent_id) {
                $info['hide'] = true;
            }
            $sortable_types = array('integer', 'float', 'date', 'datetime');
            if (!isset($info['sort']) && in_array($model->form[$field]['type'] ?? '', $sortable_types, true)) {
                $info['sort'] = true;
            }
            if (isset($info['type'])) {
                unset($info['type']);
            }
            // treetable不能排序
            if (isset($info['sort']) && !empty($tab['table']['treetable']) && isset($model->form['parent_id'])) {
                unset($info['sort']);
            }

            // 浮动识别
            if (isset($info['fixed']) && $info['fixed'] == 'left') {
                $left_fixed_index = count($cols);
            } elseif (isset($info['fixed']) && $info['fixed'] == 'right') {
                array_push($right_fixed_index, count($cols));
            }
            array_push($cols, $info);
        }

        // 20230209左浮动 有左浮动的列前面的字段全部左浮动
        if ($left_fixed_index >= 0) {
            for ($i = 0; $i <= $left_fixed_index; $i++) {
                if (!isset($cols[$i]['fixed'])) {
                    $cols[$i]['fixed'] = 'left';
                }
            }
        }

        // 20230209 右浮动列全部放后面去
        if (!empty($right_fixed_index)) {
            foreach ($right_fixed_index as $index) {
                $info = $cols[$index];
                unset($cols[$index]);
                array_push($cols, $info);
            }
        }
        $cols = array_values($cols);

        // 20230209 移动端考虑排版问题 移出浮动
        if ($this->request->isMobile()) {
            foreach ($cols as &$info) {
                if (isset($info['fixed'])) {
                    unset($info['fixed']);
                }
            }
        }

        if (!(false === $toolbar)) {
            $item_tool_list = $this->getItemToolBarList();
            if ($item_tool_list && is_array($item_tool_list)) {
                if (empty($toolbar['min_width']) || $toolbar['min_width'] < 100) {
                    $min = 0;
                    foreach ($item_tool_list as $item) {
                        if (!empty($item['length'])) {
                            $min += intval($item['length']) * 14;
                            if (!empty($item['icon'])) {
                                $min += 20;
                            }
                        } elseif (!empty($item['templet']) && is_string($item['templet'])) {
                            $w = mb_strlen(strip_tags($item['templet'])) * 14;
                            $min += $w >= 20 ? $w : 20;
                        } elseif (!empty($item['title'])) {
                            $min += mb_strlen($item['title']) * 14;
                            if (!empty($item['icon'])) {
                                $min += 20;
                            }
                        } elseif (!empty($item['icon'])) {
                            $min += 20;
                        }
                        if (!empty($item['children'])) {
                            $min += 14;
                        }
                        if (empty($tab['table']['itemToolbarStyle']) || $tab['table']['itemToolbarStyle'] == 'button') {
                            $min += 22;
                        } else {
                            $min += 14;
                        }
                    }
                    $min += 30;
                    $toolbar['min_width'] = max($min, 120);
                }
                $toolbar['minWidth'] = intval($toolbar['min_width']);
                unset($toolbar['min_width']);
                $toolbar['fixed'] = $toolbar['fixed'] ?? 'right';
                if (isset($toolbar['fixed']) && $toolbar['fixed'] === '') {
                    unset($toolbar['fixed']);
                }
                $toolbar['align'] = !empty($toolbar['align']) ? $toolbar['align'] : 'center';
                $toolbar['title'] = !empty($toolbar['title']) ? $toolbar['title'] : '操作';
                $toolbar['field'] = 'item_toolbar';
                $toolbar['ignoreExport'] = true;
                $toolbar['templet'] = 'js:renderItemToolBar';
                $toolbar['item_toolbar_list'] = $item_tool_list;
                if (isset($toolbar['fixed']) && $toolbar['fixed'] == 'left') {
                    array_unshift($cols, $toolbar);
                } else {
                    array_push($cols, $toolbar);
                }
            }
        }
        return [$cols];
    }

    protected function getTableColInfo(array $info, $model)
    {
        $tab = $this->tab[$this->selectTab];
        $field = $info['field'];
        $query_field = $field;
        $tableFields = $model->getTableFields();
        $pk = $model->getPk();

        $list = ['templet' => 'show'];
        if (isset($model->form[$field]['list'])) {
            if (is_scalar($model->form[$field]['list'])) {
                $list['templet'] = $model->form[$field]['list'];
            } elseif (is_array($model->form[$field]['list'])) {
                $list = array_merge($list, $model->form[$field]['list']);
            }
        }
        $info = array_merge($list, $info);

        if ($info['templet']  === 'assoc') {
            $info['templet']  = 'relation';
        }

        $relation = [];
        if (strpos($field, '-') > 0) {
            $relation = get_relation($field, $model);
        } else {
            if (!isset($info['title'])) {
                $info['title'] = $model->form[$field]['name'] ?? Str::studly($field);
            }
        }
        if (!isset($info['relation']['model']) && $info['templet'] === 'relation') {
            if (isset($model->form[$field]['foreign'])) {
                $relation = get_relation($model->form[$field]['foreign'], $model);
            } else {
                $relation = get_relation(Str::studly(substr($field, 0, -3)), $model);
            }
        }

        if ($relation) {
            if (isset($relation['type']) && in_array($relation['type'], ['belongsTo', 'hasOne', 'belongsToMany', 'belongsToThrough', 'hasOneThrough'])) {
                $info['templet'] = 'relation';
                $info['relation']['model'] = $relation['key'];
                $info['relation']['field'] = $relation[1];
                $info['relation']['type'] = $relation['type'];
                if ($relation['type'] == 'belongsTo') {
                    $query_field = $relation['foreignKey'] ?? Str::snake($info['relation']['model']) . '_id';
                }
                if (!isset($info['title'])) {
                    $info['title'] = model($relation[0])->form[$relation[1]]['name'] ?? Str::studly($field);
                }
                if (in_array($relation['type'], ['belongsTo', 'hasOne'])) {
                    $this->tab[$this->selectTab]['local']['with'][$info['relation']['model']]['field'][] = $info['relation']['field'];
                } else {
                    $this->tab[$this->selectTab]['local']['with'][] = $info['relation']['model'];
                }
            } else {
                $info['templet'] = 'show';
            }
        }

        if (in_array($query_field, $tableFields, true)) {
            $this->setLocal('field', [$query_field]);
        }

        if (($field == $pk && in_array($tab['is_remove_pk'] ?? 0, [2,3],true)) || empty($field) || $info['templet'] == 'hide') {
            $info['templet'] = 0;
            return $info;
        }

        if ($field == $pk && !isset($info['width']) && !isset($info['winWidth'])) {
            $info['width'] = 88;
        }

        if ($info['templet'] == 'show' && isset($model->form[$field]['options'])) {
            $info['templet'] = 'options';
        }

        if (empty($info['options']) && isset($model->form[$field]['options'])) {
            $info['options'] = $model->form[$field]['options'];
        }

        if ($info['templet'] == 'counter' && empty($info['counter'])) {
            $info['counter'] = $model->form[$field]['counter'] ?? substr($field, 0, -6);
        }

        if (isset($info['width']) && is_numeric($info['width'])) {
            $info['width'] = floatval($info['width']);
        }

        if (!isset($info['width']) && !isset($info['minWidth'])) {
            $info['minWidth'] =  intval(setting('table_cell_min_width')) >= 80 ? intval(setting('table_cell_min_width')) : 80;
        }

        if (isset($info['winWidth']) && is_numeric($info['winWidth'])) {
            $info['winWidth'] = floatval($info['winWidth']);
        }

        if ($info['templet'] == 'show') {
            unset($info['templet']);
            if (in_array($field, ['create_time', 'update_time', 'delete_time']) && !isset($info['style'])) {
                $info['style'] = "color:" . setting('table_timestamp_color') ?: '#888888';
                $info['width'] = empty($info['width']) || intval($info['width']) <= 146 ? 154: intval($info['width']);
                $info['templet'] = '#datetime';
            }
        } else {
            if (is_string($info['templet']) && false === strpos($info['templet'], '<') && false === strpos($info['templet'], 'js:')) {
                $info['templet'] = '#' . Str::camel($info['templet']);
            }
        }
        return $info;
    }

    protected function getTableAttr()
    {
        $tab = $this->tab[$this->selectTab];
        $model = empty($tab['model']) ? $this->model : $tab['model'];
        if (is_string($model)) {
            $model = model($model);
        }
        $pk = $model->getPk();
        $this->tab[$this->selectTab]['table']['pk'] = $pk;
        if (!isset($this->tab[$this->selectTab]['table']['displayField'])) {
            $this->tab[$this->selectTab]['table']['displayField'] =  $model->display;
        }
        if (!isset($this->tab[$this->selectTab]['table']['displayClassName'])) {
            $this->tab[$this->selectTab]['table']['displayClassName'] =  'table-display-field woo-theme-color';
        }
        if (!isset($tab['toolbar_options']['itemToolbarStyle']) || !in_array($tab['toolbar_options']['itemToolbarStyle'], ['button', 'text', 'text_icon'])) {
            $this->tab[$this->selectTab]['table']['itemToolbarStyle'] = setting('table_item_toolbar_style', 'button', false);
        } else {
            $this->tab[$this->selectTab]['table']['itemToolbarStyle'] = $tab['toolbar_options']['itemToolbarStyle'];
        }
        if (!isset($tab['toolbar_options']['itemToolbarTextClassName'])) {
            $this->tab[$this->selectTab]['table']['itemToolbarTextClassName'] = setting('table_item_toolbar_text_class', 'blue', false);
        } else {
            $this->tab[$this->selectTab]['table']['itemToolbarTextClassName'] = $tab['toolbar_options']['itemToolbarTextClassName'];
        }
        //table属性文档：https://www.layui.com/doc/modules/table.html
        //elem 	 	指定原始 table 容器的选择器或 DOM 系统默认 不能自行定义
        $this->tab[$this->selectTab]['table']['elem'] = '#' . $this->selectTab . 'Table';
        $this->tab[$this->selectTab]['table']['id'] = $this->selectTab . 'Table';

        // cols     设置表头  如果直接定义 系统不会做数据处理了  建议使用 setListFieldsAttr 定义 或 模型 tableTab 中list_fields定义
        if (empty($this->tab[$this->selectTab]['table']['cols'])) {
            $this->tab[$this->selectTab]['table']['cols'] = $this->getTableColsAttr();
        }

        if (!isset($this->tab[$this->selectTab]['table']['sortable'])) {
            // 列表拖拽排序 默认关闭
            $this->tab[$this->selectTab]['table']['sortable'] = $model->sortable ?? false;
        }

        if ($this->tab[$this->selectTab]['table']['sortable'] && isset($model->form['parent_id'])) {
            $this->tab[$this->selectTab]['table']['sortable'] = false;// 无限极不能列表直接排序 会容易导致数据结构错乱
        }

        if (!empty($tab['delete_index']) && !empty($this->tab[$this->selectTab]['table']['treetable'])) {
            unset($this->tab[$this->selectTab]['table']['treetable']);
        }

        if (!isset($this->tab[$this->selectTab]['table']['height']) && intval(setting('table_default_height', 0)) >= 100) {
            $this->tab[$this->selectTab]['table']['height'] = setting('table_default_height');
        }
        if (isset($this->tab[$this->selectTab]['table']['height']) && is_numeric($this->tab[$this->selectTab]['table']['height'])) {
            $this->tab[$this->selectTab]['table']['height'] = intval($this->tab[$this->selectTab]['table']['height']);
        }


        if (isset($model->form['parent_id']) && !empty($this->tab[$this->selectTab]['table']['treetable'])) {
            $this->tab[$this->selectTab]['table']['tree'] = Arr::deepMerge([
                'data' => [
                    'isSimpleData' => empty($this->tab[$this->selectTab]['table']['tree']['async']['enable'])
                ],
                'customName' => [
                    'name' =>  $this->tab[$this->selectTab]['table']['displayField'],
                    'isParent' => 'children_count',
                    'id' => $pk,
                    'pid' => 'parent_id',
                ],
                'view' => [
                    'showIcon' => true,
                    'dblClickExpand' => !$this->request->isMobile()
                ],
                'async' => [
                    'autoParam' => ['pid=id']
                ],
                'callback' => [
                    'beforeExpand' => null,
                    'onExpand' => null
                ]
            ], $this->tab[$this->selectTab]['table']['tree'] ?? []);


            $this->addToolBar([
                'name' => 'expand',
                'title' => '展开',
                'sort' => -5,
                'class' => 'btn-12',
                'js_func' => 'treetable_toggle',
                'icon' => 'layui-icon-folder',
            ]);
        } else if (isset($this->tab[$this->selectTab]['table']['treetable'])) {
            unset($this->tab[$this->selectTab]['table']['treetable']);
        }

        if (empty($this->tab[$this->selectTab]['table']['url'])) {
            $this->tab[$this->selectTab]['table']['url'] = $tab['url'] ?? '';
        }
        $this->tab[$this->selectTab]['table']['where'] = array_merge(
            $this->tab[$this->selectTab]['table']['where'] ?? [],
            [
                'tabname' => $tab['name']
            ]
        );
        if (!isset($this->tab[$this->selectTab]['table']['toolbar'])) {
            $this->tab[$this->selectTab]['table']['toolbar'] = '#' . $tab['name'] . 'ToolBar';
        }

        if ($this->tab[$this->selectTab]['table']['sortable'] && admin_link_power('sort') && array_key_exists('list_order', $model->form)) {
            if ($this->request->isMobile()) {
                array_unshift($this->tab[$this->selectTab]['table']['cols'][0], [
                    'templet' => '#mobileSortHandler',
                    'width' => 38,
                    'align' => 'center',
                    'field' => 'sort-handler',
                    'title' => ''
                ]);
            }
            $this->addToolBar([
                'name' => 'table_sort',
                'title' => '排序',
                'sort' => 19.1,
                'js_func' => 'woo_table_sort',
                'icon' => 'woo-icon-paixu',
                'class' => 'layui-btn-warm tool-disabled-show',
                'url' => (string) url('updateSort', ['parent_id' => $this->request->param('parent_id', null)])
            ]);
            $this->addToolBar([
                'name' => 'reset_table_sort',
                'title' => '重置排序',
                'sort' => 19,
                'js_func' => 'woo_table_resort',
                'icon' => 'woo-icon-zhongzhi',
                'class' => 'btn-3',
                'url' => (string) url('resetSort',['parent_id' => $this->request->param('parent_id', null)])
            ]);
        }
        if ($this->tab[$this->selectTab]['table']['toolbar']) {
            // 自定义头部工具按钮列表
            $this->tab[$this->selectTab]['table']['toolbarlist'] = $this->getToolBarList();
        }

        if (!empty($this->tab[$this->selectTab]['table']['data'])) {
            unset($this->tab[$this->selectTab]['table']['url']);
        }

        if (!isset($this->tab[$this->selectTab]['table']['page'])) {
            $this->tab[$this->selectTab]['table']['page'] = [
                'layout' => ['refresh', 'prev', 'page', 'next',  'skip', 'count',  'limit']
            ];
            if (!empty($this->tab[$this->selectTab]['table']['pageX'])) {
                $this->tab[$this->selectTab]['table']['page']['layout'] = ['refresh', 'prev', 'next', 'limit'];
            }
        }

        if (isset($this->tab[$this->selectTab]['table']['treetable'])) {
            $this->tab[$this->selectTab]['table']['page'] = false;
        }

        if (!isset($this->tab[$this->selectTab]['table']['limit'])) {
            $this->tab[$this->selectTab]['table']['limit'] = intval(setting('admin_page_limit'));
        }

        if (!isset($this->tab[$this->selectTab]['table']['loading'])) {
            $this->tab[$this->selectTab]['table']['loading'] = true;
        }
        if (!isset($this->tab[$this->selectTab]['table']['title'])) {
            $this->tab[$this->selectTab]['table']['title'] = $tab['title'];
        }
        if (!isset($this->tab[$this->selectTab]['table']['text']['none'])) {
            $this->tab[$this->selectTab]['table']['text']['none'] = setting('table_none_tip') ?: '<i class="layui-icon layui-icon-face-cry" style="font-size:20px;"></i>当前条件下，没有找到数据';
        }
        if (empty($this->tab[$this->selectTab]['table']['data']) && !isset($this->tab[$this->selectTab]['table']['autoSort'])) {
            $this->tab[$this->selectTab]['table']['autoSort'] = false;
        }

        if (!isset($this->tab[$this->selectTab]['table']['defaultToolbar'])) {
            $this->tab[$this->selectTab]['table']['defaultToolbar'] = ['filter', 'print', 'exports'];
        }
        if (!empty($this->tab[$this->selectTab]['table']['defaultToolbar']) && in_array('refresh', $this->tab[$this->selectTab]['table']['defaultToolbar']) && empty($this->tab[$this->selectTab]['table']['data'])) {
            $this->tab[$this->selectTab]['table']['defaultToolbar'][array_search('refresh', $this->tab[$this->selectTab]['table']['defaultToolbar'])] = [
                'title' => '刷新',
                'layEvent' => 'WOO_REFRESH',
                'icon' => 'layui-icon-refresh'
            ];
        }

        if (!empty($tab['total_row'])) {
            $this->tab[$this->selectTab]['table']['totalRow'] = true;
        }

        if (!isset($this->tab[$this->selectTab]['table']['limits'])) {
            $this->tab[$this->selectTab]['table']['limits'] = setting('table_default_limits')
                ? array_map('intval', explode('|', setting('table_default_limits')))
                :[10, 15, 20, 30, 40, 50, 100, 200, 500, 1000];
        }
        if (!in_array($this->tab[$this->selectTab]['table']['limit'], $this->tab[$this->selectTab]['table']['limits'])) {
            array_push($this->tab[$this->selectTab]['table']['limits'], $this->tab[$this->selectTab]['table']['limit']);
            sort($this->tab[$this->selectTab]['table']['limits']);
        }
        if (isset($this->tab[$this->selectTab]['filter_model'])) {
            $this->tab[$this->selectTab]['table']['filter_model'] = $this->tab[$this->selectTab]['filter_model'];
        }

        return $this->tab[$this->selectTab]['table'];
    }

    /**
     * 新增搜索字段
     * @param string $field
     * @param array $options
     * @return $this
     */
    public function addListFilter(string $field, array $options = [])
    {
        if (empty($this->tab[$this->selectTab]['list_filters'])) {
            $this->tab[$this->selectTab]['list_filters'] = [];
        }
        $this->tab[$this->selectTab]['list_filters'][$field] = $options;
        return $this;
    }

    /**
     * 获取搜索信息
     * @return array
     * @throws \think\Exception
     */
    protected function getFilterAttr()
    {
        $tab = $this->tab[$this->selectTab];
        $argsParams = $this->request->getParams()['args'];
        if (!empty($tab['table']['data']) || !empty($tab['table']['showTree'])) {
            return [];
        }
        $filter = $this->tab[$this->selectTab]['filter'] ?? [];
        $model = empty($tab['model']) ? $this->model : $tab['model'];
        if (is_string($model)) {
            $model = model($model);
        }

        if (!empty($filter)) {
            return $filter;
        }
        if (isset($tab['list_filters']) && false  === $tab['list_filters']) {
            return [];
        }

        if (empty($tab['list_filters'])) {
            if (!empty($model->form)) {
                foreach ($model->form as $field => $info) {
                    if (!empty($info['list_filter'])) {
                        if (is_bool($info['list_filter']) || $info['list_filter'] == 1) {
                            $tab['list_filters'][$field] = [];
                        } elseif (is_string($info['list_filter'])) {
                            $tab['list_filters'][$field] = [
                                'templet' => $info['list_filter']
                            ];
                        } elseif (is_array($info['list_filter'])) {
                            $tab['list_filters'][$field] = $info['list_filter'];
                        }
                    }
                }
            }
        }
        if (empty($tab['list_filters'])) {
            return [];
        }

        $tab['list_filters'] = Arr::normalize($tab['list_filters']);
        $args = $this->request->getParams()['args'];
        if (isset($args['parent_id']) && $parent_id = $model->getParentId()) {
            if (array_key_exists($parent_id, $tab['list_filters'])) {
                unset($tab['list_filters'][$parent_id]);
            }
        }
        $autoFilterCount = 0;
        foreach ($tab['list_filters'] as $field => $info) {
            $elem = '';
            $type = '';
            $merge = array_merge($model->form[$field] ?? [], (array) $info);
            if (!empty($merge['list_filter'])) {
                if (is_string($merge['list_filter'])) {
                    $info['templet'] = $merge['list_filter'];
                } elseif (is_array($merge['list_filter'])) {
                    $info = array_merge($info, $merge['list_filter']);
                }
            }
            $info['field'] = $field;
            if (empty($info['title'])) {
                $info['title'] = $merge['name'] ?? Str::studly($field);
            }


            // 显示模板
            if (!isset($info['templet']) && !empty($merge['elem'])) {
                switch ($merge['elem']) {
                    case 'date':
                        $info['templet'] = 'date_range';
                        break;
                    case 'year':
                        $elem = 'woo-element-year';
                        $info['templet'] = 'date';
                        break;
                    case 'month':
                        $elem = 'woo-element-month';
                        $info['templet'] = 'date';
                        break;
                    case 'time':
                        $elem = 'woo-element-time';
                        $info['templet'] = 'date';
                        break;
                    case 'datetime':
                        $info['templet'] = 'datetime_range';
                        break;
                    case 'checker':
                        $elem = 'woo-element-select';
                        $info['templet'] = 'select';
                        $info['options'][$merge['options']['no'] ?? 0] = '未' . $info['title'];
                        $info['options'][$merge['options']['yes'] ?? 1] = '已' . $info['title'];
                        break;
                    case 'relation':
                        if (!empty($merge['foreign'])) {
                            $elem = 'woo-element-relation';
                            $info['templet'] = 'relation';
                        }
                        break;
                }
            }

            if (!isset($info['templet']) && in_array($field, ['create_time', 'update_time', 'delete_time'])) {
                $info['templet'] = 'datetime_range';
            }
            if (
                isset($info['templet'])
                && in_array($info['templet'], ['date', 'year', 'month', 'time'])
                && empty($elem)) {
                $elem = 'woo-element-' . $info['templet'];
                $info['type'] = 'date';
                $info['templet'] = 'date';
            }

            if (!isset($info['type']) && (isset($merge['type']) && $merge['type']== 'join' && (!isset($merge['join']) ||  isset($merge['join']) && $merge['join'] == ','))) {
                $info['type'] = 'find_in_set';
            }

            if (!isset($info['type']) && (isset($merge['elem']) && in_array($merge['elem'], ['checkbox','array', 'keyvalue'], true) || isset($merge['type']) && $merge['type'] == 'array')) {
                $info['type'] = 'string';
            }

            if (isset($info['templet']) && $info['templet'] === 'relation') {
                $info['foreign'] = get_relation($merge['foreign'] ?? Str::studly(substr($field, 0 , -3)), $model);
            }

            if (isset($info['templet']) && $info['templet'] === 'cascader' && isset($merge['foreign'])) {
                $foreign = model(get_relation($merge['foreign'], $model)[0]);
                $info['attrs']['data-valuetype'] = 1;
                $info['attrs']['data-nostrict'] = 1;

                if (!isset($merge['optionsCallback']) || !is_callable($merge['optionsCallback'])) {
                    if (!isset($foreign->form['parent_id'])) {
                        $info['options'] = [];
                    }

                    if (empty($merge['attrs']['data-url'])) {
                        $tree = new Tree($foreign);
                        $info['options'] =  $tree->getCascaderOptions();
                    } else {
                        $info['attrs']['data-url'] = is_bool($merge['attrs']['data-url']) ? (string) url('getCascaderData') : $merge['attrs']['data-url'];
                        $info['options'][0] = ['parent_id' => 0, 'children' => []];
                        $list = $foreign->where('parent_id', '=', 0)->order($foreign->getDefaultOrder())->select()->toArray();
                        foreach ($list as $item) {
                            array_push($info['options'][0]['children'], [
                                'id' => $item[$foreign->getPk()],
                                'title' => $item[$foreign->display],
                                'is_children' => isset($item['children_count']) ?
                                    ($item['children_count'] ? true : false) :
                                    ($foreign->where('parent_id', '=', $item[$foreign->getPk()])->count() ? true : false)
                            ]);
                        }
                    }
                } else {
                    $info['options'] = $merge['optionsCallback']();
                }
                $info['options'] = json_encode($info['options'] ?? [], JSON_UNESCAPED_UNICODE);
            }
            if (isset($info['templet']) && $info['templet'] == 'select' && empty($info['options'])) {
                $info['options'] = $merge['options'] ?? [];
            }
            if (!isset($info['templet']) && isset($merge['options'])) {
                $elem = 'woo-element-select';
                $info['templet'] = 'select';
                $info['options'] = $merge['options'];
            }

            if (!isset($info['templet']) && !empty($merge['type'])) {
                switch ($merge['type']) {
                    case 'string':
                        $info['templet'] = 'string';
                        break;
                    case 'date':
                        $info['templet'] = 'date_range';
                        break;
                    case 'datetime':
                        $info['templet'] = 'datetime_range';
                        break;
                    case 'float':
                    case 'int':
                    case 'double':
                    case 'integer':
                        $info['templet'] = 'compare';
                        break;
                }
            }
            if (
                isset($info['templet'])
                &&
                in_array(
                    $info['templet'],
                    ['date_range', 'datetime_range', 'year_range', 'time_range', 'month_range']
                )
            ) {
                $range = substr($info['templet'], 0, -6);
                $info['type'] = $info['templet'];
                $info['templet'] = 'date';
                $elem = 'woo-element-' . $range;
                $info['attrs']['data-range'] = '~';
            }

            if (!isset($info['templet'])) {
                $elem = 'woo-element-text';
                $info['templet'] = 'text';
            }
            $info['templet'] = Str::camel($info['templet']);

            if (!empty($elem)) {
                $info['attrs']['class'] = ($info['attrs']['class'] ?? '') . ' ' . $elem;
            }
            if (!isset($info['attrs'])) {
                $info['attrs'] = [];
            }
            if (!isset($info['hide'])) {
                $info['hide'] = false;
            }

            if (!isset($info['type']) && !empty($tab['local']['is_query'])) {
                if (!empty($type)) {
                    $info['type'] = $type;
                }
                if (!isset($info['type'])) {
                    $type_map = [
                        'date' => 'date',
                        'string' => 'string',
                        'compare' => 'compare',
                        'select' => 'eq',
                        'number' => 'eq',
                        'numberRange' => 'number_range',
                        'cascader' => 'find_in_set'
                    ];
                    if (array_key_exists($info['templet'], $type_map)) {
                        $info['type'] = $type_map[$info['templet']];
                    } else {
                        $info['type'] = 'eq';
                    }
                }
            }
            if (array_key_exists($field, $argsParams)) {
                $info['attrs']['value'] = $argsParams[$field];
                $autoFilterCount++;
            }
            $filter[$field] = $info;
        }
        if ($autoFilterCount && empty($this->tab[$this->selectTab]['filter_model'])) {
            $this->tab[$this->selectTab]['filter_model'] = 'show';
        }
        $this->tab[$this->selectTab]['filter'] = $filter;
        return $filter;
    }

    protected function parseQuery(array $existsWhere = [])
    {
        $tab = $this->tab[$this->selectTab];
        $filter = $this->tab[$this->selectTab]['filter'] ?? [];
        $model = empty($tab['model']) ? $this->model : $tab['model'];
        if (is_string($model)) {
            $model = model($model);
        }
        $args = $this->request->getParams()['args'];
        $pk = $model->getPk();
        //pr($filter);
        //pr($args);
        $tableFields = $model->getTableFields();
        $this->setLocal('where', []);
        $this->setLocal('whereTime', []);

        if (isset($args['parent_id']) && $parent_id = $model->getParentId()) {
            if (($parent_id != 'parent_id' && $args['parent_id']) || $parent_id == 'parent_id') {
                $this->setLocal('where', [$parent_id, '=', intval($args['parent_id'])], true);
            }
        }
        foreach ($filter as $field => $info) {
            if (empty($args[$field]) && !(isset($args[$field]) && $args[$field] === '0')) {
                continue;
            }
            $val = is_string($args[$field]) ? urldecode(trim($args[$field])) : $args[$field];
            if (in_array($field, $existsWhere)) {
                continue;
            }

            if (isset($info['where'])) {
                if (is_callable($info['where'])) {
                    $where_result = call_user_func_array($info['where'], [$val, $args, $field, $info]);
                    if (!empty($where_result)) {
                        $this->setLocal('where', (array)$where_result, true);
                    }
                    continue;
                } elseif (is_string($info['where']) && method_exists($model, $info['where'])) {
                    $where_result = call_user_func_array([$model, $info['where']], [$val, $args, $field, $info]);
                    if (!empty($where_result)) {
                        $this->setLocal('where', (array)$where_result, true);
                    }
                    continue;
                }
            }

            if ($info['templet'] === 'relation') {
                // 多对多的搜索
                if (isset($info['foreign']) && $info['foreign']['type'] == 'belongsToMany') {
                    try {
                        $ids = model($info['foreign']['middle'])->where($info['foreign']['foreignKey'], intval($val))->column($info['foreign']['localKey']);
                        $this->setLocal('where', [$pk, 'IN', $ids], true);
                    } catch (\Exception $e) {
                        throw new \Exception('belongsToMany搜索错误：' . $e->getMessage());
                    }
                    continue;
                } elseif (isset($info['foreign']) && $info['foreign']['type'] == 'belongsToThrough') {
                    try {
                        $foreignKey = $info['foreign']['foreignKey'] ?? (Str::snake($info['foreign'][0]) . '_id');
                        $throughKey = $info['foreign']['throughKey'] ?? (Str::snake($info['foreign']['through']) . '_id');
                        $ids = model($info['foreign']['through'])
                            ->where($foreignKey, '=', intval($val))
                            ->column(model($info['foreign']['through'])->getPk());
                        $this->setLocal('where', [$throughKey, count($ids) == 1 ? '=' :'IN',  count($ids) == 1 ? $ids[0] : $ids], true);
                    }  catch (\Exception $e) {
                        throw new \Exception('belongsToThrough搜索错误：' . $e->getMessage());
                    }
                    continue;
                } elseif (isset($info['foreign']) && $info['foreign']['type'] == 'hasOneThrough') {
                    try {
                        $throughKey = $info['foreign']['throughKey'] ?? (Str::snake($info['foreign']['through']) . '_id');
                        $foreign_id = model($info['foreign']['foreign'])->where(model($info['foreign']['foreign'])->getPk(), '=', intval($val))->value($throughKey);
                        $foreignKey = $info['foreign']['foreignKey'] ?? Str::snake(get_base_class($model)) . '_id';
                        $througn_id = model($info['foreign']['through'])->where(model($info['foreign']['through'])->getPk(), '=', $foreign_id)->value($foreignKey);
                        $this->setLocal('where', [$pk, '=', $througn_id], true);
                    }  catch (\Exception $e) {
                        throw new \Exception('hasOneThrough搜索错误：' . $e->getMessage());
                    }
                    continue;
                }
            }

            if (!in_array($field, $tableFields)) {
                continue;
            }
            if ($info['type'] == 'eq') {
                $this->setLocal('where', [$field, '=', $val], true);
            } elseif ($info['type'] == 'date') {
                $this->setLocal('where', [$field, '=', $val], true);
            } elseif ($info['type'] == 'string') {
                $like_map = [
                    'left' => '%VALUE',
                    'right' => 'VALUE%',
                    'like' => '%VALUE%',
                    'eq' => 'VALUE'
                ];
                $like_sign = trim($args[$field . '_' . 'like'] ?? 'like');
                if (!array_key_exists($like_sign, $like_map)) {
                    $like_sign = 'like';
                }
                $this->setLocal('where', [$field, $like_sign != 'eq' ? 'LIKE' : '=', str_replace('VALUE', $val, $like_map[$like_sign])], true);
            } elseif ($info['type'] == 'compare') {
                $sign_map = [
                    'eq' => '=',
                    'gt' => '>',
                    'lt' => '<',
                    'neq' => '<>',
                ];
                $sign = trim($args[$field . '_' . 'sign'] ?? 'eq');
                if (!array_key_exists($sign, $sign_map)) {
                    $sign = 'eq';
                }
                $this->setLocal('where', [$field, $sign_map[$sign], $val], true);
            } elseif ($info['type'] == 'number_range') {
                $min = $val['min'] ?? '';
                if (is_numeric($min)) {
                    $this->setLocal('where', [$field, '>=', (int)$min], true);
                }
                $max = $val['max'] ?? '';
                if (is_numeric($max)) {
                    if (is_numeric($min) && $max < $min) {
                        $max = $min;
                    }
                    $this->setLocal('where', [$field, '<=', (int)$max], true);
                }
            } elseif (strpos($info['type'], '_range') > 0) {
                $range = explode('~', $val);
                $rf = trim($range[0] ?? '');
                $rt = trim($range[1] ?? '');

                if ($info['type'] == 'date_range' && $rt) {
                    $rt = date('Y-m-d', strtotime($rt) + 86400);
                } elseif ($info['type'] == 'datetime_range'  && $rt) {
                    $rt = date('Y-m-d H:i:s', strtotime($rt) + 1);
                } elseif ($info['type'] == 'time_range' && $rt) {
                    $rt = date('H:i:s', strtotime($rt) + 1);
                } elseif ($info['type'] == 'year_range') {
                    if ($rf) {
                        $rf = date('Y-m-d', strtotime($rf . "-01-01"));
                    }
                    if ($rt) {
                        $rt = date('Y-m-d H:i:s', strtotime($rt + 1 . "-01-01"));
                    }
                } elseif ($info['type'] == 'month_range') {
                    if ($rf) {
                        $rf = date('Y-m-d', strtotime($rf . "-01"));
                    }
                    if ($rt) {
                        $rt = date('Y-m-d H:i:s', strtotime($rt . "-"  . date('t', strtotime($rt))) + 86400);
                    }
                }
                if ($rf) {
                    $this->setLocal('whereTime', [$field, '>=', $rf], true);
                }
                if ($rt) {
                    $this->setLocal('whereTime', [$field, '<', $rt], true);
                }
            } elseif ($info['type'] == 'find_in_set') {
                $this->setLocal('where', [$field, 'FIND IN SET', $val], true);
            }
        }
        if (isset($args['sort_field'])) {
            $sort_field = trim($args['sort_field']);
            if (in_array($sort_field, $tableFields)) {
                $sort_type = trim(strtolower($args['sort_type'] ?? ''));
                if ($sort_type && in_array($sort_type, ['asc', 'desc'])) {
                    $this->setLocal('order', [$sort_field => $sort_type]);
                }
            }
        }
    }

    public function getSiderbar()
    {
        $tab = $this->tab[$this->selectTab];
        $model = empty($tab['model']) ? $this->model : $tab['model'];
        if (is_string($model)) {
            $model = model($model);
        }
        $pk = $model->getPk();
        $args = $this->request->getParams()['args'];
        $siderbars = $tab['siderbar'] ?? [];
        if (empty($siderbars)) {
            return null;
        }
        if (is_string($siderbars)) {
            $siderbars = [
                'foreign' => $siderbars
            ];
        }
        if (isset($siderbars['foreign'])) {
            $siderbars[] = $siderbars;
        }
        $return = [];
        foreach ($siderbars as $siderbar) {
            if (!isset($siderbar['foreign']) || !is_array($siderbar)) {
                continue;
            }
            if (!isset($model->relationLink[$siderbar['foreign']])) {
                continue;
            }
            $siderbar['foreignKey'] = $model->relationLink[$siderbar['foreign']]['foreignKey'] ?? Str::snake($siderbar['foreign']) . '_id';

            if (!empty($tab['filter']) && array_key_exists($siderbar['foreignKey'], $tab['filter'])) {
                continue;
                //throw new \Exception('侧边栏和搜索字段' . $siderbar['foreignKey'] . '冲突，请自行取消一方');
            }

            if (!empty($tab['local']['is_query'])) {
                if (!empty($args[$siderbar['foreignKey']]) && isset($model->form[$siderbar['foreignKey']])) {
                    $vars = explode(',', trim($args[$siderbar['foreignKey']]));
                    $info = $model->relationLink[$siderbar['foreign']];
                    if (in_array($info['type'], ['belongsTo'])) {
                        if (count($vars) > 1) {
                            $this->setLocal('where', [$siderbar['foreignKey'], 'IN', $vars], true);
                        } elseif (count($vars) == 1) {
                            $this->setLocal('where', [$siderbar['foreignKey'], '=', $vars[0]], true);
                        }
                    } elseif (in_array($info['type'], ['belongsToMany'])) {
                        try {
                            $ids = model($info['middle'])->where($info['foreignKey'], 'IN', $vars)->column($info['localKey']);
                            $this->setLocal('where', [$pk, 'IN', $ids], true);
                        } catch (\Exception $e) {
                            throw new \Exception('多对多侧边栏搜索错误：' . $e->getMessage());
                        }
                    } elseif (in_array($info['type'], ['belongsToThrough'])) {
                        try {
                            $foreignKey = $info['foreignKey'] ?? (Str::snake($info['key']) . '_id');
                            $throughKey = $info['throughKey'] ?? (Str::snake($info['through']) . '_id');
                            $ids = model($info['through'])
                                ->where($foreignKey, count($vars) == 1 ? '=': 'IN', count($vars) == 1 ? $vars[0]: $vars)
                                ->column(model($info['through'])->getPk());
                            $this->setLocal('where', [$throughKey, 'IN', $ids], true);
                        }  catch (\Exception $e) {
                            throw new \Exception('多对多搜索错误：' . $e->getMessage());
                        }
                    }
                }
                continue;
            }
            try {
                $foreign = model($model->relationLink[$siderbar['foreign']]['foreign']);
            } catch (\Exception $e) {
                throw  new \Exception($e->getMessage());
            }
            $result = $siderbar;
            $result['foreign'] = $siderbar['foreign'];
            $result['title'] =  $siderbar['title'] ?? $foreign->cname;
            $result['elem'] = '#' . $this->selectTab . $result['foreign'] . 'Siderbar';
            $result['id'] = $this->selectTab . $result['foreign'] . 'Siderbar';
            $result['showCheckbox'] = $siderbar['showCheckbox'] ?? true;
            $result['accordion'] = $siderbar['accordion'] ?? false;
            $result['onlyIconControl'] = $siderbar['onlyIconControl'] ?? false;
            $result['isJump'] = $siderbar['isJump'] ?? false;
            $result['showLine'] = $siderbar['showLine'] ?? true;

            if (!isset($siderbar['data'])) {
                if (isset($foreign->form['parent_id'])) {
                    $tree =  new Tree(model($foreign));
                    $result['data'] = $tree->getLayuiTree();
                    $result['children_count'] = [];
                    foreach ($tree->get('children') as $p => $c) {
                        $result['children_count'][$p] = count($c);
                    }
                } else {
                    $list = $foreign->
                    field([$foreign->getPk(), $foreign->display])
                        ->where($siderbar['where'] ?? [])
                        ->order($foreign->getDefaultOrder())
                        ->select()
                        ->toArray();
                    $data = [];
                    foreach ($list as $item) {
                        $data[] = [
                            'id' => $item[$foreign->getPk()],
                            'title' => $item[$foreign->display]
                        ];
                    }
                    $result['data'] = $data;
                }
            } else {
                $result['data'] = $siderbar['data'];
            }
            $return[$result['foreign']] = $result;
        }
        return $return;
    }

    /**
     * 添加一个列表统计
     * @param array $options
     * @return $this
     */
    public function addListCounter(array $options = [])
    {
        if (empty($this->tab[$this->selectTab]['counter'])) {
            $this->tab[$this->selectTab]['counter'] = [];
        }
        array_push($this->tab[$this->selectTab]['counter'], $options);
        return $this;
    }

    public function getTabAttr(string $tabName = '')
    {
        if (empty($tabName)) {
            $tabName = $this->tab;
        } else {
            $tabName = [$tabName => []];
        }
        try {
            foreach ($tabName as $tab  => $info) {
                $this->switchTab($tab);
                $arrts[$tab] = [
                    'filter' => $this->getFilterAttr(),
                    'siderbar' => $this->getSiderbar(),
                    'counter' => $this->getCounterAttr(),
                    'table' => $this->getTableAttr(),
                ];
            }
            return $arrts;
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 自动识别当前tab
     * @param string $tabName
     * @return $this
     */
    protected function autoSwitchTab(string $tabName = '')
    {
        $params = $this->request->getParams();
        if (empty($tabName) || !array_key_exists($tabName, $this->tab)) {
            if (isset($params['args']['tabname'])) {
                $this->switchTab($params['args']['tabname']);
            }
        } else {
            $this->switchTab($tabName);
        }
        return $this;
    }

    /**
     * 获取基础统计信息
     * @return array
     */
    protected function getCounterAttr()
    {
        $counter = $this->tab[$this->selectTab]['counter'] ?? [];
        if (empty($counter)) {
            return [];
        }
        foreach ($counter as &$item) {
            if (isset($item['more']) && is_array($item['more'])) {
                $item = array_merge($item['more'], $item);
            }
            if (isset($item['more'])) {
                unset($item['more']);
            }
        }
        return $counter;
    }

    // 获取统计数据
    protected function getCounterData($listWhere = [], $options = [])
    {
        $counter = $this->getCounterAttr();
        if (!$counter) {
            return null;
        }

        $tab = $this->tab[$this->selectTab];
        $model = empty($tab['model']) ? $this->model : $tab['model'];
        if (is_string($model)) {
            $model = model($model);
        }
        $data = [];
        foreach ($counter as $key => $item) {
            if (empty($item['field']) && ($item['where_type'] ?? '') != 'callback') {
                continue;
            }

            // 当前列表条件
            $filter = $listWhere;
            // 自定义条件
            $where = isset($item['where']) && is_array($item['where']) ? $item['where'] : [];

            if (isset($item['where_type']) && $item['where_type'] == 'callback') {
                if (empty($item['title'])) {
                    $item['title'] = isset($model->form[$item['field']]) ? $model->form[$item['field']]['name'] : Str::studly($item['field']);
                }
                if (empty($item['callback'])) {
                    continue;
                }
                try {
                    if (is_callable($item['callback'])) {
                        $item['value'] = call_user_func_array($item['callback'], [$where, $filter, $item, $item['field']]);
                    } elseif (is_string($item['callback']) && method_exists($model, $item['callback'])) {
                        $item['value'] = call_user_func_array([$model, $item['callback']], [$where, $filter, $item, $item['field']]);
                    } else{
                        continue;
                    }
                    $item['is_error'] = false;
                } catch (\Exception $e) {
                    $item['value'] = $e->getMessage();
                    $item['is_error'] = true;
                }
            } else {
                if (!isset($model->form[$item['field']])) {
                    continue;
                }
                $type = !empty($item['type']) ? strtolower($item['type']) : 'count';
                if (!in_array($type, ['min', 'max', 'count', 'avg', 'sum'])) {
                    $type = 'count';
                }
                if (empty($item['title'])) {
                    $type_map = ['min' => '最小值', 'max' => '最大值', 'count' => '计数', 'avg' => '平均值', 'sum' => '合计'];
                    $item['title'] = $model->form[$item['field']]['name'] . $type_map[$type];
                }
                $item['where_type'] = !empty($item['where_type']) ? $item['where_type'] : 'none';
                if ($item['where_type'] == 'none') {
                    $filter =  $where = [];
                } else if($item['where_type'] == 'auto') {
                    $where = [];
                } else if($item['where_type'] == 'where') {
                    $filter = [];
                } else {
                    $filter =  $where = [];
                }

                try {
                    $item['value'] = $model
                        ->where($model->getCheckAdminWhere($options['cancelCheckAdmin'] ?? false))
                        ->where($where)
                        ->where($filter['where'] ?? [])
                        ->whereOr($filter['whereOr'] ?? [])
                        ->$type($item['field']);
                    if (strpos((string) $item['value'], '.') !== false) {
                        // 精确2位小数
                        $item['value'] = number_format($item['value'], 2);
                    }
                    $item['is_error'] = false;
                } catch (\Exception $e) {
                    $item['value'] = $e->getMessage();
                    $item['is_error'] = true;
                }
            }

            if (empty($item['templet'])) {
                $item['templet'] = 'defaultCounterItem';
                $item['grid'] = 'layui-col-lg3 layui-col-md4 layui-col-sm6';
            }
            if ($item['templet'][0] == '#') {
                $item['templet'] = substr($item['templet'], 1);
            }
            if (empty($item['grid'])) {
                $item['grid'] = 'layui-col-lg3 layui-col-md4 layui-col-sm6';
            }
            $unset_options = ['callback', 'where', 'where_type'];
            foreach ($unset_options as $option) {
                if (isset($item[$option])) {
                    unset($item[$option]);
                }
            }
            $data[] = $item;
        }
        return $data;
    }

    // 获取表格列统计数据
    protected function getTotalRowData($list = [])
    {
        $tab = $this->tab[$this->selectTab];
        if (empty($tab['total_row']) || empty($list)) {
            return [];
        }
        $model = empty($tab['model']) ? $this->model : $tab['model'];
        if (is_string($model)) {
            $model = model($model);
        }

        $total_rows = Arr::combine($tab['total_row'], 'field');
        $data = [];

        foreach ($total_rows as $field => $info) {
            if (empty($info['total_row']) || $info['total_row'] == 'default' || $info['total_row'] == 'none' || empty($list[0][$field])) {
                continue;
            }
            try {
                $row = Arr::fieldList($field, $list);
            } catch (\Exception $e) {
                $data[$field] = $e->getMessage();
                continue;
            }

            switch ($info['total_row']) {
                case 'sum':
                    $data[$field] = round(array_sum($row),2);
                    break;
                case 'count':
                    $data[$field] = count($row);
                    break;
                case 'avg':
                    if (count($row)) {
                        $data[$field] = round(array_sum($row) / count($row), 2);
                    } else {
                        $data[$field] = 0;
                    }
                    break;
                case 'max':
                    $data[$field] = max($row);
                    break;
                case 'min':
                    $data[$field] = min($row);
                    break;
                case 'callback':
                    if ($info['callback'] && method_exists($model, $info['callback'])) {
                        try {
                            $data[$field] = call_user_func_array([$model, $info['callback']], [$row, $info, $list]);
                        } catch (\Exception $e) {
                            $data[$field] = $e->getMessage();
                        }
                    } else {
                        $data[$field] = ' ';
                    }
                    break;
            }
        }
        return $data;

    }

    public function setAutoCheckedIds($value)
    {
        $values = is_array($value) ? $value : (is_json($value) ? is_json($value) : explode(',', (string) $value));
        $values = array_diff($values, ['']);
        $this->autoCheckedIds = $values;
    }

    public function getData(array $options = [], string $tabName = '')
    {
        $this->autoSwitchTab($tabName);
        try {
            $tab = $this->tab[$this->selectTab];
            $table = $tab['table'] ?? [];
            $model = empty($tab['model']) ? $this->model : $tab['model'];
            if (is_string($model)) {
                $model = model($model);
            }
            if (setting('table_is_cache') || (isset($options['forceCache']) && false !== $options['forceCache'])) {
                $cache_key = $this->request->url() . "_" . get_base_class($model) . "_" . (new Auth())->user('id') . "_" . md5(serialize($options));
                if (Cache::has($cache_key)) {
                    $return_data = Cache::get($cache_key);
                    $return_data['autoCheckedIds'] = $this->autoCheckedIds ?:null;
                    return $return_data;
                }
            }
            $this->setLocal('is_query', true);
            $this->getTableColsAttr();
            $this->getFilterAttr();
            $this->parseQuery($options['existsWhere'] ?? []);
            $this->getSiderbar();
            $tab = $this->tab[$this->selectTab];
            if (isset($tab['query_options'])) {
                foreach ($tab['query_options'] as $key => $value) {
                    if (!isset($options[$key]) || !is_array($value)) {
                        $options[$key] = $value;
                        continue;
                    }
                    $options[$key] = array_merge($value, $options[$key]);
                }
            }

            if (empty($tab['table']['treetable'])) {
                $limit = $this->request->getParams()['args']['limit'] ?? ($table['limit'] ?? 10);
            } else {
                $limit = 10000;
            }

            $with = array_merge($tab['local']['with'] ?? [], $options['with'] ?? []);
            $fields = array_merge($tab['local']['field'] ?? [], $options['field'] ?? []);

            if (!empty($with) && !empty($fields)) {
                $withAssoc = Arr::normalize($with);
                foreach ($withAssoc as $key => $info) {
                    if (array_key_exists($key, $model->relationLink) && $model->relationLink[$key]['type'] == 'belongsTo') {
                        $foreignKey = $model->relationLink[$key]['foreignKey'] ?? Str::snake($key) . '_id';
                        if (!in_array($foreignKey, $fields) && array_key_exists($foreignKey, $model->form)) {
                            $fields[] = $foreignKey;
                        }
                    }
                }
            }

            $pageMethod = empty($table['pageX']) ? 'getPage' : 'getPageX';
            $result = $model->$pageMethod([
                'withTrashed' => $tab['local']['withTrashed'] ?? ($options['withTrashed'] ?? false),
                'onlyTrashed' => $tab['local']['onlyTrashed'] ?? ($options['onlyTrashed'] ?? false),
                'with' => $with,
                // 'withJoin' => array_merge($tab['local']['withJoin'] ?? [], $options['withJoin'] ?? []),
                'where' => array_merge($tab['local']['where'] ?? [], $options['where'] ?? []),
                'whereOr' => array_merge($tab['local']['whereOr'] ?? [], $options['whereOr'] ?? []),
                'whereColumn' => array_merge($tab['local']['whereColumn'] ?? [], $options['whereColumn'] ?? []),
                'whereTime' => array_merge($tab['local']['whereTime'] ?? [], $options['whereTime'] ?? []),
                'whereBetweenTime' => array_merge($tab['local']['whereBetweenTime'] ?? [], $options['whereBetweenTime'] ?? []),
                'whereNotBetweenTime' => array_merge($tab['local']['whereNotBetweenTime'] ?? [], $options['whereNotBetweenTime'] ?? []),
                'whereYear' => array_merge($tab['local']['whereYear'] ?? [], $options['whereYear'] ?? []),
                'whereMonth' => array_merge($tab['local']['whereMonth'] ?? [], $options['whereMonth'] ?? []),
                'whereWeek' => array_merge($tab['local']['whereWeek'] ?? [], $options['whereWeek'] ?? []),
                'whereDay' => array_merge($tab['local']['whereDay'] ?? [], $options['whereDay'] ?? []),
                'whereBetweenTimeField' => array_merge($tab['local']['whereBetweenTimeField'] ?? [], $options['whereBetweenTimeField'] ?? []),
                'field' => $fields,
                'order' => array_merge($tab['local']['order'] ?? [], $options['order'] ?? []),
                'group' => array_merge($tab['local']['group'] ?? [], $options['group'] ?? []),
                'having' => $options['having'] ?? '',
                'cancelCheckAdmin' => $options['cancelCheckAdmin'] ?? false,
                'whereCallback' => $options['whereCallback'] ?? false,
                'whereRaw' => $options['whereRaw'] ?? [],
                'limit' => intval($limit)
            ]);
            if ($result) {
                // 2021-08-09 优化无限级showTree方式
                if (!empty($tab['table']['showTree']) && empty($tab['table']['treetable'])) {
                    $tree = new Tree($model);
                    $result['list'] = $tree->getListData($result['list']);
                }

                if (!empty($table['pageX'])) {
                    $result['page']['total'] = $result['page']['per_page'] * ($result['page']['current_page'] + 2);
                }

                $return_data = [
                    'result' => 'success',
                    'code' => 0,
                    'msg' => '',
                    'page' => $result['page'],
                    'count' => $result['page']['total'],
                    'data' => $result['list'],
                    'isSort' => $args['sort_field'] ?? false,
                    'counter' => $this->getCounterData([
                        'where' => array_merge($tab['local']['where'] ?? [], $options['where'] ?? []),
                        'whereOr' => array_merge($tab['local']['whereOr'] ?? [], $options['whereOr'] ?? [])
                    ], ['cancelCheckAdmin' => $options['cancelCheckAdmin'] ?? false]),
                    'totalRow' => $this->getTotalRowData($result['list'])
                ];

                if (!empty($table['pageX'])) {
                    if (empty($return_data['data']) || count($return_data['data']) < $result['page']['per_page']) {
                        $result['page']['total'] = $return_data['count'] = $result['page']['per_page'] * ($result['page']['current_page'] - 1);
                    }
                }

                if (setting('table_is_cache') && !isset($options['forceCache'])) {
                    $tags = [get_base_class($model), 'AdminGroup'];
                    if (!empty($tab['local']['with'])) {
                        $tab['local']['with'] = Arr::normalize($tab['local']['with']);
                        $tags = array_merge($tags, array_keys($tab['local']['with']));
                    }
                    Cache::tag(model_cache_tag(count($tags) == 1 ? $tags[0] : $tags))->set($cache_key, $return_data, 3600);
                } elseif (isset($options['forceCache']) && false !== $options['forceCache']) {
                    Cache::set($cache_key, $return_data, intval($options['forceCache']));
                }
                $return_data['autoCheckedIds'] = $this->autoCheckedIds ?:null;
                return $return_data;
            } else {
                $this->forceError($model->getError());
            }
        }  catch (\Exception $e) {
            $this->forceError($e->getMessage());
        }
        return [
            'result' => 'error',
            'message' => count($this->getError()) ? array_values($this->getError()) : '获取失败'
        ];
    }


    /**
     * 获取Tab基础信息
     * @return array
     */
    public function getTabHeader()
    {
        $header = [];
        $headerAttr = ['name', 'title', 'url', 'icon','refresh','custom'];

        foreach ($this->tab as $tab) {
            $row = [];
            foreach ($headerAttr as $attr) {
                $row[$attr] = $tab[$attr] ?? '';
            }
            array_push($header, $row);
        }
        return $header;
    }

    protected function autoAddTab(array $tabs = [])
    {
        if (!empty($this->tab)) {
            return $this;
        }
        if (empty($tabs) && $this->model && isset($this->model->tableTab)) {
            $tabs = $this->model->tableTab;
        }
        if (empty($tabs)) {
            $this->addTab('basic', __('Basic'));
            return $this;
        }
        foreach ($tabs as $key => $value) {
            $this->addTab($key, '', $value);
        }
        return $this;
    }

    /**
     * 切换正在操作的Tab
     * @param string $tabName
     * @return $this
     */
    public function switchTab(string $tabName)
    {
        if (!array_key_exists($tabName, $this->tab)) {
            $this->addTab($tabName, $tabName);
        }
        $this->selectTab = $tabName;
        return $this;
    }

    /**
     * 加设错误信息
     * @param $field
     * @param string $error
     * @return $this
     */
    public function forceError($field, $error = '')
    {
        if (is_string($field)) {
            if (!empty($error)) {
                $this->error[$field] = $error;
            } else {
                $this->error[] = $field;
            }
        } elseif (is_array($field)) {
            $this->error = array_merge($this->error, $field);
        }
        return $this;
    }

    /**
     * 获取错误信息
     * @param string $field
     * @return array|mixed|string
     */
    public function getError(string $field = '')
    {
        if ($field) {
            return $this->error[$field] ?? '';
        }
        return $this->error;

    }


    public function fetch($template, $data = [], $trim = true)
    {
        ob_start();
        $assign = get_object_vars($this->view);
        $this->template->fetch($template, array_merge((array)$assign, ['data' => $data, 'values' => $this->data]));
        $content = ob_get_contents();
        if ($trim) {
            $content = preg_replace_callback("/<[^<]*?>/is", function ($matched) {
                return preg_replace("/\s*" . PHP_EOL . "\s*/is", " ", $matched[0]);
            }, $content);
        }
        ob_end_clean();
        return $content;
    }

    public function getLayout(string $layout = '')
    {
        if (empty($layout)) {
            $layout = 'table';
        }
        $origin = $layout;

        if (!is_file($layout)) {
            $layout = app()->getAppPath() . 'view' . DIRECTORY_SEPARATOR . 'table' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . $origin . '.html';
        }

        if (!is_file($layout)) {
            $layout = app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR .  'table' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . $origin . '.html';
        }

        $default = woo_path() . 'common' . DIRECTORY_SEPARATOR . 'builder' . DIRECTORY_SEPARATOR . 'table' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . $origin . '.html';
        if (!is_file($layout) && is_file($default)) {
            $layout = $default;
        }
        wooview()['common_templet_file'] =  Config::get('woo.custom_templet_file');
        wooview()['woo_templet_file'] = woo_path() . 'common/builder/table/templet/default.html';
        wooview()['woo_filter_templet_file'] = woo_path() . 'common/builder/table/templet/filter.html';
        wooview()['woo_tools_templet_file'] = woo_path() . 'common/builder/table/templet/tools.html';
        return $this->fetch($layout, $this, false);
    }

    public function __call($name, $args)
    {
        if (substr($name, 0, 3) == 'set' && substr($name, -4) == 'Attr') {
            $name = Str::snake(substr($name, 3, -4));
            $this->setAttr($name, ...$args);
            return $this;
        } elseif (substr($name, 0, 8) == 'setTable' && substr($name, -4) == 'Attr') {
            $name = Str::snake(substr($name, 8, -4));
            $this->setTableAttr($name, ...$args);
            return $this;
        }
    }

    public function __toString()
    {
        return $this->getLayout();
    }

    public function __invoke(string $layout = '')
    {
        return $this->getLayout($layout);
    }
}