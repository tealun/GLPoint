const { http, API, getUserInfo, silentLogin } = require('../../utils/request');

Page({
  data: {
    activeTab: 0, // 0:申请中, 1:已处理
    applyingList: [],
    processedList: []
  },
  onLoad() {
    const userInfo = getUserInfo();
    if (userInfo && userInfo.user_id) {
      this.fetchList(userInfo.user_id);
    }
  },
  async fetchList(user_id) {
    try {
      const res = await http.post(API.POINTS_REQUEST_LIST, { user_id });
      if (res.data && Array.isArray(res.data.data)) {
        const applyingList = [];
        const processedList = [];
        res.data.data.forEach(item => {
          if (item.status === 0) {
            applyingList.push(item);
          } else {
            processedList.push(item);
          }
        });
        this.setData({ applyingList, processedList });
      }
    } catch (err) {
      wx.showToast({ title: '加载失败', icon: 'none' });
    }
  },
  onTabChange(e) {
    const idx = e.currentTarget.dataset.idx;
    this.setData({ activeTab: idx });
  },
  onItemTap(e) {
    const id = e.currentTarget.dataset.id;
    wx.navigateTo({
      url: `/pages/point-require-detail/point-require-detail?id=${id}`
    });
  }
});
