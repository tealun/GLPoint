import API from '../../utils/api.js';
const { http, silentLogin } = require('../../utils/request.js');

Page({
  data: {
    points: [], // 积分列表数据
    showFilter: false, // 是否显示筛选选项
  },

  async onLoad(options) {
    // 检查是否已登录，没有则静默登录
    const token = wx.getStorageSync('token');
    if (!token) {
      try {
        await silentLogin();
      } catch (e) {
        wx.showToast({ title: '登录失败', icon: 'none' });
        return;
      }
    }
    this.fetchPointsData('thisWeek'); // 默认加载本周数据
  },

  // 打开筛选选项
  openFilterOptions() {
    this.setData({ showFilter: true });
  },

  // 关闭筛选选项
  closeFilterOptions() {
    this.setData({ showFilter: false });
  },

  // 筛选积分数据
  filterPoints(e) {
    const filterType = e.currentTarget.dataset.type;
    this.fetchPointsData(filterType);
    this.closeFilterOptions(); // 筛选后关闭弹层
  },

  // 统一使用 POINTS_LIST 端口，并通过 type 参数传递筛选类型（不传 user_id）
  fetchPointsData(filterType) {
    http.post(API.POINTS_LIST, {
      type: filterType
    })
      .then(res => {
        if (res && res.data) {
          const points = Array.isArray(res.data)
            ? res.data.map(item => {
                let scoreNum = Number(item.score) || 0;
                let scoreStr = scoreNum > 0 ? `+${scoreNum}` : `${scoreNum}`;
                return {
                  id: item.id,
                  date: item.create_time,
                  score: scoreStr,
                  remark: item.remark || ''
                };
              })
            : [];
          this.setData({ points });
        } else {
          wx.showToast({
            title: '获取数据失败',
            icon: 'none',
          });
        }
      })
      .catch(() => {
        wx.showToast({
          title: '请求失败，请检查网络',
          icon: 'none',
        });
      });
  },

  // 查看积分记录详情
  viewPointDetails(e) {
    const pointId = e.currentTarget.dataset.id;
    wx.navigateTo({ url: `/pages/point-detail/point-detail?id=${pointId}` });
  },
});

