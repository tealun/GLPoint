<?php
declare (strict_types=1);

namespace woo\common\helper;


use think\facade\Config;

class Str extends \think\helper\Str
{
    /**
     * @param $string
     * @param bool $numeric 数字字符串是否转换为数字
     * @return float|int|mixed|string
     */
    public static function deepJsonDecode($string, $numeric = true)
    {
        if (is_array($string)) {
            foreach ($string as &$value) {
                $value = self::deepJsonDecode($value);
            }
            return $string;
        }
        $map = [
            "true" => true,
            "false" => false,
            "none" => "",
            "0" => 0,
            "1" => 1
        ];
        if (is_string($string)) {
            $string = trim($string);
        }
        if (is_string($string) && array_key_exists($string, $map)) {
            return $map[$string];
        }
        // 将数字字符串转换为数字
        if ($numeric && is_numeric($string)) {
            $string = intval($string) == $string? intval($string): floatval($string);
        }
        if (is_string($string)) {
            $data = is_json($string);
            if (is_array($data)) {
                return self::deepJsonDecode($data);
            }
        }
        return $string;
    }

    public static function varExport($array = [], int $level = 0)
    {
        //1、 用\Nette\PhpGenerator\Dumper库的dump方法考虑的情况更加周全 强大 单格式有点不舒服
        return (new \Nette\PhpGenerator\Dumper())->dump($array);

        // 2 : 也可以用var_export 自带函数 但数组 是array定义  其实就是缩进是2个空格 有点不爽
//        if (!is_array($array)) {
//            return $array;
//        }
//        $string = var_export($array, true);
//        $string = preg_replace("/(=>\s*\n\s*)?array\s*\(\s*\n/", "=> [\n", $string);
//        $string = preg_replace("/\)\s*,\s*\n/", "],\n", $string);
//        pr($string);
//        if (substr($string, 0, 3) === '=> ') {
//            $string = substr($string, 3);
//        }
//        if (substr($string, -1) === ")") {
//            $string = substr($string, 0, -1) . ']';
//        }
//        return $string;

        // 3 : 自定义的数组转换为格式化的字符串 没有特殊值（对象、资源、函数）的数组 问题不大 需要多检验
//        if (is_int($array) || is_float($array)) {
//            return $array;
//        } elseif (is_bool($array)) {
//            return $array ? 'true' : 'false';
//        } elseif (is_string($array)) {
//            return "'"  . addcslashes($array, "'") . "'";
//        } elseif (is_null($array)) {
//            return 'null';
//        } elseif (!is_array($array)) {
//            if ($array instanceof \Serializable) {
//                return 'unserialize(' . serialize($array) . ')';
//            } elseif (is_object($array)) {
//                return (new \Nette\PhpGenerator\Dumper())->dump($array);
//            }
//            return '[]';
//        }
//        if (empty($array)) {
//            return '[]';
//        }
//        $a = str_repeat('    ',  $level);
//        $b = str_repeat('    ',  $level + 1);
//        $string = "[\n";
//        $index = 0;
//        foreach ($array as $key => $value) {
//            if (!is_int($key)) {
//                $string .= sprintf("%s'%s' => %s,\n", $b, $key, self::varExport($value, $level + 1));
//            } elseif ($key == $index) {
//                $string .= sprintf("%s%s,\n", $b, self::varExport($value, $level + 1));
//            } else {
//                $index = $key;
//                $string .= sprintf("%s%s => %s,\n", $b, $key, self::varExport($value, $level + 1));
//            }
//            $index++;
//        }
//        $string .= $a . "]";
//        return $string;
    }

    //只支持117位加密
    public static function setEncrypt($data)
    {
        $encrypted = "";
        $pu_key = openssl_pkey_get_public(Config::get('woo.rsa_public'));
        openssl_public_encrypt($data, $encrypted, $pu_key);
        $encrypted = base64_encode($encrypted);
        return $encrypted;

    }

    //超长字符加密 返回数值
    public static function setLongEncrypt($data)
    {
        $listdata = Array();
        $subject = $data;
        $spr = str_split($subject, 116);
        for ($i = 0; $i < count($spr); $i++) {
            $srt = static::setEncrypt($spr[$i]);
            array_push($listdata, $srt);
        }
        return $listdata;
    }

    //解密
    public static function setDecrypt($data)
    {
        $decrypted = "";
        $pi_key = openssl_pkey_get_private(Config::get('woo.rsa_private'));
        //这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        openssl_private_decrypt(base64_decode($data), $decrypted, $pi_key);

        return $decrypted;
    }

    //数组解密
    public static function setDecryptArray($arraydata)
    {
        $DecryptStr = "";
        for ($i = 0; $i < count($arraydata); $i++) {
            $DecryptStr = $DecryptStr . static::setDecrypt($arraydata[$i]);
        }
        return $DecryptStr;
    }
}