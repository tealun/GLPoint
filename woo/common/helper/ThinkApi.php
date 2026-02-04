<?php
declare (strict_types=1);

namespace woo\common\helper;

class ThinkApi
{
    /**
     * 如果没有安装thinkapi client 为空
     * @var \think\api\Client|null
     */
    protected $client = null;

    public function __construct(string $appCode = '')
    {
        if (class_exists(\think\api\Client::class)) {
            $appCode = empty($appCode) ? app()->config->get('woo.app_code', '') : $appCode;
            $this->client = new \think\api\Client($appCode);
        }
    }

    /**
     * 获取client 为空就是没有安装thinkapi
     * @return \think\api\Client|null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * https://docs.topthink.com/think-api/2203721
     * 短信服务
     * @param $mobile 手发送的国内手机号码
     * @param $signId  签名id 在我的服务->短信服务->签名管理里面查看
     * @param $templateId 模板id，在我的服务->短信服务->模板管理里面查看
     * @param array $templateParam 模板变量 数组
     * @return array
     */
    public function smsSend($mobile, $signId, $templateId, array $templateParam = [])
    {
        if (empty($this->client)) {
            return $this->returnError('Api扩展未安装');
        }
        try {
            return $this->client
                ->smsSend()
                ->withSignId($signId)
                ->withTemplateId($templateId)
                ->withPhone($mobile)
                ->withParams(json_encode($templateParam, JSON_UNESCAPED_UNICODE))
                ->request();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * https://docs.topthink.com/think-api/1835394
     * 身份证实名认证
     * 检测姓名和身份证号是否一致，身份证验证。
     * @param string $realname 姓名(UTF-8)
     * @param string $idcard 身份证号码
     * @return array
     */
    public function idcardAuth(string $realname, string $idcard)
    {
        if (empty($this->client)) {
            return $this->returnError('Api扩展未安装');
        }
        try {
            return $this->client
                ->idcardAuth()
                ->withIdNum($idcard)
                ->withName($realname)
                ->request();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * https://docs.topthink.com/think-api/1835395
     * 三网手机实名制认证
     * 检验姓名、身份证、手机号码是否一致，支持移动、联通和电信。
     * @param string $realname  姓名
     * @param string $idcard    身份证
     * @param string $mobile    手机
     * @return array|mixed
     */
    public function telecomQuery(string $realname, string $idcard, string $mobile)
    {
        if (empty($this->client)) {
            return $this->returnError('Api扩展未安装');
        }
        try {
            return $this->client
                ->telecomQuery()
                ->withIdcard($idcard)
                ->withRealname($realname)
                ->withMobile($mobile)
                ->withType(1)
                ->withProvince(1)
                ->request();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * https://docs.topthink.com/think-api/2053939
     * 三网手机实名制认证（详版）
     * 检验姓名、身份证、手机号码是否一致，并返回不一致详情，支持移动、联通和电信。
     * @param string $realname  姓名
     * @param string $idcard    身份证
     * @param string $mobile    手机
     * @return array|mixed
     */
    public function telecomDetail(string $realname, string $idcard, string $mobile)
    {
        if (empty($this->client)) {
            return $this->returnError('Api扩展未安装');
        }
        try {
            return $this->client
                ->telecomDetail()
                ->withIdcard($idcard)
                ->withRealname($realname)
                ->withMobile($mobile)
                ->request();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * https://docs.topthink.com/think-api/2626189
     * 内容合规检测 限定 5000 个字符以内，文本长度 超过 5000 个字符时，只检测前 5000 个字符
     * 通过AI智能引擎识别文本中的涉政暴恐、色情低俗、恶意营销、广告等不良信息，杜绝平台内容违规风险，可满足不同场景的内容风控管理审核能力
     * @param $content 检查内容
     * @param string $type 类型 需要检测的场景（多个场景用逗号分割）默认所有场景： 1=色情涉黄 2=暴恐违禁 3=谩骂侮辱 4=涉政敏感 5=游戏相关 6=恶意营销 7=广告违规
     * @return array
     */
    public  function thinkAudit($content, $type = '1,2,3,4,5,6,7')
    {
        if (empty($this->client)) {
            return $this->returnError('Api扩展未安装');
        }
        // 为节约字符数把html标签过滤掉
        $content = trim(preg_replace('/\s/', '', strip_tags($content)));
        if (empty($content)) {
            return [
                'code' => 0,
                'message' => 'success',
                'data' => [
                    'pass' => true
                ]
            ];
        }
        try {
            return $this->client->thinkAudit()
                ->withText(mb_substr($content, 0, 5000))
                ->withType($type)
                ->request();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
    /**
     * https://docs.topthink.com/think-api/1942693
     * 文本审核 最多支持800字符
     * 判断文本内容是否还有违禁和灌水低质内容，请求参数content最大支持800字符
     * @param $content
     * @return array|mixed
     * @throws \think\api\Exception
     */
    public function websiteAntispam($content)
    {
        if (empty($this->client)) {
           return $this->returnError('Api扩展未安装');
        }
        // 为节约字符数把html标签过滤掉
        $content = trim(preg_replace('/\s/', '', strip_tags($content)));
        if (empty($content)) {
            return [
                'code' => 0,
                'message' => 'success',
                'data' => [
                    [
                        'con' => '合格',
                        'con_type' => 1
                    ]
                ]
            ];
        }
        try {
            return $this->client
                ->websiteAntispam()
                ->withContent(mb_substr($content, 0, 800))
                ->request();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * https://docs.topthink.com/think-api/1942694
     * 图像审核
     * 判断网络图像是否违禁，imgurl传递网络图片url，支持识别色情、政治人物和暴恐类型。
     * @param $image
     * @return array|mixed
     */
    public function websiteImgcensor($image)
    {
        if (empty($this->client)) {
            return $this->returnError('Api扩展未安装');
        }
        try {
            return $this->client
                ->websiteImgcensor()
                ->withImgurl($image)
                ->request();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * https://docs.topthink.com/think-api/1936153
     * 中文分词
     * 效率极高的中文分词接口，支持NLP智能分词
     * @param $content
     * @return array|mixed
     */
    protected function wordSegment($content)
    {
        if (empty($this->client)) {
            return $this->returnError('Api扩展未安装');
        }
        $content = trim( strip_tags($content));
        try {
            return $this->client
                ->wordSegment()
                ->withContent($content)
                ->request();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * https://docs.topthink.com/think-api/1949865
     * 灵聚ChatBot机器人
     * 基于开放域NLP技术开发的chatbot，基于数亿实体的三元知识图谱及十亿级数据深度学习使得这个Chatbot拥有丰富的能力，可以支持闲聊、百科知识问答，对话游戏及日常生活服务等交互。配合智能家居控制及音视频播放能力，可以成为智能家居的中控“大脑”；配合行业知识图谱和RPA技能，可以成为行业服务机器人及客服机器人。
     * @param $input 请求文本，长度:[1,300]，取值：普通标点符号及中英文数字
     * @param $userid 终端设备的唯一标识
     * @param $userip 终端设备的ip
     * @return array
     */
    public function lingjuChat($input, $userid, $userip = null)
    {
        if (empty($this->client)) {
            return $this->returnError('Api扩展未安装');
        }
        $userip = $userip || request()->ip();
        try {
            return $this->client
                ->lingjuChat()
                ->withInput($input)
                ->withUserid($userid)
                ->withUserip($userip)
                ->request();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    protected function returnError($message)
    {
        return [
            'code' => -1,
            'message' => $message,
            'data' => []
        ];
    }
}