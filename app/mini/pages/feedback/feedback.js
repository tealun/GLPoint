// pages/feedback/feedback.js
const { http, API, getUserInfo } = require('../../utils/request');

Page({
  data: {
    content: '', // 反馈内容
    submitting: false, // 提交状态
    isOperator: false, // 是否为操作员
    reviewCount: 0, // 待审核建议数量
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

    this.setData({ isOperator: user.role === 'operator' });

    if (this.data.isOperator) {
      this.fetchReviewCount();
    }
  },

  // 获取待审核建议数量
  fetchReviewCount() {
    http.get(API.FEEDBACK, { status: 'pending' })
      .then((res) => {
        this.setData({ reviewCount: res.data?.length || 0 });
      })
      .catch(() => {
        this.setData({ reviewCount: 0 });
      });
  },

  // 输入反馈内容
  onContentInput(e) {
    this.setData({ content: e.detail.value });
  },

  // 提交反馈
  submitFeedback() {
    if (!this.data.content.trim()) {
      wx.showToast({
        title: '请输入反馈内容',
        icon: 'none',
      });
      return;
    }

    const user = getUserInfo();
    if (!user?.user_id) {
      wx.showToast({
        title: '登录信息失效',
        icon: 'none',
      });
      return;
    }

    this.setData({ submitting: true });
    http
      .post(API.FEEDBACK_SUBMIT, {
        content: this.data.content,
        user_id: user.user_id,
      })
      .then(() => {
        wx.showToast({
          title: '提交成功',
          icon: 'success',
        });
        setTimeout(() => wx.navigateBack(), 1500);
      })
      .catch((err) => {
        wx.showToast({
          title: err.message || '提交失败',
          icon: 'none',
        });
      })
      .finally(() => {
        this.setData({ submitting: false });
      });
  },

  // 跳转到我的建议页面
  goToMyFeedback() {
    wx.navigateTo({ url: '/pages/my-feedback/my-feedback' });
  },

  // 跳转到审核建议页面
  goToReviewFeedback() {
    wx.navigateTo({ url: '/pages/review-feedback/review-feedback' });
  },
});
