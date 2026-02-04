import API from '../../utils/api';

Page({
  data: {
    detail: null,
    isOperator: false,
    scoreType: 'fixed', // 'fixed' or 'custom'
    customScore: '',
    selectedScore: null
  },
  onLoad(options) {
    const id = options.id;
    this.setData({ id });
    const userInfo = wx.getStorageSync('userInfo');
    // 假设is_operator为操作员标识字段
    this.setData({ isOperator: !!(userInfo && userInfo.is_operator) });
    this.fetchDetail(id);
  },
  fetchDetail(id) {
    wx.request({
      url: API.POINTS_REQUEST,
      method: 'GET',
      data: { id },
      success: res => {
        if (res.data && res.data.data) {
          this.setData({
            detail: res.data.data,
            selectedScore: res.data.data.score
          });
        }
      }
    });
  },
  onScoreTypeChange(e) {
    this.setData({ scoreType: e.detail.value });
  },
  onCustomScoreInput(e) {
    this.setData({ customScore: e.detail.value });
  },
  onAccept() {
    const { id, scoreType, customScore, selectedScore } = this.data;
    let score = scoreType === 'fixed' ? selectedScore : Number(customScore);
    if (!score || isNaN(score) || score <= 0) {
      wx.showToast({ title: '请输入有效分值', icon: 'none' });
      return;
    }
    wx.request({
      url: API.POINTS_ACCEPT,
      method: 'POST',
      data: { id, score, action: 'accept' },
      success: res => {
        wx.showToast({ title: '已接受', icon: 'success' });
        setTimeout(() => wx.navigateBack(), 1000);
      }
    });
  },
  onReject() {
    const { id } = this.data;
    wx.request({
      url: API.POINTS_ACCEPT,
      method: 'POST',
      data: { id, action: 'reject' },
      success: res => {
        wx.showToast({ title: '已拒绝', icon: 'success' });
        setTimeout(() => wx.navigateBack(), 1000);
      }
    });
  },
  onScoreRadioChange(e) {
    this.setData({ selectedScore: Number(e.detail.value) });
  }
});
