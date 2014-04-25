<?php
$page = 'armband';
if(isset($_GET['name'])) {
	$braceName = urldecode($_GET['name']);
}

require_once('./init.php');
/*---------------------------------------------------------*/
if($braceName === false) {
	$stats = false;
}elseif($braceName != NULL) {
	if(isset($braceName)) $braceID = $statistics->name2brid($braceName);
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
	if($user->login) {
		//Überprüfen, ob man das Armband gekauft hat.
		$userdetails = $statistics->userdetails($user->login);
		$armbaender = profile_stats($userdetails);
		if($armbaender['brid'] != NULL) {
			if(in_array($braceID, $armbaender['brid'])) {
				$owner = true;
			}
		}
	}
	//Armband abonnieren/deabonnieren
	if(isset($_GET['sub']) && isset($_GET['sub_code'])) {
		$sub_added = $statistics->manage_subscription($_GET['sub'], $braceID, urldecode($_GET['sub_code']));
		if(isset($sub_added)) {
			if($sub_added === true) $js .= 'alert("'.$lang->php->manage_subscription->wahr->$lng.'");';
				elseif($sub_added == 2) $js .= 'alert("'.$lang->php->manage_subscription->f2->$lng.'");';
				elseif($sub_added == 3) $js .= 'alert("'.$lang->php->manage_subscription->f3->$lng.'");';
				elseif($sub_added === false) $js .= 'alert("'.$lang->php->manage_subscription->falsch->$lng.'");';
		}
	}
	$bracelet_stats = $statistics->bracelet_stats($braceID, $db);
	if(isset($bracelet_stats['owners'])) {
		$picture_details = $statistics->picture_details($braceID, true);
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
	$stmt = $db->prepare('SELECT brid FROM bracelets WHERE userid = :ownerid ORDER BY date ASC');
	if(isset($stats['owner'])){
		$stmt->execute(array(':ownerid' => $statistics->username2id($stats['owner'])));
		$userfetch = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($userfetch as $key => $val) {
			if($val['brid'] == $braceID) $stats['braceletNR'] = $key + 1;
		}
	}
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>