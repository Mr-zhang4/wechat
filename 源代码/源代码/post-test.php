<?php

	ini_set('display_errors',1); 
	ini_set('display_startup_errors',1);
	ini_set('error_log',dirname(__FILE__) . '/post_test_log.txt');
	error_reporting(E_ALL ^ E_DEPRECATED);//���������Ϣ
	
	//���ļ����ڲ���post�������ݵ�����������������������������Ӳ��ԣ�
	//http://www.csnsac.cn/post-test.php
	
	$posturl="http://����������/alarm-post.php";///////////////////////////��ĳ�ʵ������/////////////////////////////////////////////
	$bodydata="param1=99%E7%9C%9F%E7%A9%BA&param2=99%E6%8E%A7%E5%88%B6";///ģ�ⷢ�͵����ݣ�ע�������99��ϵͳ�ţ�01~98��ʹ�õ�ϵͳ�ţ�99��alarm-post.php���������˹̶����͸�����Ա
	echo post_data($posturl,$bodydata);//ִ���겢������ؽ��


//����Ϊ��װ�õ�post����
	function post_data($url,$param){
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
    
    //$strPOST = json_encode($param);
    $strPOST = $param;
    
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

    return $sContent;
	}
	
	/**
	 * ִ��CURL���󣬲���װ���ض���
	 */
	function execCURL($ch){
    $response = curl_exec($ch);
//    $error    = curl_error($ch);
//    $result   = array( 'header' => '', 
//                     'content' => '', 
//                     'curl_error' => '', 
//                     'http_code' => '',
//                     'last_url' => '');
//    
//    if ($error != ""){
//        $result['curl_error'] = $error;
//        return $result;
//    }
//
//    $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
//    $result['header'] = str_replace(array("\r\n", "\r", "\n"), "<br/>", substr($response, 0, $header_size));
//    $result['content'] = substr( $response, $header_size );
//    $result['http_code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
//    $result['last_url'] = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
//    $result["base_resp"] = array();
//    $result["base_resp"]["ret"] = $result['http_code'] == 200 ? 0 : $result['http_code'];
//    $result["base_resp"]["err_msg"] = $result['http_code'] == 200 ? "ok" : $result["curl_error"];
//
//    return $result;
			return $response;
	}
	
?>

 