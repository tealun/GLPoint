const { http, API } = require('../../utils/request');

Page({
  data: {
    pointRecords: [],
    selectedRecord: null,
    appealText: '',
    isSubmitting: false,
    noRecordTip: '', // 新增：无可申诉记录提示
  },

  async onLoad(options) {
    await this.loadPointRecords(options && options.record_id);
  },

  // 加载可申诉的积分记录
  async loadPointRecords(recordId) {
    try {
      let params = {};
      if (recordId) {
        params.record_id = recordId;
      }
      const res = await http.get(API.APPEAL_CREATE, params);
      // 兼容后端返回格式
      const records = res.data && Array.isArray(res.data.records) ? res.data.records : [];
      if (records.length === 0) {
        this.setData({
          pointRecords: [],
          selectedRecord: null,
          noRecordTip: '当前没有可申诉记录'
        });
        return;
      }
      // 格式化数据
      const mapped = records.map(record => ({
        ...record,
        shortContent: record.remark?.substring(0, 20) + (record.remark?.length > 20 ? '...' : '') || '无描述'
      }));
      this.setData({
        pointRecords: mapped,
        selectedRecord: mapped.length === 1 ? mapped[0] : null,
        noRecordTip: ''
      });
    } catch (err) {
      this.setData({
        pointRecords: [],
        selectedRecord: null,
        noRecordTip: '加载记录失败'
      });
      wx.showToast({ title: '加载记录失败', icon: 'none' });
    }
  },

  // 选择记录
  onRecordChange(e) {
    const record = this.data.pointRecords[e.detail.value];
    this.setData({ 
      selectedRecord: record,
      appealText: ''
    });
  },

  onAppealInput(e) {
    this.setData({ appealText: e.detail.value });
  },

  async submitAppeal() {
    if (!this.data.selectedRecord) {
      wx.showToast({
        title: '请选择申诉记录',
        icon: 'none'
      });
      return;
    }

    if (!this.data.appealText.trim()) {
      wx.showToast({
        title: '请输入申诉理由',
        icon: 'none'
      });
      return;
    }

    this.setData({ isSubmitting: true });
    try {
      await http.post(API.APPEAL_SUBMIT, {
        record_id: this.data.selectedRecord.id,
        reason: this.data.appealText // 修改为 reason
      });

      wx.showToast({
        title: '申诉已提交',
        icon: 'success'
      });

      wx.redirectTo({
        url: '/pages/appeal-list/appeal-list'
      });
    } catch (err) {
      wx.showToast({
        title: err.message || '提交失败',
        icon: 'none'
      });
      this.setData({ isSubmitting: false });
    }
  },

  // 新增：跳转到我的申诉列表
  goAppealList() {
    wx.navigateTo({
      url: '/pages/appeal-list/appeal-list'
    });
  }
});
