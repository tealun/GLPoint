/**
 * 优化示例：my页面使用新架构重构
 * 展示如何使用 store、mixin、api-service
 */

import store from '../../utils/store.js';
import apiService from '../../services/api-service.js';
import { loginMixin, loadingMixin } from '../../mixins/page-mixins.js';
import { formatRelativeTime } from '../../utils/helpers.js';

// 合并mixins
const pageMixins = Object.assign({}, loginMixin, loadingMixin);

Page({
  // 混入公共逻辑
  ...pageMixins,

  data: {
    userInfo: {
      avatar_url: '/resources/icons/default-man.png',
      nickname: '未设置用户名',
      level: '未定义',
      total_score: 0,
      month_score: 0,
      week_score: 0
    },
    is_operator: false
  },

  async onLoad() {
    // 调用mixin的onLoad（如果有）
    if (pageMixins.onLoad) {
      pageMixins.onLoad.call(this);
    }
    
    await this.loadUserInfo();
  },

  onUnload() {
    // 调用mixin的onUnload
    if (pageMixins.onUnload) {
      pageMixins.onUnload.call(this);
    }
  },

  // 加载用户信息
  async loadUserInfo() {
    // 从store获取本地缓存
    const localUserInfo = store.getUserInfo();
    if (localUserInfo) {
      this.updateUserInfo(localUserInfo);
    }
    
    // 等待登录就绪
    await this.waitForLogin();
    
    // 从服务器获取最新数据
    await this.fetchUserInfoFromServer();
  },

  // 从服务器获取用户信息
  async fetchUserInfoFromServer() {
    try {
      const userId = this.getUserId();
      
      if (!userId) {
        console.log('[my.js] 未找到user_id，尝试重新登录');
        await this.ensureLogin();
        return;
      }
      
      // 使用api-service
      const userInfo = await apiService.getUserInfo(userId);
      if (userInfo) {
        this.updateUserInfo(userInfo);
        store.setUserInfo(userInfo);
      }
    } catch (err) {
      console.error('[my.js] 获取用户信息失败:', err);
      
      // 处理认证错误
      if (err.code === 4401 || err.code === 401 || err.code === -1) {
        await this.ensureLogin();
        await this.fetchUserInfoFromServer();
      }
    }
  },

  // 更新用户信息到页面
  updateUserInfo(data) {
    const defaultAvatar = data.gender === 2 
      ? '/resources/icons/default-woman.png' 
      : '/resources/icons/default-man.png';
    
    const userInfo = {
      avatar_url: data.avatar_url || defaultAvatar,
      nickname: data.nickname || data.name || '未设置用户名',
      level: data.level || '未定义',
      total_score: data.total_score || 0,
      month_score: data.month_score || 0,
      week_score: data.week_score || 0,
      user_id: data.user_id
    };
    
    const is_operator = data.user_group_id === 3;
    
    this.setData({ userInfo, is_operator });
  },

  // 刷新用户信息
  async refreshUserInfo() {
    this.showLoading('刷新中...');
    
    try {
      let userId = this.getUserId();

      // 如果没有userId，先尝试重新登录
      if (!userId) {
        await this.ensureLogin();
        userId = this.getUserId();
      }

      if (!userId) {
        this.showError('登录异常，请重启小程序');
        return;
      }

      // 使用api-service获取用户信息
      const userInfo = await apiService.getUserInfo(userId);
      
      if (userInfo) {
        this.updateUserInfo(userInfo);
        store.setUserInfo(userInfo);
        this.showSuccess('刷新成功');
      }
    } catch (err) {
      console.error('[刷新] 失败:', err);
      
      // 处理认证错误
      if (err.code === 4401 || err.code === 401 || err.code === -1) {
        store.clearAuth();
        await this.ensureLogin();
        this.showToast('已重新登录，请再次点击刷新');
      } else {
        this.showError(err.message || '刷新失败');
      }
    } finally {
      this.hideLoading();
    }
  },

  // 跳转方法
  goToPointList() {
    wx.navigateTo({ url: '/pages/points-list/points-list' });
  },

  goToEditProfile() {
    wx.navigateTo({ url: '/pages/edit-profile/edit-profile' });
  },

  goToLevelDescription() {
    wx.navigateTo({ url: '/pages/level-description/level-description' });
  },

  goToPointRules() {
    wx.navigateTo({ url: '/pages/point-rules/point-rules' });
  },

  goToFeedback() {
    wx.navigateTo({ url: '/pages/feedback/feedback' });
  },

  goToAppeal() {
    const url = this.data.is_operator 
      ? '/pages/appeal-list/appeal-list' 
      : '/pages/appeal/appeal';
    wx.navigateTo({ url });
  }
});
