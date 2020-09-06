<?php

//database variables
$host = 'localhost';
$user = 'root';
$pass = 'fXDwUldNcxWi31Vp';
$dbname = 'ticket';

//connect to the mysql database
$mysql = mysql_connect($host,$user,$pass);
$db = mysql_select_db($dbname,$mysql);
mysql_set_charset('utf8', $mysql);

//grab config vars
function config($config) {
	$get_config = mysql_fetch_array(mysql_query("select * data"));
	$config = $get_config[$config];
	return $config;
}

function access() {
	if($_COOKIE['user']){
		$user = $_COOKIE['user'];
		$pass = $_COOKIE['pass'];
		$checkuser = mysql_query("SELECT * FROM ticket_config WHERE user='$user' AND pass='$pass'");
		$returned = mysql_num_rows($checkuser);
		if($returned=="1"){
			//вход выполнен
		}
		else{ //возврат на попытку входа если логин/пароль ВВЕДЕНЫ неверные
			include("templates.php");
			echo $login_header; //форма логин/пароль
?>
			<form action="login.php" method="post">
			<table cellspacing="1" cellpadding="4" border="0" bgcolor="#f2f2f2">
			<tr><td width="130">User</td><td><input type="text" name="user" /></td></tr>
			<tr><td>Password</td><td><input type="password" name="pass" /></td></tr>
			</table><br />
			<input type="hidden" name="referer" value="<? echo $_SERVER['REQUEST_URI']; ?>" />
			<input type="submit" value="Login" name="login" />
			</form>
<?
			echo $footer;
			exit;
		}

	}else{ //возврат если логин/пароль не существуют

?>
		<form action="login.php" method="post">
		<table cellspacing="1" cellpadding="4" border="0" bgcolor="#f2f2f2">
		<tr><td width="130">User</td><td><input type="text" name="user" /></td></tr>
		<tr><td>Password</td><td><input type="password" name="pass" /></td></tr>
		</table><br />
		<input type="hidden" name="referer" value="<? echo $_SERVER['REQUEST_URI']; ?>" />
		<input type="submit" value="Login" name="login" />
		</form>
<?
	exit;
	}
}
?>