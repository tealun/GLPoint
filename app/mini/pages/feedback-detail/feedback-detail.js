const { http, API, getUserInfo } = require('../../utils/request');

Page({
  data: {
    feedbackDetail: null,
    replies: [],
  },

  onLoad(options) {
    const feedbackId = options.id;
    if (!feedbackId) {
      wx.showToast({
        title: '无效的建议ID',
        icon: 'none',
      });
      wx.navigateBack();
      return;
    }

    this.fetchFeedbackDetail(feedbackId);
  },

  // 获取建议详情
  fetchFeedbackDetail(feedbackId) {
    http.get(`${API.FEEDBACK_DETAIL}/${feedbackId}`)
      .then((res) => {
        this.setData({
          feedbackDetail: res.data.feedback,
          replies: res.data.replies,
        });
      })
      .catch(() => {
        wx.showToast({
          title: '获取详情失败',
          icon: 'none',
        });
      });
  },
});