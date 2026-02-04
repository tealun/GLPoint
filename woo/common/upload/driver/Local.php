<?php
declare (strict_types=1);


namespace woo\common\upload\driver;

use think\facade\Config;
use think\facade\Db;
use think\facade\Filesystem;
use woo\common\helper\Str;
use woo\common\upload\Driver;
use think\facade\Event;

class Local extends Driver
{
    public function putFile(\think\file\UploadedFile $file, array $options = [])
    {
        $options = array_merge($this->config, $options);
        $parent_return = parent::{__FUNCTION__}($file, $options);
        if (!(true === $parent_return)) {
            return $parent_return;
        }

        try {
            $result = Filesystem::disk('public')->putFile(Str::snake($options['folder']), $file);
        } catch (\Exception $e) {
            $this->forceError($e->getMessage());
            return false;
        }
        if ($result) {
            $result = basename(Config::get('filesystem')['disks']['public']['root']) . '/' . str_replace("\\", "/", $result);
            if ($this->config['accept'] == 'images' && (isset($this->config['resizeWidth']) || isset($this->config['resizeHeight']))) {
                if (!$this->makeResize($result)) {
                    return false;
                }
            }
            $result = $this->config['domain'] . $result;
            Event::trigger('Upload', [
                'object' => $file,
                'url'    => $result,
                'model'  => $options['model'] ?? 'file',
                'driver' => 'local'
            ]);
        }
        return $result;
    }

    public function makeResize(string $imageSrc)
    {
        $realpath = root_path() . Config::get('woo.public_name') . '/' . $imageSrc;

        if (!is_file($realpath)) {
            $this->forceError('文件不存在');
        }
        try {
            $size = getimagesize($realpath);
        } catch (\Exception $e) {
            $this->forceError($e->getMessage());
            unlink($realpath);
            return false;
        }
        if (isset($this->config['resizeWidth'])) {
            $resizeWidth = (int) $this->config['resizeWidth'];
            $resizeHeight = (int) (isset($this->config['resizeHeight']) ?
                $this->config['resizeHeight']
                : ($resizeWidth * $size[1] / $size[0]));
        } else {
            $resizeHeight = (int)$this->config['resizeHeight'];
            $resizeWidth = (int) (isset($this->config['resizeWidth']) ?
                $this->config['resizeWidth']
                : ($resizeHeight * $size[0]) / $size[1]);
        }
        $resizeMethod = intval($this->config['resizeMethod'] ?? setting('upload_thumb_method'));
        $image = \think\Image::open($realpath);
        $image->thumb($resizeWidth, $resizeHeight, $resizeMethod)->save($realpath);
        return $imageSrc;
    }

    public function getThumbUrl(string $image, $width = 0, $height = 0, $method = 0, bool $water = false)
    {
        $realpath = $this->getFileRealPath($image);
        if (!is_file($realpath)) {
            return false;
        }
        $extension = get_ext($realpath);
        list($thumb_width, $thumb_height, $thumb_method) = $this->getThumbArgs($realpath, $width, $height, $method);
        $thumb_base_path = 'thumb/' . md5(md5($image) . "_{$thumb_width}_{$thumb_height}_{$thumb_method}") . "." . $extension;
        $thumb_path = Config::get('filesystem')['disks']['public']['root'] . '/' .$thumb_base_path;
        if (is_file($thumb_path)) {
            return $this->config['domain'] . 'uploads/' . $thumb_base_path;
        }
        return (string)url('index/thumb', ['src' => $image, 'w' => $width, 'h' => $height, 'm' => $method, 'wa' => $water]);
    }

    public function makeThumb(string $image, $width = 0, $height = 0, $method = 0, bool $water = false)
    {

        $realpath = $this->getFileRealPath($image);
        if (!is_file($realpath)) {
            return false;
        }
        $extension = get_ext($realpath);
        list($thumb_width, $thumb_height, $thumb_method) = $this->getThumbArgs($realpath, $width, $height, $method);
        $thumb_base_path = 'thumb/' . md5(md5($image) . "_{$thumb_width}_{$thumb_height}_{$thumb_method}") . "." . $extension;
        $thumb_path = Config::get('filesystem')['disks']['public']['root'] . '/' . $thumb_base_path;
        if (is_file($thumb_path)) {
            return $thumb_path;
        }
        if (!is_dir(Config::get('filesystem')['disks']['public']['root'] . DIRECTORY_SEPARATOR . 'thumb')) {
            mkdir(Config::get('filesystem')['disks']['public']['root'] . DIRECTORY_SEPARATOR . 'thumb', 0755, true);
        }
        $image = \think\Image::open($realpath);
        $image->thumb($thumb_width, $thumb_height, $thumb_method)->save($thumb_path);
        return $thumb_path;
    }

    protected function getThumbArgs(string $realpath, $width = 0, $height = 0, $method = 0)
    {
        $width = intval($width);
        $size = getimagesize($realpath);
        if ($width <= 0) {
            $width = $width != -1 ? intval(setting('upload_thumb_width')) : $size[0];
        }
        $height = intval($height);
        if ($height <= 0) {
            $height = $height != -1 ? intval(setting('upload_thumb_height')) : $size[1];
        }
        $method = intval($method);
        if (!in_array($method, [1, 2, 3, 4, 5, 6])) {
            $method = intval(setting('upload_thumb_method'));
        }
        return [$width, $height, $method];
    }

    protected function getFileRealPath($file)
    {
        $src = substr($file, strlen($this->config['domain']));
        return root_path() . Config::get('woo.public_name') . '/' . $src;
    }
}