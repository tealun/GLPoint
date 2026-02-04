/**
 * API服务层 - 统一封装所有业务接口
 * 优点：
 * 1. 集中管理接口调用
 * 2. 便于mock和测试
 * 3. 减少页面中的重复代码
 * 4. 统一错误处理
 */

import { http, API } from './request.js';

class ApiService {
  
  // ==================== 用户相关 ====================
  
  /**
   * 获取用户信息
   * @param {number} userId 用户ID
   */
  async getUserInfo(userId) {
    const res = await http.post(API.USER_INFO, { user_id: userId });
    return res.data;
  }

  /**
   * 更新用户信息
   * @param {object} data 用户数据
   */
  async updateUserInfo(data) {
    const res = await http.post(API.USER_UPDATE, data);
    return res.data;
  }

  /**
   * 上传用户头像
   * @param {string} filePath 图片路径
   */
  async uploadAvatar(filePath) {
    // 实现文件上传逻辑
    return new Promise((resolve, reject) => {
      wx.uploadFile({
        url: API.USER_AVATAR,
        filePath,
        name: 'avatar',
        header: {
          'Authorization': `Bearer ${wx.getStorageSync('token')}`
        },
        success: (res) => {
          const data = JSON.parse(res.data);
          if (data.code === 0) {
            resolve(data.data);
          } else {
            reject(new Error(data.message));
          }
        },
        fail: reject
      });
    });
  }

  // ==================== 积分相关 ====================

  /**
   * 获取积分列表
   * @param {object} params 查询参数
   */
  async getPointsList(params = {}) {
    const res = await http.post(API.POINTS_LIST, params);
    return res.data;
  }

  /**
   * 奖励积分
   * @param {object} data 奖励数据
   */
  async awardPoints(data) {
    const res = await http.post(API.POINTS_AWARD, data);
    return res.data;
  }

  /**
   * 申请积分
   * @param {object} data 申请数据
   */
  async requestPoints(data) {
    const res = await http.post(API.POINTS_REQUEST, data);
    return res.data;
  }

  /**
   * 获取积分申请列表
   * @param {number} userId 用户ID
   */
  async getPointsRequestList(userId) {
    const res = await http.post(API.POINTS_REQUEST_LIST, { user_id: userId });
    return res.data;
  }

  /**
   * 接受积分申请
   * @param {object} data 接受数据
   */
  async acceptPoints(data) {
    const res = await http.post(API.POINTS_ACCEPT, data);
    return res.data;
  }

  /**
   * 获取积分详情
   * @param {number} id 记录ID
   */
  async getPointsDetail(id) {
    const res = await http.post(`${API.POINTS_DETAIL}/${id}`, {});
    return res.data;
  }

  // ==================== 排行榜相关 ====================

  /**
   * 获取积分排行榜
   * @param {object} params 查询参数
   */
  async getRanking(params = {}) {
    const res = await http.post(API.POINTS_RANKING, params);
    return res.data;
  }

  // ==================== 规则相关 ====================

  /**
   * 获取积分规则列表
   */
  async getRules() {
    const res = await http.post(API.RULES, {});
    return res.data;
  }

  /**
   * 获取积分规则分类
   */
  async getRuleCategories() {
    const res = await http.get(API.RULES_CATEGORIES);
    return res.data;
  }

  // ==================== 部门相关 ====================

  /**
   * 获取部门列表
   */
  async getDepartments() {
    const res = await http.post(API.DEPARTMENTS, {});
    return res.data;
  }

  /**
   * 获取部门用户列表
   * @param {number} departmentId 部门ID
   */
  async getDepartmentUsers(departmentId) {
    const res = await http.post(`${API.DEPARTMENTS_USERS}/${departmentId}`, {});
    return res.data;
  }

  // ==================== 申诉相关 ====================

  /**
   * 获取申诉列表
   * @param {object} params 查询参数
   */
  async getAppealList(params = {}) {
    const res = await http.post(API.APPEAL_LIST, params);
    return res.data;
  }

  /**
   * 创建申诉
   * @param {object} data 申诉数据
   */
  async createAppeal(data) {
    const res = await http.post(API.APPEAL_CREATE, data);
    return res.data;
  }

  /**
   * 提交申诉
   * @param {object} data 申诉数据
   */
  async submitAppeal(data) {
    const res = await http.post(API.APPEAL_SUBMIT, data);
    return res.data;
  }

  /**
   * 获取申诉详情
   * @param {number} appealId 申诉ID
   */
  async getAppealDetail(appealId) {
    const res = await http.post(`${API.APPEAL_DETAIL}/${appealId}`, {});
    return res.data;
  }

  /**
   * 取消申诉
   * @param {number} appealId 申诉ID
   */
  async cancelAppeal(appealId) {
    const res = await http.post(`${API.APPEAL_CANCEL}/${appealId}`, {});
    return res.data;
  }

  /**
   * 回复申诉
   * @param {number} appealId 申诉ID
   * @param {object} data 回复数据
   */
  async replyAppeal(appealId, data) {
    const res = await http.post(`${API.APPEAL_REPLY}/${appealId}`, data);
    return res.data;
  }

  // ==================== 反馈相关 ====================

  /**
   * 获取反馈列表
   * @param {object} params 查询参数
   */
  async getFeedbackList(params = {}) {
    const res = await http.post(API.FEEDBACK, params);
    return res.data;
  }

  /**
   * 提交反馈
   * @param {object} data 反馈数据
   */
  async submitFeedback(data) {
    const res = await http.post(API.FEEDBACK_SUBMIT, data);
    return res.data;
  }

  // ==================== 等级相关 ====================

  /**
   * 获取等级说明
   */
  async getLevelDescription() {
    const res = await http.post(API.LEVEL_DESCRIPTION, {});
    return res.data;
  }

  // ==================== 仪表板相关 ====================

  /**
   * 获取仪表板汇总数据
   */
  async getDashboardSummary() {
    const res = await http.post(API.DASHBOARD_SUMMARY, {});
    return res.data;
  }

  /**
   * 获取仪表板动态
   */
  async getDashboardDynamics() {
    const res = await http.post(API.DASHBOARD_DYNAMICS, {});
    return res.data;
  }

  /**
   * 获取仪表板图表数据
   * @param {string} type 图表类型 (week/month/year)
   */
  async getDashboardCharts(type) {
    const res = await http.post(API.DASHBOARD_CHARTS, { type });
    return res.data;
  }
}

// 导出单例
export default new ApiService();
