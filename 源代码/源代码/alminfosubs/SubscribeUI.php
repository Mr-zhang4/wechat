<?php
//用户订阅设置网页
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
	<form action="./submit.php" method="get">
	<div class="weui-cells__title">报警订阅复选框</div>
	<div class="weui-cells weui-cells_checkbox">
		
<?php
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
	$result = mysql_query("SELECT * FROM infosub WHERE userid='$alarm_user_id'");//搜索用户订阅数据
	$checked = array();//空数组，用于保存当前用户订阅的系统列表
	if($result)
	{
		while($row = mysql_fetch_array($result))
	  {
	  	$checked[$row['infoid']]=1;
	  }
	 }
	 
	$result2 = mysql_query("SELECT * FROM alminfo");//选取alminfo数据表，并按数据表中的数据输出网页
	while($row2 = mysql_fetch_array($result2))
  {
  	echo "<label class=\"weui-cell weui-check__label\" for=\"s".$row2['id']."\">";
  	echo "<div class=\"weui-cell__hd\">";
  	echo "<input type=\"checkbox\" class=\"weui-check\" name=\"s".$row2['id']."\" id=\"s".$row2['id']."\"";
  	if($checked[$row2['id']]==1)
  	{
  		echo "checked=\"checked\"";
  	}
  	echo " />";
  	echo "<i class=\"weui-icon-checked\"></i>";
  	echo "</div>";
  	echo "<div class=\"weui-cell__bd\">";
  	echo "<p>".$row2['descr']."</p>";
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
