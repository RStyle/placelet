<?php
session_start(); //Session starten
include_once('../connection.php');
include_once('../functions.php');
include_once('../user.php');
$lang = simplexml_load_file('../../text/translations.xml');
if(isset($_POST['eng'])) $lng = $_POST['eng'];
if(!isset($_POST['notific_read'])){
	$user = new User(false, $db);
	if(isset($_POST['logout'])) User::logout();
	if(isset($_POST['login']) && isset($_POST['password'])) {
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
		}else {
			echo 'notsent';
		}
	}elseif(isset($_SESSION['user'])) {
		$user = new User($_SESSION['user'], $db);
		$checklogin = $user->logged;
	}else {
		echo 'notsent';//Nichts gesendet
	}
}
else
	$user = new User($_SESSION['user'], $db);
$statistics = new Statistics($db, $user);
if(isset($_POST['notific_read'])) {
	$user->notifications_read();
}
//echo "a".$user->userid.'a'.$user->login;
?>