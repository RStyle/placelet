<?php
$page = 'admin';
require_once('./init.php');
/*---------------------------------------------------------*/
if($user->admin && $checklogin) {
	//Kommentar löschen
	if(isset($_GET['delete_comm']) && isset($_GET['commid']) && isset($_GET['picid']) && isset($_GET['name'])) {
		$comment_deleted = $statistics->manage_comment($user->admin, 'middle', $_GET['commid'], $_GET['picid'], $statistics->name2brid(urldecode($_GET['name'])));
		if($comment_deleted === true) {
			$js .= 'alert("Kommentar erfolgreich gelöscht.");';
		}
	}
	//Bild löschen
	if(isset($_GET['delete_pic']) && isset($_GET['picid']) && isset($_GET['name'])) {
		$pic = $statistics->manage_pic($user->admin, 'middle', $_GET['picid'], $statistics->name2brid(urldecode($_GET['name'])));
		if($pic === true) {
			$js .= 'alert("Bild erfolgreich gelöscht.");';
		}
	}
	//Kein Spam
	if(isset($_GET['nospam']) && isset($_GET['commid']) && isset($_GET['picid']) && isset($_GET['name'])) {
		$no_spam = $statistics->no_spam($statistics->name2brid(urldecode($_GET['name'])), $_GET['picid'], $_GET['commid']);
	}
	$admin_stats = $statistics->admin_stats();
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>