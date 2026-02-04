const config = require('../config/index.js');
const BASE_URL = config.BASE_URL;

const API = {
  // 用户认证
  LOGIN: `${BASE_URL}/api/auth/login`, // 统一的登录接口
  LOGOUT: `${BASE_URL}/api/auth/logout`, // 登出
  CHECK_LOGIN: `${BASE_URL}/api/auth/check`, // 检查登录状态
  REGISTER: `${BASE_URL}/api/auth/register`,  // 注册
  RESET_PASSWORD: `${BASE_URL}/api/auth/reset`, // 重置密码
  FORGOT_PASSWORD: `${BASE_URL}/api/auth/forgot`, // 忘记密码
  CHANGE_PASSWORD: `${BASE_URL}/api/auth/change`, // 修改密码  

  // 获取用户信息
  USER_INFO: `${BASE_URL}/api/user/index`,  // 获取用户信息
  USER_UPDATE: `${BASE_URL}/api/user/update`, // 更新用户信息
  USER_AVATAR: `${BASE_URL}/api/user/avatar`, // 上传用户头像

  // 积分相关
  POINTS_LIST: `${BASE_URL}/api/points/list`, // 积分列表
  POINTS_AWARD: `${BASE_URL}/api/points/award`, // 积分奖励
  POINTS_REQUEST: `${BASE_URL}/api/points/require`, // 申请积分
  POINTS_REQUEST_LIST: `${BASE_URL}/api/points/requireList`, // 申请积分列表
  POINTS_ACCEPT: `${BASE_URL}/api/points/accept`, // 接受积分
  POINTS_DETAIL: `${BASE_URL}/api/points/detail`, // 积分记录详情 需要拼接 ID

  // 积分排行榜
  POINTS_RANKING: `${BASE_URL}/api/ranking/index`, // 积分榜

  // 积分规则
  RULES: `${BASE_URL}/api/rules`, // 积分规则
  RULES_CATEGORIES: `${BASE_URL}/api/rules/categories`, // 积分规则分类

  // 部门管理
  DEPARTMENTS: `${BASE_URL}/api/department`, // 部门列表
  DEPARTMENTS_USERS: `${BASE_URL}/api/department/users`, // 部门用户列表 需要拼接 departmentId

  // 申诉
  APPEAL_LIST: `${BASE_URL}/api/appeal`, // 申诉列表
  APPEAL_CREATE: `${BASE_URL}/api/appeal/create`, // 创建申诉
  APPEAL_SUBMIT: `${BASE_URL}/api/appeal/submit`, // 提交申诉
  APPEAL_DETAIL: `${BASE_URL}/api/appeal/detail`, // 申诉详情 需要拼接 appealId
  APPEAL_CANCEL: `${BASE_URL}/api/appeal/cancel`, // 取消申诉 需要拼接 appealId
  APPEAL_REPLY: `${BASE_URL}/api/appeal/reply`, // 回复申诉 需要拼接 appealId

  // 内容相关
  LEVEL_DESCRIPTION: `${BASE_URL}/api/user_grade`, // 等级说明

  // 反馈相关
  FEEDBACK: `${BASE_URL}/api/feedback`, // 反馈列表
  FEEDBACK_SUBMIT: `${BASE_URL}/api/feedback/submit`, // 提交反馈
  FEEDBACK_DETAIL: `${BASE_URL}/api/feedback/detail`, // 反馈详情 需要拼接 feedbackId
  FEEDBACK_DELETE: `${BASE_URL}/api/feedback/delete`, // 删除反馈 需要拼接 feedbackId
  FEEDBACK_UPDATE: `${BASE_URL}/api/feedback/update`, // 更新反馈 需要拼接 feedbackId
  FEEDBACK_REPLY: `${BASE_URL}/api/feedback/reply`, // 回复反馈 需要拼接 feedbackId
  FEEDBACK_SETST: `${BASE_URL}/api/feedback/set`, // 设置反馈状态 需要拼接 feedbackId


  // 导出接口
  EXPORT_POINTS: `${BASE_URL}/api/export/points`, // 导出积分记录
  EXPORT_DEPARTMENTS: `${BASE_URL}/api/export/departments`, // 导出部门信息
  EXPORT_APPEALS: `${BASE_URL}/api/export/appeals`, // 导出申诉记录
  EXPORT_RULES: `${BASE_URL}/api/export/rules`, // 导出积分规则
  EXPORT_USERS: `${BASE_URL}/api/export/users`, // 导出用户信息
  EXPORT_POINTS: `${BASE_URL}/api/export/points`, // 导出积分记录
  EXPORT_APPEALS: `${BASE_URL}/api/export/appeals`, // 导出申诉记录
  EXPORT_RULES: `${BASE_URL}/api/export/rules`, // 导出积分规则

  // 日志与通知相关
  LOGS: `${BASE_URL}/api/logs`,
  NOTIFICATIONS: `${BASE_URL}/api/notifications/all`, // 所有通知列表
  NOTIFICATIONS_DETAIL: `${BASE_URL}/api/notifications/detail`, // 通知详情 需要拼接 ID
  NOTIFICATIONS_COUNT: `${BASE_URL}/api/notifications/count`,  // 未读数量
  NOTIFICATIONS_DEL: `${BASE_URL}/api/notifications/delete `,  // 删除单个
  NOTIFICATIONS_CLEAR: `${BASE_URL}/api/notifications/clear`,  // 删除所有
  NOTIFICATIONS_MARK: `${BASE_URL}/api/notifications/mark`,    // 标记已读
  NOTIFICATIONS_UNMARK: `${BASE_URL}/api/notifications/unmark`,// 标记未读
  NOTIFICATIONS_STAR: `${BASE_URL}/api/notifications/star`,    // 标记重要
  NOTIFICATIONS_NEW: `${BASE_URL}/api/notifications/new`,      // 未读列表
  NOTIFICATIONS_TOP: `${BASE_URL}/api/notifications/top`,      // 重要通知
  NOTIFICATIONS_DONE: `${BASE_URL}/api/notifications/done`,    // 全部已读

  // 仪表板
  DASHBOARD: `${BASE_URL}/api/dashboard`, // 仪表板数据
  DASHBOARD_SUMMARY: `${BASE_URL}/api/dashboard/summary`, // 仪表板汇总数据
  DASHBOARD_DYNAMICS: `${BASE_URL}/api/dashboard/dynamics`, // 仪表板动态数据
  DASHBOARD_CHARTS: `${BASE_URL}/api/dashboard/charts`, // 仪表板图表数据

  // 订阅消息相关
  SUBSCRIBE_MESSAGE: `${BASE_URL}/api/message/subscribe`, // 订阅消息
  SEND_MESSAGE: `${BASE_URL}/api/message/send`, // 发送消息
};

export default API;
