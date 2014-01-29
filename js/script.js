var currentPath = $(location).attr('pathname');
$(document).ready(function() {
	// starte wenn DOM geladen ist
	
var Input_password = $('input[name=password]');
var default_reg_password_value = $('input[name=reg_password]').val();
var default_password_value = Input_password.val();
if (default_password_value == '')
	default_password_value = default_reg_password_value;
var show_login = false;
var login_return = true;

//Login-Box anzeigen
$("#headerlogin").click(function(){
	show_login = !show_login;
	if(show_login == true) {
		$("#login-box").show();
		$("#login").focus();
	}else {
		$("#login-box").hide();
	}
});

jQuery(document).click(function(e) {
    if (e.target.id != 'login-box' && e.target.id != 'login' && e.target.id != 'password' && e.target.id != 'form_login' && e.target.id != 'headerlogin' && e.target.id != 'login_icon'
		&& e.target.id != 'label_login' && e.target.id != 'label_password' && e.target.id != 'submit_login') {
        if(show_login == true){
			show_login = false;
			$("#login-box").hide();
		}
    }
});


//Login
$('.password').on({
    focus:function(){                   
		if(this.value == default_password_value || this.value == default_reg_password_value) this.value = '';
    },
    blur:function(){
		if(this.value == '') this.value = default_password_value;
    }
})

$("#form_login").submit(function() {
	if ($("#password").val() == default_password_value) {
	$("#password").select(); 
		return false;
	}
	
	if($("#login").val().length < 4 || $("#login").val().length > 15){
		$("#login").css( "background-color", "rgb(255, 66, 66)" );
		setTimeout(function() {
			$("#login").css( "background-color", "#FFF" );
		}, 800);
		login_return = false;
	}
	
	if($("#password").val().length < 6 || $("#login").val().length > 30){
		$("#password").css( "background-color", "rgb(255, 66, 66)" );
		setTimeout(function() {
			$("#password").css( "background-color", "#FFF" );
		}, 800);
		login_return = false;
	}
	
	return login_return;
});

$(".input_text").blur(function(){
		if(this.value != $.trim(this.value)) this.value = $.trim(this.value);  //trimt Formualer - außer Passwörter - direkt per JS
    }
);


//Registration
$("#form_reg").submit(function() {
	if ($("#reg_password").val() == default_password_value) {
	$("#reg_password").select();
		return false;
	}
	if ($("#reg_password2").val() == default_password_value) {
	$("#reg_password2").select();
		return false;
	}
	
	if ($("#reg_password").val() != $("#reg_password2").val()) {
		$('#reg_password, #reg_password2').each(function() {
			this.setCustomValidity(lang['passwoerter_unpassend'][lng]) //Errormeldung bei beiden Inputelementen - browserspezifisch, Chrome erkennt nur das erste, Firefox & IE10 beide
			
		});
		return false;
	}
});

//Emailüberprüfung
/*
Klappt irgendwie nicht :'(
$("#reg_password2").oninput(function() {
	if ($("#reg_password2").value != $('#reg_email').value) {
		document.getElementById("reg_password2").setCustomValidity('The two email addresses must match.');
	} else {
		// input is valid -- reset the error message
		document.getElementById("reg_password2").setCustomValidity('');
	}
});*/




//
$('#holder').on(
    'dragover',
    function(e) {
        e.preventDefault();
        e.stopPropagation();
    }
)
$('#holder').on(
    'dragenter',
    function(e) {
        e.preventDefault();
        e.stopPropagation();
    }
)

//Überprüfung ob Stadt bei Bildupload exestiert bzw. Korrektur
$("#form_login").submit(function() {
	geocoder = new google.maps.Geocoder();
	var address = $('#registerpic_city').val() + "," + $('#registerpic_country').val();
	if(address == ",")
		return false;
	geocoder.geocode({ 'address': address }, function (results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			lat = results[0].geometry.location.nb;
			$("#latitude").val(lat.toString());
			long = results[0].geometry.location.ob;
			$("#longitude").val(long.toString());
			initialize_postpic(results[0].geometry.location, lat, long);
			return;
		} else if(status == google.maps.GeocoderStatus.ZERO_RESULTS) {
			alert(lang['ort_nichtgefunden'][lng]);
			return false;
		} else {
			alert('Geocode was not successful for the following reason: ' + status);
			return false;
		}
	});
}
);


//Uploadvorschau
$('input[name=registerpic_file]').change(function() {
var active = false;
var i_height = 1000; 
window.clearInterval(active);
$('#image_preview').css("max-height", "0%");
var oFReader = new FileReader();
//oFReader.readAsDataURL(document.getElementById("registerpic_file").files[0]);
oFReader.readAsDataURL($('input[name=registerpic_file]').prop("files")[0]);

oFReader.onload = function (oFREvent) {
	//$('#image_preview').css("background-image", "url(" + oFREvent.target.result + ")");  

	i_height = 0; 
	active = window.setInterval( function() {
		if(i_height > 50){
			window.clearInterval(active);
		}
		//$("#image_preview").height( i_height  + "%");
		$('#image_preview').css("max-height", i_height  + "%");  
		i_height += 1.25;
	}, 40);
	
	$("#image_preview").attr("src",oFREvent.target.result);
	};
});

//Datumsabfrage
var someCallback = function(exifObject) {
	var now = Math.round(+new Date() / 1000);
	//Format: "yy:MM:dd hh:mm:ss";
	var date = exifObject.DateTimeOriginal;
	myDate = date.split(" ");
	dayDate = myDate[0].split(":");
	hourDate = myDate[1].split(":");
	timestamp = new Date(dayDate[0], dayDate[1] - 1, dayDate[2], hourDate[0], hourDate[1], hourDate[2], 0).getTime() / 1000;
	check = confirm(lang['confirm_date1'][lng] + dayDate[2] + "." + dayDate[1] + "." + dayDate[0] + lang['confirm_date2'][lng]);
	if(check == true) {
		$("#registerpic_date").val(timestamp);
	}else {
		$("#registerpic_date").val(now);
	}
	//console.log(exifObject);
}
try {
	$('#registerpic_file').change(function() {
		$(this).fileExif(someCallback);
	});
}catch (e) {
	alert(e);
}


/*Drop-Down Text mit Hover-Effekt
function dropdown(button, content) {
	var tmp;
	$('.' + button + "s").click(function (){
		number = $('.' + button + "s").attr('id').replace(button + '_', '');
		//Pfeile austauschen
		$("." + button + "_arrow" + number).toggleClass("arrow_right");
		$("." + button + "_arrow" + number).toggleClass("arrow_down");
		//Inhalt sichtbar/unsichtbar
		//$("#" + content + "_" + number).toggle();
		tmp = $("#" + content + "_" + number);
		tmp.stop(true, true);
		if (tmp.height() <= '1') {
			tmp.height('100%');
			var height = tmp.height();
			tmp.height('1px');
			tmp.animate({
				'height': height
			}, 500);
			state = 'open';
		}
		else if (tmp.height() > '2') {
			tmp.slideUp(500);
		}
	});
	
	$('.' + button + "s").hover(function() {
		var number = $('.' + button + "s").attr('id').replace(button + '_', '');
		tmp = $("#" + content + "_" + number);
		if (tmp.is(':hidden')) {
			tmp.height("1px");
			tmp.stop(true, true);
			tmp.slideDown('3s');
		}
	},
	function() {
		var number = $('.' + button + "s").attr('id').replace(button + '_', '');
		tmp = $("#" + content + "_" + number);
		if (tmp.height() <= '1') {
			tmp.slideUp('3s');
	
		}
	});
}*/
//Drop-Down Text
function dropdown(button, content) {
	$('.' + button + "s").click(function (){
		number = $(this).attr('id').replace(button + '_', '');
		//Pfeile austauschen
		$("." + button + "_arrow" + number).toggleClass("arrow_right");
		$("." + button + "_arrow" + number).toggleClass("arrow_down");
		//Inhalt sichtbar/unsichtbar
		$("#" + content + "_" + number).toggle(400);
	});
}
//Profil Showcases Ein-/Ausblenden
dropdown("tab", "showcase");
//FAQ Fragen Ein-/Ausblenden
dropdown("question", "answer");
//Home Boxen Ein-/Ausblenden
dropdown("header", "connectbox")

function check_width(){   
//Logo Positionierung 
    if(window.innerWidth < 1240) {
        $("#round_logo").css({ 'display' : 'none' });    
    }   
    if(window.innerWidth > 1500) {
		$("#section").css({ 'width' : 'calc(100% - 500px)' });
		$("#mainnavlist").css({ 'width' : 'calc(100% - 500px)' });
	}else {
		$("#section").css({ 'width' : '70%' }); 
		$("#mainnavlist").css({ 'width' : '70%' });
	}

//FB-Plugin-Höhe
    if(window.innerWidth < 1587) {
        $("#fb_plugin").attr("data-height", '200');
    }
    else {
        $("#fb_plugin").attr("data-height", '190');
    }
	
}

window.onresize = check_width;
check_width();

});

//---------------------Bildhochladeseite--------------------\\
//Funktion zum Marker setzen auf der Bildhochladenseite
var mapset = false;
var map;
var marker;
function initialize_postpic(coords, this_lat, this_lng) {
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
			title: lang['bist_da'][lng],
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

$('#registerpic_city').on({
	blur:function(){
		geocoder = new google.maps.Geocoder();
		var address = $('#registerpic_city').val() + "," + $('#registerpic_country').val();
		geocoder.geocode({ 'address': address }, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				myString = results[0].geometry.location;
				myString = myString.toString();
				myString = myString.replace('(', '');
				myString = myString.replace(')', '');
				geoData = myString.split(', ');
				//console.log(geoData[0] + "," + geoData[1]);
				lat = geoData[0];
				$("#latitude").val(lat);
				long = geoData[1];
				$("#longitude").val(long);
			  initialize_postpic(results[0].geometry.location, lat, long);
			} else if(status == google.maps.GeocoderStatus.ZERO_RESULTS) {
				alert(lang['ort_nichtgefunden'][lng]);
			} else {
				alert('Geocode was not successful for the following reason: ' + status);
			}
		});
	}
});
function success_postpic(position) {
	lat = position.coords.latitude;
	$("#latitude").val(lat.toString());
	long = position.coords.longitude;
	$("#longitude").val(long.toString());
	initialize_postpic(position.coords, false, false);
	//console.log(position.coords);
}
function error_postpic(msg) {
	console.log(typeof msg == 'string' ? msg : "error123");
}
//--------------------^^Bildhochladeseite^^-------------------\\

//Kommentare Ein-/Ausblenden
function show_comments(obj){
        number = $(obj).attr('id').replace('toggle_comment','');
        $("#comment" + number).toggle();
        if($("#toggle_comment" + number).text() == lang['hidecomment'][lng]){
                $("#toggle_comment" + number).text(lang['showcomment'][lng] + ' (' + $("#toggle_comment" + number).data("counts") + ')');
        }else {
                $("#toggle_comment" + number).text(lang['hidecomment'][lng]);
        }
}
$('.toggle_comments').click(function (){
        show_comments(this);
});


//Neuste Bilder Nachladen -start.php
var reload_q = 3;

$(window).scroll(function () {
	if($(window).scrollTop() + $(window).height() == $(document).height()) {
		var braceNameReload = $("#bracelet_name").val();
		console.log(currentPath);
		if(currentPath == "/community" || currentPath == "/community.php") reload_start(3);
		if(braceNameReload != undefined) reload_armband(braceNameReload, 3);
	}
});

function reload_start(plus) {
	console.log('reload: ' + $('#pic_br_switch').data('recent_brid_pics'));
	reload_q += plus;
	if(reload_q < 3)
		reload_q = 3;
	var nachlad = $.ajax( "./scripts/ajax/ajax_start.php?q=" + reload_q + "&recent_brid_pics=" + $('#pic_br_switch').data('recent_brid_pics'))
		.done(function(data) {
			if(data != ""){
					htmlcode = $("#recent_pics").html();
					if(plus == 0) {
						$("#recent_pics").html(data);
					}else {
						$("#recent_pics").append(data);
					}
			} else {
				reload_q -= 3;
			}
		})
		.fail(function() {
		alert( "error" );
	});
}
//Neuste Bilder Nachladen -armband.php
var reload_q2 = 6;

function reload_armband(braceName, plus) {
	var nachlad = $.ajax( "./scripts/ajax/ajax_armband.php?q=" + reload_q2 + "&braceName=" + braceName)
		.done(function( data ) {
		//$("#armband_reload").remove();
		htmlcode = $("#armband").html();
		$("#armband").append(data);
		reload_q2 += plus;
		})
		.fail(function() {
			alert( "error" );
	}); 
}
//Nächstes/Vorheriges Bild
function change_pic(cv, sv) {
	$("#loading").toggle();
	$.post("./scripts/ajax/ajax_home.php", {contentVar: cv, startVal: sv}, function(data) {
		$("#connectbox_1").html(data);
		});
	$.fail(function() {
		$("#loading").toggle();
		alert( "error" );
	}); 
}
//Aboformular anzeigen
$(document).ready(function(){
	$('#show_sub').click(function(){
		$.ajax({
			type: "POST",
			url: "../scripts/ajax/ajax_statistics.php",
			data: "login=" + 'true',
			success: function(data){
				var json = JSON.parse(data);		
				if(json.checklogin == false) {
					$('.sub_inputs').toggle();
				}else {
					bracelet_name = $('#bracelet_name').val();
					window.location.replace("armband?sub=username&sub_user=" + username + "&name=" + bracelet_name);
				}
			}
		});
	});
});
//Armband-Name Formular anzeigen
$(document).ready(function(){
	$('#edit_name').click(function(){
		$('.name_inputs').toggle();
	});
});

//Löschen von Kommentaren und Bildern bestätigen
function confirmDelete(type, object) {
	var braceName = $(object).attr('data-bracelet');
	var href = $('<a>', { href:$(object).attr("href") } )[0];
	var getVariables = href.search.replace('?', '');
	$.ajax({
		type: "POST",
		url: "../scripts/ajax/ajax_statistics.php",
		data: "braceName=" + encodeURIComponent(braceName) + "&deleterequest=true",
		success: function(data){
			var json = JSON.parse(data);
			if(json.flag) {
				var deleteORflag = 'melden';
			}else {
				var deleteORflag = 'loeschen';
			}
			if(lng == 'en') var agree = confirm("Do you really want to " + lang[deleteORflag][lng] + " that " + lang[type][lng] + "?");
			if(lng == 'de') var agree = confirm("Willst du " + lang[type][lng] + " wirklich " + lang[deleteORflag][lng] + "?");
			if(agree) {
				$.ajax({
					type: "POST",
					url: "../scripts/ajax/ajax_statistics.php",
					data: getVariables + "&name=" + braceName,
					success: function(data){
						var json = JSON.parse(data);
						if(json.gemeldet != undefined) {
							if(json.gemeldet == 'Bild') alert(lang['bild_gemeldet'][lng]);
							if(json.gemeldet == 'Kommentar') alert(lang['kommentar_gemeldet'][lng]);
						}else if(json.location != undefined) {
							window.location.replace("http://placelet.de/" + json.location);
						}else {
							alert("Error: " + json.error);
							console.log(json);
						}
					}
				});
				return true;
			}else {
				console.log("Nope, Chuck Testa!");
				return false; 
			}
		}
	});
}

//Den Rest vom Bild-Hochladformular anzeigen, wenn man nicht eingeloggt ist.
$(document).ready(function(){
	$('#picupload_nologin').click(function() {
		$('#registerpic_upload_inputs').toggle();
		$('.picupload_nologin_text').remove();
	});
});

//Ajax-Login
$(document).ready(function(){
	$("#picupload_login_submit").click(function(){
		username = $("#picupload_login_username").val();
		password = $("#picupload_login_password").val();
		$.ajax({
			type: "POST",
			url: "../scripts/ajax/ajax_login.php",
			data: "login=" + username + "&password=" + password,
			success: function(html){
				if(html == 'true') {
					$('#registerpic_upload_inputs').toggle();
					$('#picupload_login_loading').toggle();
					$('.picupload_nologin_text').remove();
					$('#login-box').remove();
					$('#headerlogin').html('Logout');
					$('#headerlogin').attr('href', 'login?logout');
					$('#registerprofile').html(lang['meinprofil'][lng]);
				}else {
					$('#picupload_login_submit').toggle();
					$('#picupload_login_loading').toggle();
					if(html == 'notsent'){
						$('#picupload_login_errormsg').html(lang['ajax_login']['f0'][lng] + '<br>');
					}else if(html == 'false') {
						$('#picupload_login_errormsg').html(lang['ajax_login']['f1'][lng] + '<br>');
					}else if(html == 'notexisting') {
						$('#picupload_login_errormsg').html(lang['ajax_login']['f2'][lng] + '<br>');
					}else if(html == 'unvalidated'){
						$('#picupload_login_errormsg').html(lang['ajax_login']['f30'][lng] + encodeURIComponent(username)+ lang['ajax_login']['f31'][lng] + '<br>');
					}else $('#picupload_login_errormsg').html(html);
				}
					
				
			},
			beforeSend:function()
			{
				$('#picupload_login_submit').toggle();
				$('#picupload_login_loading').toggle();
				$('#picupload_login_errormsg').html('');
			}
		});
		return false;
	});
});

//Benachrichtigungen gelesen
$(document).ready(function(){
	$("#notific_read").click(function(){
		$.ajax({
			type: "POST",
			url: "../scripts/ajax/ajax_login.php",
			data: "notific_read=true",
			success: function(){
				$("#notific_read").remove();
			}
		});
	});
});

//Zwischen neuesten Bildern und zuletzt geposteten Armbändern wechseln.
//$(document).ready(function() {
	$(document).on('click', '#pic_br_switch', function() {
		console.log("WHASSUP, BRO?");
		if($('#pic_br_switch').data('recent_brid_pics') == 'true') $('#pic_br_switch').data('recent_brid_pics', 'false');
			else $('#pic_br_switch').data('recent_brid_pics', 'true');
			reload_q = 3;
		reload_start(0);
	});
//});