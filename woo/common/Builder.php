<?php
declare (strict_types=1);

namespace woo\common;

use woo\common\helper\Str;

class Builder
{
    protected $builder;
    public static function make($builder, ...$argments)
    {
        $builder_class = "woo\\builder\\" . Str::studly($builder);
        if (!class_exists($builder_class)) {

        }
        return new $builder_class(...$argments);
    }
}