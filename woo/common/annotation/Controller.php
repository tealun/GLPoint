<?php
declare (strict_types=1);

namespace woo\common\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Except
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
class Controller extends  Annotation
{
    public $value = '';
    /**
     * 标题
     * @var string
     */
    public $title = '';

    /**
     * 所属模块 可以填写模块id值 或标题
     * @var string
     */
    public $module = '';

    /**
     * 描述
     * @var string
     */
    public $desc = '';

    /**
     * 是否自动生成api
     * @var bool
     */
    public $is = true;

}