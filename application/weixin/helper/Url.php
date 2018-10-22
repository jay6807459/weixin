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
    const GET_TOKEN = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';
    const AGENT_GET = 'https://qyapi.weixin.qq.com/cgi-bin/agent/get';
    const SEND_MESSAGE = 'https://qyapi.weixin.qq.com/cgi-bin/message/send';
    const GET_DEPARTMENT_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/department/list';
    const CREATE_DEPARTMENT_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/department/create';
    const UPDATE_DEPARTMENT_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/department/update';
    const DELETE_DEPARTMENT_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/department/delete';
    const GET_USER_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist';
    const GET_USER_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/user/get';
}