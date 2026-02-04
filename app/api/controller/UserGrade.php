<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\model\UserGrade as UserGradeModel;
use app\common\controller\Api;

/**
 * 用户等级控制器
 * @Controller("用户等级",module="用户",desc="等级列表、等级说明")
 */
class UserGrade extends Api
{
    /**
     * 获取所有等级
     * @ApiInfo(value="等级列表",method="GET",login=false)
     * @Returns(name="list", type="array", desc="等级列表")
     */
    public function index()
    {
        // 获取所有用户等级
        $grades = UserGradeModel::select();
        return $this->success('获取成功', $grades);
    }
}
