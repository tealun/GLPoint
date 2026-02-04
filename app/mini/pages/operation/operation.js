// pages/operation/operation.js
const { http, API, getUserInfo } = require('../../utils/request');
const SubscribeMessage = require('../../utils/subscribe-message');

Page({
  data: {
    isOperator: false,
    currentUser: null,
    // 表单数据
    ruleCategories: [], // 一级分类
    subCategories: [], // 二级分类
    rules: [], // 具体规则
    selectedCategory: null,
    selectedSubCategory: null,
    selectedRule: null,
    points: 0,
    reason: '',
    // 管理员专用
    employees: [],
    employeeOptions: [],
    selectedEmployeeOption: {},
    selectedEmployees: [],
    // 加载状态
    loading: true,
    // 订阅消息状态
    subscribeEnabled: false
  },

  onLoad() {
    this.initPage();
  },

  // 页面初始化
  async initPage() {
    wx.showLoading({ title: '加载中' });
    try {
      const currentUser = getUserInfo();
      const isOperator = currentUser?.user_group_id === 3;
      
      this.setData({
        isOperator,
        currentUser,
        loading: false
      });

      // 加载规则分类
      await this.loadRuleCategories();
      
      // 如果是操作者，加载员工列表
      if (isOperator) {
        await this.loadEmployees();
      }

      // 检查订阅消息状态
      this.checkSubscribeMessageStatus();
    } catch (err) {
      console.error('初始化失败:', err);
      wx.showToast({ 
        title: '加载失败，请重试', 
        icon: 'none' 
      });
    } finally {
      wx.hideLoading();
    }
  },

  // 加载规则分类
  async loadRuleCategories() {
    const res = await http.get(API.RULES_CATEGORIES);
    if (res.data && res.data.categories) {
      this.setData({ ruleCategories: res.data.categories });
    }
  },

  // 加载员工列表
  async loadEmployees() {
    const departmentId = this.data.currentUser?.department_id;
    if (!departmentId) {
      console.error('无法获取部门ID');
      return;
    }
    const res = await http.post(API.DEPARTMENTS_USERS, { department_id: departmentId });
    if (res.data) {
      const allOption = { id: 0, name: '全部' };
      const options = [allOption, ...res.data.map(employee => ({ id: employee.id, name: employee.nickname }))];
      console.log('加载员工列表成功:', res.data);
      console.log('员工选项:', options);
      
      this.setData({
        employees: res.data,
        employeeOptions: options
      });
    }
  },

  // 检查订阅消息状态
  checkSubscribeMessageStatus() {
    const status = SubscribeMessage.getStatus();
    this.setData({ subscribeEnabled: status === 'enabled' });
  },

  // 一级分类变化
  async onCategoryChange(e) {
    const index = e.detail.value;
    if (index >= 0 && index < this.data.ruleCategories.length) {
      const category = this.data.ruleCategories[index];
      this.setData({
        selectedCategory: category,
        subCategories: category.children || [],
        selectedSubCategory: null,
        rules: [],
        selectedRule: null,
        points: 0,
        reason: ''
      });
    } else {
      console.error('一级分类选择索引超出范围:', index);
      wx.showToast({
        title: '请选择有效的一级分类',
        icon: 'none'
      });
    }
  },

  // 二级分类变化
  async onSubCategoryChange(e) {
    const index = e.detail.value;
    if (index >= 0 && index < this.data.subCategories.length) {
      const subCategory = this.data.subCategories[index];
      this.setData({
        selectedSubCategory: subCategory,
        rules: [],
        selectedRule: null,
        points: 0,
        reason: ''
      });
      
      // 加载具体规则
      const res = await http.post(API.RULES, { category_id: subCategory.id });
      if (res.code === 0 && res.data) {
        this.setData({ rules: res.data });
        if (!res.data.length) {
          wx.showToast({
            title: '该分类下没有找到对应规则',
            icon: 'none'
          });
        }
      } else {
        console.error('规则数据格式不正确:', res.data);
        this.setData({ rules: [] });
        wx.showToast({
          title: '该分类下没有找到对应规则',
          icon: 'none'
        });
      }
    } else {
      console.error('二级分类选择索引超出范围:', index);
      wx.showToast({
        title: '请选择有效的二级分类',
        icon: 'none'
      });
    }
  },

  // 规则选择变化
  onRuleChange(e) {
    const index = e.detail.value;
    if (index >= 0 && index < this.data.rules.length) {
      const rule = this.data.rules[index];
      this.setData({
        selectedRule: rule,
        points: rule.score || 5,  // 用score字段
        reason: rule.description || ''  // 用description字段
      });
    } else {
      console.error('规则选择索引超出范围:', index);
      wx.showToast({
        title: '请选择有效的规则',
        icon: 'none'
      });
    }
  },

  // 积分值变化
  onPointsInput(e) {
    const value = e.detail.value;
    // 如果输入为空，保持空值；如果是有效数字则保留，无效输入则清空
    this.setData({
      points: value === '' ? '' : (isNaN(parseInt(value)) ? '' : parseInt(value))
    });
  },

  // 原因变化
  onReasonInput(e) {
    this.setData({
      reason: e.detail.value
    });
  },

  // 提交表单
  async submitForm() {
    if (!this.validateForm()) return;

    wx.showLoading({ title: '提交中' });
    try {
      const data = this.assembleFormData();
      const api = this.data.isOperator ? API.POINTS_AWARD : API.POINTS_REQUEST;
      
      const res = await http.post(api, data);
      if (res.data) {
        // 提交成功后，请求订阅消息
        this.requestSubscribeMessage();
        
        wx.showToast({
          title: this.data.isOperator ? '发放成功' : '申请成功',
          icon: 'success'
        });
        setTimeout(() => {
          wx.reLaunch({ url: '/pages/operation/operation' }); // 强制刷新当前页面
        }, 1500);
      }
    } catch (err) {
      console.error('提交失败:', err);
      wx.showToast({
        title: err.message || '提交失败',
        icon: 'none'
      });
    } finally {
      wx.hideLoading();
    }
  },

  // 表单验证
  validateForm() {

    if (!this.data.points || this.data.points <= 0) {
      wx.showToast({
        title: '请输入有效的积分值',
        icon: 'none'
      });
      return false;
    }

    if (!this.data.reason.trim()) {
      wx.showToast({
        title: '请输入奖励原因',
        icon: 'none'
      });
      return false;
    }

    if (this.data.isOperator && Object.keys(this.data.selectedEmployeeOption).length === 0) {
      wx.showToast({
        title: '请选择接收人',
        icon: 'none'
      });
      return false;
    }

    return true;
  },

  // 组装表单数据
  assembleFormData() {
    const baseData = {
      rule_id: this.data.selectedRule?.id || 0, // 设置默认值为 0 或其他有效值
      points: this.data.points,
      reason: this.data.reason
    };

    if (this.data.isOperator) {
      let receivers = [];
      if (this.data.selectedEmployeeOption.id === 0) {
        receivers = this.data.employees.map(emp => emp.id);
      } else {
        receivers = [this.data.selectedEmployeeOption.id];
      }
      return {
        ...baseData,
        operator_id: this.data.currentUser.user_id,
        receivers
      };
    }

    return baseData;
  },

  // 接收人选择变化
  onEmployeeSelectChange(e) {
    const index = e.detail.value;
    if (index >= 0 && index < this.data.employeeOptions.length) {
      const option = this.data.employeeOptions[index];
      this.setData({
        selectedEmployeeOption: option
      });
    } else {
      console.error('接收人选择索引超出范围:', index);
      wx.showToast({
        title: '请选择有效的接收人',
        icon: 'none'
      });
    }
  },

  // 跳转到申请列表页面
  goToPointRequires() {
    wx.navigateTo({
      url: '/pages/point-requires/point-requires'
    });
  },

  // 请求订阅消息
  requestSubscribeMessage() {
    if (this.data.isOperator) {
      // 管理员操作 - 订阅积分变动通知
      SubscribeMessage.subscribePointsChange((success, result) => {
        if (success) {
          console.log('积分变动通知订阅成功');
        }
      });
    } else {
      // 普通用户申请 - 订阅申请审核结果通知
      SubscribeMessage.subscribeAppealResult((success, result) => {
        if (success) {
          console.log('申请审核结果通知订阅成功');
        }
      });
    }
  },

  // 手动订阅消息
  onSubscribeMessage() {
    const templateConfigs = [
      { id: 'your_template_id_for_points_change', name: '积分变动通知' },
      { id: 'your_template_id_for_ranking_change', name: '排名变化通知' },
      { id: 'your_template_id_for_appeal_result', name: '审核结果通知' }
    ];

    SubscribeMessage.subscribeBatch(templateConfigs, (results) => {
      let successCount = 0;
      results.forEach(result => {
        if (result.status === 'accept') {
          successCount++;
        }
      });
      
      wx.showToast({
        title: `成功订阅${successCount}个消息`,
        icon: successCount > 0 ? 'success' : 'none'
      });
    });
  }
});
