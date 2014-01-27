<?php
$page = 'account';
require_once('./init.php');
/*---------------------------------------------------------*/
if(isset($_GET['details'])) $category = 'details';
	elseif(isset($_GET['notifications'])) $category = 'notifications';
	elseif(isset($_GET['privacy'])) $category = 'privacy';
	else $category = 'none_selected';
if($user->login) {
	$username = $user->login;
}
//Passwortlink überprüfen
if(isset($_GET['passwordCode'])) {
	$recover_code = $user->check_recover_code($_GET['passwordCode']);
}
if(isset($_POST['submit'])) {
	//Link zum Passwort wiederherstellen senden
	if(isset($_POST['recover_email'])) {
		$password_reset = $user->reset_password($_POST['recover_email']);
		if($password_reset === true) $js .= 'alert("'.$lang->php->reset_password->wahr->$lng.'");';
			else $js .= 'alert("'.$lang->php->reset_password->falsch->$lng.'");';
	}
	if($user->login) {
		//Userdetails ändern
		if(isset($_POST['change_email']) && isset($_POST['change_old_pwd']) && isset($_POST['change_new_pwd'])) {
			$change_details = $user->change_details($_POST['change_email'], $_POST['change_old_pwd'], $_POST['change_new_pwd'], $user->login);
			if($change_details === true) {
				$js .= 'alert("'.$lang->php->change_details->wahr->$lng.'");';
			}elseif($change_details == 2) {
				$js .= 'alert("'.$lang->php->change_details->f2->$lng.'");';
			}elseif($change_details == 3) {
				$js .= 'alert("'.$lang->php->change_details->f3->$lng.'");';
			}elseif($change_details === false) {
				$js .= 'alert("'.$lang->php->change_details->falsch->$lng.'");';
			}
		}elseif(isset($_POST['notification_change'])) {
			if(!isset($_POST['pic_own_online'])) $_POST['pic_own_online'] = false;
			if(!isset($_POST['pic_own_email'])) $_POST['pic_own_email'] = false;
			if(!isset($_POST['comm_own_online'])) $_POST['comm_own_online'] = false;
			if(!isset($_POST['comm_own_email'])) $_POST['comm_own_email'] = false;
			if(!isset($_POST['comm_pic_online'])) $_POST['comm_pic_online'] = false;
			if(!isset($_POST['comm_pic_email'])) $_POST['comm_pic_email'] = false;
			if(!isset($_POST['pic_subs_online'])) $_POST['pic_subs_online'] = false;
			if(!isset($_POST['pic_subs_email'])) $_POST['pic_subs_email'] = false;
			$update_notifications = $user->update_notifications(
				$_POST['pic_own_online'], $_POST['pic_own_email'],
				$_POST['comm_own_online'], $_POST['comm_own_email'],
				$_POST['comm_pic_online'], $_POST['comm_pic_email'],
				$_POST['pic_subs_online'], $_POST['pic_subs_email']);
		}
		$notifics = $user->recieve_notifications();
		$navregister['value'] = "Mein Profil";
		if(!($notifics['pic_owns'] == NULL && $notifics['comm_owns'] == NULL && $notifics['comm_pics'] == NULL && $notifics['pic_subs'] == NULL)) {
			$navregister['value'] = 'Mein Profil ('.(count($notifics['pic_owns']) + count($notifics['comm_owns']) + count($notifics['comm_pics']) + count($notifics['pic_subs'])).')';
		}
	}
	//Passwort ändern
	if(isset($_POST['new_username']) && isset($_POST['new_pwd'])) {
		$new_password = $user->new_password($_POST['new_username'], $_POST['new_pwd']);
		if($new_password === true) {
			$js .= 'alert("'.$lang->php->new_password->wahr->$lng.'");';
		}
	}
}
//Userdetails abrufen
if(isset($username) && Statistics::userexists($username)) {
	$userdetails = $statistics->userdetails($username);
	$armbaender = profile_stats($userdetails);
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>