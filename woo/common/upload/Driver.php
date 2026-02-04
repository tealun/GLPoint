<?php
declare (strict_types=1);

namespace woo\common\upload;

use think\facade\Db;

abstract class Driver
{
    /**
     * 驱动句柄
     * @var object
     */
    protected $handler = null;

    /**
     * 上传参数
     * @var array
     */
    protected $config = [];

    /**
     * 错误信息
     * @var string
     */
    protected $error = [];

    public function __construct(array $config = [])
    {
        $this->checkConfig($config);
    }


    public function putFile(\think\file\UploadedFile $file, array $options = [])
    {
        $options = array_merge($this->config, $options);
        $result = $this->validateFile($file, $options);
        if (!$result) {
            return false;
        }
        if (setting('upload_is_check_uploaded') || isset($options['checkUploaded'])) {
            $result = $this->checkUploaded($file);
            if ($result) {
                return $result;
            }
        }
        return true;
    }

    //abstract public function  getThumbUrl(string $image, $width = 0, $height = 0, $method = 0, bool $water = false);

    protected function validateFile(\think\file\UploadedFile $file, array $options = [])
    {
        if (empty($options)) {
            $options = $this->config;
        }
//        pr($options);echo PHP_EOL;
//        pr($file->getOriginalName());
//        echo PHP_EOL;
//        pr($file->getOriginalExtension());
//        echo PHP_EOL;
//        pr($file->getSize());
//        echo PHP_EOL;
//        pr($file->getRealPath());

        if ($options['maxSize'] &&  $file->getSize() > $options['maxSize'] * 1024) {
            $this->forceError('上传文件不允许超过' . return_size($options['maxSize'] * 1024));
            return false;
        }

        if ($options['validExt'] && !in_array(strtolower($file->getOriginalExtension()), $options['validExt'])) {
            $this->forceError('上传文件后缀只允许是：' . implode(',', $options['validExt']));
            return false;
        }

        if ($options['forbiddenExt'] && in_array(strtolower($file->getOriginalExtension()), $options['forbiddenExt'])) {
            $this->forceError('上传文件后缀不允许是：' . $file->getOriginalExtension());
            return false;
        }
        if (isset($options['imageWidth']) || isset($options['imageHeight'])) {
            $sign = $options['imageSign'] ?? '<=';
            if ($sign == '=') {
                $sign = '==';
            }
            try {
                $size = getimagesize($file->getRealPath());
            } catch (\Exception $e) {
                $this->forceError($e->getMessage());
                return false;
            }
            if (isset($options['imageWidth'])) {
                $is = true;
                if ($sign == '<=' && $size[0] > $options['imageWidth']) {
                    $is = false;
                } elseif ($sign == '>=' && $size[0] < $options['imageWidth']) {
                    $is = false;
                } elseif ($size[0] != $options['imageWidth']) {
                    $is = false;
                }

                if (!$is) {
                    $this->forceError('上传图片的宽度应该' . $sign . $options['imageWidth']);
                    return false;
                }
            }

            if (isset($options['imageHeight'])) {
                $is = true;
                if ($sign == '<=' && $size[1] > $options['imageHeight']) {
                    $is = false;
                } elseif ($sign == '>=' && $size[1] < $options['imageHeight']) {
                    $is = false;
                } elseif ($size[1] != $options['imageHeight']) {
                    $is = false;
                }

                if (!$is) {
                    $this->forceError('上传图片的高度应该' . $sign . $options['imageHeight']);
                    return false;
                }
            }
        }
        return true;
    }

    public function checkConfig(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        $this->config['accept'] = $this->config['accept'] ?? 'file';
        $this->config['maxSize'] = $this->config['maxSize'] ?? ($this->config['accept'] == 'images' ? intval(setting('upload_image_max_size')) : intval(setting('upload_file_max_size')));
        $this->config['folder'] = $this->config['model'] ?? 'file';
        if (empty($this->config['folder'])) {
            $this->config['folder'] = 'file';
        }
        $this->config['validExt'] = $this->config['validExt'] ?? ($this->config['accept'] == 'images' ? setting('upload_image_valid_ext') : setting('upload_file_valid_ext'));
        if (is_string($this->config['validExt'])) {
            $this->config['validExt'] = explode('|', $this->config['validExt']);
        }
        $this->config['forbiddenExt'] = $this->config['forbiddenExt'] ?? ['exe', 'php', 'asp', 'bat', 'asa', 'vbs', 'php2'];
    }

    /**
     * 加设错误信息
     * @param $field
     * @param string $error
     * @return bool
     */
    public function forceError($field, $error = '')
    {
        if (is_string($field)) {
            if (!empty($error)) {
                $this->error[$field] = $error;
            } else {
                $this->error[] = $field;
            }
        } elseif (is_array($field)) {
            $this->error = array_merge($this->error, $field);
        }
        return true;
    }

    /**
     * 获取错误信息
     * @param string $field
     * @return array|mixed|string
     */
    public function getError(string $field = '')
    {
        if ($field) {
            return $this->error[$field] ?? '';
        }
        return $this->error;
    }

    public function handler()
    {
        return $this->handler;
    }

    protected function checkUploaded(\think\file\UploadedFile $file)
    {
        $hash = $file->hash('md5');
        $result = Db::name('Attachement')->where('hash', '=', $hash)->value('url');
        if ($result) {
            return $result;
        }
        return false;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->handler, $method], $args);
    }
}