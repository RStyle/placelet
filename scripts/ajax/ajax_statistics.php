<?php
session_start(); //Session starten
include_once('../connection.php');
include_once('../functions.php');
require_once('../user.php');
$lang = simplexml_load_file('../../text/translations.xml');
if(isset($_POST['eng'])) $lng = $_POST['eng'];
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
		$return = array('checklogin' => $checklogin, 'username' => $user->login);
		
	}
}elseif(isset($_POST['braceName']) && isset($_POST['deleterequest'])) {//'flag' => true bedeutet als Spam markieren und false bedeutet löschen
	$braceID = $statistics->name2brid(urldecode($_POST['braceName']));
	if($user->login == false) {
		$return = array('flag' => true);
	}elseif($user->admin) {
		$return = array('flag' => false);
	}else {
		$bracelet_stats = $statistics->bracelet_stats($braceID);
		if($user->login == $bracelet_stats['owner']) {
			$return = array('flag' => false);
		}else {
			$return = array('flag' => true);
		}
	}
}elseif(isset($_POST['brid']) && isset($_POST['new_name']) && isset($_POST['change_name'])) {
	$change_name = $user->edit_br_name($_POST['brid'], $_POST['new_name']);
	if($change_name == 1) {
		$return = array('change_name' => true, 'brace_name' => htmlentities($_POST['new_name']));
	}elseif($change_name == 2) {
		$return = array('change_name' => false);
	}
}elseif(isset($_POST['edit_pic']) && $_POST['name'] && $_POST['picid'] && $_POST['location'] && $_POST['title'] && $_POST['description']) {
	$pic_edited = $user->edit_pic($_POST['name'], $_POST['picid'], $_POST['location'], $_POST['title'], $_POST['description']);
	$return = array('pic_edited' => $pic_edited);
	//print_r($_POST);
}elseif(isset($_POST['subscribe'])) {
	//Armband abonnieren
	if($user->login) {
		$sub_type = 'username';
		$sub_user = $user->login;
		$subscribe = true;
	}elseif(isset($_POST['subscribe_email'])) {
		$sub_type = 'email';
		$sub_user = $_POST['subscribe_email'];
		$subscribe = true;
	}else {
		$subscribe = false;
		$return = array('subscribe' => false);
	}
	if($subscribe) {
		$sub_added = $statistics->manage_subscription($sub_type, $statistics->name2brid($_POST['subscribe']), $sub_user);
			if($sub_added === true) $return = array('subscribe' => 'wahr');
				elseif($sub_added == 2) $return = array('subscribe' => '2');
				elseif($sub_added == 3) $return = array('subscribe' => '3');
				elseif($sub_added === false) $return = array('subscribe' => 'falsch');
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
?>