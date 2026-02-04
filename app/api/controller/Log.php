<?php
declare (strict_types = 1);
namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\Auth;
use think\facade\Log as LogLib;

class Log extends Api
{
    /**
     * @ApiInfo("获取系统日志",desc="获取系统操作日志列表",method="GET",login=true)
     * @Param("page",require=false,type="integer",desc="页码")
     * @Param("limit",require=false,type="integer",desc="每页数量")
     */
    public function index()
    {
        try {
            $page = $this->request->param('page/d', 1);
            $limit = $this->request->param('limit/d', 10);

            $where = [];
            
            // 管理员检索
            if($admin_id = $this->request->param('admin_id/d')) {
                $where[] = ['admin_id', '=', $admin_id];
            }

            // 用户检索
            if($user_id = $this->request->param('user_id/d')) {
                $where[] = ['user_id', '=', $user_id];
            }

            // 时间范围
            if($daterange = $this->request->param('daterange')) {
                $dates = explode(' - ', $daterange);
                $where[] = ['create_time', 'between', [
                    strtotime($dates[0]),
                    strtotime($dates[1])
                ]];
            }

            $data = model('Log')
                ->where($where)
                ->order('id DESC')
                ->paginate([
                    'list_rows' => $limit,
                    'page' => $page
                ]);

            return $this->success('获取成功', $data);

        } catch(\Exception $e) {
            LogLib::error('获取日志列表失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("删除日志",desc="删除指定日志记录",method="DELETE",login=true)
     * @Param("id",require=true,type="integer",desc="日志ID")
     */
    public function delete($id = null)
    {
        try {
            $log = model('Log')->find($id);
            if(!$log) {
                return $this->error('日志不存在');
            }

            if($log->delete()) {
                return $this->success('删除成功');
            }
            return $this->error('删除失败');

        } catch(\Exception $e) {
            LogLib::error('删除日志失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("清空日志",desc="清空所有日志记录",method="DELETE",login=true)
     */
    public function clear()
    {
        try {
            model('Log')->where('id', '>', 0)->delete();
            return $this->success('清空成功');

        } catch(\Exception $e) {
            LogLib::error('清空日志失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("日志详情",desc="获取日志详细信息",method="GET",login=true) 
     * @Param("id",require=true,type="integer",desc="日志ID")
     */
    public function detail($id)
    {
        try {
            $log = model('Log')->find($id);
            if(!$log) {
                return $this->error('日志不存在');
            }

            return $this->success('获取成功', $log);

        } catch(\Exception $e) {
            LogLib::error('获取日志详情失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * @ApiInfo("导出日志",desc="导出系统日志记录",method="GET",login=true)
     */
    public function export()
    {
        try {
            $where = [];
            
            // 管理员检索
            if($admin_id = $this->request->param('admin_id/d')) {
                $where[] = ['admin_id', '=', $admin_id];
            }

            // 用户检索
            if($user_id = $this->request->param('user_id/d')) {
                $where[] = ['user_id', '=', $user_id];
            }

            // 时间范围
            if($daterange = $this->request->param('daterange')) {
                $dates = explode(' - ', $daterange);
                $where[] = ['create_time', 'between', [
                    strtotime($dates[0]),
                    strtotime($dates[1])
                ]];
            }

            $logs = model('Log')
                ->where($where)
                ->order('id DESC')
                ->select()
                ->toArray();

            // 导出处理
            $filename = '系统日志_' . date('YmdHis') . '.xlsx';
            return download_excel($logs, $filename, [
                'ID' => 'id',
                '管理员' => 'admin.username',
                '用户' => 'user.username',
                '操作' => 'action',
                'IP地址' => 'ip',
                '创建时间' => 'create_time'
            ]);

        } catch(\Exception $e) {
            LogLib::error('导出日志失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }
}
