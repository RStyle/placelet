<!DOCTYPE HTML>
<html lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="description" content="Placelet - Reisearmband/Travelbracelet">
		<meta name="keywords" content="
			Placelet, Placelet Shop, Shop, Placelet kaufen, buy placelet,
			Global Bracelet, Travel and Connect,
			Armband, bracelet, Reise, Reisearmband, Reisearmband kaufen, Reise Armband, travel, travelbracelet, buy travelbracelet, travel bracelet, 
		">
		<meta name="author" content="Roman S., Daniel S., Julian Z.">
		<meta name="google-site-verification" content="ZkpZK9hxVHOscD4izd7OXGCCjMkpytJMbuOgjO-AWjI">
		<link href="/css/main.css" rel="stylesheet" type="text/css">
		<link href="/css/lightbox.css" rel="stylesheet">
		<!--Google Fonts-->
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Dosis%7COpen+Sans">
<?php
if(is_mobile($_SERVER['HTTP_USER_AGENT']) == TRUE) {//moblie.css für Mobile Clients
?>
		<link href="/var/www/virtual/placelet.de/htdocs/css/mobile.css" rel="stylesheet" type="text/css">
<?php
}
?>
		<link rel="apple-touch-icon" href="/img/touchicon.png">
		<link rel="icon" href="/img/favicon-16.png" type="image/png" sizes="16x16">
		<link rel="icon" href="/img/favicon-32.png" type="image/png" sizes="32x32">
		<!--[if IE]><link rel="shortcut icon" href="img/favicon.ico"><![endif]-->
<?php
if($page == 'armband' && @!$defaultPicid && isset($startPicid) && isset($braceName)) {
	$stmt = $db->prepare('SELECT brid FROM bracelets WHERE name=:name');
	$stmt->execute(array('name' => $braceName));
	$rowbrid = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid=:brid');
	$stmt->execute(array('picid' => $startPicid, 'brid' => $rowbrid['brid']));
	$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
	echo '		<link rel="image_src" href="http://placelet.de/pictures/bracelets/thumb-'.$rowid['id'].'.jpg">';
}
?>
		<meta name="msapplication-TileColor" content="#FFF">
		<meta name="msapplication-TileImage" content="/img/tileicon.png">
		<meta name="viewport" content="width=device-width, initial-scale=1"><!--Verhindert Font-Boosting-->
		<title><?php echo $title; ?></title>
	</head>
	<body id="body">
<?php
if($page == 'home') {
?>
	<div id="fb-root"></div>
	<!---FB-Plugin-->
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/<?php echo $lang->misc->facebooklang->$lng; ?>/all.js#xfbml=1";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<?php
}
?>
<!--###BENACHRICHTIGUNG TAG###-->
<?php
	if($user->login) {
		$msg_notific = $user->receive_messages(true, true);
		if($msg_notific != NULL) {
?>
		<div id="msg_notes">
<?php
		}
		foreach($msg_notific as $sender => $val) {
			$sender = array('name' => Statistics::id2username($sender), 'id' => $sender);
?>
			<div id="note<?php echo $sender['id']; ?>" class="note"><a href="/nachrichten?msg=<?php echo $sender['name']; ?>" style="text-decoration: none;">
				<div class="highlighted float_left">+1&nbsp;</div>
				<img class="float_left" src="/img/msg_icon.png" style="margin-right: 10px;">
				<p style="margin: 0; width: 270px; float: left;"><?php echo $lang->community->new_msg->$lng->p1.$sender['name'].$lang->community->new_msg->$lng->p2; ?></p></a>
				<div data-del_note="<?php echo $sender['id']; ?>" class="del_msg pseudo_link float_right">x</div>
			</div>
<?php
		}
		if($msg_notific != NULL) {
?>
		</div>
<?php
		}
	}
?>
<!--###HEADER TAG###-->
		<header id="header">
			<div id="headerregisterbr">
				<form name="registerbr" action="/search" method="post">
					<label for="squery"><?php echo $lang->misc->search->$lng; ?></label>
					<input name="squery" type="search" id="squery" placeholder="<?php echo $lang->form->suchen->$lng; ?>..." size="20" maxlength="18" required>
				</form>
			</div>
<?php
if($user->logged) {//Wenn man nicht eingeloggt ist, wird Logout angezeigt
?>
			<a href="<?php echo $friendly_self.'?logout'; ?>" id="headerlogin">Logout</a>
<?php
}
else {//Wenn man jedoch nicht eingeloggt ist, kann man die Login-Box öffnen
?>
			<a href="#" id="headerlogin"><img src="/img/login.svg" alt="Login" width="16" height="19" id="login_icon">&nbsp;&nbsp;Login</a>
			<div id="login-box">
				<div class="arrow_up"></div>
				<form name="login" id="form_login" action="<?php echo $friendly_self;?>" method="post">
					<label for="login" id="label_login"><?php echo $lang->form->benutzername->$lng; ?></label><br>
					<input type="text" name="login" id="login" size="20" maxlength="15" placeholder="<?php echo $lang->form->benutzername->$lng; ?>" pattern="\w{4,15}" title="Min.4 - Max.15" required><br>
					<label for="password" id="label_password"><?php echo $lang->form->passwort->$lng; ?></label><br>
					<input type="password" name="password" id="password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required><br>
					<input type="submit" value="Login" id="submit_login">
				</form><br>
				<a href="/account?recoverPassword=yes"><?php echo $lang->form->passwort_vergessen->$lng; ?></a>
			</div>
<?php
}
?>
			<ul id="headerlist">
				<?php if(isset($_SESSION['testserver'])) if($_SESSION['testserver'] === true) echo '<li><a href="/test">Auf die öffentliche Seite</a></li>'; ?>
				<li><a href="<?php echo $friendly_self_get; if(strpos($friendly_self_get,'?')!==false) echo '&amp;language=de'; else echo '?language=de' ?>" hreflang="de"><img src="/img/de_flag.png" alt="Deutsche Flagge" id="de_flag"></a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="<?php echo $friendly_self_get; if(strpos($friendly_self_get,'?')!==false) echo '&amp;language=en'; else echo '?language=en' ?>" hreflang="en"><img src="/img/gb_flag.png" alt="British Flag" id="gb_flag"></a></li>
				<li class="headerlist_main_divider">|</li>
				<li><a href="/privacy-policy"><?php echo $lang->misc->nav->datenschutz->$lng; ?></a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="/impressum"><?php echo $lang->misc->nav->impressum->$lng; ?></a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="/kontakt"><?php echo $lang->misc->nav->kontakt->$lng; ?></a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="/faq"><?php echo $lang->misc->nav->faq->$lng; ?></a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="http://www.juniorprojekt.de" target="_blank">JUNIOR</a></li>
			</ul>
		</header>
<!--###LOGO###-->
		<!--<div id="round_logo">--><a href="http://placelet.de"><img id="logo" src="/img/logo_extended.svg" alt="Placelet" width="206"></a><!--</div>-->
<!--###NAV TAG###-->
		<nav id="mainnav">
			<ul id="mainnavlist">
				<li style="border-left: 1px #fff solid;" class="mainnavlinks<?php if($page == 'home') echo ' mainnavlink_active'?>"><a href="/home" class="navlinks"><?php echo $lang->misc->nav->home->$lng; ?></a></li>
				<li class="mainnavlinks<?php if($page == 'start') echo ' mainnavlink_active'?>"><a href="/community" class="navlinks"><?php echo $lang->misc->nav->community->$lng; ?></a></li>
				<li class="mainnavlinks<?php if($page == 'about') echo ' mainnavlink_active'?>"><a href="/about" class="navlinks"><?php echo $lang->misc->nav->about->$lng; ?></a></li>
				<li class="mainnavlinks<?php if($page == 'shop') echo ' mainnavlink_active'?>"><a href="/shop" class="navlinks"><?php echo $lang->misc->nav->shop->$lng; ?></a></li>
				<li class="mainnavlinks<?php if($page == 'profil') echo ' mainnavlink_active'?>"><a href="/<?php echo $navregister['href']; ?>" class="navlinks" id="registerprofile"><?php echo $navregister['value']; ?></a></li>
				<?php if($user->admin) { ?>
				<li class="mainnavlinks<?php if($page == 'admin') echo ' mainnavlink_active'?>"><a href="/admin" class="navlinks"><?php echo $lang->misc->nav->admin->$lng." (".$spam_count['comm'].", ".$spam_count['pic'].")"; ?></a></li>
				<?php } ?>
				
			</ul>
		</nav>
<!--###SECTION TAG###-->
		<section id="section">
<?php
require_once($this_path.'/pages/'.$page.'.php');
?>
		</section>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<?php 
		$js.= 'lang = new Array();'."\n";
		foreach($lang->js as $obj) {
			foreach($obj as $key => $val) {
				foreach($val as $key_lng => $value) {
						if($key_lng == $lng) $js.= 'lang["'.$key.'"] = "'.$value.'";'."\n";
				}
			}
		}
		$js.= 'lng = "'.$lng.'";';
?>
		<script type="text/javascript" src="/js/exif+jssor+lightbox.js"></script>
		<script type="text/javascript" src="/js/script.js"></script>
<?php
if($page == 'login' && isset($postpic)) {
?>
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBdaJT9xbPmjQRykuZ7jX6EZ0Poi5ZSmfc&amp;sensor=true&amp;v=3.exp"></script>
		<script>
		$(document).ready(function() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(success_postpic, error_postpic);
			} else {
				console.log("GeoLocation API ist NICHT verfügbar!");
			}
		});</script>
<?php
}elseif($page == 'home'){
?>
		<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBdaJT9xbPmjQRykuZ7jX6EZ0Poi5ZSmfc&amp;sensor=true&amp;v=3.exp"></script>
		<script>
		<?php $eecho = '';
		$data = getlnlt();
		$central = '0, 0';
		$max = array(false, false, false, false, 0);
		$i = 0;
		foreach($data as $pos){ 
			if($pos['latitude'] < $max[0] || $max[0] == false)
				$max[0] = $pos['latitude'];
			if($pos['latitude'] > $max[1] || $max[1] == false)
				$max[1] = $pos['latitude'];
			if($pos['longitude'] < $max[2] || $max[2] == false)
				$max[2] = $pos['longitude'];
			if($pos['longitude'] > $max[3] || $max[3] == false)
				$max[3] = $pos['longitude'];
			echo '
			var latlng'.$i.' = new google.maps.LatLng('.$pos['latitude'].', '.$pos['longitude'].');';
			$eecho .= '
			var marker'.$i.' = new google.maps.Marker({
				position: latlng'.$i.',
				map: map
		  });'; $i++; }
				$central = ($max[0]+($max[1]-$max[0])/2) . ', ' . ($max[2]+($max[3]-$max[2])/2);
				$max[4] = ($max[1]-$max[0]);
				if(($max[3]-$max[2]) > $max[4])
					$max[4] = ($max[3]-$max[2]);
					
				$zoom = 1;
				if($max[4] < 0.02)
					$zoom = 14;
				else if($max[4] < 0.0625)
					$zoom = 12;
				else if($max[4] < 0.125)
					$zoom = 11;
				else if($max[4] < 0.25)
					$zoom = 10;
				else if($max[4] < 0.5)
					$zoom = 9;
				else if($max[4] < 1)
					$zoom = 8;
				else if($max[4] < 2)
					$zoom = 7;
				else if($max[4] < 5)
					$zoom = 6;
				else if($max[4] < 6.5)
					$zoom = 5;
				else if($max[4] < 18)
					$zoom = 4;
				else if($max[4] < 40)
					$zoom = 3;
				else if($max[4] < 80)
					$zoom = 2;
			?>
		function initialize() {
		  var mapOptions = {
			zoom: <?php echo $zoom; ?>,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: new google.maps.LatLng(<?php echo $central ?>)
		  }
		var map = new google.maps.Map(document.getElementById('map_home'), mapOptions);
		
		<?php echo $eecho; ?>
		}
		google.maps.event.addDomListener(window, 'load', initialize);
		
		</script>
<?php
}elseif($page == 'armband'){
?>
		<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBdaJT9xbPmjQRykuZ7jX6EZ0Poi5ZSmfc&amp;sensor=true&amp;v=3.exp"></script>
<?php
} if ($page != 'community' && $page != 'start') { ?>
		<br><br><footer <?php if(is_mobile($_SERVER['HTTP_USER_AGENT']) == false && in_array($page,array('shop', 'kontakt', 'faq', 'nachrichten'))){ echo' class="footer_down'; } ?> style="position:relative;bottom:0px;"><small>&copy; 2013-2014 Placelet <span class="headerlist_sub_divider">|</span> <a href="/privacy-policy"><?php echo $lang->misc->nav->datenschutz->$lng; ?></a> <span class="headerlist_sub_divider">|</span> <a href="/impressum"><?php echo $lang->misc->nav->impressum->$lng; ?></a></small></footer>
<?php } 	/*$js.='
	function header(){
	if($(window).width() < 1100)
		$("#header").css("width", "1100px" );
	else
		$("#header").css("width", "100%" );
	}
	window.onresize = header;';*//*
	$js.='
	var p = $( "#footer" );
	var position = p.position();
	console.log(position.top);
	parseInt($("#profil").position().top) + parseInt($("#profil").css("height"))
	if($("footer").position().top < $(window).height() ){ $("footer").css("position","absolute");$("footer").css("top",($(window).height() - 24) + "px");}
	if((parseInt($("#profil").position().top) + parseInt($("#profil").css("height"))) > $("footer").position().top){ $("footer").css("position","absolute");$("footer").css("top",($(window).height() - 24) + "px");} ';*/

if($js != '<script type="text/javascript">$(document).ready(function(){') {
	$js .= '});</script>'; echo $js;
}
?>
	</body>
</html>