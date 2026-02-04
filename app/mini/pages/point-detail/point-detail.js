// pages/point-detail/point-detail.js
const { http, API } = require('../../utils/request');

Page({
  data: {
    detail: null,
    loading: false
  },

  onLoad(options) {
    // 取消测试数据，改为API获取
    const id = options.id;
    if (id) {
      this.setData({ loading: true });
      this.loadPointDetail(id);
    } else {
      wx.showToast({
        title: '缺少记录ID',
        icon: 'none'
      });
    }
  },

  // 加载积分详情
  async loadPointDetail(id) {
    try {
      // 使用POST方法传递参数id
      const res = await http.post(API.POINTS_DETAIL, { id });
      if (res.data && Array.isArray(res.data) && res.data.length > 0) {
        const raw = res.data[0];
        // 格式化日期，字段适配
        const detail = {
          ...raw,
          created_at: this.formatDate(raw.created_at),
          operator_name: raw.giver_name // 兼容旧字段
        };
        this.setData({ 
          detail,
          loading: false
        });
      } else {
        wx.showToast({
          title: '未找到详情',
          icon: 'none'
        });
        this.setData({ loading: false });
      }
    } catch (err) {
      console.error('加载详情失败:', err);
      wx.showToast({
        title: '加载失败',
        icon: 'none'
      });
      this.setData({ loading: false });
    }
  },

  // 格式化日期
  formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
  },

  // 跳转到申诉页面
  goToAppeal() {
    const { id } = this.data.detail;
    wx.navigateTo({
      url: `/pages/appeal/appeal?record_id=${id}`
    });
  }
});
