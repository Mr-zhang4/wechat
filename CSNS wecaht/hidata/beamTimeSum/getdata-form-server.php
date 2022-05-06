<?php
					require_once dirname(__FILE__) . "/helper.php";
					$res=http_get("http://10.1.236.136:8080/accst/timesum");
					$json_obj=json_decode($res['content'],true);
					echo $json_obj['beamTimeSum'];
					$datetime = date("Y-m-d H:i:s", time());//获取时间
					echo '<br>';
					echo $datetime;
?>