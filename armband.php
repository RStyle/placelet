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
		$write_comment = $statistics->write_comment(
			$braceID,
			$_POST['comment_content'][$_POST['comment_form']],
			$_POST['comment_picid'][$_POST['comment_form']]);
	}
	if(isset($write_comment)) {
		if($write_comment === true) {
			$js .= 'alert("'.$lang->php->write_comment->wahr->$lng.'");';			
		}elseif($write_comment == 2) {
			$js .= 'alert("'.$lang->php->write_comment->f2->$lng.'");';			
		}elseif($write_comment == 3) {
			$js .= 'alert("'.$lang->php->write_comment->f3->$lng.'");';			
		}elseif($write_comment === false) {
			$js .= 'alert("'.$lang->php->write_comment->falsch->$lng.'");';			
		}
	}
	//Kommentar löschen
	if(isset($_GET['comment_deleted'])) {
		if($_GET['comment_deleted'] == 'true') {
			$js .= 'alert("'.$lang->php->manage_comment->$lng.'");';	
		}
	}
	//Bild löschen
	if(isset($_GET['pic_deleted'])) {
		if($_GET['pic_deleted'] == 'true') {
			$js .= 'alert("'.$lang->php->manage_pic->$lng.'");';	
		}
	}
	//Armband Name ändern
	$owner = false;
	if(isset($_POST['edit_submit'])) {
		$change_name = $user->edit_br_name($braceID, $_POST['edit_name']);
		if($change_name == 1) {
			header('Location: armband?name='.urlencode($_POST['edit_name']).'&name_edited='.$change_name);
		}elseif($change_name == 2) {
			$js .= 'alert("'.$lang->php->edit_br_name->f2->$lng.'");';
		}
	}
	if(isset($_GET['name_edited'])) {
		$js .= 'alert("'.$lang->php->edit_br_name->name_edited->$lng.'");';
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
				if($sub_added === true) $js .= 'alert("'.$lang->php->manage_subscription->wahr->$lng.'");';
					elseif($sub_added == 2) $js .= 'alert("'.$lang->php->manage_subscription->f2->$lng.'");';
			}
		}
	}
	//Armband abonnieren/deabonnieren
	if(isset($_GET['sub']) && isset($_GET['sub_code'])) {
		$sub_added = $statistics->manage_subscription($_GET['sub'], $braceID, urldecode($_GET['sub_code']));
		if(isset($sub_added)) {
			if($sub_added === true) $js .= 'alert("'.$lang->php->manage_subscription->wahr->$lng.'");';
				elseif($sub_added == 2) $js .= 'alert('.$lang->php->manage_subscription->f2->$lng.'");';
				elseif($sub_added == 3) $js .= 'alert("'.$lang->php->manage_subscription->f3->$lng.'");';
				elseif($sub_added === false) $js .= 'alert("'.$lang->php->manage_subscription->falsch->$lng.'");';
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
	$user_subscribed = false;
	if($user->login) {
		$userdetails = $statistics->userdetails($user->login);
		if($userdetails['subscriptions'] != NULL)
			if(array_key_exists($braceID, $userdetails['subscriptions'])) $user_subscribed = true;
	}
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>