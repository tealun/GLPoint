<?php
declare (strict_types=1);

namespace woo\common\model\traits;


use woo\common\facade\Auth;
use woo\common\helper\Arr;
use woo\common\helper\Str;

trait Validate
{
    /**
     * 全局自动验证
     * @var bool
     */
    protected $autoValidate = true;

    /**
     * 定义数据验证规则
     * @var array
     */
    protected $validate = [];
    protected $businessValidate = [];

    /**
     * 验证选项
     * @var array
     */
    protected $validateOptions = [];

    /**
     * 默认选项
     * @var array
     */
    protected $defaultOptions = [
        'is' => true,// $autoValidate 为true 临时调整是否验证
        'batch' => true,// 是否批量验证
        'rule' => [],// 单独定义验证规则，完全自定义规则，不使用默认规则
        'mergeRule' => [],// 合并验证规则 一般用于临时增加规则，但默认的规则也需要
        'scene' => 'add',// 验证场景
        'instance' => null, // 验证器实例  如果传递了具体验证器实例  以验证器中定义的规则为准  按系统定义的规则都无效
        'only' => [], // 只验证列表中的字段 如果为true 只验证提交的字段
        'fieldsName' => []
    ];

    protected $validateCache = [];

    /**
     * 验证器实例
     * @var
     */
    protected $validateInstance;


    protected function beforeValidate()
    {
        $data   = $this->getData();
        //$fields = $this->getTableFields();
        $fields = array_merge(array_keys($this->form), $this->getTableFields());
        $types  = $this->getFieldsType();
        foreach ($fields as $field) {
            $value = null;
            if (isset($data[$field])) {
                $value = $data[$field];
            } else if ('string' === ($types[$field] ?? '') && $this->isInsert) {
                $this[$field] = $this->form[$field]['default'] ?? '';
                $value = $this[$field];
                if (!empty($this->field) && !in_array($field, $this->field)) {
                    $this->field = array_merge($this->field, [$field]);
                }
            }
            // SESSION中自动取值
            if (!empty($this->form[$field]['session']) && empty($value)) {
                $posNum = strpos($this->form[$field]['session'], '-');
                if (false === $posNum) {
                    $sessionKey = $this->form[$field]['session'];
                    $sessionData = Auth::user();
                } else {
                    $sessionName = substr($this->form[$field]['session'], 0, $posNum);
                    $sessionKey = substr($this->form[$field]['session'], $posNum + 1);
                    $sessionData = \think\facade\Session::has($sessionName) ? \think\facade\Session::get($sessionName) : [];
                }
                if (!empty($sessionData)) {
                    $sessionKey = explode('.', $sessionKey);
                    foreach ($sessionKey as $key) {
                        if (isset($sessionData[$key])) {
                            $sessionData = $value = $sessionData[$key];
                        } else {
                            $value = null;
                        }
                    }
                }
            }

            if (!isset($value)) {
                continue;
            }

            // 可以在form属性中给每个字段设置一个filter回调 用于最后的数据处理
            if (isset($this->form[$field]['filter'])) {
                $filter = $this->form[$field]['filter'];
                if (is_string($filter)) {
                    if (function_exists($filter)) {// 可以是一个普通函数 但只支持回传一个参数（当前value值） 如 'trim'
                        $this[$field] = $value = $filter($value);
                    } elseif (method_exists($this, $filter)) {// 可以是一个模型方法 用于定义一些公共规则 参数是当前值和所有值
                        $this[$field]= $value = $this->$filter($value, $data);
                    }
                } elseif (is_callable($filter)) {//可以是一个匿名函数 参数是当前值和所有值
                    $this[$field]= $value = $filter($value, $data);
                }
            }
            // date类型如果值为空 unset掉 交给mysql自己处理 不然空字符串数据库容易报错
            if (in_array($types[$field] ?? '', ['date', 'datetime', 'time']) && empty($value)) {
                // 解决date 允许null 又清空不了的问题
                if (!empty($this->tableColumns[$field]) && $this->tableColumns[$field]['default'] === null) {
                    $this[$field] = null;
                    continue;
                }
                unset($this[$field]);
                continue;
            }

            if (isset($this->form[$field]['type'])) {
                $type = strtolower($this->form[$field]['type']);
                switch ($type) {
                    case 'int':
                    case 'integer':
                        $this[$field] = intval($value);
                        break;
                    case 'double':
                    case 'float':
                        $this[$field] = floatval($value);
                        break;
                    case 'bool':
                    case 'boolean':
                        $this[$field] = boolval($value);
                        break;
                    case 'time':
                        $this[$field] = !is_numeric($value) ? strtotime($value): $value;
                        break;
                    case 'json':
                        if (empty($value)) {
                            $value = [];
                        }
                    case 'array':
                        if (is_string($value) && is_json($value)) {
                            $value = json_decode($value, true);
                        }
                        if (is_array($value)) {
                            $this[$field] = json_encode($value, JSON_UNESCAPED_UNICODE);
                        }
                        break;
                    case 'blob':
                        $this[$field] = gzcompress($value);
                        break;
                    case 'blob.array':
                        $this[$field] = gzcompress(serialize($value));
                        break;
                    case 'join':
                        if (is_array($value)) {
                            $join = $this->form[$field]['join'] ?? ',';
                            $this[$field] = implode($join, $value);
                        }
                        break;
                    case 'string':
                    case 'text':
                        if (is_array($value)) {
                            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                        }
                        $this[$field] = strip_tags((string) $value);
                        break;
                    case 'html':
                        // 暂不做任何处理 -- 富文本编辑器字段需要是html
                        break;

                    case 'none':
                        /*
                        if (isset($this[$field])) {
                            unset($this[$field]);
                        }
                        */
                        break;
                    default:
                        break;
                }
            }

            if (isset($this[$field]) && is_array($this[$field])) {
                $this[$field] = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
        }
    }


    /**
     * 是否自动验证  一般通过 定义$autoValidate = false|true 实现全局配置是否验证  而isValidate(false)临时调整某次是否验证
     * @param bool $is
     * @return $this
     */
    public function setAutoValidate(bool $is = true)
    {
        $this->autoValidate = $is;
        return $this;
    }

    /**
     * 是否验证  受 autoValidate 约束 如果 autoValidate关闭  无论isValidate 怎么设置都不验证了
     * @param bool $is
     * @return $this
     */
    public function isValidate(bool $is = true)
    {
        $this->setValidate('is', $is);
        return $this;
    }

    /**
     * 设置验证选项
     * @param string|array $key 需要设置的选项名
     * @param string $value 选项值
     * @return $this
     */
    public function setValidate($key, $value = '')
    {
        if (is_array($key)) {
            $this->validateOptions = array_merge($this->validateOptions, $key);
        } else {
            if (array_key_exists($key, $this->defaultOptions)) {
                $this->validateOptions[$key] = $value;
            }
        }
        return $this;
    }

    public function getValidateRule()
    {
        return $this->validate;
    }

    /**
     * 重置验证选项  如果有多个写入操作 前面的操作可能已经设置了选项 后面的操作会保留 如果需要还原 调用该方法
     * @return $this
     */
    public function resetValidate()
    {
        $this->validateOptions = [];
        return $this;
    }

    public function validate($data = [])
    {
        $options = array_merge($this->defaultOptions, $this->validateOptions);

        // 验证前
        $this->beforeValidate();

        // 全局关闭了自动验证  全局关闭 一般用于 不使用系统的验证方式  自行按照TP的验证方式  写入前自行验证
        if (false === $this->autoValidate) {
            return empty($this->error) ? true : false;
        }

        // 本次操作无需自动验证 比如本次确实不需验证或自行使用了其他验证方式（比如 自行使用了验证器验证）
        if (false === $options['is']) {
            return empty($this->error) ? true : false;
        }
        // 准备验证实例
        $instance = $options['instance'];
        if (empty($instance)) {
            $instance = \think\Validate::class;
        }
        if (is_string($instance)) {
            if (class_exists($instance)) {
                $this->validateInstance = new $instance();
            } else {
                $this->forceError(__('class not exists') . ":" . $instance);
                return false;
            }
        } else {
            $this->validateInstance = $instance;
        }
        if (!is_object($this->validateInstance) || !($this->validateInstance instanceof \think\Validate)) {
            $this->forceError('验证实例错误');
            return false;
        }

        $data = empty($data) ? $this->getData(): $data;

        if (empty($options['instance'])) {
            // 按系统规则进行验证 准备验证规则  信息 和字段
            $originRules = empty($options['rule'])? $this->validate : $options['rule'];
            if (!empty($this->validateOptions['mergeRule'])) {
                foreach ($this->validateOptions['mergeRule'] as $field => $r) {
                    $originRules[$field][] = $r;
                }
            }

            $this->parseValidate($originRules);

            // 验证规则
            $rules = $this->validateCache['rule'][$options['scene']] ?? [];
            if (empty($rules)) {
                return empty($this->error) ? true : false;
            }
            if ($options['only'] === true) {
                $options['only'] = array_keys($data);
            }
            // 只验证特定字段
            if (!empty($options['only']) && is_array($options['only'])) {
                foreach ($rules as $field => $r) {
                    if (!in_array($field, $options['only'], true)) {
                        unset($rules[$field]);
                    }
                }
            }
            // 错误信息
            $message = $this->validateCache['message'] ?? [];
            // 字段描述处理
            $fields = $this->getFieldsName();

            $this->validateInstance->rule($rules, $fields)->message($message);
        } else {
            // 按TP规则 自行定义 验证器 这里仅仅帮你执行验证而已
            $this->validateInstance->scene($options['scene']);
        }

        $this->validateCache = [];
        // 执行验证
        if (!$this->validateInstance->batch(!!$options['batch'])->check($data)) {
            $this->forceError($this->validateInstance->getError());
        }
        return empty($this->error) ? true : false;
    }

    /**
     * 获取字段描述
     * @param string $field
     * @return array|string
     */
    public function getFieldsName(string $field = ''){

        if ($field) {
            return !empty($this->defaultOptions['fieldsName'][$field])? $this->defaultOptions['fieldsName'][$field]: ($this->form[$field]['name'] ?? Str::studly($field));
        }
        $fields = $this->validateOptions['fieldsName'] ?? [];
        foreach ($this->form as $field => $info) {
            if (isset($fields[$field])) {
                continue;
            }
            $fields[$field] = $info['name'] ?? Str::studly($field);
        }
        return $fields;
    }

    protected function parseValidate(array $ruleList)
    {
        if (empty($ruleList)) {
            return;
        }
        foreach ($ruleList as $field => $info) {
            if (isset($info['rule'])) {
                if (is_array($info['rule'])) {
                    $rule = $info['rule'][0];
                    if ('call' === $rule) {
                        $rule = $info['rule'][1];
                        if (strpos($rule, ',') !==  false) {
                            $rules = explode(',', $rule);
                            $rule = $rules[0];
                            $info['rule'] = $rules;
                        } else {
                            array_shift($info['rule']);
                        }
                        if (count($info['rule']) <= 1) {
                            array_push($info['rule'], true);
                        }
                        $this->validateInstance->extend($rule, [$this, $rule]);
                    }
                    $rule_args = $info['rule'];
                    array_shift($rule_args);
                    // 20230110针对一些特殊规则 强行判断参数
                    if ($rule == 'unique' && empty($rule_args)) {
                        array_push($rule_args, substr($this->getTable(), strlen(get_db_config('prefix', $this->getConnection()))));
                    }
                    if (count($rule_args) > 0) {
                        $rule_args = implode(',', $rule_args);
                        $rule = [$rule => $rule_args];
                    } else {
                        $rule = [$rule];
                    }
                } else {
                    $rule = [$info['rule']];
                }
                $this->ruleMerge('full', $field, $rule);

                if (isset($info['on'])) {
                    $this->ruleMerge($info['on'], $field, $rule);
                } else {
                    $this->ruleMerge('add', $field, $rule);
                    $this->ruleMerge('edit', $field, $rule);
                }
                if (isset($info['message'])) {
                    $this->validateCache['message'][$field . '.' . (isset($rule[0]) ? $rule[0] : array_keys($rule)[0])] = $info['message'];
                }
            } else {
                if (is_array($info)) {
                    foreach ($info as $deepInfo) {
                        $this->parseValidate([$field => $deepInfo]);
                    }
                }
            }
        }
    }

    protected function ruleMerge($scene, $field, $rule)
    {
        $scene = trim($scene);
        $this->validateCache['rule'][$scene][$field] = array_merge(
            $this->validateCache['rule'][$scene][$field] ?? [],
            (array) $rule
        );
    }

    protected function hasRule($field, $rule = 'require')
    {
        $rules = $this->validateCache['rule']['full'][$field] ?? [];
        if (empty($rules)) {
            return false;
        }
        $rules = Arr::normalize($rules);
        return array_key_exists($rule, $rules);
    }

}