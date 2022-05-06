<?php
	header('Content-type:text/html; charset=utf-8');
	session_start();
	require_once dirname(__FILE__) . "/../lib/helper.php";
	
	if ((!isset($_SESSION['alarm_islogin']))||(!isset($_SESSION['alarm_user_id'])))
	{
		$url="./no_access.php";//跳转到错误页面
		header("Location: {$url}");
		exit;//如果没有登陆，直接退出程序
	}

	$appConfigs = loadConfig();//加载config.php配置文件
	$db_name = $appConfigs->MysqlDBname;//数据库名称、用户名、密码
	$db_user = $appConfigs->MysqlDBUser;
	$db_password = $appConfigs->MysqlDBPassword;
	
	$con = mysql_connect("localhost",$db_user,$db_password); //MySQL 连接
	if (!$con)
  {
  	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db_name, $con);//选择 MySQL 数据库
	
	$alarm_user_id=$_SESSION['alarm_user_id'];
	$sql = "DELETE FROM infosub WHERE userid='$alarm_user_id'";//先清除用户的订阅
	if(!mysql_query($sql,$con))//SQL 查询,返回一个资源标识符，如果查询执行不正确则返回 FALSE
	{
		die('Error: ' . mysql_error());//如果出错，显示错误		
	}
	for($i=1;$i<=98;$i++)//判断系统编号1~98，如果拓展了系统编号位数，这里需要修改
	{
		if(isset($_GET['s'.$i]))//如果有选中相应系统，添加数据库记录
		{
		  $sql = "INSERT IGNORE INTO infosub (userid,infoid)  values('$alarm_user_id','$i')";
			if(!mysql_query($sql,$con))//SQL 查询,返回一个资源标识符，如果查询执行不正确则返回 FALSE
			{
				die('Error: ' . mysql_error());//如果出错，显示错误
				//die() 函数输出一条消息，并退出当前脚本			
			}
		}
	}
	
	//以下用于在数据库中保存用户信息
	$alarm_user_name=$_SESSION['alarm_user_name'];
	$alarm_user_mobile=$_SESSION['alarm_user_mobile'];
	$alarm_user_mail=$_SESSION['alarm_user_email'];
	$sql = "DELETE FROM user WHERE id='$alarm_user_id'";//先删除原有的用户信息
	if(!mysql_query($sql,$con))
	{
		die('Error: ' . mysql_error());//如果出错，显示错误
	}
	$sql = "INSERT INTO user (id,name,mobile,mail)  values('$alarm_user_id','$alarm_user_name','$alarm_user_mobile','$alarm_user_mail')";//插入新用户信息
	if(!mysql_query($sql,$con))
	{
		die('Error: ' . mysql_error());			
	}
	
	mysql_close($con);//关闭 MySQL 连接
	
	$url="./infoEnquiry.php";//跳转到报警查询页面
	header("Location: {$url}"); 
?>