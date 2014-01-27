<?php
date_default_timezone_set("Europe/Berlin");
// Alle Fehlermeldungen werden angezeigt
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', true);
//Einbinden der Dateien, die Funktionen, MySQL Daten und PDO Funktionen enthalten
if($_SERVER['SERVER_NAME'] == 'localhost') {
	$this_path = '';
	$this_path_html = '';
}else {
	$this_path = '/var/www/virtual/placelet.de/htdocs/';
	$this_path_html = 'http://www.placelet.de/';
}
$lang = simplexml_load_file('./text/translations.xml');
$lng = 'en';
if(isset($_GET['en'])) $lng = 'en';
require_once($this_path.'scripts/recaptchalib.php');
require_once($this_path.'scripts/functions.php'); 
require_once($this_path.'scripts/connection.php');
require_once($this_path.'scripts/user.php');

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

$js = '<script type="text/javascript">$(document).ready(function(){';
foreach($lang->js as $key => $val) {
	$js.= 'langjs['.$key.'] = '.$val->$lng;
}

if(isset($_GET['logout']))  {
	User::logout();
	header('Location: home');
	exit;
}

$checklogin = false;
if(isset($_POST['login']) && isset($_POST['password'])){
	if(Statistics::userexists($_POST['login'])) {
		$user = new User($_POST['login'], $db);	
		$checklogin = $user->login($_POST['password']);
		if($checklogin === true) {
			if(isset($_POST['login_location'])) {
				header('Location: '.$_POST['login_location']);
				exit;
			}else {
				header('Location: start');
				exit;
			}
		}elseif($checklogin == 3) {
			$js .= 'validationRegister = confirm("Bestätigung erfolgreich.\\nMöchtest du direkt ein Armband registrieren?"); if(validationRegister) window.location.replace("/login?registerbr");';
		}elseif($checklogin == 2) {
			header('Location: login?unvalidated='.$user->login);
			exit;
		}elseif ($checklogin === false) {
			header('Location: login?loginattempt=false');
			exit;
		}
	}else {
		$user = new User(false, $db);
		header('Location: login?notexisting');
		exit;
	}
}elseif(isset($_SESSION['user'])) {
	$user = new User($_SESSION['user'], $db);
	$checklogin = $user->logged;
}else {
	$user = new User(false, $db);
}

$statistics = new Statistics($db, $user);

//--//

//Maximale Größe für hochgeladene Bilder
$max_file_size = 8000000;

if (!isset($braceName)) $braceName = "";
//Dateinamen werden Titel zugeordnet - Nach dem Alphabet geordnet!!
$pagename = array(
	"404" => "Seite nicht gefunden",
	"about" => "&Uuml;ber Uns",
	"account" => "Account Einstellungen",
	'admin' => 'Admin',
	"armband" => "Armband: ".$braceName,
	"connect" => "Connect",
    "faq" => "FAQ",
	"home" => "Global Bracelet. Travel & Connect",
	"impressum" => "Impressum",
	"kontakt" => "Konkakt",
	"login" => "Registrieren",
	"order" => "Bestellung",
	"profil" => "Profil",
	"search" => "Suchergebnis",
	"shop" => "Shop",
	"start" => "Start"
);
$navregister['href'] = "login";	
$navregister['value'] = $lang->misc->nav->register->$lng;

if($user->logged) {//Wenn man eingeloggt ist erscheint anstatt 'Registrieren' 'Mein Profil'
	$navregister['href'] = "profil";
	$navregister['value'] = $lang->misc->nav->profil->$lng;
	$notifics = $user->recieve_notifications();
	if(!($notifics['pic_owns'] == NULL && $notifics['comm_owns'] == NULL && $notifics['comm_pics'] == NULL && $notifics['pic_subs'] == NULL)) {
		$navregister['value'] = $lang->misc->nav->profil->$lng.' ('.(count($notifics['pic_owns']) + count($notifics['comm_owns']) + count($notifics['comm_pics']) + count($notifics['pic_subs'])).')';
	}
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
	$friendly_self_get = $friendly_self.'?';
	$gets = '';
	foreach($_GET as $key => $val) {
		if(!$first) {
			$friendly_self_get .= '&'.$key.'='.$val;
			$gets .= '&'.$key.'='.$val;
		}else {
			$friendly_self_get .= $key.'='.$val;
			$gets .= $key.'='.$val;
		}
	}
}else {
	$friendly_self_get = false;
}
?>