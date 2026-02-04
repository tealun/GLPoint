<?php
declare (strict_types = 1);

namespace woo\common\event;

use woo\common\facade\Auth;
use woo\common\helper\Str;

class Upload
{
    public function handle($data)
    {
        if (!empty($data['object'])) {
            $data['title'] = $data['object']->getOriginalName();
            $data['ext']   = $data['object']->getOriginalExtension();
            $data['size']   = $data['object']->getSize();
            $data['hash']   = $data['object']->hash('md5');
        }
        if (isset($data['ext'])) {
            $data['ext'] = strtolower($data['ext']);
        }
        if (!empty($data['ext']) && in_array($data['ext'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $data['type'] = 'image';
            if (empty($data['width']) && !empty($data['object'])) {
                $path = $data['object']->getRealPath();
                if (is_file($path) && empty($data['width'])) {
                    $size = getimagesize($path);
                    $data['width'] = $size[0];
                    $data['height'] = $size[1];
                }
            }

        } else {
            $data['type'] = 'file';
        }
        // 上传用户
        $login = Auth::user();
        if (!empty($login) && isset($login['login_foreign_key'])) {
            $data[$login['login_foreign_key']] = $login['login_foreign_value'];
            if (isset($login['business_id'])) {
                $data['business_id'] = $login['business_id'];
            }
        }

        unset($data['object']);
        try {
            $folderModel = model('Folder')->where('ex_title', '=', $data['model'] ?? 'file')->find();
            if (empty($folderModel)) {
                $folder['ex_title'] = $data['model'] ?? 'file';
                $folder['parent_id'] = 0;
                if (get_model_name($folder['ex_title'])) {
                    $folder['title'] = model($folder['ex_title'])->cname;
                } else {
                    $folder['title'] = Str::studly($folder['ex_title']);
                }
                $folderModel = model('Folder');
                $folderModel->save($folder);
            }
            $data['folder_id'] = $folderModel->id;
            model('Attachement', '',true)->save($data);
        } catch (\Exception $e) {
            throw  new \Exception($e->getMessage());
        }
    }
}
