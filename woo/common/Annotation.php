<?php
declare (strict_types=1);

namespace woo\common;

use ReflectionClass;
use woo\common\helper\Str;

class Annotation
{
    protected $reader;

    public function __construct()
    {
        $this->reader = app('Doctrine\Common\Annotations\Reader');
    }

    /**
     * 获取指定类的指定注解名的注解
     * @param ReflectionClass $class  类
     * @param string $name  注解名（类）
     * @param bool $is_recursion 是否递归上父类
     * @return bool|object|null
     */
    public function getClassAnnotation(ReflectionClass $class, string $name, bool $is_recursion = true)
    {
        if (!class_exists($name)) {
            $name = "woo\\common\\annotation\\" . Str::studly($name);
        }
        $result = $this->reader->getClassAnnotation($class, $name);
        if ($result) {
            return $result;
        }
        if ($is_recursion) {
            $parent_class = $class->getParentClass();
            if ($parent_class) {
                return $this->getClassAnnotation($parent_class, $name, $is_recursion);
            }
        }
        return false;
    }

    /**
     * 获取指定类的 指定方法 指定注解名的注册
     * @param ReflectionClass $class  类
     * @param string $name   注解名
     * @param string $method 方法
     * @return bool|object|null
     * @throws \ReflectionException
     */
    public function getMethodAnnotation(ReflectionClass $class, string $name, string $method, bool $is_recursion = true)
    {
        if (!class_exists($name)) {
            $name = "woo\\common\\annotation\\" . Str::studly($name);
        }
        if ($class->hasMethod($method)) {
            $method_reflect = $class->getMethod($method);
            $result = $this->reader->getMethodAnnotation($method_reflect, $name);
            if ($result) {
                return $result;
            }
            if ($is_recursion) {

                $parent_class = $class->getParentClass();
                if ($parent_class) {
                    return $this->getMethodAnnotation($parent_class, $name, $method, $is_recursion);
                }
            }
        }
        return false;
    }

    public function getMethodAnnotations(ReflectionClass $class, string $name, string $method, bool $is_recursion = true)
    {
        if (!class_exists($name)) {
            $name = "woo\\common\\annotation\\" . Str::studly($name);
        }
        if ($class->hasMethod($method)) {
            $method_reflect = $class->getMethod($method);
            $result = $this->reader->getMethodAnnotations($method_reflect);
            if ($result) {
                $result = array_filter($result, function ($annotation) use ($name) {
                    return $annotation instanceof $name;
                });
                return $result;
            }
            if ($is_recursion) {
                $parent_class = $class->getParentClass();
                if ($parent_class) {
                    return $this->getMethodAnnotations($parent_class, $name, $method, $is_recursion);
                }
            }
        }
        return false;
    }
}