<?php //Datenbank einbinden usw.
$page = 'login';
require_once('./init.php');
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
/*$path = "pictures/bracelets/pic-116699-1.jpg";
$source = imagecreatefromjpeg($path);
$rotate = imagerotate($source, 180, 0);
imagejpeg($rotate, $path);
imagedestroy($source);
imagedestroy($rotate);*/
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