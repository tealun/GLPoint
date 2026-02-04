/**
 * 页面通用混入 - 登录相关
 * 提供统一的登录状态管理和用户信息获取
 */

import store from '../utils/store.js';
import { silentLogin } from '../utils/request.js';

export const loginMixin = {
  data: {
    __userInfo: null,
    __isLogin: false,
    __loginReady: false
  },

  onLoad() {
    // 订阅store变化
    this.__unsubscribe = store.subscribe((state) => {
      this.setData({
        __userInfo: state.userInfo,
        __isLogin: state.isLogin,
        __loginReady: state.loginReady
      });
    });

    // 初始化状态
    const state = store.getState();
    this.setData({
      __userInfo: state.userInfo,
      __isLogin: state.isLogin,
      __loginReady: state.loginReady
    });
  },

  onUnload() {
    // 取消订阅
    if (this.__unsubscribe) {
      this.__unsubscribe();
    }
  },

  // 获取用户信息
  getUserInfo() {
    return store.getUserInfo();
  },

  // 获取用户ID
  getUserId() {
    return store.getUserId();
  },

  // 检查登录状态
  checkLogin() {
    return store.isLoggedIn();
  },

  // 确保已登录（未登录则自动登录）
  async ensureLogin() {
    if (store.isLoggedIn()) {
      return true;
    }

    try {
      await silentLogin();
      return true;
    } catch (err) {
      console.error('[loginMixin] 自动登录失败:', err);
      wx.showToast({
        title: '登录失败，请重试',
        icon: 'none'
      });
      return false;
    }
  },

  // 等待登录就绪
  async waitForLogin(timeout = 3000) {
    if (store.isLoginReady()) {
      return true;
    }

    const startTime = Date.now();
    while (Date.now() - startTime < timeout) {
      if (store.isLoginReady()) {
        return true;
      }
      await this.sleep(100);
    }

    console.warn('[loginMixin] 等待登录超时');
    return false;
  },

  sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  }
};

/**
 * 页面通用混入 - 加载状态
 * 提供统一的加载状态管理
 */
export const loadingMixin = {
  data: {
    __loading: false,
    __refreshing: false
  },

  // 显示加载中
  showLoading(title = '加载中...') {
    this.setData({ __loading: true });
    wx.showLoading({ title, mask: true });
  },

  // 隐藏加载中
  hideLoading() {
    this.setData({ __loading: false });
    wx.hideLoading();
  },

  // 显示刷新中
  showRefreshing() {
    this.setData({ __refreshing: true });
  },

  // 隐藏刷新中
  hideRefreshing() {
    this.setData({ __refreshing: false });
  },

  // Toast提示
  showToast(title, icon = 'none', duration = 2000) {
    wx.showToast({ title, icon, duration });
  },

  // 成功提示
  showSuccess(title, duration = 2000) {
    wx.showToast({ title, icon: 'success', duration });
  },

  // 错误提示
  showError(title, duration = 2000) {
    wx.showToast({ title, icon: 'none', duration });
  }
};

/**
 * 页面通用混入 - 列表管理
 * 提供统一的列表数据加载、分页、下拉刷新、上拉加载
 */
export const listMixin = {
  data: {
    __list: [],
    __page: 1,
    __pageSize: 20,
    __hasMore: true,
    __loading: false,
    __refreshing: false
  },

  // 加载列表数据（需要子类实现）
  async fetchListData(page, pageSize) {
    throw new Error('fetchListData() 必须在页面中实现');
  },

  // 初始化列表
  async initList() {
    this.setData({
      __page: 1,
      __hasMore: true,
      __list: []
    });
    await this.loadList();
  },

  // 加载列表
  async loadList() {
    if (this.data.__loading || !this.data.__hasMore) {
      return;
    }

    this.setData({ __loading: true });

    try {
      const result = await this.fetchListData(this.data.__page, this.data.__pageSize);
      const newList = result.list || [];
      const hasMore = result.hasMore !== undefined ? result.hasMore : newList.length >= this.data.__pageSize;

      this.setData({
        __list: this.data.__page === 1 ? newList : [...this.data.__list, ...newList],
        __hasMore: hasMore,
        __page: this.data.__page + 1
      });
    } catch (err) {
      console.error('[listMixin] 加载列表失败:', err);
      wx.showToast({
        title: err.message || '加载失败',
        icon: 'none'
      });
    } finally {
      this.setData({ __loading: false });
    }
  },

  // 下拉刷新
  async onPullDownRefresh() {
    this.setData({ __refreshing: true });
    await this.initList();
    this.setData({ __refreshing: false });
    wx.stopPullDownRefresh();
  },

  // 上拉加载更多
  async onReachBottom() {
    await this.loadList();
  }
};

/**
 * 表单验证混入
 */
export const formMixin = {
  // 验证手机号
  validatePhone(phone) {
    const reg = /^1[3-9]\d{9}$/;
    return reg.test(phone);
  },

  // 验证邮箱
  validateEmail(email) {
    const reg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return reg.test(email);
  },

  // 验证必填
  validateRequired(value, fieldName = '该字段') {
    if (!value || (typeof value === 'string' && !value.trim())) {
      wx.showToast({
        title: `${fieldName}不能为空`,
        icon: 'none'
      });
      return false;
    }
    return true;
  },

  // 验证长度
  validateLength(value, min, max, fieldName = '该字段') {
    const len = value ? value.length : 0;
    if (len < min || len > max) {
      wx.showToast({
        title: `${fieldName}长度应在${min}-${max}之间`,
        icon: 'none'
      });
      return false;
    }
    return true;
  }
};

// 导出混入组合器
export function mixins(...mixinObjects) {
  const result = {
    data: {},
    methods: {}
  };

  mixinObjects.forEach(mixin => {
    // 合并data
    if (mixin.data) {
      Object.assign(result.data, mixin.data);
    }

    // 合并其他属性（方法、生命周期等）
    Object.keys(mixin).forEach(key => {
      if (key !== 'data') {
        if (key === 'onLoad' || key === 'onUnload' || key === 'onShow' || key === 'onHide') {
          // 生命周期方法需要合并执行
          const original = result[key];
          result[key] = function(...args) {
            if (original) original.apply(this, args);
            mixin[key].apply(this, args);
          };
        } else {
          result[key] = mixin[key];
        }
      }
    });
  });

  return result;
}
