<?php
namespace app\common\model\traits;

trait UserGradeTrait
{
	protected function afterStart()
	{
		parent::{__FUNCTION__}();
		// 代码执行到这里的时候已经 直接执行过了start方法 所以start定义的属性都可以获取到 当然也可以在该文件定义更多的自定义属性和方法
		// $this->form[字段名] =  动态修改字段的某个属性;
	}
}
