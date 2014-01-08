<?php
session_start(); //Session starten
include_once('../connection.php');
include_once('../functions.php');
include_once('../user.php');
if(isset($_POST['login']) && isset($_POST['password'])) {
	if(Statistics::userexists($_POST['login'])) {
		$user = new User($_POST['login'], $db);	
		$checklogin = $user->login($_POST['password']);
		if($checklogin === true) {
			echo 'true';
		}elseif($checklogin == 2) {
			echo 'login?unvalidated='.$_POST['login'];
		}elseif ($checklogin === false) {
			echo'login?loginattempt=false';
		}
	}else {
		echo 'login?notexisting';
	}
}elseif(isset($_SESSION['user'])) {
	$user = new User($_SESSION['user'], $db);
	$checklogin = $user->logged;
}else {
	echo 'notsent';//Nichts gesendet
}
$statistics = new Statistics($db, $user);