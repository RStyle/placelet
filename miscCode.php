<?php //Datenbank einbinden usw.
$page = 'login';
$test314 = true;
require_once('./init.php');
if(isset($_GET['androidMSG'])) sendNotificationToAndroid($_GET['androidMSG']);
?>
<?php //E-Mail an alle Benutzer schicken
/*$sql = "SELECT user, email FROM users";
$stmt = $db->prepare($sql);
$stmt->execute(array());
$useremails = $stmt->fetchAll(PDO::FETCH_ASSOC);
$betreff = "Placelet - Online-Shop eröffnet - Online-Shop opened";
$mail_header = "From: Placelet <info@placelet.de>\n";
$mail_header .= "MIME-Version: 1.0" . "\n";
$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
$mail_header .= "Content-transfer-encoding: 8bit";
$datei = 'text/email/basic.php';
$i = 0;
foreach($useremails as $key => $val) {
	echo $i.'-'.$val['user'].': '.$val['email'].'<br>';
	$username = $val['user'];
	ob_start();
	include($datei);
	$inhalt = ob_get_clean();
	//mail($val['email'], $betreff, $inhalt, $mail_header);
	$i++;
}*/
?>
<?php //Bild drehen
/*$path = "pictures/bracelets/pic-145.jpg";
$source = imagecreatefromjpeg($path);
$rotate = imagerotate($source, 270, 0);
imagejpeg($rotate, $path);
imagedestroy($source);
imagedestroy($rotate);
echo 'gedreht';*/
?>
<?php //Alle Benutzer und deren ID ausgeben
/*$sql = "SELECT user, id FROM users WHERE status <> 0";
$stmt = $db->prepare($sql);
$stmt->execute();
$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($q as $user) {
	$usernames[$user['id']] = $user['user'];
}
print_r($usernames);
echo 'sadfsdf';*/
?>
<?php //Bestätigungsemail an alle unbestätgigten Benutzer versenden
/*$sql = "SELECT user, id, email FROM users WHERE status = 0";
$stmt = $db->prepare($sql);
$stmt->execute(array());
$useremails = $stmt->fetchAll(PDO::FETCH_ASSOC);

$betreff = "Placelet - Registrierung/Bestätigung";
$mail_header = "From: Placelet <info@placelet.de>\n";
$mail_header .= "MIME-Version: 1.0" . "\n";
$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
$mail_header .= "Content-transfer-encoding: 8bit";
$datei = 'text/email/confirm.php';
$i = 0;
foreach($useremails as $key => $val) {
	$sql = "SELECT code FROM user_status WHERE userid = :userid";
	$stmt = $db->prepare($sql);
	$stmt->execute(array(':userid' => $val['id']));
	$usercode = $stmt->fetch(PDO::FETCH_ASSOC);
	print_r($usercode);
	if($usercode == NULL) {
		$sql = "INSERT INTO user_status (userid, code) VALUES (:userid, :code)";
		$q = $db->prepare($sql);
		$code = substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20);
		$q->execute(array(
			':userid' => $val['id'],
			':code' => $code // Ein 60 buchstabenlanger Zufallscode
		));
	}else {
		echo $i.'-'.$val['id'].'-'.$val['user'].': '.$val['email'].'-'.$usercode['code'].'<br>';
		$username = $val['user'];
		$code = $usercode['code'];
		ob_start();
		include($datei);
		$inhalt = ob_get_clean();
		mail($val['email'], $betreff, $inhalt, $mail_header);
	}
	$i++;
}*/
?>
<?php
/*for($i = 1; $i <= 39; $i++) {
	$sql = "INSERT INTO notifications (userid, pic_own, comm_own, comm_pic, pic_subs) VALUES (:userid, :pic_own, :comm_own, :comm_pic, :pic_subs)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array(":userid" => $i, ":pic_own" => 3, ":comm_own" => 1, ":comm_pic" => 1, ":pic_subs" => 3));
}*/
?>
<?php
//Alle Städte ausgeben
/*$sql = "SELECT DISTINCT country FROM pictures GROUP BY city";
$stmt = $db->prepare($sql);
$stmt->execute();
$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
$countries = array();
foreach($q as $number => $city) {
	echo $city['country'].'<br>';
	if(!in_array($city['country'], $countries)) $countries[$number] = $city['country'];
}
//echo count($q).' St&auml;dte<br>';
echo count($countries).' L&auml;nder';*/
?>
<?php
//Lücken aus IDs entfernen
/*$sql = "SELECT id, filext FROM pictures GROUP BY city";
$stmt = $db->prepare($sql);
$stmt->execute();
$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($q as $number => $city) {
	echo $city['city'].', '.$city['country'].'<br>';
}*/
?>
<?php
//Picture Overview
$sql = "SELECT id, picid, fileext, city, country, brid FROM pictures";
$stmt = $db->prepare($sql);
$stmt->execute();
$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
$column = 1;
$q = array_reverse($q);
foreach($q as $pic) {
	$sql = "SELECT userid FROM bracelets WHERE brid = :brid";
	$stmt = $db->prepare($sql);
	$stmt->execute(array(':brid' => $pic['brid']));
	$q2 = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt = $db->prepare('SELECT brid FROM bracelets WHERE userid = :ownerid ORDER BY date ASC');
	$stmt->execute(array(':ownerid' => $q2['userid']));
	$userfetch = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($userfetch as $key => $val) {
		if($val['brid'] == $pic['brid']) $pic['braceletNR'] = $key + 1;
	}
	if(count($userfetch) == 1) $pic['braceletNR'] = 1;
	if(isset($_GET['daniel']) && $column <= 10) create_thumbnail('pictures/bracelets/pic-'.$pic['id'].'.'.$pic['fileext'], 'pictures/bracelets/mini_thumbs/mini-thumb-'.$pic['id'].'.jpg', 50, 50, $pic['fileext'], false);
	//echo '<a href="/'.Statistics::id2username($q2['userid']).'/'.$pic['braceletNR'].'/'.$pic['picid'].'"><img src="/cache.php?f=/pictures/bracelets/mini_thumbs/mini-thumb-'.$pic['id'].'.jpg" width="50" height="50" style="margin: 1px;"></a>';
	if($column % 15 == 0) {
		//echo '<br>';
	}
	$column++;
}
?>
<?php
//Letzten $anzahl Bilder - keine Infos über das Armband
/*$pic_count = 5;

$sql = "SELECT brid, title, description, city, country, userid, date, id FROM pictures ORDER BY id DESC LIMIT :limit";
$stmt = $db->prepare($sql);
$stmt->bindParam(':limit',  $pic_count, PDO::PARAM_INT);
$stmt->execute();
$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($q as $key => $pic) {
	$q[$key]['user'] = Statistics::id2username($pic['userid']);
}
print_r($q);*/
?>
<?php
/*$return = $statistics->picture_details(588888, true);
$json =  json_encode($return);
$json = remove_smileys($json);
var_dump($json);*/
?>
<?php
//Sucht nach Mustern in einem String
/*for($j = 0; $j < strlen($data); $j++) {
	$pattern = '';
	for($i = $j; $i < strlen($data) - 1; $i++){
		$pattern .= $data[$i];
		$count = substr_count($data, $pattern);
		if($count >= 4 && strlen($pattern) >= 2) {
			//echo $pattern.' found '.$count.' times-'.$j.'<br>';
			$result[$pattern] = $count;
		}
	}
}
print_r($result);*/
?>
<?php
//MyPlacelet
/*$username = "JohnZoidberg";
$userdetails = $statistics->userdetails($username);
	foreach($userdetails['brid'] as $key => $brid) {
			$sql = "SELECT city, country, title FROM pictures WHERE brid = :brid ORDER BY picid DESC";
			$stmt = $db->prepare($sql);
			$stmt->execute(array('brid' => $brid));
			$q = $stmt->fetch(PDO::FETCH_ASSOC);
			$return['ownBracelets'][$key] = $q;
	}
	$return['ownBracelets'][$key] = $q;
	
	$sql = "SELECT city, country, title FROM pictures WHERE userid = :user ORDER BY picid DESC";
	$stmt = $db->prepare($sql);
	$stmt->execute(array('user' => Statistics::username2id($username)));
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$return['pics'] = $result;
	print_r($return);
	echo json_encode($return);
*/
?>
<?php
//Traveldistance of every bracelet and sum
/*$sql = "SELECT name FROM bracelets";
$stmt = $db->prepare($sql);
$stmt->execute(array());
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_distance = 0;
$braceletcount = 0;
foreach($result as $bracelet) {
	$data = getlnlt($bracelet['name']);
	$i = 0;
	$distance = 0;
	foreach($data as $l){
		if($i > 0){
			$p1 = array('latitude' => $data[$i-1]['latitude'], 'longitude' => $data[$i-1]['longitude']);
			$p2 = array('latitude' => $data[$i]['latitude'], 'longitude' => $data[$i]['longitude']);
			$distance = $distance + getDistance($data[$i-1], $l);
		}
		$i++;
	}
	$distance = round($distance) / 1000;
	$total_distance += $distance;
	if($distance) {
	$braceletcount++;
		echo '<a href="armband?name='.urlencode($bracelet['name']).'">'.$bracelet['name'].'</a>: '.$distance.'km<br>';
	}
}
echo $total_distance."km<br>";
echo $braceletcount;*/
?>
<?php
//Datum des letzten Logins von allen Benutzern
/*$sql = "SELECT last_login, user FROM users ORDER BY last_login DESC";
$stmt = $db->prepare($sql);
$stmt->execute(array());
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($result as $user) {
	if($user['last_login'] != 0) echo $user['user'].' - '.date('H:i d.m.Y', $user['last_login']).'<br>';
}*/
?>
<?php
$username = "JohnZoidberg";
$dynPW = "$2a$10$13e962f854323c57def3buDGnjORibJIk/USZ/5ZrO1t6EHFWSAhu";

$brid = "c56eu8";
$picture_details = $statistics->bracelet_stats($brid, true);
$picture_details['subscribed'] = false;
$return = $picture_details;
$return['update'] = $picture_details[1]['upload'];
print_r($return);
?>