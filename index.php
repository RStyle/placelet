<?php
error_reporting(E_ALL|E_STRICT); // php5 
ini_set('display_errors', true);

include_once('./other/functions.php'); //Einbinden einer Datei, welche verschiedene PHP-Funktionen bereitstellt, wie z.B. eine Überprüfung, ob die hochgeladene Datei wirklich ein Bild ist
include_once('./start.php');
include_once('./other/user.php');

if(isset($_GET['regstatuschange']) && isset($_GET['regstatuschange_user'])){
$user = new User($_GET['regstatuschange_user'], $db); //substr(md5 (uniqid (rand())), 0, 20)
$user->regstatuschange($_GET['regstatuschange']);

//header('Location: LINK');/*
//break();
}

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

$checklogin = false;

if(isset($_GET['logout']))
	User::logout();

if(isset($_POST['login']) && isset($_POST['password'])){
	$user = new User($_POST['login'], $db);	
	$checklogin=$user->login($_POST['password']);
}elseif(isset($_SESSION['user'])){
	$user = new User('RSty', $db);
	$checklogin = $user->logged;
	echo $_SESSION['user'].'-'.$_SESSION['dynamic_password'];//Randnotiz
}
if(isset($_POST['reg_name']) && isset($_POST['reg_first_name']) && isset($_POST['reg_login']) && isset($_POST['reg_email']) && isset($_POST['reg_password'])  && isset($_POST['reg_password2'])){
	User::register($_POST, $db);
}



//--//

$pagename = array(//Dateinamen werden Titel zugeordnet
	"shop" => "Shop",
	"profil" => "Profil",
	"impressum" => "Impressum",
	"home" => "Global Placelet. Travel & Connect",
	"connect" => "Connect",
	"agb" => "AGB",
	"about" => "&Uuml;ber Uns",
	"kontakt" => "Konkakt"
	);
if (empty($title)) {
    $title = 'Placelet - '.$pagename[$page];
}  // Wenn $title nicht gesetzt ist, wird sie zu 'Placelet - $title' geändert



//Ich denke, dass der Head immer gleich sein wird, auf Wunsch kann das aber geändert werden//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js
echo'
<!DOCTYPE HTML>
<html lang="de">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="Placelet Shop and Image Service">
    <meta name="keywords" content="Placelet, Placelet Shop, Global Bracelet, Travel & Connect, Global Bracelet. Travel & Connect, Travel and Connect, Global Bracelet. Travel and Connect">
    <meta name="author" content="Roman S., Danial S., Julian Z.">
    <link href="other/main.css" rel="stylesheet" type="text/css">
    <link rel="apple-touch-icon" href="pictures/touchicon.png">
    <link rel="icon" href="pictures/favicon-16.png" type="image/png" sizes="16x16">
    <link rel="icon" href="pictures/favicon-32.png" type="image/png" sizes="32x32">
    <!--[if IE]><link rel="shortcut icon" href="pictures/favicon.ico"><![endif]-->
    <meta name="msapplication-TileColor" content="#FFF">
    <meta name="msapplication-TileImage" content="pictures/tileicon.png">
    <title>'.$title.'</title>
  </head>
  <body> 
  
    <header id="header">
      <div id="headerregisterbr">
        <form name="registerbr" action="'.$_SERVER['PHP_SELF'].'" method="post">
          <label for="registerbr">Band registrieren&nbsp;</label>
          <input name="registerbr" type="text" required="required" id="registerbr" placeholder="Placelet ID..." size="20" maxlength="30">
        </form>
      </div>
      <a href="#" id="headerlogin"><img src="pictures/login.svg" alt="Login" width="16" height="19">&nbsp;&nbsp;Login</a>
	  <div id="login-box">
	    <form name="login" id="form_login">
		  <label for="login" id="label_login">Username</label>
		  <input type="text" name="login" id="login" size="20" maxlength="15" placeholder="Username" required>
		  <label for="password" id="label_password">Password</label>
		  <input type="password" name="password" id="password" class="password"  size="20" maxlength="30"  value="!§%$$%\/%§$" required>
		  <input type="submit" value="Login" id="submit_login">
		</form>
		<a href="register">Hier registrieren</a>
      </div>
      <ul id="headerlist">
        <li><a href="ger"><img src="pictures/de_flag.png" alt="Deutsche Flagge" id="de_flag"></a></li>
		<li class="headerlist_sub_divider">|</li>
        <li><a href="eng"><img src="pictures/gb_flag.png" alt="British Flag" id="gb_flag"></a></li>
		<li class="headerlist_main_divider">|</li>
        <li><a href="impressum">Impressum</a></li>
		<li class="headerlist_sub_divider">|</li>
        <li><a href="kontakt">Kontakt</a></li>
		<li class="headerlist_sub_divider">|</li>
        <li><a href="http://www.juniorprojekt.de" target="_blank">JUNIOR</a></li>
      </ul>
    </header>
    
    <a href="http://placelet.de"><img id="logo" src="pictures/logo_extended.svg" alt="Placelet"></a>
    
    <nav id="mainnav">
      <ul id="mainnavlist">
        <li><a href="home" class="mainnavlinks">Home</a></li>
        <li><a href="about" class="mainnavlinks">Über uns</a></li>
        <li><a href="shop" class="mainnavlinks">Shop</a></li>
      </ul>
    </nav>
	
	<section id="section">
';
/*if($checklogin == true)echo "EINGELOGGT"; else echo "PECH";
//Wenn nicht eingeloggt
if(isset($user)){if($user->logged == true){echo "2EINGELOGGT2"; echo '<a href="?logout">LOGOUT</a>';} else echo "2PECH2";}
if(isset($_SESSION['user']))echo'- USERISTDA';
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

echo $pagename[$page].' löl'; //zu Testzwecken*/
include_once('./pages/'.$page.'.php');

echo'
    </section>
    <div id="sidenav">
      <ul id="sidenav">
        <li>Erster Eintrag</li>
        <li>Zweiter Eintrag</li>
        <li>Dritter Eintrag</li>
      </ul>
    </div>
    
    <footer id="footer">
    Placelet - by Daniel, Julian, Roman
    </footer>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript" src="./other/script.js?asda"></script>
  </body>
</html>';
?>