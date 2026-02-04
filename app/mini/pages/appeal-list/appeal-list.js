const { http, API } = require('../../utils/request');

Page({
  data: {
    appealList: [],
    filteredList: [],
    activeTab: 0,
    loading: false,
    noDataTip: '',
  },

  async onLoad() {
    // 判断当前用户是否为操作员
    const userInfo = wx.getStorageSync('userInfo') || {};
    const isOperator = userInfo.user_group_id === 3;
    if (isOperator) {
      wx.setNavigationBarTitle({
        title: '申诉处理'
      });
    } else {
      wx.setNavigationBarTitle({
        title: '我的申诉'
      });
    }

    await this.loadAppealList();
  },

  async onPullDownRefresh() {
    await this.loadAppealList();
    wx.stopPullDownRefresh();
  },

  async loadAppealList() {
    this.setData({ loading: true, noDataTip: '' });
    try {
      const res = await http.get(API.APPEAL_LIST);
      // 适配接口返回格式
      const list = Array.isArray(res.data) ? res.data : [];
      // 格式化数据
      const mapped = list.map(item => ({
        ...item,
        shortReason: item.reason?.substring(0, 20) + (item.reason?.length > 20 ? '...' : '') || '无理由',
        statusText: this.getStatusText(item.status),
        replyText: item.reply || '',
        created_at: item.create_time
      }));
      this.setData({
        appealList: mapped
      });
      this.filterList(this.data.activeTab, mapped);
    } catch (err) {
      this.setData({
        appealList: [],
        filteredList: [],
        noDataTip: '加载失败'
      });
      wx.showToast({ title: '加载失败', icon: 'none' });
    } finally {
      this.setData({ loading: false });
    }
  },

  getStatusText(status) {
    switch (status) {
      case 0: return '申诉中';
      case 1: return '已通过';
      case -1: return '已驳回';
      default: return '未知';
    }
  },

  filterList(tab, list) {
    let filtered = [];
    if (tab === 0) {
      filtered = (list || this.data.appealList).filter(item => item.status === 0);
      this.setData({
        filteredList: filtered,
        noDataTip: filtered.length ? '' : '暂无申诉中记录'
      });
    } else {
      filtered = (list || this.data.appealList).filter(item => item.status === 1 || item.status === -1);
      this.setData({
        filteredList: filtered,
        noDataTip: filtered.length ? '' : '暂无已处理记录'
      });
    }
  },

  onTabChange(e) {
    const index = Number(e.currentTarget.dataset.index);
    this.setData({ activeTab: index });
    this.filterList(index);
  },

  // 可扩展：点击查看详情
  onItemTap(e) {
    const id = e.currentTarget.dataset.id;
    wx.navigateTo({
      url: `/pages/appeal-detail/appeal-detail?id=${id}`
    });
  }
});
