<?php

/**
 * 你把当前trait文件理解为模型文件【app\common\model\Sign】的一部分，自定义代码都定义在当前文件中
 */

namespace app\common\model\traits;

trait SignTrait
{
	protected function afterStart()
	{
		parent::{__FUNCTION__}();
		// 代码执行到这里的时候已经 直接执行过了start方法 所以start定义的属性都可以获取到 当然也可以在该文件定义更多的自定义属性和方法
		// $this->form[字段名] =  动态修改字段的某个属性;
		// $this->form[字段名]['filter'] =  function($value){};// 自定义字段提交以后的数据处理
		// $this->form[字段名]['options'] = dict(模型名, 字段名);// 利用字典功能把字段的选项做活
		// 建议多了解模型事件、模型获取器、修改器；它们都会成为你开发的利器。可以多搜索下\woo\common\model下的文件里面很多模型都有定义模型事件，去多理解下。
	}


	/**
	 * 模型事件示范
	 * 自执行时机：新增后
	 * 一共11个，自行查阅文档
	 */
	public function afterInsertCall()
	{
		// 调用父类同名方法，防止父类定义的模型事件代码丢失
		$parent_return = parent::{__FUNCTION__}();
		// 你的自定义代码 ...

		return $parent_return;
	}
}
