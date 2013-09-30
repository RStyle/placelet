<?php
error_reporting(E_ALL|E_STRICT); // php5 
ini_set('display_errors', true);

include_once('./other/functions.php'); //Einbinden einer Datei, welche verschiedene PHP-Funktionen bereitstellt, wie z.B. eine Überprüfung, ob die hochgeladene Datei wirklich ein Bild ist
include_once('./start.php');
include_once('./other/user.php');
$test = new User('blabla');


//Hier werden Cookies überprüft gesetzt usw.
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
$_SESSION['login'] = false; //bereits eingeloggt wird vorgegeben und später bei bereits eingeloggt geändert

if(isset($_POST['login']) && isset($_POST['password'])){
$user = new User($_POST['login']);	
$checklogin=$user->login($_POST['password']);
}
if(isset($_POST['reg_name']) && isset($_POST['reg_first_name']) && isset($_POST['reg_login']) && isset($_POST['reg_email']) && isset($_POST['reg_password'])  && isset($_POST['reg_password2'])){
User::register($_POST, $db);
}



//--//

$pagename = array(//Dateinamen werden Titel zugeordnet
	"shop" => "Shop",
	"profil" => "Profil",
	"impressum" => "Impressum",
	"home" => "Startseite",
	"connect" => "Connect",
	"agb" => "AGB",
	"about-us" => "&Uuml;ber Uns"
	);
if (empty($title)) {
    $title = 'Placelet - '.$pagename[$page];
}  // Wenn $title nicht gesetzt ist, wird sie zu 'Placelet - $title' geändert



//Ich denke, dass der Head immer gleich sein wird, auf Wunsch kann das aber geändert werden//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js
echo'
<!DOCTYPE HTML>
<html>
  <head>
    <!--<meta http-equiv="Content-Type" content="text/html" charset="utf-8">-->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="Placelet shop and image service">
    <meta name="keywords" content="Placelet, Placelet Shop, Global Bracelet, Travel & Connect, Global Bracelet. Travel & Connect, Travel and Connect, Global Bracelet. Travel and Connect">
    <meta name="author" content="Roman S., Danial S., Julian Z.">
    <link href="other/main.css" rel="stylesheet" type="text/css">
    <title>'.$title.'</title>
  </head>
  <body>
';
if($checklogin == true)echo "EINGELOGGT";
//Wenn nicht eingeloggt
if($_SESSION['login']==false){
echo'
<form name="login" id="form_login" action="'.$_SERVER['PHP_SELF'].'" method="post">
	<label for="login">Username</label><input type="text" name="login" id="login" size="20" maxlength="30" placeholder="Username" required ><br>
	<label for="password">Password</label><input type="password" name="password" id="password" class="password"  size="20" maxlength="30"  value="!§%&$%&/%§$" required ><br>
	<input type="submit" value="Connect/Login">
</form>
<form name="reg" id="form_reg" action="'.$_SERVER['PHP_SELF'].'" method="post">
	<label for="reg_login">Username</label><input type="text" name="reg_login" id="reg_login" class="input_text" size="20" maxlength="30" placeholder="Username" required ><br><br>
	<label for="reg_name">Name</label><input type="text" name="reg_name" id="reg_name" class="input_text" size="20" maxlength="30" placeholder="Name" required ><br>
	<label for="reg_first_name">First name</label><input type="text" name="reg_first_name" id="reg_first_name" class="input_text" size="20" maxlength="30" placeholder="First name" required ><br><br>
	<label for="reg_email">Email</label><input type="email" name="reg_email" id="reg_email" class="input_text" size="20" maxlength="30" placeholder="Email" required ><br>
	<label for="reg_password">Password</label><input type="password" name="reg_password" id="reg_password" class="password"  size="20" maxlength="30"  value="!§%&$%&/%§$" required ><br>
	<label for="reg_password2">Repeat password</label><input type="password" name="reg_password2" id="reg_password2" class="password" size="20" maxlength="30"  value="!§%&$%&/%§$" required ><br>
	<input type="submit" value="Registrate">
</form>
';}

echo $pagename[$page].' löl'; //zu Testzwecken
include_once('./pages/'.$page.'.php');

echo'
    <ul>
      <li><a href="shop">Shop</a></li>
      <li><a href="profil">Profil</a></li>
      <li><a href="impressum">Impressum</a></li>
      <li><a href="home">Startseite</a></li>
      <li><a href="connect">Connect</a></li>
      <li><a href="agb">AGB</a></li>
      <li><a href="about-us">Über Uns</a></li>
	  <li><a href="design.htm">Design</a></li>
	</ul>
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript" src="./other/script.js?asda"></script>
  </body>
</html>';
?>