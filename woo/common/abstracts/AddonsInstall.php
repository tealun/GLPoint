<?php
declare (strict_types=1);

namespace woo\common\abstracts;

use think\facade\Db;

abstract class AddonsInstall extends AppInstall
{
    public function __construct(string $targetName = '')
    {
        $this->targetName = $targetName ?: $this->getName();
        $this->targetPath = app()->addons->getAddonPath($this->targetName);
        $this->nameSpace = "\\addons\\" . $this->targetName;
        parent::__construct($targetName);
    }

    protected function addAddonSetting($setting, string $prefix="__PREFIX__", $parentReplace = "__PARENT_ID__")
    {
        // 可以是二维数组
        if (is_array($setting) && isset($setting[0]) && is_array($setting[0])) {
            foreach ($setting as $key => &$item) {
                if (empty($item['var'])) {
                    unset($setting[$key]);
                    continue;
                }
                if (strpos($item['var'], $this->targetName . '_') !== 0) {
                    $item['var'] =  $this->targetName . '_' . $item['var'];
                }
                $item['addon_id'] = -1;
                $item['type'] = !empty($item['type']) ? $item['type'] : 'text';
                $item['title'] = !empty($item['title']) ? $item['title'] : '配置' . $key;
            }
            try {
                model('AddonSetting')->saveAll($setting);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            return true;
        }
        // 可以是字符串的sql语句
        if (is_string($setting)) {
            $sql = str_replace($prefix, get_db_config('prefix'), $setting);
            $sql = str_replace($parentReplace, '-1', $sql);
            $sql = preg_replace('/,\s\d{10}/i', ', ' . time(), $sql);
            try {
                Db::execute($sql);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
        return true;
    }
}