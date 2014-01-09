<?php
session_start(); //Session starten
include_once('../connection.php');
include_once('../functions.php');
include_once('../user.php');
if(isset($_SESSION['user'])){
	$user = new User($_SESSION['user'], $db);
	$checklogin = $user->logged;
}else{
	$user = new User(false, $db);
	$checklogin = false;
}
$statistics = new Statistics($db, $user);
if(isset($_POST['login'])) if($_POST['login'] == 'true') {
	$return = Array('checklogin' => $checklogin);
	echo json_encode($return);
}elseif(isset($_POST['braceName']) && isset($_POST['delete'])) {//'flag' => true bedeutet als Spam markieren und false bedeutet löschen
	if($userlogin === false) {
		$return = Array('flag' => true);
	}elseif($user->admin) {
		$return = Array('flag' => false);
	}else {
		$bracelet_stats = $statistics->bracelet_stats($_POST['braceName'], $db);
		if($user->login == $bracelet_stats['owner']) {
			$return = Array('flag' => false);
		}else {
			$return = Array('flag' => true);
		}
	}
	echo json_encode($return);
}