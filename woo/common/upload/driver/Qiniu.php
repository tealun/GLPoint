<?php
declare (strict_types=1);

namespace woo\common\upload\driver;

use think\facade\Event;
use woo\common\helper\Str;
use woo\common\upload\Driver;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Qiniu extends Driver
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->handler = new Auth($this->config['ak'], $this->config['sk']);
    }

    public function putFile(\think\file\UploadedFile $file, array $options = [])
    {
        $options = array_merge($this->config, $options);
        $parent_return = parent::{__FUNCTION__}($file, $options);
        if (!(true === $parent_return)) {
            return $parent_return;
        }

        $token = $this->getToken();
        $extension = $file->getOriginalExtension();
        $fiename = md5((string) microtime(true)) . '.' . $extension;

        try {
            $uploadMgr = new UploadManager();
            list($result, $err) = $uploadMgr->putFile($token, 'uploads/' . Str::snake($this->config['folder']) . '/' . $fiename, $file->getRealPath());
            if ($err !== null) {
                $this->forceError($err);
                return false;
            }
            $result = $this->config['domain'] . $result['key'];
            Event::trigger('Upload', [
                'object' => $file,
                'url'    => $result,
                'model'  => $options['model'] ?? 'file',
                'driver' => 'qiniu'
            ]);
            return $result;
        } catch (\Exception $e) {
            $this->forceError($e->getMessage());
            return false;
        }
    }

    public function getThumbUrl(string $image, $width = 0, $height = 0, $method = 0, bool $water = false)
    {
        list($thumb_width, $thumb_height, $thumb_method) = $this->getThumbArgs($width, $height, $method);
        if ($thumb_width >= 0) {
            if (in_array($thumb_method, [2, 3, 1])) {
                $image .= "?imageView2/{$thumb_method}/w/{$thumb_width}/h/{$thumb_height}";
            } elseif ($thumb_method == 4) {
                $image .= "?imageMogr2/crop/{$thumb_width}x{$thumb_height}/gravity/northwest";
            } elseif ($thumb_method == 5) {
                $image .= "?imageMogr2/crop/{$thumb_width}x{$thumb_height}/gravity/southeast";
            } elseif ($thumb_method == 6) {
                $image .= "?imageMogr2/thumbnail/{$thumb_width}x{$thumb_height}!";
            }
        }
        // | 分割缩略图 和水印
        return $image;
    }

    protected function getThumbArgs($width = 0, $height = 0, $method = 0)
    {
        $width = intval($width);
        if ($width == 0) {
            $width = intval(setting('upload_thumb_width'));
        }
        $height = intval($height);
        if ($height == 0) {
            $height = intval(setting('upload_thumb_height'));
        }
        $method = intval($method);
        if (!in_array($method, [1, 2, 3, 4, 5, 6])) {
            $method = intval(setting('upload_thumb_method'));
        }
        $map = [1 => 2, 2 => 3, 3 => 1, 4 => 4, 5 => 5, 6 => 6];

        return [$width, $height, $map[$method]];
    }

    protected  function getToken($bucket = null, $keyToOverwrite = null, $expires = 7200, $policy = null)
    {
        if ($bucket === null) {
            $bucket = $this->config['bucket'];
        }
        return  $this->handler->uploadToken($bucket, $keyToOverwrite, $expires, $policy, true);
    }

}