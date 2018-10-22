<?php
/**
 * Created by PhpStorm.
 * User: 10728
 * Date: 2018/10/22
 * Time: 13:39
 */

namespace app\weixin\helper;

use think\Exception;

/**
 * 部门相关
 * Class Department
 * @package app\weixin\helper
 */
class Department extends Agent
{
    public function __construct($agent_id)
    {
        parent::__construct($agent_id);
    }

    /**
     * 获取部门列表
     * @return mixed
     */
    public function getList(){
        $result = http_get(append_access_token(Url::GET_DEPARTMENT_LIST, $this->access_token));
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result['department'];
    }

    /**
     * 获取部门信息
     * @param $id
     * @return mixed
     */
    public function getInfo($id){
        $result = http_get(append_access_token(Url::GET_DEPARTMENT_LIST, $this->access_token), ['id' => $id]);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result['department'];
    }

    /**
     * 创建部门
     * @param $data
     */
    public function create($data){
        $result = http_post(append_access_token(Url::CREATE_DEPARTMENT_INFO, $this->access_token), $data);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result;
    }

    /**
     * 更新部门信息
     * @param $data
     */
    public function update($data){
        $result = http_post(append_access_token(Url::UPDATE_DEPARTMENT_INFO, $this->access_token), $data);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result;
    }

    /**
     * 删除部门
     * @param $id
     */
    public function delete($id){
        $result = http_get(append_access_token(Url::DELETE_DEPARTMENT_INFO, $this->access_token), ['id' => $id]);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result;
    }

}