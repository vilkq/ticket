<?php

include("config.php");

//logout procedure
if($_GET['do']=="kill"){
	setcookie ("user");
	setcookie ("pass");
	header("Location: index.php");
}

//checking user details
$user = $_POST['user'];
$pass = md5($_POST['pass']);
$checkuser = mysql_query("SELECT * FROM ticket_config WHERE user='$user' AND pass='$pass'");
$returned = mysql_num_rows($checkuser);

if($returned=="1"){
	$referer = $_POST['referer'];
	setcookie ("user", $user, time()+11520000);
	setcookie ("pass", $pass, time()+11520000);
	header("Location: index.php");
}else{
	header("Location: index.php");
}
?>