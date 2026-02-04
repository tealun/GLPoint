<?php
declare (strict_types=1);

namespace woo\common\helper;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\Font;

class QRcode
{
    /**
     * @param string $data 二维码上的内容
     * @param int $size 大小
     * @param int $margin margin
     * @param string $logo logo路径
     * @param array $options 底部文字 text 文字  path 字体文件  size 大小  ground_color 二维码颜色
     * @return \Endroid\QrCode\Writer\Result\ResultInterface
     * @throws \Exception
     */
    public  static  function makeQRcode(string $data, int $size = 300, int $margin = 10, string $logo = '', array $options = [])
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($size)
            ->margin($margin)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->validateResult(false);
        if ($logo) {
            // 添加logo
            $result = $result->logoPath($logo);
        }
        if (!empty($options['ground_color'])) {
            // 二维码默认是黑色 可以改颜色, 比如红色：  $options['ground_color'] = [255, 0, 0]
            $result->foregroundColor(new Color(...$options['ground_color']));
        }
        if (!empty($options['background_color'])) {
            //二维码背景颜色 默认是白色的
            $result->backgroundColor(new Color(...$options['background_color']));
        }

        if (!empty($options['text'])) {
            // 添加二维码底部的文字
            $result->labelText($options['text'])
                ->labelFont(new NotoSans($options['size'] ?? 10))
                ->labelAlignment(new LabelAlignmentCenter());// 位置可以自己new了 可以自己去找下源文件 就知道类名了
            if (!empty($options['path'])) {
                $result->labelFont(new Font($options['path'])); // 字体文件
            }
        }
        return $result->build();
    }
}