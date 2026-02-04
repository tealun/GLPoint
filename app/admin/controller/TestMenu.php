<?php

declare(strict_types=1);

namespace app\admin\controller;

use woo\common\annotation\Ps;

/**
 * @Ps(false)
 */
class TestMenu extends \app\common\controller\Admin
{
	use \woo\common\controller\traits\Tree;

	/**
	 * 列表操作
	 */
	public function index()
	{
		// 开启ajax加载下级  如果数据量比较多 可以开启
		//$this->assign->options['is_ajax'] = true;
		// 关闭 添加一级分类 按钮
		//$this->local['tool_bar']['create'] = false;
		// 关闭 排序一级 分类 按钮
		//$this->local['tool_bar']['sortable'] = false;
		// 关闭 添加子分类 按钮
		//$this->local['item_tool_bar']['create_child'] = false;
		// 关闭 排序子分类 按钮
		//$this->local['item_tool_bar']['sort_child'] = false;
		// 关闭  编辑 按钮
		//$this->local['item_tool_bar']['modify'] = false;
		// 关闭删除 子分类 按钮
		//$this->local['item_tool_bar']['delete'] = false;
		// 添加一个字段显示 默认只显示 id和标题（主显字段）
		$this->local['fields'] = [
		   'children_count' => [
		       'title' => '子' . $this->mdl->cname . '数',
		       'templet' => '{{# if (d.children_count> 0){ }}{{d.children_count}}{{#} }}',
		       'style' => 'color:#36b368;'
		   ]
		];
		return $this->showList();
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
