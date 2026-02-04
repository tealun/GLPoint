<?php
declare (strict_types=1);

namespace woo\common\builder\form\traits;

use woo\common\helper\Arr;
use woo\common\helper\Str;

trait ItemAttrs
{

    protected function getForeignAttr(array $options = [])
    {
        //$options['foreign_model'] =>
        //pr($options);
    }


    /**
     * 识别表单类型是可见的、还是隐藏的
     * @param array $options
     * @return bool
     */
    protected function getIsHiddenAttr(array $options = [])
    {
        $is_hidden = false;
        if (in_array($options['element'], $this->hiddenElementList, true)) {
            $is_hidden = true;
        }
        if (isset($options['is_hidden']) && true === $options['is_hidden']) {
            $is_hidden = true;
        }

        if (false === $is_hidden) {
            if (!in_array($options['real_field_name'], $this->visibleItems)) {
                array_push($this->visibleItems, $options['real_field_name']);
            }
        } else {
            if (!in_array($options['real_field_name'], $this->hiddenItems)) {
                array_push($this->hiddenItems, $options['real_field_name']);
            }
        }
        return $is_hidden;
    }

    /**
     * 渲染表单对应的html结构
     * @param array $options
     * @return mixed|string
     */
    protected function getHtmlAttr(array $options = [], $force = false)
    {
        if (!empty($options['html']) && false === $force) {
            return $options['html'];
        }
        if (empty($options['fetch'])) {
            return '';
        }
        $options['error'] = $this->getError($options['field_name']);
        return $this->fetch($options['fetch'], $options);
    }

    /**
     * 特殊情况下需要更新html
     * @param array $options
     * @return string
     */
    public static function renderHtml(array $options)
    {
        $item = new static();
        $item->addFormItem($options['field_name'], $options['element'], $options);
        return $item->fetch($options['fetch'], $options);
    }

    /**
     * 获取表单不同类型对应的模板地址
     * @param array $options
     * @return mixed|string
     */
    protected function getFetchAttr(array $options = [])
    {
        if (in_array($options['element'], [0, '0', ''], true)) {
            return '';
        }
        if (!empty($options['html'])) {
            return $options['html'];
        }

        $element = empty($options['use_element']) ? $options['element'] : $options['use_element'];

        if (empty($options['fetch'])) {
            // 默认加载当前应用下的：应用/view/form中查找表单模板
            $fetch = app()->getAppPath() . 'view' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR . $element . '.html';
            if (!is_file($fetch)) {
                // common/view/form中查找
                $fetch = app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR . $element . '.html';
            }
        } else {
            $fetch = $options['fetch'];
            if (!is_file($fetch)) {
                $fetch = app()->getBasePath() . $fetch;
            }
        }
        if (!is_file($fetch)) {
            $fetch =  woo_path() . 'common' . DIRECTORY_SEPARATOR . 'builder' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $element . '.html';
        }
        return $fetch;
    }

    /**
     * 获取表单项目需要使用的模板名称
     * @param array $options
     * @return string
     */
    protected function getUseElementAttr(array $options = [])
    {
        if (isset($options['use_element'])) {
            return $options['use_element'];
        }
        if (in_array($options['element'], ['datetime', 'time', 'year', 'month'])) {
            return 'date';
        }
        if (in_array($options['element'], ['selectfortree'])) {
            return 'select';
        }
        if (in_array($options['element'], ['xmselectfortree'])) {
            return 'xmselect';
        }
        if (in_array($options['element'], ['image', 'multiimage', 'file', 'multifile'])) {
            return 'upload';
        }
    }

    /**
     * 获取提示信息
     * @param array $options
     * @return mixed|string
     */
    protected function getTipAttr(array $options = [])
    {
        return $options['tip'] ?? '';
    }

    /**
     * 获取label标签html结构
     * @param array $options
     * @return string
     */
    protected function getLabelTagAttr(array $options = [])
    {
        $notNeedRequire = ['hidden', '0', 'format'];
        $require = false;
        if (($this->config['require_star'] ?? true) && !empty($options['require']) && !in_array($options['element'], $notNeedRequire)) {
            $require = true;
        }

        $for = !empty($options['label_for_id']) ? 'for="' . $options['label_for_id'] . '"' : '';
        return sprintf(
            '<label %s %s data-element="%s" title="%s">%s%s%s</label>',
            $for,
            empty($this->labelClass) ? '' : 'class="' . $this->labelClass .'"',
            $options['element'],
            $options['label'],
            $require ? '<span class="require-star tooltip" data-tip-text="必填项"></span>': '',
            $options['label'],
            $options['label_suffix']
        );
    }

    /**
     * 获取label分割符号
     * @param array $options
     * @return false|mixed|string|string[]|null
     */
    protected function getLabelSuffixAttr(array $options = [])
    {
        return $options['label_suffix'] ?? __('');
    }

    /**
     * 获取label标签的for属性值
     * @param array $options
     * @return string
     */
    protected function getLabelForIdAttr(array $options = [])
    {
        return !empty($options['attrs']['id']) ? $options['attrs']['id'] : '';
    }

    /**
     * 获取 表单项目 名称
     * @param array $options
     * @return string
     */
    protected function getLabelAttr(array $options = [])
    {
        if (empty($options['label'])) {
            return Str::studly($options['real_field_name'], 1);
        }
        return strval($options['label']);
    }

    /**
     * 获取表单标签上的属性
     * @param array $options
     * @return mixed
     */
    protected function getAttrsAttr(array $options = [])
    {

        if (isset($options['attrs']['value'])) {
            $this->setItemValue($options['real_field_name'], $options['attrs']['value']);
        }
        if (isset($this->data[$options['field_name']])) {
            $options['attrs']['value'] = $this->data[$options['field_name']];
        }

        if (!empty($options['attrs']['value']) && is_json($options['attrs']['value'])) {
            $options['attrs']['value'] = json_decode($options['attrs']['value'], true);
        }

        if (!isset($options['attrs']['value']) && isset($options['default'])) {
            $options['attrs']['value'] = $options['default'];
        }

        //name
        if (!isset($options['attrs']['name'])) {
            $options['attrs']['name'] = Str::snake($options['field_name']);
        }
        // 子表单name
        if ($this->parentField) {
            $options['attrs']['name'] = $this->parentField['field_name'] . '[0]' . '[' .$options['attrs']['name'] .']';
        }
        //id
        if (!isset($options['attrs']['id'])) {
            $options['attrs']['id'] = 'id_' . $options['attrs']['name'];
        }

        //class
        if (isset($options['attrs']['class']) && is_array($options['attrs']['class'])) {
            $options['attrs']['class'] = implode(' ', $options['attrs']['class']);
        }
        if (isset($options['attrs']['class'])) {
            $options['attrs']['class'] .= ' woo-element-' . $options['element'] . ' woo-element';
        } else {
            $options['attrs']['class'] = 'woo-element-' . $options['element'] . ' woo-element';
        }
        $options['attrs']['class'] .= ' element-item-' . $options['field_name'];

        return $options['attrs'];
    }
}