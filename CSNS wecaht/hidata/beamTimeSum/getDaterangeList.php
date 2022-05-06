<?php
//本文件用于获取指定日期范围的日期列表数组
//如访问(本文件路径)?sdate=2018-06-01&edate=2018-06-12
//返回一个数组，数组包含起点到终点每一天的日期
    if(!isset($_GET['sdate'])||!isset($_GET['edate']))
    {
  	  exit;//没有附带日期参数则退出程序
  	}else
  	{
  		$sdate=$_GET['sdate'];//有给sdate参数，则按参数的日期
  		$edate=$_GET['edate'];
  	}
	  if($sdate <= $edate)//做一下判断，避免起始日期比结束日期还迟
  	{
	  	$date_start=$sdate;//日期起点
	  	$date_end=$edate;//日期终点
	  }else
	  {
	  	$date_start=$edate;//日期起点
	  	$date_end=$sdate;//日期终点
	  }
  	//var_dump($date_start);
  	$con = mysql_connect("localhost","root","csns2017."); //MySQL 连接
		if (!$con)
	  {
	  	die('Could not connect: ' . mysql_error());
	  }
	  mysql_select_db("hidata", $con);//选择 MySQL 数据库
	  $dateList = array();//定义数组存储从起点到终点每天的出束时间
	  $count = 0;
	  do{
	  	$date_next=date('Y-m-d',strtotime("$date_start +1 day"));
	  	$dateList[$count]=date('m-d',strtotime("$date_start"));;
			$date_start=$date_next;
			$count++;
	  }while($date_start <= $date_end);
	  mysql_close($con);//关闭 MySQL 连接
	  //var_dump($dateList);
	  echo json_encode($dateList);
?>