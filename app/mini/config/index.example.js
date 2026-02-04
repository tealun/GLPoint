/**
 * 小程序 API 地址配置
 * 
 * 配置说明：
 * 1. 开发环境：修改下方 BASE_URL 为本地服务器地址
 * 2. 生产环境：后端在 .env 中配置 API_BASE_URL
 * 3. AppID 在 project.private.config.json 中配置（不提交到Git）
 */

const BASE_URL = 'http://localhost'; // 本地开发时修改此处

module.exports = {
  BASE_URL,
  APP_NAME: 'GLpoint积分系统'
};
