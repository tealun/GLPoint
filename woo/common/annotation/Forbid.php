<?php
declare (strict_types=1);

namespace woo\common\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Except
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
class Forbid extends  Annotation
{
    /**
     * @var bool
     */
    public $value = false;
    /**
     * @var array
     */
    public $only = [];
    /**
     * @var array
     */
    public $except = [];
    /**
     * @var bool
     */
    public $nodebug = false;
}