<?php
declare (strict_types=1);

namespace woo\common\builder;

use woo\common\helper\Arr;

class FormPage extends \app\common\builder\Form
{
    /**
     * Tab 默认一定会有一个叫【basic => 基本信息】的Tab  如果最终只有一个Tab不会显示tab-title
     */
    protected $tab = [
        'basic' => [
            'title' => '基本信息',
            'icon' => '',
            'grids' => []
        ]
    ];
    /**
     * 记录grid和tab对应关系
     */
    protected $gridToTab = [];

    /**
     * 当前正在操作的Tab标识
     */
    protected $selectTab = 'basic';

    protected $isTabParse = false;

    protected $labelClass = 'layui-form-label';

    /**
     * 表单相关信息  可以在builder/form/layout/form_page.html下查看每个信息使用的地方
     * @var array
     */
    protected $formInfo = [
        'action' => '',
        'method' => 'POST',
        'enctype' => 'multipart/form-data',
        'class' => '',
        'submit_button_fixed' => true,
        'lay_filter' => 'wooForm'
    ];
    protected $config = [
        'require_star' => true,// 是否输出必填的 * 号
        'star' => '*', // 必填的 * 号
    ];

    /**
     * 记录触发器信息
     * @var array
     */
    protected $trigger = [];
    /**
     * 被触发的隐藏项目的字段名
     * @var array
     */
    protected $triggerFields = [];

    protected $storeGridFieldList = [];


    public function __construct(array $data = [], $model = null, $parentField = null)
    {
        // 默认一定会有一个叫【basic => 基本信息】的Tab  如果最终只有一个Tab不会显示tab-title
        $this->tab['basic']['title'] = __('Basic');
        parent::__construct($data, $model, $parentField);
    }

    /**
     * 设置formInfo相关属性信息
     * @param $key
     * @param $value
     * @return $this
     */
    public function setFormInfo($key, $value)
    {
        if (!array_key_exists($key, $this->formInfo)) {
            return $this;
        }
        $this->formInfo[$key] = $value;
        return $this;
    }

    /**
     * 获取formInfo相关属性信息  如果为空 将获取全部
     * @param string $key
     * @return array|mixed|string
     */
    public function getFormInfo($key = '')
    {
        if (!empty($key) && !array_key_exists($key, $this->formInfo)) {
            return '';
        }
        if ($key) {
            return $this->formInfo[$key];
        }
        return $this->formInfo;
    }
    /**
     * 添加、修改一个Tab
     * @param string $tabName  Tab标识 自定义  一般为字母
     * @param string $title Tab名称  页面上最终显示的名称
     * @return $this
     */
    public function setTab(string $tabName, string $title, $icon = '')
    {
        $grids = isset($this->tab[$tabName]['grids']) ? $this->tab[$tabName]['grids'] : [];
        $this->tab[$tabName] = [
            'title' => $title,
            'icon' => $icon,
            'grids' => $grids
        ];
        $this->switchTab($tabName);
        return $this;
    }

    /**
     * 从模型中自动创建Tab
     * @param array $groups
     * @return $this
     */
    public function setFormTab(array $groups = [])
    {
        if ($this->getTabNumber() > 1) {
            return $this;
        }
        if (empty($groups) && $this->model) {
            $groups = $this->model->formGroup;
        }
        if (!empty($groups)) {
            foreach ($groups as $name => $title) {
                $this->setTab($name, $title);
            }
        }
        return $this;
    }

    /**
     * 从模型中自动分组 -- 如果是从模型form属性自动创建的表单项目 才会自动分组
     * @return $this
     */
    public function setFormGrid()
    {
        if (!empty($this->groupFields)) {
            foreach ($this->groupFields as $group => $fields) {
                $fields = array_diff($fields, $this->storeGridFieldList);
                if ($fields) {
                    $this->switchTab($group)->setTabItems($fields);
                }
            }
        }
        return $this;
    }

    /**
     * 从模型中自动创建表单项目触发器
     * @return $this
     */
    public function setFormTrigger(array $trigger = [])
    {
        if (count($this->getTrigger()) > 0) {
            return $this;
        }
        if (empty($trigger) && $this->model) {
            $trigger = $this->model->formTrigger;
        }
        if (!empty($trigger)) {
            foreach ($trigger as $name => $vlaue) {
                $this->addTrigger($name, $vlaue);
            }
        }
        return $this;
    }


    /**
     * 获取最终Tab信息
     * @return array
     * @throws \think\Exception
     */
    public function getTabs()
    {
        if ($this->isTabParse) {
            return $this->tab;
        }
        $this->setFormTab();
        $this->parseItems();
        $this->setFormGrid();
        $this->setFormTrigger();
        $items = $this->collection->keys()->toArray();
        $visibleItems = array_keys($this->getVisibleItems());
        $parseItems = [];
        $itemGrid = [1 => 12, 2 => 6, 3 => 4, 4 => 3];
        foreach ($this->tab as $tabName => &$tabInfo) {
            foreach ($tabInfo['grids'] as $gridName => &$gridInfo) {
                foreach ($gridInfo['items'] as $key => &$item) {
                    $fieldName = null;
                    if (is_string($item) && in_array($item, $visibleItems)) {
                        $fieldName = $item;
                    } elseif (is_array($item) && in_array($key, $visibleItems, true)) {
                        $fieldName = $key;
                    }
                    if (!empty($fieldName)) {
                        $item = array_merge($this->collection->getItemAttr($fieldName), is_array($item) ? $item : []);
                        $parseItems[] = $fieldName;
                        continue;
                    }
                    if (is_array($item) && !in_array($key, $items, true)) {
                        if (is_string($key)) {
                            foreach ($item as $key3 => &$item3) {
                                $fieldName = null;
                                if (is_string($item3) && in_array($item3, $visibleItems)) {
                                    $fieldName = $item3;
                                } elseif (is_array($item3) && in_array($key3, $visibleItems, true)) {
                                    $fieldName = $key3;
                                }
                                if (!empty($fieldName)) {
                                    $item3 = array_merge($this->collection->getItemAttr($fieldName), is_array($item3) ? $item3 : []);
                                    $parseItems[] = $fieldName;
                                    continue;
                                }

                                if (!is_array($item3)) {
                                    unset($item[$key3]);
                                    continue;
                                }
                                $grid = 3;
                                if (array_key_exists(count($item3), $itemGrid)) {
                                    $grid = $itemGrid[count($item3)];
                                }
                                foreach ($item3 as $key4 => &$item4) {
                                    $fieldName = null;
                                    if (is_string($item4)) {
                                        $fieldName = $item4;
                                    } else {
                                        $fieldName = $key4;
                                    }
                                    if (!in_array($fieldName, $visibleItems, true)) {
                                        unset($item3[$key4]);
                                        continue;
                                    }
                                    $item4 = array_merge($this->collection->getItemAttr($fieldName),
                                        ['grid' => $grid],
                                        is_array($item4) ? $item4 : []
                                    );
                                    $parseItems[] = $fieldName;
                                }
                            }
                            continue;
                        }
                        foreach ($item as $key1 => $item1) {
                            if ((is_scalar($item1) && !in_array($item1, $visibleItems, true))
                                || is_array($item1) && !in_array($key1, $visibleItems, true)
                            ) {
                                unset($item[$key1]);
                                continue;
                            }
                        }
                        $grid = 3;
                        if (array_key_exists(count($item), $itemGrid)) {
                            $grid = $itemGrid[count($item)];
                        }
                        foreach ($item as $key2 => &$item2) {
                            $fieldName = null;
                            if (is_string($item2)) {
                                $fieldName = $item2;
                            } else {
                                $fieldName = $key2;
                            }
                            $item2 = array_merge($this->collection->getItemAttr($fieldName),
                                ['grid' => $grid],
                                is_array($item2) ? $item2 : []
                            );
                            $parseItems[] = $fieldName;
                        }
                        continue;
                    }
                    unset($gridInfo['items'][$key]);
                }
            }
        }
        $notSet = array_diff($visibleItems, $parseItems);
        foreach ($notSet as &$item) {
            $item = $this->collection->getItemAttr($item);
        }
        if (count(reset($this->tab)['grids']) == 0) {
            $this->switchTab(array_keys($this->tab)[0])->setGrid('', '', 12, $notSet, '');
        } else {
            $this->tab[array_keys($this->tab)[0]]['grids'][array_keys($this->tab[array_keys($this->tab)[0]]['grids'])[0]]['items'] = array_merge(
                $this->tab[array_keys($this->tab)[0]]['grids'][array_keys($this->tab[array_keys($this->tab)[0]]['grids'])[0]]['items'],
                $notSet
            );
        }
        //pr($this->tab);
        $this->isTabParse = true;
        return $this->tab;
    }

    /**
     * 移除一个Tab
     * @param string $tabName Tab标识
     * @return $this
     */
    public function removeTab(string $tabName)
    {
        if (count($this->tab) == 1) {
            return $this;
        }
        if (isset($this->tab[$tabName])) {
            unset($this->tab[$tabName]);
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
            $this->setTab($tabName, $tabName);
        }
        $this->selectTab = $tabName;
        return $this;
    }

    /**
     * 获取当前Tab数量
     * @return int
     */
    public function getTabNumber()
    {
        return count($this->tab);
    }

    /**
     * 添加指定Tab的一个栅格  通过switchTab来指定给哪个Tab加栅格
     * @param string $gridName Grid的标识
     * @param string $title Grid的名称
     * @param int|array $grid 栅格定义 int 表示只定义md值 ; 也可以['xs' => 12,'sm' => 12,'md' => 9,'lg' => 9] 定义不同尺寸下栅格
     * @param array $items 表单项目
     * @param string $icon 图标
     * @return $this
     */
    public function setGrid(string $gridName,
                            string $title,
                            $grid = 12,
                            array $items = [],
                            string $icon = '')
    {

        if (empty($gridName)) {
            $gridName = strval(mt_rand());
        }
        $tabName = $this->getGridTabName($gridName);
        $this->storeGridField($items);
        $this->tab[$tabName]['grids'][$gridName] = [
            'title' => $title,
            'icon'=> $icon,
            'name' => $gridName,
            'grid' => $grid,
            'items' => $items
        ];
        $this->gridToTab[$gridName] = $tabName;
        return $this;
    }

    protected function storeGridField($items)
    {
        if (is_string($items)) {
            if ($this->collection->itemExists($items)) {
                array_push($this->storeGridFieldList, $items);
            }
            return;
        }
        foreach ((array) $items as $key => $value) {
            if (is_string($key) && $this->collection->itemExists($key)) {
                array_push($this->storeGridFieldList, $key);
            } else {
                if (is_string($value) && $this->collection->itemExists($value)) {
                    array_push($this->storeGridFieldList, $value);
                }
                if (is_array($value)) {
                    $this->storeGridField($value);
                }
            }
        }
    }

    /**
     * 直接给表单Tab设置有哪些表单项目
     * @param array $items
     * @param string $gridName
     * @return $this|FormPage
     */
    public function setTabItems(array $items, string $gridName = '')
    {

        if (trim($gridName)) {
            $tabName = $this->getGridTabName($gridName);
            if ($tabName === $this->selectTab) {
                $this->tab[$tabName]['grids'][$gridName] = $items;
                return $this;
            } elseif (isset($this->gridToTab[$gridName])) {
                $grid = $this->tab[$tabName]['grids'][$gridName];
                $grid['items'] = empty($items) ? $grid['items'] : $items;
                unset($this->tab[$tabName]['grids'][$gridName]);
                $this->gridToTab[$gridName] = $this->selectTab;
                $this->tab[$this->selectTab]['grids'][$gridName] = $grid;
                return $this;
            }
        }

        if (empty($gridName) && count($this->tab[$this->selectTab]['grids'])) {
            $gridName = array_keys($this->tab[$this->selectTab]['grids'])[0];
            $this->tab[$this->selectTab]['grids'][$gridName]['items'] = array_merge(
                $this->tab[$this->selectTab]['grids'][$gridName]['items'] ?? [],
                $items
            );
            return $this;
        }
        return $this->setGrid($gridName, '', 12, $items);
    }

    /**
     * 添加一个触发表单项目
     * @param string $item 触发的表单项目
     * @param array $trigger 被触发的值和对应项目  [值1 => [字段列表],值2 => [字段列表]]
     * @return $this
     */
    public function addTrigger(string $item, array $trigger)
    {
        $trigger_fields = [];
        $callback = '';
        foreach ($trigger as $value => &$fields) {
            if ($value === 'callback') {
                $callback = $fields;
                continue;
            }
            if (is_string($fields)) {
                $fields = explode('|', $fields);
            }
            $fields = is_array($fields) ? $fields : [$fields];
            $trigger_fields = array_merge($trigger_fields, $fields);
        }
        $this->triggerFields = array_merge($this->triggerFields, $trigger_fields);
        $this->trigger[$item] = [
            'fields' => $trigger_fields,
            'values' => $trigger
        ];
        if (!empty($callback)) {
            $this->trigger[$item]['callback'] = (string) $callback;
        }
        return $this;
    }

    /**
     * 获取触发隐藏字段
     * @return array
     */
    public function getTriggerFields()
    {
        return $this->triggerFields;
    }

    /**
     * 获取整个触发项目信息
     * @return array
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    protected function getGridTabName(string $gridName)
    {
        if (isset($this->gridToTab[$gridName])) {
            $tabName = $this->gridToTab[$gridName];
        } else {
            $tabName = $this->selectTab;
        }
        if (!isset($this->tab[$tabName])) {
            $tabName = array_keys($this->tab)[0];
        }
        return $tabName;
    }

    protected function setDefaultAttrs()
    {
        // 需要加layui-input 类名的表单项
        $input_list = [
            'text'
            ,'password'
            ,'emailh5'
            ,'urlh5'
            ,'telh5'
            ,'number'
            ,'date'
            ,'datetime'
            ,'month'
            ,'year'
            ,'time'
            ,'color'
            ,'icon'
            ,'bankcard'
            ,'email'
            ,'ip4'
            ,'ip6'
            ,'amap'
            ,'random'
            ,'relation2'
        ];
        $layui_input_list = array_map(function ($value) {
            return [
                'class' => [
                    'layui-input'
                ]
            ];
        }, Arr::normalize($input_list));


        $this->defaultAttrs = array_merge(
            [
                'textarea' => [
                    'class' => 'layui-textarea'
                ],
                'captcha' => [
                    'class' => [
                        'layui-input',
                        'captcha-input'
                    ]
                ],
                'checkbox' => [
                    'lay-skin' => 'primary'
                ]
            ],
            $layui_input_list,
            $this->defaultAttrs
        );
    }

    protected function getAttrsAttr(array $options = [])
    {
        // layui 过滤器
        if (in_array($options['element'], ['checker', 'radio', 'select', 'checkbox'])) {
            $options['attrs']['lay-filter'] = $options['field_name'];
        }
        return parent::getAttrsAttr($options);
    }

    protected function multiattrsBeforeParse(array $options = [])
    {
        return parent::{__FUNCTION__}(...func_get_args());
    }

    public function __toString()
    {
        return $this->getLayout('form_page');
    }
}