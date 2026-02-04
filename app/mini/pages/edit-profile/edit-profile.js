const { http, API, getUserInfo } = require('../../utils/request');

Page({
  data: {
    name: '',
    gender: 1, // 默认为男(1)，2为女
    departments: [], // 子部门一维数组
    departmentIndex: 0, // 选中部门索引
    departmentId: 6, // 默认国内销售部
    submitting: false,
    privacyAgreed: false
  },

  onLoad() {
    this.loadDepartments();
  },

  // 加载部门列表并初始化用户信息
  loadDepartments() {
    const departments = [
      { id: 1, title: '总部' },
      { id: 6, title: '国内销售部' },
      { id: 2, title: '海外销售部' },
      { id: 3, title: '财务部' },
      { id: 4, title: '技术部' },
    ];
    console.log('hardcoded departments:', departments); // 打印硬编码的部门列表
    
    // 获取本地 userInfo
    let userInfo = wx.getStorageSync('userInfo') || {};
    // 兼容 getUserInfo 方法
    if (typeof getUserInfo === 'function') {
      const info = getUserInfo();
      if (info && Object.keys(info).length > 0) userInfo = info;
    }
    // 取部门
    let departmentId = userInfo?.department_id || 6;
    let departmentIndex = departments.findIndex(dept => dept.id === departmentId);
    if (departmentIndex === -1) {
      departmentIndex = 1; // 默认选中国内销售部
      departmentId = 6;
    }
    
    this.setData({
      departments,
      departmentIndex,
      departmentId,
      name: userInfo?.nickname || userInfo?.username || '',
      gender: userInfo?.gender === 2 ? 2 : 1 // 仅允许1或2，默认为1
    });
    console.log('最终 setData:', {
      departments,
      departmentIndex,
      departmentId
    });
  },

  // picker选中
  onDepartmentChange(e) {
    console.log('onDepartmentChange:', e);
    const departmentIndex = e.detail.value;
    const { departments } = this.data;
    let departmentId = 6;
    if (departments[departmentIndex]) {
      departmentId = departments[departmentIndex].id;
    } else {
      console.log('onDepartmentChange: 部门数据异常', departments, departmentIndex);
    }
    console.log('after onDepartmentChange:', departmentIndex, departmentId);
    this.setData({ departmentIndex, departmentId });
  },

  // 输入事件处理
  onNameInput(e) { this.setData({ name: e.detail.value }) },
  onGenderSelect(e) {
    // 1为男，2为女
    const gender = parseInt(e.currentTarget.dataset.gender, 10);
    this.setData({ gender: gender === 2 ? 2 : 1 });
  },

  // 表单验证
  validateForm() {
    const { name, gender, departmentId } = this.data;
    if (!name.trim()) {
      wx.showToast({ title: '请输入侠客姓名', icon: 'none' });
      return false;
    }
    if (gender !== 1 && gender !== 2) {
      wx.showToast({ title: '请选择侠客性别', icon: 'none' });
      return false;
    }
    if (!departmentId) {
      wx.showToast({ title: '请选择侠客门派', icon: 'none' });
      return false;
    }
    return true;
  },

  // 提交资料
  async submitProfile() {
    if (!this.validateForm()) return;
    this.setData({ submitting: true });
    try {
      const userInfo = getUserInfo();
      const data = {
        user_id: userInfo?.user_id || 0,
        name: this.data.name,
        gender: this.data.gender,
        department_id: this.data.departmentId
      };

      const res = await http.post(API.USER_UPDATE, data);
      if (res && res.code === 0) {
        wx.showToast({ title: '保存成功', icon: 'success', duration: 1500 });
        setTimeout(() => {
          wx.navigateBack({ delta: 1 }); // 返回上一页，通常是“我的”页面
        }, 1500);
      } else {
        wx.showToast({ title: res.message || '保存失败', icon: 'none' });
      }
    } catch (err) {
      wx.showToast({
        title: err.message || '保存失败',
        icon: 'none'
      });
    } finally {
      this.setData({ submitting: false });
    }
  },

  onPrivacyCheck(e) {
    // e.detail.value 是数组，勾选时为 ['on']，未勾选为 []
    this.setData({ privacyAgreed: Array.isArray(e.detail.value) ? e.detail.value.length > 0 : !!e.detail.value });
  },

  onShowPrivacyPolicy() {
    wx.showModal({
      title: '隐私政策',
      content: '我们仅收集您填写的侠客昵称、性别及门派信息，用于完善您的身份识别，不会用于其他用途。您的信息将严格保密，除用于身份识别外不会用于其他目的。如有疑问请联系管理员。',
      showCancel: false,
      confirmText: '关闭'
    });
  },
});
