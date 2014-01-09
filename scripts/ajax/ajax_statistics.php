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
if(isset($_POST['login'])) {
		if($_POST['login'] == 'true') {
		$return = array('checklogin' => $checklogin);
		echo json_encode($return);
	}
}elseif(isset($_POST['braceName']) && isset($_POST['delete'])) {//'flag' => true bedeutet als Spam markieren und false bedeutet löschen
	$braceID = $statistics->name2brid(urldecode($_POST['braceName']));
	if($user->login == false) {
		$return = array('flag' => true);
	}elseif($user->admin) {
		$return = array('flag' => false);
	}else {
		$bracelet_stats = $statistics->bracelet_stats($braceID, $db);
		if($user->login == $bracelet_stats['owner']) {
			$return = array('flag' => false);
		}else {
			$return = array('flag' => true);
		}
	}
	echo json_encode($return);
}