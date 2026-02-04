<?php
declare (strict_types=1);

namespace woo\common\upload\driver;

use think\facade\Event;
use woo\common\helper\Str;
use woo\common\upload\Driver;
use Qcloud\Cos\Client;


class Cos extends Driver
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->handler = new Client(
            array(
                'region' => $this->config['region'],
                'schema' => $this->config['schema'],
                'credentials'=> array(
                    'secretId'  => $this->config['ak'] ,
                    'secretKey' => $this->config['sk'])));
    }

    public function putFile(\think\file\UploadedFile $file, array $options = [])
    {
        $options = array_merge($this->config, $options);
        $parent_return = parent::{__FUNCTION__}($file, $options);
        if (!(true === $parent_return)) {
            return $parent_return;
        }

        $extension = $file->getOriginalExtension();
        $fiename = 'uploads/' . Str::snake($this->config['folder']) . '/' . md5((string) microtime(true)) . '.' . $extension;

        try {
            $upload_file = fopen($file->getRealPath(), 'rb');
            if ($upload_file) {
                $this->handler->Upload(
                    $bucket = $this->config['bucket'],
                    $key = $fiename,
                    $body = $upload_file);
                Event::trigger('Upload', [
                    'object' => $file,
                    'url'    => $this->config['domain'] . $fiename,
                    'model'  => $options['model'] ?? 'file',
                    'driver' => 'cos'
                ]);
                return $this->config['domain'] . $fiename;
            } else {
                $this->forceError('上传文件打开失败');
                return false;
            }
        } catch (\Exception $e) {
            $this->forceError($e->getMessage());
            return false;
        }
    }

    public function getThumbUrl(string $image, $width = 0, $height = 0, $method = 0, bool $water = false)
    {
        list($thumb_width, $thumb_height, $thumb_method) = $this->getThumbArgs($width, $height, $method);
        if ($thumb_method == 1) {
            $image .= "?imageMogr2/thumbnail/{$thumb_width}x{$thumb_height}";
        } elseif ($thumb_method == 3) {
            $image .= "?imageMogr2/crop/{$thumb_width}x{$thumb_height}/gravity/center";
        } elseif ($thumb_method == 4) {
            $image .= "?imageMogr2/crop/{$thumb_width}x{$thumb_height}/gravity/northwest";
        } elseif ($thumb_method == 5) {
            $image .= "?imageMogr2/crop/{$thumb_width}x{$thumb_height}/gravity/southeast";
        } else {
            $image .= "?imageMogr2/thumbnail/{$thumb_width}x{$thumb_height}!";
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
        return [$width, $height, $method];
    }

}