// pages/level-description/level-description.js
const { http, API } = require('../../utils/request');

Page({
  data: {
    title: '等级说明',
    description: '',
    levels: [],
    loading: true,
    userInfo: null,         // 新增：用户信息
    userLevelName: '',      // 新增：用户等级名称
    progressPercent: 0,   // 进度百分比
    nextLevelPoints: 0    // 距离下一级所需积分
  },

  onLoad() {
    this.loadUserInfo();
    this.loadLevelDescription();
  },

  onPullDownRefresh() {
    this.loadUserInfo();
    this.loadLevelDescription(() => {
      wx.stopPullDownRefresh();
    });
  },

  loadUserInfo() {
    try {
      const userInfo = wx.getStorageSync('userInfo') || {};
      this.setData({
        userInfo,
        userLevelName: userInfo.level || ''
      });
    } catch (e) {
      this.setData({
        userInfo: null,
        userLevelName: ''
      });
    }
  },

  loadLevelDescription(callback) {
    this.setData({ loading: true });
    const finish = () => {
      if (typeof callback === 'function') callback();
    };
    const { userInfo } = this.data;
    http.get(API.LEVEL_DESCRIPTION)
      .then(res => {
        const items = (res.data && res.data['\u0000*\u0000items']) || [];
        const levels = items.map(item => ({
          name: item.title,
          min: item.min,
          max: item.max,
          range: `${item.min} ~ ${item.max}`
        }));
        this.setData({
          levels,
          loading: false
        });
        finish();
      })
      .catch(err => {
        console.error('[等级说明] 加载失败:', err);
        this.setData({
          description: '加载失败，请稍后重试',
          levels: [],
          loading: false
        });
        finish();
      });
  }
});
