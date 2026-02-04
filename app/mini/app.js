// app.js
const { API } = require('./utils/request');
import store from './utils/store.js';

App({
  globalData: {
    needLogin: false,  // 登录标记，默认 false
    loginReady: false  // 登录就绪标记
  },
  
  onLaunch() {
    this.checkAndLogin();
  },
  
  // 检查token并自动登录
  async checkAndLogin() {
    const token = wx.getStorageSync('token');
    
    if (!token) {
      // 没有 token，需要登录
      this.globalData.needLogin = true;
      await this.autoLogin();
      return;
    }
    
    // 有token，验证是否有效
    try {
      const res = await this.checkTokenValid();
      if (res && res.code === 0) {
        // token有效
        this.globalData.needLogin = false;
        this.globalData.loginReady = true;
        store.setLoginReady(true);
      } else {
        // token无效，重新登录
        console.log('[app.js] token已失效，重新登录');
        await this.autoLogin();
      }
    } catch (err) {
      // 验证失败，重新登录
      console.log('[app.js] token验证失败，重新登录:', err);
      await this.autoLogin();
    }
  },
  
  // 验证token是否有效
  checkTokenValid() {
    return new Promise((resolve, reject) => {
      const token = wx.getStorageSync('token');
      if (!token) {
        reject(new Error('无token'));
        return;
      }
      
      wx.request({
        url: API.CHECK_LOGIN,
        method: 'GET',
        header: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        success: (res) => {
          resolve(res.data);
        },
        fail: (err) => {
          reject(err);
        }
      });
    });
  },
  
  // 自动登录
  async autoLogin() {
    try {
      const loginRes = await this.wxLogin();
      if (!loginRes.code) {
        throw new Error('获取code失败');
      }
      
      const res = await this.doLogin(loginRes.code);
      if (res.code === 0 && res.data.token) {
        // 更新到store
        store.setToken(res.data.token);
        store.setUserInfo(res.data.user);
        store.setLoginReady(true);
        
        this.globalData.needLogin = false;
        this.globalData.loginReady = true;
        console.log('[app.js] 自动登录成功');
      } else {
        throw new Error(res.message || '登录失败');
      }
    } catch (err) {
      console.error('[app.js] 自动登录失败:', err);
      this.globalData.needLogin = true;
      this.globalData.loginReady = false;
      // 清除无效数据
      store.clearAuth();
    }
  },
  
  // 调用微信登录
  wxLogin() {
    return new Promise((resolve, reject) => {
      wx.login({
        success: resolve,
        fail: reject
      });
    });
  },
  
  // 调用后端登录接口
  doLogin(code) {
    return new Promise((resolve, reject) => {
      wx.request({
        url: API.LOGIN,
        method: 'POST',
        data: { code },
        header: { 'Content-Type': 'application/json' },
        success: (res) => {
          resolve(res.data);
        },
        fail: reject
      });
    });
  }
});
