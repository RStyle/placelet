<?php
$page = 'nachrichten';
require_once('./init.php');
/*---------------------------------------------------------*/
if($user->login) {
	$messages = $user->receive_messages(false, false);
	if(isset($_GET['msg'])) {
		$recipient = array('name' => $_GET['msg'], 'id' => @Statistics::username2id($_GET['msg']));
		if(!array_key_exists($recipient['id'], $messages) && Statistics::userexists($recipient['name']) && $recipient['id'] != $user->userid) $new_message = true;
			else $new_message = false;
	}else $new_message = false;
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>