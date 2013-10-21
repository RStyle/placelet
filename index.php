<?php
date_default_timezone_set("Europe/Berlin");
// Alle Fehlermeldungen werden angezeigt
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors', true);
//Einbinden der Dateien, die Funktionen, MySQL Daten und PDO Funktionen enthalten
include_once('./scripts/functions.php'); 
include_once('./scripts/connection.php');
include_once('./scripts/user.php');

if(isset($_GET['regstatuschange']) && isset($_GET['regstatuschange_user'])){
$user = new User($_GET['regstatuschange_user'], $db); //substr(md5 (uniqid (rand())), 0, 20)
$user->regstatuschange($_GET['regstatuschange']);

//header('Location: LINK');/*
//break();
}

// Hier werden Cookies überprüft gesetzt usw.
// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
ini_set( 'session.use_only_cookies', '1' );
ini_set( 'session.use_trans_sid', '0' );
session_start(); //Session starten
if (!isset( $_SESSION['server_SID'] ))
{
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

$checklogin = false;

if(isset($_GET['logout']))
	User::logout();

if(isset($_POST['login']) && isset($_POST['password'])){
	$user = new User($_POST['login'], $db);	
	$checklogin = $user->login($_POST['password']);
	if ($checklogin == false) {
		header('Location: login?loginattempt=false');
		exit;
	}
}elseif(isset($_SESSION['user'])){
	$user = new User($_SESSION['user'], $db);
	$checklogin = $user->logged;
	//echo $_SESSION['user'].'-'.$_SESSION['dynamic_password'];//Das nervt!!
}else{
$user = new User(false, $db);
}

//Maximale Größe für hochgeladene Bilder
$max_file_size = 8000000;

if(isset($_POST['reg_name']) && isset($_POST['reg_first_name']) && isset($_POST['reg_login']) && isset($_POST['reg_email']) && isset($_POST['reg_password'])  && isset($_POST['reg_password2'])){
	User::register($_POST, $db);
}
//--//

if (!isset($braceID)) { $braceID = ""; }
//Dateinamen werden Titel zugeordnet
$pagename = array(
	"about" => "&Uuml;ber Uns",
	"armband" => "Armband ID: ".$braceID,
	"connect" => "Connect",
	"home" => "Global Placelet. Travel & Connect",
	"impressum" => "Impressum",
	"kontakt" => "Konkakt",
	"login" =>"Registrieren",
	"profil" => "Profil",
	"shop" => "Shop",
	"start" => "Start"
	);
	
$navregister['href'] = "login";	
$navregister['value'] = "Registrieren";

if($user->logged) {//Wenn man eingeloggt ist erscheint anstatt 'Registrieren' 'Mein Profil'
	$navregister['href'] = "profil";
	$navregister['value'] = "Mein Profil";
}
	
if(isset($_GET['registerbr'])) {//Wenn man keine ID eingegeben hat lautet der Titel von login.php 'Armband registrieren' und nicht 'Registrieren'
	$pagename['login'] = "Armband registrieren";	
}

if (empty($title)) {
    $title = "Placelet - ".$pagename[$page];
}  // Wenn $title nicht gesetzt ist, wird sie zu 'Placelet - $title' geändert
$friendly_self = $_SERVER['PHP_SELF'];
$friendly_self = str_replace(".php", "", $friendly_self);
//Ich denke, dass der Head immer gleich sein wird, auf Wunsch kann das aber geändert werden//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="description" content="Placelet Shop and Image Service">
		<meta name="keywords" content="Placelet, Placelet Shop, Global Bracelet, Travel & Connect, Global Bracelet. Travel & Connect, Travel and Connect, Global Bracelet. Travel and Connect">
		<meta name="author" content="Roman S., Danial S., Julian Z.">
		<link href="css/main.css" rel="stylesheet" type="text/css">
		<link href="css/lightbox.css" rel="stylesheet">
<?php
if(is_mobile($_SERVER['HTTP_USER_AGENT']) == TRUE) {//moblie.css für Mobile Clients
?>
		<link href="css/mobile.css" rel="stylesheet" type="text/css">
<?php
}
?>
		<link rel="apple-touch-icon" href="img/touchicon.png">
		<link rel="icon" href="img/favicon-16.png" type="image/png" sizes="16x16">
		<link rel="icon" href="img/favicon-32.png" type="image/png" sizes="32x32">
		<!--[if IE]><link rel="shortcut icon" href="img/favicon.ico"><![endif]-->
		<meta name="msapplication-TileColor" content="#FFF">
		<meta name="msapplication-TileImage" content="img/tileicon.png">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $title; ?></title>
	</head>
	<body> 
<!--###HEADER TAG###-->
		<header id="header">
			<div id="headerregisterbr">
				<form name="registerbr" action="login" method="get">
					<label for="registerbr">Armband registrieren/Bild posten </label>
					<input name="registerbr" type="text" required="required" id="registerbr" placeholder="Armband ID..." size="20" maxlength="30">
				</form>
			</div>
<?php
if($user->logged) {//Wenn man nicht eingeloggt ist, wird Logout angezeigt
?>
			<a href="?logout" id="headerlogin">Logout</a>
<?php
}
else {//Wenn man jedoch nicht eingeloggt ist, kann man die Login-Box öffnen
?>
			<a href="#" id="headerlogin"><img src="img/login.svg" alt="Login" width="16" height="19" id="login_icon">&nbsp;&nbsp;Login</a>
			<div id="login-box">
				<form name="login" id="form_login" action="start" method="post">
					  <label for="login" id="label_login">Benutzername</label><br>
					  <input type="text" name="login" id="login" size="20" maxlength="15" placeholder="Username" pattern=".{4,15}" title="Min.4 - Max.15" required><br>
					  <label for="password" id="label_password">Passwort</label><br>
					  <input type="password" name="password" id="password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required><br>
					  <input type="submit" value="Login" id="submit_login">
				</form><br>
				<a href="login">Hier registrieren</a>
			</div>
<?php
}
?>
            <ul id="headerlist">
				<li><a href="http://placelet.de<?php echo $friendly_self; ?>"><img src="img/de_flag.png" alt="Deutsche Flagge" id="de_flag"></a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="http://placelet.net<?php echo $friendly_self; ?>"><img src="img/gb_flag.png" alt="British Flag" id="gb_flag"></a></li>
				<li class="headerlist_main_divider">|</li>
				<li><a href="impressum">Impressum</a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="kontakt">Kontakt</a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="http://www.juniorprojekt.de" target="_blank">JUNIOR</a></li>
            </ul>
		</header>	
<!--###LOGO###-->
		<a href="http://placelet.de"><img id="logo" src="img/logo_extended.svg" alt="Placelet"></a>
<!--###NAV TAG###-->
		<nav id="mainnav">
			<ul id="mainnavlist">
				<li><a href="home" class="mainnavlinks">Home</a></li>
				<li><a href="about" class="mainnavlinks">Über uns</a></li>
				<li><a href="shop" class="mainnavlinks">Shop</a></li>
				<li><a href="start" class="mainnavlinks">Start</a></li>
				<li><a href="<?php echo $navregister['href']; ?>" class="mainnavlinks"><?php echo $navregister['value']; ?></a></li>
			</ul>
		</nav>
<!--###SECTION TAG###-->
        <section id="section">
<?php
include_once('./pages/'.$page.'.php');
?>

		</section>
		<!--<script src="js/jquery-1.10.2.min.js"></script>-->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="./js/script.js"></script>
		<script src="js/lightbox-2.6.min.js"></script>
	</body>
</html>