<?php
	//require_once dirname(__FILE__) . "/helper.php";
	//$url="http://10.1.44.202:80/beamtime/sumbysec";//2018-08-06�޸ĳ���һ���Ǹ���ַ
	$url="http://10.1.236.136:80/beamtime/sumbysec";
	$url=$url.'?sdate='.$_GET['sdate'].'&edate='.$_GET['edate'];
	//$url="http://10.1.44.103:8090/neutron/count?sdate=2018-06-24 12:35:07&edate=2018-06-24 12:35:40";///��ַʾ��
	$url=str_replace(" ","%20",$url);//�ѿո��滻��%20����������
	//echo $url.'<br>';
	//$res=http_get($url);
	$ch = curl_init();  
	curl_setopt($ch,CURLOPT_URL,$url);  
	curl_setopt($ch,CURLOPT_HEADER,0);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );  
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  
	$res = curl_exec($ch);  
	curl_close($ch);
	//var_dump($res);
	echo $res;//������ص�ֵ
	//$json_obj = json_decode($res,true);
	//var_dump($json_obj);
?>