<?php
declare (strict_types = 1);

namespace woo\admin\controller;

use app\common\controller\Admin;
use woo\common\Upload;
use woo\common\annotation\Ps;

class Attachement extends Admin
{

    public function index()
    {
        if (get_installed_addons('woofinder')) {
            $this->addAction('c','附件管理插件', (string)addons_url('woofinder://index/index'), 'new_tab btn-3', '', -2);
        }
        return parent::index();
    }

    /**
     * @Ps(name="上传")
     */
    public function upload()
    {
        $upload = new Upload($this->request->post());
        $filepath = $upload->putFile($this->request->file('upload'));

        if ($filepath) {
            return json([
                'code' => 0,
                'type' => 'success',
                'message' => '',
                'url' => $filepath
            ]);
        } else {
            return json([
                'code' => 0,
                'type' => 'error',
                'message' => $upload->getError()[0] ?? '上传错误'
            ]);
        }
    }

    /**
     * 目前没有ck了 暂时不用
     * @Ps(name="Ckeditor上传")
     */
    protected function ckuploader()
    {
        $this->args['accept'] = strtolower(trim($this->args['accept'] ?? 'file'));
        if (empty($this->args['model'])) {
            $this->args['model'] = 'ckeditor';
        }
        $upload = new Upload($this->args);
        $filepath = $upload->putFile($this->request->file('upload'));
        if ($filepath) {
            return json([
                'uploaded' => 1,
                'message' => '文件上传成功',
                'url' =>  $filepath
            ]);
        } else {
            return json([
                'uploaded' => -1,
                'message' => $upload->getError()[0] ?? '上传错误'
            ]);
        }
    }
}