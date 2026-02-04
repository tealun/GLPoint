<?php
declare(strict_types=1);

namespace woo\common\model;

use app\common\model\App;

class Addon extends App
{
    /** 模型名称 */
    public $cname = '插件';

    /** 主显字段信息 */
    public $display = 'title';

    /** 自定义数据 */
    public $customData = [
        'create' => true,
        'batch_delete' => true,
        'modify' => true,
        'delete' => true,
        'detail' => true,
    ];

    /** 模型关联信息 */
    public $relationLink = [
        'AddonSetting' => [
            'type' => 'hasMany',
            'deleteWith' => true,
        ],
    ];


    protected function start()
    {
        parent::{__FUNCTION__}();

        /** 表单form属性 */
        $this->form = [
            'id' => [
                'type' => 'integer',
                'name' => 'ID',
                'elem' => 'hidden',
                'is_contribute' => false,
            ],
            'name' => [
                'type' => 'string',
                'name' => '插件目录',
                'elem' => 'text',
                'is_contribute' => false,
            ],
            'title' => [
                'type' => 'string',
                'name' => '标题',
                'elem' => 'text',
                'is_contribute' => false,
            ],
            'author' => [
                'type' => 'string',
                'name' => '作者',
                'elem' => 'text',
                'is_contribute' => false,
            ],
            'version' => [
                'type' => 'string',
                'name' => '版本',
                'elem' => 'text',
                'is_contribute' => false,
            ],
            'is_verify' => [
                'type' => 'integer',
                'name' => '审核',
                'elem' => 'checker',
                'is_contribute' => false,
                'list' => 'checker',
            ],
            'is_disuninstall' => [
                'type' => 'integer',
                'name' => '禁止卸载',
                'elem' => 'checker',
                'is_contribute' => false,
            ],
            'describe' => [
                'type' => 'string',
                'name' => '插件描述',
                'elem' => 'textarea',
                'is_contribute' => false,
            ],
            'admin_id' => [
                'type' => 'integer',
                'name' => '管理员ID',
                'elem' => 0,
                'is_contribute' => false,
                'list' => 0,
            ],
            'create_time' => [
                'type' => 'integer',
                'name' => '创建日期',
                'elem' => 0,
                'is_contribute' => false,
                'list' => [
                    'width' => '138',
                ],
            ],
            'update_time' => [
                'type' => 'integer',
                'name' => '修改日期',
                'elem' => 0,
                'is_contribute' => false,
                'list' => [
                    'width' => '138',
                ],
            ],
        ];

        /** 表单分组属性 */
        $this->formGroup = [];

        /** 表单触发器属性 */
        $this->formTrigger = [];

        /** 表单验证属性 */
        $this->validate = [
            'name' => [
                [
                    'rule' => ['require'],
                ],
                [
                    'rule' => ['unique', 'addon'],
                ],
                [
                    'rule' => ['call', 'checkName'],
                ],
                [
                    'rule' => ['regex', '/^[a-z]+[a-z0-9]+$/i'],
                ],
            ],
            'title' => [
                [
                    'rule' => ['require'],
                ],
            ],
        ];
    }

    public function checkName($value)
    {
        if (is_dir(base_path() . $value)) {
            return '插件目录名不能是' . $value;
        }
        return true;
    }

    public function afterInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $data = $this->getData();
        $addon_path = app()->addons->getAddonPath($data['name']);
        if (is_dir($addon_path)) {
            return $parent_return;
        }
        mkdir($addon_path, 0777, true);
        mkdir($addon_path . 'controller', 0777, true);
        mkdir($addon_path . 'view', 0777, true);
        mkdir($addon_path . 'middleware', 0777, true);
        mkdir($addon_path . 'event', 0777, true);
        mkdir($addon_path . 'config', 0777, true);
        mkdir($addon_path . 'install', 0777, true);

        $name = $data['name'];
        $title = $data['title'];
        $author = $data['author'];
        $version = $data['version'];
        $des = $data['describe'];

        $content = <<<DOC
<?php
declare (strict_types=1);

namespace addons\\$name;

use woo\common\abstracts\AddonsInstall;

class Install extends AddonsInstall
{
    /**
    * 安装程序
    */
    public function install()
    {
    }
    
    /**
    * 卸载程序
    */
    public function uninstall()
    {
    }
    
    /**
    * 查询配置
    */
    public function getConfig():array
    {
        return [
            "title" => "$title",
            "author" => "$author",
            "version" => "$version",
            "describe" => "$des",
        ];
    }
}
DOC;
        file_put_contents($addon_path . "Install.php", $content);

        $content = <<<DOC
<?php
// 插件函数库
DOC;
        file_put_contents($addon_path . "common.php", $content);

        $content = <<<DOC
<?php
// 插件中间件定义文件
return [

];
DOC;
        file_put_contents($addon_path . "middleware.php", $content);

        $content = <<<DOC
<?php
// 插件路由定义文件 改文件不能删除
return [
    // 插件路由前缀，默认是当前插件目录名
    'route_prefix' => '',    
    'rules' => [
        //路由规则 路由 => 控制器/方法
        //'test' => 'index/test'
        //'test' => ['rule' => 'index/test', 'append' => [...]]
    ]
];
DOC;
        file_put_contents($addon_path . "route.php", $content);
        $content = <<<DOC
<?php
// 插件事件定义文件
return [

];
DOC;
        file_put_contents($addon_path . "event.php", $content);

        $content = <<<DOC
<?php
return [
    'apps' => [
        'addon.$name' => [
            'type'                 => 'session',
            'session_key'          => 'login',
            'model'                => 'User',
            'response_mode'        => 'json',
            'allow_from_all'       => true,
        ],
    ],
];
DOC;
        file_put_contents($addon_path . 'config' . DIRECTORY_SEPARATOR . "wooauth.php", $content);

        $content = <<<DOC
<?php
declare (strict_types=1);

namespace addons\\$name;

use woo\common\addons\Controller;
/**
 * 插件控制器基类 其他控制器都继承它
 */
class BaseController extends Controller
{
    /**
     * 控制器中间件
     * @var array
     */
    protected \$middleware = [];
    
    /**
     * 初始化方法
     */
    protected function initialize()
    {
        // 你的代码...
        parent::{__FUNCTION__}();
        // 你的代码...
    }
}
DOC;
        file_put_contents($addon_path . "BaseController.php", $content);
        $content = <<<DOC
<?php
declare (strict_types=1);

namespace addons\\$name\controller;

use addons\\$name\BaseController;

class Index extends BaseController
{   
    public function index()
    {
        return "这里是插件【{$name}】首页";
    }
}
DOC;
        file_put_contents($addon_path . 'controller' . DIRECTORY_SEPARATOR . "Index.php", $content);
        $content = <<<DOC
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{block name="title"}{if !empty(\$meta.title)}{:implode(' - ', \$meta.title)}{else}$name{/if}{/block}</title>
    <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" name="viewport"/>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
    <meta name="renderer" content="webkit"/>
    <meta name="HandheldFriendly" content="true"/>
    <meta name="format-detection" content="telephone=no, email=no"/>
    {block name="keywords"}<meta name="keywords" content="{if !empty(\$meta.keywords)}{:implode(',', \$meta.keywords)}{/if}"/>{/block}
    {block name="description"}<meta name="description" content="{if !empty(\$meta.description)}{:implode(',', \$meta.description)}{/if}"/>{/block}
    <link rel="shortcut icon" href="{\$root}favicon.ico"/>
    {block name="css"}{\$woo_static_links.css | raw}{/block}
    <script>
        var woo_script_vars = {\$woo_script_vars | raw};
        woo_script_vars.woo = {
            wwwroot : woo_script_vars.wwwroot || '{\$root}',
            approot : woo_script_vars.approot || '{\$approot}',
            isLoadLayui : false,
            isGlobalLayer : false,
            layerCallback : {
                layform : function() {},
                layer : function () {}
            }
        };
    </script>
    {block name="js"}{\$woo_static_links.js | raw}{/block}
    {block name="headscript"}{/block}
</head>
<body>
{block name="header"}{/block}
{block name="content"}{/block}
{block name="footer"}{/block}
{block name="deferjs"}{\$woo_static_links.deferjs | raw}{/block}
{block name="script"}{/block}
</body>
</html>
DOC;
        file_put_contents($addon_path . 'view' . DIRECTORY_SEPARATOR . "global.html", $content);
        $content = <<<DOC
{extend name="global" /}

{block name="content"}
<style>
.home_message{ margin: 8% auto;width: 92%;max-width: 768px;min-height: 120px;}
.home_message .notification{ padding: 9px 0 9px 60px;line-height: 30px;}
.home_message .notification div{ font-size: 18px;}
.home_message.success .notification{ background: url({\$root}static/images/home/success.png) left top no-repeat;color: #05994f;}
.home_message.error  .notification{ background: url({\$root}static/images/home/error.png) left top no-repeat;color: #cb1b05;}
.home_message .redirect{ padding-top: 20px;}
.home_message .redirect a{  display: inline-block;height: 36px;line-height: 36px;border: 1px solid #e2e2e2;padding: 0 15px;border-radius: 2px}
.home_message .count_down{ padding-top: 20px;color: #9E9E9E;}
.home_message .count_down a{ color: #0080FF;}
.home_message .count_down span{ color: #555555;margin: 0 2px;}
</style>
<div class="{\$data.type} home_message">
    <div class="notification">
        <div>{\$data.message}</div>
    </div>
    <div class="redirect btn_count_{:count(\$data.redirect)}">
        {foreach \$data.redirect as \$title=>\$url}
        <a class="redirect_selection  {if isset(\$url.class)}{\$url.class}{/if} {if isset(\$url.rel)}javascript{/if}" {if isset(\$url.rel)}rel="{\$url.rel}"{/if}   href="{\$url.url}">{if isset(\$url.icon)}<i class="layui-icon {\$url.icon}"></i>{/if}{\$url.title}</a>
        {/foreach}
    </div>
    {if \$data.auto && \$data.redirect}
    <div class="count_down">
        系统将在<span id="count_down_count">{\$data.auto}</span>秒后自动跳转到第一个链接 <a id="cancel_count_down" style="cursor:pointer;">取消自动跳转</a>
    </div>

    <script>
        var left_seconds=parseInt('{\$data.auto}');
        function count_down(){
            if(left_seconds<0){
                if (!$('#rs_close:visible').length) {
                    var href = "{:addons_url('Index/index')}";
                    if ($('a.redirect_selection').not('.javascript').eq(0).attr('href')) {
                        href = $('a.redirect_selection').eq(0).attr('href')
                    }
                    window.location.href = href;
                } else {
                    $('#rs_close').trigger('click');
                }
                return;
            }
            $('#count_down_count').html(left_seconds--);
            count_down.timeout = window.setTimeout(count_down, 1000);
        }
        $(document).ready(function(){ count_down(); });
        $('#cancel_count_down').click(function(){
            window.clearTimeout(count_down.timeout);
            $(this).parent().remove();
        })
    </script>
    {/if}
</div>
{/block}
DOC;
        file_put_contents($addon_path . 'view' . DIRECTORY_SEPARATOR . "message.html", $content);


        return $parent_return;
    }
}
