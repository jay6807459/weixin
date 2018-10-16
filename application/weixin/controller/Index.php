<?php
namespace app\weixin\controller;

use app\weixin\helper\Base;
use app\weixin\helper\Message;
use think\facade\Config;
class Index
{
    /**
     * 开启消息服务器验证
     */
    public function index()
    {
        //1.对收到的请求做Urldecode处理
        $msg_signature = input('get.msg_signature');
        $timestamp = input('get.timestamp');
        $nonce = input('get.nonce');
        $echostr = input('get.echostr');
        //2.通过参数msg_signature对请求进行校验，确认调用者的合法性。
        $signature = Message::validateSignature($msg_signature, $timestamp, $nonce, $echostr);
        //3.解密echostr参数得到消息内容(即msg字段)
        $reply_echostr = Message::decode($echostr);
        //4.在1秒内响应GET请求，响应内容为上一步得到的明文消息内容(不能加引号，不能带bom头，不能带换行符)
        echo $reply_echostr;
    }
}
