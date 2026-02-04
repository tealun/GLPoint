<?php
declare (strict_types=1);

namespace addons\ueditor\controller;

use addons\ueditor\BaseController;
use think\facade\Event;
use woo\common\helper\Str;
use woo\common\Upload;

class Index extends BaseController
{
    private $uconfig = [];
    protected  $Vfolder;
    protected $Upload;

    protected function initialize()
    {
        parent::initialize();
        $this->uconfig = $this->app->config->get('ueditor');
        $this->Vfolder = $this->loadModel('Folder');
        $this->Upload = $this->loadModel('Attachement');
    }

    public function index()
    {
        return "这里是插件【ueditor】首页";
    }

    public function server()
    {
        $action = trim($this->args['action'] ?? '');
        $result = ['state' => '请求错误'];
        if ($action == 'config') {
            $result = $this->uconfig;
        } elseif (in_array($action, ['uploadimage', 'uploadvideo', 'uploadfile'])) {
            $result = $this->uploadFile($action);
        } elseif (in_array($action, ['listimage', 'listfile'])) {
            $result = $this->getFileList($action);
        } elseif ($action == 'uploadscrawl') {
            $result = $this->uploadScrawl($action);
        } elseif ($action == 'catchimage') {
            set_time_limit( 0 );
            $source = $this->request->param($this->uconfig['catcherFieldName']);
            $list = [];
            foreach ($source as $remoteImage) {
                array_push($list, $this->catchRemoteImage($remoteImage));
            }
            $result = [
                'list' => $list,
                'state' => 'SUCCESS'
            ];
        }

        if (isset($this->args["callback"])) {
            if (preg_match("/^[\w_]+$/", $this->args["callback"])) {
                return jsonp($result);
            } else {
                return json(['state'=> 'callback参数不合法']);
            }
        } else {
            return json($result);
        }
    }

    protected function catchRemoteImage($remoteImage)
    {
        $args = $this->args;
        $args['model'] = $args['model'] ?? 'file';

        $imgUrl = str_replace("&amp;", "&", htmlspecialchars($remoteImage));
        if (strpos($imgUrl, "http") !== 0) {
            return [
                'state' => '资源非网络地址'
            ];
        }
        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';
        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            return [
                'state' => '资源网络地址非法URL'
            ];
        }
//        try {
//            preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
//            $host_without_protocol = count($matches) > 1 ? $matches[1] : '';
//            // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
//            $ip = gethostbyname($host_without_protocol);
//            // 判断是否是私有 ip
//            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
//                return [
//                    'state' => '资源地址为非法 IP'
//                ];
//            }
//        } catch (\Exception $e) {
//            return [
//                'state' => $e->getMessage()
//            ];
//        }

        //获取请求头并检测死链
        try {
            $heads = get_headers($imgUrl, 1);
            if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
                return [
                    'state' => '资源链接不可用'
                ];
            }
        }  catch (\Exception $e) {
            return [
                'state' => $e->getMessage()
            ];
        }
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array( $fileType , $this->uconfig[ 'catcherAllowFiles'])) {
            return [
                'state' => '文件格式不允许'
            ];
        }
        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);
        $file_size = strlen($img);
        if ($file_size > $this->uconfig['catcherMaxSize']) {
            return [
                'state' => '文件大小不能超过' . return_size($this->uconfig['catcherMaxSize'])
            ];
        }
        $basepath = $this->app->config->get('filesystem')['disks']['public']['root'] . '/' . Str::snake($args['model']) . '/' . date('Ymd');
        if (!is_dir($basepath)) {
            mkdir($basepath, 0755, true);
        }
        $filename = uniqid((string) mt_rand()) . $fileType;
        $rslt = file_put_contents($basepath . '/' . $filename, $img);

        if (!$rslt || !is_file($basepath . '/' . $filename)) {
            return [
                'state' => '文件上传失败'
            ];
        }
        $data['model'] = $args['model'];
        $data['title'] = $filename;
        $data['type'] = 'image';
        $data['driver'] = 'local';
        $data['url'] = $this->app->config->get('wooupload')['drivers']['local']['domain'] . 'uploads/' . Str::snake($args['model']) . '/' . date('Ymd') . '/' . $filename;
        $data['size'] = $file_size;
        $data['ext'] = substr($fileType, 1);
        $file_info = @getimagesize($basepath . '/' . $filename);
        $data['width'] = $file_info[0];
        $data['height'] = $file_info[1];
        Event::trigger('Upload', $data);

        return [
            "state" => 'SUCCESS',
            "url" => $data['url'],
            "title" => '',
            "original" => $data['title'],
            "type" => '.' . $data['ext'],
            "size" => $data['size'],
            "source" => htmlspecialchars($imgUrl)
        ];

    }

    protected function uploadScrawl($action)
    {
        $args = $this->args;
        $args['model'] = $args['model'] ?? 'file';

        $image = $this->request->param('upfile');
        $file_data = base64_decode($image);
        $file_size = strlen($file_data);
        if ($file_size > $this->uconfig['scrawlMaxSize']) {
            return [
                'state' => '文件大小不能超过' . return_size($this->uconfig['scrawlMaxSize'])
            ];
        }
        $basepath = $this->app->config->get('filesystem')['disks']['public']['root'] . '/' . Str::snake($args['model']) . '/' . date('Ymd');
        if (!is_dir($basepath)) {
            mkdir($basepath, 0755, true);
        }
        $ext = 'png';
        $filename = uniqid((string) mt_rand()) . '.' . $ext;
        $rslt = file_put_contents($basepath . '/' . $filename, $file_data);

        if (!$rslt || !is_file($basepath . '/' . $filename)) {
            return [
                'state' => '文件上传失败'
            ];
        }

        $data['model'] = $args['model'];
        $data['title'] = $filename;
        $data['type'] = 'image';
        $data['driver'] = 'local';
        $data['url'] = $this->app->config->get('wooupload')['drivers']['local']['domain'] . 'uploads/' . Str::snake($args['model']) . '/' . date('Ymd') . '/' . $filename;
        $data['size'] = $file_size;
        $data['ext'] = $ext;
        $file_info = @getimagesize($basepath . '/' . $filename);
        $data['width'] = $file_info[0];
        $data['height'] = $file_info[1];
        Event::trigger('Upload', $data);

        return [
            "state" => 'SUCCESS',
            "url" => $data['url'],
            "title" => '',
            "original" => $data['title'],
            "type" => '.' . $data['ext'],
            "size" => $data['size']
        ];
    }

    protected function getFileList($action)
    {
        $args = $this->args;
        $start = intval($args['start'] ?? 0);
        $size = intval($args['size'] ?? 0);
        $page = ($start / $size) + 1;

        $where = [];
        if ($action == 'listimage') {
            $where[] = ['type', '=', 'image'];
        }
        $order = ['id' => 'DESC'];

        $list = $this->Upload->getPage([
            'where' => $where,
            'order' => $order,
            'field' => [],
            'limit' => $size,
            'paginate' => [
                'page' => $page
            ]
        ]);

        unset($list['render']);
        if (empty($list['list'])) {
            return [
                "state" => "no match file",
                "list" => [],
                "start" => 0,
                "total" => 0
            ];
        }

        return [
            "state" => "SUCCESS",
            "list" => $list['list'],
            "start" => $start,
            "total" => $list['page']['total']
        ];
    }

    protected function uploadFile($action)
    {
        $args['model'] = $this->args['model'] ?? 'file';

        if ($action == 'uploadimage') {
            $args['validExt'] = $this->uconfig['imageAllowFiles'];
            $args['maxSize'] = $this->uconfig['imageMaxSize'] / 1024;
        } elseif ($action == 'uploadvideo') {
            $args['validExt'] = $this->uconfig['videoAllowFiles'];
            $args['maxSize'] = $this->uconfig['videoMaxSize'] / 1024;
        } else {
            $args['validExt'] = $this->uconfig['fileAllowFiles'];
            $args['maxSize'] = $this->uconfig['fileMaxSize'] / 1024;
        }
        foreach ($args['validExt'] as &$value) {
            if ($value[0] == '.') {
                $value = substr($value, 1);
            }
        }
        $file = $this->request->file('upfile');
        $upload = new Upload($args);
        $filepath = $upload->putFile($file);

        if ($filepath) {
            return [
                "state" => 'SUCCESS',
                "url" => $filepath,
                "title" => '',
                "original" => $file->getOriginalName(),
                "type" => '.' . $file->getOriginalExtension(),
                "size" => $file->getSize()
            ];
        } else {
            return [
                "state" => $upload->getError()[0] ?? '上传错误'
            ];
        }
    }




}