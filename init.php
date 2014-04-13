<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', true);
date_default_timezone_set("Europe/Berlin");

// Hier werden Cookies überprüft gesetzt usw.
// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
ini_set( 'session.use_only_cookies', '1' );
ini_set( 'session.use_trans_sid', '0' );
session_start(); //Session starten
if(!isset($_SESSION['server_SID'])) {
    // Möglichen Session Inhalt löschen
    session_unset();
    // Ganz sicher gehen das alle Inhalte der Session gelöscht sind
    $_SESSION = array();
    // Session zerstören
    session_destroy();
    // Session neu starten
    session_start();
    // Neue Server-generierte Session ID vergeben
    session_regenerate_id();
    // Status festhalten
    $_SESSION['server_SID'] = true;
}
// Alle Fehlermeldungen werden angezeigt
//
$ziffern = 6;
if(isset($_SESSION['testserver'])) if($_SESSION['testserver'] === true) $ziffern = 7;
//Einbinden der Dateien, die Funktionen, MySQL Daten und PDO Funktionen enthalten
if($_SERVER['SERVER_NAME'] == 'localhost') {
	$this_path = '';
	$this_path_html = '';
}else {
	$this_path = '/var/www/virtual/placelet.de/htdocs/';
	$this_path_html = 'http://www.placelet.de/';
}
require_once($this_path.'scripts/recaptchalib.php');
require_once($this_path.'scripts/connection.php');
require_once($this_path.'scripts/functions.php'); 
require_once($this_path.'scripts/user.php');
$lang = simplexml_load_file('./text/translations.xml');


$js = '<script type="text/javascript">$(document).ready(function(){';

if(isset($_GET['logout'])) {
	User::logout();
	header('Location: /home');
	exit;
}

$checklogin = false;
if(isset($_POST['login']) && isset($_POST['password'])){
	if(Statistics::userexists($_POST['login'])) {
		$user = new User($_POST['login'], $db);	
		$checklogin = $user->login($_POST['password']);
		if($checklogin === true) {
			if(isset($_POST['login_location'])) {
				header('Location: /'.$_POST['login_location']);
				exit;
			}else {
				header('Location: /start');
				exit;
			}
		}elseif($checklogin == 3) {
			$js .= 'validationRegister = confirm("Bestätigung erfolgreich.\\nMöchtest du direkt ein Armband registrieren?"); if(validationRegister) window.location.replace("/login?registerbr");';
		}elseif($checklogin == 2) {
			header('Location: /login?unvalidated='.$user->login);
			exit;
		}elseif ($checklogin === false) {
			header('Location: /login?loginattempt=false');
			exit;
		}
	}else {
		$user = new User(false, $db);
		header('Location: /login?notexisting');
		exit;
	}
}elseif(isset($_SESSION['user'])) {
	$user = new User($_SESSION['user'], $db);
	$checklogin = $user->logged;
}else {
	$user = new User(false, $db);
}

$statistics = new Statistics($db, $user);

if(isset($_GET['language'])){
	if($_GET['language'] == 'de' || $_GET['language'] == 'en'){
		if($user->login) {
			if(isset($_COOKIE['language'])) {
				if($_COOKIE['language'] != $_GET['language']) {
					$sql= "UPDATE users SET lng = :lng WHERE user = :user LIMIT 1";
					$q = $db->prepare($sql);
					$q->execute(array(
						':user' => $user->login,
						':lng' => $_GET['language']
					));
				}
			}else {
				$sql= "UPDATE users SET lng = :lng WHERE user = :user LIMIT 1";
				$q = $db->prepare($sql);
				$q->execute(array(
					':user' => $user->login,
					':lng' => $_GET['language']
				));
			}
		}
		setcookie('language', $_GET['language'], time()+3600*24*365); //für ein Jahr
		$_COOKIE['language'] = $_GET['language'];
	}
}
if(!isset($_COOKIE['language']))
	$lng = getBrowserLanguage(array('de','en'), 'en');
else
	$lng = $_COOKIE['language'];
if(isset($_GET['en'])) $lng = 'en';

if($page == 'armband') {
	if(isset($_GET['absolute'])) {
		$urlData = explode('/', $_GET['absolute']);
		if(isset($urlData[1])) {
			$sql = "SELECT userid FROM users WHERE user = :user";
			$q = $db->prepare($sql);
			$q->execute(array(':user' => $urlData[0]));
			$owner = $q->fetch(PDO::FETCH_ASSOC);
			
			$sql = "SELECT name FROM bracelets WHERE userid = :userid ORDER BY date ASC";
			$q = $db->prepare($sql);
			$q->execute(array(':userid' => $owner['userid']));
			$result = $q->fetchAll(PDO::FETCH_ASSOC);
			if($result != NULL) {
				foreach($result as $key => $val) if($key + 1 == $urlData[1]) $braceName = $val['name'];
				if(isset($urlData[2])) {
					$startPicid = $urlData[2];
					$js .= "$(document.body).animate({
								'scrollTop':   $('#pic-".$startPicid."').offset().top
							}, 2000);";
					$defaultPicid = false;
				}else $defaultPicid = true;
			}else $braceName = false;
		}else $braceName = $_GET['absolute'];
	}
	if(isset($_GET['pic'])) {
		$startPicid = htmlentities($_GET['pic']);
		$js .= "$(document.body).animate({
					'scrollTop': $('#pic-".$startPicid."').offset().top
				}, 2000);";
		$defaultPicid = false;
	}elseif(!isset($startPicid)) {
		$startPicid = 3;
		$defaultPicid = true;
	}
}
//--//

//Maximale Größe für hochgeladene Bilder
$max_file_size = 8000000;

if (!isset($braceName)) $braceName = "";
//Dateinamen werden Titel zugeordnet - Nach dem Alphabet geordnet!!
$pagename = array(
	"404" => $lang->error404[$lng.'-title'],
	"about" => $lang->about[$lng.'-title'],
	"account" => $lang->account[$lng.'-title'],
	'admin' => $lang->admin[$lng.'-title'],
	"armband" => $lang->armband[$lng.'-title'].$braceName,
    "faq" => "FAQ",
	"home" => "Global Bracelet. Travel & Connect",
	"impressum" => $lang->impressum[$lng.'-title'],
	"kontakt" => $lang->kontakt[$lng.'-title'],
	"login" => $lang->login[$lng.'-title'],
	"nachrichten" => $lang->nachrichten[$lng.'-title'],
	"order" => $lang->order[$lng.'-title'],
	"privacy-policy" => $lang->privacypolicy[$lng.'-title'],
	"profil" => $lang->profil[$lng.'-title'],
	"search" => $lang->search[$lng.'-title'],
	"shop" => "Shop",
	"start" => "Community"
);
$navregister['href'] = "login";	
$navregister['value'] = $lang->misc->nav->register->$lng;

if($user->logged) {//Wenn man eingeloggt ist erscheint anstatt 'Registrieren' 'Mein Profil'
	$navregister['href'] = "profil";
	$navregister['value'] = $lang->misc->nav->profil->$lng;
	$notifics = $user->receive_notifications();
	if(!($notifics['pic_owns'] == NULL && $notifics['comm_owns'] == NULL && $notifics['comm_pics'] == NULL && $notifics['pic_subs'] == NULL)) {
		$navregister['value'] = $lang->misc->nav->profil->$lng.' ('.(count($notifics['pic_owns']) + count($notifics['comm_owns']) + count($notifics['comm_pics']) + count($notifics['pic_subs'])).')';
	}
	//
	$admin_stats = $statistics->admin_stats();
	$spam_count = array("comm" => count($admin_stats['spam_comments']), "pic" => count($admin_stats['spam_pics']));
}
if($page == 'login') {
	if(isset($_GET['registerbr'])) {//Wenn man keine ID eingegeben hat lautet der Titel von login.php 'Armband registrieren' und nicht 'Registrieren'
		$pagename['login'] = $lang->login->armband_registrieren->$lng;	
	}
	if(isset($_GET['loginattempt'])) {
		$pagename['login'] = $lang->login->notlogged_pic[$lng.'-title'];
	}
	if(isset($_GET['postpic'])) {
		$pagename['login'] = $lang->login->bildupload->$lng;
	}
}

if (empty($title)) {
    $title = "Placelet - ".$pagename[$page];
}  // Wenn $title nicht gesetzt ist, wird sie zu 'Placelet - $title' geändert
//freundlichere Version von $_SERVER['PHP_SELF']
$friendly_self = $_SERVER['PHP_SELF'];
$friendly_self = str_replace(".php", "", $friendly_self);
if(isset($_GET)) {
	$first = true;
	$friendly_self_get = $friendly_self;
	if($_GET != NULL) $friendly_self_get = $friendly_self.'?';
	$gets = '';
	$keycount = 0;
	foreach($_GET as $key => $val) {
		if($key != 'language'){
			$key = urlencode($key);
			$val = urlencode($val);
			if(!$first) {
				$friendly_self_get .= '&amp;'.$key.'='.$val;
				$gets .= '&amp;'.$key.'='.$val;
			}else {
				$friendly_self_get .= $key.'='.$val;
				$gets .= $key.'='.$val;
				$first = false;
			}
		$keycount++;
		}
	}
	if($keycount <= 0)
		$friendly_self_get = false;
}else {
	$friendly_self_get = false;
}
?>