/**
 * 微信小程序订阅消息后端处理示例
 * 需要在你的后端服务中实现这些接口
 */

const express = require('express');
const router = express.Router();

// 假设你使用的是某个微信SDK，如 wechat-api
const WeChatAPI = require('wechat-api'); // 需要安装: npm install wechat-api

// 微信小程序配置
const wechatConfig = {
  appId: 'your_mini_program_app_id',
  appSecret: 'your_mini_program_app_secret'
};

const api = new WeChatAPI(wechatConfig.appId, wechatConfig.appSecret);

/**
 * 保存用户订阅状态
 * POST /api/message/subscribe
 */
router.post('/subscribe', async (req, res) => {
  try {
    const { template_id, status, openid } = req.body;
    
    // 保存到数据库
    // 这里是伪代码，你需要根据你的数据库结构来实现
    const subscribeRecord = {
      openid: openid,
      template_id: template_id,
      status: status,
      subscribe_time: new Date(),
      is_active: status === 'accept'
    };
    
    // 存储到数据库
    // await db.collection('subscribe_messages').insert(subscribeRecord);
    
    res.json({
      code: 0,
      message: '订阅状态保存成功',
      data: subscribeRecord
    });
  } catch (error) {
    console.error('保存订阅状态失败:', error);
    res.json({
      code: -1,
      message: '保存失败',
      error: error.message
    });
  }
});

/**
 * 发送订阅消息
 * POST /api/message/send
 */
router.post('/send', async (req, res) => {
  try {
    const { 
      openid, 
      template_id, 
      page, 
      data,
      miniprogram_state = 'formal' // formal, trial, developer
    } = req.body;
    
    // 检查用户是否订阅了该模板
    // const subscription = await db.collection('subscribe_messages')
    //   .findOne({ openid, template_id, is_active: true });
    
    // if (!subscription) {
    //   return res.json({
    //     code: -1,
    //     message: '用户未订阅该消息模板'
    //   });
    // }
    
    // 发送订阅消息
    const result = await api.sendSubscribeMessage({
      touser: openid,
      template_id: template_id,
      page: page,
      data: data,
      miniprogram_state: miniprogram_state
    });
    
    // 发送成功后，将订阅状态设为已使用（一次性订阅消息）
    // await db.collection('subscribe_messages')
    //   .updateOne(
    //     { openid, template_id }, 
    //     { $set: { is_active: false, used_time: new Date() } }
    //   );
    
    res.json({
      code: 0,
      message: '消息发送成功',
      data: result
    });
  } catch (error) {
    console.error('发送订阅消息失败:', error);
    res.json({
      code: -1,
      message: '发送失败',
      error: error.message
    });
  }
});

/**
 * 积分变动时发送通知的业务逻辑示例
 */
async function sendPointsChangeNotification(userId, pointsChange, reason) {
  try {
    // 获取用户openid
    // const user = await db.collection('users').findOne({ _id: userId });
    // const openid = user.openid;
    
    // 模板消息数据
    const templateData = {
      // 这些字段需要与你在微信公众平台申请的模板保持一致
      thing1: {
        value: reason.substring(0, 20) // 变动原因，限制20个字符
      },
      number2: {
        value: pointsChange.toString() // 积分变动数量
      },
      time3: {
        value: new Date().toLocaleString() // 变动时间
      },
      thing4: {
        value: '请查看详情' // 备注信息
      }
    };
    
    // 发送消息
    const result = await api.sendSubscribeMessage({
      touser: openid,
      template_id: 'your_template_id_for_points_change',
      page: 'pages/points-list/points-list', // 跳转到积分列表页
      data: templateData
    });
    
    console.log('积分变动通知发送成功:', result);
    return result;
  } catch (error) {
    console.error('发送积分变动通知失败:', error);
    throw error;
  }
}

/**
 * 申请审核结果通知示例
 */
async function sendAppealResultNotification(userId, appealId, status, reason) {
  try {
    // const user = await db.collection('users').findOne({ _id: userId });
    // const openid = user.openid;
    
    const statusText = status === 'approved' ? '审核通过' : '审核未通过';
    
    const templateData = {
      phrase1: {
        value: statusText // 审核结果
      },
      thing2: {
        value: reason.substring(0, 20) // 审核意见
      },
      time3: {
        value: new Date().toLocaleString() // 审核时间
      },
      thing4: {
        value: '点击查看详情' // 温馨提示
      }
    };
    
    const result = await api.sendSubscribeMessage({
      touser: openid,
      template_id: 'your_template_id_for_appeal_result',
      page: `pages/appeal-detail/appeal-detail?id=${appealId}`,
      data: templateData
    });
    
    console.log('审核结果通知发送成功:', result);
    return result;
  } catch (error) {
    console.error('发送审核结果通知失败:', error);
    throw error;
  }
}

// 导出函数供其他模块使用
module.exports = {
  router,
  sendPointsChangeNotification,
  sendAppealResultNotification
};
