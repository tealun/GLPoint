<?php
declare (strict_types = 1);
namespace app\api\controller;

use app\common\controller\Api;
use app\api\library\Auth;
use app\api\library\Export as ExportLib;
use think\facade\Log;

class Export extends Api
{
    /**
     * 导出积分记录
     * @ApiInfo("导出积分记录",desc="导出用户积分明细",method="GET",login=true)
     */
    public function points()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            // 获取查询参数
            $params = $this->request->get();
            
            // 导出积分记录
            $result = ExportLib::exportPoints($user['id'], $params);
            if(!$result) {
                return $this->error('导出失败');
            }

            return $this->success('导出成功', [
                'url' => $result
            ]);
            
        } catch(\Exception $e) {
            Log::error('导出积分记录失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * 导出部门信息
     * @ApiInfo("导出部门信息",desc="导出部门及成员信息",method="GET",login=true)
     */
    public function departments()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            // 导出部门信息
            $result = ExportLib::exportDepartments();
            if(!$result) {
                return $this->error('导出失败');
            }

            return $this->success('导出成功', [
                'url' => $result
            ]);

        } catch(\Exception $e) {
            Log::error('导出部门信息失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * 导出申诉记录
     * @ApiInfo("导出申诉记录",desc="导出积分申诉记录",method="GET",login=true)
     */
    public function appeals()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            // 获取查询参数
            $params = $this->request->get();

            // 导出申诉记录
            $result = ExportLib::exportAppeals($user['id'], $params);
            if(!$result) {
                return $this->error('导出失败');
            }

            return $this->success('导出成功', [
                'url' => $result
            ]);

        } catch(\Exception $e) {
            Log::error('导出申诉记录失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * 导出用户信息
     * @ApiInfo("导出用户信息",desc="导出用户基本信息",method="GET",login=true)
     */
    public function users()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            // 获取查询参数
            $params = $this->request->get();

            // 导出用户信息
            $result = ExportLib::exportUsers($params);
            if(!$result) {
                return $this->error('导出失败');
            }

            return $this->success('导出成功', [
                'url' => $result
            ]);

        } catch(\Exception $e) {
            Log::error('导出用户信息失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * 导出部门用户信息
     * @ApiInfo("导出部门用户",desc="导出指定部门的用户信息",method="GET",login=true)
     */
    public function departmentUsers()
    {
        try {
            $user = Auth::getUser();
            if(!$user) {
                return $this->error('请先登录');
            }

            $departmentId = $this->request->get('department_id/d');
            if(!$departmentId) {
                return $this->error('参数错误');
            }

            // 导出部门用户
            $result = ExportLib::exportDepartmentUsers($departmentId);
            if(!$result) {
                return $this->error('导出失败');
            }

            return $this->success('导出成功', [
                'url' => $result
            ]);

        } catch(\Exception $e) {
            Log::error('导出部门用户失败: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }
}
