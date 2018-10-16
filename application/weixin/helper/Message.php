<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 2018/10/17
 * Time: 0:07
 */

namespace app\weixin\helper;


use app\weixin\helper\algorithm\crypt;
use think\facade\Config;

class Message extends Base
{
    /**
     * 加密字符串解密
     * @param $echostr
     */
    public static function decode($echostr){
        $crypt = new crypt(Config::get('encoding_aeskey'));
        $result = $crypt->decrypt($echostr, Config::get('corpid'));
        if ($result[0] != 0) {
            return $result[0];
        }
        $reply_echostr = $result[1];
        return $reply_echostr;
    }
}