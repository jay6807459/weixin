<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 2018/10/16
 * Time: 23:07
 */

namespace app\weixin\helper;
use think\Exception;
use think\facade\Config;

/**
 * 企业微信基础服务
 * Class Base
 * @package app\weixin\helper
 */
class Base
{
    /**
     * 验证签名的合法性
     * @param $msg_signature
     * @param $timestamp
     * @param $nonce
     * @param $echostr
     */
    public static function validateSignature($msg_signature, $timestamp, $nonce, $echostr)
    {
        $token = Config::get('token');
        $signature_arr = array($token, $timestamp, $nonce, $echostr);
        sort($signature_arr, SORT_STRING);
        $signature = sha1(implode($signature_arr));
        if($signature != $msg_signature){
            throw new Exception($signature . '验证签名不合法');
        }
        return $signature;
    }
}