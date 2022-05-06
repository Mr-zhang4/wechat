<?php
	header('Content-type:text/html; charset=utf-8');
	// 开启Session
	session_start();
	
		
	// 处理用户登录信息
	if (isset($_POST['login'])) {
		# 接收用户的登录信息
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$auth_user=false;
		// 判断提交的登录信息
		if (($username == 'admin') && ($password == 'time2019'))
		{
			$auth_user=true;
		}else
		{
			$auth_user=false;
		}
	
		if ($auth_user==false) {
			# 用户名或密码错误,同空的处理方式
			$_SESSION['islogin'] = 0;
			header('refresh:3; url=login.html');
			echo "用户名或密码错误,将在3秒后回到登录界面";
			exit;
		} else{
			# 用户名和密码都正确,将用户信息存到Session中
			$_SESSION['username'] = $username;
			$_SESSION['islogin'] = 1;
			//跳转到修改页
			header('location:modify.html');
		}
	}
	
	
?>
