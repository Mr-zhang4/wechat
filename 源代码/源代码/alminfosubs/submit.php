<?php
	header('Content-type:text/html; charset=utf-8');
	session_start();
	require_once dirname(__FILE__) . "/../lib/helper.php";
	
	if ((!isset($_SESSION['alarm_islogin']))||(!isset($_SESSION['alarm_user_id'])))
	{
		$url="./no_access.php";//��ת������ҳ��
		header("Location: {$url}");
		exit;//���û�е�½��ֱ���˳�����
	}

	$appConfigs = loadConfig();//����config.php�����ļ�
	$db_name = $appConfigs->MysqlDBname;//���ݿ����ơ��û���������
	$db_user = $appConfigs->MysqlDBUser;
	$db_password = $appConfigs->MysqlDBPassword;
	
	$con = mysql_connect("localhost",$db_user,$db_password); //MySQL ����
	if (!$con)
  {
  	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db_name, $con);//ѡ�� MySQL ���ݿ�
	
	$alarm_user_id=$_SESSION['alarm_user_id'];
	$sql = "DELETE FROM infosub WHERE userid='$alarm_user_id'";//������û��Ķ���
	if(!mysql_query($sql,$con))//SQL ��ѯ,����һ����Դ��ʶ���������ѯִ�в���ȷ�򷵻� FALSE
	{
		die('Error: ' . mysql_error());//���������ʾ����		
	}
	for($i=1;$i<=98;$i++)//�ж�ϵͳ���1~98�������չ��ϵͳ���λ����������Ҫ�޸�
	{
		if(isset($_GET['s'.$i]))//�����ѡ����Ӧϵͳ��������ݿ��¼
		{
		  $sql = "INSERT IGNORE INTO infosub (userid,infoid)  values('$alarm_user_id','$i')";
			if(!mysql_query($sql,$con))//SQL ��ѯ,����һ����Դ��ʶ���������ѯִ�в���ȷ�򷵻� FALSE
			{
				die('Error: ' . mysql_error());//���������ʾ����
				//die() �������һ����Ϣ�����˳���ǰ�ű�			
			}
		}
	}
	
	//�������������ݿ��б����û���Ϣ
	$alarm_user_name=$_SESSION['alarm_user_name'];
	$alarm_user_mobile=$_SESSION['alarm_user_mobile'];
	$alarm_user_mail=$_SESSION['alarm_user_email'];
	$sql = "DELETE FROM user WHERE id='$alarm_user_id'";//��ɾ��ԭ�е��û���Ϣ
	if(!mysql_query($sql,$con))
	{
		die('Error: ' . mysql_error());//���������ʾ����
	}
	$sql = "INSERT INTO user (id,name,mobile,mail)  values('$alarm_user_id','$alarm_user_name','$alarm_user_mobile','$alarm_user_mail')";//�������û���Ϣ
	if(!mysql_query($sql,$con))
	{
		die('Error: ' . mysql_error());			
	}
	
	mysql_close($con);//�ر� MySQL ����
	
	$url="./infoEnquiry.php";//��ת��������ѯҳ��
	header("Location: {$url}"); 
?>