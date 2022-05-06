<?php
//报警推送使能网页，仅允许config.php内的AlarmAminUserid参数指定的管理员可访问
	header('Content-type:text/html; charset=utf-8');
	session_start();
	require_once dirname(__FILE__) . "/../lib/helper.php";
	require_once dirname(__FILE__) . "/../lib/access_token.php";
	
	$appConfigs = loadConfig();//加载config.php配置文件
	$db_name = $appConfigs->MysqlDBname;//数据库名称、用户名、密码
	$db_user = $appConfigs->MysqlDBUser;
	$db_password = $appConfigs->MysqlDBPassword;
	$admin_user = $appConfigs->AlarmAminUserid;//管理员列表（注：多个管理员使用|分开）

	if ((!isset($_SESSION['alarm_islogin']))||(!isset($_SESSION['alarm_user_id'])))
	{
		$url="./no_access.php";//跳转到错误页面
		header("Location: {$url}");
		exit;//如果没有登陆，直接退出程序
	}
	
	$alarm_user_id=$_SESSION['alarm_user_id'];
	if(strpos($admin_user, '|')!=false)//这个判断是为了区分有没有多个管理员
	{
		$admin_list=explode('|',$admin_user);//使用|把字符串分割成数组
		if((array_search($alarm_user_id,$admin_list)===false)||(array_search($alarm_user_id,$admin_list)===NULL))//如果不是管理员(注意这里的判断必须是三个等号，否则索引为0也会进入)
		{
			$url="./no_access.php";//跳转到错误页面
			header("Location: {$url}");
			exit;//如果不是管理员，直接退出程序
		}
	}
	else
	{
		if(strcmp($alarm_user_id,$admin_user)!=0)
		{
			$url="./no_access.php";//跳转到错误页面
			header("Location: {$url}");
			exit;//如果不是管理员，直接退出程序
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes"/>
    <title>报警推送使能管理</title>
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
	<form action="./admin_submit.php" method="get">
	<div class="weui-cells__title">报警推送使能复选框</div>
	<div class="weui-cells weui-cells_checkbox">
<?php
	$con = mysql_connect("localhost",$db_user,$db_password); //MySQL 连接
	if (!$con)
  {
  	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db_name, $con);//选择 MySQL 数据库
	$result = mysql_query("SELECT * FROM alminfo");//选取alminfo数据表，并按数据表中的数据输出网页
	while($row = mysql_fetch_array($result))
  {
  	echo "<label class=\"weui-cell weui-check__label\" for=\"s".$row['id']."\">";
  	echo "<div class=\"weui-cell__hd\">";
  	if($row['trig']==1)
  	{
  		echo "<input type=\"checkbox\" class=\"weui-check\" name=\"s".$row['id']."\" id=\"s".$row['id']."\" checked=\"checked\" />";
  	}else
  	{
  		echo "<input type=\"checkbox\" class=\"weui-check\" name=\"s".$row['id']."\" id=\"s".$row['id']."\" />";
  	}
  	echo "<i class=\"weui-icon-checked\"></i>";
  	echo "</div>";
  	echo "<div class=\"weui-cell__bd\">";
  	echo "<p>".$row['descr']."</p>";
  	echo "</div>";
  	echo "</label>";
  }
	 
  
  mysql_close($con);//关闭 MySQL 连接
?>
<!--这一段是输出的模板
		<label class="weui-cell weui-check__label" for="s1">
	    <div class="weui-cell__hd">
        <input type="checkbox" class="weui-check" name="s1" id="s1" checked="checked" />
        <i class="weui-icon-checked"></i>
	    </div>
	    <div class="weui-cell__bd">
	       <p>束测</p>
	    </div>
		</label>
-->
	</div>

	<div style="text-align:center;">
		<button type="button" onclick="checkall()" class="weui-btn weui-btn_mini weui-btn_primary">全选</button>
		<button type="button" onclick="cleanall()" class="weui-btn weui-btn_mini weui-btn_primary">重置</button>
		<button class="weui-btn weui-btn_mini weui-btn_primary" type="submit">提交</button>
	</div>
	</form>
	
	<br>
	<div class="weui-footer">
  	<p class="weui-footer__text">Copyright &copy; CSNS Accelerator Control Group</p>
  </div>
<script>
	function checkall()
	{
	  var x= document.getElementsByTagName("input");
	  for (var i = 0; i < x.length; i++)
	  {
	  	x[i].checked = true;
	  }
	}
	
	function cleanall()
	{
	  var x= document.getElementsByTagName("input");
	  for (var i = 0; i < x.length; i++)
	  {
	  	x[i].checked = false;
	  }
	}
</script>
</body>
</html>
