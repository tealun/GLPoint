const request = require('./request.js');
const API = require('./api.js');

/**
 * 订阅消息管理工具
 */
class SubscribeMessage {
  
  /**
   * 请求订阅消息
   * @param {Array} tmplIds 模板ID数组
   * @param {Function} success 成功回调
   * @param {Function} fail 失败回调
   */
  static requestSubscribeMessage(tmplIds, success, fail) {
    wx.requestSubscribeMessage({
      tmplIds: tmplIds,
      success: (res) => {
        console.log('订阅消息结果:', res);
        if (success) success(res);
      },
      fail: (err) => {
        console.error('订阅消息失败:', err);
        if (fail) fail(err);
      }
    });
  }

  /**
   * 积分变动通知订阅
   * @param {Function} callback 回调函数
   */
  static subscribePointsChange(callback) {
    // 这里需要替换为你在微信公众平台申请的模板ID
    const templateIds = [
      'your_template_id_for_points_change', // 积分变动通知模板ID
    ];

    this.requestSubscribeMessage(templateIds, (res) => {
      // 将订阅结果发送到后端
      this.saveSubscribeResult(templateIds[0], res[templateIds[0]], callback);
    });
  }

  /**
   * 排名变化通知订阅
   * @param {Function} callback 回调函数
   */
  static subscribeRankingChange(callback) {
    const templateIds = [
      'your_template_id_for_ranking_change', // 排名变化通知模板ID
    ];

    this.requestSubscribeMessage(templateIds, (res) => {
      this.saveSubscribeResult(templateIds[0], res[templateIds[0]], callback);
    });
  }

  /**
   * 申请审核结果通知订阅
   * @param {Function} callback 回调函数
   */
  static subscribeAppealResult(callback) {
    const templateIds = [
      'your_template_id_for_appeal_result', // 申请审核结果通知模板ID
    ];

    this.requestSubscribeMessage(templateIds, (res) => {
      this.saveSubscribeResult(templateIds[0], res[templateIds[0]], callback);
    });
  }

  /**
   * 保存订阅结果到后端
   * @param {String} templateId 模板ID
   * @param {String} status 订阅状态
   * @param {Function} callback 回调函数
   */
  static saveSubscribeResult(templateId, status, callback) {
    if (status === 'accept') {
      // 用户同意订阅，保存到后端
      request.post(API.SUBSCRIBE_MESSAGE, {
        template_id: templateId,
        status: status,
        openid: wx.getStorageSync('openid') || ''
      }).then(res => {
        console.log('订阅状态保存成功:', res);
        // 设置订阅状态为启用
        this.setStatus('enabled');
        if (callback) callback(true, res);
      }).catch(err => {
        console.error('订阅状态保存失败:', err);
        if (callback) callback(false, err);
      });
    } else {
      console.log('用户拒绝订阅消息');
      if (callback) callback(false, { message: '用户拒绝订阅' });
    }
  }

  /**
   * 批量订阅多个消息模板
   * @param {Array} templateConfigs 模板配置数组 [{id: 'template_id', name: '模板名称'}]
   * @param {Function} callback 回调函数
   */
  static subscribeBatch(templateConfigs, callback) {
    const templateIds = templateConfigs.map(config => config.id);
    
    this.requestSubscribeMessage(templateIds, (res) => {
      const results = [];
      templateConfigs.forEach(config => {
        const status = res[config.id];
        results.push({
          templateId: config.id,
          templateName: config.name,
          status: status
        });
        
        // 如果用户同意，保存到后端
        if (status === 'accept') {
          this.saveSubscribeResult(config.id, status);
        }
      });
      
      if (callback) callback(results);
    });
  }

  /**
   * 获取订阅消息状态
   * @returns {String} 返回订阅状态：'enabled' 或 'disabled'
   */
  static getStatus() {
    try {
      // 从本地缓存获取订阅状态
      const status = wx.getStorageSync('subscribe_message_status');
      return status || 'disabled';
    } catch (error) {
      console.error('获取订阅状态失败:', error);
      return 'disabled';
    }
  }

  /**
   * 设置订阅消息状态
   * @param {String} status 订阅状态：'enabled' 或 'disabled'
   */
  static setStatus(status) {
    try {
      wx.setStorageSync('subscribe_message_status', status);
      console.log('订阅状态已设置为:', status);
    } catch (error) {
      console.error('设置订阅状态失败:', error);
    }
  }
}

module.exports = SubscribeMessage;
