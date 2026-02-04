const { http, API } = require('../../utils/request');

Page({
  data: {
    loading: true,
    categories: [],
    subCategories: [],
    rulesMap: {},
    expandedCategoryId: null, // 当前展开的一级分类id
    scrollIntoView: '',       // 用于 scroll-view 定位
  },

  async onLoad() {
    this.setData({ loading: true });
    try {
      // 获取分类树
      const catRes = await http.get(API.RULES_CATEGORIES);
      const rawCategories = Array.isArray(catRes.data?.categories)
        ? catRes.data.categories
        : [];
      // 一级分类
      const categories = rawCategories
        .map(cat => ({
          id: Number(cat.id),
          category_name: cat.category_name
        }))
        .sort((a, b) => b.category_name.localeCompare(a.category_name)); // 按名称降序
      // 拉平所有二级分类，确保结构
      let subCategories = [];
      rawCategories.forEach(cat => {
        if (Array.isArray(cat.children) && cat.children.length > 0) {
          cat.children.forEach(sub => {
            subCategories.push({
              id: Number(sub.id),
              parent_id: Number(cat.id),
              category_name: sub.category_name,
              description: sub.description || ''
            });
          });
        }
      });
      // 升序排序
      subCategories = subCategories.sort((a, b) => a.id - b.id);

      // 获取所有规则
      const ruleRes = await http.get(API.RULES);
      const rules = Array.isArray(ruleRes.data)
        ? ruleRes.data
        : (Array.isArray(ruleRes.data?.data) ? ruleRes.data.data : []);
      // 按二级分类分组
      const rulesMap = {};
      rules.forEach(rule => {
        const cid = Number(rule.score_category_id);
        if (!rulesMap[cid]) rulesMap[cid] = [];
        rulesMap[cid].push(rule);
      });

      // 可选：调试输出
      // console.log('categories', categories);
      // console.log('subCategories', subCategories);
      // console.log('rulesMap', rulesMap);

      this.setData({
        categories,
        subCategories,
        rulesMap,
        loading: false,
        expandedCategoryId: categories.length > 0 ? categories[0].id : null // 默认第一个展开
      });
    } catch (err) {
      this.setData({ loading: false });
      wx.showToast({ title: '加载失败', icon: 'none' });
    }
  },

  onToggleCategory(e) {
    const { id } = e.currentTarget.dataset;
    const { expandedCategoryId } = this.data;
    this.setData({
      expandedCategoryId: expandedCategoryId === id ? null : id,
      scrollIntoView: `category-${id}`
    });
  }
});
