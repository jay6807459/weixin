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
        if(!$msg_signature || !$timestamp || !$nonce || !$echostr){
            throw new Exception('参数异常');
        }
        $token = Config::get('token');
        $signature_arr = array($token, $timestamp, $nonce, $echostr);
        sort($signature_arr, SORT_STRING);
        $signature = sha1(implode($signature_arr));
        if($signature != $msg_signature){
            throw new Exception($signature . '验证签名不合法');
        }
        return $signature;
    }

    /**
     * 获取应用的凭证
     * @param $agentid
     */
    public static function getAccessToken($agentid)
    {
        $cache_path = __DIR__ . '/../cache/' . $agentid . '.php';
        $cache = json_decode(get_file_cache($cache_path));
        //缓存文件不存在或者缓存过期
        if($cache->expires_in == 0 || time() >= filemtime($cache_path) + $cache->expires_in){
            $agent = new Agent($agentid);
            $secret = $agent->getSecret();
            $cache = json_decode(http_get(Url::GET_TOKEN, [
                'corpid' => Config::get('corpid'),
                'corpsecret' => $secret
            ]));
            set_file_cache($cache_path, json_encode($cache));
        }
        return $cache->access_token;
    }
}