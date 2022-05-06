<?php
	header('Content-type:text/html; charset=utf-8');
	session_start();
	require_once dirname(__FILE__) . "/../lib/helper.php";

	$appConfigs = loadConfig();//����config.php�����ļ�
	$db_name = $appConfigs->MysqlDBname;//���ݿ����ơ��û���������
	$db_user = $appConfigs->MysqlDBUser;
	$db_password = $appConfigs->MysqlDBPassword;
	$admin_user = $appConfigs->AlarmAminUserid;//����Ա�б���ע���������Աʹ��|�ֿ���
	
	if ((!isset($_SESSION['alarm_islogin']))||(!isset($_SESSION['alarm_user_id'])))
	{
		$url="./no_access.php";//��ת������ҳ��
		header("Location: {$url}");
		exit;//���û�е�½��ֱ���˳�����
	}
	
	$alarm_user_id=$_SESSION['alarm_user_id'];
	if(strpos($admin_user, '|')!=false)//����ж���Ϊ��������û�ж������Ա
	{
		$admin_list=explode('|',$admin_user);//ʹ��|���ַ����ָ������
		if((array_search($alarm_user_id,$admin_list)===false)||(array_search($alarm_user_id,$admin_list)===NULL))//������ǹ���Ա(ע��������жϱ����������Ⱥţ���������Ϊ0Ҳ�����)
		{
			$url="./no_access.php";//��ת������ҳ��
			header("Location: {$url}");
			exit;//������ǹ���Ա��ֱ���˳�����
		}
	}
	else
	{
		if(strcmp($alarm_user_id,$admin_user)!=0)
		{
			$url="./no_access.php";//��ת������ҳ��
			header("Location: {$url}");
			exit;//������ǹ���Ա��ֱ���˳�����
		}
	}
	
	$con = mysql_connect("localhost",$db_user,$db_password); //MySQL ����
	if (!$con)
  {
  	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db_name, $con);//ѡ�� MySQL ���ݿ�
  
  $sql = "UPDATE alminfo SET trig=0";//��ȡ������ϵͳ��ʹ��
	if(!mysql_query($sql,$con))//SQL ��ѯ,����һ����Դ��ʶ���������ѯִ�в���ȷ�򷵻� FALSE
	{
		die('Error: ' . mysql_error());//�����������ʾ����		
	}
	
	for($i=1;$i<=98;$i++)//�ж�ϵͳ���1~98�������չ��ϵͳ���λ����������Ҫ�޸�
	{
		if(isset($_GET['s'.$i]))//�����ѡ����Ӧϵͳ���������ݿ��¼
		{
		  $sql = "UPDATE alminfo SET trig=1 WHERE id=$i";
			if(!mysql_query($sql,$con))//SQL ��ѯ,����һ����Դ��ʶ���������ѯִ�в���ȷ�򷵻� FALSE
			{
				die('Error: ' . mysql_error());//�����������ʾ����
				//die() �������һ����Ϣ�����˳���ǰ�ű�			
			}
		}
	}

	
	mysql_close($con);//�ر� MySQL ����
	
	$url="./successful.php";//��ת��������ѯҳ��
	header("Location: {$url}"); 
?>