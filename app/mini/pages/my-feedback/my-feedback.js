const { http, API, getUserInfo } = require('../../utils/request');

Page({
  data: {
    feedbackList: [],
  },

  onLoad() {
    const user = getUserInfo();
    if (!user?.user_id) {
      wx.showToast({
        title: '登录信息失效',
        icon: 'none',
      });
      wx.redirectTo({ url: '/pages/login/login' });
      return;
    }

    this.fetchFeedbackList(user.user_id);
  },

  // 获取用户建议列表
  fetchFeedbackList(userId) {
    http.get(API.FEEDBACK, { user_id: userId })
      .then((res) => {
        const feedbackList = res.data.map((item) => ({
          id: item.id,
          content: item.content.slice(0, 50),
          time: item.created_at,
          adopted: item.adopted,
          replies: item.replies_count,
          closed: item.closed,
        }));
        this.setData({ feedbackList });
      })
      .catch(() => {
        wx.showToast({
          title: '获取建议失败',
          icon: 'none',
        });
      });
  },

  // 跳转到建议详情页面
  goToFeedbackDetail(e) {
    const feedbackId = e.currentTarget.dataset.id;
    wx.navigateTo({ url: `/pages/feedback-detail/feedback-detail?id=${feedbackId}` });
  },
});