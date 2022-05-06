<?php
//本文件用于获取指定日期范围的出束时间总和
//如访问(本文件路径)?sdate=2018-06-01
//返回一个有两个数据的数组，分别是当天的最新修改的出束时间和原始出束时间
  header('Content-type:text/html; charset=utf-8');
	// 开启Session
	session_start();
	//处理提交的修改数据请求
	if (isset($_POST['modify'])) {
		# 接收用户的登录信息
		$sdate = trim($_POST['sdate']);
		$newdata = trim($_POST['newdata']);
		//echo 'sdate='.$sdate;
		//echo 'newdata='.$newdata;
		if (!isset($_SESSION['islogin'])||($_SESSION['islogin']!=1))
		{
			header('refresh:3; url=login.html');
			echo "请先登录，3秒后将跳转登陆页面";
			exit;
		}
		if (empty($sdate))//不能或empty($newdata)，那样$newdata为0的时候也会跳转
		{
			header('refresh:3; url=modify.html');
			echo "日期为空，3秒后将跳转回网页";
			exit;
		}
		if (!is_numeric($newdata))//假如说输入框为空或者输入了其它字符，这一步会跳转
		{
			header('refresh:3; url=modify.html');
			echo "您提交的修正值不是数字，3秒后将跳转回网页";
			exit;
		}
		
		$con = mysql_connect("localhost","root","csns2017."); //MySQL 连接
		if (!$con)
	  {
	  	die('Could not connect: ' . mysql_error());
	  }
	  mysql_select_db("hidata", $con);//选择 MySQL 数据库
	  $date_next=date('Y-m-d',strtotime("$sdate +1 day"));
	  $result = mysql_query("UPDATE beamTime SET TimeSum = '$newdata' WHERE Date > '$sdate' and Date < '$date_next'");
	  mysql_close($con);//关闭 MySQL 连接
	  if($result)
	  {
	  	header('refresh:2; url=modify.html');
			echo "修改成功，正在跳转回网页";
			exit;
	  }
	  else
		{
			header('refresh:3; url=modify.html');
			echo "修改数据库失败，3秒后将跳转回网页";
			exit;
		}
	  
	}
?>