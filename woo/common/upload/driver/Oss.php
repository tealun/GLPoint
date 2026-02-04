<?php
declare (strict_types=1);


namespace woo\common\upload\driver;

use think\facade\Event;
use woo\common\helper\Str;
use woo\common\upload\Driver;
use OSS\OssClient;
use OSS\Core\OssException;

class Oss extends Driver
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->handler =  new OssClient($this->config['ak'], $this->config['sk'], $this->config['endpoint']);
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
            $this->handler->uploadFile($this->config['bucket'],  $fiename, $file->getRealPath());
            Event::trigger('Upload', [
                'object' => $file,
                'url'    => $this->config['domain'] . $fiename,
                'model'  => $options['model'] ?? 'file',
                'driver' => 'oss'
            ]);
            return $this->config['domain'] . $fiename;
        } catch (OssException $e) {
            $this->forceError($e->getMessage());
            return false;
        }
    }

    public function getThumbUrl(string $image, $width = 0, $height = 0, $method = 0, bool $water = false)
    {
        list($thumb_width, $thumb_height, $thumb_method) = $this->getThumbArgs($width, $height, $method);
        if ($thumb_width >= 0) {
            if ($thumb_method == 4) {
                $image .= "?x-oss-process=image/crop,x_{$thumb_width},y_{$thumb_height},g_nw";
            } elseif ($thumb_method == 5) {
                $image .= "?x-oss-process=image/crop,x_{$thumb_width},y_{$thumb_height},g_se";
            } else {
                $image .= "?x-oss-process=image/resize,m_{$thumb_method},h_{$thumb_height},w_{$thumb_width},limit_0";
            }
        }
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
        $map = [1 => 'lfit', 2 => 'pad', 3 => 'fill', 4 => 4, 5 => 5, 6 => 'fixed'];

        return [$width, $height, $map[$method]];
    }
}