/**
 * 全局状态管理
 * 统一管理用户信息、登录状态等全局数据
 */

class Store {
  constructor() {
    this.state = {
      userInfo: null,
      isLogin: false,
      loginReady: false,
      token: null
    };
    this.listeners = [];
    this.init();
  }

  // 初始化：从本地存储恢复状态
  init() {
    try {
      const token = wx.getStorageSync('token');
      const userInfo = wx.getStorageSync('userInfo');
      
      if (token && userInfo) {
        this.state.token = token;
        this.state.userInfo = userInfo;
        this.state.isLogin = true;
      }
    } catch (e) {
      console.error('[Store] 初始化失败:', e);
    }
  }

  // 获取状态
  getState() {
    return this.state;
  }

  // 获取用户信息
  getUserInfo() {
    return this.state.userInfo;
  }

  // 获取用户ID
  getUserId() {
    return this.state.userInfo?.user_id || null;
  }

  // 检查是否已登录
  isLoggedIn() {
    return this.state.isLogin && this.state.token;
  }

  // 检查登录是否就绪
  isLoginReady() {
    return this.state.loginReady;
  }

  // 设置用户信息
  setUserInfo(userInfo) {
    this.state.userInfo = userInfo;
    this.state.isLogin = true;
    
    // 持久化到本地
    if (userInfo) {
      wx.setStorageSync('userInfo', userInfo);
    }
    
    this.notify();
  }

  // 设置Token
  setToken(token) {
    this.state.token = token;
    
    if (token) {
      wx.setStorageSync('token', token);
      this.state.isLogin = true;
    }
    
    this.notify();
  }

  // 设置登录就绪状态
  setLoginReady(ready) {
    this.state.loginReady = ready;
    this.notify();
  }

  // 清除登录信息
  clearAuth() {
    this.state.userInfo = null;
    this.state.isLogin = false;
    this.state.loginReady = false;
    this.state.token = null;
    
    wx.removeStorageSync('token');
    wx.removeStorageSync('userInfo');
    
    this.notify();
  }

  // 更新用户信息（部分更新）
  updateUserInfo(updates) {
    if (this.state.userInfo) {
      this.state.userInfo = { ...this.state.userInfo, ...updates };
      wx.setStorageSync('userInfo', this.state.userInfo);
      this.notify();
    }
  }

  // 订阅状态变化
  subscribe(listener) {
    this.listeners.push(listener);
    
    // 返回取消订阅函数
    return () => {
      const index = this.listeners.indexOf(listener);
      if (index > -1) {
        this.listeners.splice(index, 1);
      }
    };
  }

  // 通知所有订阅者
  notify() {
    this.listeners.forEach(listener => {
      try {
        listener(this.state);
      } catch (e) {
        console.error('[Store] 通知订阅者失败:', e);
      }
    });
  }
}

// 创建单例
const store = new Store();

export default store;
