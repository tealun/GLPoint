<?php
declare (strict_types=1);

namespace woo\common\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Except
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
class Returns extends  Annotation
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
     * 参数类型  int float string boolean object array
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
     * 示例值
     */
    public $example = '';

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