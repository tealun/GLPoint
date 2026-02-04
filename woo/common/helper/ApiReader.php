<?php
declare (strict_types = 1);

namespace woo\common\helper;

use woo\common\Annotation;

class ApiReader
{
    protected $reader;
    protected $namespace;
    protected $reflect;
    protected $params = [];
    protected $filterParams = [];

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
        $this->reflect = reflect($this->namespace);
        $this->reader = $this->getReader();
    }

    public function getActionHeader(string $action, bool $is_recursion = true)
    {
        if (!$this->reflect) {
            return [];
        }
        $result = $this->reader->getMethodAnnotations($this->reflect, 'Header', $action, $is_recursion);
        if (!$result) {
            return [];
        }
        $return = [];
        foreach ($result as $item) {
            $item = (array)$item;
            if ($item['target']) {
                list($namespace, $act) = explode('::', $item['target']) + ['', ''];
                if (!$namespace || !$act) {
                    continue;
                }
                $reader = new self($namespace);
                $return = array_merge($return, $reader->getActionHeader($act));
                continue;
            }
            $item['name'] = $item['name'] ?: $item['value'];
            $item['title'] = $item['title'] ?: Str::studly($item['name']);
            $return[] = $item;
        }
        return $return;
    }

    public function getActionParam(string $action, bool $is_recursion = true, $param = [])
    {
        if (!$this->reflect) {
            return [];
        }
        if (\woo\common\helper\Str::startsWith($this->reflect->name, "app\\common\\model\\")) {
            if (!get_model_name($this->reflect->name)) {
                return [];
            }
            $model  = model($this->reflect->name);
            $fields = empty($param['field']) ? array_keys($model->form) : (is_string($param['field']) ? explode('|', $param['field']) : $param['field']);
            if (!empty($param['withoutField'])) {
                $without = is_string($param['withoutField']) ? explode('|', $param['withoutField']) : $param['withoutField'];
                $fields = array_diff($fields, $without);
            }
            $result = [];
            foreach ($fields as $key => $field) {
                $require = false;
                if (isset($model->validate[$field])) {
                    foreach ($model->validate[$field] as $rule) {
                        if (isset($rule['rule'][0]) && $rule['rule'][0] == 'require') {
                            $require = true;
                            break;
                        }
                    }
                }
                $type = $model->form[$field]['type'] ?? 'string';
                if (in_array($type, ['time', 'integer'])) {
                    $type =  'int';
                } elseif (in_array($type, ['blob', 'blob.array', 'text', 'html'])) {
                    $type = 'string';
                } elseif ($type == 'none') {
                    continue;
                }
                $result[] = [
                    'type' => $type,
                    'name' => $field,
                    'title' => $model->form[$field]['name'] ?? Str::studly($field),
                    'require' => $require
                ];
            }
            return $result;
        }
        $result = $this->reader->getMethodAnnotations($this->reflect, 'Param', $action, $is_recursion);
        $this->params = [];
        $this->filterParams = [];
        if (!$result) {
            return [];
        }
        foreach ($result as $item) {
            $this->params[$item->value] = (array)$item;
        }
        $return = [];
        foreach ($this->params as $key => $item) {
            if (!empty($item['target']) && $item['type'] == 'string') {
                $return = array_merge($return, $this->parseParam($item));
                continue;
            }
            $return[] = $this->parseParam($item);
        }
        if ($this->filterParams) {
            $return = array_filter($return, function ($item) {
                if (isset($item['value']) && in_array($item['value'], $this->filterParams)) {
                    return false;
                }
                return true;
            });
        }
        return $return;
    }

    protected function parseParam($param)
    {
        $param = (array)$param;
        if (!empty($param['target']) && $param['type'] == 'string') {
            list($namespace, $action) = explode('::', $param['target']) + ['', ''];
            $reader = new self($namespace);
            return $reader->getActionParam($action, true, $param);
        }
        if (in_array($param['type'], ['object', 'array']) && $param['params']) {
            if (is_array($param['params'])) {
                $arr =  [];
                foreach ($param['params'] as $p) {
                    if (isset($this->params[$p])) {
                        $arr[] =  $this->parseParam($this->params[$p]);
                        $this->filterParams[] = $p;
                    }
                }
                $param['params'] = $arr;
            } elseif (is_string($param['params'])) {
                list($namespace, $action) = explode('::', $param['params'])  + ['', ''];
                $reader = new self($namespace);
                $param['params'] = $reader->getActionParam($action, true, $param);
            }
        }
        $param['name'] = $param['name'] ?: $param['value'];
        $param['title'] = $param['title'] ?: Str::studly($param['name']);
        $param['require'] = !empty($param['require']) ? true: (isset($param['validate']) && is_array($param['validate']) && in_array('require', $param['validate']) ? true : false);
        return $param;
    }

    public function getActionReturn(string $action, bool $is_recursion = true, $param = [])
    {
        if (!$this->reflect) {
            return [];
        }
        if (\woo\common\helper\Str::startsWith($this->reflect->name, "app\\common\\model\\")) {
            if (!get_model_name($this->reflect->name)) {
                return [];
            }
            $model  = model($this->reflect->name);
            $fields = empty($param['field']) ? array_keys($model->form) : (is_string($param['field']) ? explode('|', $param['field']) : $param['field']);
            if (!empty($param['withoutField'])) {
                $without = is_string($param['withoutField']) ? explode('|', $param['withoutField']) : $param['withoutField'];
                $fields = array_diff($fields, $without);
            }
            $result = [];
            foreach ($fields as $key => $field) {
                $type = $model->form[$field]['type'] ?? 'string';
                if (in_array($type, ['time', 'integer'])) {
                    $type =  'int';
                } elseif (in_array($type, ['blob', 'blob.array', 'text', 'html'])) {
                    $type = 'string';
                } elseif ($type == 'none') {
                    continue;
                }
                $result[] = [
                    'type' => $type,
                    'name' => $field,
                    'title' => $model->form[$field]['name'] ?? Str::studly($field)
                ];
            }
            return $result;
        }
        $result = $this->reader->getMethodAnnotations($this->reflect, 'Returns', $action, $is_recursion);
        $this->params = [];
        $this->filterParams = [];
        if (!$result) {
            return [];
        }
        foreach ($result as $item) {
            $this->params[$item->value] = (array)$item;
        }
        $return = [];
        foreach ($this->params as $key => $item) {
            if (!empty($item['target']) && $item['type'] == 'string') {
                $return = array_merge($return, $this->parseRerurn($item));
                continue;
            }
            $return[] = $this->parseRerurn($item);
        }
        if ($this->filterParams) {
            $return = array_filter($return, function ($item) {
                if (isset($item['value']) && in_array($item['value'], $this->filterParams)) {
                    return false;
                }
                return true;
            });
        }
        return $return;
    }

    protected function parseRerurn($param)
    {
        $param = (array)$param;
        if (!empty($param['target']) && $param['type'] == 'string') {
            list($namespace, $action) = explode('::', $param['target']) + ['', ''];
            $reader = new self($namespace);
            return $reader->getActionReturn($action, true, $param);
        }
        if (in_array($param['type'], ['object', 'array']) && $param['params']) {
            if (is_array($param['params'])) {
                $arr =  [];
                foreach ($param['params'] as $p) {
                    if (isset($this->params[$p])) {
                        $arr[] =  $this->parseRerurn($this->params[$p]);
                        $this->filterParams[] = $p;
                    }
                }
                $param['params'] = $arr;
            } elseif (is_string($param['params'])) {
                list($namespace, $action) = explode('::', $param['params'])  + ['', ''];
                $reader = new self($namespace);
                $param['params'] = $reader->getActionReturn($action, true, $param);
            }
        }
        $param['name'] = $param['name'] ?: $param['value'];
        $param['title'] = $param['title'] ?: Str::studly($param['name']);
        return $param;
    }

    /**
     * 获取访问的ApiInfo注解信息
     * @param string $action
     * @param bool $is_recursion
     * @return array
     * @throws \ReflectionException
     */
    public function getActionApiInfo(string $action, bool $is_recursion = true)
    {
        if (!$this->reflect) {
            return [];
        }
        $result = $this->reader->getMethodAnnotation($this->reflect, 'ApiInfo', $action, $is_recursion);
        if (!$result) {
            return [];
        }
        $result = array_merge(['title' => $result->value], (array) $result);
        unset($result['value']);
        return $result;
    }

    public function getControllerInfo()
    {
        if (!$this->reflect) {
            return [];
        }
        $result = $this->reader->getClassAnnotation($this->reflect, 'Controller', true);
        if (!$result) {
            return [];
        }
        $result = (array)$result;
        if (!$result['title']) {
            $result['title'] = $result['value'];
        }
        unset($result['value']);
        return $result;
    }

    protected function getReader()
    {
        if ($this->reader) {
            return $this->reader;
        }
        $this->reader = new Annotation();
        return $this->reader;
    }
}