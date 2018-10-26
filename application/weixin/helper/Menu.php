<?php
/**
 * Created by PhpStorm.
 * User: 10728
 * Date: 2018/10/26
 * Time: 10:08
 */

namespace app\weixin\helper;

use think\Exception;

class Menu extends Agent
{
    protected $menu_list;

    protected $sub_button;

    public function __construct($agent_id)
    {
        parent::__construct($agent_id);
    }

    /**
     * 添加主菜单
     * @param $button
     */
    public function addButton($button){
        $this->menu_list['button'] = $button;
    }

    /**
     * 添加子菜单
     * @param $sub_button
     */
    public function addSubButton($sub_button){

    }

    public function create(){
        $result = http_post($this->appendAccessToken(Url::MENU_LIST_CREATE . '?agentid=' . $this->agent_id), $this->menu_list);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        p($result);
        return $result;
    }

    /**
     * 获取菜单列表
     */
    public function getList(){
        $result = http_get($this->appendAccessToken(Url::MENU_LIST_GET), ['agentid' => $this->agent_id]);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result;
    }

    /**
     * 删除菜单列表
     */
    public function deleteList(){
        $result = http_get($this->appendAccessToken(Url::MENU_LIST_DELETE), ['agentid' => $this->agent_id]);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result;
    }

}