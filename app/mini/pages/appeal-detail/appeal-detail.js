const { http, API, getUserInfo } = require('../../utils/request');

Page({
  data: {
    loading: true,
    record: null,
    appeal: null,
    reply: null,
    statusText: '',
    error: '',
    isOperator: false,
    replyInput: '',
    submitting: false,
    showReplyForm: false,
    showProcessing: false,
    appealId: null // 新增
  },

  async onLoad(options) {
    const id = options.id;
    if (!id) {
      this.setData({ error: '参数错误', loading: false });
      return;
    }
    this.setData({ appealId: id }); // 存储id
    await this.loadDetail(id);
  },

  async loadDetail(id) {
    this.setData({ loading: true, error: '' });
    try {
      // 获取申诉详情
      const res = await http.get(API.APPEAL_DETAIL, { id });
      const data = res.data || {};
      const record = data.record || {};
      let appeal = data.appeal || {};
      let reply = data.reply || {};

      // 兼容 reply 为数组
      let replyArr = Array.isArray(reply) ? reply : (reply ? [reply] : []);
      // 查找 status > 0 的 reply（已处理）
      let handledReply = replyArr.find(r => r.status > 0);
      // 如果没有已处理的，取第一个
      if (!handledReply && replyArr.length > 0) handledReply = replyArr[0];

      // 操作员判断：本地 userInfo.user_group_id == 3
      const userInfo = getUserInfo();
      const isOperator = userInfo && userInfo.user_group_id == 3;

      // 状态优先用 handledReply.status，否则用 appeal.status
      const status = handledReply ? handledReply.status : (appeal.status ?? 0);
      const statusText = this.getStatusText(status);

      // 补全 appeal.id
      if (!appeal.id && record.id) {
        appeal.id = record.id;
      }

      // 处理显示逻辑
      let showReplyForm = false;
      let showProcessing = false;
      if (status !== 0) {
        // 已处理，显示处理情况
        showReplyForm = false;
        showProcessing = false;
      } else {
        // 申诉中状态
        if (isOperator) {
          // 操作员显示回复框
          showReplyForm = true;
          showProcessing = false;
        } else {
          // 非操作员显示“正在处理中”
          showReplyForm = false;
          showProcessing = true;
        }
      }

      this.setData({
        record,
        appeal,
        reply: handledReply || {},
        statusText,
        loading: false,
        isOperator,
        showReplyForm,
        showProcessing,
        replyInput: ''
      });
    } catch (err) {
      this.setData({
        error: err.message || '加载失败',
        loading: false
      });
      wx.showToast({ title: '加载失败', icon: 'none' });
    }
  },

  getStatusText(status) {
    switch (status) {
      case 0: return '申诉中';
      case 1: return '已通过';
      case -1: return '已拒绝';
      case 2: return '已处理';
      default: return '未知';
    }
  },

  onReplyInput(e) {
    this.setData({ replyInput: e.detail.value });
  },

  async handleReply(status) {
    const appealId = this.data.appealId; // 直接用data里的id
    if (!appealId) {
      wx.showToast({ title: '申诉ID缺失，无法提交', icon: 'none' });
      return;
    }
    if (!this.data.replyInput.trim() && status === 1) {
      wx.showToast({ title: '请输入回复内容', icon: 'none' });
      return;
    }
    this.setData({ submitting: true });
    try {
      await http.post(API.APPEAL_REPLY, {
        id: appealId,
        reply: this.data.replyInput,
        status: status
      });
      wx.showToast({ title: '操作成功', icon: 'success' });
      await this.loadDetail(appealId);
    } catch (err) {
      wx.showToast({ title: err.message || '操作失败', icon: 'none' });
    } finally {
      this.setData({ submitting: false, replyInput: '' });
    }
  },

  onSubmitReply() {
    this.handleReply(1);
  },

  onRejectReply() {
    this.handleReply(-1);
  }
});
