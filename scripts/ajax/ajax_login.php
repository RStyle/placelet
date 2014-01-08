<?php
session_start(); //Session starten
include_once('../connection.php');
include_once('../functions.php');
include_once('../user.php');
if($_POST['login'] != '' && $_POST['password'] != '') {
	if(Statistics::userexists($_POST['login'])) {
		$user = new User($_POST['login'], $db);	
		$checklogin = $user->login($_POST['password']);
		if($checklogin === true) {
			echo 'true';
		}elseif($checklogin == 2) {
			echo 'unvalidated';
		}elseif ($checklogin === false) {
			echo 'false';
		}
	}else {
		echo 'notexisting';
	}
}elseif(isset($_SESSION['user'])) {
	$user = new User($_SESSION['user'], $db);
	$checklogin = $user->logged;
}else {
	echo 'notsent';//Nichts gesendet
}
$statistics = new Statistics($db, $user);