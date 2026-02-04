<?php
declare (strict_types=1);

namespace woo\common\helper;


class Model
{
    protected static $requiredFields = [];
    public static function parseModel(array $data = [])
    {
        if (empty($data['full_table'])) {
            if ($data['addon']) {
                $parse['table'] = get_db_config('prefix', $data['connection'] ?? '') . trim(Str::snake($data['addon']) . '_' . Str::snake($data['model']));
            }
        } else {
            $parse['table'] = trim($data['full_table']);
        }
        if (!empty($data['suffix'])) {
            $parse['suffix'] = trim($data['suffix']);
        }
        if (!empty($data['pk'])) {
            $parse['pk'] = trim($data['pk']);
        }

        if (!empty($data['id'])) {
            $parse['modelId'] = intval($data['id']);
        }

        if (!empty($data['connection'])) {
            $parse['connection'] = trim($data['connection']);
        }
        if (!empty($data['parent_model'])) {
            $parse['parentModel'] = $data['parent_model'] != 'parent' ? Str::studly($data['parent_model']) : 'parent';
        }
        if (!empty($data['cname'])) {
            $parse['cname'] = trim($data['cname']);
        }
        if (!empty($data['display'])) {
            $parse['display'] = trim($data['display']);
        }
        if (!empty($data['order_type']) && in_array($data['order_type'], ['desc', 'asc'])) {
            $parse['orderType'] = trim($data['order_type']);
        }
        if (!empty($data['tree_level'])) {
            $parse['treeLevel'] = intval($data['tree_level']);
        }
        $parse['customData'] = [];
        if (!empty($data['custom_data'])) {
            $parse['customData'] = Str::deepJsonDecode($data['custom_data']);
        }
        if (!empty($data['list_config'])) {
            foreach (explode(',', $data['list_config']) as $key) {
                if (!$key) {
                    continue;
                }
                $list_config[$key] = true;
            }
            if (!empty($list_config['sortable'])) {
                $parse['sortable'] = true;
                unset($list_config['sortable']);
            }
            $parse['customData'] = array_merge($parse['customData'], $list_config);
        }

        // business add
        if (!empty($data['business_list_config'])) {
            foreach (explode(',', $data['business_list_config']) as $key) {
                if (!$key) {
                    continue;
                }
                $list_config['business_' . $key] = true;
            }
            if (!empty($list_config['business_sortable'])) {
                $parse['businessSortable'] = true;
                unset($list_config['business_sortable']);
            }
            $parse['customData'] = array_merge($parse['customData'], $list_config);
        }

        $parse['relationLink'] = self::getRelationLinkAttr($data);
        $parse['validate'] = self::getValidateAttr($data);
        $parse['form'] = self::getFormAttr($data);
        $parse['formTrigger'] = self::getFormTriggerAttr($data);
        $parse['formGroup'] = self::getFormGroupAttr($data);

        $parse['adminCustomTab'] = self::getAdminCustomTab($data, $parse);

        $parse['businessValidate'] = self::getBusinessValidateAttr($data);
        $parse['businessForm'] = self::getBusinessFormAttr($data);
        $parse['businessFormTrigger'] = self::getBusinessFormTriggerAttr($data);

        $parse['businessCustomTab'] = self::getBusinessCustomTab($data, $parse);

        $parse['tableColumns'] = self::getTableColumnsCache($data);
        return $parse;
    }

    public static function getItemFromDb(array $item)
    {
        if (!isset($item['Field'])) {
            return [];
        }
        $result['field'] = $item['Field'];
        if (!empty($item['Type'])) {
            $item['Type'] = strtolower($item['Type']);
            $length_start = strpos($item['Type'], '(');
            $length_end = strpos($item['Type'], ')');
            if ($length_start > 0 && $length_end > $length_start) {
                $result['length'] = substr($item['Type'], $length_start + 1, $length_end - $length_start - 1);
            }

            if (false === $length_start) {
                $result['type'] = strtoupper($item['Type']);
            } else {
                $result['type'] = strtoupper(substr($item['Type'], 0, $length_start));
            }

            if (!(false === strpos($item['Type'], 'unsigned'))) {
                $result['is_unsigned'] = 1;
            }
        }

        if (isset($item['Null'])) {
            $result['is_not_null'] = strtoupper($item['Null']) === 'NO' ? 1 : 0;
        }

        if (!empty($item['Key'])) {
            $item['Key'] = strtoupper($item['Key']);
            if ($item['Key'] === 'UNI') {
                $result['index'] = 'unique';
            } elseif ($item['Key'] === 'MUL') {
                $result['index'] = 'index';
            }
        }
        if (!empty($item['Extra'])) {
            $item['Extra'] = strtolower($item['Extra']);
            if ($item['Extra'] === 'auto_increment') {
                $result['is_ai'] = 1;
                $result['default'] = 'none';
            }
        }

        if (isset($item['Default']) && !isset($result['default'])) {
            $result['default'] = $item['Default'];
        }

        if (empty($item['Comment'])) {
            $result['name'] = $result['field'];
            return $result;
        }
        $comments = explode('|', $item['Comment']);
        $result['name'] = array_shift($comments);
        if (empty($comments)) {
            return $result;
        }
        $map = [
            'form', 'form_foreign', 'form_item_attrs', 'form_tag_attrs', 'form_options', 'form_trigger',
            'list', 'list_attrs', 'list_filter', 'list_filter_attrs', 'list_filter_tag_attrs'
        ];
        foreach ($comments as $value) {
            $value = str_replace('：', ':', $value);
            list($arrt, $val) = explode(':', $value . ':PLACEHOLDER');
            if (in_array($arrt, $map) && $val != 'PLACEHOLDER') {
                $result[$arrt] = Str::deepJsonDecode($val);
            }
        }
        return $result;
    }

    protected static function getValidateAttr(array $data = [])
    {
        self::$requiredFields = [];
        if (empty($data['Field'])) {
            return [];
        }

        $validate = [];
        foreach ($data['Field'] as $item) {
            if (empty($item['validate'])) {
                continue;
            }
            $item['validate'] = Str::deepJsonDecode($item['validate']);
            if (is_array($item['validate'])) {
                foreach ($item['validate'] as $v) {
                    $one = [];
                    if (empty($v['rule'])) {
                        continue;
                    }
                    if ($v['rule'] == 'require' && empty($v['on'])) {
                        array_push(self::$requiredFields, $item['field']);
                    }
                    $one['rule'] = [$v['rule']];
                    if ($v['args'] !== '') {
                        array_push($one['rule'], $v['args']);
                    }
                    if ($v['on']) {
                        $one['on'] = $v['on'];
                    }
                    if ($v['message']) {
                        $one['message'] = $v['message'];
                    }
                    $validate[$item['field']][] = $one;
                }
            }
        }
        return $validate;
    }

    protected static function getBusinessValidateAttr(array $data = [])
    {
        self::$requiredFields = [];
        if (empty($data['Field'])) {
            return [];
        }

        $validate = [];
        foreach ($data['Field'] as $item) {
            if (empty($item['business_validate'])) {
                continue;
            }
            $item['business_validate'] = Str::deepJsonDecode($item['business_validate']);
            if (is_array($item['business_validate'])) {
                foreach ($item['business_validate'] as $v) {
                    $one = [];
                    if (empty($v['rule'])) {
                        continue;
                    }
                    $one['rule'] = [$v['rule']];
                    if ($v['rule'] == 'require' && empty($v['on'])) {
                        array_push(self::$requiredFields, $item['field']);
                    }
                    if ($v['args'] !== '') {
                        array_push($one['rule'], $v['args']);
                    }
                    if ($v['on']) {
                        $one['on'] = $v['on'];
                    }
                    if ($v['message']) {
                        $one['message'] = $v['message'];
                    }
                    $validate[$item['field']][] = $one;
                }
            }
        }
        return $validate;
    }

    protected static function getFormGroupAttr(array $data = [])
    {
        if (empty($data['form_group'])) {
            return [];
        }
        return Str::deepJsonDecode($data['form_group']);
    }

    protected static function getFormTriggerAttr(array $data = [])
    {
        if (empty($data['Field'])) {
            return [];
        }
        $formTrigger = [];
        foreach ($data['Field'] as $item) {
            if (empty($item['form_trigger'])) {
                continue;
            }
            $trigger = Str::deepJsonDecode($item['form_trigger']);
            if (!is_array($trigger)) {
                continue;
            }
            $formTrigger[$item['field']] = $trigger;
        }
        return $formTrigger;
    }

    protected static function getBusinessFormTriggerAttr(array $data = [])
    {
        if (empty($data['Field'])) {
            return [];
        }
        $formTrigger = [];
        foreach ($data['Field'] as $item) {
            if (empty($item['business_form_foreign'])) {
                continue;
            }
            $trigger = Str::deepJsonDecode($item['business_form_foreign']);
            if (!is_array($trigger)) {
                continue;
            }
            $formTrigger[$item['field']] = $trigger;
        }
        return $formTrigger;
    }

    protected static function getFormAttr(array $data = [])
    {
        if (empty($data['Field'])) {
            return [];
        }
        $form = [];
        foreach ($data['Field'] as $item) {
            if (count($item) <= 2) {
                $form[$item['field']] = [
                    'type' => 'string',
                    'name' => $item['name'] ?: Str::studly($item['field']),
                    'form' => 'text',
                    'list' => 0
                ];
                continue;
            }
            $item_form = [];
            $item_attrs = !empty($item['form_item_attrs']) ? Str::deepJsonDecode($item['form_item_attrs']) : [];
            $item_form['type'] = $item_attrs['type'] ?? self::getFormTypeAttr($item);

            if (empty($item['form']) || $item['form'] == 'none') {
                $item['form'] = 0;
            }
            $item_form['name'] = $item['name'];
            $item_form['elem'] = $item['form'];
            if (isset($item['modify_form']) && $item['modify_form'] != '') {
                $item_form['modify_elem'] = $item['modify_form'];
                if (empty($item_form['modify_elem']) || $item_form['modify_elem'] == 'none') {
                    $item_form['modify_elem'] = 0;
                }
            }

            if (!empty($item['form_foreign'])) {
                $item_form['foreign'] = $item['form_foreign'];
            }
            $item_form['is_contribute'] = !empty($item['is_contribute']);

            if (in_array($item['field'], self::$requiredFields) && !isset($item_attrs['require'])) {
                $item_form['require'] = true;
            }

            if (!empty($item['form_options'])) {
                $item['form_options'] = Str::deepJsonDecode($item['form_options']);
                $item_form['options'] = is_array($item['form_options']) ? $item['form_options'] : [];
                if (empty($item_form['options'])) {
                    unset($item_form['options']);
                }
            }
            if (!empty($item['form_tag_attrs'])) {
                $item['form_tag_attrs'] = Str::deepJsonDecode($item['form_tag_attrs']);
                $item_form['attrs'] = is_array($item['form_tag_attrs']) ? $item['form_tag_attrs'] : [];
            }
            if (!empty($item['form_upload'])) {
                $item['form_upload'] = Str::deepJsonDecode($item['form_upload']);
                $item_form['upload'] = is_array($item['form_upload']) ? $item['form_upload'] : [];
            }

            if (isset($item['list']) && $item['list'] !== '0') {
                $item['list'] = trim($item['list']);
                if ($item['list']) {
                    $item_form['list'] = $item['list'];
                }
                if ($item['list_attrs']) {
                    $item['list_attrs'] = Str::deepJsonDecode($item['list_attrs']);
                    $item['list_attrs'] = is_array($item['list_attrs']) ? $item['list_attrs'] : [];
                    if (!empty($item_form['list'])) {
                        $item_form['list'] = array_merge(['templet' => $item_form['list']], $item['list_attrs']);
                    } else {
                        $item_form['list'] = $item['list_attrs'];
                    }
                }
            } else {
                $item_form['list'] = 0;
            }

            if (!empty($item['list_filter'])) {
                if ($item['list_filter'] === '1') {
                    $item_form['list_filter'] = true;
                } else {
                    $item_form['list_filter'] = trim($item['list_filter']);
                }

                $item['list_filter_attrs'] = Str::deepJsonDecode($item['list_filter_attrs'] ?? '');
                $item['list_filter_tag_attrs'] = Str::deepJsonDecode($item['list_filter_tag_attrs'] ?? '');
                if (!empty($item['list_filter_attrs']) || !empty($item['list_filter_tag_attrs'])) {
                    if (is_string($item_form['list_filter'])) {
                        $item_form['list_filter'] = [
                            'templet' => $item_form['list_filter']
                        ];
                    } else {
                        $item_form['list_filter'] = [];
                    }
                    if (!empty($item['list_filter_attrs']) && is_array($item['list_filter_attrs'])) {
                        $item_form['list_filter'] = array_merge($item_form['list_filter'], $item['list_filter_attrs']);
                    }
                    if (!empty($item['list_filter_tag_attrs']) && is_array($item['list_filter_tag_attrs'])) {
                        $item_form['list_filter'] = array_merge($item_form['list_filter'], ['attrs' => $item['list_filter_tag_attrs']]);
                    }
                }
            }
            if (isset($item['detail']) && $item['detail'] !== '0') {
                $item['detail'] = trim($item['detail']);
                if ($item['detail']) {
                    $item_form['detail'] = $item['detail'];
                }
                if ($item['detail_attrs']) {
                    $item['detail_attrs'] = Str::deepJsonDecode($item['detail_attrs']);
                    $item['detail_attrs'] = is_array($item['detail_attrs']) ? $item['detail_attrs'] : [];
                    if (!empty($item_form['detail'])) {
                        $item_form['detail'] = array_merge(['templet' => $item_form['detail']], $item['detail_attrs']);
                    } else {
                        $item_form['detail'] = $item['detail_attrs'];
                    }
                }
            } else {
                $item_form['detail'] = 0;
            }
            $item_form = array_merge($item_form, $item_attrs);
            $form[$item['field']] = $item_form;
        }
        return $form;
    }

    protected static function getBusinessFormAttr(array $data = [])
    {
        if (empty($data['Field'])) {
            return [];
        }
        $form = [];
        foreach ($data['Field'] as $item) {
            if (count($item) <= 2) {
                $form[$item['field']] = [
                    'type' => 'string',
                    'name' => $item['name'] ?: Str::studly($item['field']),
                    'form' => 'text',
                    'list' => 0
                ];
                continue;
            }
            $item_form = [];
            $item_attrs = !empty($item['business_form_item_attrs']) ? Str::deepJsonDecode($item['business_form_item_attrs']) : [];
            $item_form['type'] = $item_attrs['type'] ?? self::getFormTypeAttr($item);

            if (empty($item['business_form']) || $item['business_form'] == 'none') {
                $item['business_form'] = 0;
            }
            $item_form['name'] = $item['name'];
            $item_form['elem'] = $item['business_form'];
            if (isset($item['business_modify_form']) && $item['business_modify_form'] != '') {
                $item_form['modify_elem'] = $item['business_modify_form'];
                if (empty($item_form['modify_elem']) || $item_form['modify_elem'] == 'none') {
                    $item_form['modify_elem'] = 0;
                }
            }

            if (!empty($item['business_form_foreign'])) {
                $item_form['foreign'] = $item['business_form_foreign'];
            }
            $item_form['is_contribute'] = !empty($item['is_contribute']);

            if (in_array($item['field'], self::$requiredFields) && !isset($item_attrs['require'])) {
                $item_form['require'] = true;
            }


            if (!empty($item['business_form_options'])) {
                $item['business_form_options'] = Str::deepJsonDecode($item['business_form_options']);
                $item_form['options'] = is_array($item['business_form_options']) ? $item['business_form_options'] : [];
                if (empty($item_form['options'])) {
                    unset($item_form['options']);
                }
            }
            if (!empty($item['business_form_tag_attrs'])) {
                $item['business_form_tag_attrs'] = Str::deepJsonDecode($item['business_form_tag_attrs']);
                $item_form['attrs'] = is_array($item['business_form_tag_attrs']) ? $item['business_form_tag_attrs'] : [];
            }
            if (!empty($item['business_form_upload'])) {
                $item['business_form_upload'] = Str::deepJsonDecode($item['business_form_upload']);
                $item_form['upload'] = is_array($item['business_form_upload']) ? $item['business_form_upload'] : [];
            }

            if (isset($item['business_list']) && $item['business_list'] !== '0') {
                $item['business_list'] = trim($item['business_list']);
                if (!empty($item['business_list'])) {
                    $item_form['list'] = $item['business_list'];
                }
                if (!empty($item['business_list_attrs'])) {
                    $item['business_list_attrs'] = Str::deepJsonDecode($item['business_list_attrs']);
                    $item['business_list_attrs'] = is_array($item['business_list_attrs']) ? $item['business_list_attrs'] : [];
                    if (!empty($item_form['list'])) {
                        $item_form['list'] = array_merge(['templet' => $item_form['list']], $item['business_list_attrs']);
                    } else {
                        $item_form['list'] = $item['business_list_attrs'];
                    }
                }
            } else {
                $item_form['list'] = 0;
            }

            if (!empty($item['business_list_filter'])) {
                if ($item['business_list_filter'] === '1') {
                    $item_form['list_filter'] = true;
                } else {
                    $item_form['list_filter'] = trim($item['business_list_filter']);
                }

                $item['business_list_filter_attrs'] = Str::deepJsonDecode($item['business_list_filter_attrs'] ?? '');
                $item['business_list_filter_tag_attrs'] = Str::deepJsonDecode($item['business_list_filter_tag_attrs' ?? '']);
                if (!empty($item['business_list_filter_attrs']) || !empty($item['business_list_filter_tag_attrs'])) {
                    if (is_string($item_form['list_filter'])) {
                        $item_form['list_filter'] = [
                            'templet' => $item_form['list_filter']
                        ];
                    } else {
                        $item_form['list_filter'] = [];
                    }
                    if (!empty($item['business_list_filter_attrs']) && is_array($item['business_list_filter_attrs'])) {
                        $item_form['list_filter'] = array_merge($item_form['list_filter'], $item['business_list_filter_attrs']);
                    }
                    if (!empty($item['business_list_filter_tag_attrs']) && is_array($item['business_list_filter_tag_attrs'])) {
                        $item_form['list_filter'] = array_merge($item_form['list_filter'], ['attrs' => $item['business_list_filter_tag_attrs']]);
                    }
                }
            }
            if (isset($item['business_detail']) && $item['business_detail'] !== '0') {
                $item['business_detail'] = trim($item['business_detail']);
                if ($item['business_detail']) {
                    $item_form['detail'] = $item['business_detail'];
                }
                if (!empty($item['business_detail_attrs'])) {
                    $item['business_detail_attrs'] = Str::deepJsonDecode($item['business_detail_attrs']);
                    $item['business_detail_attrs'] = is_array($item['business_detail_attrs']) ? $item['business_detail_attrs'] : [];
                    if (!empty($item_form['detail'])) {
                        $item_form['detail'] = array_merge(['templet' => $item_form['detail']], $item['business_detail_attrs']);
                    } else {
                        $item_form['detail'] = $item['business_detail_attrs'];
                    }
                }
            } else {
                $item_form['detail'] = 0;
            }

            $item_form = array_merge($item_form, $item_attrs);
            $form[$item['field']] = $item_form;
        }
        return $form;
    }

    protected static function getFormTypeAttr(array $item, array $itemForm = [])
    {
        if (empty($item['is_field'])) {
            return 'none';
        }
        if (in_array($item['form'], ['date', 'datetime']) && in_array($item['type'], ['INT', 'BIGINT'])) {
            return 'time';
        }
        if (in_array($item['type'], ['TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'BOOLEAN'])) {
            return 'integer';
        } elseif (in_array($item['type'], ['FLOAT', 'DOUBLE', 'DECIMAL'])) {
            return 'float';
        } elseif ($item['type'] == 'JSON') {
            return 'json';
        }
        if (in_array($item['form'], ['ckeditor', 'nkeditor', 'ueditor', 'tinymce', 'wangeditor'])) {
            return 'html';
        } elseif (in_array($item['form'], ['array', 'keyvalue', 'multiattrs', 'checkbox', 'json'])) {
            return 'array';
        }

        return 'string';
    }

    protected static function getAdminCustomTab(array $data = [], array $parse = [])
    {
        $tool_bar = [];
        if (!empty($data['admin_tool_bar'])) {
            $admin_tool_bar = Str::deepJsonDecode($data['admin_tool_bar']);
            foreach ($admin_tool_bar as $item) {
                if (empty($item['name'])) {
                    continue;
                }
                if (empty($item['templet'])) {
                    unset($item['templet']);
                }
                if (empty($item['js_func'])) {
                    unset($item['js_func']);
                }
                if (empty($item['length'])) {
                    unset($item['length']);
                }
                $tool_bar[$item['name']] = $item;
            }
            foreach ($tool_bar as $key => &$item2) {
                if (empty($item2['parent'])) {
                    unset($item2['parent']);
                    continue;
                }
                if (isset($tool_bar[$item2['parent']])) {
                    $parent = $item2['parent'];
                    unset($item2['parent']);
                    $tool_bar[$parent]['children'] =
                        !empty($tool_bar[$parent]['children']) ?
                            $tool_bar[$parent]['children'] : [];
                    $tool_bar[$parent]['children'][] = $item2;
                }
                unset($tool_bar[$key]);
            }
            $tool_bar = array_values($tool_bar);
        }

        $item_tool_bar = [];
        if (!empty($data['admin_item_tool_bar'])) {
            $admin_tool_bar = Str::deepJsonDecode($data['admin_item_tool_bar']);

            foreach ($admin_tool_bar as $item) {
                if (empty($item['name'])) {
                    continue;
                }
                if (empty($item['templet'])) {
                    unset($item['templet']);
                }
                if (empty($item['js_func'])) {
                    unset($item['js_func']);
                }
                if (empty($item['length'])) {
                    unset($item['length']);
                }
                $item_tool_bar[$item['name']] = $item;
            }
            foreach ($item_tool_bar as $key => &$item3) {
                if (empty($item3['parent'])) {
                    unset($item3['parent']);
                    continue;
                }
                if (isset($item_tool_bar[$item3['parent']])) {
                    $parent = $item3['parent'];
                    unset($item3['parent']);
                    $item_tool_bar[$parent]['children'] =
                        !empty($item_tool_bar[$parent]['children']) ?
                            $item_tool_bar[$parent]['children'] : [];
                    $item_tool_bar[$parent]['children'][] = $item3;
                }
                unset($item_tool_bar[$key]);
            }
            $item_tool_bar = array_values($item_tool_bar);
        }

        $siderbar = [];
        if (!empty($data['admin_siderbar'])) {
            $admin_siderbar = Str::deepJsonDecode($data['admin_siderbar']);
            if ($admin_siderbar) {
                foreach ($admin_siderbar as $bar) {
                    if (!array_key_exists($bar, $parse['relationLink'])) {
                        continue;
                    }
                    $siderbar[] = ['foreign' => Str::studly($bar)];
                }
            }
        }
        $table = [];
        if (!empty($data['admin_table_attrs'])) {
            $table = Str::deepJsonDecode($data['admin_table_attrs']);
        }

        if (isset($data['admin_item_checkbox']) && $data['admin_item_checkbox'] === 'false') {
            $checkbox = false;
        } elseif (empty($data['admin_item_checkbox'])) {
            $checkbox = 'checkbox';
        } else {
            $checkbox = $data['admin_item_checkbox'] ?? 'checkbox';
        }

        $toolbar_options = [];
        if (!empty($data['admin_item_toolbar_options'])) {
            $options = Str::deepJsonDecode($data['admin_item_toolbar_options']);
            if (!empty($options)) {
                $options = $options[0];
                if (!empty($options['is_show'])) {
                    unset($options['is_show']);
                    if (!empty($options['more']) && is_array($options['more'])) {
                        if (isset($options['more']['width'])) {
                            $options['more']['width'] = intval($options['more']['width']) - 4;
                        }
                        $options = array_merge($options, $options['more']);
                    }
                    if (isset($options['more'])) {
                        unset($options['more']);
                    }
                    $toolbar_options = $options;
                } else {
                    $toolbar_options = false;
                }
            }
        }
        $counter = [];
        if (!empty($data['admin_counter'])) {
            $counter = Str::deepJsonDecode($data['admin_counter']);
        }
        $total_row = [];
        if (!empty($data['admin_total_row'])) {
            $total_row = Str::deepJsonDecode($data['admin_total_row']);
        }

        $is_remove_pk = 0;
        if (isset($data['admin_is_remove_pk'])) {
            $is_remove_pk = $data['admin_is_remove_pk'];
        }

        $filter_model = '';
        if (!empty($data['admin_filter_model'])) {
            $filter_model = $data['admin_filter_model'];
        }

        $list_with = [];
        if (!empty($data['admin_list_with'])) {
            $list_with = Str::deepJsonDecode($data['admin_list_with']);
            foreach ($list_with as $key => &$value) {
                if (is_string($value)) {
                    $value = [];
                }
            }
        }
        $list_fields = [];
        if (!empty($data['admin_list_fields'])) {
            $a = Str::deepJsonDecode($data['admin_list_fields']);
            foreach ($a as $item) {
                if (empty($item['field'])) {
                    continue;
                }
                $list_fields[$item['field']] = [];
                if (!empty($item['title'])) {
                    $list_fields[$item['field']]['title'] = $item['title'];
                }
                if (!empty($item['templet'])) {
                    $list_fields[$item['field']]['templet'] = $item['templet'];
                }
                if (!empty($item['attr'])) {
                    $list_fields[$item['field']] = array_merge($list_fields[$item['field']], $item['attr']);
                }
            }
        }

        $list_filters = [];
        if (!empty($data['admin_list_filters'])) {
            $a = Str::deepJsonDecode($data['admin_list_filters']);
            foreach ($a as $item) {
                if (empty($item['field'])) {
                    continue;
                }
                $list_filters[$item['field']] = [];
                if (!empty($item['title'])) {
                    $list_filters[$item['field']]['title'] = $item['title'];
                }
                if (!empty($item['templet'])) {
                    $list_filters[$item['field']]['templet'] = $item['templet'];
                }
                if (!empty($item['attr'])) {
                    $list_filters[$item['field']] = array_merge($list_filters[$item['field']], $item['attr']);
                }
            }
        }

        $return['tool_bar'] = $tool_bar;
        $return['item_tool_bar'] = $item_tool_bar;
        $return['siderbar'] = $siderbar;
        $return['table'] = $table;
        $return['checkbox'] = $checkbox;
        $return['toolbar_options'] = $toolbar_options;
        $return['counter'] = is_array($counter) ? $counter : [];
        $return['total_row'] = $total_row;
        $return['is_remove_pk'] = $is_remove_pk;
        $return['filter_model'] = $filter_model;
        $return['list_with'] = $list_with;
        $return['list_fields'] = $list_fields;
        $return['list_filters'] = $list_filters;
        return $return;
    }

    protected static function getBusinessCustomTab(array $data = [], array $parse = [])
    {
        $tool_bar = [];
        if (!empty($data['business_tool_bar'])) {
            $admin_tool_bar = Str::deepJsonDecode($data['business_tool_bar']);
            foreach ($admin_tool_bar as $item) {
                if (empty($item['name'])) {
                    continue;
                }
                if (empty($item['templet'])) {
                    unset($item['templet']);
                }
                if (empty($item['js_func'])) {
                    unset($item['js_func']);
                }
                if (empty($item['length'])) {
                    unset($item['length']);
                }
                $tool_bar[$item['name']] = $item;
            }
            foreach ($tool_bar as $key => &$item2) {
                if (empty($item2['parent'])) {
                    unset($item2['parent']);
                    continue;
                }
                if (isset($tool_bar[$item2['parent']])) {
                    $parent = $item2['parent'];
                    unset($item2['parent']);
                    $tool_bar[$parent]['children'] =
                        !empty($tool_bar[$parent]['children']) ?
                            $tool_bar[$parent]['children'] : [];
                    $tool_bar[$parent]['children'][] = $item2;
                }
                unset($tool_bar[$key]);
            }
            $tool_bar = array_values($tool_bar);
        }

        $item_tool_bar = [];
        if (!empty($data['business_item_tool_bar'])) {
            $admin_tool_bar = Str::deepJsonDecode($data['business_item_tool_bar']);

            foreach ($admin_tool_bar as $item) {
                if (empty($item['name'])) {
                    continue;
                }
                if (empty($item['templet'])) {
                    unset($item['templet']);
                }
                if (empty($item['js_func'])) {
                    unset($item['js_func']);
                }
                if (empty($item['length'])) {
                    unset($item['length']);
                }
                $item_tool_bar[$item['name']] = $item;
            }
            foreach ($item_tool_bar as $key => &$item3) {
                if (empty($item3['parent'])) {
                    unset($item3['parent']);
                    continue;
                }
                if (isset($item_tool_bar[$item3['parent']])) {
                    $parent = $item3['parent'];
                    unset($item3['parent']);
                    $item_tool_bar[$parent]['children'] =
                        !empty($item_tool_bar[$parent]['children']) ?
                            $item_tool_bar[$parent]['children'] : [];
                    $item_tool_bar[$parent]['children'][] = $item3;
                }
                unset($item_tool_bar[$key]);
            }
            $item_tool_bar = array_values($item_tool_bar);
        }

        $siderbar = [];
        if (!empty($data['business_siderbar'])) {
            $admin_siderbar = Str::deepJsonDecode($data['business_siderbar']);
            if ($admin_siderbar) {
                foreach ($admin_siderbar as $bar) {
                    if (!array_key_exists($bar, $parse['relationLink'])) {
                        continue;
                    }
                    $siderbar[] = ['foreign' => Str::studly($bar)];
                }
            }
        }
        $table = [];
        if (!empty($data['business_table_attrs'])) {
            $table = Str::deepJsonDecode($data['business_table_attrs']);
        }

        if (isset($data['business_item_checkbox']) && $data['business_item_checkbox'] === 'false') {
            $checkbox = false;
        } elseif (empty($data['business_item_checkbox'])) {
            $checkbox = 'checkbox';
        } else {
            $checkbox = $data['business_item_checkbox'] ?? 'checkbox';
        }

        $toolbar_options = [];
        if (!empty($data['business_item_toolbar_options'])) {
            $options = Str::deepJsonDecode($data['business_item_toolbar_options']);
            if (!empty($options)) {
                $options = $options[0];
                if (!empty($options['is_show'])) {
                    unset($options['is_show']);
                    if (!empty($options['more']) && is_array($options['more'])) {
                        if (isset($options['more']['width'])) {
                            $options['more']['width'] = intval($options['more']['width']) - 4;
                        }
                        $options = array_merge($options, $options['more']);
                    }
                    if (isset($options['more'])) {
                        unset($options['more']);
                    }
                    $toolbar_options = $options;
                } else {
                    $toolbar_options = false;
                }
            }
        }
        $counter = [];
        if (!empty($data['business_counter'])) {
            $counter = Str::deepJsonDecode($data['business_counter']);
        }
        $total_row = [];
        if (!empty($data['business_total_row'])) {
            $total_row = Str::deepJsonDecode($data['business_total_row']);
        }

        $is_remove_pk = 0;
        if (isset($data['business_is_remove_pk'])) {
            $is_remove_pk = $data['business_is_remove_pk'];
        }

        $filter_model = '';
        if (!empty($data['business_filter_model'])) {
            $filter_model = $data['business_filter_model'];
        }

        $list_with = [];
        if (!empty($data['business_list_with'])) {
            $list_with = Str::deepJsonDecode($data['business_list_with']);
            foreach ($list_with as $key => &$value) {
                if (is_string($value)) {
                    $value = [];
                }
            }
        }
        $list_fields = [];
        if (!empty($data['business_list_fields'])) {
            $a = Str::deepJsonDecode($data['business_list_fields']);
            foreach ($a as $item) {
                if (empty($item['field'])) {
                    continue;
                }
                $list_fields[$item['field']] = [];
                if (!empty($item['title'])) {
                    $list_fields[$item['field']]['title'] = $item['title'];
                }
                if (!empty($item['templet'])) {
                    $list_fields[$item['field']]['templet'] = $item['templet'];
                }
                if (!empty($item['attr'])) {
                    $list_fields[$item['field']] = array_merge($list_fields[$item['field']], $item['attr']);
                }
            }
        }

        $list_filters = [];
        if (!empty($data['business_list_filters'])) {
            $a = Str::deepJsonDecode($data['business_list_filters']);
            foreach ($a as $item) {
                if (empty($item['field'])) {
                    continue;
                }
                $list_filters[$item['field']] = [];
                if (!empty($item['title'])) {
                    $list_filters[$item['field']]['title'] = $item['title'];
                }
                if (!empty($item['templet'])) {
                    $list_filters[$item['field']]['templet'] = $item['templet'];
                }
                if (!empty($item['attr'])) {
                    $list_filters[$item['field']] = array_merge($list_filters[$item['field']], $item['attr']);
                }
            }
        }
        $return['tool_bar'] = $tool_bar;
        $return['item_tool_bar'] = $item_tool_bar;
        $return['siderbar'] = $siderbar;
        $return['table'] = $table;
        $return['checkbox'] = $checkbox;
        $return['toolbar_options'] = $toolbar_options;
        $return['counter'] = is_array($counter) ? $counter : [];
        $return['total_row'] = $total_row;
        $return['is_remove_pk'] = $is_remove_pk;
        $return['filter_model'] = $filter_model;
        $return['list_with'] = $list_with;
        $return['list_fields'] = $list_fields;
        $return['list_filters'] = $list_filters;
        return $return;
    }

    protected static function getRelationLinkAttr(array $data = [])
    {
        if (empty($data['relation_link'])) {
            return [];
        }
        $link = Str::deepJsonDecode($data['relation_link']);
        if (!is_array($link)) {
            return [];
        }
        $relation = [];
        foreach ($link as $item) {
            if (empty($item['key']) || empty($item['type'])) {
                continue;
            }
            $item['key'] = Str::studly($item['key']);
            $relation[$item['key']]['type'] = $item['type'];
            if (!empty($item['foreign'])) {
                $relation[$item['key']]['foreign'] = $item['foreign'];
            }
            if (!empty($item['foreign_key'])) {
                $relation[$item['key']]['foreignKey'] = $item['foreign_key'];
            }
            if (!empty($item['more'])) {
                $item['more'] = array_diff_key($item['more'], ['type' => '', 'foreign' => '', 'foreignKey' => '']);
                $relation[$item['key']] = array_merge($relation[$item['key']], $item['more']);
            }
        }
        return $relation;
    }

    protected static function getTableColumnsCache(array $data = [])
    {
        if (empty($data['Field'])) {
            return [];
        }
        $columns = [];
        $defaultMap = [
            '0' => 0,
            'NULL' => null
        ];
        foreach ($data['Field'] as $field => $item) {
            if (!isset($item['is_field'])) {
                continue;
            }
            $columns[$field] = [
                'is_field' => $item['is_field'],
                'type' => $item['type'],
                'length' => $item['length'],
                'default' => array_key_exists($item['default'], $defaultMap)? $defaultMap[$item['default']]: $item['default'],
                'is_not_null' => $item['default'] === 'NULL' || !$item['is_not_null']? 0: 1,
                'is_unsigned' => $item['is_unsigned'],
                'is_ai' => $item['is_ai'],
                'index' => $item['index']
            ];
        }
        return $columns;
    }
}