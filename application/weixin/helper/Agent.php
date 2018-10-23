<?php
/**
 * Created by PhpStorm.
 * User: 10728
 * Date: 2018/10/17
 * Time: 15:29
 */

namespace app\weixin\helper;


use think\Exception;
use think\facade\Config;

class Agent
{
    public $corpid;

    public $agent_id;

    public $secret;

    public $access_token;

    public function __construct($agent_id)
    {
        $this->corpid = Config::get('corpid');
        $this->agent_id = $agent_id;
        $agents = Config::get('agents');
        if(!array_key_exists($agent_id, $agents)){
            throw new Exception('应用ID不存在');
        }
        $this->secret = $agents[$agent_id]['secret'];
        $this->access_token = $this->getAccessToken();
    }

    protected function getToken(){
        $agents = Config::get('agents');
        return $agents[$this->agent_id]['token'];
    }

    protected function getEncodingAeskey(){
        $agents = Config::get('agents');
        return $agents[$this->agent_id]['encoding_aeskey'];
    }

    /**
     * 验证签名的合法性
     * @param $msg_signature
     * @param $timestamp
     * @param $nonce
     * @param $echostr
     */
    public function validateSignature($msg_signature, $timestamp, $nonce, $echostr)
    {
        if(!$msg_signature || !$timestamp || !$nonce || !$echostr){
            throw new Exception('参数异常');
        }
        $signature = $this->createSignature($timestamp, $nonce, $echostr);
        if($signature != $msg_signature){
            throw new Exception($signature . '验证签名不合法');
        }
        return true;
    }

    /**
     * 生成签名
     * @param $timestamp
     * @param $nonce
     * @param $echostr
     * @return string
     */
    public function createSignature($timestamp, $nonce, $echostr){
        $signature_arr = array($this->getToken(), $timestamp, $nonce, $echostr);
        sort($signature_arr, SORT_STRING);
        $signature = sha1(implode($signature_arr));
        return $signature;
    }

    /**
     * 获取应用的凭证
     * @param $agentid
     */
    public function getAccessToken()
    {
        $cache_path = __DIR__ . '/../cache/' . $this->agent_id . '.php';
        $cache = json_decode(get_file_cache($cache_path), true);
        //缓存文件不存在或者缓存过期
        if($cache['expires_in'] == 0 || time() >= filemtime($cache_path) + $cache['expires_in']){
            $cache = http_get(Url::GET_TOKEN, [
                'corpid' => $this->corpid,
                'corpsecret' => $this->secret
            ]);
            set_file_cache($cache_path, json_encode($cache));
        }
        return $cache['access_token'];
    }

    /**
     * 获取应用信息
     */
    public function getInfo(){
        $result = http_get(append_access_token(Url::AGENT_GET, $this->access_token), ['agentid' => $this->agent_id]);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result;
    }
}