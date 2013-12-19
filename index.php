<!DOCTYPE HTML>
<html lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="description" content="Placelet Shop and Image Service">
		<meta name="keywords" content="Placelet, Placelet Shop, Global Bracelet, Travel & Connect, Global Bracelet. Travel & Connect, Travel and Connect, Global Bracelet. Travel and Connect">
		<meta name="author" content="Roman S., Danial S., Julian Z.">
		<link href="<?php echo $this_path_html; ?>css/main.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $this_path_html; ?>css/lightbox.css" rel="stylesheet">
		<!--Google Fonts-->
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Dosis|Open+Sans">
<?php
if(is_mobile($_SERVER['HTTP_USER_AGENT']) == TRUE) {//moblie.css für Mobile Clients
?>
		<link href="/var/www/virtual/placelet.de/htdocs/css/mobile.css" rel="stylesheet" type="text/css">
<?php
}
?>
		<link rel="apple-touch-icon" href="<?php echo $this_path_html; ?>img/touchicon.png">
		<link rel="icon" href="<?php echo $this_path_html; ?>img/favicon-16.png" type="image/png" sizes="16x16">
		<link rel="icon" href="<?php echo $this_path_html; ?>img/favicon-32.png" type="image/png" sizes="32x32">
		<!--[if IE]><link rel="shortcut icon" href="img/favicon.ico"><![endif]-->
		<meta name="msapplication-TileColor" content="#FFF">
		<meta name="msapplication-TileImage" content="img/tileicon.png">
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
      js.src = "//connect.facebook.net/de_DE/all.js#xfbml=1";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<?php
}
?>
<!--###HEADER TAG###-->
		<header id="header">
			<div id="headerregisterbr">
				<form name="registerbr" action="search" method="get">
					<label for="squery">Benutzer/Armband suchen </label>
					<input name="squery" type="search" id="squery" placeholder="Suchen..." size="20" maxlength="18" required>
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
			<a href="#" id="headerlogin"><img src="img/login.svg" alt="Login" width="16" height="19" id="login_icon">&nbsp;&nbsp;Login</a>
			<div id="login-box">
				<div class="arrow_up"></div>
				<form name="login" id="form_login" action="<?php echo $friendly_self;?>" method="post">
					<label for="login" id="label_login">Benutzername</label><br>
					<input type="text" name="login" id="login" size="20" maxlength="15" placeholder="Username" pattern=".{4,15}" title="Min.4 - Max.15" required><br>
					<label for="password" id="label_password">Passwort</label><br>
					<input type="password" name="password" id="password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required><br>
					<input type="submit" value="Login" id="submit_login">
				</form><br>
				<a href="account?recoverPassword=yes">Passwort vergessen?</a>
			</div>
<?php
}
?>
			<ul id="headerlist">
				<li><a href="http://placelet.de<?php echo $friendly_self_get; ?>"><img src="img/de_flag.png" alt="Deutsche Flagge" id="de_flag"></a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="http://placelet.net<?php echo $friendly_self_get; ?>"><img src="img/gb_flag.png" alt="British Flag" id="gb_flag"></a></li>
				<li class="headerlist_main_divider">|</li>
				<li><a href="impressum">Impressum</a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="kontakt">Kontakt</a></li>
				<li class="headerlist_sub_divider">|</li>
				<li><a href="http://www.juniorprojekt.de" target="_blank">JUNIOR</a></li>
			</ul>
		</header>
<!--###LOGO###-->
		<a href="http://placelet.de"><img id="logo" src="img/neueFarbenLogoExtended.svg" alt="Placelet"></a>
<!--###NAV TAG###-->
		<nav id="mainnav">
			<ul id="mainnavlist">
				<li style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;" class="mainnavlinks<?php if($page == 'home') echo ' mainnavlink_active'?>"><a href="home" class="navlinks">Home</a></li>
				<li class="mainnavlinks<?php if($page == 'start') echo ' mainnavlink_active'?>"><a href="start" class="navlinks">Start</a></li>
				<li class="mainnavlinks<?php if($page == 'about') echo ' mainnavlink_active'?>"><a href="about" class="navlinks">Das Team</a></li>
				<li class="mainnavlinks<?php if($page == 'shop') echo ' mainnavlink_active'?>"><a href="shop" class="navlinks">Shop</a></li>
				<li 
				<?php if (!($user->admin)) { ?>
                style="margin-right: 0; border-top-right-radius: 10px; border-bottom-right-radius: 10px;" 
                <?php } ?>
                class="mainnavlinks<?php if($page == 'profil') echo ' mainnavlink_active'?>"><a href="<?php echo $navregister['href']; ?>" class="navlinks"><?php echo $navregister['value']; ?></a></li>
				<?php if($user->admin) { ?>
				<li style="margin-right: 0; border-top-right-radius: 10px; border-bottom-right-radius: 10px;" class="mainnavlinks<?php if($page == 'admin') echo ' mainnavlink_active'?>"><a href="admin" class="navlinks">Admin-Tools</a></li>
				<?php } ?>
				
			</ul>
		</nav>
<!--###SECTION TAG###-->
		<section id="section">
<?php
require_once($this_path.'pages/'.$page.'.php');
?>

		</section>
		<script>username = '<?php echo $user->login; ?>';</script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="./js/script.js"></script>
		<?php if($js != '<script type="text/javascript">$(document).ready(function(){'){ $js .= '});</script>'; echo $js;} ?>
		<script src="js/lightbox-2.6.min.js"></script>
<?php
if($page == 'login' && isset($postpic)) {
?>
		<script type="text/javascript" src="./js/jquery.exif.js"></script>
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBdaJT9xbPmjQRykuZ7jX6EZ0Poi5ZSmfc&sensor=true&v=3.exp"></script>
		<script>
		$(document).ready(function() {
			
			$('#registerpic_city').on({
				blur:function(){
					geocoder = new google.maps.Geocoder();
					var address = $('#registerpic_city').val() + "," + $('#registerpic_country').val();
					geocoder.geocode({ 'address': address }, function (results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							console.log(results[0].geometry.location.ob + "," + results[0].geometry.location.nb);
							lat = results[0].geometry.location.nb;
							$("#latitude").val(lat.toString());
							long = results[0].geometry.location.ob;
							$("#longitude").val(long.toString());
						  initialize(results[0].geometry.location, lat, long);
						} else if(status == google.maps.GeocoderStatus.ZERO_RESULTS) {
							alert('Dieser Ort wurde nicht gefunden.');
						} else {
							alert('Geocode was not successful for the following reason: ' + status);
						}
					});
				}
			});
		
			function initialize(coords, this_lat, this_lng) {
				if(this_lat != false && this_lng != false)
					var latlng = new google.maps.LatLng(this_lat, this_lng);
				else
					var latlng = new google.maps.LatLng(coords.latitude, coords.longitude);
				
				$.ajax({
					type: 'GET',
					dataType: "json",
					url: "http://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+long+"&sensor=false",
					data: {},
					success: function(data) {
						city = "";
						bundesland = "";
						country = "";
						$('#registerpic_city').val("");
						$('#registerpic_country').val("");
						$.each( data['results'],function(i, val) {
							$.each( val['address_components'],function(i, val) {
								if (val['types'] == "locality,political") {
									if (val['long_name']!="") {
										city = val['long_name'];
									}
								}
								if (val['types'] == "country,political") {
									if (val['long_name']!="") {
										country = val['long_name'];
									}
								}
								//if (val['types'].indexOf("administrative_area_level_1") >= 0) {
								if (val['types'] == "administrative_area_level_1,political") {
									if (val['long_name']!="") {
										bundesland = val['long_name'];
									//console.log(i+", " + val['long_name']);
									//console.log(i+", " + val['types']);
									}
								}
							});
							$('#registerpic_city').val(city);
							$('#registerpic_state').val(bundesland);
							$('#registerpic_country').val(country);
						});
						console.log('Success');
					},
					error: function () { console.log('error'); } 
				}); 
			
				var myOptions = {
					zoom: 8,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					tilt: 0
				};
				
				if(mapset == true){
					marker.setPosition(latlng);
					console.log("moved the marker");
				} else {
					map = new google.maps.Map(document.getElementById("pos"), myOptions);
					mapset = true;
						console.log("newmap");
					
					marker = new google.maps.Marker({
						position: latlng, 
						map: map, 
						title: "Hier bist du :)",
						draggable: true
					}); 
					
					//map.setCenter(marker.position);
					google.maps.event.addListener(marker, 'dragend', function(evt) {
						map.setCenter(marker.position);
						
						lat = evt.latLng.lat().toFixed(6);
						long = evt.latLng.lng().toFixed(6);
						
						$.ajax({
							type: 'GET',
							dataType: "json",
							url: "http://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+long+"&sensor=false",
							data: {},
							success: function(data) {
								city = "";
								bundesland = "";
								country = "";
								$('#registerpic_city').val("");
								$('#registerpic_country').val("");
								$.each( data['results'],function(i, val) {
									$.each( val['address_components'],function(i, val) {
										if (val['types'] == "locality,political") {
											if (val['long_name']!="") {
												city = val['long_name'];
												//return;
											}
										}
										if (val['types'] == "country,political") {
											if (val['long_name']!="") {
												country = val['long_name'];
											}
										}
										if (val['types'] == "administrative_area_level_1,political") {
											if (val['long_name']!="") {
												bundesland = val['long_name'];
											}
										}
									});
								});
								$('#registerpic_city').val(city);
								$('#registerpic_state').val(bundesland);
								$('#registerpic_country').val(country);
								$("#latitude").val(lat.toString());
								$("#longitude").val(long.toString());
								console.log('Success');
							},
							error: function () { console.log('error'); } 
						}); 
					});
				}
				
				map.setCenter(marker.position);
				
			}
			
			function success(position) {
				lat = position.coords.latitude;
				$("#latitude").val(lat.toString());
				long = position.coords.longitude;
				$("#longitude").val(long.toString());
				initialize(position.coords, false, false);
				//console.log(position.coords);
			}
			
			function error(msg) {
				console.log(typeof msg == 'string' ? msg : "error123");
				
				//http://maps.googleapis.com/maps/api/geocode/json?address=Bad%20Kreuznach,%20Germany&sensor=true
				/*$.ajax({
						type: 'GET',
						dataType: "json",
						url: "http://maps.googleapis.com/maps/api/geocode/json?address=" + $('#registerpic_city').val() + "," + $('#registerpic_country').val() + "&sensor=true",
						data: {},
						success: function(data) {
							//$('#latitude').val("");
							//$('#longitude').val("");
							$.each( data['results'],function(i, val) {
								$.each( val['address_components'],function(i, val) {
									if (val['types'] == "locality,political") {
										if (val['long_name']!="") {
											$('#registerpic_city').val(val['long_name']);
											//return;
										}
										else {
											$('#registerpic_city').val("");
										}
										console.log(i+", " + val['long_name']);
										console.log(i+", " + val['types']);
									}
									if (val['types'] == "country,political") {
										if (val['long_name']!="") {
											$('#registerpic_country').val(val['long_name']);
										}
										else {
											$('#registerpic_country').val("");
										}
										console.log(i+", " + val['long_name']);
										console.log(i+", " + val['types']);
									}
								});
							});
							console.log('Success');
						},
						error: function () { console.log('error'); } 
					});*/
				
				/*lat = position.coords.latitude;
				$("latitude").val(lat);
				long = position.coords.longitude;
				$("longitude").val(long);
				initialize(position.coords);*/
			}
			
			var mapset = false;
			var map;
			var marker;
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(success, error);
			} else {
				console.log("GeoLocation API ist NICHT verfügbar!");
			}
		});</script>
<?php
}
?>
	</body>
</html>