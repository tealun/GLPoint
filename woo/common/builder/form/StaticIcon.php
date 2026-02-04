<?php

namespace woo\common\builder\form;

use think\facade\Config;

class StaticIcon
{
    public static function getIcon()
    {
        $custom_icons = Config::get('woomodel.form_custom_icons', []);
        return [
            'layui' => self::layuiIcon(),
            'woo'   => self::getWooIcon(),
            'custom' => $custom_icons
        ];
    }

    public static function getWooIcon()
    {
        return [
            [
                'name' => '靶心 命中',
                'icon' => 'woo-icon-hit-full',
            ],
            [
                'name' => '盾牌 安全',
                'icon' => 'woo-icon-security-shield',
            ],
            [
                'name' => '签字 审核',
                'icon' => 'woo-icon-sign-review',
            ],
            [
                'name' => '天平 公平',
                'icon' => 'woo-icon-balance',
            ],
            [
                'name' => '数据 数据库',
                'icon' => 'woo-icon-database',
            ],
            [
                'name' => '积分 数据',
                'icon' => 'woo-icon-jifen',
            ],
            [
                'name' => '闹钟 时间',
                'icon' => 'woo-icon-clock',
            ],
            [
                'name' => '垃圾桶',
                'icon' => 'woo-icon-qingchuhuancun',
            ],
            [
                'name' => '视频',
                'icon' => 'woo-icon-shipin',
            ],
            [
                'name' => '更多',
                'icon' => 'woo-icon-gengduo',
            ],
            [
                'name' => '更多',
                'icon' => 'woo-icon-gengduo-2',
            ],
            [
                'name' => '走势',
                'icon' => 'woo-icon-zoushi1',
            ],
            [
                'name' => '订单查询',
                'icon' => 'woo-icon-dingdanchaxun',
            ],
            [
                'name' => '排序',
                'icon' => 'woo-icon-paixu',
            ],
            [
                'name' => '属性',
                'icon' => 'woo-icon-shuxing',
            ],
            [
                'name' => '修改',
                'icon' => 'woo-icon-xiugai',
            ],
            [
                'name' => '编辑 修改',
                'icon' => 'woo-icon-bianji',
            ],
            [
                'name' => '上下箭头',
                'icon' => 'woo-icon-shangxiajiantou',
            ],
            [
                'name' => '左右箭头',
                'icon' => 'woo-icon-zuoyoujiantou',
            ],
            [
                'name' => '单位成员',
                'icon' => 'woo-icon-danweichengyuanguanli',
            ],
            [
                'name' => '工作台',
                'icon' => 'woo-icon-gongzuotai',
            ],
            [
                'name' => '共享设备',
                'icon' => 'woo-icon-gongxiangshebei',
            ],
            [
                'name' => '优化',
                'icon' => 'woo-icon-youhua',
            ],
            [
                'name' => '复制',
                'icon' => 'woo-icon-fuzhi',
            ],
            [
                'name' => '话筒',
                'icon' => 'woo-icon-huatong',
            ],
            [
                'name' => '无线wifi',
                'icon' => 'woo-icon-wuxianwifi',
            ],
            [
                'name' => '用户组',
                'icon' => 'woo-icon-organiz-full',
            ],
            [
                'name' => '电话 有声',
                'icon' => 'woo-icon-dianhua',
            ],
            [
                'name' => '任务',
                'icon' => 'woo-icon-gantt-full',
            ],
            [
                'name' => '取消任务',
                'icon' => 'woo-icon-quxiaorenwu',
            ],
            [
                'name' => '设置组',
                'icon' => 'woo-icon-gearmore',
            ],
            [
                'name' => '设置',
                'icon' => 'woo-icon-set',
            ],
            [
                'name' => '扳手',
                'icon' => 'woo-icon-xiufu',
            ],
            [
                'name' => '扳手-实心',
                'icon' => 'woo-icon-xiufu-full',
            ],
            [
                'name' => '退出',
                'icon' => 'woo-icon-exit',
            ],
            [
                'name' => '网页代码',
                'icon' => 'woo-icon-program-full',
            ],
            [
                'name' => '医院',
                'icon' => 'woo-icon-hospital',
            ],
            [
                'name' => '建筑 高楼',
                'icon' => 'woo-icon-city-full',
            ],
            [
                'name' => '阅读',
                'icon' => 'woo-icon-yudu',
            ],
            [
                'name' => '店铺',
                'icon' => 'woo-icon-dianpu',
            ],

            [
                'name' => '攻略',
                'icon' => 'woo-icon-gonglve',
            ],

            [
                'name' => '链接',
                'icon' => 'woo-icon-lianjie',
            ],

            [
                'name' => '导出 下载',
                'icon' => 'woo-icon-daochu',
            ],
            [
                'name' => '导入 上传',
                'icon' => 'woo-icon-daoru',
            ],
            [
                'name' => '拉黑',
                'icon' => 'woo-icon-lahei',
            ],
            [
                'name' => '管理',
                'icon' => 'woo-icon-guanli1',
            ],
            [
                'name' => '添加好友',
                'icon' => 'woo-icon-tianjiahaoyou',
            ],
            [
                'name' => '权限',
                'icon' => 'woo-icon-quanxian',
            ],
            [
                'name' => '历史记录',
                'icon' => 'woo-icon-lishijilu1',
            ],
            [
                'name' => '扫码',
                'icon' => 'woo-icon-saoma',
            ],
            [
                'name' => '认证',
                'icon' => 'woo-icon-renzheng',
            ],

            [
                'name' => '衣服 服饰',
                'icon' => 'woo-icon-clothes',
            ],
            [
                'name' => '指纹 识别',
                'icon' => 'woo-icon-fingerprint',
            ],
            [
                'name' => '笔记本',
                'icon' => 'woo-icon-notebook',
            ],
            [
                'name' => '二维码',
                'icon' => 'woo-icon-qrcode-full',
            ],
            [
                'name' => '监控',
                'icon' => 'woo-icon-jiankong-full',
            ],
            [
                'name' => '监控',
                'icon' => 'woo-icon-jiankong',
            ],
            [
                'name' => '评价 客服',
                'icon' => 'woo-icon-reception',
            ],
            [
                'name' => '刷新',
                'icon' => 'woo-icon-refresh-full',
            ],
            [
                'name' => '刷新 重置',
                'icon' => 'woo-icon-zhongzhi',
            ],
            [
                'name' => '信息',
                'icon' => 'woo-icon-envelope',
            ],
            [
                'name' => '已读信息',
                'icon' => 'woo-icon-envelope-open',
            ],
            [
                'name' => '面板 主题',
                'icon' => 'woo-icon-sketchpad-theme',
            ],
            [
                'name' => '面板 主题',
                'icon' => 'woo-icon-sketchpad-theme-full',
            ],
            [
                'name' => '加载',
                'icon' => 'woo-icon-load-full',
            ],
            [
                'name' => '云上传',
                'icon' => 'woo-icon-cloud-upload',
            ],
            [
                'name' => '云下载',
                'icon' => 'woo-icon-cloud-download',
            ],
            [
                'name' => '音乐',
                'icon' => 'woo-icon-music',
            ],
            [
                'name' => '闪电',
                'icon' => 'woo-icon-lightning',
            ],
            [
                'name' => '眼睛 可见',
                'icon' => 'woo-icon-visible',
            ],
            [
                'name' => '固定 图钉',
                'icon' => 'woo-icon-fix',
            ],
            [
                'name' => '打楼 企业单位',
                'icon' => 'woo-icon-qiye',
            ],
            [
                'name' => '路线',
                'icon' => 'woo-icon-map-direction',
            ],
            [
                'name' => '轨迹',
                'icon' => 'woo-icon-locus',
            ],
            [
                'name' => '交换 平行线',
                'icon' => 'woo-icon-exchange',
            ],
            [
                'name' => '打印 复印',
                'icon' => 'woo-icon-printing',
            ],
            [
                'name' => '剪切',
                'icon' => 'woo-icon-crop',
            ],
            [
                'name' => '列表 栏目',
                'icon' => 'woo-icon-list-full',
            ],
            [
                'name' => '移动',
                'icon' => 'woo-icon-move',
            ],
            [
                'name' => '左右箭头',
                'icon' => 'woo-icon-jiantou-zuoyouqiehuan',
            ],
            [
                'name' => '上下箭头',
                'icon' => 'woo-icon-jiantou-shangxiaqiehuan',
            ],
            [
                'name' => '随机',
                'icon' => 'woo-icon-random',
            ], [
                'name' => '首页',
                'icon' => 'woo-icon-shouye',
            ],
            [
                'name' => '工具',
                'icon' => 'woo-icon-gongju',
            ],
            [
                'name' => '分类',
                'icon' => 'woo-icon-fenlei',
            ],
            [
                'name' => '购物车',
                'icon' => 'woo-icon-gouwuche',
            ],
            [
                'name' => '我的',
                'icon' => 'woo-icon-wode',
            ], [
                'name' => '分类',
                'icon' => 'woo-icon-kuaijie',
            ],
            [
                'name' => '收藏',
                'icon' => 'woo-icon-shoucang',
            ],
            [
                'name' => '发现',
                'icon' => 'woo-icon-faxian',
            ],
            [
                'name' => '管理',
                'icon' => 'woo-icon-guanli-2',
            ],
            [
                'name' => '统计 投资',
                'icon' => 'woo-icon-tongji',
            ], [
                'name' => '删除',
                'icon' => 'woo-icon-shanchu',
            ],
            [
                'name' => '图片',
                'icon' => 'woo-icon-tupian',
            ],
            [
                'name' => '游戏',
                'icon' => 'woo-icon-youxi',
            ],
            [
                'name' => '消息',
                'icon' => 'woo-icon-xiaoxi',
            ],
            [
                'name' => '历史记录',
                'icon' => 'woo-icon-lishijilu',
            ], [
                'name' => '位置',
                'icon' => 'woo-icon-weizhi',
            ],
            [
                'name' => '语音',
                'icon' => 'woo-icon-yuyin',
            ],
            [
                'name' => '签到',
                'icon' => 'woo-icon-qiandao',
            ],
            [
                'name' => '扫描',
                'icon' => 'woo-icon-saomiao',
            ],
            [
                'name' => '转账',
                'icon' => 'woo-icon-zhuanzhang',
            ], [
                'name' => '身份证',
                'icon' => 'woo-icon-shenfenzheng',
            ],
            [
                'name' => '组织',
                'icon' => 'woo-icon-zuzhi',
            ],
            [
                'name' => '骑士',
                'icon' => 'woo-icon-qishifengcai',
            ],
            [
                'name' => '废旧回收',
                'icon' => 'woo-icon-feijiuhuishou',
            ],
            [
                'name' => '邀请',
                'icon' => 'woo-icon-yaoqing',
            ], [
                'name' => '权益',
                'icon' => 'woo-icon-quanyi',
            ],
            [
                'name' => '完成任务',
                'icon' => 'woo-icon-wanchengrenwu',
            ],
            [
                'name' => '资料',
                'icon' => 'woo-icon-ziliao',
            ],
            [
                'name' => '待审核',
                'icon' => 'woo-icon-daishenhe',
            ],
            [
                'name' => '提示',
                'icon' => 'woo-icon-tishi',
            ], [
                'name' => '日历',
                'icon' => 'woo-icon-rili1',
            ],
            [
                'name' => '存储',
                'icon' => 'woo-icon-cunchu',
            ],
            [
                'name' => '版本',
                'icon' => 'woo-icon-banben',
            ],
        ];
    }

    public static function layuiIcon()
    {
        return [
            [
                'name' => '文件夹',
                'icon' => 'layui-icon-folder',
            ],
            [
                'name' => '文件夹打开',
                'icon' => 'layui-icon-folder-open',
            ],
            [
                'name' => '叶子',
                'icon' => 'layui-icon-leaf',
            ],
            [
                'name' => 'Github',
                'icon' => 'layui-icon-github',
            ],
            [
                'name' => '月亮',
                'icon' => 'layui-icon-moon',
            ],
            [
                'name' => '错误',
                'icon' => 'layui-icon-error',
            ],
            [
                'name' => '成功',
                'icon' => 'layui-icon-success',
            ],
            [
                'name' => '问号',
                'icon' => 'layui-icon-question',
            ],
            [
                'name' => '锁定',
                'icon' => 'layui-icon-lock',
            ],
            [
                'name' => '显示',
                'icon' => 'layui-icon-eye',
            ],
            [
                'name' => '隐藏',
                'icon' => 'layui-icon-eye-invisible',
            ],
            [
                'name' => '清空/删除',
                'icon' => 'layui-icon-clear',
            ],
            [
                'name' => '退格',
                'icon' => 'layui-icon-backspace',
            ],
            [
                'name' => '禁用',
                'icon' => 'layui-icon-disabled',
            ],
            [
                'name' => '感叹号/提示',
                'icon' => 'layui-icon-tips-fill',
            ],
            [
                'name' => '测试/K线图',
                'icon' => 'layui-icon-test',
            ],
            [
                'name' => '音乐/音符',
                'icon' => 'layui-icon-music',
            ],
            [
                'name' => 'Chrome',
                'icon' => 'layui-icon-chrome',
            ],
            [
                'name' => 'Firefox',
                'icon' => 'layui-icon-firefox',
            ],
            [
                'name' => 'Edge',
                'icon' => 'layui-icon-edge',
            ],
            [
                'name' => 'IE',
                'icon' => 'layui-icon-ie',
            ],
            [
                'name' => '实心',
                'icon' => 'layui-icon-heart-fill',
            ],
            [
                'name' => '空心',
                'icon' => 'layui-icon-heart',
            ],
            [
                'name' => '太阳/明亮',
                'icon' => 'layui-icon-light',
            ],
            [
                'name' => '时间/历史',
                'icon' => 'layui-icon-time',
            ],
            [
                'name' => '蓝牙',
                'icon' => 'layui-icon-bluetooth',
            ],
            [
                'name' => '@艾特',
                'icon' => 'layui-icon-at',
            ],
            [
                'name' => '静音',
                'icon' => 'layui-icon-mute',
            ],
            [
                'name' => '录音/麦克风',
                'icon' => 'layui-icon-mike',
            ],
            [
                'name' => '密钥/钥匙',
                'icon' => 'layui-icon-key',
            ],
            [
                'name' => '礼物/活动',
                'icon' => 'layui-icon-gift',
            ],
            [
                'name' => '邮箱',
                'icon' => 'layui-icon-email',
            ],
            [
                'name' => 'RSS',
                'icon' => 'layui-icon-rss',
            ],
            [
                'name' => 'WiFi',
                'icon' => 'layui-icon-wifi',
            ],
            [
                'name' => '退出/注销',
                'icon' => 'layui-icon-logout',
            ],
            [
                'name' => 'Android 安卓',
                'icon' => 'layui-icon-android',
            ],
            [
                'name' => 'Apple IOS 苹果',
                'icon' => 'layui-icon-ios',
            ],
            [
                'name' => 'Windows',
                'icon' => 'layui-icon-windows',
            ],
            [
                'name' => '穿梭框',
                'icon' => 'layui-icon-transfer',
            ],
            [
                'name' => '客服',
                'icon' => 'layui-icon-service',
            ],
            [
                'name' => '减',
                'icon' => 'layui-icon-subtraction',
            ],
            [
                'name' => '加',
                'icon' => 'layui-icon-addition',
            ],
            [
                'name' => '滑块',
                'icon' => 'layui-icon-slider',
            ],
            [
                'name' => '打印',
                'icon' => 'layui-icon-print',
            ],
            [
                'name' => '导出',
                'icon' => 'layui-icon-export',
            ],
            [
                'name' => '列',
                'icon' => 'layui-icon-cols',
            ],
            [
                'name' => '退出全屏',
                'icon' => 'layui-icon-screen-restore',
            ],
            [
                'name' => '全屏',
                'icon' => 'layui-icon-screen-full',
            ],
            [
                'name' => '半星',
                'icon' => 'layui-icon-rate-half',
            ],
            [
                'name' => '星星-空心',
                'icon' => 'layui-icon-rate',
            ],
            [
                'name' => '星星-实心',
                'icon' => 'layui-icon-rate-solid',
            ],
            [
                'name' => '手机',
                'icon' => 'layui-icon-cellphone',
            ],
            [
                'name' => '验证码',
                'icon' => 'layui-icon-vercode',
            ],
            [
                'name' => '微信',
                'icon' => 'layui-icon-login-wechat',
            ],
            [
                'name' => 'QQ',
                'icon' => 'layui-icon-login-qq',
            ],
            [
                'name' => '微博',
                'icon' => 'layui-icon-login-weibo',
            ],
            [
                'name' => '密码',
                'icon' => 'layui-icon-password',
            ],
            [
                'name' => '用户名',
                'icon' => 'layui-icon-username',
            ],
            [
                'name' => '刷新-粗',
                'icon' => 'layui-icon-refresh-3',
            ],
            [
                'name' => '授权',
                'icon' => 'layui-icon-auz',
            ],
            [
                'name' => '左向右伸缩菜单',
                'icon' => 'layui-icon-spread-left',
            ],
            [
                'name' => '右向左伸缩菜单',
                'icon' => 'layui-icon-shrink-right',
            ],
            [
                'name' => '雪花',
                'icon' => 'layui-icon-snowflake',
            ],
            [
                'name' => '提示说明',
                'icon' => 'layui-icon-tips',
            ],
            [
                'name' => '便签',
                'icon' => 'layui-icon-note',
            ],
            [
                'name' => '主页',
                'icon' => 'layui-icon-home',
            ],
            [
                'name' => '高级',
                'icon' => 'layui-icon-senior',
            ],
            [
                'name' => '刷新',
                'icon' => 'layui-icon-refresh',
            ],
            [
                'name' => '刷新',
                'icon' => 'layui-icon-refresh-1',
            ],
            [
                'name' => '旗帜',
                'icon' => 'layui-icon-flag',
            ],
            [
                'name' => '主题',
                'icon' => 'layui-icon-theme',
            ],
            [
                'name' => '消息-通知',
                'icon' => 'layui-icon-notice',
            ],
            [
                'name' => '网站',
                'icon' => 'layui-icon-website',
            ],
            [
                'name' => '控制台',
                'icon' => 'layui-icon-console',
            ],
            [
                'name' => '表情-惊讶',
                'icon' => 'layui-icon-face-surprised',
            ],
            [
                'name' => '设置-空心',
                'icon' => 'layui-icon-set',
            ],
            [
                'name' => '模板',
                'icon' => 'layui-icon-template-1',
            ],
            [
                'name' => '应用',
                'icon' => 'layui-icon-app',
            ],
            [
                'name' => '模板',
                'icon' => 'layui-icon-template',
            ],
            [
                'name' => '赞',
                'icon' => 'layui-icon-praise',
            ],
            [
                'name' => '踩',
                'icon' => 'layui-icon-tread',
            ],
            [
                'name' => '男',
                'icon' => 'layui-icon-male',
            ],
            [
                'name' => '女',
                'icon' => 'layui-icon-female',
            ],
            [
                'name' => '相机-空心',
                'icon' => 'layui-icon-camera',
            ],
            [
                'name' => '相机-实心',
                'icon' => 'layui-icon-camera-fill',
            ],
            [
                'name' => '菜单-水平',
                'icon' => 'layui-icon-more',
            ],
            [
                'name' => '菜单-垂直',
                'icon' => 'layui-icon-more-vertical',
            ],

            [
                'name' => '人民币',
                'icon' => 'layui-icon-rmb',
            ],
            [
                'name' => '美元',
                'icon' => 'layui-icon-dollar',
            ],
            [
                'name' => '钻石',
                'icon' => 'layui-icon-diamond',
            ],
            [
                'name' => '火',
                'icon' => 'layui-icon-fire',
            ],
            [
                'name' => '返回',
                'icon' => 'layui-icon-return',
            ],
            [
                'name' => '位置-地图',
                'icon' => 'layui-icon-location',
            ],
            [
                'name' => '办公-阅读',
                'icon' => 'layui-icon-read',
            ],
            [
                'name' => '调查',
                'icon' => 'layui-icon-survey',
            ],
            [
                'name' => '表情-微笑',
                'icon' => 'layui-icon-face-smile',
            ],
            [
                'name' => '表情-哭泣',
                'icon' => 'layui-icon-face-cry',
            ],
            [
                'name' => '购物车',
                'icon' => 'layui-icon-cart-simple',
            ],
            [
                'name' => '购物车',
                'icon' => 'layui-icon-cart',
            ],
            [
                'name' => '下一页',
                'icon' => 'layui-icon-next',
            ],
            [
                'name' => '上一页',
                'icon' => 'layui-icon-prev',
            ],
            [
                'name' => '上传-空心-拖拽',
                'icon' => 'layui-icon-upload-drag',
            ],
            [
                'name' => '上传-实心',
                'icon' => 'layui-icon-upload',
            ],
            [
                'name' => '下载-圆圈',
                'icon' => 'layui-icon-download-circle',
            ],
            [
                'name' => '组件',
                'icon' => 'layui-icon-component',
            ],
            [
                'name' => '文件-粗',
                'icon' => 'layui-icon-file-b',
            ],
            [
                'name' => '用户',
                'icon' => 'layui-icon-user',
            ],
            [
                'name' => '发现-实心',
                'icon' => 'layui-icon-find-fill',
            ],
            [
                'name' => 'loading',
                'icon' => 'layui-icon-loading',
            ],
            [
                'name' => 'loading',
                'icon' => 'layui-icon-loading-1',
            ],
            [
                'name' => '添加',
                'icon' => 'layui-icon-add-1',
            ],
            [
                'name' => '播放',
                'icon' => 'layui-icon-play',
            ],
            [
                'name' => '暂停',
                'icon' => 'layui-icon-pause',
            ],
            [
                'name' => '音频-耳机',
                'icon' => 'layui-icon-headset',
            ],
            [
                'name' => '视频',
                'icon' => 'layui-icon-video',
            ],
            [
                'name' => '语音-声音',
                'icon' => 'layui-icon-voice',
            ],
            [
                'name' => '消息-通知-喇叭',
                'icon' => 'layui-icon-speaker',
            ],
            [
                'name' => '删除线',
                'icon' => 'layui-icon-fonts-del',
            ],
            [
                'name' => '代码',
                'icon' => 'layui-icon-fonts-code',
            ],
            [
                'name' => 'HTML',
                'icon' => 'layui-icon-fonts-html',
            ],
            [
                'name' => '字体加粗',
                'icon' => 'layui-icon-fonts-strong',
            ],
            [
                'name' => '删除链接',
                'icon' => 'layui-icon-unlink',
            ],
            [
                'name' => '图片',
                'icon' => 'layui-icon-picture',
            ],
            [
                'name' => '链接',
                'icon' => 'layui-icon-link',
            ],
            [
                'name' => '表情-笑-粗',
                'icon' => 'layui-icon-face-smile-b',
            ],
            [
                'name' => '左对齐',
                'icon' => 'layui-icon-align-left',
            ],
            [
                'name' => '右对齐',
                'icon' => 'layui-icon-align-right',
            ],
            [
                'name' => '居中对齐',
                'icon' => 'layui-icon-align-center',
            ],
            [
                'name' => '字体-下划线',
                'icon' => 'layui-icon-fonts-u',
            ],
            [
                'name' => '字体-斜体',
                'icon' => 'layui-icon-fonts-i',
            ],
            [
                'name' => 'abs 选项卡',
                'icon' => 'layui-icon-tabs',
            ],
            [
                'name' => '单选框-选中',
                'icon' => 'layui-icon-radio',
            ],
            [
                'name' => '单选框-候选',
                'icon' => 'layui-icon-circle',
            ],
            [
                'name' => '编辑',
                'icon' => 'layui-icon-edit',
            ],
            [
                'name' => '分享',
                'icon' => 'layui-icon-share',
            ],
            [
                'name' => '删除',
                'icon' => 'layui-icon-delete',
            ],
            [
                'name' => '表单',
                'icon' => 'layui-icon-form',
            ],
            [
                'name' => '手机-细体',
                'icon' => 'layui-icon-cellphone-fine',
            ],
            [
                'name' => '聊天 对话 沟通',
                'icon' => 'layui-icon-dialogue',
            ],
            [
                'name' => '文字格式化',
                'icon' => 'layui-icon-fonts-clear',
            ],
            [
                'name' => '窗口',
                'icon' => 'layui-icon-layer',
            ],
            [
                'name' => '日期',
                'icon' => 'layui-icon-date',
            ],
            [
                'name' => '水 下雨',
                'icon' => 'layui-icon-water',
            ],
            [
                'name' => '代码-圆圈',
                'icon' => 'layui-icon-code-circle',
            ],
            [
                'name' => '轮播组图',
                'icon' => 'layui-icon-carousel',
            ],
            [
                'name' => '翻页',
                'icon' => 'layui-icon-prev-circle',
            ],
            [
                'name' => '布局',
                'icon' => 'layui-icon-layouts',
            ],
            [
                'name' => '工具',
                'icon' => 'layui-icon-util',
            ],
            [
                'name' => '选择模板',
                'icon' => 'layui-icon-templeate-1',
            ],
            [
                'name' => '上传-圆圈',
                'icon' => 'layui-icon-upload-circle',
            ],
            [
                'name' => '树',
                'icon' => 'layui-icon-tree',
            ],
            [
                'name' => '表格',
                'icon' => 'layui-icon-table',
            ],
            [
                'name' => '图表',
                'icon' => 'layui-icon-chart',
            ],
            [
                'name' => '图标 报表 屏幕',
                'icon' => 'layui-icon-chart-screen',
            ],
            [
                'name' => '引擎',
                'icon' => 'layui-icon-engine',
            ],
            [
                'name' => '下三角',
                'icon' => 'layui-icon-triangle-d',
            ],
            [
                'name' => '右三角',
                'icon' => 'layui-icon-triangle-r',
            ],
            [
                'name' => '文件',
                'icon' => 'layui-icon-file',
            ],
            [
                'name' => '设置-小型',
                'icon' => 'layui-icon-set-sm',
            ],
            [
                'name' => '减少-圆圈',
                'icon' => 'layui-icon-reduce-circle',
            ],
            [
                'name' => '添加-圆圈',
                'icon' => 'layui-icon-add-circle',
            ],
            [
                'name' => '404',
                'icon' => 'layui-icon-404',
            ],
            [
                'name' => '关于',
                'icon' => 'layui-icon-about',
            ],
            [
                'name' => '箭头 向上',
                'icon' => 'layui-icon-up',
            ],
            [
                'name' => '箭头 向下',
                'icon' => 'layui-icon-down',
            ],
            [
                'name' => '箭头 向左',
                'icon' => 'layui-icon-left',
            ],
            [
                'name' => '箭头 向右',
                'icon' => 'layui-icon-right',
            ],
            [
                'name' => '圆点',
                'icon' => 'layui-icon-circle-dot',
            ],
            [
                'name' => '搜索',
                'icon' => 'layui-icon-search',
            ],
            [
                'name' => '设置-实心',
                'icon' => 'layui-icon-set-fill',
            ],
            [
                'name' => '群组',
                'icon' => 'layui-icon-group',
            ],
            [
                'name' => '好友',
                'icon' => 'layui-icon-friends',
            ],
            [
                'name' => '回复 评论 实心',
                'icon' => 'layui-icon-reply-fill',
            ],
            [
                'name' => '菜单 隐身 实心',
                'icon' => 'layui-icon-menu-fill',
            ],
            [
                'name' => '记录',
                'icon' => 'layui-icon-log',
            ],
            [
                'name' => '图片-细体',
                'icon' => 'layui-icon-picture-fine',
            ],
            [
                'name' => '表情-笑-细体',
                'icon' => 'layui-icon-face-smile-fine',
            ],
            [
                'name' => '列表',
                'icon' => 'layui-icon-list',
            ],
            [
                'name' => '发布 纸飞机',
                'icon' => 'layui-icon-release',
            ],
            [
                'name' => '对 OK',
                'icon' => 'layui-icon-ok',
            ],
            [
                'name' => '帮助',
                'icon' => 'layui-icon-help',
            ],
            [
                'name' => '客服',
                'icon' => 'layui-icon-chat',
            ],
            [
                'name' => 'top 置顶',
                'icon' => 'layui-icon-top',
            ],
            [
                'name' => '收藏-空心',
                'icon' => 'layui-icon-star',
            ],
            [
                'name' => '收藏-实心',
                'icon' => 'layui-icon-star-fill',
            ],
            [
                'name' => '关闭-实心',
                'icon' => 'layui-icon-close-fill',
            ],
            [
                'name' => '关闭-空心',
                'icon' => 'layui-icon-close',
            ],
            [
                'name' => '正确',
                'icon' => 'layui-icon-ok-circle',
            ],
            [
                'name' => '添加-圆圈-细体',
                'icon' => 'layui-icon-add-circle-fine',
            ],
        ];
    }
}