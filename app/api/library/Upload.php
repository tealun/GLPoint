<?php
namespace app\api\library;

use think\facade\Filesystem; 
use think\facade\Log;

class Upload
{
    /**
     * 上传文件
     */
    public static function uploadFile(string $file, string $path = ''): array
    {
        try {
            $savename = Filesystem::putFile($path, $file);
            return [
                'name' => $savename,
                'path' => Filesystem::getDiskConfig()['url'] . $savename
            ];
        } catch(\Exception $e) {
            Log::error('文件上传失败:' . $e->getMessage());
            throw new \Exception('文件上传失败');
        }
    }

    /**
     * 删除文件
     */  
    public static function deleteFile(string $filename): bool
    {
        try {
            return Filesystem::delete($filename);
        } catch(\Exception $e) {
            Log::error('文件删除失败:' . $e->getMessage());
            return false;
        }
    }
}
