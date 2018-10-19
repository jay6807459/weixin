<?php
namespace app\weixin\controller;

use app\weixin\helper\Agent;
use app\weixin\helper\Base;
use app\weixin\helper\Message;
use think\facade\Config;
class Index
{
    /**
     * 开启消息服务器验证-应用一
     */
    public function agent1()
    {
        //1.对收到的请求做Urldecode处理
        $msg_signature = input('get.msg_signature');
        $timestamp = input('get.timestamp');
        $nonce = input('get.nonce');
        $echostr = input('get.echostr');
        //2.通过参数msg_signature对请求进行校验，确认调用者的合法性。
        $message = new Message(1000002);
        $message->validateSignature($msg_signature, $timestamp, $nonce, $echostr);
        //3.解密echostr参数得到消息内容(即msg字段)
        $reply_echostr = $message->decode($echostr);
        //4.在1秒内响应GET请求，响应内容为上一步得到的明文消息内容(不能加引号，不能带bom头，不能带换行符)
        echo $reply_echostr;
    }

    /**
     * 消息发送
     */
    public function message(){
        $type = input('get.type');
        switch ($type){
            case 'text':
                try{
                    $message = new Message(1000002);
                    $result = $message->setTouser('XiongQinLiang')->setMsgtype('text')->send([
                        'text' => [
                            'content' => '主动发送文本消息222'
                        ]
                    ]);
                    var_dump($result);

                }catch(\Exception $e){
                    echo $e->getFile() . $e->getLine() . $e->getMessage();
                }
                break;
            default:
                echo 'message';
                break;
        }
    }
}
