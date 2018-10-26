<?php
/**
 * Created by PhpStorm.
 * User: 10728
 * Date: 2018/10/26
 * Time: 9:17
 */

namespace app\weixin\controller;


use think\Controller;
use app\weixin\helper\User;

class Auth extends Controller
{
    /**
     * 企业微信扫码授权登录
     * @route('weixin/auth/qrcode', 'get')
     */
    public function qrcode(){
        $code = input('get.code');
        if(isset($code)){
            $user = new User(1000002);
            $user_info = $user->getInfoByCode($code);
            p($user_info);
            //数据库查询UserId绑定用户
        }else{
            return $this->fetch();
        }
    }

    /**
     * 企业微信网页授权登录
     * @route('weixin/auth/webpage', 'get')
     */
    public function webpage(){
        $code = input('get.code');
        $user = new User(1000002);
        $user_info = $user->getInfoByCode($code);
        p($user_info, false);
        if(isset($user_info['user_ticket'])){
            //获取成员详细信息
            $user_info = $user->getDetailByUserTicket($user_info['user_ticket']);
        }
        p($user_info);
    }
}