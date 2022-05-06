<?php
//本文件用于获取指定日期范围的出束时间总和
//如访问(本文件路径)?sdate=2018-06-01
//返回一个有两个数据的数组，分别是当天的最新修改的出束时间和原始出束时间
    if(!isset($_GET['sdate']))
    {
  	  exit;//没有附带日期参数则退出程序
  	}else
  	{
  		$sdate=$_GET['sdate'];//有给sdate参数，则按参数的日期
  	}
  	$con = mysql_connect("localhost","root","csns2017."); //MySQL 连接
		if (!$con)
	  {
	  	die('Could not connect: ' . mysql_error());
	  }
	  mysql_select_db("hidata", $con);//选择 MySQL 数据库
  	$date_next=date('Y-m-d',strtotime("$sdate +1 day"));
  	$result = mysql_query("SELECT * FROM beamTime WHERE Date > '$sdate' and Date < '$date_next'");//搜索指定日期
	  if($result)
		{
			$row = mysql_fetch_array($result);//获取搜索结果的第一行
			if($row)//如果有数据
			{
				$time_sum=$row['TimeSum'];
				$time_sum_raw=$row['TimeSum_raw'];
				//echo $row['TimeSum'].'<br>';
				//返回结果
			  echo '[';
			  echo $time_sum;
			  echo ',';
			  echo $time_sum_raw;
			  echo ']';
			}
		}
	  mysql_close($con);//关闭 MySQL 连接
	  exit;
?>