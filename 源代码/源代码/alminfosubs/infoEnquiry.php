<?php
//用户订阅查询网页
	header('Content-type:text/html; charset=utf-8');
	session_start();
	require_once dirname(__FILE__) . "/../lib/helper.php";
	if ((!isset($_SESSION['alarm_islogin']))||(!isset($_SESSION['alarm_user_id'])))
	{
		$url="./no_access.php";//跳转到错误页面
		header("Location: {$url}");
		exit;//如果没有登陆，直接退出程序
	}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes"/>
    <title>报警推送订阅</title>
    <link rel="stylesheet" type="text/css" href="style/weui.css">
</head>
<body style="width:90%;margin:0 auto;">
<style>
/*覆盖几个weui库的参数*/
.weui-cell{
   height:8px;
}

.weui-cells__title{
	font-size:18px;
  font-weight:bold;
}
</style>
<div align="center">
	<img width="100%" height="80" src="logo.png"/>
</div>
	<div class="weui-cells__title">订阅列表</div>
<?php
//读取当前用户的订阅列表，并输出
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
	$result = mysql_query("SELECT * FROM infosub WHERE userid='$alarm_user_id'");//按用户id搜索订阅的系统
	if($result)
	{
		$i=0;
		while($row = mysql_fetch_array($result))
	  {
	  	$id=$row['infoid'];
	  	$result2 = mysql_query("SELECT * FROM alminfo WHERE id='$id'");//按订阅的系统ID搜索系统名称，用于网页显示
	  	if($result2)
	  	{
	  		$row2 = mysql_fetch_array($result2);
	  		echo "<div class=\"weui-cell weui-cell__bd\"><p>• ".$row2['descr']."</p></div>";
	  	}
	  	$i++;
	  }
	}
	if($i==0)
	{
		echo "<div class=\"weui-cell weui-cell__bd\"><p>您的订阅列表为空</p></div>";
	}
  
  mysql_close($con);//关闭 MySQL 连接
?>
	<div class="weui-cell weui-cell__bd"></div>
	
	<br>
	<div class="weui-footer">
  	<p class="weui-footer__text">Copyright &copy; CSNS Accelerator Control Group</p>
  </div>
<script>
</script>
</body>
</html>
