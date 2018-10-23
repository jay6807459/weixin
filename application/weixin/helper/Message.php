<?php
/**
 * Created by PhpStorm.
 * User: lily
 * Date: 2018/10/17
 * Time: 0:07
 */

namespace app\weixin\helper;


use app\weixin\helper\algorithm\crypt;
use think\Exception;
use think\facade\Config;

class Message extends Agent
{
    //消息主题
    protected $content;

    //成员ID列表（消息接收者，多个接收者用‘|’分隔，最多支持1000个）。特殊情况：指定为@all，则向该企业应用的全部成员发送
    protected $touser;

    //部门ID列表，多个接收者用‘|’分隔，最多支持100个。当touser为@all时忽略本参数
    protected $toparty;

    //标签ID列表，多个接收者用‘|’分隔，最多支持100个。当touser为@all时忽略本参数
    protected $totag;

    //消息类型，此时固定为：text
    protected $msgtype;

    //是否是保密消息，0表示否，1表示是，默认0
    protected $safe = 0;

    public function __construct($agent_id)
    {
        parent::__construct($agent_id);
        $this->content['agentid'] = $agent_id;
        $this->content['safe'] = $this->safe;
    }

    public function setTouser($touser){
        $this->content['touser'] = $touser;
        return $this;
    }

    public function setToparty($toparty){
        $this->content['toparty'] = $toparty;
        return $this;
    }

    public function setTotag($totag){
        $this->content['totag'] = $totag;
        return $this;
    }

    public function setMsgtype($msgtype){
        $this->content['msgtype'] = $msgtype;
        return $this;
    }

    public function send($data){
        $this->content = array_merge($this->content, $data);
        if(!isset($this->content['touser']) && !isset($this->content['toparty']) && !isset($this->content['totag'])){
            throw new Exception('成员列表、部门列表、标签列表不能同时为空');
        }elseif(!isset($this->content['msgtype'])){
            throw new Exception('消息类型不能为空');
        }
        switch ($this->content['msgtype']){
            case 'text':
                if(empty($this->content['text']['content'])){
                    throw new Exception('消息内容不能为空');
                }
                break;
            default:
                break;
        }
        $result = http_post(append_access_token(Url::SEND_MESSAGE, $this->access_token), $this->content);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result;
    }

    /**
     * 被动接收消息
    接收xml格式：
    <xml>
    <ToUserName><![CDATA[toUser]]></ToUserName>
    <AgentID><![CDATA[toAgentID]]></AgentID>
    <Encrypt><![CDATA[msg_encrypt]]></Encrypt>
    </xml>
    响应xml格式：
    <xml>
    <Encrypt><![CDATA[msg_encrypt]]></Encrypt>
    <MsgSignature><![CDATA[msg_signature]]></MsgSignature>
    <TimeStamp>timestamp</TimeStamp>
    <Nonce><![CDATA[nonce]]></Nonce>
    </xml>
     */
    public function receive(){
        $receive_crypt_xml = file_get_contents('php://input');
        $crypt = new crypt($this->getEncodingAeskey());
        $result = $crypt->decrypt($receive_crypt_xml, $this->corpid);
        if ($result[0] != 0) {
            return $result[0];
        }
        //解密xml明文
        $receive_decrypt_xml = $result[1];
        $receive_decrypt_arr = $this->xmlToArray($receive_decrypt_xml);

        //构造被动响应数据

    }

    /**
     * 被动响应消息
     */
    public function response($data, $timestamp, $nonce){

    }

    /**
     * 字符串加密
     */
    public function encode($data, $timestamp, $nonce){
        $crypt = new crypt($this->getEncodingAeskey());
        $visible_xml = $this->arrayToXml($data);
        $result = $crypt->encrypt($visible_xml, $this->corpid);
        if ($result[0] != 0) {
            return $result[0];
        }
        //加密
        $encrypt = $result[1];
        //加密后的明文
        $signature = $this->createSignature($timestamp, $nonce, $encrypt);
        $hidden_xml = $this->arrayToXml([
            'Encrypt' => $encrypt,
            'MsgSignature' => $signature,
            'TimeStamp' => $timestamp,
            'Nonce' => $nonce
        ]);
        return $hidden_xml;
    }

    /**
     * 加密字符串解密
     * @param $echostr
     */
    public function decode($msg_signature, $timestamp, $nonce, $echostr){
        //验证签名
        $this->validateSignature($msg_signature, $timestamp, $nonce, $echostr);
        //解密
        $crypt = new crypt($this->getEncodingAeskey());
        $result = $crypt->decrypt($echostr, $this->corpid);
        if ($result[0] != 0) {
            return $result[0];
        }
        $reply_echostr = $result[1];
        return $reply_echostr;
    }

    /**
     * 数组转xml格式
     * @param $data
     */
    public function arrayToXml($data){
        $xml = '<xml>';
        foreach($data as $k => $v){
            $xml .= ($k == 'TimeStamp') ? "<{$k}><![CDATA[{$v}]]></{$k}>" : "<{$k}>{$v}</{$k}>";
        }
        $xml .= '</xml>';
        return $xml;
    }

    /**
     * xml转数组格式
     * @param $data
     */
    public function xmlToArray($data){

    }
}