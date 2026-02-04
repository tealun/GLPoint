<?php
declare (strict_types=1);


namespace woo\common\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Except
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
class Except extends  Annotation
{
    /**
     * @var array
     */
    public $action = [];

    /**
     * @Enum({"post", "get", "put", "delete"})
     * @var array
     */
    public $method = [];

}