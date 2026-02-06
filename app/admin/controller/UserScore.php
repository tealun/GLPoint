<?php

declare(strict_types=1);

namespace app\admin\controller;

use think\facade\Log;
use woo\common\helper\Excel;
use woo\common\Upload;

class UserScore extends \woo\admin\controller\UserScore
{
    /**
     * Dashboard 展示积分各项指标和图表
     * 可展示内容建议：
     * 1. 积分累计奖励与扣除数量、累计增长趋势图
     * 2. 所有用户积分的周、月、年排名
     * 3. 所有用户积分的周、月、年奖励与扣除数据
     * 4. 每次积分奖励与扣除数值的分布情况
     * 5. 积分余额分布（区间分布、TOP N 用户）
     * 6. 积分获取与扣除的来源/用途分析
     * 7. 活跃用户积分变化与对比
     * 8. 新增用户与老用户积分奖励对比
     * 9. 积分异常波动预警
     */
    public function dashboard()
    {
        $mdl = model('UserScore');
        Log::debug('【调试】UserScore dashboard 开始');

        // 只统计未软删除的数据
        // 正确写法：delete_time === 0
        $notDeleted = ['delete_time' => 0];

        // 1. 积分累计奖励与奖励人数
        $totalReward = $mdl->where($notDeleted)->where('score', '>', 0)->sum('score');
        $rewardUserCount = $mdl->where($notDeleted)->where('score', '>', 0)->group('user_id')->count();
        Log::debug('【调试】累计奖励: ' . $totalReward . '，奖励人数: ' . $rewardUserCount);

        // 积分增长趋势
        $growthData = $mdl
            ->where($notDeleted)
            ->field("FROM_UNIXTIME(create_time, '%Y-%m-%d') as date, SUM(CASE WHEN score > 0 THEN score ELSE 0 END) as reward")
            ->group('date')
            ->order('date')
            ->select();
        Log::debug('【调试】积分增长趋势数据: ' . json_encode($growthData, JSON_UNESCAPED_UNICODE));

		// 周排行榜（本周积分排行）
		$weekRank = $mdl
			->where($notDeleted)
			->field('user_id, SUM(score) as total')
			->where('create_time', '>=', strtotime('this week'))
			->group('user_id')
			->order('total', 'desc')
			->limit(10)
			->select();
		Log::debug('【调试】本周排行榜: ' . json_encode($weekRank, JSON_UNESCAPED_UNICODE));

		// 月排行榜（本月积分排行）
		$monthRank = $mdl
			->where($notDeleted)
			->field('user_id, SUM(score) as total')
			->where('create_time', '>=', strtotime(date('Y-m-01')))
			->group('user_id')
			->order('total', 'desc')
			->limit(10)
			->select();
		Log::debug('【调试】本月排行榜: ' . json_encode($monthRank, JSON_UNESCAPED_UNICODE));

		// 年排行榜（本年积分排行）
		$yearRank = $mdl
			->where($notDeleted)
			->field('user_id, SUM(score) as total')
			->where('create_time', '>=', strtotime(date('Y-01-01')))
			->group('user_id')
			->order('total', 'desc')
			->limit(10)
			->select();
		Log::debug('【调试】本年排行榜: ' . json_encode($yearRank, JSON_UNESCAPED_UNICODE));

        // 总榜（累计积分排行）
        $totalRank = $mdl
            ->where($notDeleted)
            ->field('user_id, SUM(score) as total')
            ->group('user_id')
            ->order('total', 'desc')
            ->limit(10)
            ->select();
        Log::debug('【调试】总榜: ' . json_encode($totalRank, JSON_UNESCAPED_UNICODE));

        // 周、月、年奖励及奖励人数
        $weekStats = $mdl
            ->where($notDeleted)
            ->field("SUM(CASE WHEN score > 0 THEN score ELSE 0 END) as reward")
            ->where('create_time', '>=', strtotime('this week'))
            ->find();
        $weekStats['reward_user_count'] = $mdl->where($notDeleted)->where('score', '>', 0)->where('create_time', '>=', strtotime('this week'))->distinct(true)->count('user_id');
        Log::debug('【调试】本周统计: ' . json_encode($weekStats, JSON_UNESCAPED_UNICODE));

        $monthStats = $mdl
            ->where($notDeleted)
            ->field("SUM(CASE WHEN score > 0 THEN score ELSE 0 END) as reward")
            ->where('create_time', '>=', strtotime(date('Y-m-01')))
            ->find();
        $monthStats['reward_user_count'] = $mdl->where($notDeleted)->where('score', '>', 0)->where('create_time', '>=', strtotime(date('Y-m-01')))->distinct(true)->count('user_id');
        Log::debug('【调试】本月统计: ' . json_encode($monthStats, JSON_UNESCAPED_UNICODE));

        $yearStats = $mdl
            ->where($notDeleted)
            ->field("SUM(CASE WHEN score > 0 THEN score ELSE 0 END) as reward")
            ->where('create_time', '>=', strtotime(date('Y-01-01')))
            ->find();
        $yearStats['reward_user_count'] = $mdl->where($notDeleted)->where('score', '>', 0)->where('create_time', '>=', strtotime(date('Y-01-01')))->distinct(true)->count('user_id');
        Log::debug('【调试】本年统计: ' . json_encode($yearStats, JSON_UNESCAPED_UNICODE));

        // 奖励分布
        $rewardDistribution = $mdl
            ->where($notDeleted)
            ->where('score', '>', 0)
            ->field('score, COUNT(*) as count')
            ->group('score')
            ->order('score')
            ->select();
        Log::debug('【调试】奖励分布: ' . json_encode($rewardDistribution, JSON_UNESCAPED_UNICODE));

        // 余额分布、排行榜、奖励来源等其它统计保持不变
		// 使用 User 模型的 score 字段进行积分余额分布统计
		$balanceDistribution = model('User')
			->where('delete_time', 0)
			->field("CASE 
				WHEN score < 100 THEN '<100'
				WHEN score < 500 THEN '100-499'
				WHEN score < 1000 THEN '500-999'
				WHEN score < 2000 THEN '1000-1999'
				WHEN score < 5000 THEN '2000-4999'
				WHEN score < 10000 THEN '5000-9999'
				WHEN score < 20000 THEN '10000-19999'
				ELSE '20000+' END as score_range, COUNT(*) as count")
			->group('score_range')
			->select();

        $topUsers = $mdl
            ->where($notDeleted)
            ->field('user_id, after as score')
            ->order('after', 'desc')
            ->limit(10)
            ->select();

        $rewardSources = $mdl
            ->where($notDeleted)
            ->where('score', '>', 0)
            ->field('score_rule_id, SUM(score) as total')
            ->group('score_rule_id')
            ->select();

        // 获取用户ID到昵称的映射
        $userIds = [];
        // 收集所有榜单涉及的user_id
        $collectUserIds = function($list) use (&$userIds) {
            foreach ($list as $item) {
                if (isset($item['user_id'])) {
                    $userIds[] = $item['user_id'];
                }
            }
        };

        $collectUserIds($weekRank);
        $collectUserIds($monthRank);
        $collectUserIds($yearRank);
        $collectUserIds($totalRank);

        // 查询所有涉及用户的昵称
        $userIds = array_unique($userIds);
        $userNicknames = [];
        if ($userIds) {
            $userNicknames = model('User')
                ->whereIn('id', $userIds)
                ->column('nickname', 'id');
        }

        // 替换榜单中的user_id为nickname（如无昵称则显示user_id）
        $replaceUserIdWithNickname = function(&$list) use ($userNicknames) {
            foreach ($list as &$item) {
                $uid = $item['user_id'];
                $item['user_nickname'] = $userNicknames[$uid] ?? $uid;
            }
        };
        $replaceUserIdWithNickname($weekRank);
        $replaceUserIdWithNickname($monthRank);
        $replaceUserIdWithNickname($yearRank);
        $replaceUserIdWithNickname($totalRank);

        Log::debug('【调试】UserScore dashboard 结束');

        // 渲染到视图
        return $this->fetch('dashboard', [
            'totalReward' => $totalReward,
            'rewardUserCount' => $rewardUserCount,
            'growthData' => $growthData,
            'weekRank' => $weekRank,
            'monthRank' => $monthRank,
            'yearRank' => $yearRank,
            'totalRank' => $totalRank,
            'weekStats' => $weekStats,
            'monthStats' => $monthStats,
            'yearStats' => $yearStats,
            'rewardDistribution' => $rewardDistribution,
            'balanceDistribution' => $balanceDistribution,
            'topUsers' => $topUsers,
            'rewardSources' => $rewardSources,
        ]);
    }

    /**
     * 积分排名页面 - 支持周/月/年/累计榜单查询和分页
     * 增加独立的排名页面，显示所有用户的积分排名
     */
    public function ranking()
    {
        // 获取查询参数
        $period = $this->request->param('period', 'week'); // 默认周榜
        $page = (int)$this->request->param('page', 1);
        $limit = 20; // 每页20条
        
        $mdl = model('UserScore');
        $notDeleted = ['delete_time' => 0];
        
        // 根据周期构建时间条件
        $timeWhere = [];
        switch ($period) {
            case 'week':
                $timeWhere[] = ['create_time', '>=', strtotime('this week')];
                break;
            case 'month':
                $timeWhere[] = ['create_time', '>=', strtotime(date('Y-m-01'))];
                break;
            case 'year':
                $timeWhere[] = ['create_time', '>=', strtotime(date('Y-01-01'))];
                break;
            case 'total':
                // 不加时间条件
                break;
            default:
                $timeWhere[] = ['create_time', '>=', strtotime('this week')];
                break;
        }
        
        // 查询总人数（用于分页）
        $totalUsers = $mdl
            ->where($notDeleted)
            ->where($timeWhere)
            ->group('user_id')
            ->count();
        
        // 查询当前页排名数据
        $rankList = $mdl
            ->where($notDeleted)
            ->where($timeWhere)
            ->field('user_id, SUM(score) as total')
            ->group('user_id')
            ->order('total', 'desc')
            ->limit(($page - 1) * $limit, $limit)
            ->select();
        
        // 获取用户昵称
        $userIds = array_column($rankList->toArray(), 'user_id');
        $userNicknames = [];
        if ($userIds) {
            $userNicknames = model('User')
                ->whereIn('id', $userIds)
                ->column('nickname', 'id');
        }
        
        // 替换user_id为nickname，并添加排名
        $startRank = ($page - 1) * $limit + 1;
        foreach ($rankList as $index => &$item) {
            $uid = $item['user_id'];
            $item['user_nickname'] = $userNicknames[$uid] ?? "用户{$uid}";
            $item['rank'] = $startRank + $index;
        }
        
        // 计算总页数
        $totalPages = ceil($totalUsers / $limit);
        
        // AJAX请求返回JSON
        if ($this->request->isAjax()) {
            return json([
                'code' => 0,
                'msg' => '获取成功',
                'data' => $rankList,
                'count' => $totalUsers,
                'page' => $page,
                'totalPages' => $totalPages
            ]);
        }
        
        // 正常请求渲染视图
        return $this->fetch('ranking', [
            'rankList' => $rankList,
            'period' => $period,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'limit' => $limit
        ]);
    }

    public function importScoreFromExcel()
    {
        if ($this->request->isPost()) {
            Log::debug('【调试】收到上传请求');
            // 使用 Upload 类上传
            $upload = new Upload([
                'type' => 'local',
                'validExt' => 'xlsx',
                'model' => 'UserScore'
            ]);
            $fileObj = $this->request->file('file');
            Log::debug('【调试】上传文件对象: ' . print_r($fileObj, true));
            $filepath = $upload->putFile($fileObj);

            Log::debug('【调试】putFile 返回路径: ' . $filepath);

            if (!$filepath) {
                Log::error('【调试】上传失败: ' . json_encode($upload->getError(), JSON_UNESCAPED_UNICODE));
                return $this->message($upload->getError()[0] ?? '上传错误', 'error', [], 6);
            }

            Log::debug('【调试】上传文件的完整路径: ' . $filepath);

            if (strpos($filepath, '/public/') === false) {
                $filepath = public_path() . ltrim($filepath, '/\\');
            }
            Log::debug('【调试】转换后的文件路径: ' . $filepath);

            // 确保文件路径正确
            $filepath = str_replace('\\', '/', $filepath);
            Log::debug('【调试】标准化后的文件路径: ' . $filepath);

            // 检查文件是否存在
            $fileExists = file_exists($filepath);
            Log::debug('【调试】file_exists 检查: ' . ($fileExists ? '存在' : '不存在') . '，路径: ' . $filepath);
            if (!$fileExists) {
                return $this->message('文件保存失败，未找到上传的Excel文件：' . $filepath, 'error', [], 6);
            }

            // 读取Excel文件
            Log::debug('【调试】开始读取Excel文件: ' . $filepath);
            try {
                $excel = new Excel();
                $data = $excel->readExcel($filepath);
                Log::debug('【调试】Excel读取结果: ' . json_encode($data, JSON_UNESCAPED_UNICODE));
            } catch (\Exception $e) {
                Log::error('【调试】Excel读取异常: ' . $e->getMessage());
                return $this->message('Excel读取失败：' . $e->getMessage(), 'error', [], 6);
            }

            if (empty($data['data'])) {
                Log::debug('【调试】Excel中没有数据: ' . json_encode($data, JSON_UNESCAPED_UNICODE));
                return $this->message('Excel中没有数据', 'error', [], 6);
            }
            // 获取所有涉及的昵称（包括“姓名”、“开具人”、“审核人”、“记录人”）
            $nicknames = array_map('trim', array_column($data['data'], '姓名'));
            $givers = array_map('trim', array_column($data['data'], '开具人'));
            $reviewers = array_map('trim', array_column($data['data'], '审核人'));
            $recorders = array_map('trim', array_column($data['data'], '记录人'));
            $allNicknames = array_unique(array_merge(
                $nicknames,
                array_filter($givers, function($v){return $v!=='';}),
                array_filter($reviewers, function($v){return $v!=='';}),
                array_filter($recorders, function($v){return $v!=='';})
            ));
            Log::debug('【调试】Excel昵称列（含开具人、审核人、记录人）: ' . json_encode($allNicknames, JSON_UNESCAPED_UNICODE));
            // 保证 userMap 的 key 也为 trim 后的昵称
            $userMap = [];
            if ($allNicknames) {
                $dbMap = model('User')->whereIn('nickname', $allNicknames)->column('id', 'nickname');
                foreach ($dbMap as $nickname => $id) {
                    $userMap[trim($nickname)] = $id;
                }
            }
            Log::debug('【调试】昵称到用户ID映射: ' . json_encode($userMap, JSON_UNESCAPED_UNICODE));

            $success = 0;
            $fail = 0;
            $failDetails = []; // 收集详细的失败信息
            
            // 收集所有未找到用户的姓名
            $notFoundNames = [];
            foreach ($data['data'] as $index => $row) {
                $name = trim($row['姓名']);
                if (!isset($userMap[$name]) || !$userMap[$name]) {
                    $notFoundNames[$name] = true;
                    if (!isset($failDetails[$name])) {
                        $failDetails[$name] = [];
                    }
                    $failDetails[$name][] = [
                        'row' => $index + 2, // Excel行号（从2开始，1是表头）
                        'reason' => '未找到该用户'
                    ];
                }
            }
            if (!empty($notFoundNames)) {
                Log::warning('【调试】以下姓名未找到用户，将跳过其所有记录: ' . implode('，', array_keys($notFoundNames)));
            }
            
            foreach ($data['data'] as $index => $row) {
                $rowNum = $index + 2; // Excel行号
                $name = trim($row['姓名']);
                
                if (isset($notFoundNames[$name])) {
                    Log::warning('【调试】跳过未找到用户的姓名: ' . $name);
                    $fail++;
                    continue;
                }
                
                $user_id = $userMap[$name] ?? null;
                if (!$user_id) {
                    Log::warning('【调试】未找到用户: ' . $row['姓名']);
                    $failDetails[$name][] = [
                        'row' => $rowNum,
                        'reason' => '未找到该用户'
                    ];
                    $fail++;
                    continue;
                }
                
                $giver_id = 0;
                if (isset($row['开具人']) && trim($row['开具人']) !== '') {
                    $giver_id = $userMap[trim($row['开具人'])] ?? 0;
                    Log::debug('【调试】开具人: ' . $row['开具人'] . '，ID: ' . $giver_id);
                }
                
                // 新增审核人和记录人
                $reviewer_id = 0;
                if (isset($row['审核人']) && trim($row['审核人']) !== '') {
                    $reviewer_id = $userMap[trim($row['审核人'])] ?? 0;
                    Log::debug('【调试】审核人: ' . $row['审核人'] . '，ID: ' . $reviewer_id);
                }
                
                $recorder_id = 0;
                if (isset($row['记录人']) && trim($row['记录人']) !== '') {
                    $recorder_id = $userMap[trim($row['记录人'])] ?? 0;
                    Log::debug('【调试】记录人: ' . $row['记录人'] . '，ID: ' . $recorder_id);
                }
                
                // 日期格式兼容处理
                $dateRaw = $row['日期'];
                if (is_numeric($dateRaw)) {
                    // 已经是时间戳或Excel数字日期
                    // 如果是Excel数字日期（如44197），需转为时间戳
                    if ($dateRaw > 30000) { // 简单判断，Excel序列号一般大于30000
                        // Excel序列号转时间戳
                        $unixDate = ($dateRaw - 25569) * 86400;
                        $create_time = (int)$unixDate;
                        $dateStr = date('Y-m-d', $create_time);
                    } else {
                        // 直接用
                        $create_time = (int)$dateRaw;
                        $dateStr = $dateRaw;
                    }
                } else {
                    // 字符串日期
                    $dateStr = str_replace('/', '-', $dateRaw);
                    $create_time = strtotime($dateStr) ?: time();
                }
                Log::debug('【调试】原始日期: ' . $row['日期'] . '，转换后: ' . $dateStr . '，时间戳: ' . $create_time);

                $data = [
                    'user_id' => $user_id,
                    'score' => floatval($row['积分']),
                    'remark' => $row['事由'] ?? '',
                    'giver_id' => $giver_id,
                    'reviewer_id' => $reviewer_id,
                    'recorder_id' => $recorder_id,
                    'create_time' => $create_time,
                    'status' => 1, // 默认状态为1（正常）
                    'score_rule_id' => 0, // 默认规则ID为0
                    'delete_time' => 0, // 默认未删除
                ];
                Log::debug('【调试】待保存数据: ' . json_encode($data, JSON_UNESCAPED_UNICODE));
                try {
                    model('UserScore')->create($data); // 强制插入
                    $success++;
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    Log::error('【调试】保存失败: ' . $errorMsg);
                    if (!isset($failDetails[$name])) {
                        $failDetails[$name] = [];
                    }
                    $failDetails[$name][] = [
                        'row' => $rowNum,
                        'reason' => '保存失败: ' . $errorMsg,
                        'data' => $row
                    ];
                    $fail++;
                }
            }

            Log::debug("【调试】导入完成，成功{$success}条，失败{$fail}条");
            
            // 根据结果返回不同的消息
            if ($fail === 0) {
                // 全部成功 - 延长显示时间并增加提示
                $successMsg = "<div style='font-size:16px;'><i class='layui-icon layui-icon-ok-circle' style='font-size:20px;color:#5FB878;'></i> 导入成功！</div>";
                $successMsg .= "<div style='margin-top:10px;font-size:14px;'>共成功导入 <span style='color:#5FB878;font-weight:bold;font-size:18px;'>{$success}</span> 条记录</div>";
                return $this->message($successMsg, 'success', [], 5);
            } else if ($success === 0) {
                // 全部失败 - 生成纯文本和HTML两个版本
                $errorText = "导入失败！共{$fail}条记录失败\n\n";
                $errorMsg = "导入失败！共{$fail}条记录失败";
                $errorMsg .= "<button onclick='copyErrorText()' style='margin-left:10px;padding:4px 12px;background:#409eff;color:#fff;border:none;border-radius:3px;cursor:pointer;'>复制错误信息</button><br><br>";
                $errorMsg .= "<div id='error-details' style='text-align:left;max-height:300px;overflow-y:auto;user-select:text;'>";
                foreach ($failDetails as $name => $errors) {
                    $errorText .= "【{$name}】\n";
                    $errorMsg .= "<strong>【{$name}】</strong><br>";
                    foreach ($errors as $error) {
                        $errorText .= "  • 第{$error['row']}行: {$error['reason']}\n";
                        $errorMsg .= "  • 第{$error['row']}行: {$error['reason']}<br>";
                    }
                }
                $errorMsg .= "</div>";
                $errorMsg .= "<textarea id='error-text' style='position:absolute;left:-9999px;'>" . htmlspecialchars($errorText) . "</textarea>";
                $errorMsg .= "<script>function copyErrorText(){var t=document.getElementById('error-text');t.select();document.execCommand('copy');alert('错误信息已复制到剪贴板');}</script>";
                // 失败时延长显示时间到10秒
                return $this->message($errorMsg, 'error', [], 10);
            } else {
                // 部分成功，部分失败 - 生成纯文本和HTML两个版本
                $errorText = "导入部分完成！成功{$success}条，失败{$fail}条\n\n失败记录：\n";
                $errorMsg = "导入部分完成！成功{$success}条，失败{$fail}条";
                $errorMsg .= "<button onclick='copyErrorText()' style='margin-left:10px;padding:4px 12px;background:#409eff;color:#fff;border:none;border-radius:3px;cursor:pointer;'>复制错误信息</button><br><br>";
                $errorMsg .= "<div id='error-details' style='text-align:left;max-height:300px;overflow-y:auto;user-select:text;'>";
                $errorMsg .= "<strong style='color:#f56c6c;'>失败记录：</strong><br>";
                foreach ($failDetails as $name => $errors) {
                    $errorText .= "【{$name}】\n";
                    $errorMsg .= "<strong>【{$name}】</strong><br>";
                    foreach ($errors as $error) {
                        $errorText .= "  • 第{$error['row']}行: {$error['reason']}\n";
                        $errorMsg .= "  • 第{$error['row']}行: {$error['reason']}<br>";
                    }
                }
                $errorMsg .= "</div>";
                $errorMsg .= "<textarea id='error-text' style='position:absolute;left:-9999px;'>" . htmlspecialchars($errorText) . "</textarea>";
                $errorMsg .= "<script>function copyErrorText(){var t=document.getElementById('error-text');t.select();document.execCommand('copy');alert('错误信息已复制到剪贴板');}</script>";
                // 部分失败时也延长显示时间到8秒
                return $this->message($errorMsg, 'warn', [], 8);
            }
        } else {
            Log::debug('【调试】UserScore importScoreFromExcel 返回上传页面');
            // 返回上传页面
            $this->assign('title', '导入积分数据');
            $this->assign('description', '请上传包含姓名、积分、事由等信息的Excel文件。');
            $this->assign('uploadUrl', url('import_score_from_excel'));
            $this->assign('fileTypes', 'xlsx,xls');
            $this->assign('maxSize', 1024 * 1024 * 5); // 5MB

            Log::debug('【调试】UserScore importScoreFromExcel 返回上传页面');

            return $this->fetch('import_score');
        }
    }
}