<?php

	ini_set('max_execution_time','100');//默认超时时间30秒，改为100
	ini_set('display_errors',1); 
	ini_set('display_startup_errors',1);
	ini_set('error_log',dirname(__FILE__) . '/alarm_post_log.txt');
	error_reporting(E_ALL ^ E_DEPRECATED);//输出错误信息
	
	require_once dirname(__FILE__) . "/lib/msgcrypt.php";
	require_once dirname(__FILE__) . "/lib/helper.php";
	require_once dirname(__FILE__) . "/lib/app_api.php";
	
	$appConfigs = loadConfig();//加载config.php配置文件
	$agentid = $appConfigs->AlarmAgentId;//使用的应用id
	$admin_user = $appConfigs->AlarmAminUserid;//管理员列表，程序设置99号系统固定推送给管理员（注：多个管理员在配置文件中使用|分开）
	$db_name = $appConfigs->MysqlDBname;//数据库名称、用户名、密码
	$db_user = $appConfigs->MysqlDBUser;
	$db_password = $appConfigs->MysqlDBPassword;
	
	$bodydata=file_get_contents("php://input");
	$bodydatas=explode("&",$bodydata);//把body用&分割成字符串数组
	//var_dump(explode("&",$bodydata));
	//var_dump($bodydatas);
	
	$errcount=0;
	$con = mysql_connect("localhost",$db_user,$db_password); //MySQL 连接
	if (!$con)
	{
		exit();
		echo "errcount=1";
	}		
	mysql_select_db($db_name, $con);
	foreach ($bodydatas as $bodydata)
	{
		$param=explode("=",$bodydata);//用=号分割获取参数
		//echo $param[1];
		$syscode=substr($param[1],0,2);//获取系统编码
		//$syscode=intval($syscode);
		$alarmdes=substr($param[1],2);//获取报警描述
		//$alarmdes=mb_convert_encoding(urldecode($alarmdes),"GBK","UTF-8");//解码
		$alarmdes=urldecode($alarmdes);
		
		//2018年4月10日新增，当数据库中系统的trig为1时才发送报警消息
		$result = mysql_query("SELECT * FROM alminfo WHERE id=$syscode");
		if($result)
		{
			$row = mysql_fetch_array($result);
			if($row)
			{
				$trig=$row['trig'];
			}
		}
		
		if((!empty($trig)&&($trig==1))||(!empty($syscode)&&($syscode == 99)))//2018年4月10日新增，当数据库中系统的trig为1时才发送报警消息
		{
			if(!empty($syscode)&&($syscode >= 0)&&($syscode < 98))//如果系统号无误，发送给订阅的用户
			{
				
				$result = mysql_query("SELECT * FROM infosub WHERE infoid=$syscode");
				if($result)
				{
					$userlist='';
					while($row = mysql_fetch_array($result))
				  {
				  $userlist=$userlist.$row['userid']."|";
				  }
				  if($userlist)
				  {
				  	$userlist = rtrim($userlist,"|");
				  	//echo $alarmdes;///////////调试信息
				  	//echo $userlist;///////////调试信息
				  	if(pushAlarmMsgTest($userlist,$alarmdes)){//推送消息，如果发送失败统计次数
				  		$errcount+=1;
				  	}
				  	//pushTextMsgTest();
				  }else
				  {
				  	//echo "no user";///////////调试信息
				  }
				 }
			}else if(!empty($syscode)&&($syscode == 99))//如果是99号系统，即为调试信息，固定推送给管理员
			{
				if(pushAlarmMsgTest($admin_user,$alarmdes)){//推送消息，如果发送失败统计次数
				  	$errcount+=1;
				  }
			}
		}
	}
	mysql_close($con);
	echo "errcount=$errcount";
	exit();
	
	function pushAlarmMsgTest($userlist,$alarmdes)
	{
		global $agentid;
		$msg = array(
		'touser'=>$userlist, 
		'toparty'=>'', 
		'msgtype'=>'text',
		'agentid'=>$agentid,
		'text'=>array(
		"content"=>"报警提示：\n  $alarmdes"		
		)
		);
		
		$api = new APP_API($agentid);
		
		//var_dump($api->sendMsgToUser($msg));
		$retdat=$api->sendMsgToUser($msg);
		$json_obj = json_decode($retdat,true);
		//echo $retdat;
		//echo "errmsg=";
		//echo $json_obj['errmsg'];
		if($json_obj['errmsg']=="ok"){//如果发送错误，返回1
			return 0;
		}else{
			return 1;
		}
	}
	
?>

 