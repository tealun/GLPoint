// pages/ranking/ranking.js
const app = getApp();
const { http, API, getUserInfo } = require('../../utils/request');

Page({
  data: {
    rankingType: 'week', // 默认显示本周排行
    rankingData: [], // 排行榜数据
    currentUserId: null, // 当前用户ID
    loading: false,
    noData: false // 新增：无数据提示
  },

  onLoad() {
    const userInfo = getUserInfo();
    this.setData({ 
      currentUserId: userInfo?.user_id || 1  // 测试时默认为ID 1
    });
    this.fetchRankingData('week');
  },

  // 切换排行榜类别
  switchRanking(e) {
    const type = e.currentTarget.dataset.type;
    this.setData({ 
      rankingType: type,
      loading: true 
    });
    this.fetchRankingData(type);
  },

  // 根据类型获取排行榜数据
  fetchRankingData(type) {
    this.setData({ loading: true, noData: false });
    http.post(API.POINTS_RANKING, { period: type }).then(res => {
      let list = Array.isArray(res.data) ? res.data : [];
      // 字段映射，头像根据 sex 字段判断
      const rankingData = list.map(item => ({
        user_id: item.user_id,
        nickname: item.nickname,
        points: item.score,
        avatar_url: item.sex === 2
          ? '/resources/icons/default-woman.png'
          : '/resources/icons/default-man.png'
      }));
      this.setData({
        rankingData,
        loading: false,
        noData: rankingData.length === 0
      });
    }).catch(() => {
      this.setData({
        rankingData: [],
        loading: false,
        noData: true
      });
    });
  }
});
