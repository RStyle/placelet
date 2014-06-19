<?php //Datenbank einbinden usw.
$page = 'login';
$test314 = true;
require_once('./init.php');
if(isset($_GET['androidMSG'])) sendNotificationToAndroid($_GET['androidMSG']);
echo '<br>';
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
/*$sql = "SELECT city, country FROM pictures GROUP BY city";
$stmt = $db->prepare($sql);
$stmt->execute();
$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
$countries = array();
foreach($q as $number => $city) {
	echo $city['city'].', '.$city['country'].'<br>';
	if(!in_array($city['country'], $countries)) $countries[$number] = $city['country'];
}
echo count($q).' St&auml;dte<br>';
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
$sql = "SELECT id, picid, fileext, city, country FROM pictures";
$stmt = $db->prepare($sql);
$stmt->execute();
$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
$column = 1;
foreach($q as $pic) {
	/*foreach($userfetch as $key => $val) {
		if($val['brid'] == $bracelets_displayed[$i]) $stats[$i]['braceletNR'] = $key + 1;
	}*/
	if(isset($_GET['daniel']) && $column > 100) create_thumbnail('pictures/bracelets/pic-'.$pic['id'].'.'.$pic['fileext'], 'pictures/bracelets/mini_thumbs/mini-thumb-'.$pic['id'].'.jpg', 50, 50, $pic['fileext'], false);
	echo '<a href=""><img src="/cache.php?f=/pictures/bracelets/mini_thumbs/mini-thumb-'.$pic['id'].'.jpg" width="50" height="50"></a>';
	if($column % 15 == 0) {
		echo '<br>';
	}
	$column++;
}
?>