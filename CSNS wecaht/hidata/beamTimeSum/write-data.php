<?php
					//�ļ�˵����ִ�б��ļ�����$serverURL���õķ�������ַ�����ȥ24Сʱ��Ч����ʱ��
					//�������ʧ�ܣ���ೢ��3��
					//������ݺ󱣴浽���ݿ�
					//���أ�����᷵��ִ�����
					ini_set('max_execution_time','150');//Ĭ�ϳ�ʱʱ��30�룬��Ϊ150
					require_once dirname(__FILE__) . "/helper.php";
					//���ļ����ڰѹ�ȥ24Сʱ��Ч����ʱ��д�����ݿ⣬��Crontab�ж�ʱÿ��ͬһʱ��ִ�д��ļ�
					$serverURL="http://10.1.236.136:8000/beamtime/24hr";//��������ַ�����ڻ�ȡ��ȥ24Сʱ����ʱ��
					$res=http_get($serverURL);
					$json_obj=json_decode($res['content'],true);
					$timesum=$json_obj['beamTime24Hr'];
					$datetime = date("Y-m-d H:i:s", time());//��ȡʱ��
					if(is_null($timesum))//�����һ������ʧ��
					{
						echo $datetime.' timesum is null\n';//�������ʧ�ܻ���û�еõ���ȷ���������ʱ��ʹ�����Ϣ
						sleep(10);/////�ӳ�10�����������һ��
						$res=http_get($serverURL);
						$json_obj=json_decode($res['content'],true);
						$timesum=$json_obj['beamTimeSum'];
						$datetime = date("Y-m-d H:i:s", time());//��ȡʱ��
						if(is_null($timesum))//����ڶ�������ʧ��
						{
							echo $datetime.' timesum is null\n';//�������ʧ�ܻ���û�еõ���ȷ���������ʱ��ʹ�����Ϣ
							sleep(10);/////�ӳ�10�����������һ��
							$res=http_get($serverURL);
							$json_obj=json_decode($res['content'],true);
							$timesum=$json_obj['beamTimeSum'];
							$datetime = date("Y-m-d H:i:s", time());//��ȡʱ��
							if(is_null($timesum))//�������������ʧ��
							{
								die($datetime.' timesum is null\n');//�������ʧ�ܻ���û�еõ���ȷ���������ʱ��ʹ�����Ϣ������������
							}
						}
					}
					$con = mysql_connect("localhost","root","csns2017."); //MySQL ����
					if (!$con)
				  {
				  	die('Could not connect: ' . mysql_error());
				  }
				  mysql_select_db("hidata", $con);//ѡ�� MySQL ���ݿ�
				  $sql = "insert into beamTime (Date,TimeSum,TimeSum_raw)  values('$datetime','$timesum','$timesum')";
					if(!mysql_query($sql,$con))//SQL ��ѯ,����һ����Դ��ʶ���������ѯִ�в���ȷ�򷵻� FALSE
					{
						die('Error: ' . mysql_error());//���������ʾ����
						//die() �������һ����Ϣ�����˳���ǰ�ű�			
					}
					mysql_close($con);//�ر� MySQL ����
					echo $datetime." added record ".$json_obj['beamTimeSum']."\n";//����ִ�гɹ���ʱ�����Ϣ
?>