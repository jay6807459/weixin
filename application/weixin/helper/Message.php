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
        $result = http_post($this->appendAccessToken(Url::MESSAGE_INFO_SEND), $this->content);
        if($result['errcode'] != 0){
            throw new Exception($result['errmsg']);
        }
        return $result;
    }

    /**
     * 被动接收消息并回复
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
    public function response(){
        $receive_xml = file_get_contents('php://input');
        $receive_arr = $this->xmlToArray($receive_xml);
        $encrypt = $receive_arr['Encrypt'];
        $decrypt_xml = $this->decode($encrypt);
        $decrypt_arr = $this->xmlToArray($decrypt_xml);
//        switch ($decrypt_arr['MsgType']){
//            //接收用户消息并回复
//            case 'text':
//                if($decrypt_arr['Content'] == 666){
//                    $content = '最右666';
//                }else{
//                    $content = '没有关键字，无法自动回复';
//                }
//                $response_arr = [
//                    'ToUserName' => $decrypt_arr['FromUserName'],
//                    'FromUserName' => $decrypt_arr['ToUserName'],
//                    'CreateTime' => time(),
//                    'MsgType' => 'text',
//                    'Content' => $content
//                ];
//                break;
//            //进入应用事件、进入自定义菜单事件处理
//            case 'event':
//                if($decrypt_arr['Event'] == 'enter_agent'){
//                    $response_arr = [
//                        'ToUserName' => $decrypt_arr['FromUserName'],
//                        'FromUserName' => $decrypt_arr['ToUserName'],
//                        'CreateTime' => time(),
//                        'MsgType' => 'text',
//                        'Content' => '欢迎进入测试应用'
//                    ];
//                }else{
//                    $response_arr = [
//                        'ToUserName' => $decrypt_arr['FromUserName'],
//                        'FromUserName' => $decrypt_arr['ToUserName'],
//                        'CreateTime' => time(),
//                        'MsgType' => 'text',
//                        'Content' => json_encode($decrypt_arr)
//                    ];
//                }
//            default:
//                break;
//        }
        $response_arr = [
            'ToUserName' => $decrypt_arr['FromUserName'],
            'FromUserName' => $decrypt_arr['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => json_encode($decrypt_arr, JSON_UNESCAPED_UNICODE)
        ];
        //消息体加密
        $encrypt = $this->encode($response_arr);
        //构造被动响应xml
        $response_xml = $this->construct_response($encrypt);
        return $response_xml;

    }

    /**
     * 构造被动响应消息
     */
    public function construct_response($encrypt){
        $timestamp = time();
        $nonce = $this->getRandomStr();
        $signature = $this->createSignature($timestamp, $nonce, $encrypt);
        $crypt_xml = $this->arrayToXml([
            'Encrypt' => $encrypt,
            'MsgSignature' => $signature,
            'TimeStamp' => $timestamp,
            'Nonce' => $nonce
        ]);
        return $crypt_xml;
    }

    /**
     * 未加密的数组消息转换成加密字符串
     */
    public function encode($response_arr){
        $crypt = new crypt($this->getEncodingAeskey());
        $decrypt_xml = $this->arrayToXml($response_arr);
        $result = $crypt->encrypt($decrypt_xml, $this->corpid);
        if ($result[0] != 0) {
            return $result[0];
        }
        //加密
        $encrypt = $result[1];
        return $encrypt;
    }

    /**
     * Encrypt加密字符串解密为xml字符串或者普通字符串
     * @param $echostr
     */
    public function decode($encrypt){
        //解密
        $crypt = new crypt($this->getEncodingAeskey());
        $result = $crypt->decrypt($encrypt, $this->corpid);
        if ($result[0] != 0) {
            return $result[0];
        }
        $decrypt = $result[1];
        return $decrypt;
    }

    /**
     * 数组转xml格式
     * @param $arr
     */
    public function arrayToXml($arr){
        $xml = '<xml>';
        foreach($arr as $k => $v){
            $xml .= (is_numeric($v)) ? "<{$k}>{$v}</{$k}>" : "<{$k}><![CDATA[{$v}]]></{$k}>";
        }
        $xml .= '</xml>';
        return $xml;
    }

    /**
     * xml转数组格式
     * @param $xml
     */
    public function xmlToArray($xml){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }

    /**
     * 生成随机字符串
     *
     * @return string
     */
    public function getRandomStr()
    {
        $str     = '';
        $str_pol = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyl';
        $max     = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }
}