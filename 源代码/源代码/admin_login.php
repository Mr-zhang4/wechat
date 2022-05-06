<?php  
	header('Content-type:text/html; charset=utf-8');
	session_start();
	
	require_once dirname(__FILE__) . "/lib/access_token.php";
	require_once dirname(__FILE__) . "/lib/helper.php";
	
	$appConfigs = loadConfig();//加载config.php配置文件
	$agentid = $appConfigs->AlarmAgentId;//使用的应用id

//在微信里面访问下面这个地址，参数需要根据实际修改
//https://open.weixin.qq.com/connect/oauth2/authorize?appid=企业号ID&redirect_uri=http://服务器域名/alminfosubs/admin_login.php&response_type=code&scope=snsapi_privateinfo&agentid=1000002&state=1#wechat_redirect
	$code = $_GET["code"];  
	$alarm_ins  = new AccessToken($agentid);
	$access_token=$alarm_ins->getAccessToken();
	$get_userid_url = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token='.$access_token.'&code='.$code;
	  
	$ch = curl_init();  
	curl_setopt($ch,CURLOPT_URL,$get_userid_url);  
	curl_setopt($ch,CURLOPT_HEADER,0);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );  
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  
	$res = curl_exec($ch);  
	curl_close($ch);  
	$json_obj = json_decode($res,true);  
	  
	//根据openid和access_token查询用户信息  
	$userid = $json_obj['UserId'];  
	$user_ticket = $json_obj['user_ticket'];
	$openid = $json_obj['OpenId'];
	//echo $userid;/////////////////////////////////
	//echo $user_ticket;/////////////////////////////
	
	if(!empty($userid))//如果有userid，表示是企业用户
	{
//			$get_user_detail_url = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserdetail?access_token='.$access_token;  
//			$postdata = array("user_ticket" => $user_ticket);
//			$res=http_post($get_user_detail_url,$postdata);//函数里面已经做了json_encode处理，并且对返回值做了封装，所以和前面的处理不一样
//			$user_obj = json_decode($res['content'],true);
		//print_r($user_obj);
		//echo $user_obj['name'];
		//echo $user_obj['mobile'];
		//echo $user_obj['email'];
		//print_r($res);
		$_SESSION['alarm_islogin'] = 1;//在session保存用户信息
		$_SESSION['alarm_user_id'] = $userid;
		$url="./alminfosubs/admin.php";
		header("Location: {$url}"); 
		
	}else if(!empty($openid))//非企业用户
	{
		$_SESSION = array();
		session_destroy();
		$url="./alminfosubs/no_access.php";//跳转到错误页面
		header("Location: {$url}");
	}else//授权失败
	{
		$_SESSION = array();
		session_destroy();
		$url="./alminfosubs/no_access.php";//跳转到错误页面
		header("Location: {$url}");
	}

?>
	