Component({
  properties: {
    show: {
      type: Boolean,
      value: true
    },
    type: {
      type: String,
      value: 'no-data' // no-data, network-error, search-empty
    },
    text: {
      type: String,
      value: ''
    },
    buttonText: {
      type: String,
      value: ''
    }
  },

  data: {
    iconSrc: ''
  },

  lifetimes: {
    attached() {
      this.updateIcon();
    }
  },

  observers: {
    'type': function(newType) {
      this.updateIcon();
    }
  },

  methods: {
    updateIcon() {
      const iconMap = {
        'no-data': '/resources/icons/empty-data.png',
        'network-error': '/resources/icons/network-error.png',
        'search-empty': '/resources/icons/search-empty.png'
      };

      const defaultText = {
        'no-data': '暂无数据',
        'network-error': '网络错误',
        'search-empty': '未找到相关内容'
      };

      this.setData({
        iconSrc: iconMap[this.data.type] || iconMap['no-data']
      });

      // 如果没有自定义文本，使用默认文本
      if (!this.data.text) {
        this.setData({
          text: defaultText[this.data.type] || defaultText['no-data']
        });
      }
    },

    onButtonTap() {
      this.triggerEvent('action');
    }
  }
});
