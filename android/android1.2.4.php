<?php
//Android Version 1.2.4
ini_set('display_errors', true);
$return = array('notsentlol' => 'dudenotsent', 'test' => 'waslos?');
include_once('../scripts/connection.php');
include_once('../scripts/functions.php');
include_once('../scripts/user.php');
define("NOT_EXISTING", 0);
define("WRONG_PW", 1);
define("NOT_LOGGED_IN", "logged_out");
define("PIC_START_COUNT", 10);
define("PIC_LOAD_COUNT", 5);
//Maximale Größe für hochgeladene Bilder
$max_file_size = 8000000;
function login($deviceToken, $username, $pw, $db) {
	//return array('hi' => 'waslos?');
	$stmt = $db->prepare('SELECT * FROM users WHERE user = :user');
	$stmt->execute(array('user' => $username));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if($row['status'] == 0) return array('error' => NOT_EXISTING, 'success' => false);
	if(PassHash::check_password($row['password'], $pw)) {
		$userid = Statistics::username2id($username);
		/*$this->login = $row['user'];
		$this->userid = $row['userid'];*/
		$sql = "SELECT * FROM dynamic_password WHERE userid = :userid LIMIT 1"; 
		$q = $db->prepare($sql); 
		$q->execute(array(':userid' => $userid));
		$anz = $q->rowCount(); 
		
		$dynamic_password = PassHash::hash($row['password']);
		
		if($anz > 0){
				$row123 = $stmt->fetch(PDO::FETCH_ASSOC);
				$dynamic_password = $row123['password'];
 				//$_SESSION['dynamic_password'] = $row123['password'];
 		}
 
		$dynpw = PassHash::hash(substr($dynamic_password, 0, 15)).PassHash::hash(substr($dynamic_password, 15, 15)).PassHash::hash(substr($dynamic_password, 30, 15)).PassHash::hash(substr($dynamic_password, 45, 15));
		//4-facher Hash des Hashes - da der Hash ab einer bestimmten Anzahl von Buchstaben das Passwort abschneidet.
		/*$_SESSION['user'] = $this->login;
		$_SESSION['userid'] = $this->userid;*/
	
		if($anz > 0)
			 $sql= "UPDATE dynamic_password SET password = :password WHERE userid = :userid LIMIT 1";
		else
			$sql = "INSERT INTO dynamic_password (userid, password) VALUES (:userid, :password)";
	
		$q = $db->prepare($sql);
		$q->execute(array(
			':userid' => $userid,
			':password' => $dynamic_password)
		);
		$logged = true;
		//Einlogg-Zeit und Android Device Token eintragen
		$sql= "UPDATE users SET last_login = :date, androidToken = :androidToken WHERE user = :user LIMIT 1";
		$q = $db->prepare($sql);
		$q->execute(array(
			':user' => $username,
			':date' => time(),
			':androidToken' => $deviceToken
		));
		//Status abfragen
		if($row['status'] == 2) $admin = true;
			else $admin = false;
		if($row['last_login'] == 0) $firstLogin = true;
			else $firstLogin = false;
		return array('error' => false, 'success' => true, 'admin' => $admin, 'firstLogin' => $firstLogin, 'userid' => $userid, 'dynPW' => $dynpw);
	}else { 
		return array('error' => WRONG_PW, 'success' => false); 
	}
}
function minify_json($json) {
	$username_json = "John#Zoidberg";
	if(isset($_POST['user'])) $username_json = $_POST['user'];
	$search  = array("recipient", "name", "sender", "sent", "seen", "message", "update", "exists", "brid", "title", "description", "city", "country", "userid", "date", "upload", "user", "user", "ownBracelet", "alreadyUpToDate", "picid", "longitude", "latitude", "state", "commid", "fileext", $username_json, "Deutschland", "United States");//id
	$replace = array("1‡", "2‡", "3‡", "4‡", "5‡", "6‡", "7‡", "8‡", "9‡", "‡10", "‡11", "‡12", "‡13", "‡14", "‡15", "‡16", "‡17", "‡18", "‡19", "‡20", "‡21", "‡22", "‡3", "‡24", "‡25", "‡26", "‡27", "‡28", "‡29");
	$json = str_replace($search, $replace, $json);
	return $json;
}
function writeToAndroidText($Data) {
	 $File = "android.txt"; 
	 $Handle = fopen($File, 'w');
	 fwrite($Handle, $Data); 
	 fclose($Handle);
}
$lang = simplexml_load_file('../text/translations.xml');
if(isset($_POST['eng'])) $lng = $_POST['eng'];
if(isset($_POST['androidLogin'])) {
	$return = login($_POST['deviceToken'], $_POST['user'], $_POST['password'], $db);
}elseif(isset($_POST['androidAuthenticate'])) {
	if(isset($_POST['user'])) {
		if($_POST['user'] == NOT_LOGGED_IN) {
			$user = new User(false, $db);
		}elseif(isset($_POST['dynPW'])) {
			$user = new User($_POST['user'], $db, $_POST['dynPW'], true);
		}
	}else {
		$return['authentication'] = 'notsent';
	}
}elseif(isset($_POST['androidRegister'])) {
	$input = $_POST;
	$input['reg_password2'] = $input['reg_password'];
	$input['lng'] = 'en'; // TODO Richtige Sprache
	$user = new User(false, $db);
	$return = array('registered' => User::register($input, $db));
}

$statistics = new Statistics($db, $user);

if(isset($_POST['androidGetMessages'])) {
	if($user->login) {
		$messages = $user->receive_messages(false, false);
		$latestMSGDate = 0;
		$latestMSGSeen = 0;
		foreach(array_reverse($messages) as $key => $chat) {
				$latestMSG = end($chat);
				reset($chat);
				$news[$chat['recipient']['name']] = $latestMSG;
				if($latestMSG['sent'] > $latestMSGDate) $latestMSGDate = $latestMSG['sent'];
				if($latestMSG['seen'] > $latestMSGSeen) $latestMSGSeen = $latestMSG['seen'];
		}
		$return = $news;
		if($_POST['lastUpdate'] > $latestMSGDate && $_POST['lastUpdate'] > $latestMSGSeen) $return = array("update" => "alreadyUpToDate");
	}else {
		$return = array('notlogged' => $user->login);
	}
}elseif(isset($_POST['androidGetIOMessages'])) {
	if($user->login) {
		if(Statistics::userexists($_POST['recipient'])) {
			$return = $user->receive_messages(false, false, $_POST['recipient']);
			$user->messages_read(Statistics::username2id($_POST['recipient']));
			$msgs = end($return);
			reset($return);
			$latestMsg = end($msgs);
			if($_POST['lastUpdate'] > $latestMsg['sent']) $return = array("update" => "alreadyUpToDate");
		}
	}else {
		$return = array('notlogged' => $user->login);
	}
	$return['exists'] = Statistics::userexists($_POST['recipient']);
}elseif(isset($_POST['androidSendMessages'])) {
	if($user->login) {
		if(Statistics::userexists($_POST['recipient'])) {
			$recipient = Statistics::username2id($_POST['recipient']);
			$user->messages_read(Statistics::username2id($_POST['recipient']));
			$user->send_message($recipient, urldecode($_POST['content']));
			$return = $user->receive_messages(false, false, $_POST['recipient']);
			$msgs = end($return);
			reset($return);
			$latestMsg = end($msgs);
			if($_POST['lastUpdate'] > $latestMsg['sent']) $return = array("update" => "alreadyUpToDate");
			$return['messageSent'] = true;
		}else $return = array('messageSent' => false, 'error' => NOT_EXISTING);
	}else {
		$return = array('notlogged' => $user->login);
	}
}elseif(isset($_POST['androidProfileInfo'])) {
	$username = $_POST['user'];
	if($username != "not_logged") {
		$userdetails = $statistics->userdetails($_POST['user']);
		$armbaender = profile_stats($userdetails);
		$return = $userdetails;
	}
}elseif(isset($_POST['androidGetCommunityPictures'])) {
	if(isset($_POST['pic_count'])) $pic_count = (int) $_POST['pic_count'];
	if(isset($_POST['pic_start'])) $pic_start = (int) $_POST['pic_start'];
	$sql = "SELECT brid, title, description, city, country, userid, date, id, upload FROM pictures ORDER BY id DESC LIMIT :start, :count";
	$stmt = $db->prepare($sql);
	$stmt->bindParam(':start', $pic_start, PDO::PARAM_INT);
	$stmt->bindParam(':count', $pic_count, PDO::PARAM_INT);
	$stmt->execute();
	$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($q as $key => $pic) {
		$q[$key]['user'] = Statistics::id2username($pic['userid']);
	}
	$return = $q;
	$return['test'] = array($pic_start, $pic_count);
	if($_POST['lastUpdate'] > $q[0]['upload'] && $pic_count != PIC_LOAD_COUNT) $return = array("update" => "alreadyUpToDate");
	// $return['news'] = array();
	// Change SharedPreferences array("type" => "updatePrefs", "prefKey" => "version", "content" => "1.2.4.1");
	
	// Display Toast:    array("type" => "toast", "content" => "Hallo");
	
	// Display Dialog:   array("type" => "dialog", "positiveLabel" => "Yup", "negativeLabel" => "Nah", "title" => "Hey :)"
	//  with URL action:       "action" => "URL", "content" => "http://google.com"
	//  with activity action:  "action" => "Activity", "content" => "AboutActivity"
	//  snooze dialog:    "snooze" => 10
	
	// Display only for specific user:      if($_POST['user'] == "username")
	// Display only for specific version:      if($_POST['v'] == "version")
	//if($_POST['user'] == "JohnZoidberg") $return['news'] = array("type" => "dialog", "positiveLabel" => "Yup", "negativeLabel" => "Nah", "title" => "Hey :)", "action" => "Activity", "content" => "AboutActivity", "snooze" => 10);
	/*$return['news'] = array(
		array("type" => "toast", "content" => "test", "snooze" => 0),
		array("type" => "updatePrefs", "prefKey" => "version", "content" => "1.2.4.0", "snooze" => 0),
		array("type" => "dialog", "positiveLabel" => "Yup", "negativeLabel" => "Nah", "title" => "Hey :)", "action" => "Activity", "content" => "AboutActivity", "snooze" => 10),
		array("type" => "toast", "content" => "update", "snooze" => 0)
		);
	if(isset($_POST['v']) && $_POST['v'] != "1.2.4") $return['u'] = true;*/
	
}elseif(isset($_POST['androidGetBraceletData'])) {
	$picture_details = $statistics->bracelet_stats($_POST['braceID'], true);
	$userdetails = $statistics->userdetails($user->login);
	if($userdetails['subscriptions'] != NULL) if(array_key_exists($_POST['braceID'], $userdetails['subscriptions'])) $picture_details['subscribed'] = true;
	$return = $picture_details;
	$lastChange = 0;
	//$lastChange = $var > $lastChange ? $var : $lastChange;
	$lastChange = $picture_details[$picture_details['pic_anz']]['upload'] > $lastChange ? $picture_details[$picture_details['pic_anz']]['upload'] : $lastChange;
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
	if($_POST['lastUpdate'] > $lastChange) $return = array("update" => "alreadyUpToDate");
	$return['subscribed'] = false;
	if($userdetails['subscriptions'] != NULL) if(array_key_exists($_POST['braceID'], $userdetails['subscriptions'])) $return['subscribed'] = true;
}elseif(isset($_POST['androidGetOwnBracelets'])) {
	$return = array();
	$username = $_POST['user'];
	// Own bracelets
	$userdetails = $statistics->userdetails($username);
	if(isset($userdetails['brid'])) {
		if(!is_array($userdetails['brid'])) {
			$userdetails['brid'] = array($userdetails['brid']);
		}
		foreach($userdetails['brid'] as $key => $brid) {
			$sql = "SELECT id, city, country, title, picid, brid, latitude, longitude FROM pictures WHERE brid = :brid ORDER BY picid DESC";
			$stmt = $db->prepare($sql);
			$stmt->execute(array('brid' => $brid));
			$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
			// Calculate travelled distance
			$distance = 0;
			for($i = 1; $i < count($q); $i++)
					$distance += getDistance(array('latitude' => $q[$i - 1]['latitude'], 'longitude' => $q[$i - 1]['longitude']), array('latitude' => $q[$i]['latitude'], 'longitude' => $q[$i]['longitude']));
			$return['ownBracelets'][$key] = $q[0];
			$return['ownBracelets'][$key]['distance'] = round($distance / 1000);
			$return['ownBracelets'][$key]['name'] = $statistics->brid2name($brid);
		}
		// Own pictures
		$sql = "SELECT id, city, country, title, picid, brid, upload FROM pictures WHERE userid = :user ORDER BY picid DESC";
		$stmt = $db->prepare($sql);
		$stmt->execute(array('user' => Statistics::username2id($username)));
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$return['pics'] = $result;
		if($_POST['lastUpdate'] > $result[0]['upload']) $return = array("update" => "alreadyUpToDate");
	}
}elseif(isset($_POST['androidText'])) {
	writeToAndroidText($_POST['androidText']);
	 $return = array("text" => "true");
}elseif(isset($_POST['androidUploadPicture'])) {
	$brid = $_POST['brid'];
	$description = urldecode($_POST['description']);
	$city = urldecode($_POST['city']);
	$country = urldecode($_POST['country']);
	$state = '';
	$latitude = 0;
	$longitude = 0;
	if(isset($_POST['latitude']) && $_POST['longitude']) {
		$latitude = $_POST['latitude'];
		$longitude =  $_POST['longitude'];
	}
	$title = urldecode($_POST['title']);
	$date = $_POST['date'];
	$picture_file = $_FILES['uploadPic'];
	$upload = $statistics->registerpic($brid, $description, $city, $country, $state, $latitude, $longitude, $title, $date, $picture_file, $max_file_size);
	$return = array("upload" => $upload);
}elseif(isset($_POST['androidSubscribe'])) {
	$brid = $_POST['brid'];
	if($_POST['androidSubscribe'] == 'true') {
		$input = 'username';
		$email = $user->login;
	}elseif($_POST['androidSubscribe'] == 'false') {
		$input = 'false';
		$userdetails = $statistics->userdetails($user->login);
		$email = PassHash::hash($userdetails['email']);
	}
		
	$subscription = $statistics->manage_subscription($input, $brid, $email);
	$return = array("subscribed" => $subscription);
}elseif(isset($_POST['androidRegisterBracelet'])) {
	$registered = $user->registerbr($_POST['brid']);
	if(is_array($registered)) $registered = $registered[0];
	$return = array("registered" => $registered);
}elseif(isset($_POST['androidPostComment'])) {	
	$return['commentPosted'] = $statistics->write_comment($_POST['braceID'], $_POST['comment'], $_POST['picid']);
	
	$picture_details = $statistics->bracelet_stats($_POST['braceID'], true);
	$picture_details['subscribed'] = false;
	$userdetails = $statistics->userdetails($user->login);
	if($userdetails['subscriptions'] != NULL) if(array_key_exists($_POST['braceID'], $userdetails['subscriptions'])) $picture_details['subscribed'] = true;
	$return = $picture_details;
}
foreach($_POST as $key => $val) {
	$return[$key] = $val;
}
$return[''] = '';
$json = json_encode($return);
$jsonMinified = minify_json(emojify($json));
$gzipOutput = gzencode($jsonMinified, 9);
writeToAndroidText(
	//strlen($json).'-'.strlen($jsonMinified).'-'.strlen($gzipOutput)."\n".
	//((1 - strlen($jsonMinified) / strlen($json)) * 100).'%-'.((1 - strlen($gzipOutput) / strlen($jsonMinified)) * 100).'%'
	$json
	."<br><br><br>".
	$jsonMinified
);
header('Content-Length: '.strlen($gzipOutput));
header('Content-Type: text/html; charset=utf-8');
header('Content-Encoding: gzip');
echo $gzipOutput;
?>