// 调试日志函数
const log = {
  info: (tag, data) => {
    // console.log(`[${new Date().toLocaleTimeString()}][${tag}] >>>>>>`, data)
  },
  error: (tag, data) => {
    // console.error(`[${new Date().toLocaleTimeString()}][${tag}] xxxxxx`, data)
  },
  debug: (tag, data) => {
    // console.debug(`[${new Date().toLocaleTimeString()}][${tag}] ------`, data)
  },
  flow: (tag, step, data) => {
    // console.log(`[${new Date().toLocaleTimeString()}][${tag}][${step}] >>>>>> `, data)
  }
};

const API = require('./api.js').default;
import store from './store.js';

// 登录Promise，防止并发登录
let loginPromise = null;
let tokenChecked = false;

// 获取用户信息 - 从store获取
const getUserInfo = () => {
  return store.getUserInfo();
};

// 设置用户信息 - 更新到store
const setUserInfo = (info) => {
  if (!info) return;
  store.setUserInfo(info);
};

// 清除用户信息 - 从store清除
const clearUserInfo = () => {
  store.clearAuth();
  tokenChecked = false;
};

// 静默登录
const silentLogin = () => {
  if (loginPromise) {
    return loginPromise;
  }

  loginPromise = new Promise((resolve, reject) => {
    // log.flow('登录流程', '1.开始', '准备获取微信code');
    wx.login({
      success: (loginRes) => {
        // log.flow('登录流程', '2.wx.login成功', loginRes);
        if (loginRes.code) {
          wx.request({
            url: API.LOGIN,
            method: 'POST',
            data: { code: loginRes.code },
            header: { 'Content-Type': 'application/json' },
            success: (res) => {
              // log.flow('登录流程', '4.登录响应', res.data);
              
              if (res.statusCode === 200 && res.data.code === 0) {
                const { token, user } = res.data.data;
                if (token) {
                  // log.flow('登录流程', '5.保存数据', {
                  //   hasToken: !!token,
                  //   hasUser: !!user
                  // });
                  
                  wx.setStorageSync('token', token);
                  setUserInfo(user);
                  tokenChecked = true;
                  resolve(res.data);
                } else {
                  const err = new Error('登录响应缺少token');
                  // 更新到store
                  store.setToken(token);
                  setUserInfo(user);
                  store.setLoginReady(true);
                }
              } else {
                const err = new Error(res.data.message || '登录失败');
                // log.error('登录流程', err);
                reject(err);
              }
            },
            fail: (err) => {
              // log.error('登录流程', '请求失败:', err);
              reject(err);
            }
          });
        } else {
          // log.error('登录流程', '获取code失败');
          reject(new Error('获取code失败'));
        }
      },
      fail: (err) => {
        // log.error('登录流程', 'wx.login调用失败:', err);
        reject(err);
      }
    });
  }).finally(() => {
    loginPromise = null;
  });

  return loginPromise;
};

// 不需要token的接口白名单
const NO_TOKEN_APIS = [
  API.LOGIN,
  API.REGISTER
];

// 检查是否需要token
const needToken = (url) => {
  return NO_TOKEN_APIS.indexOf(url) === -1;
};

// 需要更新用户信息的接口列表
const USER_INFO_APIS = [
  API.LOGIN,
  API.USER_INFO,
  API.USER_UPDATE,
  API.CHECK_LOGIN
];

// 检查是否为需要更新用户信息的接口
const isUserInfoApi = (url) => {
  return USER_INFO_APIS.indexOf(url) !== -1;
};

// 检查token状态
const checkToken = async () => {
  // 取消检查登录状态的逻辑
  return true;
};

// 通用请求方法
const request = (url, method = 'POST', data = {}) => {
  return new Promise(async (resolve, reject) => {
    const reqId = Math.random().toString(36).substring(7);
    try {
      // 如果正在登录中，等待登录完成
      if (loginPromise) {
        await loginPromise;
      }
      
      // 取消检查登录状态的逻辑
      
      const fullUrl = url.startsWith('https') ? url : url;
      const headers = { 
        'Content-Type': 'application/json',
      };

      // 获取并检查token
      const token = wx.getStorageSync('token');
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
        // log.flow(`请求${reqId}`, 'headers', headers);
      } else {
        // log.error(`请求${reqId}`, '未找到token');
      }

      // log.flow(`请求${reqId}`, '发送请求', {
      //   url: fullUrl,
      //   method,
      //   headers,
      //   data
      // });

      wx.request({
        url: fullUrl,
        method,
        data,
        header: headers,
        success: async (res) => {
          // log.flow('请求流程', '4.响应', {
          //   url: fullUrl,
          //   status: res.statusCode,
          //   data: res.data
          // });
          
          // 只对需要token的接口处理401错误
          if (needToken(url) && res.data && (res.data.code === 4401 || res.statusCode === 401)) {
            // log.error(`请求${reqId}`, '收到401错误，准备重新登录');
            clearUserInfo();
            tokenChecked = false; // 重置token检查状态
            
            try {
              // 重新登录
              await silentLogin();
              
              // 重新登录成功后，直接重试原始请求（不使用retryCount，避免嵌套）
              // log.flow(`请求${reqId}`, '重新登录成功，重试原始请求');
              
              // 获取新的token
              const newToken = wx.getStorageSync('token');
              if (!newToken) {
                reject({
                  message: '重新登录后未获取到token',
                  code: 4401
                });
                return;
              }
              
              // 更新headers
              headers['Authorization'] = `Bearer ${newToken}`;
              
              // 重新发起请求
              wx.request({
                url: fullUrl,
                method,
                data,
                header: headers,
                success: (retryRes) => {
                  if (retryRes.data && retryRes.data.code !== 0) {
                    reject({
                      message: retryRes.data.message || '请求失败',
                      code: retryRes.data.code,
                      data: retryRes.data
                    });
                    return;
                  }
                  
                  // 更新用户信息缓存
                  if (retryRes.data?.data?.userInfo && isUserInfoApi(url)) {
                    setUserInfo(retryRes.data.data.userInfo);
                  }
                  
                  resolve(retryRes.data);
                },
                fail: (retryErr) => {
                  reject({
                    message: '重试请求失败',
                    error: retryErr
                  });
                }
              });
            } catch (err) {
              // log.error(`请求${reqId}`, '重新登录失败:', err);
              reject({
                message: '登录已失效，自动登录失败',
                code: 4401,
                error: err
              });
            }
            return;
          }

          if (res.data && res.data.code !== 0) {
            reject({
              message: res.data.message || '请求失败',
              code: res.data.code,
              data: res.data
            });
            return;
          }

          // 只在特定接口返回用户信息时更新缓存
          if (res.data?.data?.userInfo && isUserInfoApi(url)) {
            setUserInfo(res.data.data.userInfo);
          }
          
          resolve(res.data);
        },
        fail: (err) => {
          // log.flow('请求流程', '4.失败', err);
          reject({
            message: '网络请求失败',
            error: err
          });
        }
      });
    } catch (err) {
      // log.error(`请求${reqId}`, err);
      reject({ message: '请求失败', error: err });
    }
  });
};

// API请求方法
const http = {
  request,
  get: (url, data) => request(url, 'POST', data),    // 强制POST
  post: (url, data) => request(url, 'POST', data),
  put: (url, data) => request(url, 'POST', data),     // 强制POST
  delete: (url, data) => request(url, 'POST', data),  // 强制POST
};

module.exports = {
  http,
  API,
  getUserInfo,
  setUserInfo,
  clearUserInfo,
  silentLogin
};
