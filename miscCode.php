<?php
$page = 'login';
require_once('./init.php');

$sql = "SELECT user, email FROM users";
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
}
?>
<?php
/*$path = "pictures/bracelets/pic-116699-1.jpg";
$source = imagecreatefromjpeg($path);
$rotate = imagerotate($source, 180, 0);
imagejpeg($rotate, $path);
imagedestroy($source);
imagedestroy($rotate);*/

/*$content = "Bitte klicke auf diesen Link, um deinen Account zu bestätigen:\n" . 'http://placelet.de/?regstatuschange_user='.urlencode($reg['reg_login']).'&regstatuschange='.urlencode($code);
$content = str_replace(array('username', 'code'),array(urlencode($reg['reg_login']), urlencode($code)),file_get_contents('./text/email/basic.html'));
$mail_header = "From: Placelet <support@placelet.de>\n";
$mail_header .= "MIME-Version: 1.0" . "\n";
$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
$mail_header .= "Content-transfer-encoding: 8bit";
mail($reg['reg_email'], 'Bestätigungsemail', $content, $mail_header);*/
include('scripts/connection.php');
$sql = "SELECT user, id FROM users WHERE status <> 0";
$stmt = $db->prepare($sql);
$stmt->execute();
$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($q as $user) {
	$usernames[$user['id']] = $user['user'];
}
print_r($usernames);
echo 'sadfsdf';
?>