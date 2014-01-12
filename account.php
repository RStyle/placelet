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
	switch($_POST['submit']) {
		//Link zum Passwort wiederherstellen senden
		case 'Neues Passwort zuschicken':
			$password_reset = $user->reset_password($_POST['recover_email']);
			$js .= 'alert("'.$password_reset.'");';
			break;
		//Userdetails ändern
		case 'Änderungen speichern':
			if($user->login) {
				$change_details = $user->change_details($_POST['change_email'], $_POST['change_old_pwd'], $_POST['change_new_pwd'], $user->login);
				$js .= 'alert("'.$change_details.'");';
			}
			break;
		case 'Passwort ändern':
			$new_password = $user->new_password($_POST['new_username'], $_POST['new_pwd']);
			$js .= 'alert("'.$new_password.'");';
			break;
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