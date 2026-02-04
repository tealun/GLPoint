<?php
declare (strict_types=1);

namespace woo\admin\controller;

use app\common\controller\Admin;
use think\facade\Cache;
use think\facade\Db;
use woo\common\annotation\Forbid;
use app\common\builder\FormPage;
use woo\common\annotation\Ps;
use app\common\builder\Table;
use woo\common\builder\form\StaticIcon;
use woo\common\helper\Arr;
use woo\common\helper\Backup;
use woo\common\model\Forge;

/**
 * @Forbid(true)
 */
class Demo extends Admin
{
    /**
     * @Ps(false)
     */
    public function index()
    {}

    /**
     * @Ps(name="表单类型")
     */
    public function demo1()
    {
        // 可以在实例的时候批量赋值 和指定当前模型
        $form = new FormPage(['a1' => '张三', 'e' => '张三'], 'AdminRule');

        $form->addFormItem('json', 'json', [
            'attrs' => [
                'value' => '{"a" : "aa", "b":"bb"}',
                'data-search' => 1,
            ]
        ])->setLabelAttr('JSON对象2.3.1');

        $form->addFormItem('a5', 'password', [
            'prefix-icon' => 'layui-icon-password',
            'attrs' => [
                'lay-affix' => "eye"
            ]
        ])->setLabelAttr('密码带可见');// 2.3.0以后支持 和前后缀内容不要同时用

        $form->addFormItem('a4', 'text', [
            'attrs' => [
                'lay-affix' => "clear"
            ]

        ])->setLabelAttr('输入带清除');// 2.3.0以后支持 和前后缀内容不要同时用 textarea支持图标和清除 不支持前后缀内容 其他类型暂时均不支持

        $form->addFormItem('a3', 'text', [
            'prefix-icon' => 'layui-icon-username',
            'suffix-icon' => 'woo-icon-kuaijie',
        ])->setLabelAttr('前后缀图标');// 2.3.0以后支持  和前后缀内容不要同时用

        $form->addFormItem('k7', 'date', [
            'label' => '日期',
            'suffix-icon' => 'woo-icon-rili1',
        ]);

        $form->addFormItem('a2', 'text', [
            //'quick' => ['张三' => '张三', '李四' => '李四'],
            'prefix' => '我是前缀',
            'suffix' => '我是后缀',
        ])->setLabelAttr('前后缀内容');// 2.3.0以后支持

        $form->addFormItem('a1', 'text', [
            'quick' => ['张三' => '张三', '李四' => '李四']
        ])->setLabelAttr('输入带选择');

        $form->addFormItem('parent_id', 'relation2', [
            'foreign' => 'AdminRule',
        ])->setLabelAttr('另一种关联选择2.2.3');

        $form->addFormItem('iconpicker', 'iconpicker', [
        ])->setLabelAttr('另一种图标选择2.2.3');

        $form->addFormItem('spec', 'spec', [
            'fields' => [
                'a1' => [
                    'elem' => 'image',
                    'label' => '图片',
                ],
                'a' => [
                    'elem' => 'number',
                    'label' => '价格',
                    'tip' => '请认真填写',
                    'width' => '100'
                ],
                'b' => [
                    'elem' => 'number',
                    'label' => '数量',
                ],
                'c' => [
                    'elem' => 'number',
                    'label' => '成本价',
                ],
                'd' => [
                    'elem' => 'number',
                    'label' => '库存'
                ],
                'e' => [
                    'elem' => 'text',
                    'label' => '编号'
                ],
            ]
        ])->setLabelAttr('多规格2.1.2');

        $form->addFormItem('sort', 'sortvalues', [
            'options' => [
                'df' => '默认',
                'pt' => '拼团',
                'ms' => '秒杀',
                'kj' => '砍价',
            ],
            'background' => [
                'df' => '#ed4014',
                'pt' => '#feb900',
                'ms' => '#1e9fff',
                'kj' => '#36b368',
            ],
            'tip' => '用于内置一组选项键值对，用户来进行排序'
        ])->setLabelAttr('优先级2.1.2');

        $form->addFormItem('random2', 'random', [
            'attrs' => [
                'data-random' => 'BAT,:1|date|number:6',
                'data-repeat' => true,
                'data-auto' => true
            ],
            'is_hidden_btn' => true
        ])->setLabelAttr('单据编号');
        $form->addFormItem('random', 'random')->setLabelAttr('随机值2.1.0');
        $form->addFormItem('random1', 'random', [
            'attrs' => [
                // 支持3种内置规则number - 数字，alpha - 字母，alphaNum - 数字和字母 默认number:5
                // 也支持自定义随机字符范围
                //'data-random' => 'number:3|alpha:2|alphaNum:5|a,b,c,x,y,z:2',
                // 还可以这样写 不同随机段支持定义回调处理的方法或自定义函数 参数用-分隔
                // 如果是字符串内置方法 就直接把参数以,分隔后依次传入；如果是自定义函数就第一次函数是随机值，从第二个参数开始依次传入
                // 比如：字符串内置方法 substr-0,3   调用方式：随机值.substr(0,3) ；自定义函数 test-0,3   调用方式：test(随机值,0,3) 并且里面的this是当前点击的按钮
                //'data-random' => 'number:3|alpha:2:toLowerCase|alphaNum:5:toUpperCase:substr-0,3:test-0,3|upperAndLowerAlphaNum:20|a,b,c,x,y,z:2',
                'data-random' => 'alpha:2-4:toUpperCase|number:8|xixi,haha,heihei,jiumi:1|固定,:1',
                // 如果random有多个随机规则的分隔符 默认 空字符串
                'data-delimiter' => '-',
                // 是否允许重复
                'data-repeat' => true
            ],
            'tip' => '支持定义随机组合，支持字符随机，单词随机，固定词，数量也可以随机'
        ])->setLabelAttr('组合式随机值2.1.0');

//        $form->addFormItem('amap', 'amap')->setLabelAttr('高德地图2.0.8');

        $form->addFormItem('email', 'email', [
            'options' => ['my.com'] // 自定义更多邮箱
        ])->setLabelAttr('邮箱v2.0.5');

        $form->addFormItem('bankcard', 'bankcard')->setLabelAttr('银行卡v2.0.5');
        $form->addFormItem('ip4', 'ip4')->setLabelAttr('IP4v2.0.5');
        $form->addFormItem('ip6', 'ip6')->setLabelAttr('IP6v2.0.5');


        $form->addFormItem('aa', 'cascader', [
            'foreign' => 'Region',
            'attrs' => [
                //'data-width' => 180,// 设置每一级选项的宽度 默认159.5
                //'data-height' => 252, // 设置每一级选项的高度 默认252（建议是36的倍数）
                //'data-valuttype' => 1,// 值的存储方式 填任意值都表示 0   默认1 不需要填
                //'data-texttype' => 1,// 文本的显示方式 填任意值都表示 0  默认1 不需要填
                //'data-textseparator' => '-', //texttype为1时 多个文本直接的分割符 默认/
                //'data-nostrict' => true,// 是否是严格模式，填任意值都表示非严格模式 需要严格默认就不要设置该属性  默认就是严格模式
                //'data-url' => ''// 如果数据多 支持异步加载选项 设置接口url地址 默认空 不走异步
            ],
            'tip' => '支持异步请求'
        ])->setLabelAttr('级联选择v2.0.4');
        $form->addFormItem('aaa', 'slider', [
            'attrs' => [
                'data-theme' => '#36b368', // 主题颜色，以便用在不同的主题风格下 	string 	#36b368
                'data-type' => 'default',// 滑块类型，可选值有：default（水平滑块）、vertical（垂直滑块） 	string 	default
                'data-min' => 10,// 滑动条最小值，正整数，默认为 0 	number 	0
                'data-max' => 200,// 滑动条最大值 	number 	100
                'data-range' => true,// 是否开启滑块的范围拖拽，若设为 true，则滑块将出现两个可拖拽的环 	boolean 	false
                'data-step' => 1,// 拖动的步长 	number 	1
                'data-showstep' => false,  // showstep 	是否显示间断点 	boolean 	false
                'input-input' => false,// 是否显示输入框（注意：若 range 参数为 true 则强制无效） boolean 	true
                'height' => 200, // height 	滑动条高度，需配合 type:"vertical" 参数使用 	number 	200
                'disabled' => false //disabled 	是否将滑块禁用拖拽 	boolean 	false
            ]
        ])->setLabelAttr('滑块v2.0.4');
        $form->addFormItem('aaaa', 'rate', [
            'attrs' => [
                // 'data-theme' => '#ff0000'
            ]
        ])->setLabelAttr('评分v2.0.4');

        $form->addFormItem('aaaaa', 'transfer', [
            'foreign' => 'AdminRule',
            'attrs' => [

            ]
        ])->setLabelAttr('穿梭框v2.0.4');

        $form->addFormItem('a', 'text')->setLabelAttr('单行文本')->setItemValue('默认值');

        $form->addFormItem('b', 'number')->setLabelAttr('数字输入');
        $form->addFormItem('c', 'password', [
            'attrs' => [
                'placeholder' => '请输入密码'
            ],
            'tip' => '我有提示哦'
        ])->setLabelAttr('密码输入');
        $form->addFormItem('d', 'textarea')->setLabelAttr('多行文本');
        $form->addFormItem('e', 'radio', [
            'label' => '单选',
            'options' => ['张三' => '张三', '李四' => '李四']
        ]);
        $form->addFormItem('f', 'checkbox', [
            'label' => '多选',
            'options' => ['张三' => '张三', '李四' => '李四']
        ]);
        $form->addFormItem('g', 'select', [
            'label' => '下拉',
            'options' => ['张三' => '张三', '李四' => '李四']
        ]);
        $form->addFormItem('h', 'xmselect', [
            'label' => '下拉单选',
            'attrs' => [
                'data-max' => 1,
            ],
            'options' => ['张三' => '张三', '李四' => '李四', '张三', '张三', '张三', '张三', '张三', '张三', '张三', '张三', '张三', '张三']
        ]);
        $form->addFormItem('i', 'xmselect', [
            'label' => '下拉多选',
            'attrs' => [
                'data-max' => 3,
            ],
            'tip' => '可以自定义选择最多选择个数',
            'options' => ['张三' => '张三', '李四' => '李四', '张三', '张三', '张三', '张三', '张三', '张三', '张三', '张三', '张三', '张三']
        ]);

        $form->addFormItem('j', 'checker', [
            'label' => '是否'
        ]);
        $form->addFormItem('j1', 'checker', [
            'label' => '是否改值',
            'attrs' => [
                'title' => '对|错'
            ],
            'options' => ['yes' => 'yes', 'no' => 'no']
        ]);

        $form->addFormItem('k', 'date', [
            'label' => '日期',
        ]);
        $form->addFormItem('k1', 'datetime', [
            'label' => '日期时间'
        ]);
        $form->addFormItem('k2', 'year', [
            'label' => '年'
        ]);
        $form->addFormItem('k3', 'month', [
            'label' => '月'
        ]);
        $form->addFormItem('k4', 'time', [
            'label' => '时间'
        ]);
        $form->addFormItem('k5', 'date', [
            'label' => '日期限制',
            'attrs' => [
                'data-min' => '2020-08-01',
                'data-max' => date('Y-m-d'),
                'data-calendar' => true
            ]
        ]);
        $form->addFormItem('k6', 'date', [
            'label' => '日期范围',
            'attrs' => [
                'data-range' => '~'
            ]
        ]);
        $form->addFormItem('admin_id', 'relation', [
            'label' => '关联模型',
            'foreign' => 'Admin',
            'tip' => '支持一对多，多对多'
        ]);
        $form->addFormItem('l', 'image', [
            'label' => '单图',
            'upload' => [
                // 'maxSize' => '5'
            ]
        ]);

        $form->addFormItem('l1', 'multiimage', [
            'label' => '多图',
            'upload' => [
                // 'maxSize' => '5',
                'maxLength' => 5
            ]
        ]);
        $form->addFormItem('l2', 'file', [
            'label' => '单文件',
            'upload' => [
                // 'maxSize' => '5'
            ]
        ]);
        $form->addFormItem('l3', 'multifile', [
            'label' => '多文件',
            'upload' => [
                // 'maxSize' => '5',
                'maxLength' => 5
            ]
        ]);
        $form->addFormItem('m', 'array', [
            'label' => '数组'
        ]);
        $form->addFormItem('m1', 'keyvalue', [
            'label' => '键值对'
        ]);
        $form->addFormItem('o', 'xmtree', [
            'label' => '父级选择',
            'tip' => '支持一对多，多对多'
        ]);
        $form->addFormItem('o1', 'xmselectfortree', [
            'label' => '父级选择'
        ]);
        $form->addFormItem('p', 'color', [
            'label' => '取色器'
        ]);
        $form->addFormItem('q', 'icon', [
            'label' => '图标'
        ]);
        $form->addFormItem('q1', 'tag', [
            'label' => '标签'
        ]);
        $form->addFormItem('r', 'multiattrs', [
            'label' => '多属性',
            'fields' => [
                'a' => [
                    'elem' => 'text',
                    'label' => '名称'
                ],
                'b' => [
                    'elem' => 'number',
                    'label' => '价格'
                ],
                'c' => [
                    'elem' => 'radio',
                    'label' => '类型',
                    'options' => ['张三', '李四']
                ]
            ]
        ]);
        $form->addFormItem('s', 'ueditor', [
            'label' => '富文本',
            'attrs' => [
                'data-height' => 320 // 自定义高度
            ]
        ]);
        $form->addFormItem('captcha', 'captcha', [
            'label' => '验证码'
        ]);
        $this->assign->form = $form;
        $this->local['header_title'] = '表单类型';
        $this->local['header_tip'] = '内置表单项不够，支持自定义';
        return $this->fetch('form');
    }

    /**
     * @Ps(name="调拨单录入")
     */
    public function order()
    {
        $form = new FormPage(['f' => '0.00'], 'Admin');
        $form->addFormItem('admin_id', 'relation2', [
            'label' => '出库单位',
            'foreign' => 'Admin',
        ]);

        $form->addFormItem('admin_group_id', 'relation2', [
            'label' => '入库单位',
            'foreign' => 'AdminGroup',
        ]);
        $form->addFormItem('user_id', 'select', [
            'label' => '经手人',
            'options' => [
                '张三' => '张三',
                '李四' => '李四',
                '王五' => '王五'
            ]
        ]);
        $form->addFormItem('a', 'date', [
            'label' => '出库日期',
            'attrs' => [
                'data-max' => date('Y-m-d')
            ]
        ]);
        $form->addFormItem('b', 'random', [
            'attrs' => [
                'data-random' => 'BAT,:1|date|number:6',
                'data-repeat' => true,
                'data-auto' => true
            ],
            'is_hidden_btn' => true
        ])->setLabelAttr('单据编号');

        $form->addFormItem('detail', 'orderitem', [
            'foreign' => 'Admin', // 乱写的一个
            'attrs' => [
                // 监听行字段改变 同步修改同行其他字段行
                'data-watch' => [
                    // num字段change以后 money和money2进行修改 后面的修改支持layui模板语法 会把整行数据传给你 也支持自定义函数
                    'num' => [
                        'money' => "{{(parseInt(d.num || 0) * parseFloat(d.price || 0)).toFixed(2)}}", // 支持函数名
                        'money2' => "{{(parseInt(d.num || 0) * parseFloat(d.sell_price || 0)).toFixed(2)}}"
                    ],
                    'price' => [
                        'money' => "{{(parseInt(d.num || 0) * parseFloat(d.price || 0)).toFixed(2)}}"
                    ],
                    'sell_price' => [
                        'money2' => "{{(parseInt(d.num || 0) * parseFloat(d.sell_price || 0)).toFixed(2)}}"
                    ],
                    'money' => [
                        'price' => "{{(parseFloat(d.money || 0) / parseInt(d.num || 1)).toFixed(2)}}"
                    ],
                    'money2' => [
                        'sell_price' => "{{(parseFloat(d.money || 0) / parseInt(d.num || 1)).toFixed(2)}}"
                    ]
                ],
                // 监听列change以后 底部行统计
                'data-counter' => [
                    'num' => [
                        'type' => 'callback', //统计方式支持： count（计数） max（最大） min（最小） avg（平均） sum（和） callback(自定义回调 也会把统计结果都给你 只是返回值自定义显示结果；你可以返回空字符串 获取到统计结果把接口输出到其他字段中)
                        'callback' => 'aaaaa',// 当type是callback是自定义回调函数名 你要自己去js定义该函数做业务处理
                        'name' => '和:',// 为空字符串 就值显示统计结果
                        'default' => '0'// 初始化的默认显示 不定义就不显示
                    ],
                    'price' => [
                        'type' => 'avg',
                        'name' => '均价:',
                        'default' => '0.00'
                    ],
                    'sell_price' => [
                        'type' => 'avg',
                        'default' => '0.00'
                    ],
                    'money' => [
                        'type' => 'callback',
                        'callback' => 'bbbbb',
                        'name' => '',
                        'default' => '0.00'
                    ],
                    'money2' => [
                        'type' => 'sum',
                        'name' => '',
                        'default' => '0.00'
                    ],
                ]
            ],
            'fields' => [
                'test_product_id' =>  [
                    'field' => 'test_product_id',// 真实模型环境 不写
                    'name' => '产品',
                    'tip' => '输入编号，敲回车试试',
                    'cname' => '产品',// 真实模型环境 不写
                    'display' => 'number',
                    'elem' => 'relation',
                    'foreign' => 'TestProduct',
                    'href' => (string)url( 'TestProduct/index2'),// 真实模型环境 不写 自动处理
                    'withName' => json_encode(['number', 'title']), // 真实模型环境 直接写数组 用于设置选择以后显示的字段组合 底层会转换json
                    'withValue' => json_encode(['unit' => 'unit', 'num' => 1, 'price' => 'price', 'sell_price' => 'sell_price']),
                    //真实模型环境 直接写数组 用于选择产品以后 自动填充其他字段数据 前面是当前明细字段 后面是产品表字段 也可以填写具体值 后layui模板引擎语法
                    'width' => 320 // 宽度不写 默认100
                ],
                'unit' => [
                    'field' => 'unit',// 真实模型环境 不写
                    'name' => '单位',
                    'elem' => 'text',
                    'width' => 100
                ],
                'num' => [
                    'field' => 'num',// 真实模型环境 不写
                    'name' => '数量',
                    'elem' => 'text',
                    'width' => 110,
                    'attrs' => [
                        'data-type' => 'integer' // 只能输入整数  设置float 就是小数
                    ]
                ],

//                'aa' => [
//                    'field' => 'aa',// 真实模型环境 不写
//                    'name' => '单位',
//                    'elem' => 'text',// 表单类型 非原生系统表单类型 单独js封装的表单组件 目前只支持relation和text  更多的后期扩展 或自行扩展
//                    'width' => 120,
//                    'options' => [ // 带options 就会显示下拉
//                        '1' => '仓库一',
//                        '2' => '仓库二',
//                        '3' => '仓库三',
//                    ],
//                    'attrs' => [
//                        'readonly' => 'readonly', // 只允许选择了 不可以手改
//                        //'data-max' => 2
//                    ]
//                ],

                'price' => [
                    'field' => 'price',// 真实模型环境 不写
                    'name' => '成本价',
                    'elem' => 'text',
                    'width' => 100,
                    'attrs' => [
                        'data-type' => 'float' // 只能输入整数  设置float 就是小数
                    ]
                ],
                'sell_price' => [
                    'field' => 'sell_price',// 真实模型环境 不写
                    'name' => '销售价',
                    'elem' => 'text',
                    'width' => 100,
                    'attrs' => [
                        'data-type' => 'float' // 只能输入整数  设置float 就是小数
                    ]
                ],
                'money' => [
                    'field' => 'money',// 真实模型环境 不写
                    'name' => '总成本价',
                    'elem' => 'text',
                    'width' => 100,
                    'attrs' => [
                        'data-type' => 'integer' // 只能输入整数  设置float 就是小数
                    ]
                ],
                'money2' => [
                    'field' => 'money2',// 真实模型环境 不写
                    'name' => '总销售价',
                    'elem' => 'text',
                    'width' => 100,
                    'attrs' => [
                        'data-type' => 'integer', // 只能输入整数  设置float 就是小数
                        'readonly' => 'readonly'
                    ]
                ],
                'remark' => [
                    'field' => 'remark',// 真实模型环境 不写
                    'name' => '备注',
                    'elem' => 'text',
                    'width' => 150,
                ],
            ]
        ])->setLabelAttr('产品明细');

        $form->addFormItem('c', 'textarea', [
            'attrs' => [
                'placeholder' => '请填写备注信息'
            ]
        ])->setLabelAttr('备注');
        $form->addFormItem('d', 'text', [
            'attrs' => [
                'data-type' => 'int'
            ]
        ])->setLabelAttr('调拨总数');
        $form->addFormItem('e', 'text', [
            'attrs' => [
                'data-type' => 'float',
                'readonly' => 'readonly'
            ]
        ])->setLabelAttr('调拨金额');

        $form->addFormItem('f', 'text', [
            'attrs' => [
                'data-type' => 'float'
            ]
        ])->setLabelAttr('已付金额');

        $this->assign->form = $form;
        if ($this->request->isPost()) {

        }
        $this->local['header_title'] = '调拨单录入';
        $this->local['header_tip'] = '演示类似于进销存、ERP中特殊表单中的单据录入；整个明细录入中几乎不用动鼠标即可完成；演示完全自由控制表单html结构';
        return $this->fetch('order');
    }


    /**
     * @Ps(name="表单分组")
     */
    public function demo2()
    {
        $form = new FormPage([], 'AdminRule');
        $form->addFormItem('a', 'text')->setLabelAttr('项目一');
        $form->addFormItem('b', 'text')->setLabelAttr('项目二');
        $form->addFormItem('c', 'text')->setLabelAttr('项目三');
        $form->addFormItem('d', 'text')->setLabelAttr('项目四');
        $form->addFormItem('e', 'text')->setLabelAttr('项目五');
        $form->addFormItem('f', 'text')->setLabelAttr('项目六');
        $form->setTab('gaoji', '高级信息');
        $form->setTab('basic', '基本信息');
        $form->switchTab('gaoji')->setTabItems(['d', 'e', 'f']);

        if ($this->request->isPost()) {
            $form->forceError([
                'a' => '项目一输入错误',
                'b' => '项目二输入错误',
            ]);
        }

        $this->assign->form = $form;
        $this->local['header_title'] = '表单分组和错误提示';
        $this->local['header_tip'] = '按住ALT+Q可以快速切换Tab';
        return $this->fetch('form');
    }

    /**
     * @Ps(name="表单触发")
     */
    public function demo3()
    {
        $form = new FormPage([], 'AdminRule');
        $form->addFormItem('a', 'select', [
            'options' => [
                '1' => '项目四',
                '2' => '项目五',
                '3' => '项目五和项目六'
            ]
        ])->setLabelAttr('触发项');
        $form->addFormItem('b', 'text')->setLabelAttr('项目二');
        $form->addFormItem('c', 'text')->setLabelAttr('项目三');
        $form->addFormItem('d', 'text')->setLabelAttr('项目四');
        $form->addFormItem('e', 'text')->setLabelAttr('项目五');
        $form->addFormItem('f', 'text')->setLabelAttr('项目六');

        $form->addTrigger('a', ['1' => 'd', '2' => 'e', '3' => 'e|f']);

        $this->assign->form = $form;
        $this->local['header_title'] = '表单触发';
        return $this->fetch('form');
    }

    /**
     * @Ps(name="表单布局")
     */
    public function demo4()
    {
        $form = new FormPage([], 'AdminRule');
        $form->addFormItem('a', 'text')->setLabelAttr('产品标题');
        $form->addFormItem('b', 'text')->setLabelAttr('副标题');
        $form->addFormItem('c', 'xmtree')->setLabelAttr('分类');
        $form->addFormItem('d', 'tag')->setLabelAttr('标签');
        $form->addFormItem('e', 'textarea', [
            'attrs' => [
                'style' => 'max-width:100%;'
            ]
        ])->setLabelAttr('简介');
        $form->addFormItem('f', 'file', [
            'upload' => [
                'validExt' => 'mp4',
                'maxSize' => '10'
            ]
        ])->setLabelAttr('主图视频');
        $form->addFormItem('g', 'image', [
            'upload' => [
                'validExt' => 'png|jpg|gif|jpeg',
                'maxSize' => '10'
            ]
        ])->setLabelAttr('轮播图片');
        $form->addFormItem('h', 'multiimage', [
            'upload' => [
                'validExt' => 'png|jpg|gif|jpeg',
                'maxSize' => '10',
                'maxLength' => 5
            ]
        ])->setLabelAttr('封面图片');
        $form->addFormItem('j', 'multiattrs', [
            'label' => '规格',
            'fields' => [
                'a' => [
                    'elem' => 'image',
                    'label' => '图片',
                    'upload' => [
                        'validExt' => 'png|jpg|gif|jpeg',
                        'maxSize' => '10'
                    ]
                ],
                'b' => [
                    'elem' => 'number',
                    'label' => '售价'
                ],
                'c' => [
                    'elem' => 'number',
                    'label' => '成本价'
                ],
                'd' => [
                    'elem' => 'number',
                    'label' => '原价'
                ],
                'e' => [
                    'elem' => 'number',
                    'label' => '库存'
                ],
                'f' => [
                    'elem' => 'text',
                    'label' => '编号'
                ]
            ]
        ]);
        $form->addFormItem('k', 'select', [
            'label' => '运费模板',
            'options' => ['送货上门' =>'送货上门', '自取' => '自取']
        ]);
        $form->addFormItem('l', 'radio', [
            'label' => '商品状态',
            'options' => ['下架', '上架']
        ]);
        $form->addFormItem('m', 'ckeditor', [
            'label' => '内容'
        ]);
        $form->addFormItem('n1', 'number')->setLabelAttr('库存');
        $form->addFormItem('n2', 'number')->setLabelAttr('销量');
        $form->addFormItem('n3', 'number')->setLabelAttr('积分');
        $form->addFormItem('n4', 'number')->setLabelAttr('排序');

        $form->addFormItem('p1', 'checker')->setLabelAttr('热卖');
        $form->addFormItem('p2', 'checker')->setLabelAttr('促销');
        $form->addFormItem('p3', 'checker')->setLabelAttr('推荐');
        $form->addFormItem('p4', 'checker')->setLabelAttr('精品');


        $form->addFormItem('q1', 'textarea')->setLabelAttr('SEO关键词');
        $form->addFormItem('q2', 'textarea')->setLabelAttr('SEO描述');

        $form->addFormItem('x0', 'text', [
            'attrs' => [
                'style' => 'max-width:100%;'
            ]
        ])->setLabelAttr('测试0')->setItemValue(12);
        $form->addFormItem('x1', 'text')->setLabelAttr('测试1')->setItemValue(4);
        $form->addFormItem('x2', 'text')->setLabelAttr('测试2')->setItemValue(4);
        $form->addFormItem('x3', 'text')->setLabelAttr('测试3')->setItemValue(4);
        $form->addFormItem('x4', 'text')->setLabelAttr('测试4')->setItemValue(5);
        $form->addFormItem('x5', 'text')->setLabelAttr('测试5')->setItemValue(4);
        $form->addFormItem('x6', 'text')->setLabelAttr('测试6')->setItemValue(3);
        $form->addFormItem('x7', 'text')->setLabelAttr('测试7')->setItemValue(6);
        $form->addFormItem('x8', 'text')->setLabelAttr('测试8')->setItemValue(6);
        $form->addFormItem('x9', 'text')->setLabelAttr('测试9')->setItemValue(3);
        $form->addFormItem('x10', 'text')->setLabelAttr('测试10');

        $form->setTab('basic', '基本信息');
        $form->setTab('content', '商品详情');
        $form->setTab('seo', 'SEO设置');
        $form->setTab('gridtest', '栅格测试');

        $form->switchTab('basic')->setGrid('a', '', 7, [
            ['a', 'b'],
            'd',
            'e',
            '上传' => [
                'f',
                'g',
                'h',
            ],
            '规格' => [
                'j' => [
                    'is_not_label' => true
                ]
            ]
        ])->setGrid('b', '基本信息', 5, [
            'c',
            'k',
            'l',
            '数量设置' => [
                ['n1','n2'],
                ['n3','n4']
            ],
            '标识设置' => [
                ['p1','p2'],
                ['p3','p4']
            ]
        ])
            ->switchTab('content')->setTabItems(['m' => [
                'is_not_label' => true
            ]])
            ->switchTab('seo')->setTabItems(['q1', 'q2'])
            ->switchTab('gridtest')->setGrid('c', '栅格占8', 8, [
                'x0',
                [
                    'x7', 'x8'
                ],
                [
                    'x1', 'x2', 'x3'
                ],

                [
                    'x9', 'x9', 'x9', 'x9'
                ],
                [
                    'x4' => [
                        // 支持不同屏幕响应式
                        'grid' => [
                            'lg' => 5,
                            'md' => 5,
                            'sm' => 5,
                            'xs' => 12
                        ],
                    ],
                    'x5' => [
                        'grid' => [
                            'lg' => 4,
                            'md' => 4,
                            'sm' => 4,
                            'xs' => 12
                        ],
                    ],
                    'x6' => [
                        'grid' => [
                            'lg' => 3,
                            'md' => 3,
                            'sm' => 3,
                            'xs' => 12
                        ],
                    ]
                ]

            ])->setGrid('d', '栅格占4', 4, [
                'x10', 'x10', 'x10', 'x10', 'x10'
            ])->setGrid('e', '栅格占12', 12, [
                'j' => [
                    'is_not_label' => true
                ]
            ]);

        $this->assign->form = $form;
        $this->local['header_title'] = '表单布局';
        $this->local['header_tip'] = '可以根据自身情况定制出复杂布局的表单页面';
        return $this->fetch('form');
    }

    /**
     * @Ps(name="自定义页")
     */
    public function demo5()
    {
        $this->setHeaderInfo('title', '自定义标题');
        $this->setHeaderInfo('ex_title', '自定义副标题');
        $this->setHeaderInfo('ex_title_href', '副标题链接');
        $this->setHeaderInfo('tip', '自定义网页提示语');
        //$this->addAction('随意的唯一标识', '按钮名', 'URL地址', '类名自定义类名；btn-0到btn-17设置按钮样式', '图标', 排序权重, JS函数名（然后自定义对应的函数名，默认false）);
        $this->addAction('mybtn1', '自定义按钮', (string)url('admin/index'), 'btn-8', 'layui-icon-gift', 10, 'myjsfunc');
        $this->addAction('mybtn2', '按钮', '', 'btn-5');
        $this->addAction('mybtn3', '按钮', '', 'btn-11');
        // 渲染返回顶部
        $this->local['topBar'] = true;
        // 网页水印
        $this->local['watermark'] = '水印测试';
        return $this->fetch('demo5');
    }

    /**
     * @Ps(name="扩展图标")
     */
    public function icon()
    {
        $this->setHeaderInfo('title', '扩展图标');

        // 获取图标列表
        $this->assign->icons = StaticIcon::getIcon();
        // 渲染返回顶部
        $this->local['topBar'] = true;
        return $this->fetch();
    }

    /**
     * @Ps(name="表格功能")
     */
    public function demo6()
    {
        $tableTab = [
            'basic' => [
                'model' => 'User',
                'title' => '会员',
                'siderbar' => [
                    [
                        'foreign' => 'UserGroup'
                    ],
                    [
                        'foreign' => 'UserGrade'
                    ],
                ],
                'total_row' => [
                    [
                        'field' => 'id',
                        'row_text' => '合计：',
                        'total_row' => '',
                        'callback' => '',
                        'templet' => '',
                    ],
                    [
                        'field' => 'money',
                        'row_text' => '',
                        'total_row' => 'max',
                        'callback' => '',
                        'templet' => '最大:{{d.TOTAL_NUMS}}',
                    ]
                ],
                'counter' => [
                    [
                        'field' => '',
                        'title' => '今日注册',
                        'type' => '',
                        'where_type' => 'callback',
                        'where' => '',
                        'callback' => 'getTodayRegisterNumber',
                        'templet' => '',
                        'more' => '',
                    ],
                    [
                        'field' => 'id',
                        'title' => '今日生日',
                        'type' => '',
                        'where_type' => 'callback',
                        'where' => '',
                        'callback' => 'getTodayBirthdayNumber',
                        'templet' => '',
                        'more' => '',
                    ],
                    [
                        'field' => '',
                        'title' => '今日登录人数',
                        'type' => '',
                        'where_type' => 'callback',
                        'where' => '',
                        'callback' => 'getTodayLoginNumber',
                        'templet' => '',
                        'more' => '',
                    ],
                    [
                        'field' => '',
                        'title' => '今日充值',
                        'type' => '',
                        'where_type' => 'callback',
                        'where' => '',
                        'callback' => 'getTodayRechargeSum',
                        'templet' => '',
                        'more' => '',
                    ],
                ],
                'list_fields' => [
                    'id',
                    'username',
                    'user_group_id',
                    'money',
                    'is_bind_mobile',
                    'mobile',
                    'a' => [
                        //'title' => '合并列',
                        'width' => 160,
                        'templet' => 'merge',
                        'merge_fields' => [
                            'nickname' => [
                                'templet' => 'show'
                            ],
                            'truename'
                        ]
                    ],
                    'create_time',
                ],
                'tool_bar' => [
                    [
                        'name' => 'btn1',
                        'title' => '新增',
                        'sort' => 30,
                        'icon' => 'layui-icon-add-1',
                        'class' => 'woo-layer-load woo-theme-btn',
                        'url' => (string) url('user/create'),
                        'power'=> 'User/create'
                    ],
                    [
                        'name' => 'btn2',
                        'title' => '按钮二',
                        'sort' => 11,
                        'icon' => 'layui-icon-delete',
                        'class' => 'btn-4',
                        'check' => true
                    ],
                    [
                        'name' => 'btn3',
                        'title' => '更多',
                        'sort' => 10,
                        'class' => 'btn-12',
                        'children' => [
                            [
                                'name' => 'btn4',
                                'title' => '下拉一',
                                'class' => ''
                            ],
                            [
                                'name' => 'btn5',
                                'title' => '下拉二',
                                'class' => ''
                            ],
                            [
                                'name' => 'btn5',
                                'title' => '下拉三',
                                'class' => ''
                            ],
                        ]
                    ]
                ],
                'item_tool_bar' => [
                    [
                        'name' => 'btn2',
                        'title' => '',
                        'sort' => 30,
                        'class' => 'btn-22 woo-layer-load',
                        'icon' => 'layui-icon-edit',
                        'url' => (string)url('user/modify', ['id' => '{{d.id}}']),
                        'power' => 'User/modify',
                        'hover' => '修改'
                    ],
                    [
                        'name' => 'btn3',
                        'title' => '',
                        'sort' => 20,
                        'class' => 'btn-25',
                        'icon' => 'layui-icon-delete',
                        'url' => (string)url('user/delete', ['id' => '{{d.id}}']),
                        'power' => 'User/delete',
                        'where' => '{{d.id}} > 1',
                        'hover' => '删除',
                        'js_func' => 'woo_delete',
                        'tip' => '提示信息是可以自定义的！'
                    ],
                    [
                        'name' => 'btn1',
                        'title' => '更多',
                        'sort' => 10,
                        'class' => 'btn-35',
                        'children' => [
                            [
                                'name' => 'btn4',
                                'title' => '下拉一',
                                'class' => ''
                            ],
                            [
                                'name' => 'btn5',
                                'title' => '下拉二',
                                'class' => ''
                            ],
                            [
                                'name' => 'btn5',
                                'title' => '下拉三',
                                'class' => ''
                            ],
                        ]

                    ],

                ]
            ],
            'group' => [
                'model' => 'UserGroup',
                'title' => '会员组'
            ],
            'userlogin' => [
                'model' => 'UserLogin',
                'title' => '登录日志'
            ]
        ];

        $table = new Table('Admin', $tableTab);
        if ($this->request->isAjax()) {
            return $table->getData();
        }
        $this->assign->table = $table;
        $this->local['header_title'] = '主要功能';
        $this->setHeaderInfo('tip', '表格：字段、按钮(可自定义条件等)、侧边栏、搜索、统计、列合计、多(单)选、拖拽排序、排序、模型关联...全无代码生成，单元格可自定义模板和属性...还有更多...');
        return $this->fetch('list');
    }

    /**
     * @Ps(name="表格按钮")
     */
    public function demo63()
    {
        $tableTab = [
            'basic' => [
                'model' => 'User',
                'title' => '会员',
                'siderbar' => [
                    [
                        'foreign' => 'UserGroup'
                    ],
                    [
                        'foreign' => 'UserGrade'
                    ],
                ],
                'total_row' => [
                    [
                        'field' => 'id',
                        'row_text' => '合计：',
                        'total_row' => '',
                        'callback' => '',
                        'templet' => '',
                    ],
                    [
                        'field' => 'money',
                        'row_text' => '',
                        'total_row' => 'max',
                        'callback' => '',
                        'templet' => '最大:{{d.TOTAL_NUMS}}',
                    ]
                ],
                'counter' => [
                    [
                        'field' => '',
                        'title' => '今日注册',
                        'type' => '',
                        'where_type' => 'callback',
                        'where' => '',
                        'callback' => 'getTodayRegisterNumber',
                        'templet' => '',
                        'more' => '',
                    ],
                    [
                        'field' => 'id',
                        'title' => '今日生日',
                        'type' => '',
                        'where_type' => 'callback',
                        'where' => '',
                        'callback' => 'getTodayBirthdayNumber',
                        'templet' => '',
                        'more' => '',
                    ],
                    [
                        'field' => '',
                        'title' => '今日登录人数',
                        'type' => '',
                        'where_type' => 'callback',
                        'where' => '',
                        'callback' => 'getTodayLoginNumber',
                        'templet' => '',
                        'more' => '',
                    ],
                    [
                        'field' => '',
                        'title' => '今日充值',
                        'type' => '',
                        'where_type' => 'callback',
                        'where' => '',
                        'callback' => 'getTodayRechargeSum',
                        'templet' => '',
                        'more' => '',
                    ],
                ],
                'list_fields' => [
                    'id',
                    'username',
                    'user_group_id',
                    'money',
                    'is_bind_mobile',
                    'mobile',
                    'a' => [
                        //'title' => '合并列',
                        'width' => 160,
                        'templet' => 'merge',
                        'merge_fields' => [
                            'nickname' => [
                                'templet' => 'show'
                            ],
                            'truename'
                        ]
                    ],
                    'create_time',
                ],
                'tool_bar' => [
                    [
                        'name' => 'btn1',
                        'title' => '新增',
                        'sort' => 30,
                        'icon' => 'layui-icon-add-1',
                        'class' => 'woo-layer-load woo-theme-btn',
                        'url' => (string) url('user/create'),
                        'power'=> 'User/create'
                    ],
                    [
                        'name' => 'btn2',
                        'title' => '按钮二',
                        'sort' => 11,
                        'icon' => 'layui-icon-delete',
                        'class' => 'btn-4',
                        'check' => true
                    ],
                    [
                        'name' => 'btn3',
                        'title' => '更多',
                        'sort' => 10,
                        'class' => 'btn-12',
                        'children' => [
                            [
                                'name' => 'btn4',
                                'title' => '下拉一',
                                'class' => ''
                            ],
                            [
                                'name' => 'btn5',
                                'title' => '下拉二',
                                'class' => ''
                            ],
                            [
                                'name' => 'btn5',
                                'title' => '下拉三',
                                'class' => ''
                            ],
                        ]
                    ]
                ],
                'item_tool_bar' => [
                    [
                        'name' => 'btn2',
                        'title' => '',
                        'sort' => 30,
                        'class' => 'btn-22 woo-layer-load',
                        'icon' => 'layui-icon-edit',
                        'url' => (string)url('user/modify', ['id' => '{{d.id}}']),
                        'power' => 'User/modify',
                        'hover' => '修改'
                    ],
                    [
                        'name' => 'btn3',
                        'title' => '',
                        'sort' => 20,
                        'class' => 'btn-25',
                        'icon' => 'layui-icon-delete',
                        'url' => (string)url('user/delete', ['id' => '{{d.id}}']),
                        'power' => 'User/delete',
                        'where' => '{{d.id}} > 1',
                        'hover' => '删除',
                        'js_func' => 'woo_delete',
                        'tip' => '提示信息是可以自定义的！'
                    ],
                    [
                        'name' => 'btn1',
                        'title' => '更多',
                        'sort' => 10,
                        'class' => 'btn-35',
                        'children' => [
                            [
                                'name' => 'btn4',
                                'title' => '下拉一',
                                'class' => ''
                            ],
                            [
                                'name' => 'btn5',
                                'title' => '下拉二',
                                'class' => ''
                            ],
                            [
                                'name' => 'btn5',
                                'title' => '下拉三',
                                'class' => ''
                            ],
                        ]

                    ],
                ],
                'toolbar_options' => [
                    'itemToolbarStyle' => 'button', // 单独指定列表项按钮的显示方式 值：button text  text_icon
                    'itemToolbarTextClassName' => 'woo-theme-color' // text  text_ion时有效 设置按钮类名 你可以再根据该类名去设置按钮的样式和颜色等 woo-theme-color可以自动跟随主题色
                ]
            ]
        ];

        $table = new Table('Admin', $tableTab);
        if ($this->request->isAjax()) {
            return $table->getData();
        }
        $this->assign->table = $table;
        $this->local['header_title'] = '表格按钮';
        $this->setHeaderInfo('tip', '列表项按钮可在"系统设置"中全局配置显示方式，也可每个模型单独指定显示方式');
        return $this->fetch('list');
    }

    /**
     * @Ps(name="订单模板")
     */
    public function demo61()
    {
        $tableTab = [
            'basic' => [
                'model' => 'AdminRule',
                'title' => '测试',
//                'list_fields' => [
//                    'id'
//                ],
                'custom' => [
                    'headerSelector' => 'customOrderHeader',
                    'selector' => 'customOrderBody',
                    'footerSelector' => 'customOrderFooter'
                ],
                'tool_bar' => [
                    [
                        'name' => 'btn1',
                        'title' => '按钮一',
                        'sort' => 30,
                        'icon' => 'layui-icon-add-1'
                    ],
                    [
                        'name' => 'btn2',
                        'title' => '按钮二',
                        'sort' => 11,
                        'class' => 'btn-11'
                    ],
                    [
                        'name' => 'btn3',
                        'title' => '更多操作',
                        'sort' => 10,
                        'class' => 'btn-12',
                        'children' => [
                            [
                                'name' => 'btn4',
                                'title' => '下拉一',
                                'class' => ''
                            ],
                            [
                                'name' => 'btn5',
                                'title' => '下拉二',
                                'class' => ''
                            ],
                            [
                                'name' => 'btn5',
                                'title' => '下拉三',
                                'class' => ''
                            ],
                        ]
                    ]
                ],
                'item_tool_bar' => [
                    [
                        'name' => 'btn1',
                        'title' => '修改订单',
                        'sort' => 30,
                        'class' => 'btn-22'
                    ],
                    [
                        'name' => 'btn2',
                        'title' => '取消订单',
                        'sort' => 20,
                        'class' => 'btn-25'
                    ],
                    [
                        'name' => 'btn3',
                        'title' => '物流信息',
                        'sort' => 20,
                        'class' => 'btn-23'
                    ],
                    [
                        'name' => 'btn4',
                        'title' => '备注',
                        'sort' => 10,
                        'class' => 'btn-35'
                    ]
                ]
            ],
        ];

        $table = new Table('AdminRule', $tableTab);
        if ($this->request->isAjax()) {
            return $table->getData();
        }
        $this->assign->table = $table;
        $this->setHeaderInfo('tip', '列表针对特殊情况，可以自定义布局，并非只限于表格，让列表更多样化和美观。');
        $this->local['header_title'] = $this->local['header_title'] ?? '自定义列表-订单列表';
        $this->local['topBar'] = true;
        return $this->fetch($this->local['fetch'] ?? 'custom-list-1');
    }

    /**
     * @Ps(name="图文模板")
     */
    public function demo62()
    {
        $this->local['fetch'] = 'custom-list-2';
        $this->local['header_title'] = '自定义列表-图文列表';
        return $this->demo61();
    }

    /**
     * @Ps(name="静态数据")
     */
    public function demo7()
    {
        // 数据表结构
        $data = Db::query("SHOW TABLE STATUS LIKE '" . get_db_config('prefix') ."%'");
        Cache::set('woo_table_list', array_values(Arr::combine($data, 'Name', 'Name')), 60);

        // Table构建器比较依赖模型，所有Forge是一个假模型（没有继承核心模型）
        $basic = new Forge();
        // 给假模型注入字段信息，同时也是table字段信息
        $basic->setTableFields([
            // 你自己的字段名
            'Name' => [
                'name' => '表名',
                'list' => [
                    'templet' => 'show',// 演示方式 更多参考手册
                ],
            ],
            'Rows' => [
                'name' => '记录条数',
                'list' => [
                    'templet' => 'show',
                    'sort' => true
                ],
            ],
            'Data_length' => [
                'name' => '数据大小',
                'list' => [
                    'templet' => 'filesize',
                    'sort' => true
                ],
            ],
            'Index_length' => [
                'name' => '索引大小',
                'list' => [
                    'templet' => 'filesize',
                    'sort' => true
                ],
            ],
            'Engine' => [
                'name' => '引擎',
                'list' => 'show'
            ],
            'Collation' => [
                'name' => '字符集',
                'list' => 'show'
            ],
            'Comment' => [
                'name' => '表注释',
                'list' => 'show'
            ],
            'Create_time' => [
                'name' => '创建时间',
                'list' => [
                    'templet' => 'date',
                    'sort' => true,
                    'style' => 'color:#888;'
                ],
            ],
        ])->setPk('Name')->setDisplay('Name');// 设置假"主键"和"主显"字段

        // 另外一个tab的表格 你要多tab 你就继续new
        $file = new Forge();
        $file->setTableFields([
            'name' => [
                'name' => '文件名',
                'list' => 'show'
            ],
            'size' => [
                'name' => '大小',
                'list' => 'filesize'
            ],
            'compress' => [
                'name' => '压缩',
                'list' => 'show'
            ],
            'time' => [
                'name' => '日期',
                'list' => 'datetime'
            ]
        ])->setPk('time')->setDisplay('name');
        $db = new Backup();
        $filelist = array_reverse(array_values($db->fileList()));

        $tableTab = [
            'basic' => [
                'title' => '数据结构',
                'table' => [
                    // table信息 你根据你自己的来了
                    'data' => $data,// 你自己的静态数据 可以写死、其他地方得来
                    'limit' => 50,
                    'page' => count($data) <= 50 ? false : true
                ],
                // 头部工具按钮
                'tool_bar' => [
                    [
                        'name' => 'backup',
                        'title' => '测试',
                        'sort' => 10,
                        'js_func' => '',
                        'icon' => 'woo-icon-beifen',
                        'class' => 'btn-2',
                        'check' => true
                    ]
                ],
                'item_tool_bar' => [
                    [
                        'name' => 'optimize',
                        'title' => '',
                        'sort' => 10,
                        'js_func' => '',
                        'icon' => 'woo-icon-youhua',
                        'hover' => '测试1',
                        'class' => 'btn-22',
                    ],
                    [
                        'name' => 'repair',
                        'title' => '',
                        'sort' => 20,
                        'js_func' => '',
                        'icon' => 'woo-icon-xiufu',
                        'hover' => '测试2',
                        'class' => 'btn-27',
                    ]
                ]
            ],
            'file' => [
                'model' => $file,
                'title' => '备份文件',
                'table' => [
                    'data' => $filelist,
                    'limit' => 10,
                    'page' => count($filelist) <= 10 ? false : true
                ],
                'item_tool_bar' => [
                    [
                        'name' => 'download',
                        'title' => '测试',
                        'sort' => 10,
                        'js_func' => '',
                        'icon' => '',
                        'class' => 'btn-5'
                    ],
                    [
                        'name' => 'delete',
                        'title' => '删除',
                        'sort' => 20,
                        'js_func' => '',
                        'icon' => '',
                        'class' => 'btn-6'
                    ]
                ],
                'checkbox' => false// 不显示多选框
            ]
        ];
        $table = new Table($basic, $tableTab);
        $this->assign->table = $table;
        $this->local['header_title'] = '静态数据';
        $this->local['header_tip'] = '对于非数据表或单一页数据的展示比较有用';
        return $this->fetch('list');
    }

    /**
     * @Ps(name="注解功能")
     */
    public function demo8()
    {
        $this->setHeaderInfo('title', '注解功能');
        $this->setHeaderInfo('tip', '系统启用了一些注解功能，以便可以快速的定义一些功能；后期根据情况考虑注入更多注解功能');
        return $this->fetch('demo8');
    }

    /**
     * @Ps(name="403页面")
     */
    public function demo9()
    {
        return $this->fetch(\think\facade\App::getBasePath() . 'common/view/common/403.html');
    }

    /**
     * @Ps(name="404页面")
     */
    public function demo10()
    {
        return $this->fetch(\think\facade\App::getBasePath() . 'common/view/common/404.html');
    }

    /**
     * @Ps(name="500页面")
     */
    public function demo11()
    {
        return $this->fetch(\think\facade\App::getBasePath() . 'common/view/common/500.html');
    }

    /**
     * @Ps(name="大屏展示")
     */
    public function demo12()
    {
        $this->assign->week = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
        $charts = [
            'total'       => 6666,
            'break_total' => 888,
            'charts_1' => [
                [
                    'title' => '订单数量',
                    'rows' => 888,
                    'breaks' => 333,
                    'color' => '#0088cc'
                ],
                [
                    'title' => '私有信息',
                    'rows' => 200,
                    'breaks' => 60,
                    'color' => '#fccb00'
                ],
                [
                    'title' => '公有信息',
                    'rows' => 200,
                    'breaks' => 80,
                    'color' => '#62b62f'
                ],
                [
                    'title' => '招标数据',
                    'rows' => 60,
                    'breaks' => 30,
                    'color' => '#0088cc'
                ],
                [
                    'title' => '产品数据',
                    'rows' => 2000,
                    'breaks' => 1000,
                    'color' => '#0088cc'
                ],
                [
                    'title' => '文章数据',
                    'rows' => 800,
                    'breaks' => 300,
                    'color' => '#62b62f'
                ],

            ],
            'charts_2' => [
                [
                    'id' => 1,
                    'title' => '男生注册量',
                    'rows' => 10,
                    'breaks' => 6,
                    'color' => '#0088cc'
                ],
                [
                    'id' => 2,
                    'title' => '女生注册量',
                    'rows' => 20,
                    'breaks' => 3,
                    'color' => '#fccb00'
                ],
                [
                    'id' => 3,
                    'title' => '未注册量',
                    'rows' => 20,
                    'breaks' => 3,
                    'color' => '#62b62f'
                ],
            ],
            'break_list' => [
                '我是一条测试数据1',
                '我是一条测试数据2',
                '我是一条测试数据3',
                '我是一条测试数据4',
                '我是一条测试数据5',
                '我是一条测试数据6',
                '我是一条测试数据7'
            ]
        ];
        $this->assign->charts = $charts;
        $this->assign->removeCss();
        $this->assign->addCss('/charts1/css/charts.css');
        $this->assign->addJs('admin/echarts.common.min', true);
        $this->assign->addJs('admin/jquery.countup', true);
        return $this->fetch('demo12');
    }
}