<?php
declare (strict_types=1);

namespace woo\common\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Except
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
class Header extends  Annotation
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
     * 默认值
     */
    public $default = '';

    /**
     * 示例值
     */
    public $example = '';

    /**
     * 引用其他类库 便于注释的复用
     * @var string
     */
    public $target = null;

}