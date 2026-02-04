<?php
declare (strict_types=1);

namespace woo\common\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class ApiInfo
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
class ApiInfo extends  Annotation
{
    /**
     * 标题
     * @var string
     */
    public $value = '';

    /**
     * 描述
     * @var string
     */
    public $desc = '';

    /**
     * 是否需要登录以后访问
     * @var bool
     */
    public $login = true;

    /**
     * 是否需要授权（前提是 需登录  true授权页面会显示 否则不显示）
     * @var bool
     */
    public $power = true;

    /**
     * 作者
     * @var string
     */
    public $author = '';

    /**
     * 请求类型 GET POST  PUT DELETE
     * @var string
     */
    public $method = '';

    /**
     * API的url地址 如果自定义了路由就必须写 否则路由会生成错误
     * @var string
     */
    public $url = '';

    /**
     * 标签 多个直接用|分割
     * @var string
     */
    public $tag = '';

    /**
     * 是否自动生成api
     * @var bool
     */
    public $is = true;

    /**
     * 禁止访问接口 可用于关闭接口
     * @var bool
     */
    public $isForbidden = false;

    /**
     * 自定义验证情况  true 由程序自动获取到每个参数规则（仅限一级参数）；false不自动验证（程序中你自行处理验证）；字符串  自定义一个验证器类（命名空间）
     * @var bool|string
     */
    public $validate = true;
}