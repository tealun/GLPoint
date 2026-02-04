<?php

declare(strict_types=1);

namespace app\admin\controller;

class ScoreRule extends \app\common\controller\Admin
{
	/**
	 * 列表操作
	 */
	public function index()
	{
		// 自定义列表的一些业务情况 ...
		// 比如需要自定义条件： $this->local['where'][] = ['字段','符号','值'];// 支持也更多的条件传递方式
		// 可以通过 $this->mdl 获取到当前控制器对应的同名模型实例  $this->args 获取到url参数
		// $this->mdl->tableTab['basic'] 获取到basic的Tab定义实现动态改变相关数据
		// 比如：$this->mdl->tableTab['basic']['item_tool_bar'][] = [] 实现自定义项目按钮
		// 比如：$this->mdl->tableTab['basic']['tool_bar'][] = [] 实现添加表格头部按钮
		// 比如：$this->mdl->tableTab['basic']['list_fields']= [] 实现独立控制列表字段项
		// 比如：$this->mdl->tableTab['basic']['list_filters']= [] 实现独立控制搜索字段项
		// $this->local['header_title'] = '自定义标题';
		// $this->setHeaderInfo('ex_title', '自定义副标题');
		// $this->setHeaderInfo('ex_title_href', '副标题链接');
		// $this->setHeaderInfo('tip', '自定义网页提示语');
		// $this->addAction('随意的唯一标识', '按钮名', 'URL地址', '类名自定义类名；btn-0到btn-17设置按钮样式', '图标', 排序权重, JS函数名（然后自定义对应的函数名，默认false）);// 自定义顶部按钮
		// $this->assign->addCss('/files/loaders/loaders');// 添加自己的css文件
		// $this->assign->addJs('/js/jquery', true);// 添加自己的js文件 true 表示js加到body结尾 反之加到head中
		// $this->assign->setScriptData('myvar', 'test');// 添加自己的全局Js变量值  js代码中通过 ：woo_script_vars.myvar 获取
		// 调用父类方法
		return parent::{__FUNCTION__}();
	}


	/**
	 * 添加操作
	 */
	public function create()
	{
		// 自定义添加的一些业务情况 ...
		// 比如需要设置字段的默认值：  $this->setFormValue('date', date('Y-m-d'));
		// 比如需要添加的时候改变表单类型： $this->mdl->form[字段]['elem'] = '类型'
		// 调用父类方法
		return parent::{__FUNCTION__}();
	}


	/**
	 * 修改操作
	 */
	public function modify()
	{
		// 自定义修改的一些业务情况 ...
		// 比如需要设置字段的默认值：  $this->setFormValue('date', date('Y-m-d'));
		// 比如需要修改的时候改变表单类型： $this->mdl->form[字段]['elem'] = '类型'
		// 比如需要自定义条件： $this->local['where'][] = ['字段','符号','值'];
		// 调用父类方法
		return parent::{__FUNCTION__}();
	}


	/**
	 * 删除操作
	 */
	public function delete()
	{
		return parent::{__FUNCTION__}();
	}


	/**
	 * 批量删除操作
	 */
	public function batchDelete()
	{
		return parent::{__FUNCTION__}();
	}


	/**
	 * 详情操作
	 */
	public function detail()
	{
		return parent::{__FUNCTION__}();
	}


	/**
	 * 列表开关操作
	 */
	public function ajaxSwitch()
	{
		return parent::{__FUNCTION__}();
	}


	/**
	 * 回收操作
	 */
	public function deleteIndex()
	{
		return parent::{__FUNCTION__}();
	}


	/**
	 * 恢复操作
	 */
	public function restore()
	{
		return parent::{__FUNCTION__}();
	}


	/**
	 * 批量恢复操作
	 */
	public function batchRestore()
	{
		return parent::{__FUNCTION__}();
	}


	/**
	 * 强制删除操作
	 */
	public function forceDelete()
	{
		return parent::{__FUNCTION__}();
	}


	/**
	 * 批量强制删除操作
	 */
	public function forceBatchDelete()
	{
		return parent::{__FUNCTION__}();
	}


	/**
	 * 排序渲染操作
	 */
	public function sort()
	{
		return parent::{__FUNCTION__}();
	}


	/**
	 * 排序数据数据提交操作
	 */
	public function updateSort()
	{
		return parent::{__FUNCTION__}();
	}
}
