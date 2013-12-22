<?php
$page = 'profil';
require_once('./init.php');
/*---------------------------------------------------------*/
if(isset($_GET['user'])) {
	$username = urldecode($_GET['user']);
}elseif($user->login) {
	$username = $user->login;
}
if(isset($username) && Statistics::userexists($username)) {
	$userdetails = $statistics->userdetails($username);
	$armbaender = profile_stats($userdetails);
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>