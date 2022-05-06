<?php
					//文件说明，执行本文件将向$serverURL配置的服务器地址请求过去24小时有效出束时间
					//如果请求失败，最多尝试3次
					//获得数据后保存到数据库
					//返回：程序会返回执行情况
					ini_set('max_execution_time','150');//默认超时时间30秒，改为150
					require_once dirname(__FILE__) . "/helper.php";
					//本文件用于把过去24小时有效供束时间写入数据库，在Crontab中定时每天同一时间执行此文件
					$serverURL="http://10.1.236.136:8000/beamtime/24hr";//服务器地址，用于获取过去24小时出束时间
					$res=http_get($serverURL);
					$json_obj=json_decode($res['content'],true);
					$timesum=$json_obj['beamTime24Hr'];
					$datetime = date("Y-m-d H:i:s", time());//获取时间
					if(is_null($timesum))//如果第一次请求失败
					{
						echo $datetime.' timesum is null\n';//如果连接失败或者没有得到正确参数，输出时间和错误信息
						sleep(10);/////延迟10秒后重新请求一次
						$res=http_get($serverURL);
						$json_obj=json_decode($res['content'],true);
						$timesum=$json_obj['beamTimeSum'];
						$datetime = date("Y-m-d H:i:s", time());//获取时间
						if(is_null($timesum))//如果第二次请求失败
						{
							echo $datetime.' timesum is null\n';//如果连接失败或者没有得到正确参数，输出时间和错误信息
							sleep(10);/////延迟10秒后重新请求一次
							$res=http_get($serverURL);
							$json_obj=json_decode($res['content'],true);
							$timesum=$json_obj['beamTimeSum'];
							$datetime = date("Y-m-d H:i:s", time());//获取时间
							if(is_null($timesum))//如果第三次请求失败
							{
								die($datetime.' timesum is null\n');//如果连接失败或者没有得到正确参数，输出时间和错误信息，并结束程序
							}
						}
					}
					$con = mysql_connect("localhost","root","csns2017."); //MySQL 连接
					if (!$con)
				  {
				  	die('Could not connect: ' . mysql_error());
				  }
				  mysql_select_db("hidata", $con);//选择 MySQL 数据库
				  $sql = "insert into beamTime (Date,TimeSum,TimeSum_raw)  values('$datetime','$timesum','$timesum')";
					if(!mysql_query($sql,$con))//SQL 查询,返回一个资源标识符，如果查询执行不正确则返回 FALSE
					{
						die('Error: ' . mysql_error());//如果出错，显示错误
						//die() 函数输出一条消息，并退出当前脚本			
					}
					mysql_close($con);//关闭 MySQL 连接
					echo $datetime." added record ".$json_obj['beamTimeSum']."\n";//返回执行成功的时间和信息
?>