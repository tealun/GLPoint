<?php

declare(strict_types=1);

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\ScoreCategory;
use app\common\model\ScoreRule;
use think\facade\Log;
use app\api\library\ListToTree;

/**
 * 积分规则控制器
 * @Controller("积分规则",module="积分",desc="规则分类、规则列表")
 */
class Rules extends Api
{
    /**
     * 获取积分规则分类列表
     */
    public function categories()
    {
        try {
            $CateGories = new ScoreCategory();
            $categories = $CateGories
                ->order('list_order DESC')
                ->select();
            if ($categories->isEmpty()) {
                return $this->error('没有找到积分规则分类');
            }
            Log::info('获取积分规则分类: ' . json_encode($categories));
            // 将平级数组转换为树形结构
            $categories = ListToTree::toTree(
                $categories->toArray(),
                'id',
                'parent_id',
                'children'
            );
            Log::info('获取积分规则分类成功: ' . json_encode($categories));
            return $this->success('获取成功', [
                'categories' => $categories
            ]);

        } catch(\Exception $e) {
            Log::error('获取分类失败: ' . $e->getMessage());
            return $this->error('获取分类失败');
        }
    }

    /**
     * 获取规则条目列表
     */
    public function index()
    {
        try {
            $params = $this->request->isPost() ? $this->request->post() : $this->request->get();
            if (isset($params['category_id'])) {
                $categoryId = $params['category_id'];
                if (!$categoryId && !isset($params['category_id'])) {
                    return $this->error('缺少 category_id 参数');
                }
                if (!is_numeric($categoryId)) {
                    return $this->error('category_id 参数必须是数字');
                }
                $categoryId = (int)$categoryId;
                $ScoreCategory = new ScoreCategory();
                $category = $ScoreCategory->find($categoryId);
                if (!$category) {
                    return $this->error('分类不存在');
                }
                $ScoreRule = new ScoreRule();
                $list = $ScoreRule->where('score_category_id', $categoryId)->select();
            } else {
                // 无 category_id，返回所有未删除的积分规则条目
                $ScoreRule = new ScoreRule();
                $list = $ScoreRule->where('delete_time', 0)->select();
            }
            return $this->success('获取成功', $list->toArray());
        } catch (\Exception $e) {
            Log::error('获取规则条目失败: ' . $e->getMessage());
            return $this->error('获取规则条目失败');
        }
    }

    
}
