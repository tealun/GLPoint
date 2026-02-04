<?php
declare (strict_types=1);

namespace woo\common\builder\form\traits;

use woo\common\facade\Cache;
use woo\common\helper\Arr;
use woo\common\helper\Str;
use woo\common\helper\Tree;

trait AfterParse
{

    protected function textAfterParse(array $options)
    {
        if (!empty($options['options'])) {
            $options['quick'] = $options['options'];
        }
        if (isset($options['quick']) && !isset($options['attrs']['autocomplete'])) {
            $options['attrs']['autocomplete'] = 'off';
        }
        if (!empty($options['clear'])) {
            $options['attrs']['lay-affix'] = 'clear';
        }
        if (!isset($options['attrs']['lay-affix']) && !empty($options['attrs']['value']) && !empty($this->error[$options['real_field_name']])) {
            $options['attrs']['lay-affix'] = 'clear';
        }
        if (!isset($options['attrs']['data-type']) && $this->model && isset($this->model->form[$options['real_field_name']])) {
            $type = $this->model->form[$options['real_field_name']]['type'] ?? 'string';
            $options['attrs']['data-type'] = $type;
        }
        if (isset($options['attrs']['value']) && is_array($options['attrs']['value'])) {
            $options['attrs']['value'] = json_encode($options['attrs']['value'], JSON_UNESCAPED_UNICODE);
        }
        return $options;
    }

    protected function numberAfterParse(array $options)
    {
        if (!isset($options['attrs']['data-type']) && $this->model && isset($this->model->form[$options['real_field_name']])) {
            $type = $this->model->form[$options['real_field_name']]['type'] ?? 'string';
            $options['attrs']['data-type'] = $type;
        }
        return $options;
    }

    protected function randomAfterParse(array $options)
    {
        if (!isset($options['attrs']['data-random'])) {
            $options['attrs']['data-random'] = 'number:5';
        }
        return $options;
    }

    protected function textareaAfterParse(array $options)
    {
        if (isset($options['attrs']['value']) && is_array($options['attrs']['value'])) {
            $options['attrs']['value'] = json_encode($options['attrs']['value'], JSON_UNESCAPED_UNICODE);
        }
        if (!empty($options['clear'])) {
            $options['attrs']['lay-affix'] = 'clear';
        }
        return $options;
    }

    protected function bankcardAfterParse(array $options)
    {
        if (empty($options['attrs']['maxlength'])) {
            $options['attrs']['maxlength'] = 23;
        }
        return $options;
    }

    protected function emailAfterParse(array $options)
    {
        $emails = array_merge(['qq.com', 'gmail.com', '163.com', '126.com', 'sina.com'], $options['options'] ?? []);
        $options['attrs']['data-emails'] = implode(',', $emails);
        return $options;
    }

    protected function datetimeAfterParse(array $options)
    {
        if (!empty($options['attrs']['value']) && is_numeric($options['attrs']['value'])) {
            $options['attrs']['value'] = date('Y-m-d H:i:s', $options['attrs']['value']);
        }
        if (empty($options['attrs']['value'])) {
            $options['attrs']['value'] = '';
        }
        return $options;
    }

    protected function dateAfterParse(array $options)
    {
        if (!empty($options['attrs']['value']) && is_numeric($options['attrs']['value'])) {
            $options['attrs']['value'] = date('Y-m-d', $options['attrs']['value']);
        }
        if (empty($options['attrs']['value'])) {
            $options['attrs']['value'] = '';
        }
        return $options;
    }

    protected function monthAfterParse(array $options)
    {
        if (!empty($options['attrs']['value']) && is_numeric($options['attrs']['value'])) {
            $options['attrs']['value'] = date('Y-m', $options['attrs']['value']);
        }
        if (empty($options['attrs']['value'])) {
            $options['attrs']['value'] = '';
        }
        return $options;
    }
    protected function colorAfterParse(array $options)
    {
        if (!isset($options['attrs']['data-predefine'])) {
            $options['attrs']['data-predefine'] = true;
        }
        return $options;
    }

    protected function sortvaluesAfterParse(array $options)
    {
        if (empty($options['attrs']['value']) && !empty($options['options'])) {
            $options['attrs']['value'] = implode(',', array_keys($options['options']));
        }
        return $options;
    }

    /**
     * checker render之后的回调
     * @param array $options
     * @return array
     */
    protected function checkerAfterParse(array $options)
    {
        if (!isset($options['attrs']['lay-skin'])) {
            $options['attrs']['lay-skin'] = 'switch';
        }
        //建议layui 2.8.0以后 lay-text属性调整为title属性
        if (isset($options['attrs']['lay-text']) && !isset($options['attrs']['title'])) {
            $options['attrs']['title'] = $options['attrs']['lay-text'];
        }
        if (!isset($options['attrs']['title'])) {
            $options['attrs']['title'] = 'Y|N';
        }
        $options['lay-text'] = explode('|', $options['attrs']['title']);
        if (!isset($options['options']['yes'])) {
            $options['options']['yes'] = 1;
        }
        if (!isset($options['options']['no'])) {
            $options['options']['no'] = 0;
        }
        return $options;
    }

    /**
     * checkbox render之后的回调
     */
    protected function checkboxAfterParse(array $options)
    {
        //name 单独处理 追加[]
        if (isset($options['attrs']['name'])) {
            $options['attrs']['name'] .=  "[]";
        }
        if (isset($options['attrs']['value']) && !is_array($options['attrs']['value'])) {
            $join = ',';
            if (!empty($this->model)) {
                $join = $this->model->form[$options['field_name']]['join'] ?? ',';
            }
            $options['attrs']['value'] = is_json($options['attrs']['value']) ? json_decode($options['attrs']['value']) : explode($join, (string)$options['attrs']['value']);
        }
        return $options;
    }

    protected function xmselectAfterParse(array $options)
    {
        if (isset($options['attrs']['value']) && is_array($options['attrs']['value'])) {
            $options['attrs']['value'] = implode(',', $options['attrs']['value']);
        }
        return $options;
    }

    protected function formatAfterParse(array $options)
    {
        if (!isset($options['format']) && isset($options['attrs']['value']) && !empty($options['options'])) {
            $options['format'] = $options['options'][$options['attrs']['value']] ?? $options['attrs']['value'];
        }
        if (!empty($options['foreign']) && !empty($options['attrs']['value']) && is_numeric($options['attrs']['value'])) {
            $foreign = get_relation($options['foreign'], $this->model);
            $model = model($foreign[0]);
            $options['format'] = $model->where($model->getPk(), '=', $options['attrs']['value'])->value($foreign[1]) ?? $options['attrs']['value'];
        }
        return $options;
    }

    protected function selectAfterParse(array $options)
    {
        if (!empty($options['options'])) {
            return $options;
        }
        if (isset($options['foreign'])) {
            $foreign = get_relation($options['foreign'], $this->model);
            $model = model($foreign[0]);
            $pk = $model->getPk();
            $result = $model->field([$pk, $foreign[1]])->select()->toArray();
            $options['options'] = Arr::combine($result, $pk, $foreign[1]);
        }

        $options['attrs']['data-value'] = $options['attrs']['value'] ?? '';
        return $options;
    }

    protected function relationAfterParse(array $options)
    {
        if (isset($options['attrs']['value']) && is_array($options['attrs']['value'])) {
            $options['attrs']['value'] = implode(',', $options['attrs']['value']);
        }
        if (!isset($options['relation_controller'])) {
            $options['relation_controller'] = get_base_class($this->model);
        }
        if (!isset($options['relation_action'])) {
            $options['relation_action'] = 'getRelationOptions';
        }
        $foreign = $options['foreign'] ?? '';

        if (empty($foreign)) {
            $foreign = Str::studly(substr($options['field_name'], 0, -3));
        }
        $foreign = get_relation($foreign, $this->model);
        if (!isset($options['attrs']['data-type'])) {
            $options['attrs']['data-type'] = isset($foreign['type']) && $foreign['type'] == 'belongsToMany' ? 'checkbox' : 'radio';
        }
        $model = model($foreign[0]);
        $pk = $model->getPk();
        $options['attrs']['data-pk'] = $pk;
        $options['attrs']['data-display'] = $model->display;

        if (empty($options['attrs']['value'])) {
            return $options;
        }
        $ids = explode(',', (string) $options['attrs']['value']);
        $result = $model->where($pk, 'IN', $ids)->column($foreign[1], $pk);
        $display = [];
        foreach ($ids as $id) {
            if (isset($result[$id])) {
                array_push($display,  '<span class="layui-badge relation-item" data-value="' . $id . '">' . $id . '<span class="separator">:</span>' . $result[$id] . '<i class="layui-icon layui-icon-close relation-item-remove"></i></span>');
            } else {
                array_push($display,  '<span class="layui-badge relation-item" data-value="' . $id . '">' . $id . '<span class="separator">:</span>已删除<i class="layui-icon layui-icon-close relation-item-remove"></i></span>');
            }
        }
        $options['display'] = implode('', $display);
        return $options;
    }

    protected function relation2AfterParse($options)
    {
        return $this->relationAfterParse($options);
    }

    protected function xmtreeAfterParse(array $options)
    {
        if (isset($options['attrs']['value']) && is_array($options['attrs']['value'])) {
            $options['attrs']['value'] = implode(',', $options['attrs']['value']);
        }
        if (isset($options['foreign'])) {
            $foreign = get_relation($options['foreign'], $this->model);
            $model = model($foreign[0]);
        } elseif ($options['real_field_name'] != 'parent_id' && Str::endsWith($options['real_field_name'], '_id')) {
            $foreign = get_relation(Str::studly(substr($options['real_field_name'], 0, -3)), $this->model);
            $model = model($foreign[0]);
        } else {
            $model = $this->model;
        }
        if (isset($foreign) && isset($foreign['type']) && $foreign['type'] == 'belongsToMany' && !isset($options['attrs']['data-max'])) {
            $options['attrs']['data-max'] = 99;
        }
        if (!isset($options['attrs']['data-max'])) {
            $options['attrs']= array_merge($options['attrs'] ?? [], ['data-max' => 1]);
        }
        if (!empty($options['options'])) {
            return $options;
        }
        if (!isset($options['optionsCallback']) || !is_callable($options['optionsCallback'])) {
            try {
                $tree = new Tree($model);
                $options['options'] =  $tree->getXmOptions(0, $options['attrs']['value'] ?? '', $this->getData());

                if ($options['current_model'] != $model->getName() && empty($options['allowTop'])) {
                    $options['options'][0]['disabled'] = true;
                }
            } catch (\Exception $e) {
                $options['message'] = '提示，表单使用错误：' . $e->getMessage();
            }
        } else {
            $options['options'] =  $options['optionsCallback'](0, $options['attrs']['value'] ?? '', $this->getData());
        }

        return $options;
    }

    protected function selectfortreeAfterParse(array $options)
    {
        return $this->xmselectfortreeAfterParse($options);
    }

    protected function xmselectfortreeAfterParse(array $options)
    {
        if (!empty($options['options'])) {
            return $options;
        }
        if (!isset($options['optionsCallback']) || !is_callable($options['optionsCallback'])) {
            $model = isset($options['foreign']) ? model(get_relation($options['foreign'], $this->model)[0]) : $this->model;
            try {
                $tree = new Tree($model);
                $options['options'] =  $tree->getOptions();
            } catch (\Exception $e) {
                $options['message'] = '提示，表单使用错误：' . $e->getMessage();
            }
        } else {
            $options['options'] = $options['optionsCallback']($this->getData());
        }

        if (!isset($options['attrs']['data-max'])) {
            $options['attrs']= array_merge($options['attrs'] ?? [], ['data-max' => 1]);
        }
        return $options;
    }

    protected function cascaderAfterParse(array $options)
    {
        if (!empty($options['options'])) {
            return $options;
        }
        $options['attrs']['data-field'] = $options['field_name'];
        if (!isset($options['optionsCallback']) || !is_callable($options['optionsCallback'])) {
            $model = isset($options['foreign']) ? model(get_relation($options['foreign'], $this->model)[0]) : $this->model;
            if (!isset($model->form['parent_id'])) {
                $options['options'] = [];
            }

            if (empty($options['attrs']['data-url'])) {
                try {
                    $tree = new Tree($model);
                    $options['options'] =  $tree->getCascaderOptions($options['attrs']['value'] ?? '', $this->getData());
                } catch (\Exception $e) {
                    $options['message'] = '提示，表单使用错误：' . $e->getMessage();
                }
            } else {
                $default_url = (string) url($this->request->controller() . '/getCascaderData');
                $options['attrs']['data-url'] = is_bool($options['attrs']['data-url']) ? $default_url : $options['attrs']['data-url'];
                $options['options'][0] = ['parent_id' => 0, 'children' => []];
                $list = $model->where('parent_id', '=', 0)->order($model->getDefaultOrder())->select()->toArray();
                foreach ($list as $item) {
                    array_push($options['options'][0]['children'], [
                        'id' => $item[$model->getPk()],
                        'title' => $item[$model->display],
                        'is_children' => isset($item['children_count']) ?
                            ($item['children_count'] ? true : false) :
                            ($model->where('parent_id', '=', $item[$model->getPk()])->count() ? true : false)
                    ]);
                }
            }
        } else {
            $options['options'] = $options['optionsCallback']($options['attrs']['value'] ?? '', $this->getData());
        }
        return $options;
    }

    protected function iconpickerAfterParse(array $options)
    {
        if (!isset($options['attrs']['data-search'])) {
            $options['attrs']['data-search'] = true;
        }
        if (!isset($options['attrs']['data-page'])) {
            $options['attrs']['data-page'] = true;
        }
        $options['attrs']['style'] = 'display:none;';
        return $options;
    }

    protected function transferAfterParse(array $options)
    {
        if (!empty($options['options'])) {
            $options = [];
            foreach ($options['options'] as $value => $title) {
                if (is_array($title) && isset($title['value'])) {
                    array_push($options, $title);
                    continue;
                }
                array_push($options, [
                    'value' => $value,
                    'title' => $title,
                ]);
            }
            $options['options'] = $options;
            return $options;
        }
        if (!isset($options['optionsCallback']) || !is_callable($options['optionsCallback'])) {
            if (isset($options['foreign'])) {
                $model = model(get_relation($options['foreign'], $this->model)[0]);
                $list = $model->order($model->getDefaultOrder())->select()->toArray();
                $options = [];
                foreach ($list as $item) {
                    array_push($options, [
                        'value' => $item[$model->getPk()],
                        'title' => $item[$model->display],
                    ]);
                }
                $options['options'] = $options;
            }
        } else {
            $options['options'] = $options['optionsCallback']($options['attrs']['value'] ?? '', $this->getData());
        }

        return $options;
    }

    protected function childBeforeParse(array $options)
    {
        $class = get_class($this);
        $form = new $class([], $options['foreign'], $options);
        $options['forms'][] =  $form;
        return $options;
    }

    protected function multiattrsBeforeParse(array $options)
    {
        if (!empty($options['fields'])) {
            $class = get_class($this);
            $form = new $class();
            foreach ($options['fields'] as $field => &$info) {
                $info['attrs']['name'] = ($options['attrs']['name'] ?? Str::snake($options['field_name'])) . '[PLACEINDEX]' . '[' . Str::snake($field) .']';
                $info = $form->addFormItem($field, $info['elem'] ?? 'text', $info)->parseItem($field);
            }
        }
        return $options;
    }

    protected function specBeforeParse(array $options)
    {
        if (!empty($options['fields'])) {
            $class = get_class($this);
            $form = new $class();
            foreach ($options['fields'] as $field => &$info) {
                $info['attrs']['name'] = Str::snake($options['field_name']) . '[list][0]' . '[' . Str::snake($field) .']';
                $info = $form->addFormItem($field, $info['elem'] ?? 'text', $info)->parseItem($field);
            }
        }
        return $options;
    }

    protected function specAfterParse(array $options)
    {
        if (!empty($options['attrs']['value'])) {
            $options['attrs']['value'] = Str::deepJsonDecode($options['attrs']['value']);
        } else {
            $options['attrs']['value'] = [];
        }
        return $options;
    }

    protected function getTogetherInfo($options)
    {
        $foreign = $options['foreign'] ?? '';
        if (empty($foreign)) {
            $foreign = Str::studly($options['field_name']);
        }
        $foreign = get_relation($foreign, $this->model);
        $foreign['foreignKey'] = empty($foreign['foreignKey']) ? Str::snake($this->model->getName()) . '_id' : $foreign['foreignKey'];
        if (empty($foreign[0]) || !get_model_name($foreign[0])) {
            return false;
        }
        if (isset($foreign['type']) && in_array($foreign['type'], ['hasMany', 'hasOne'])) {
            $foreignModel = model($foreign[0]);
            $pk = $foreignModel->getPk();
            $this->together[$options['field_name']] = [
                'foreign' => $foreign[0],
                'type' => $foreign['type'],
                'pk' => $pk,
                'foreign_key' => $foreign['foreignKey']
            ];
        }
    }

    protected function orderitemBeforeParse(array $options)
    {
        $foreign = $options['foreign'] ?? '';
        $setFields = Arr::normalize($options['fields'] ?? []);
        if (empty($foreign)) {
            $foreign = Str::studly($options['field_name']);
        }
        $foreign = get_relation($foreign, $this->model);
        $foreign['foreignKey'] = empty($foreign['foreignKey']) ? Str::snake($this->model->getName()) . '_id' : $foreign['foreignKey'];

        if (empty($foreign[0]) || !get_model_name($foreign[0])) {
            $options['error'] = '关联写入的模型查找失败';
            return $options;
        }
        if (isset($foreign['type']) && in_array($foreign['type'], ['hasMany'])) {
            $foreignModel = model($foreign[0]);
            $pk = $foreignModel->getPk();
            $with = [];
            $fields = [];
            foreach ($foreignModel->form as $field => $info) {
                if (!empty($setFields) && !array_key_exists($field, $setFields) && $field != $pk) {
                    continue;
                }
                if (!empty($setFields[$field])) {
                    $info = array_merge($info, $setFields[$field]);
                }
                $item = array_diff_key($info, Arr::normalize([
                    'list', 'detail', 'form_group', 'filter'
                ]));
                $item['width'] = intval($item['width'] ?? 100);
                if (!in_array(app('http')->getName(), ['admin', 'business']) && isset($item['is_contribute']) && !$item['is_contribute']) {
                    continue;
                }
                if (in_array(($info['elem'] ?? 'text'), ['0', 'csrf', 'hidden', 'none', '']) && $field != $pk) {
                    continue;
                }
                // 关联字段不允许自行编辑
                if ($field == $foreign['foreignKey'] || $field == $pk) {
                    continue;
                }
                if (isset($item['elem']) && $item['elem'] == 'number') {
                    $item['elem'] = 'text';
                    $item['type'] = 'integer';
                }
                if (isset($item['elem']) && $item['elem'] == 'select') {
                    $item['elem'] = 'text';
                    $item['attrs']['readonly'] = 'readonly';
                }
                if (isset($item['elem']) && $item['elem'] == 'relation') {
                    $foreign2 = $item['foreign'] ?? '';
                    if (empty($foreign2)) {
                        $foreign2 = Str::studly(substr($field, 0, -3));
                    }
                    $foreign2 = get_relation($foreign2, $foreignModel);
                    if (empty($foreign2['type']) || $foreign2['type'] != 'belongsTo') {
                        $options['message'] = '提示：'.$field.'关联模型只支持belongsTo';
                    };
                    if (!empty($foreign2['key'])) {
                        $with[] = $foreign2['key'];
                    }
                    $item['display'] = model($foreign2['foreign'])->display;
                    $item['cname'] = model($foreign2['foreign'])->cname;

                    if (empty($item['withName'])) {
                        $item['withName'] = [model($foreign2['foreign'])->display];
                        //$item['withName'] = ['number', 'title'];// 删除
                    } else {
                        $item['withName'] = (array) $item['withName'];
                    }
                    $item['withName'] = json_encode($item['withName']);
                    //$item['withValue'] = ['unit', 'ck', 'num' => 1, 'price', 'cb'];// 要删除
                    if (!empty($item['withValue'])) {
                        $item['withValue'] = Arr::normalize((array)$item['withValue']);
                        foreach ($item['withValue'] as $k => &$v) {
                            if (!isset($v)) {
                                $v = $k;
                            }
                        }
                        $item['withValue'] = json_encode($item['withValue']);
                    }
                    if (empty($item['href'])) {
                        $item['href'] = (string)url($foreign2['foreign'] . '/index2');
                    }
                }
                $item['attrs']['data-type'] = $item['attrs']['data-type'] ?? ($item['type'] ?? 'string');
                $item['field'] = $field;
                $fields[$field] = $item;
            }
            $options['fields'] = $fields;
            $this->together[$options['field_name']] = $options['together'] = [
                'foreign' => $foreign[0],
                'type' => $foreign['type'],
                'pk' => $pk,
                'foreign_key' => $foreign['foreignKey']
            ];

            $options['pk'] = $pk;
            if (!empty($this->data[$this->model->getPk()]) && empty($this->data[$options['field_name']])) {
                $local_id = $this->data[$this->model->getPk()];
                $list = $foreignModel
                    ->where($foreign['foreignKey'], '=', $local_id)
                    ->with($with)
                    ->order([$pk => 'ASC'])
                    ->select()
                    ->toArray();
                if ($list) {
                    $options['attrs']['value'] = $list;
                }
            }
        } else {
            $options['error'] = '关联写入目前只支持"hasMany"关联类型';
        }
        // 要删除
        /*
        $options['attrs']['data-watch'] = [
            'num' => [
                'money' => "{{parseInt(d.num || 0) * parseFloat(d.cb || 0)}}" // 支持函数名
            ],
            'cb' => [
                'money' => "{{parseInt(d.num || 0) * parseFloat(d.cb || 0)}}"
            ],
            'money' => [
                'cb' => "{{parseFloat(d.money || 0) / parseInt(d.num || 1)}}"
            ]
        ];
        */
        if (isset($options['attrs']['data-watch'])) {
            $options['attrs']['data-watch'] = json_encode($options['attrs']['data-watch']);
        }
        // 要删除
        /*
        $options['attrs']['data-counter'] = [
            'num' => [
                'type' => 'sum', // count max min avg sum callback
               // 'callback' => 'aaa',
                'name' => '和:',
                'default' => 0
            ],
            'cb' => [
                'type' => 'avg',
                'name' => '均价:',
            ],
            'money' => [
                'type' => 'sum',
                'name' => '总价:',
            ]
        ];
        */
        if (isset($options['attrs']['data-counter'])) {
            $options['attrs']['data-counter'] = json_encode($options['attrs']['data-counter']);
        }

        return $options;
    }

    protected function together2BeforeParse(array $options)
    {
        return $this->togetherBeforeParse($options);
    }

    protected function together2AfterParse($options)
    {
        if (!empty($options['attrs']['value'])) {
            if (!is_array($options['attrs']['value'])) {
                $options['attrs']['value'] = json_decode($options['attrs']['value']);
            }
            $options['together']['tab'] = [];
            foreach ($options['attrs']['value'] as $item) {
                array_push($options['together']['tab'], $item[$options['together']['display']]);
            }
            $options['attrs']['value'] = array_values($options['attrs']['value']);
        }
        return $options;
    }

    protected  function togetherBeforeParse(array $options)
    {
        $foreign = $options['foreign'] ?? '';
        $setFields = Arr::normalize($options['fields'] ?? []);

        if (empty($foreign)) {
            $foreign = Str::studly($options['field_name']);
        }

        $foreign = get_relation($foreign, $this->model);
        $foreign['foreignKey'] = empty($foreign['foreignKey']) ? Str::snake($this->model->getName()) . '_id' : $foreign['foreignKey'];

        if (empty($foreign[0]) || !get_model_name($foreign[0])) {
            $options['error'] = '关联写入的模型查找失败';
            return $options;
        }
        if (isset($foreign['type']) && in_array($foreign['type'], ['hasMany', 'hasOne'])) {
            if ($foreign['type'] === 'hasOne') {
                // 只能添加一条
                $options['attrs']['max'] = 1;
            }
            $options['multiattrs']['cancel_insert'] = true;
            $foreignModel = model($foreign[0]);
            $pk = $foreignModel->getPk();
            $fields = [];
            $class = get_class($this);
            $form = new $class([], $foreignModel);
            foreach ($foreignModel->form as $field => $info) {
                if (!empty($setFields) && !array_key_exists($field, $setFields) && $field != $pk) {
                    continue;
                }
                if (!empty($setFields[$field])) {
                    $info = array_merge($info, $setFields[$field]);
                }
                $item = array_diff_key($info, Arr::normalize([
                    'elem', 'name', 'type', 'list', 'detail', 'form_group', 'filter'
                ]));
                if (!in_array(app('http')->getName(), ['admin', 'business']) && isset($item['is_contribute']) && !$item['is_contribute']) {
                    continue;
                }
                if (in_array(($info['elem'] ?? 'text'), ['0', 'csrf', 'hidden', 'none', '']) && $field != $pk) {
                    continue;
                }
                // 关联字段不允许自行编辑
                if ($field == $foreign['foreignKey']) {
                    continue;
                }
                // 主键必须显示出来 不然会编辑失败
                if ($field == $pk) {
                    $info['elem'] = 'hidden';
                }
                $item['attrs']['data-field'] = $field;
                $item['attrs']['name'] = ($options['attrs']['name'] ?? Str::snake($options['field_name'])) . '[PLACEINDEX]' . '[' . Str::snake($field) .']';
                $fields[$field] = $form->addFormItem($field, $info['elem'] ?? 'text', $item)->setLabelAttr($info['name'] ?? $field)->parseItem($field);
            }
            $options['fields'] = $fields;

            $this->together[$options['field_name']] = $options['together'] = [
                'foreign' => $foreign[0],
                'type' => $foreign['type'],
                'pk' => $pk,
                'foreign_key' => $foreign['foreignKey']
            ];
            $options['together']['display'] = $foreignModel->display;
            $options['together']['cname'] = $foreignModel->cname;
            if (!empty($this->data[$this->model->getPk()]) && empty($this->data[$options['field_name']])) {
                $local_id = $this->data[$this->model->getPk()];
                $list = $foreignModel
                    ->where($foreign['foreignKey'], '=', $local_id)
                    ->order($foreignModel->getDefaultOrder())
                    ->select()
                    ->toArray();
                if ($list) {
                    $options['attrs']['value'] = $list;
                }
            }
        } else {
            $options['error'] = '关联写入目前只支持"hasMany"、"hasOne"关联类型';
        }
        return $options;
    }

    protected function imageAfterParse(array $options)
    {
        return $this->parseUpload($options, 'images', false);
    }

    protected function fileAfterParse(array $options)
    {
        return $this->parseUpload($options, 'file', false);
    }


    protected function multiimageAfterParse(array $options)
    {
        return $this->parseUpload($options, 'images', true);
    }

    protected function multifileAfterParse(array $options)
    {
        return $this->parseUpload($options, 'file', true);
    }

    protected function parseUpload(array $options, string $accept = 'images' ,bool $multiple = false)
    {
        $options['attrs']['data-multiple'] = $multiple;
        $options['attrs']['data-number'] =  $multiple ? ($options['upload']['maxLength'] ?? intval(setting('upload_max_length'))) : 1;
        $options['attrs']['data-url'] = $options['upload']['url'] ?? (string)url('Attachement/upload');
        $options['attrs']['data-model'] =  $options['attrs']['data-model'] ?? $options['current_model'];
        $options['attrs']['data-field'] = $options['field_name'];
        $options['attrs']['data-namefield'] = $options['upload']['nameFiled'] ?? '';
        $options['attrs']['data-sizefield'] = $options['upload']['sizeField'] ?? '';
        $options['attrs']['data-accept'] = $accept;
        $options['attrs']['data-exts'] = isset($options['upload']['validExt'])
            ? (is_array($options['upload']['validExt']) ? implode('|', $options['upload']['validExt']) : $options['upload']['validExt'])
            : ($accept == 'images' ?  setting('upload_image_valid_ext') :  setting('upload_file_valid_ext'));
        $options['attrs']['data-size'] = $options['upload']['maxSize'] ?? ($accept == 'images' ? intval(setting('upload_image_max_size')) : intval(setting('upload_file_max_size')));
        if ($accept == 'images' && empty($options['attrs']['data-accept-mime'])) {
            $options['attrs']['data-accept-mime'] = [];
            if (!empty($options['attrs']['data-exts'])) {
                $exts = explode('|', $options['attrs']['data-exts']);
                foreach ($exts as $e) {
                    $options['attrs']['data-accept-mime'][] = 'image/' . $e;
                }
                $options['attrs']['data-accept-mime'] = implode(',', $options['attrs']['data-accept-mime']);
            } else {
                $options['attrs']['data-accept-mime'] = 'image/*';
            }
        }
        return $options;
    }

    protected function ckeditorAfterParse(array $options)
    {
        return $this->ueditorAfterParse($options);
    }

    protected function ueditorAfterParse(array $options)
    {
        if (isset($options['attrs']['data-serverurl'])) {
            return $options;
        }
        if (get_installed_addons('ueditor')) {
            $options['attrs']['data-serverurl'] = (string)addons_url('ueditor://index/server', ['model' => $options['current_model']]);
        } else {
            $options['attrs']['data-serverurl'] = setting('do_ueditor_server', '');
        }
        return $options;
    }

    protected function iconBeforeParse(array $options)
    {
        $options['options'] = \woo\common\builder\form\StaticIcon::getIcon();
        // 避免请求和使用QueryList扩展 暂时把图标写死了 可以composer安装QueryList 然后注释打开动态获取图标
//        if (Cache::has('layui_icons_cache')) {
//            $data = Cache::get('layui_icons_cache');
//        } else {
//            try {
//                $data = QueryList::get('https://www.layui.com/doc/element/icon.html')
//                    ->rules([
//                        'name' => ['.doc-icon-name', 'text'],
//                        'icon' => ['.doc-icon-fontclass', 'text']
//                    ])
//                    ->range('.site-doc-icon li')
//                    ->query()
//                    ->getData()
//                    ->toArray();
//            } catch (\Exception $e) {
//                $data = [];
//            }
//
//            Cache::set('layui_icons_cache', $data, 86400 * 30);
//        }
//        if (!empty($data)) {
//            $options['options'] = $data;
//        } else {
//            $options['element'] = 'text';
//        }
        return $options;
    }
}