<?php
$page='kontakt';
require_once('./init.php');
/*---------------------------------------------------------*/
if(isset($_POST['submit']) && isset($_POST['sender']) && isset($_POST['subject']) && isset($_POST['content']) && isset($_POST['mailer'])) {
	$send_email = send_email($_POST['sender'], $_POST['subject'], $_POST['content'], $_POST['mailer']);
}
if(isset($send_email)) {
	if($send_email === true) $js .= 'alert("'.$lang->php->send_email->wahr->$lng.'");';
		else $js .= 'alert("'.$lang->php->send_email->falsch->$lng.'");';
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>