const { http, API, getUserInfo, silentLogin } = require('../../utils/request');

Page({
  data: {
    userInfo: {
      avatar_url: '/resources/icons/default-man.png',
      nickname: '未设置用户名',
      level: '未定义',
      total_score: 0,
      month_score: 0,
      week_score: 0
    },
    is_operator: false // 新增
  },

  onLoad() {
    this.loadUserInfo();
  },

  // 加载用户信息
  async loadUserInfo() {
    // 优先从本地存储获取并显示
    const localUserInfo = wx.getStorageSync('userInfo');
    if (localUserInfo) {
      this.updateUserInfo(localUserInfo);
    }
    
    // 等待app登录就绪
    const app = getApp();
    if (!app.globalData.loginReady) {
      console.log('[my.js] 等待登录就绪...');
      // 等待最多3秒
      for (let i = 0; i < 30; i++) {
        await this.sleep(100);
        if (app.globalData.loginReady) {
          break;
        }
      }
    }
    
    // 从服务器获取最新数据
    await this.fetchUserInfoFromServer();
  },
  
  // 延迟函数
  sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  },
  
  // 从服务器获取用户信息
  async fetchUserInfoFromServer() {
    try {
      const cachedUser = getUserInfo();
      const userId = cachedUser?.user_id;
      
      if (!userId) {
        console.log('[my.js] 未找到user_id，尝试重新登录');
        await silentLogin();
        // 重新获取用户信息
        const newUserInfo = getUserInfo();
        if (newUserInfo) {
          this.updateUserInfo(newUserInfo);
        }
        return;
      }
      
      const res = await http.post(API.USER_INFO, { user_id: userId });
      if (res.data) {
        this.updateUserInfo(res.data);
        wx.setStorageSync('userInfo', res.data);
      }
    } catch (err) {
      console.error('[my.js] 获取用户信息失败:', err);
      // 如果是认证错误，尝试重新登录
      if (err.code === 4401 || err.code === 401 || err.code === -1) {
        try {
          await silentLogin();
          // 重新加载
          await this.fetchUserInfoFromServer();
        } catch (loginErr) {
          console.error('[my.js] 重新登录失败:', loginErr);
        }
      }
    }
  },

  // 更新用户信息到页面
  updateUserInfo(data) {
    // 默认头像为男性头像，仅对女性用户进行更改
    let defaultAvatar = '/resources/icons/default-man.png';
    if (data.gender === 2) {
      defaultAvatar = '/resources/icons/default-woman.png';
    }
    // 合并data和默认字段，优先data
    const userInfo = {
      avatar_url: data.avatar_url || defaultAvatar,
      nickname: data.nickname || data.name || '未设置用户名',
      level: data.level || '未定义',
      total_score: data.total_score || 0,
      month_score: data.month_score || 0,
      week_score: data.week_score || 0,
      user_id: data.user_id // 保留id用于后续判断
    };
    // 新增：判断是否为operator
    const is_operator = data.user_group_id === 3;
    // 更新页面数据
    this.setData({
      userInfo,
      is_operator
    });
    // 同步到本地存储
    wx.setStorageSync('userInfo', { ...data, ...userInfo });
  },

  // 刷新用户信息
  async refreshUserInfo() {
    wx.showLoading({ title: '刷新中...' });
    
    try {
      const cachedUser = getUserInfo();
      let userId = cachedUser?.user_id;

      // 如果没有userId，先尝试重新登录
      if (!userId) {
        console.log('[刷新] 未找到用户ID，重新登录');
        try {
          await silentLogin();
          const newUserInfo = getUserInfo();
          userId = newUserInfo?.user_id;
        } catch (loginErr) {
          wx.hideLoading();
          console.error('[刷新] 登录失败:', loginErr);
          wx.showToast({ 
            title: '登录失败，请稍后重试', 
            icon: 'none' 
          });
          return;
        }
      }

      // 如果还是没有userId，说明登录有问题
      if (!userId) {
        wx.hideLoading();
        wx.showToast({ 
          title: '登录异常，请重启小程序', 
          icon: 'none' 
        });
        return;
      }

      // 获取用户信息
      const res = await http.post(API.USER_INFO, { user_id: userId });
      wx.hideLoading();
      
      if (res.data) {
        this.updateUserInfo(res.data);
        wx.setStorageSync('userInfo', res.data);
        wx.showToast({ title: '刷新成功', icon: 'success' });
      }
    } catch (err) {
      wx.hideLoading();
      console.error('[刷新] 失败:', err);
      
      // 处理认证错误，自动重新登录并重试
      if (err.code === 4401 || err.code === 401 || err.code === -1) {
        console.log('[刷新] 认证失败，尝试重新登录');
        wx.removeStorageSync('token');
        
        try {
          await silentLogin();
          // 重新登录成功后，递归调用刷新（只递归一次）
          if (!this._refreshRetried) {
            this._refreshRetried = true;
            await this.refreshUserInfo();
            this._refreshRetried = false;
          } else {
            wx.showToast({ 
              title: '已重新登录，请再次点击刷新', 
              icon: 'none' 
            });
          }
        } catch (loginErr) {
          console.error('[刷新] 重新登录失败:', loginErr);
          wx.showToast({ 
            title: '自动登录失败，请重启小程序', 
            icon: 'none' 
          });
        }
      } else {
        wx.showToast({ 
          title: err.message || '刷新失败', 
          icon: 'none' 
        });
      }
    }
  },

  // 跳转到积分明细页面
  goToPointList() {
    wx.navigateTo({ url: '/pages/points-list/points-list' });
  },

  // 跳转到资料修改页面
  goToEditProfile() {
    wx.navigateTo({ url: '/pages/edit-profile/edit-profile' });
  },

  // 跳转到等级说明页面
  goToLevelDescription() {
    wx.navigateTo({ url: '/pages/level-description/level-description' });
  },

  // 跳转到积分规则页面
  goToPointRules() {
    wx.navigateTo({ url: '/pages/point-rules/point-rules' });
  },

  // 跳转到建议反馈页面
  goToFeedback() {
    wx.navigateTo({ url: '/pages/feedback/feedback' });
  },

  // 跳转到申诉页面或申诉列表
  goToAppeal() {
    if (this.data.is_operator) {
      wx.navigateTo({ url: '/pages/appeal-list/appeal-list' });
    } else {
      wx.navigateTo({ url: '/pages/appeal/appeal' });
    }
  },
});
