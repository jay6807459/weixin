<?php
namespace app\weixin\controller;

use app\weixin\helper\Agent;
use app\weixin\helper\Base;
use app\weixin\helper\Department;
use app\weixin\helper\Message;
use app\weixin\helper\User;
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
        $op = input('get.op');
        switch ($op){
            case 'text':
                $message = new Message(1000002);
                $result = $message->setTouser('XiongQinLiang')->setMsgtype('text')->send([
                    'text' => [
                        'content' => '主动发送文本消息222'
                    ]
                ]);
                p($result);
                break;
            default:
                break;
        }
    }

    /**
     * 用户相关
     */
    public function user(){
        $op = input('get.op');
        switch ($op){
            case 'list':
                $user = new User(1000002);
                $user_list = $user->getList(1);
                p($user_list);
                break;
            case 'info':
                $user = new User(1000002);
                $user_info = $user->getInfo('XiongQinLiang');
                p($user_info);
            default:
                break;
        }
    }

    /**
     * 部门相关
     */
    public function department(){
        $op = input('get.op');
        switch ($op){
            case 'list':
                $department = new Department(1000002);
                $department_list = $department->getList();
                p($department_list);
                break;
            case 'info':
                $department = new Department(1000002);
                $department_info = $department->getInfo(1);
                p($department_info);
            case 'update':
                $department = new Department(1000002);
                $result = $department->update([
                    'id' => 1,
                    'name' => '测试',
                    'parentid' => 0,
                    'order' => 100
                ]);
                p($result);
            case 'delete':
                $department = new Department(1000002);
                $result = $department->delete(1);
                p($result);
            default:
                break;
        }
    }
}
