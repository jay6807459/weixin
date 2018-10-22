<?php
/**
 * Created by PhpStorm.
 * User: 10728
 * Date: 2018/10/22
 * Time: 13:35
 */

namespace app\weixin\helper;


use think\Exception;

class User extends Agent
{
    public function __construct($agent_id)
    {
        parent::__construct($agent_id);
    }

    /**
     * 获取用户列表
     * @param $department_id        部门ID
     * @param int $fetch_child      1/0：是否递归获取子部门下面的成员
     */
    public function getList($department_id, $fetch_child = 1){
        $result = http_get(append_access_token(Url::GET_USER_LIST, $this->access_token), [
            'department_id' => $department_id,
            'fetch_child' => $fetch_child
        ]);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result['userlist'];
    }

    /**
     * 获取用户信息
     * @param $userid
     */
    public function getInfo($userid){
        $result = http_get(append_access_token(Url::GET_USER_INFO, $this->access_token), ['userid' => $userid]);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result;
    }

}