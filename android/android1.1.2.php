<?php
ini_set('display_errors', true);
$return = array('notsentlol' => 'dudenotsent', 'test' => 'waslos?');
include_once('../scripts/connection.php');
include_once('../scripts/functions.php');
include_once('../scripts/user.php');
require_once('../scripts/PushBots.class.php');
define("NOT_EXISTING", 0);
define("WRONG_PW", 1);
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
$lang = simplexml_load_file('../text/translations.xml');
if(isset($_POST['eng'])) $lng = $_POST['eng'];
if(isset($_POST['androidLogin'])) {
	$return = login($_POST['deviceToken'], $_POST['user'], $_POST['pasword'], $db);
}elseif(isset($_POST['androidGetMessages'])) {
	if(Statistics::userexists($_POST['user'])) {
		$user = new User($_POST['user'], $db, $_POST['dynPW'], true);
		$messages = $user->receive_messages(false, false);
		foreach(array_reverse($messages) as $key => $chat) {
				$latestMSG = end($chat);
				reset($chat);
				$news[$chat['recipient']['name']] = $latestMSG;
		}
		$return = $news;
	}
}elseif(isset($_POST['androidGetIOMessages'])) {
	if(Statistics::userexists($_POST['user'])) {
		$user = new User($_POST['user'], $db, $_POST['dynPW'], true);
		$return = $user->receive_messages(false, false, $_POST['recipient']);
		if(isset($_POST['recipient'])) $return['exists'] = Statistics::userexists($_POST['recipient']);
	}
}elseif(isset($_POST['androidSendMessages'])) {
	if(Statistics::userexists($_POST['user'])) {
		if(Statistics::userexists($_POST['recipient'])) {
			$recipient = Statistics::username2id($_POST['recipient']);
			$user = new User($_POST['user'], $db, $_POST['dynPW'], true);
			$user->send_message($recipient, $_POST['content']);
			$return = array('messageSent' => true);
		}else $return = array('messageSent' => false, 'error' => NOT_EXISTING);
	}
}elseif(isset($_POST['androidUploadPicture'])) {
	$user = new User($_POST['user'], $db, @$_POST['dynPW'], true);
		//$return = array('login' => $user->login);
}
$statistics = new Statistics($db, $user);
if(isset($_POST['androidProfileInfo'])) {
	$username = $_POST['user'];
	if($username != "not_logged") {
		$userdetails = $statistics->userdetails($_POST['user']);
		$armbaender = profile_stats($userdetails);
		$return = $userdetails;
	}
}elseif(isset($_POST['androidGetCommunityPictures'])) {
	if(isset($_POST['pic_count'])) $pic_count = (int) $_POST['pic_count'];
	else $pic_count = 5;

	$sql = "SELECT brid, title, description, city, country, userid, date, id FROM pictures ORDER BY id DESC LIMIT :limit";
	$stmt = $db->prepare($sql);
	$stmt->bindParam(':limit', $pic_count, PDO::PARAM_INT);
	$stmt->execute();
	$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($q as $key => $pic) {
		$q[$key]['user'] = Statistics::id2username($pic['userid']);
	}
	$return = $q;
}elseif(isset($_POST['androidGetBraceletPictures'])) {
	$picture_details = $statistics->picture_details($_POST['braceID'], true);
	$return = $picture_details;
}elseif(isset($_POST['androidText'])) {
	 $File = "android.txt"; 
	 $Handle = fopen($File, 'w');
	 $Data = $_POST['textContent']; 
	 fwrite($Handle, $Data); 
	 $return = array("text" => "true");
	 fclose($Handle);
}elseif(isset($_POST['androidUploadPicture'])) {
	$brid = $_POST['brid'];
	$description = $_POST['description'];
	$city = $_POST['city'];
	$country = $_POST['country'];
	$state = 'Rheinland-Pfalz';
	$latitude = 0;
	$longitude = 0;
	if(isset($_POST['latitude']) && $_POST['longitude']) {
		$latitude = $_POST['latitude'];
		$longitude =  $_POST['longitude'];
	}
	$title = $_POST['title'];
	$date = $_POST['date'];
	$picture_file = $_FILES['uploadPic'];
	$upload = $statistics->registerpic($brid, $description, $city, $country, $state, $latitude, $longitude, $title, $date, $picture_file, $max_file_size);
	$return = array("upload" => $upload);
}
foreach($_POST as $key => $val) {
	$return[$key] = $val;
}
echo json_encode($return);
?>