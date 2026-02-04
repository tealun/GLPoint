<?php
declare (strict_types=1);

namespace woo\common\builder\form\traits;


trait AttrValues
{
    /**
     * 内置表单原始类型
     */
    //protected $elementList = [];

    /**
     * 每种表单类型需要处理的属性方法
     * @var array
     */
    protected $parseMethod = [];

    /**
     * 哪些表单类型是 隐藏类型
     * @var array
     */
    protected $hiddenElementList = [];

    /**
     * 表单项目默认属性
     * @var array
     */
    protected $defaultOptions = [];

    protected $defaultAttrs = [];

    protected function setParseMethodValue()
    {
        $this->parseMethod = array_merge([
            '_' => [
                'attrs', 'label', 'label_for_id', 'label_suffix', 'label_tag', 'is_hidden', 'use_element', 'tip', 'fetch'
            ],
            'text' => [
            ],
            'radio' => [
                'options'
            ],
            'checkbox' => [
                'options'
            ],
            'select' => [
                'options'
            ],
            'relation' => [
                'foreign'
            ]
        ],$this->parseMethod);
    }

    protected function setDefaultAttrs()
    {
        $this->defaultAttrs = array_merge(
            [],
            $this->defaultAttrs
        );
    }

    protected function setElementListValue()
    {
        $this->elementList = array_merge($this->elementList, [
            'csrf'       // CSRF验证
            ,'captcha'      // 验证码
            ,'text'         // 单行文本框
            ,'emailh5'      // H5-email框 type="email"
            ,'urlh5'        // H5-url框 type="url"
            ,'telh5'        // H5-tel框 type="tel"
            ,'password'     // 密码输入框
            ,'number'       // H5-number数字数字框
            ,'radio'        // 单选框
            ,'checkbox'     // 多选框
            ,'select'       // 下列框
            ,'xmselect'     // xm-select单选、多选 兼容IE10以上 一般后台使用
            ,'textarea'     // 多行文本框
            ,'hidden'       // 隐藏域
            ,'color'        // 颜色选择
            ,'colorh5'      // H5颜色选择 type="color"
            ,'date'         // 日期选择
            ,'datetime'     //
            ,'month'        //
            ,'year'         //
            ,'time'         //
            ,'ueditor'      // ueditor富文本编辑器
            ,'checker'      // 开关
            ,'tag'          // 标签
            ,'icon'         // 图标
            ,'image'        // 单图片上传 - 异步
            ,'multi_image'  // 多图片上传 - 异步
            ,'file'         // 文件上传 - 异步
            ,'upload'       // 普遍同步上传
            ,'array'        // 数组
            ,'keyvalue'    // 键值对
            ,'relation'    // 关联选择
            ,'format'      // 直接输出
            ,'cascader'    // 联动选择 V2.0.4
            ,'slider'      // 滑块 V2.0.4
            ,'rate'        // 评分 V2.0.4
            ,'transfer'    // 穿梭框 V2.0.4
            ,'amap'        // 高德地图 V2.0.8
            ,'email'       // 邮箱输入 V2.0.5
            ,'bankcard'    // 银行卡格式输入 V2.0.5
            ,'ip4'         // IP4格式输入 V2.0.5
            ,'ip6'         // IP6格式输入 V2.0.5
            ,'random'      // 随机字符串 V2.1.0
            ,'spec'        // 规格 V2.1.2
            ,'sortvalues'  // 值排序 V2.1.2
            ,'none'
            ,'0'
            ,''
        ]);
    }

    protected function setDefaultOptionsValue()
    {
        $this->defaultOptions = array_merge([
            '_' => [
            ],
            'captcha' => [
                'label' => __('captcha'),
                'attrs' => [
                    'class' => 'captcha-input'
                ]
            ],
            'text' => [
            ],
            'radio' => [
                'options' => [],
                'is_title' => true
            ],
            'checkbox' => [
                'options' => [],
                'is_title' => true
            ],
            'select' => [
                'options' => []
            ]
        ], $this->defaultOptions);
    }

    protected function setHiddenElementListValue()
    {
        $this->hiddenElementList = array_merge($this->hiddenElementList, [
            'hidden',
            'csrf',
            'none',
            '0',
            ''
        ]);
    }


}