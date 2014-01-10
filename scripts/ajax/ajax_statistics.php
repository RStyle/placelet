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

$return = array('notsent' => 'notsent');
if(isset($_POST['login'])) {
		if($_POST['login'] == 'true') {
		$return = array('checklogin' => $checklogin);
		
	}
}elseif(isset($_POST['braceName']) && isset($_POST['deleterequest'])) {//'flag' => true bedeutet als Spam markieren und false bedeutet löschen
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
}else {
	if(isset($_POST['last_comment']) && isset($_POST['delete_comm']) && isset($_POST['commid']) && isset($_POST['picid']) && isset($_POST['name'])) {//Kommentar löschen
		$braceID = $statistics->name2brid($_POST['name']);
		$comment_deleted = $statistics->manage_comment($user->admin, $_POST['last_comment'], $_POST['commid'], $_POST['picid'], $braceID);
		if(isset($comment_deleted)) {
			if($comment_deleted === true ) {
				$return = array('location' => 'armband?name='.urlencode($_POST['name']).'&comment_deleted=true');
			}elseif($comment_deleted == 2) {
				$return = array('gemeldet' => 'Kommentar');
			}
		}
	}elseif(isset($_POST['last_pic']) && isset($_POST['delete_pic']) && isset($_POST['picid']) && isset($_POST['name'])) {//Bild löschen
		$braceID = $statistics->name2brid($_POST['name']);
		$pic_deleted = $statistics->manage_pic($user->admin, $_POST['last_pic'], $_POST['picid'], $braceID);
		if($pic_deleted === true ) {
			$return = array('location' => 'armband?name='.urlencode($_POST['name']).'&pic_deleted=true');
		}elseif($pic_deleted == 2) {
			$return = array('gemeldet' => 'Bild');
		}elseif ($pic_deleted == false) {
			$return = array('error' => 'Es ist ein Fehler beim Löschen des Bildes aufgetreten<br>Bitte informiere den Support.');
		}else $return = array('error' => $pic_deleted);
	}
}
echo json_encode($return);