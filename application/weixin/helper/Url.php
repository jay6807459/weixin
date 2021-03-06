<?php
/**
 * Created by PhpStorm.
 * User: 10728
 * Date: 2018/10/17
 * Time: 15:45
 */

namespace app\weixin\helper;


class Url
{
    const TOKEN_INFO_GET = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';
    const AGENT_LIST_GET = 'https://qyapi.weixin.qq.com/cgi-bin/agent/list';
    const AGENT_INFO_GET = 'https://qyapi.weixin.qq.com/cgi-bin/agent/get';
    const MESSAGE_INFO_SEND = 'https://qyapi.weixin.qq.com/cgi-bin/message/send';
    const DEPARTMENT_LIST_GET = 'https://qyapi.weixin.qq.com/cgi-bin/department/list';
    const DEPARTMENT_INFO_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/create';
    const DEPARTMENT_INFO_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/update';
    const DEPARTMENT_INFO_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/department/delete';
    const USER_LIST_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist';
    const USER_INFO_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/get';
    const USER_ID_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo';
    const USER_DETAIL_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserdetail';
    const MENU_LIST_GET = 'https://qyapi.weixin.qq.com/cgi-bin/menu/get';
    const MENU_LIST_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/menu/delete';
    const MENU_LIST_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/menu/create';
}