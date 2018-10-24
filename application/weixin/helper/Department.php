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
        $result = http_get($this->appendAccessToken(Url::DEPARTMENT_LIST_GET));
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
        $result = http_get($this->appendAccessToken(Url::DEPARTMENT_LIST_GET), ['id' => $id]);
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
        $result = http_post($this->appendAccessToken(Url::DEPARTMENT_INFO_CREATE), $data);
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
        $result = http_post($this->appendAccessToken(Url::DEPARTMENT_INFO_UPDATE), $data);
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
        $result = http_get($this->appendAccessToken(Url::DEPARTMENT_INFO_DELETE), ['id' => $id]);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result;
    }

}