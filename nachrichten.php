<?php
$page = 'nachrichten';
require_once('./init.php');
/*---------------------------------------------------------*/
$user->send_message(40, "Hi, was geht?");
$messages = $user->recieve_messages();
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>