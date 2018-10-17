<?php
/**
 * Created by PhpStorm.
 * User: 10728
 * Date: 2018/10/17
 * Time: 15:29
 */

namespace app\weixin\helper;


use think\Exception;
use think\facade\Config;

class Agent extends Base
{
    protected $agent_id;

    protected $secret;

    public function __construct($agent_id)
    {
        $this->agent_id = $agent_id;
        $agents = Config::get('agents');
        $is_exist = false;
        foreach($agents as $agent){
            if($agent['agentid'] == $agent_id){
                $this->secret = $agent['secret'];
                $is_exist = true;
            }
        }
        if(!$is_exist){
            throw new Exception('应用ID不存在');
        }
    }

    public function getAgentId(){
        return $this->agent_id;
    }

    public function getSecret()
    {
        return $this->secret;
    }
}