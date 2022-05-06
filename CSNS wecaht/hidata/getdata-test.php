<?php
					require_once dirname(__FILE__) . "/helper.php";
					$res=http_get("http://10.1.236.136:8080/accst/histdata");
					$hidatas=json_decode($res['content'],true);
					echo '[';
					for($i=0;$i<1439;$i++)//因为最后一个数字不需要逗号，所以不能用foreach
					{
						echo $hidatas[$i];
						echo ',';
					}
					echo $hidatas[1439];
					echo ']';
?>