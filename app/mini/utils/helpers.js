/**
 * 通用工具函数库
 */

/**
 * 日期格式化
 * @param {Date|string|number} date 日期
 * @param {string} format 格式 (YYYY-MM-DD HH:mm:ss)
 */
export function formatDate(date, format = 'YYYY-MM-DD HH:mm:ss') {
  if (!date) return '';
  
  const d = new Date(date);
  if (isNaN(d.getTime())) return '';

  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  const hour = String(d.getHours()).padStart(2, '0');
  const minute = String(d.getMinutes()).padStart(2, '0');
  const second = String(d.getSeconds()).padStart(2, '0');

  return format
    .replace('YYYY', year)
    .replace('MM', month)
    .replace('DD', day)
    .replace('HH', hour)
    .replace('mm', minute)
    .replace('ss', second);
}

/**
 * 相对时间格式化（刚刚、1分钟前等）
 * @param {Date|string|number} date 日期
 */
export function formatRelativeTime(date) {
  if (!date) return '';
  
  const d = new Date(date);
  if (isNaN(d.getTime())) return '';

  const now = new Date();
  const diff = now.getTime() - d.getTime();
  const seconds = Math.floor(diff / 1000);
  const minutes = Math.floor(seconds / 60);
  const hours = Math.floor(minutes / 60);
  const days = Math.floor(hours / 24);

  if (seconds < 60) return '刚刚';
  if (minutes < 60) return `${minutes}分钟前`;
  if (hours < 24) return `${hours}小时前`;
  if (days < 7) return `${days}天前`;
  
  return formatDate(date, 'YYYY-MM-DD');
}

/**
 * 防抖函数
 * @param {Function} func 要执行的函数
 * @param {number} wait 等待时间(ms)
 */
export function debounce(func, wait = 300) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}

/**
 * 节流函数
 * @param {Function} func 要执行的函数
 * @param {number} wait 等待时间(ms)
 */
export function throttle(func, wait = 300) {
  let timeout;
  let previous = 0;
  
  return function(...args) {
    const now = Date.now();
    const remaining = wait - (now - previous);
    
    if (remaining <= 0) {
      if (timeout) {
        clearTimeout(timeout);
        timeout = null;
      }
      previous = now;
      func.apply(this, args);
    } else if (!timeout) {
      timeout = setTimeout(() => {
        previous = Date.now();
        timeout = null;
        func.apply(this, args);
      }, remaining);
    }
  };
}

/**
 * 深拷贝
 * @param {*} obj 要拷贝的对象
 */
export function deepClone(obj) {
  if (obj === null || typeof obj !== 'object') return obj;
  if (obj instanceof Date) return new Date(obj);
  if (obj instanceof Array) return obj.map(item => deepClone(item));
  
  const cloned = {};
  Object.keys(obj).forEach(key => {
    cloned[key] = deepClone(obj[key]);
  });
  return cloned;
}

/**
 * 判断是否为空
 * @param {*} value 要判断的值
 */
export function isEmpty(value) {
  if (value === null || value === undefined) return true;
  if (typeof value === 'string') return value.trim() === '';
  if (Array.isArray(value)) return value.length === 0;
  if (typeof value === 'object') return Object.keys(value).length === 0;
  return false;
}

/**
 * 数字格式化（千分位）
 * @param {number} num 数字
 */
export function formatNumber(num) {
  if (num === null || num === undefined) return '0';
  return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * 手机号脱敏
 * @param {string} phone 手机号
 */
export function maskPhone(phone) {
  if (!phone) return '';
  return phone.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2');
}

/**
 * 姓名脱敏
 * @param {string} name 姓名
 */
export function maskName(name) {
  if (!name) return '';
  if (name.length <= 2) return name[0] + '*';
  return name[0] + '*'.repeat(name.length - 2) + name[name.length - 1];
}

/**
 * 获取URL参数
 * @param {string} name 参数名
 */
export function getUrlParam(name) {
  const pages = getCurrentPages();
  if (pages.length === 0) return null;
  
  const currentPage = pages[pages.length - 1];
  return currentPage.options[name] || null;
}

/**
 * 导航到页面（带参数）
 * @param {string} url 页面路径
 * @param {object} params 参数对象
 */
export function navigateTo(url, params = {}) {
  const query = Object.keys(params)
    .map(key => `${key}=${encodeURIComponent(params[key])}`)
    .join('&');
  
  const fullUrl = query ? `${url}?${query}` : url;
  wx.navigateTo({ url: fullUrl });
}

/**
 * 重定向到页面（带参数）
 * @param {string} url 页面路径
 * @param {object} params 参数对象
 */
export function redirectTo(url, params = {}) {
  const query = Object.keys(params)
    .map(key => `${key}=${encodeURIComponent(params[key])}`)
    .join('&');
  
  const fullUrl = query ? `${url}?${query}` : url;
  wx.redirectTo({ url: fullUrl });
}

/**
 * 数组去重
 * @param {Array} arr 数组
 * @param {string} key 去重的key（对象数组时使用）
 */
export function unique(arr, key) {
  if (!Array.isArray(arr)) return [];
  
  if (key) {
    const seen = new Set();
    return arr.filter(item => {
      const k = item[key];
      if (seen.has(k)) return false;
      seen.add(k);
      return true;
    });
  }
  
  return [...new Set(arr)];
}

/**
 * 数组分组
 * @param {Array} arr 数组
 * @param {Function|string} keyGetter 分组依据（函数或属性名）
 */
export function groupBy(arr, keyGetter) {
  if (!Array.isArray(arr)) return {};
  
  const getKey = typeof keyGetter === 'function'
    ? keyGetter
    : item => item[keyGetter];
  
  return arr.reduce((result, item) => {
    const key = getKey(item);
    if (!result[key]) result[key] = [];
    result[key].push(item);
    return result;
  }, {});
}

/**
 * 延迟执行
 * @param {number} ms 延迟时间(ms)
 */
export function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * 重试函数
 * @param {Function} fn 要执行的函数
 * @param {number} maxRetries 最大重试次数
 * @param {number} delay 重试延迟(ms)
 */
export async function retry(fn, maxRetries = 3, delay = 1000) {
  let lastError;
  
  for (let i = 0; i < maxRetries; i++) {
    try {
      return await fn();
    } catch (err) {
      lastError = err;
      if (i < maxRetries - 1) {
        await sleep(delay);
      }
    }
  }
  
  throw lastError;
}

/**
 * 选择图片
 * @param {number} count 数量
 * @param {Array} sizeType 尺寸类型 ['original', 'compressed']
 * @param {Array} sourceType 来源类型 ['album', 'camera']
 */
export function chooseImage(count = 1, sizeType = ['compressed'], sourceType = ['album', 'camera']) {
  return new Promise((resolve, reject) => {
    wx.chooseImage({
      count,
      sizeType,
      sourceType,
      success: resolve,
      fail: reject
    });
  });
}

/**
 * 预览图片
 * @param {Array} urls 图片URL数组
 * @param {number} current 当前显示图片的索引
 */
export function previewImage(urls, current = 0) {
  wx.previewImage({
    urls: Array.isArray(urls) ? urls : [urls],
    current: Array.isArray(urls) ? urls[current] : urls
  });
}

/**
 * 设置页面标题
 * @param {string} title 标题
 */
export function setTitle(title) {
  wx.setNavigationBarTitle({ title });
}

/**
 * 获取系统信息
 */
export function getSystemInfo() {
  return new Promise((resolve, reject) => {
    wx.getSystemInfo({
      success: resolve,
      fail: reject
    });
  });
}

/**
 * 复制到剪贴板
 * @param {string} data 要复制的内容
 */
export function copyToClipboard(data) {
  return new Promise((resolve, reject) => {
    wx.setClipboardData({
      data: String(data),
      success: () => {
        wx.showToast({ title: '复制成功', icon: 'success' });
        resolve();
      },
      fail: reject
    });
  });
}

/**
 * 拨打电话
 * @param {string} phoneNumber 电话号码
 */
export function makePhoneCall(phoneNumber) {
  wx.makePhoneCall({ phoneNumber });
}

/**
 * 扫码
 * @param {boolean} onlyFromCamera 是否只能从相机扫码
 */
export function scanCode(onlyFromCamera = false) {
  return new Promise((resolve, reject) => {
    wx.scanCode({
      onlyFromCamera,
      success: resolve,
      fail: reject
    });
  });
}
