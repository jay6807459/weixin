<?php
//打印数据
function p($var, $isdie=TRUE, $type=null){
    if($type==1) die(var_dump($var));
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if($isdie) die;
}

/**
 * GET 请求
 * @param string $url
 */
function http_get($url, $param = array()){
    $url .= (strpos($url, '?') === false) ? '?' : '&';
    if(!empty($param)){
        foreach($param as $k => $v){
            $url .= $k . '=' . $v . '&';
        }
        $url = substr($url, 0, -1);
    }
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
    curl_setopt($oCurl, CURLOPT_HEADER, 1);

    // $sContent = curl_exec($oCurl);
    // $aStatus = curl_getinfo($oCurl);
    $sContent = execCURL($oCurl);
    curl_close($oCurl);
    return json_decode($sContent['content'], true);
}
/**
 * POST 请求
 * @param string $url
 * @param array $param
 * @param boolean $post_file 是否文件上传
 * @return string content
 */
function http_post($url,$param,$post_file=false){
    $oCurl = curl_init();

    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    if(PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')){
        $is_curlFile = true;
    }else {
        $is_curlFile = false;
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
        }
    }

    if($post_file) {
        if($is_curlFile) {
            foreach ($param as $key => $val) {
                if(isset($val["tmp_name"])){
                    $param[$key] = new \CURLFile(realpath($val["tmp_name"]),$val["type"],$val["name"]);
                }else if(substr($val, 0, 1) == '@'){
                    $param[$key] = new \CURLFile(realpath(substr($val,1)));
                }
            }
        }
        $strPOST = $param;
    }else{
        $strPOST = json_encode($param);
    }

    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($oCurl, CURLOPT_POST,true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
    curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
    curl_setopt($oCurl, CURLOPT_HEADER, 1);

    // $sContent = curl_exec($oCurl);
    // $aStatus  = curl_getinfo($oCurl);

    $sContent = execCURL($oCurl);
    curl_close($oCurl);

    return json_decode($sContent['content'], true);
}

/**
 * 执行CURL请求，并封装返回对象
 */
function execCURL($ch){
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    $result   = array( 'header' => '',
        'content' => '',
        'curl_error' => '',
        'http_code' => '',
        'last_url' => '');

    if ($error != ""){
        $result['curl_error'] = $error;
        return $result;
    }

    $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
    $result['header'] = str_replace(array("\r\n", "\r", "\n"), "<br/>", substr($response, 0, $header_size));
    $result['content'] = substr( $response, $header_size );
    $result['http_code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    $result['last_url'] = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
    $result["base_resp"] = array();
    $result["base_resp"]["ret"] = $result['http_code'] == 200 ? 0 : $result['http_code'];
    $result["base_resp"]["err_msg"] = $result['http_code'] == 200 ? "ok" : $result["curl_error"];

    return $result;
}

/**
 * 设置文件缓存
 * @param $file_path
 * @param $content
 */
function set_file_cache($file_path, $content){
    $fp = fopen($file_path, 'w');
    fwrite($fp, "<?php exit();?>" . $content);
    fclose($fp);
}


/**
 * 获取文件缓存
 * @param $file_path
 * @param $content
 */
function get_file_cache($file_path){
    if(file_exists($file_path)){
        return trim(substr(file_get_contents($file_path), 15));
    }else{
        return '{"expires_in":0}';
    }

}