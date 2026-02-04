<?php
declare (strict_types = 1);
namespace app\api\library;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use think\facade\Log;
use app\common\model\{UserScore, User, Department, ScoreAppeal, ScoreRule};

class Export
{
    /**
     * 导出积分记录
     * @param int $userId 用户ID
     * @param array $params 查询参数
     * @return string|false 返回文件URL或false
     */
    public static function exportPoints(int $userId, array $params = [])
    {
        try {
            // 查询积分记录
            $query = UserScore::where('user_id', $userId)
                ->where('delete_time', 0)
                ->where('status', 1);

            // 时间范围筛选
            if (isset($params['start_time']) && isset($params['end_time'])) {
                $query->where('create_time', 'between', [
                    strtotime($params['start_time']),
                    strtotime($params['end_time'])
                ]);
            }

            $records = $query->with(['user', 'scoreRule'])
                ->order('create_time', 'desc')
                ->select()
                ->toArray();

            if (empty($records)) {
                return false;
            }

            // 创建Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('积分记录');

            // 设置表头
            $headers = ['ID', '积分', '变动前', '变动后', '规则', '备注', '创建时间'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getStyle($column . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0']
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
                $column++;
            }

            // 填充数据
            $row = 2;
            foreach ($records as $record) {
                $sheet->setCellValue('A' . $row, $record['id']);
                $sheet->setCellValue('B' . $row, $record['score']);
                $sheet->setCellValue('C' . $row, $record['before']);
                $sheet->setCellValue('D' . $row, $record['after']);
                $sheet->setCellValue('E' . $row, $record['score_rule']['rule_name'] ?? '');
                $sheet->setCellValue('F' . $row, $record['remark']);
                $sheet->setCellValue('G' . $row, date('Y-m-d H:i:s', $record['create_time']));
                $row++;
            }

            // 自动调整列宽
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // 保存文件
            $filename = 'points_' . date('YmdHis') . '.xlsx';
            $filepath = root_path() . 'public/uploads/exports/' . $filename;
            
            // 确保目录存在
            $dir = dirname($filepath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($filepath);

            return '/uploads/exports/' . $filename;

        } catch (\Exception $e) {
            Log::error('导出积分记录失败: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 导出部门信息
     * @return string|false
     */
    public static function exportDepartments()
    {
        try {
            $departments = Department::where('delete_time', 0)
                ->order('id', 'asc')
                ->select()
                ->toArray();

            if (empty($departments)) {
                return false;
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('部门信息');

            // 设置表头
            $headers = ['ID', '部门名称', '上级部门', '负责人', '排序', '创建时间'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getStyle($column . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0']
                    ]
                ]);
                $column++;
            }

            // 填充数据
            $row = 2;
            foreach ($departments as $dept) {
                $parent = $dept['parent_id'] > 0 
                    ? Department::where('id', $dept['parent_id'])->value('title')
                    : '顶级部门';
                
                $sheet->setCellValue('A' . $row, $dept['id']);
                $sheet->setCellValue('B' . $row, $dept['title'] ?? '');
                $sheet->setCellValue('C' . $row, $parent);
                $sheet->setCellValue('D' . $row, $dept['leader'] ?? '');
                $sheet->setCellValue('E' . $row, $dept['list_order'] ?? 0);
                $sheet->setCellValue('F' . $row, date('Y-m-d H:i:s', $dept['create_time']));
                $row++;
            }

            // 自动调整列宽
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'departments_' . date('YmdHis') . '.xlsx';
            $filepath = root_path() . 'public/uploads/exports/' . $filename;
            
            $dir = dirname($filepath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($filepath);

            return '/uploads/exports/' . $filename;

        } catch (\Exception $e) {
            Log::error('导出部门信息失败: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 导出申诉记录
     * @param int $userId 用户ID
     * @param array $params 查询参数
     * @return string|false
     */
    public static function exportAppeals(int $userId, array $params = [])
    {
        try {
            $user = User::find($userId);
            $query = ScoreAppeal::where('delete_time', 0);

            // 如果不是操作员，只能导出自己的申诉
            if (!$user || $user->user_group_id != 3) {
                $query->where('user_id', $userId);
            }

            // 状态筛选
            if (isset($params['status'])) {
                $query->where('status', $params['status']);
            }

            $appeals = $query->with(['userId', 'userScoreId', 'replyUserId'])
                ->order('create_time', 'desc')
                ->select()
                ->toArray();

            if (empty($appeals)) {
                return false;
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('申诉记录');

            // 设置表头
            $headers = ['ID', '申诉人', '积分记录ID', '申诉理由', '回复内容', '状态', '创建时间'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getStyle($column . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0']
                    ]
                ]);
                $column++;
            }

            // 填充数据
            $row = 2;
            $statusMap = [-1 => '已拒绝', 0 => '待处理', 1 => '已通过', 2 => '已取消'];
            foreach ($appeals as $appeal) {
                $sheet->setCellValue('A' . $row, $appeal['id']);
                $sheet->setCellValue('B' . $row, $appeal['user_id']['nickname'] ?? '');
                $sheet->setCellValue('C' . $row, $appeal['user_score_id']);
                $sheet->setCellValue('D' . $row, $appeal['reason']);
                $sheet->setCellValue('E' . $row, $appeal['reply'] ?? '');
                $sheet->setCellValue('F' . $row, $statusMap[$appeal['status']] ?? '未知');
                $sheet->setCellValue('G' . $row, date('Y-m-d H:i:s', $appeal['create_time']));
                $row++;
            }

            // 自动调整列宽
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'appeals_' . date('YmdHis') . '.xlsx';
            $filepath = root_path() . 'public/uploads/exports/' . $filename;
            
            $dir = dirname($filepath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($filepath);

            return '/uploads/exports/' . $filename;

        } catch (\Exception $e) {
            Log::error('导出申诉记录失败: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 导出用户信息
     * @param array $params 查询参数
     * @return string|false
     */
    public static function exportUsers(array $params = [])
    {
        try {
            $query = User::where('delete_time', 0)
                ->where('status', 'verified');

            // 部门筛选
            if (isset($params['department_id'])) {
                $query->where('department_id', $params['department_id']);
            }

            $users = $query->order('id', 'asc')
                ->select()
                ->toArray();

            if (empty($users)) {
                return false;
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('用户信息');

            // 设置表头
            $headers = ['ID', '用户名', '昵称', '手机号', '部门', '积分', '创建时间'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getStyle($column . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0']
                    ]
                ]);
                $column++;
            }

            // 填充数据
            $row = 2;
            foreach ($users as $user) {
                $dept = $user['department_id'] > 0 
                    ? Department::where('id', $user['department_id'])->value('title')
                    : '';
                
                $sheet->setCellValue('A' . $row, $user['id']);
                $sheet->setCellValue('B' . $row, $user['username']);
                $sheet->setCellValue('C' . $row, $user['nickname']);
                $sheet->setCellValue('D' . $row, $user['mobile'] ?? '');
                $sheet->setCellValue('E' . $row, $dept);
                $sheet->setCellValue('F' . $row, $user['score'] ?? 0);
                $sheet->setCellValue('G' . $row, date('Y-m-d H:i:s', $user['create_time']));
                $row++;
            }

            // 自动调整列宽
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'users_' . date('YmdHis') . '.xlsx';
            $filepath = root_path() . 'public/uploads/exports/' . $filename;
            
            $dir = dirname($filepath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($filepath);

            return '/uploads/exports/' . $filename;

        } catch (\Exception $e) {
            Log::error('导出用户信息失败: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 导出部门用户
     * @param int $departmentId 部门ID
     * @return string|false
     */
    public static function exportDepartmentUsers(int $departmentId)
    {
        try {
            $department = Department::find($departmentId);
            if (!$department) {
                return false;
            }

            // 获取所有子部门ID（包括自身）
            $departments = Department::field(['id', 'parent_id'])->select()->toArray();
            $allIds = [$departmentId];
            $queue = [$departmentId];
            
            while ($queue) {
                $current = array_shift($queue);
                foreach ($departments as $dept) {
                    if ($dept['parent_id'] == $current && !in_array($dept['id'], $allIds)) {
                        $allIds[] = $dept['id'];
                        $queue[] = $dept['id'];
                    }
                }
            }

            $users = User::where('delete_time', 0)
                ->where('status', 'verified')
                ->whereIn('department_id', $allIds)
                ->order('id', 'asc')
                ->select()
                ->toArray();

            if (empty($users)) {
                return false;
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($department->title . '用户');

            // 设置表头
            $headers = ['ID', '用户名', '昵称', '手机号', '部门', '积分', '创建时间'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getStyle($column . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0']
                    ]
                ]);
                $column++;
            }

            // 填充数据
            $row = 2;
            foreach ($users as $user) {
                $dept = $user['department_id'] > 0 
                    ? Department::where('id', $user['department_id'])->value('title')
                    : '';
                
                $sheet->setCellValue('A' . $row, $user['id']);
                $sheet->setCellValue('B' . $row, $user['username']);
                $sheet->setCellValue('C' . $row, $user['nickname']);
                $sheet->setCellValue('D' . $row, $user['mobile'] ?? '');
                $sheet->setCellValue('E' . $row, $dept);
                $sheet->setCellValue('F' . $row, $user['score'] ?? 0);
                $sheet->setCellValue('G' . $row, date('Y-m-d H:i:s', $user['create_time']));
                $row++;
            }

            // 自动调整列宽
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'department_' . $departmentId . '_users_' . date('YmdHis') . '.xlsx';
            $filepath = root_path() . 'public/uploads/exports/' . $filename;
            
            $dir = dirname($filepath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($filepath);

            return '/uploads/exports/' . $filename;

        } catch (\Exception $e) {
            Log::error('导出部门用户失败: ' . $e->getMessage());
            return false;
        }
    }
}
