<?php

/**
 * 你把当前trait文件理解为模型文件【app\common\model\AdminRule】的一部分，自定义代码都定义在当前文件中
 */

namespace app\common\model\traits;

trait AdminRuleTrait
{
	protected function afterStart()
	{
		parent::{__FUNCTION__}();
	}
}
