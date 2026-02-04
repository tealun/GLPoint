<?php
declare (strict_types=1);


namespace woo\common\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Except
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
class Log extends  Annotation
{

    /**
     * @var bool
     */
    public $value = true;
    /**
     * @var array
     */
    public $only = [];
    /**
     * @var array
     */
    public $except = [];
    /**
     * 需要排除的属性名 比如：密码
     * @var array
     */
    public $remove = [];
}