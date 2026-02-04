<?php
namespace app\api\model;
use app\common\model\UserScore as UserScoreBase;

class UserScore extends UserScoreBase
{
    // 1. 统计指定用户的累计积分总数（所有非删除状态）
	public function getUserTotalScore($user_id)
	{
		return self::where('user_id', $user_id)
			->where('delete_time', 0)
			->where('status', 1) // 确保只统计直接奖励和审核通过的积分
			->sum('score');
	}

	// 2. 统计指定用户本周积分总数（自然周内所有非删除状态）
	public function getUserWeekScore($user_id)
	{
		$startOfWeek = strtotime('monday this week');
		$endOfWeek = strtotime('sunday this week 23:59:59');
		return self::where('user_id', $user_id)
			->where('delete_time', 0)
			->where('status', 1) // 确保只统计直接奖励和审核通过的积分
			->where('create_time', '>=', $startOfWeek)
			->where('create_time', '<=', $endOfWeek)
			->sum('score');
	}

	// 3. 统计指定用户本月积分总数（自然月内所有非删除状态）
	public function getUserMonthScore($user_id)
	{
		$startOfMonth = strtotime(date('Y-m-01 00:00:00'));
		$endOfMonth = strtotime(date('Y-m-t 23:59:59'));
		return self::where('user_id', $user_id)
			->where('delete_time', 0)
			->where('status', 1) // 确保只统计直接奖励和审核通过的积分
			->where('create_time', '>=', $startOfMonth)
			->where('create_time', '<=', $endOfMonth)
			->sum('score');
	}

	// 4. 获取指定用户本年的积分总数（自然年内所有非删除状态）
	public function getUserYearScore($user_id)
	{
		$startOfYear = strtotime(date('Y-01-01 00:00:00'));
		$endOfYear = strtotime(date('Y-12-31 23:59:59'));
		return self::where('user_id', $user_id)
			->where('delete_time', 0)
			->where('status', 1) // 确保只统计直接奖励和审核通过的积分
			->where('create_time', '>=', $startOfYear)
			->where('create_time', '<=', $endOfYear)
			->sum('score');
	}

	// 5. 获取指定用户的积分记录（分页）
	public function getUserScoreRecords($user_id, $page = 1, $limit = 10)
	{
		return self::where('user_id', $user_id)
			->where('delete_time', 0)
			->where('status', 1) // 确保只获取直接奖励和审核通过的积分
			->order('create_time', 'desc')
			->page($page, $limit)
			->select();
	}

	// 6. 获取指定用户的积分记录（不分页）
	public function getUserScoreRecordsAll($user_id)
	{
		return self::where('user_id', $user_id)
			->where('delete_time', 0)
			->where('status', 1) // 确保只获取直接奖励和审核通过的积分
			->order('create_time', 'desc')
			->select();
	}

	// 7. 获取指定用户的积分记录总数
	public function getUserScoreRecordCount($user_id)
	{
		return self::where('user_id', $user_id)
			->where('delete_time', 0)
			->where('status', 1) // 确保只获取直接奖励和审核通过的积分
			->count();
	}
	
}
