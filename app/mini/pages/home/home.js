// pages/home/home.js
import wxCharts from '../../utils/wxcharts';
import API from '../../utils/api.js';
import { http, getUserInfo, silentLogin } from '../../utils/request.js'; // 新增：引入 request.js
const app = getApp();
let lineChart = null; // 保存图表实例

Page({
  data: {
    records: [],
    chartType: 'week', // 默认显示周趋势
    chartDataArray: [], // 用于 swiper 的趋势图数据数组
    currentSwiperIndex: 0,
    summary: { 
      week: 0,
      month: 0,
      year: 0,
      total: 0
    },
    dynamics: [],
    showLogin: false,
    userInfo: null,
    chartWidth: 375, // 默认宽度
    chartLoading: false, // 图表加载状态
  },
  async onLoad() {
    // 自动静默登录并刷新用户信息
    try {
      await silentLogin();
      const userInfo = getUserInfo();
      this.setData({ userInfo });
      await this.fetchSummaryAndDynamics(); // 首次加载只请求一次汇总和动态
      await this.fetchTrendChart(this.data.chartType); // 请求默认趋势图
    } catch (err) {
      wx.showToast({ title: '自动登录失败', icon: 'none' });
    }
    // 获取屏幕宽度并保存
    const sysInfo = wx.getSystemInfoSync();
    // 计算图表宽度：每个数据点60px，最小为屏幕宽度
    this.setData({
      chartWidth: Math.max(sysInfo.windowWidth, (this.data.chartDataArray.length || 7) * 60)
    });
    // 不再调用 this.initChart('week')，避免空数据初始化
  },

  // 只请求汇总和动态数据（只需加载一次）
  async fetchSummaryAndDynamics() {
    try {
      const summaryRes = await http.post(API.DASHBOARD_SUMMARY, {});
      const dynamicsRes = await http.post(API.DASHBOARD_DYNAMICS, {});
      // 适配 summary 嵌套结构
      const summaryData = (summaryRes && summaryRes.data && summaryRes.data.summary)
        ? summaryRes.data.summary
        : {};
      // 适配 dynamics 嵌套结构
      const dynamicsList = (dynamicsRes && dynamicsRes.data && Array.isArray(dynamicsRes.data.dynamics))
        ? dynamicsRes.data.dynamics
        : [];
      this.setData({
        summary: summaryData,
        dynamics: dynamicsList,
        records: dynamicsList
      });
    } catch (err) {
      wx.showToast({
        title: '请求失败',
        icon: 'none'
      });
    }
  },

  // 只请求趋势图数据（每次切换都请求）
  async fetchTrendChart(type) {
    this.setData({ chartLoading: true });
    
    try {
      const trendRes = await http.post(API.DASHBOARD_CHARTS, { type });
      console.log('趋势图API返回:', trendRes);
      
      const chartDataArray = (trendRes && trendRes.data && Array.isArray(trendRes.data.trendDataArray))
        ? trendRes.data.trendDataArray
        : [];
      
      // 计算宽度：每个点60-80rpx，最小为屏幕宽度
      const sysInfo = wx.getSystemInfoSync();
      const pointWidth = type === 'year' ? 80 : 70; // 年度图表点间距更大
      const chartWidth = Math.max(sysInfo.windowWidth - 80, chartDataArray.length * pointWidth);
      
      this.setData({
        chartDataArray: chartDataArray,
        chartWidth: chartWidth,
        chartLoading: false
      });
      
      // 延迟初始化图表，确保canvas渲染完成
      setTimeout(() => {
        this.initChart(type);
      }, 100);
    } catch (err) {
      console.error('图表加载失败:', err);
      this.setData({ chartLoading: false });
      wx.showToast({
        title: '图表加载失败',
        icon: 'none'
      });
    }
  },

  // 初始化图表
  initChart(type) {
    const chartData = Array.isArray(this.data.chartDataArray) ? this.data.chartDataArray : [];
    console.log('图表数据:', chartData);
    
    if (!chartData || chartData.length === 0) {
      console.log('图表数据为空');
      return;
    }

    // 直接使用后端返回的标签（后端已统一格式化）
    let categories;
    
    if (type === 'week') {
      categories = chartData.map(item => item.day || '');
    } else if (type === 'month') {
      categories = chartData.map(item => item.day || '');
    } else if (type === 'year') {
      categories = chartData.map(item => item.month || '');
    } else {
      categories = chartData.map(item => '');
    }

    if (!categories.some(c => !!c)) {
      console.log('横坐标数据为空');
      return;
    }

    const seriesData = chartData.map(item => item.value || 0);
    const chartWidth = this.data.chartWidth || 375;

    // 销毁旧图表
    if (lineChart) {
      lineChart = null;
    }

    try {
      lineChart = new wxCharts({
        canvasId: 'trendChart',
        type: 'line',
        categories: categories,
        series: [
          {
            name: '积分',
            data: seriesData,
            format: (val) => val.toFixed(0),
            color: '#44BBBB',
          },
        ],
        xAxis: {
          disableGrid: false,
          gridColor: '#F5F5F5',
          fontColor: '#999',
          fontSize: 10,
        },
        yAxis: {
          title: '',
          format: (val) => val.toFixed(0),
          min: 0,
          gridColor: '#F5F5F5',
          fontColor: '#999',
          fontSize: 10,
        },
        width: chartWidth,
        height: 210, // rpx转px
        dataLabel: true,
        dataPointShape: true,
        extra: {
          lineStyle: 'curve',
          pointShape: 'circle',
        },
        legend: false,
        background: '#ffffff',
        enableScroll: false,
      });
      
      console.log('图表初始化成功:', type);
    } catch (err) {
      console.error('图表初始化失败:', err);
    }
  },

  // 切换图表数据
  switchChart(e) {
    const type = e.currentTarget.dataset.type;
    if (type === this.data.chartType) return; // 避免重复请求
    
    this.setData({
      chartType: type,
    });
    this.fetchTrendChart(type);
  },

    // 查看积分记录详情
    viewPointDetails(e) {
      const pointId = e.currentTarget.dataset.id;
      wx.navigateTo({ url: `/pages/point-detail/point-detail?id=${pointId}` });
    },
  
    // 查看更多记录
    viewMoreRecords() {
      wx.navigateTo({ url: '/pages/points-list/points-list' });
    },
});
