<?php
namespace app\weixin\controller;

use app\weixin\helper\Agent;
use app\weixin\helper\Base;
use app\weixin\helper\Department;
use app\weixin\helper\Message;
use app\weixin\helper\User;
use app\weixin\helper\Menu;
use think\Controller;

class Index extends Controller
{
    /**
     * 开启消息服务器验证-应用一
     * 响应字符串(8415675619882302974)
     * @route('weixin/index/agent1', 'get')
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
     * @route('weixin/index/agent1', 'post')
     */
    public function response(){
        $message = new Message(1000002);
        $response_xml = $message->response();
        echo $response_xml;
    }

    /**
     * @route('weixin/index/test', 'get')
     */
    public function test(){
        echo 'test';
    }

    /**
     * 应用相关
     * @route('weixin/index/agent', 'get')
     */
    public function agent(){
        $op = input('get.op');
        switch ($op){
            case 'list':
                $agent = new Agent(1000002);
                $agent_list = $agent->getList();
                p($agent_list);
                break;
            case 'info':
                $agent = new Agent(1000002);
                $agent_info = $agent->getInfo();
                p($agent_info);
                break;
            default:
                break;
        }
    }

    /**
     * 消息发送
     * @route('weixin/index/message', 'get')
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
     * @route('weixin/index/user', 'get')
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
     * @route('weixin/index/department', 'get')
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
                break;
            case 'create':
                $department = new Department(1000002);
                $result = $department->create([
                    'id' => 2,
                    'name' => '部门2',
                    'parentid' => 0,
                    'order' => 10000
                ]);
                p($result);
                break;
            case 'update':
                $department = new Department(1000002);
                $result = $department->update([
                    'id' => 1,
                    'name' => '测试',
                    'parentid' => 0,
                    'order' => 100
                ]);
                p($result);
                break;
            case 'delete':
                $department = new Department(1000002);
                $result = $department->delete(2);
                p($result);
            default:
                break;
        }
    }

    /**
     * 菜单相关
     * @route('weixin/index/menu', 'get')
     */
    public function menu(){
        $op = input('get.op');
        switch ($op){
            case 'create':
                $menu = new Menu(1000002);
                $menu->addButton([
                    "name" => "菜单"
                ])->addSubButton([
                    "type" => "view",
                   "name" => "搜索",
                   "url" => "http://www.soso.com/"
                ])->addSubButton([
                    "type" => "click",
                   "name" => "赞一下我们",
                   "key" => "V1001_GOOD"
                ])->addSubButton([
                    "type" => "click",
                    "name" => "今日歌曲",
                    "key" => "V1001_TODAY_MUSIC"
                ])->addButton([
                    "name" => "扫码"
                ])->addSubButton(
                    [
                        "type" => "scancode_waitmsg",
                        "name" => "扫码带提示",
                        "key" => "rselfmenu_0_0",
                        "sub_button" => [ ]
                    ])->addSubButton(
                    [
                        "type" => "scancode_push",
                        "name" => "扫码推事件",
                        "key" => "rselfmenu_0_1",
                        "sub_button" => [ ]
                    ])->addButton([
                    "name" => "发图"
                ])->addSubButton([
                    "type" => "pic_sysphoto",
                    "name" => "系统拍照发图",
                    "key" => "rselfmenu_1_0",
                   "sub_button" => [ ]
                ])->addSubButton([
                    "type" => "pic_photo_or_album",
                    "name" => "拍照或者相册发图",
                    "key" => "rselfmenu_1_1",
                    "sub_button" => [ ]
                ])->addSubButton([
                    "type" => "pic_weixin",
                    "name" => "微信相册发图",
                    "key" => "rselfmenu_1_2",
                    "sub_button" => [ ]
                ])->addSubButton([
                    "name" => "发送位置",
                    "type" => "location_select",
                    "key" => "rselfmenu_2_0"
                ]);
//                $menu_list = json_encode($menu->getMenuData(), JSON_UNESCAPED_UNICODE);
//                p($menu_list);
                $result = $menu->create();
                p($result);
                break;
            case 'list':
                $menu = new Menu(1000002);
                $menu_list = $menu->getList();
                p($menu_list);
                break;
            case 'delete':
                $user = new Menu(1000002);
                $result = $user->deleteList();
                p($result);
            default:
                break;
        }
    }
}
