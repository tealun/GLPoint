<?php
declare (strict_types=1);

namespace woo\common\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Except
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
class Param extends  Annotation
{
    /**
     * 参数标识符一般就是 参数字段名
     * @var string
     */
    public $value = '';

    /**
     * 参数字段名 一般不用填 即$value
     * @var string
     */
    public $name = '';

    /**
     * 参数类型  int float string boolean object array file
     * @var string
     */
    public $type = 'string';

    /**
     * 标题
     * @var string
     */
    public $title = '';

    /**
     * 备注、描述
     * @var string
     */
    public $desc = '';

    /**
     * 是否必须
     * @var bool
     */
    public $require = true;

    /**
     * 是否RSA加密字符
     * @var bool
     */
    public $rsa = false;

    /**
     * 默认值
     */
    public $default = '';

    /**
     * 示例值
     */
    public $example = '';

    /**
     * 验证规则 -- 只会验证一级参数 二级以后参数就不用写该属性了
     * @var array
     */
    public $validate = [];

    /**
     * 验证规则 -- 自定义错误提示
     * @var array
     */
    public $message = [];

    /**
     * 引用其他类库 便于注释的复用
     * @var string
     */
    public $target = null;

    /**
     * 类型是object 或 array时，定义子节点参数
     * @var array|string
     */
    public $params = [];

    /**
     * 当$target是模型时，用它来指定字段列表  字符串用|分割
     * @var array|string
     */
    public $field = [];

    /**
     * 当$target是模型时，用它来过滤字段列表 字符串用|分割
     * @var array|string
     */
    public $withoutField = [];
}