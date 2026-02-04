<?php
declare(strict_types=1);

namespace woo\admin\controller;

use app\common\controller\Admin;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Db;
use woo\common\annotation\Ps;
use woo\common\Upload;

/**
 * @Ps(false,name="Index")
 */
class Index extends Admin
{
    /**
     * @Ps(false)
     * @\woo\common\annotation\Log(false)
     */
    public function index()
    {
        // 主页url
        // 特殊用户可以给admin表自行一个dashboard字段 这样可以给他单独指定主页url
        $dashboard =  (string) url($this->login['dashboard'] ?? 'dashboard');

        if (!empty($this->login['AdminGroup']) && empty($this->login['dashboard'])) {
            foreach ($this->login['AdminGroup'] as $group) {
                if (!empty($group['dashboard'])) {
                    $dashboard = (string) url($group['dashboard']);
                    break;
                }
            }
        }
        $this->assign->dashboard = $dashboard;


        $this->assign->addCss([
            '/woo/css/admin/index_pear'
        ]);
        return $this->fetch();
    }

    /**
     * 默认主页
     * @Ps(false)
     * @\woo\common\annotation\Log(false)
     */
    public function dashboard()
    {
        // 首页需要处理的数据是比较多的  自定义的数据查询也建议开启一定的缓存
        $this->assign->addCss([
            '/woo/css/admin/dashboard'
        ]);

        $this->assign->addJs([
            'admin/echarts.common.min',
            'admin/jquery.countup'
        ]);

        // 快捷方式
        $this->shortcut();

        // 运行环境
        try {
            $this->assign->operating = $this->getOperating();
        } catch (\Exception $e) {
            $this->assign->operating = [];
        }
        // 开发企业 信息在woo.about配置中 可以自行配置
        $this->assign->about = Config::get('woo.about');
        // 获取统计数据
        $this->assign->statistics = $this->getStatistics();
        // 数据分析图 -- $charts_map 中增加一个数据处理方法 再对应方法中处理自己的图形数据
        // 默认的三个,第一个是统计7日内的用户注册数据 其他2个写死的 自行修改
        // 官网 ：http://echarts.baidu.com
        // 具体配置： http://echarts.baidu.com/option.html  这里都按PHP数组配置 页面中会自动转换为JSON
        $charts_map = ['getUserCharts', 'getDemo1Charts', 'getDemo2Charts'];
        $charts = [];
        foreach ($charts_map as $action) {
            $charts[] = method_exists($this, $action) ? $this->$action() : [];
        }
        $this->assign->charts = $charts;
        return $this->fetch();
    }

    protected function getUserCharts()
    {
        if (Cache::has('user_charts_data')) {
            $cache = Cache::get('user_charts_data');
            $data = $cache['data'];
            $value = $cache['value'];
        } else {
            $now = time();
            for ($i = 6; $i >= 0; $i--) {
                $data[] = date('m-d', $now - $i * 86400);
                $start = date('Y-m-d', $now - $i * 86400);
                $end = date('Y-m-d 23:59:59', $now - $i * 86400);
                $value[] = model('User')
                    ->whereTime('create_time', 'between', [$start, $end])
                    ->count();
            }
            // 不用担心数据统计有延时 和数据库具体数据一致(手动删除数据库除外)
            Cache::tag('User')->set('user_charts_data', ['data' => $data, 'value' => $value], 86400);
        }

        return [
            'title' => [
                'text' => '最近一周新增的用户量',
                'left' => 'center',
                'top' => '10px',
                'textStyle' => [
                    'fontWeight' => 'normal',
                    'color' => !$this->assign->darkMode ? '#888' : '#b4c2c5',
                    'fontSize' => '13px'
                ]
            ],
            'grid' => [
                'top' => '40px',
                'bottom' => '30px',
                'left' => '30px',
                'right' => '20px',

            ],
            'tooltip' => [
                'trigger' => 'item',
                'formatter' => '{b}:{c}'
            ],
            'xAxis' => [
                'type' => 'category',
                'data' => $data,
                'boundaryGap'=> false,
                'splitLine' => [
                    'show' => true,
                    'lineStyle' => [
                        'color' => !$this->assign->darkMode ? '#f6ffed' : '#535c65'
                    ]
                ],
                'axisTick' => [
                    'show' => false
                ],
                'axisLine' => [
                    'lineStyle' => [
                        'color' => '#5fb878',
                        'width' => 2,
                        'opacity' => 0.8
                    ]
                ],
                'axisLabel' => [
                    'color' => !$this->assign->darkMode ? '#888' : '#b4c2c5',
                ]
            ],
            'yAxis' => [
                'type' => 'value',
                'axisTick' => [
                    'show' => false
                ],
                'splitLine' => [
                    'show' => true,
                    'lineStyle' => [
                        'color' => !$this->assign->darkMode ? '#f6ffed' : '#535c65'
                    ]
                ],
                'axisLine' => [
                    'lineStyle' => [
                        'color' => '#5fb878',
                        'width' => 2,
                        'opacity' => 0.8
                    ]
                ],
                'axisLabel' => [
                    'color' => !$this->assign->darkMode ? '#888' : '#b4c2c5',
                ]
            ],
            'series' => [
                [
                    'data' => $value,
                    'type' => 'line',
                    'smooth' => true,
                    'itemStyle' => [
                        'color' => '#5fb878'
                    ]
                ]
            ]
        ];
    }

    protected function getDemo1Charts()
    {
        return [
            'title' => [
                'text' => '虚拟统计图一',
                'left' => 'center',
                'top' => '10px',
                'textStyle' => [
                    'fontWeight' => 'normal',
                    'color' => !$this->assign->darkMode ? '#888' : '#b4c2c5',
                    'fontSize' => '13px'
                ]
            ],
            'grid' => [
                'top' => '50px',
                'bottom' => '40px',
                'left' => '40px',
                'right' => '30px',

            ],
            'tooltip' => [
                'trigger' => 'item',
                'formatter' => '{b}:{c}'
            ],
            'xAxis' => [
                'type' => 'category',
                'data' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'splitLine' => [
                    'show' => true,
                    'lineStyle' => [
                        'color' => !$this->assign->darkMode ? '#f6ffed' : '#535c65'
                    ]
                ],
                'axisTick' => [
                    'show' => false
                ],
                'axisLine' => [
                    'lineStyle' => [
                        'color' => '#5fb878',
                        'width' => 2,
                        'opacity' => 0.8
                    ]
                ],
                'axisLabel' => [
                    'color' => !$this->assign->darkMode ? '#888' : '#b4c2c5',
                ]
            ],
            'yAxis' => [
                'type' => 'value',
                'axisTick' => [
                    'show' => false
                ],
                'splitLine' => [
                    'show' => true,
                    'lineStyle' => [
                        'color' => !$this->assign->darkMode ? '#f6ffed' : '#535c65'
                    ]
                ],
                'axisLine' => [
                    'lineStyle' => [
                        'color' => '#5fb878',
                        'width' => 2,
                        'opacity' => 0.8
                    ]
                ],
                'axisLabel' => [
                    'color' => !$this->assign->darkMode ? '#888' : '#b4c2c5',
                ]
            ],
            'series' => [
                [
                    'data' => [120, 200, 150, 80, 70, 110, 130],
                    'type' => 'bar',
                    'smooth' => true,
                    'itemStyle' => [
                        'color' => '#5fb878'
                    ]
                ]
            ]
        ];
    }

    protected function getDemo2Charts()
    {
        return [
            'title' => [
                'text' => '虚拟统计图二',
                'left' => 'center',
                'top' => '10px',
                'textStyle' => [
                    'fontWeight' => 'normal',
                    'color' => !$this->assign->darkMode ? '#888' : '#b4c2c5',
                    'fontSize' => '13px'
                ]
            ],
            'grid' => [
                'top' => '50px',
                'bottom' => '40px',
                'left' => '8%',
                'right' => '8%',

            ],
            'tooltip' => [
                'trigger' => 'item',
                'formatter' => '{a}<br/>{b}:{c}({d}%)'
            ],
            'legend' => [
                'icon' => '',
                'orient' => 'vertical',
                'left' => '6%',
                'top' => '50px',
                'data' => [
                    [
                        'name' => '直接访问',
                        'icon' => 'circle'
                    ],
                    [
                        'name' => '邮件营销',
                        'icon' => 'circle'
                    ],
                    [
                        'name' => '联盟广告',
                        'icon' => 'circle'
                    ],
                    [
                        'name' => '视频广告',
                        'icon' => 'circle'
                    ],
                    [
                        'name' => '搜索引擎',
                        'icon' => 'circle'
                    ]
                ]
            ],
            'series' => [
                [
                    'name' => '访问来源',
                    'radius' => '55%',
                    'data' => [
                        ['value' => 335, 'name' => '直接访问', 'itemStyle' => ['color' => '#5fb878']],
                        ['value' => 310, 'name' => '邮件营销', 'itemStyle' => ['color' => '#fa6141']],
                        ['value' => 310, 'name' => '联盟广告', 'itemStyle' => ['color' => '#409eff']],
                        ['value' => 310, 'name' => '视频广告', 'itemStyle' => ['color' => '#FFB800']],
                        ['value' => 310, 'name' => '搜索引擎', 'itemStyle' => ['color' => '#f56c6c']]
                    ],
                    'type' => 'pie',
                    'itemStyle' => [
                        'emphasis' => [
                            'shadowBlur' => 10,
                            'shadowOffsetX' => 0,
                            'shadowColor' => 'rgba(0, 0, 0, 0.5)'
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function getStatistics()
    {

        if (Cache::has('index_Statistics_cache_' . $this->login['id'])) {
            return Cache::get('index_Statistics_cache_' . $this->login['id']);
        }
        try {
            $list = model('Statistics')
                ->where([
                    ['is_verify', '=', 1]
                ])
                ->order(model('Statistics')->getDefaultOrder())
                ->select()
                ->toArray();
            $data = [];
            if (!empty($list)) {
                foreach ($list as $item) {
                    if (!empty($item['url']) && !admin_link_power($item['url'])) {
                        continue;
                    }
                    if (!empty($item['admin_group_id']) && empty($this->login['AdminGroup'])) {
                        continue;
                    }
                    if (!empty($item['admin_group_id'])) {
                        $groups = explode(',', $item['admin_group_id']);
                        $continue = true;
                        foreach ($this->login['AdminGroup'] as $group) {
                            if (in_array($group['id'], $groups)) {
                                $continue = false;
                                break;
                            }
                        }
                        if ($continue) {
                            continue;
                        }
                    }
                    $model = get_model_name($item['model']);
                    if ($model) {
                        if (!admin_link_power($item['model'] . '/index')) {
                            continue;
                        }
                        $data[] = [
                            'title' => $item['title'],
                            'is_virtual' => 0,
                            'model' => $item['model'],
                            'url' => $item['url'],
                            'count' => isset(model($model)->form['admin_id']) && $item['is_self'] ?
                                model($model)->where('admin_id', $this->login['id'])->count() :
                                model($model)->count()
                        ];
                    } else {
                        // 假数据 前面有统计意义的模型不多  后期自行在统计中修改统计相关
                        $data[] = [
                            'title' => $item['title'],
                            'count' => 88,
                            'url' => $item['url'],
                            'is_virtual' => 1,
                            'model' => $item['model'],
                        ];
                    }
                }
            }
            Cache::tag('Statistics')->set('index_Statistics_cache_' . $this->login['id'], $data, intval(setting('admin_statistics_cache_expire')));
        } catch (\Exception $e) {
            $data = [];
        }
        return $data;
    }

    protected function getOperating()
    {
        if (Cache::has('index_operating_cache')) {
            return Cache::get('index_operating_cache');
        }
        $dev['php_version'] = PHP_VERSION;
        if (@ini_get('file_uploads')) {
            $dev['upload_max_filesize'] = ini_get('upload_max_filesize');
        } else {
            $dev['upload_max_filesize'] = '<i class="layui-icon layui-icon-close"></i>';
        }
        $dev['php_os'] = PHP_OS;
        $softArr = explode('/', $this->request->server('SERVER_SOFTWARE', 'None'));
        $dev['server_software'] = array_shift($softArr);
        $dev['server_name'] = $this->request->server('SERVER_ADDR', '0.0.0.0');
        $dev['mysql_version'] = Db::query('SELECT VERSION() AS `version`')[0]['version'] ?? '-';

        if (extension_loaded('curl')) {
            $dev['curl_extension'] = '<i class="layui-icon layui-icon-ok"></i>';
        } else {
            $dev['curl_extension'] = '<i class="layui-icon layui-icon-close"></i>';
        }
        if (extension_loaded('MBstring')) {
            $dev['mbstring_extension'] = '<i class="layui-icon layui-icon-ok"></i>';
        } else {
            $dev['mbstring_extension'] = '<i class="layui-icon layui-icon-close"></i>';
        }
        if (extension_loaded('pdo')) {
            $dev['pdo_extension'] = '<i class="layui-icon layui-icon-ok"></i>';
        } else {
            $dev['pdo_extension'] = '<i class="layui-icon layui-icon-close"></i>';
        }
        if (extension_loaded('fileinfo')) {
            $dev['fileinfo'] = '<i class="layui-icon layui-icon-ok"></i>';
        } else {
            $dev['fileinfo'] = '<i class="layui-icon layui-icon-close"></i>';
        }
        $dev['max_execution_time'] = ini_get('max_execution_time') . 'S';
        Cache::set('index_operating_cache', $dev, 86400);
        return $dev;
    }

    protected function shortcut()
    {
        if (Cache::has('index_shortcut_cache_' . $this->login['id'])) {
            $shortcut = Cache::get('index_shortcut_cache_' . $this->login['id']);
        } else {
            try {
                $list = model('Shortcut')
                    ->where([
                        ['is_verify', '=', 1]
                    ])
                    ->order(model('Shortcut')->getDefaultOrder())
                    ->select()
                    ->toArray();
                $shortcut = [];
                foreach ($list as $item) {
                    if (!empty($item['url']) && !admin_link_power($item['url'])) {
                        continue;
                    }
                    if (empty($item['admin_group_id'])) {
                        $shortcut[] = $item;
                        continue;
                    }
                    if (empty($this->login['AdminGroup'])) {
                        continue;
                    }
                    $groups = explode(',', $item['admin_group_id']);
                    foreach ($this->login['AdminGroup'] as $group) {
                       if (in_array($group['id'], $groups)) {
                           $shortcut[] = $item;
                       }
                    }
                }

                Cache::tag('Shortcut')->set('index_shortcut_cache_' . $this->login['id'], $shortcut);
            } catch (\Exception $e) {
                $shortcut = [];
            }
        }
        $this->assign->shortcut = $shortcut;
    }

    /**
     * @return string
     * @throws \Exception
     * @Ps(false,name="缩略图")
     */
    public function thumb()
    {
        $this->request->isNotStore = true;
        if (empty($this->args['src'])) {
            return $this->message('参数错误', 'error');
        }
        $thumb_path = (new Upload(['type' => 'local']))->makeThumb(
            $this->args['src'],
            $this->args['w'] ?? 0,
            $this->args['h'] ?? 0,
            $this->args['m'] ?? 0,
            $this->args['wa'] ?? false
        );
        if (!$thumb_path) {
            return $this->message('生成失败', 'error');
        }
        return image_response($thumb_path);
    }
}
