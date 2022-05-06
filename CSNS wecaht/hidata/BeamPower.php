<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Target Beam Power</title>
    <script src="mycharts.js"></script>
</head>
<body style="width:1002px;position:relative;left:-20px;">
<style>
#title{
  font-size:50px;
  font-family:verdana;
  text-align:center;
  background-color:#F8F8FF;
  font-weight:bold;
  position:relative;
  top:-50px;
}
</style>
		<div align="center">
			<img width="100%" height="150" src="logo.png"/>
		</div>
    <p id="title">束流打靶功率24小时历史曲线</p>
    <div id="main" style="width: 960px;height:800px;margin:0 auto;position:relative;top:-70px;left:20px;"></div>
    <script type="text/javascript">
	    var date = [];//x轴用当前时间生成，y轴数据由php发出http请求获得(函数的参数)
			var base = new Date();//获取现在的时间
			var oneDay = 24 * 3600 * 1000;
			var halfDay = 12 * 3600 * 1000;
			//var oneHours = 3600 * 1000;
			var oneMin = 60*1000;
			var halfMin = 30*1000;
			var now = new Date(base -= oneDay);//从过去24小时开始算起，每分钟一个点，填充时间数组
			for (var i = 0; i < 1440; i++) {
			  now = new Date(+now + oneMin);
			  
			  date.push([now.getHours(), now.getMinutes()].join(':')+" \n"+[(now.getMonth()+1), now.getDate()].join('/'));
			}
	    var data=<?php
			require_once dirname(__FILE__) . "/helper.php";
			//$res=http_get("http://10.1.44.202:8080/accst/histdata");
			$res=http_get("http://10.1.236.136:8080/accst/histdata");
			$hidatas=json_decode($res['content'],true);
			echo '[';
			for($i=0;$i<1439;$i++)//因为最后一个数字不需要逗号，所以不能用foreach
			{
				if($hidatas[$i]>0)
				{
					echo $hidatas[$i];
				}else
				{
					echo '0';
				}
				echo ',';
			}
			
			if($hidatas[1439]>0)
			{
				echo $hidatas[1439];
			}else
			{
				echo '0';
			}
			echo ']';
			?>;
			addcharts(data,date);
    </script>
    <p style="font-family:verdana;font-size:35px;text-align:center;position:relative;top:-70px;">注：数据采样率为1次/分钟</p>
    <div style="font-family:verdana;font-size:40px;font-weight:bold;text-align:center;position:relative;top:-70px;">
    	<span>24小时束流有效打靶时间：</span>
    	<span style="color:blue;">
    	<?php
				//$serverURL="http://10.1.236.136:8080/accst/timesum";//服务器地址，用于获取过去24小时出束时间
				$serverURL="http://10.1.236.136:8000/beamtime/24hr";//服务器地址，用于获取过去24小时出束时间
				$res=http_get($serverURL);
				$json_obj=json_decode($res['content'],true);
				//$timesum=$json_obj['beamTimeSum'];
				$timesum=$json_obj['beamTime24Hr'];
				echo $timesum;
			?>
			</span>
			<span> 小时</span>
    </div>
</body>
</html>