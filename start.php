<?php
$page='start';
require_once('./init.php');
/*---------------------------------------------------------*/
//Kommentare schreiben
if(isset($_POST['comment_submit'])) {
	$write_comment = $statistics->write_comment ($_POST['comment_brid'][$_POST['comment_form']],
						 $_POST['comment_user'][$_POST['comment_form']],
						 $_POST['comment_content'][$_POST['comment_form']],
						 $_POST['comment_picid'][$_POST['comment_form']],
						 $user);
}
if(isset($write_comment)) {
	$js .= 'alert("'.$write_comment.'");';
}
//Kommentar löschen
if(isset($_GET['last_comment']) && isset($_GET['delete_comm']) && isset($_GET['commid']) && isset($_GET['picid']) && isset($_GET['comm_name'])) {
	$comment_deleted = $statistics->manage_comment($user->admin, $_GET['last_comment'], $_GET['commid'], $_GET['picid'], $statistics->name2brid(urldecode($_GET['comm_name'])));
	if($comment_deleted === true) {
		header('Location: start?comment_deleted=true');
	}elseif($comment_deleted == 2) {
			$js .= 'alert("Kommentar gemeldet.");';
	}
}
if(isset($_GET['comment_deleted'])) {
	if($_GET['comment_deleted'] == 'true') {
		$js .= 'alert("Kommentar erfolgreich gelöscht.");';	
	}
}
//Bild löschen
if(isset($_GET['last_pic']) && isset($_GET['delete_pic']) && isset($_GET['picid']) && isset($_GET['pic_name'])) {
	$pic_deleted = $statistics->manage_pic($user->admin, $_GET['last_pic'], $_GET['picid'], $statistics->name2brid(urldecode($_GET['pic_name'])));
	if($pic_deleted === true ) {
		header('Location: start?pic_deleted=true');
		echo 'jep';
	}elseif($pic_deleted == 2) {
		$js .= 'alert("Bild gemeldet.");';
	}elseif ($pic_deleted == false) {
		$js .= 'Es ist ein Fehler beim Löschen des Bildes aufgetreten<br>Bitte informiere den Support.';
		echo 'Fehler';
	}else echo $pic_deleted;
}
if(isset($_GET['pic_deleted'])) {
	if($_GET['pic_deleted'] == 'true') {
		$js .= 'alert("Bild erfolgreich gelöscht.");';	
	}
}
$user_anz = 5;
$systemStats = $statistics->systemStats($user_anz, 3);
//hier werden die Armbänder bestimmt, die angezeigt werden
$bracelets_displayed = $systemStats['recent_brids'];
foreach($bracelets_displayed as $key => $val) {
	$stats[$key] = array_merge($statistics->bracelet_stats($val), $statistics->picture_details($val));
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>