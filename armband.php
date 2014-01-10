<?php
$page = 'armband';
if (isset($_GET['name'])) {
	$braceName = urldecode($_GET['name']);
}
require_once('./init.php');
/*---------------------------------------------------------*/
if (isset($braceName)) {
	$braceID = $statistics->name2brid($braceName);
}
if ($braceName != NULL) {
	//Kommentare schreiben
	if(isset($_POST['comment_submit'])) {
		$write_comment = $statistics->write_comment ($braceID,
							 $_POST['comment_user'][$_POST['comment_form']],
							 $_POST['comment_content'][$_POST['comment_form']],
							 $_POST['comment_picid'][$_POST['comment_form']]);
	}
	if(isset($write_comment)) {
		$js .= 'alert("'.$write_comment.'");';
	}
	//Kommentar löschen
	if(isset($_GET['comment_deleted'])) {
		if($_GET['comment_deleted'] == 'true') {
			$js .= 'alert("Kommentar erfolgreich gelöscht.");';	
		}
	}
	//Bild löschen
	if(isset($_GET['pic_deleted'])) {
		if($_GET['pic_deleted'] == 'true') {
			$js .= 'alert("Bild erfolgreich gelöscht.");';	
		}
	}
	//Armband Name ändern
	$owner = false;
	if(isset($_POST['edit_submit'])) {
		$change_name = $user->edit_br_name($braceID, $_POST['edit_name']);
		if($change_name == 1) {
			header('Location: armband?name='.urlencode($_POST['edit_name']).'&name_edited='.$change_name);
		}elseif($change_name == 2) {
			$js .= 'alert("Es gibt schon ein Armband mit diesem Namen.");';
		}
	}
	if(isset($_GET['name_edited'])) {
		$js .= 'alert("Name erfolgreich geändert.");';
	}
	if($user->login) {
		//Überprüfen, ob man das Armband gekauft hat.
		$userdetails = $statistics->userdetails($user->login);
		$armbaender = profile_stats($userdetails);
		if($armbaender['brid'] != NULL) {
			if(in_array($braceID, $armbaender['brid'])) {
				$owner = true;
			}
		}
		//Armband abonnieren
		if(isset($_GET['sub']) && isset($_GET['sub_user'])) {
			$sub_added = $statistics->manage_subscription($_GET['sub'], $braceID, $_GET['sub_user']);
			if(isset($sub_added)) {
				if($sub_added === true) $js .= 'alert("Abonnement erfolgreich hinzugefügt.");';
					elseif($sub_added == 2) $js .= 'alert("Du hast dieses Armband schon abonniert.");';
			}
		}
	}
	//Armband abonnieren/deabonnieren
	if(isset($_GET['sub']) && (isset($_GET['sub_code']) || isset($_GET['sub_email']))) {
		if(isset($_GET['sub_code'])) {
			$sub_email = $_GET['sub_code'];
		}elseif(isset($_GET['sub_email'])) {
			$sub_email = $_GET['sub_email'];
		}
		$sub_added = $statistics->manage_subscription($_GET['sub'], $braceID, $sub_email);
		if(isset($sub_added)) {
			if($sub_added === true) $js .= 'alert("Abonnement erfolgreich hinzugefügt.");';
				elseif($sub_added == 2) $js .= 'alert("Dieses Armband wurde schon mit der eingegebenen E-Mail abonniert.");';
				elseif($sub_added == 3) $js .= 'alert("Dieses Armband wurde nicht mit dieser E-Mail abonniert.");';
				elseif($sub_added === false) $js .= 'alert("Du hast das Abonnement erfolgreich beendet.");';
		}
	}
	$bracelet_stats = $statistics->bracelet_stats($braceID, $db);
	if(isset($bracelet_stats['owners'])) {
		$picture_details = $statistics->picture_details($braceID, $db);
		$stats = array_merge($bracelet_stats, $picture_details);
	}else {
		$bracelet_stats['owners'] = 0;
		$stats = $bracelet_stats;
	}
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>