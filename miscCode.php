<?php
//Datenbank einbinden usw.
$page = 'login';
$test314 = true;
require_once('./init.php');
if(isset($_GET['androidMSG'])) sendNotificationToAndroid($_GET['androidMSG']);
if($user->login == "JohnZoidberg") {
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
/*Alle Städte ausgeben
$sql = "SELECT DISTINCT country FROM pictures GROUP BY city";
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
/*Picture Overview
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
}*/
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
/*$data = '{"0":{"brid":"63m8qj","title":"Hand it over","description":"Handing the Placelt over to the older Generation at the greatest breakfast-place on earth. Have a save trip!","city":"Hinesville","country":"USA","userid":"132","date":"1408259502","id":"353","upload":"1408813030","user":"Sgorn"},"1":{"brid":"63m8qj","title":"Back Home in the South","description":"The statue of James Oglethorpe, the founder of Georgia, in Savannah, GA.","city":"Savannah","country":"USA","userid":"132","date":"1407669200","id":"352","upload":"1408812794","user":"Sgorn"},"2":{"brid":"63m8qj","title":"Empire State of Mind","description":"On top of the world ;)","city":"New York City","country":"USA","userid":"132","date":"1407158568","id":"351","upload":"1408812447","user":"Sgorn"},"3":{"brid":"63m8qj","title":"Lexington Battleground","description":"Following the American Revolution!","city":"Lincoln","country":"USA","userid":"132","date":"1407059529","id":"350","upload":"1408812242","user":"Sgorn"},"4":{"brid":"63m8qj","title":"Boston here I come!","description":"Beautiful Old City Hall in downtown Boston.","city":"Boston","country":"USA","userid":"132","date":"1406898231","id":"347","upload":"1408809303","user":"Sgorn"},"5":{"brid":"63m8qj","title":"Quick Pitstop","description":"Quick stop to change the flight and bringt the Placeland to Iceland ;)","city":"Reykjanesb&aelig;r","country":"Island","userid":"132","date":"1406821433","id":"346","upload":"1408808281","user":"Sgorn"},"6":{"brid":"588888","title":"Opelzoo II","description":"Nicht nur &quot;Ebelfanten&quot;, sondern auch &quot;kanz kroo&szlig;e Karaffen&quot; wurden geboten. Was will man mehr? ;)","city":"K&ouml;nigstein im Taunus","country":"Deutschland","userid":"73","date":"1408706075","id":"345","upload":"1408736733","user":"CarGol"},"7":{"brid":"588888","title":"Eine Dosis pures Kindergl&uuml;ck","description":"Opelzoo-Besuch mit Patenkind-Kr&uuml;melchen. Quietschende Begeisterung an jeder Ecke und jede Menge Sonnenschein. UND &quot;Ebelfanten! Mit Aa!&quot; :D ... Happy day.","city":"K&ouml;nigstein im Taunus","country":"Deutschland","userid":"73","date":"1408708498","id":"344","upload":"1408736114","user":"CarGol"},"8":{"brid":"588888","title":"Ohhh! Original OF.","description":"Zu Besuch an der Leibnizschule... good times teaching there, millions of years ago... :)","city":"Offenbach am Main","country":"Deutschland","userid":"73","date":"1408702788","id":"343","upload":"1408735633","user":"CarGol"},"9":{"brid":"nuer2q","title":"Battlefield of Little Big Horn","description":"In June 1876, General George Custer, US Army, was sent to Montana with the 7th Cavalry Regiment to contain some problematic Indians. He was a very successful soldier in the Civil War and had an outstanding reputation. He thought this newest assignment would be easy, however the Indians had other ideas. Lakota Souix, Cheyenne and Arapaho tribes banded together and  annihilated Custer and his men. It\'s unknown exactly how many Indians there were, but eyewitnesses estimate between 2,000-5,000. This battle is known as &quot;Custer\'s Last Stand&quot; and it overshadowed all of his many previous military successes. The battlefield is covered with white headstones where Custer\'s men fell and red headstones were Indians fell. There is a very famous painting that depicts the final moments of the battle, in which Custer and his men are on a hill surrounded by Indians. The cavalry had killed their horses to use as breastworks, but this only delayed the inevitable.","city":"Garryowen","country":"United States","userid":"112","date":"1408296368","id":"342","upload":"1408296368","user":"RockyMtnHigh"},"update":"alreadyUpToDate","u":true,"pic_count":"10","v":"1.2.4","user":"JohnZoidberg","lastUpdate":"1409077066","androidGetCommunityPictures":"true","":""}';
for($j = 0; $j < strlen($data); $j++) {
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
//print_r($result);*/
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
/*$username = "JohnZoidberg";
$dynPW = "$2a$10$13e962f854323c57def3buDGnjORibJIk/USZ/5ZrO1t6EHFWSAhu";

$brid = "c56eu8";
$picture_details = $statistics->bracelet_stats($brid, true);
$picture_details['subscribed'] = false;
$return = $picture_details;
$return['update'] = $picture_details[1]['upload'];
print_r($return);*/
?>
<?php
}
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
/*$brid = "9tcb66";
$sql = "SELECT id, city, country, title, picid, brid, latitude, longitude FROM pictures WHERE brid = :brid ORDER BY picid DESC";
$stmt = $db->prepare($sql);
$stmt->execute(array('brid' => $brid));
$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
$distance = 0;
for($i = 1; $i < count($q); $i++) {
	echo $i.': ';
	print_r($q[$i]);
		$distance += getDistance(array("latitude" => $q[$i - 1]['latitude'], "longitude" => $q[$i - 1]['longitude']), array("latitude" => $q[$i]['latitude'], "longitude" => $q[$i]['longitude']));
}
echo round($distance) / 1000;*/
?>
<?php
// if x == 10 dann limit 0,10
// if x >  10 dann limit x - 10, 5
/*define("PIC_START", 10);
define("PIC_LOAD", 5);
$pic_start = 15;
if($pic_start > PIC_START) $pic_count = PIC_LOAD;
		else $pic_count = PIC_START;
$pic_start -= PIC_LOAD;
$sql = "SELECT brid, title, description, city, country, userid, date, id, upload FROM pictures ORDER BY id DESC LIMIT :start, :count";
$stmt = $db->prepare($sql);
$stmt->bindParam(':start', $pic_start, PDO::PARAM_INT);
$stmt->bindParam(':count', $pic_count, PDO::PARAM_INT);
$stmt->execute();
$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($q);
echo "[$pic_start, $pic_count]";*/
?>
<?php
// Android Notifications Test
//var_dump(picPush("Jules", "Jules#1", "brid", 109, "APA91bEtsL8bBVauBL-uXCKoQKhjdZI3kA3NrubUxhECEKehhhBzS9G-4g0RnMNPOv6x7vQz1d3GzwEpkuWowySDiWLJOwHGMxQhnha-EsyNC9lDp0SJ5ECY9EF4ubilfKzVTfTwaQnu_aaqtFV30J6sXgcHz7o2zA"));
//var_dump(commentPush("Jules", "brid", "picid", "Wunderschönes Bild :o", "APA91bEtsL8bBVauBL-uXCKoQKhjdZI3kA3NrubUxhECEKehhhBzS9G-4g0RnMNPOv6x7vQz1d3GzwEpkuWowySDiWLJOwHGMxQhnha-EsyNC9lDp0SJ5ECY9EF4ubilfKzVTfTwaQnu_aaqtFV30J6sXgcHz7o2zA"));
//var_dump(messagePush("Jules", 2, "Hi, was geht bei dir?", "APA91bEtsL8bBVauBL-uXCKoQKhjdZI3kA3NrubUxhECEKehhhBzS9G-4g0RnMNPOv6x7vQz1d3GzwEpkuWowySDiWLJOwHGMxQhnha-EsyNC9lDp0SJ5ECY9EF4ubilfKzVTfTwaQnu_aaqtFV30J6sXgcHz7o2zA"));
?>
<?php
function minify_json($json) {
	$username_json = "John#Zoidberg";
	if(isset($_POST['user'])) $username_json = $_POST['user'];
	$search  = array("recipient", "name", "sender", "sent", "seen", "message", "update", "exists", "brid", "title", "description", "city", "country", "userid", "date", "upload", "user", "user", "ownBracelet", "alreadyUpToDate", "picid", "longitude", "latitude", "state", "commid", "fileext", $username_json, "Deutschland", "United States");//id
	$replace = array("1‡", "2‡", "3‡", "4‡", "5‡", "6‡", "7‡", "8‡", "9‡", "‡10", "‡11", "‡12", "‡13", "‡14", "‡15", "‡16", "‡17", "‡18", "‡19", "‡20", "‡21", "‡22", "‡3", "‡24", "‡25", "‡26", "‡27", "‡28", "‡29");
	$json = str_replace($search, $replace, $json);
	return $json;
}
$brid = "nuer2q";
$picture_details = $statistics->bracelet_stats($brid, true);
$picture_details['subscribed'] = false;
$userdetails = $statistics->userdetails($user->login);
if($userdetails['subscriptions'] != NULL) if(array_key_exists($brid, $userdetails['subscriptions'])) $picture_details['subscribed'] = true;
$json = json_encode($picture_details);
$jsonMinified = minify_json(($json));
//echo $jsonMinified;
echo $picture_details[$picture_details['pic_anz']]['upload'].'<br>';
$lastChange = 0;
foreach($picture_details as $key => $val) {
	$i = 1;
	$exists = true;
	while($exists) {
		if(isset($val[$i]['date'])) {
			$lastChange = $val[$i]['date'] > $lastChange ? $val[$i]['date'] : $lastChange;
			$i++;
		}else $exists = false;
	}
}
echo $lastChange;
?>