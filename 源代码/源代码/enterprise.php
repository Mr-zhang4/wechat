<?php

	require_once dirname(__FILE__) . "/lib/msgcrypt.php";
	require_once dirname(__FILE__) . "/lib/helper.php";
	require_once dirname(__FILE__) . "/lib/app_api.php";

//	//企业号在企业微信后台设置的参数如下
		//读取config文件里面的配置
	$appConfigs = loadConfig();//需要在函数里面设置路径
	$agentid = $appConfigs->AlarmAgentId;//使用的应用id
	$config = getConfigByAgentId($agentid);
	$corpId = $appConfigs->CorpId;
	$token = $config->Token;
	$encodingAesKey = $config->EncodingAESKey;	
	$corpId = $appConfigs->CorpId;
		
	if(!isset($_GET['echostr'])) //isset()在php中用来检测变量是否设置，该函数返回的是布尔类型的值，即true/false
	{
		//调用响应消息函数
		receiveMsgFromQyWx($encodingAesKey,$token,$corpId);
	}
	else
	{
		//验证回调URL
		valid($encodingAesKey,$token,$corpId);
	}

	function valid($encodingAesKey,$token,$corpId)
	{
//		$sVerifyMsgSig = urldecode($_GET["msg_signature"]);//官方手册说要urldecose，但实测加了反而解码错误
//		$sVerifyTimeStamp = urldecode($_GET["timestamp"]);
//    $sVerifyNonce = urldecode($_GET["nonce"]);
//    $sVerifyEchoStr = urldecode($_GET["echostr"]);
		$sVerifyMsgSig = $_GET["msg_signature"];
		$sVerifyTimeStamp = $_GET["timestamp"];
    $sVerifyNonce = $_GET["nonce"];
    $sVerifyEchoStr = $_GET["echostr"];


    // 需要返回的明文
    $sEchoStr = "";
    
//      $myfile = fopen("cache3.txt", "w") or die("Unable to open file!");/////////////////////////////////////////
//			fwrite($myfile, $sVerifyMsgSig);
//			fclose($myfile);

    $wxcpt = new MsgCrypt($token, $encodingAesKey, $corpId);
    $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);

    if ($errCode == 0) {
        // 验证URL成功，将sEchoStr返回
      ob_clean();
      echo $sEchoStr;
      //print($sEchoStr);
//      $myfile = fopen("cache.txt", "w") or die("Unable to open file!");//////////////////////////////////////////////////////////
//			fwrite($myfile, $sEchoStr);
//			fclose($myfile);
        exit;
    } else {
        print("ERR: " . $errCode . "\n\n");
        			$myfile = fopen("cache.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $errCode);
			fclose($myfile);
    }
	}
	
		function receiveMsgFromQyWx($encodingAesKey,$token,$corpId){
		
		$sReqMsgSig = $_GET["msg_signature"];	
		$sReqTimeStamp = $_GET["timestamp"];	
		$sReqNonce = $_GET["nonce"];	
		$sReqData = file_get_contents("php://input");		

		$sMsg = "";  // 解析之后的明文
		$wxcpt = new MsgCrypt($token,$encodingAesKey,$corpId);
		$errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);

		if ($errCode == 0) {
			// 解密成功，sMsg即为xml格式的明文 			
//			$xml = new DOMDocument();
//			$xml->loadXML($sMsg);
//			$FromUserName = $xml->getElementsByTagName('FromUserName')->item(0)->nodeValue;  //发送消息的UserID
//			$content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;  //发送的消息内容体
			$object = simplexml_load_string($sMsg,"SimpleXMLElement",LIBXML_NOCDATA);
			$MsgType = $object->MsgType;//消息类型
			$content =$object->Content;//文本消息内容
		
		 	switch ($MsgType) //消息类型
			{
		 		case 'event'://接收事件推送为event
		 			$sRespData=receiveEvent($object);	
		 			$sEncryptMsg = ""; //xml格式的密文
					$wxcpt = new MsgCrypt($token,$encodingAesKey,$corpId);
					$errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);
					if ($errCode == 0) {			
						// 加密成功，企业需要将加密之后的sEncryptMsg返回
						echo $sEncryptMsg;
					} else {
						print("ERR: " . $errCode . "\n\n");
						// exit(-1);
					}
		 			break;	
					
		 		case 'text'://文本消息为text
		 		  $sRespData=receiveText($object,$content);//需要回复的明文，以下再加密后发送
		 		  $sEncryptMsg = ""; //xml格式的密文
					$wxcpt = new MsgCrypt($token,$encodingAesKey,$corpId);
					$errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);
					if ($errCode == 0) {			
						// 加密成功，企业需要将加密之后的sEncryptMsg返回
						echo $sEncryptMsg;
					} else {
						print("ERR: " . $errCode . "\n\n");
						// exit(-1);
					}
		 			break;
					
		 		case 'voice'://接收语音消息为voice
				
		 			break; 
		 		default: 
		 			break;
		 	}
			
		} else {
			print("ERR: " . $errCode . "\n\n");	
		}
	}
	
	//接收事件推送函数
	function receiveEvent($obj)
	{
		switch ($obj->Event) 
		{ 
			//关注事件
			case 'subscribe':
				//扫描带参数的二维码，用户未关注时，进行关注后的事件
                //$text = "欢迎关注，点击下方菜单栏查询运行参数。";
				return replyText($obj,$text);
				break;
				
			//取消关注事件
			case 'unsubscribe':
				break;
				
			//扫描带参数的二维码，用户已关注时，进行关注后的事件
			case 'SCAN':
				//做相关的处理
				break;
				
			//自定义菜单事件
			case 'click':
				switch ($obj->EventKey) 
				{
					case 'status':
					  return receiveText($obj,"status");
						break;
					case 'table':
						return receiveText($obj,"table");
						break;
					case 'ctdata':
						return receiveText($obj,"ctdata");
						break;
					case 'dtl':
						return receiveText($obj,"dtlclick");
						break;
					case 'fevac':
						return receiveText($obj,"fevacclick");
						break;
					default:
						break;
				}
				
				break;
		}	
	}	

	//接收文本消息
	function receiveText($obj,$content)
	{
        // $content = $obj ->Content;
    if (strstr($content, "status"))//不同关键词，此处保留几个用于示例
		{
    		$reply="如需帮助，请联系管理员。";
    		return replyText($obj,$reply);
    }
    else if (strstr($content, "table")) 
		{
    		$reply="如需帮助，请联系管理员。";
    		return replyText($obj,$reply);
    }
    else
    {
    		$reply="如需帮助，请联系管理员。";
    		return replyText($obj,$reply);
    }
        //关闭
		
        //获取文本消息的内容
        //$content = $obj->Content;
		//发送文本消息
		#return replyStatus($obj,$reply);   
         
	}
	//发送文本消息
	function replyText($obj,$content)
	{
		$replyXml = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>";
			//返回一个进行xml数据包

		$resultStr = sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),$content);
		return $resultStr;		
	}
	
		function replyStatus($obj,$content)
	{
		$replyXml = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<Content><![CDATA[]]></Content>
    			<ArticleCount>1</ArticleCount>
   					<Articles>
   					<item>
          	<Title><![CDATA[运行状态]]></Title>
          	<Description><![CDATA[%s]]>
   					</Description>
          	<PicUrl><![CDATA[]]></PicUrl>
          	<Url><![CDATA[]]></Url>
        		</item>
    				</Articles>
    				<FuncFlag>0</FuncFlag>
					</xml>";
			//返回一个进行xml数据包

		$resultStr = sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),$content);
		return $resultStr;		
	}

	
?>
	