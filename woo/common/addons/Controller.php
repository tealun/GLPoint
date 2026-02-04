<?php
declare (strict_types = 1);

namespace woo\common\addons;

class Controller extends  \woo\common\controller\Controller
{
    /**
     * 插件配置
     * @var array
     */
    protected $setting = [];
    /**
     * 插件信息
     * @var array
     */
    protected $addonInfo = [];

    protected $addonName;



    protected function initialize()
    {
        $this->addonName = $this->request->addon;
        $this->addonInfo = get_installed_addons($this->addonName);
        parent::initialize();
        $this->assign->setting = $this->setting = get_addons_setting($this->request->addon);
        $this->setTitle($this->addonInfo['title'] ?? $this->addonName);
    }


}