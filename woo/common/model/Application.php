<?php
declare (strict_types = 1);

namespace woo\common\model;

use app\common\model\App;
use woo\common\facade\Auth;
use woo\common\helper\Str;
use think\facade\Console;

class Application extends App
{
    protected function afterStart()
    {
        parent::{__FUNCTION__}();
        $this->form['name']['filter'] = function ($value) {
            return trim(strtolower($value));
        };
        $this->validate['name'][] = ['rule' => ['call', 'checkName']];
    }

    public function checkName($value)
    {
        if (is_dir(app()->addons->getAddonPath($value))) {
            return '应用目录名不能是' . $value;
        }
        return true;
    }

    public function afterInsertCall()
    {
        $parent_return = parent::{__FUNCTION__}();
        $data = $this->getData();
        if (is_dir(base_path() . $data['name'])) {
            return $parent_return;
        }
        Console::call('build', [$data['name']]);
        $name = $data['name'];
        $nameStudly = Str::studly($name);
        $title = $data['title'];
        $author = $data['author'];
        $version = $data['version'];
        $des = $data['describe'];
        $is_api = $data['is_api'] ?? 0;

        mkdir(base_path() . $name . DIRECTORY_SEPARATOR . 'config', 0777, true);


$install = <<<DOC
<?php
namespace app\\$name;

use woo\common\abstracts\AppInstall;

class Install extends AppInstall
{
    public function install()
    {
    }
    public function uninstall()
    {
    }
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
file_put_contents(base_path() . $name . DIRECTORY_SEPARATOR . "Install.php", $install);

$callback = <<<DOC
<?php
namespace app\\$name;

class Callback extends \woo\common\abstracts\CallbackAbstract
{
    public function before()
    {        
    }

    // +--------------------------------------------分界线--------------------------------------------------------------
    // 上面是调用当前请求方法之前执行
    // 下面是即将渲染页面之前（逻辑业务已经完成）执行 可以拦截到所有assign出去的数据
    // +--------------------------------------------分界线--------------------------------------------------------------

    public function after()
    {        
    }
}
DOC;
file_put_contents(base_path() . $name . DIRECTORY_SEPARATOR . "Callback.php", $callback);

if (!$is_api) {
    $callback = <<<DOC
<?php
declare (strict_types = 1);

namespace app\common\controller;

use woo\common\controller\Controller;

/**
 * 开发者自己的{$title}应用控制器基类
 * 具体应用控制器都继承它或子类
 */
abstract class $nameStudly extends Controller
{
    protected function initialize()
    {        
        parent::initialize();
    }
}
DOC;
    file_put_contents(base_path() . 'common' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $nameStudly . ".php", $callback);

    $callback = <<<DOC
<?php
declare (strict_types = 1);

namespace app\\$name\controller;

use app\common\controller\\$nameStudly;

class Index extends $nameStudly
{
    public function index()
    {
        
        \$this->assign->addJs(['jquery-3.4.1.min']);// 引入头部js文件
        /*
        \$this->assign->addJs(['test', '/test'], true);// 引入底部js文件
        \$this->assign->addCss(['test', '/test']);// 引入css文件
        */
        \$this->assign->test = '[{$name}]应用模板测试';// 模板赋值
        \$this->addTitle('[{$name}]应用首页');// 添加网页标题
        //return \$this->message('成功', 'error'); // 消息提示
        return \$this->fetch();
    }
}
DOC;
    file_put_contents(base_path() . $name . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . "Index.php", $callback);

    $callback = <<<DOC
<?php
return [
    'apps' => [
        '{$name}' => [
            'type'                 => 'session',
            'session_key'          => 'login',
            'model'                => 'User',
            'response_mode'        => 'json',
            'allow_from_all'       => true,
        ],
    ],
];
DOC;
    file_put_contents(base_path() . $name . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'wooauth.php', $callback);


    mkdir(base_path() . $name . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'index', 0777, true);

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
    file_put_contents(base_path() . $name . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . "global.html", $content);
    $content = <<<DOC
{extend name="global" /}

{block name="content"}
    {\$test}
{/block}
DOC;
    file_put_contents(base_path() . $name . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . "index.html", $content);

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
                    var href = "{:url('Index/index')}";
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
    file_put_contents(base_path() . $name . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . "message.html", $content);

} else {
    $callback = <<<DOC
<?php
declare (strict_types = 1);

namespace app\common\controller;

use woo\common\controller\Controller;
use woo\common\controller\\traits\ApiCommon;

/**
 * 开发者自己的{$title}应用控制器基类
 * 具体应用控制器都继承它或子类
 */
abstract class $nameStudly extends Controller
{
    use ApiCommon;

    protected function initialize()
    {        
        parent::initialize();
    }
}
DOC;
    file_put_contents(base_path() . 'common' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $nameStudly . ".php", $callback);

    $callback = <<<DOC
<?php
declare (strict_types = 1);

namespace app\\$name\controller;

use app\common\controller\\$nameStudly;
use woo\common\annotation\{Controller,ApiInfo,Param,Header,Returns};

/**
 * @Controller("首页",module="首页",desc="控制器作用描述")
 */
class Index extends $nameStudly
{
    /**
     * @ApiInfo(value="获取首页数据",method="GET",login=false)
     */
    public function index()
    {               
        return \$this->ajax('success', '[$name]应用请求成功');
    }
}
DOC;
    file_put_contents(base_path() . $name . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . "Index.php", $callback);

    $callback = <<<DOC
<?php
declare (strict_types = 1);

namespace app\\$name\controller;

use app\common\controller\\$nameStudly;
use woo\common\annotation\{Controller,ApiInfo,Param,Header,Returns};
use woo\common\Auth;

/**
 * @Controller("会员管理",module="会员",desc="用于会员相关的功能管理")
 */
class User extends $nameStudly
{
     /**
     * @ApiInfo(value="登录",desc="通过输入账号、密码登录",method="POST",tag="测试|基础",author="张三",login=false)
     * @Param("username",title="用户名",require=true)
     * @Param("password",title="密码",require=true)
     * @Returns("token", type="string", title="token", desc="请妥善保管，每次请求header中Authorization:Bearer token值")
     */
    public function login()
    {
        \$auth = new Auth();
        \$logined = \$auth->login();
        if (\$logined) {
            return \$this->ajax('success', '登录成功', ['token' => \$logined]);
        } else {
            return \$this->ajax('error', \$auth->getError());
        }
    }
    
    /**
     * @ApiInfo("注册",desc="注册账号",method="POST",login=false)
     * @Param("username",require=true,title="用户名",validate={"regex":"/^\w{5,11}$/"},message={"regex":"用户名由5-10位数字、字母、下划线组成","require":"用户名不能为空"})
     * @Param("password",require=true,title="密码",validate={"length":"6,16"},message={"length":"密码长度应该是6-16位"})
     * @Param("repassword",require=true,title="重复密码",validate={"length":"6,16","confirm":"password"})
     * @Returns("token", type="string", title="token", desc="请妥善保管，每次请求header中Authorization:Bearer token值")
     */
    public function register()
    {
        \$post = \$this->request->post();
        \$data = [
            'user_group_id' => 1,// 默认的组名
            'status' => 'verified',// 状态,
            'register_ip' => \$this->request->ip(),
            'username' => \$post['username'],
            'password' => \$post['password']
        ];
        \$result = \$this->mdl->createData(\$data, ['allowField' => ['username', 'password', 'pay_password', 'user_group_id', 'status', 'salt', 'register_ip']]);

        if (\$result) {
            //return \$this->ajax('success', '注册成功', ['id' => \$result]);
            // 立即登录 如果不登录 返回上面代码即可
            \$auth = new Auth();
            \$logined = \$auth->login();
            if (\$logined) {
                return \$this->ajax('success', '注册成功并成功登录', ['token' => \$logined]);
            } else {
                return \$this->ajax('error', \$auth->getError());
            }
        }
        return \$this->ajax('error', '注册失败', \$this->mdl->getError());
    }
}
DOC;
    file_put_contents(base_path() . $name . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . "User.php", $callback);
    $callback = <<<DOC
<?php
return [
    'apps' => [
        '{$name}' => [
            'type'                 => 'jwt',
            'model'                => 'User',
            'response_mode'        => 'json',
            'response_json' => ["result" => "nologin", "message" => "未登录"],
            'forbid_response_json' => ["result" => "error", "message" => "当前请求被拒绝"],
            'allow_login_model' => ['User' => ['withJoin' => ['UserGroup','UserGrade']]],
            'allow_from_all' => true,
            'is_annotation_except' => false,
        ],
    ],
];
DOC;
    file_put_contents(base_path() . $name . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'wooauth.php', $callback);

    if (get_model_name('mapi.Project')) {
        $count = model('mapi.Project')->where('app_name', '=', $name)->count();
        if (!$count) {
            model('mapi.Project')->createData([
                'title' => $title,
                'app_name' => $name,
                'desc'  => $des,
                'admin_id' => Auth::user('id')
            ]);
        }
    }


}
        return $parent_return;
    }
}