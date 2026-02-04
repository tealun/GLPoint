<?php
declare (strict_types=1);

namespace woo\common\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Except
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
class Ps extends  Annotation
{

    /**
     * @var bool
     */
    public $value = true;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $as = '';

    /**
     * @var array
     */
    public $except = [];
}