var currentPath = $(location).attr('pathname');
$(document).ready(function() {
	
var Input_password = $('input[name=password]');
var default_reg_password_value = '!§%$$%\/%§$';
var default_password_value = '!§%$$%\/%§$';
var show_login = false;
var login_return = true;

 pic_br_switch_data = true;

/*Login-Box anzeigen*/
$("#headerlogin").click(function(){
	show_login = !show_login;
	if(show_login == true) {
		$("#login-box").show();
		$("#login").focus();
	}else {
		$("#login-box").hide();
	}
});

$(document).click(function(e) {
    if (e.target.id != 'login-box' && e.target.id != 'login' && e.target.id != 'password' && e.target.id != 'form_login' && e.target.id != 'headerlogin' && e.target.id != 'login_icon'
		&& e.target.id != 'label_login' && e.target.id != 'label_password' && e.target.id != 'submit_login') {
        if(show_login == true){
			show_login = false;
			$("#login-box").hide();
		}
    }
});


/*Login*/
$('.password').on({
    focus:function(){                   
		if(this.value == default_password_value || this.value == default_reg_password_value) this.value = '';
    },
    blur:function(){
		if(this.value == '') this.value = default_password_value;
    }
});

$("#form_login").submit(function() {
	if ($("#password").val() == default_password_value) {
	$("#password").select(); 
		console.log("defaultpassword");
		return false;
	}
	
	if($("#login").val().length < 4 || $("#login").val().length > 15){
		$("#login").css( "background-color", "rgb(255, 66, 66)" );
		setTimeout(function() {
			$("#login").css( "background-color", "#FFF" );
		}, 800);
		console.log("login");
		return false;
	}
	
	if($("#password").val().length < 6 || $("#login").val().length > 30){
		$("#password").css( "background-color", "rgb(255, 66, 66)" );
		setTimeout(function() {
			$("#password").css( "background-color", "#FFF" );
		}, 800);
		console.log("password");
		return false;
	}
});

$(".input_text").blur(function(){
		if(this.value != $.trim(this.value)) this.value = $.trim(this.value);  /*trimt Formualer - außer Passwörter - direkt per JS*/
});


/*Registration*/
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
			this.setCustomValidity(lang['passwoerter_unpassend']); /*Errormeldung bei beiden Inputelementen - browserspezifisch, Chrome erkennt nur das erste, Firefox & IE10 beide*/
			
		});
		return false;
	}
});





$('#holder').on(
    'dragover',
    function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
$('#holder').on(
    'dragenter',
    function(e) {
        e.preventDefault();
        e.stopPropagation();
    });

/*Überprüfung ob Stadt bei Bildupload exestiert bzw. Korrektur*/
$("form[name=registerpic]").submit(function() {
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
			alert(lang['ort_nichtgefunden']);
			return false;
		} else {
			/*alert('Geocode was not successful for the following reason: ' + status);*/
			return false;
		}
	});
});


/*Uploadvorschau*/
$('input[id=upload_pic]').change(function preview() {
		var active = false;
		var i_height = 1000; 
		window.clearInterval(active);
		$('#image_preview').css("max-height", "0%");
		var oFReader = new FileReader();
		oFReader.readAsDataURL($('input[id=upload_pic]').prop("files")[0]);
	
		oFReader.onload = function (oFREvent) {
		
			i_height = 0; 
			active = window.setInterval( function() {
				if(i_height > 50){
					window.clearInterval(active);
				}
				$('#image_preview').css("max-height", i_height  + "%");  
				i_height += 1.25;
			}, 40);
			
			$("#image_preview").attr("src",oFREvent.target.result);
		};
	});

/*Datumsabfrage*/
var exifDateCallback = function(exifObject) {
	var now = Math.round(+new Date() / 1000);
	/*Format: "yy:MM:dd hh:mm:ss";*/
	var date = exifObject.DateTimeOriginal;
	myDate = date.split(" ");
	dayDate = myDate[0].split(":");
	hourDate = myDate[1].split(":");
	timestamp = new Date(dayDate[0], dayDate[1] - 1, dayDate[2], hourDate[0], hourDate[1], hourDate[2], 0).getTime() / 1000;
	check = confirm(lang['confirm_date1'] + dayDate[2] + "." + dayDate[1] + "." + dayDate[0] + lang['confirm_date2']);
	if(check == true) {
		$("#registerpic_date").val(timestamp);
	}else {
		$("#registerpic_date").val(now);
	}
}; /*1*/ /*<--wichtig!*/
$('#upload_pic').change(function() {
	$(this).fileExif(exifDateCallback);
});


/*Drop-Down Text*/
function dropdown(button, content) {
	$('.' + button + "s").click(function (){
		number = $(this).attr('id').replace(button + '_', '');
		/*Pfeile austauschen*/
		$("." + button + "_arrow" + number).toggleClass("arrow_right");
		$("." + button + "_arrow" + number).toggleClass("arrow_down");
		/*Inhalt sichtbar/unsichtbar*/
		$("#" + content + "_" + number).toggle(400);
	});
}
/*Profil Showcases Ein-/Ausblenden*/
dropdown("tab", "showcase");
/*FAQ Fragen Ein-/Ausblenden*/
dropdown("question", "answer");
/*Home Boxen Ein-/Ausblenden*/
dropdown("header", "connectbox");

function check_width(){   
/*Logo Positionierung */
	if(window.innerWidth < 1480 && window.innerWidth > 1230){
		$("#logo").attr("src", '/cache.php?f=/img/logo.svg');
		$("#logo").attr("width", '93');
	}/*1*/else if(window.innerWidth > 1500) {
		$("#section").css({ 'width' : 'calc(100% - 500px)' });
		$("#mainnavlist").css({ 'width' : 'calc(100% - 500px)' });
	}else {
		$("#logo").attr("src", '/cache.php?f=/img/logo_extended.svg');
		$("#logo").attr("width", '206');
		$("#section").css({ 'width' : '70%' });
		$("#mainnavlist").css({ 'width' : '70%' });
	}
	if(window.innerWidth > 1230){
		$("#logo").attr("style", 'top: 7em;');
	}   
	else {
		$("#logo").attr("style", 'display: none;');
	}

/*FB-Plugin-Höhe*/
    if(window.innerWidth < 1587) {
        $("#fb_plugin").attr("data-height", '200');
    }
    else {
        $("#fb_plugin").attr("data-height", '190');
    }
/*Login-Box-Positionierung*/
	if(window.innerWidth < 1017){
		$("#login-box").css({ 'left' : 460 });
		$("#login-box").css({ 'right' : 'initial' });
	}   
	else {
		$("#login-box").css({ 'right' : '350px' });
		$("#login-box").css({ 'left' : 'initial' });
	}
	
}

window.onresize = check_width;
check_width();

});

/*---------------------Bildhochladeseite--------------------\\*/
/*Funktion zum Marker setzen auf der Bildhochladenseite*/
var mapset = false;
var map;
var marker;
function initialize_postpic(coords, this_lat, this_lng) {/*1*/
	var latlng;
	if(this_lat != false && this_lng != false){
		latlng = new google.maps.LatLng(this_lat, this_lng);}
	else{
		latlng = new google.maps.LatLng(coords.latitude, coords.longitude);}
	/*1*/
	$.ajax({
		type: 'GET',
		dataType: "json",
		url: "http://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+long+"&sensor=false&eng=" + lng,
		data: {},
		success: function(data) {
			city = "";
			bundesland = "";
			country = "";
			$('#registerpic_city').val("");
			$('#registerpic_country').val("");
			$.each( data['results'],function(i, val) {
				$.each( val['address_components'],function(i, val) {
					if (val['types'] == "locality, political") {
						if (val['long_name']!="") {
							city = val['long_name'];
						}
					}
					if (val['types'] == "country, political") {
						if (val['long_name']!="") {
							country = val['long_name'];
						}
					}
					if (val['types'] == "administrative_area_level_1, political") {
						if (val['long_name']!="") {
							bundesland = val['long_name'];
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
			title: lang['bist_da'],
			draggable: true
		}); 
		
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
				lat = geoData[0];
				$("#latitude").val(lat);
				long = geoData[1];
				$("#longitude").val(long);
			  initialize_postpic(results[0].geometry.location, lat, long);
			} else if(status == google.maps.GeocoderStatus.ZERO_RESULTS) {
				alert(lang['ort_nichtgefunden']);
			} else {
				/*alert('Geocode was not successful for the following reason: ' + status);*/
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
}
function error_postpic(msg) {
	console.log(typeof msg == 'string' ? msg : "error123");
}
/*--------------------^^Bildhochladeseite^^-------------------\\*/

/*Kommentare Ein-/Ausblenden*/
function show_comments(obj){
        number = $(obj).attr('id').replace('toggle_comment','');
        $("#comment" + number).toggle();
        if($("#toggle_comment" + number).text() == lang['hidecomment']){
                $("#toggle_comment" + number).text(lang['showcomment'] + ' (' + $("#toggle_comment" + number).data("counts") + ')');
        }else {
                $("#toggle_comment" + number).text(lang['hidecomment']);
        }
}
$('.toggle_comments').click(function (){
        show_comments(this);
});


/*Neuste Bilder Nachladen -start.php*/
var reload_q = 3;
console.log($("#overview").data("reload") == true);
if($("#overview").data("reload") != true) {
	$(window).scroll(function () {
		if($(window).scrollTop() + $(window).height() == $(document).height()) {
			var braceNameReload = $("#disp_bracelet_name").data("url_bracename");
			if(pic_br_switch_data == true) pic_br_switch_data = false; else pic_br_switch_data = true;
			if(currentPath == "/community" || currentPath == "/community.php") reload_start(3);
			if(pic_br_switch_data == true) pic_br_switch_data = false; else pic_br_switch_data = true;
			if(braceNameReload != undefined) reload_armband(braceNameReload);
		}
	});
}

function reload_start(plus) {
	var displayed_picnr = $('#comment' + reload_q).data('picnr');
	reload_q += plus;
	if(reload_q < 3)
		reload_q = 3;
	var nachlad = $.ajax( "/scripts/ajax/ajax_start.php?q=" + reload_q + "&recent_brid_pics=" + pic_br_switch_data + "&eng=" + lng + "&displayed_picnr=" + displayed_picnr)
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
		/*alert("Reload Error");*/
	});
}
/*Neuste Bilder Nachladen -armband.php*/
var reload_q2 = $('#bracelet').data('pics');

function reload_armband(braceName) {
	console.log(reload_q2);
	reload_q2 += 3;
	var nachlad = $.ajax( "/scripts/ajax/ajax_armband.php?q=" + reload_q2 + "&braceName=" + braceName + "&eng=" + lng)
		.done(function(data) {
			$("#armband").append(data);
		})
		.fail(function() {
			console.log("error");
	});
}
/*Nächstes/Vorheriges Bild*/
function change_pic(cv, sv) {
	$("#loading").toggle();
	$.post("/scripts/ajax/ajax_home.php", {contentVar: cv, startVal: sv, eng: lng}, function(data) {
		$("#connectbox_1").html(data);
		});
	$.fail(function() {
		$("#loading").toggle();
		/*alert("Reload Error");*/
	}); 
}
/*Aboformular anzeigen*/
$(document).ready(function(){
	$('#show_sub').click(function(){
		bracelet_name = $('#bracelet_name').val();
		$.ajax({
			type: "POST",
			url: "/scripts/ajax/ajax_statistics.php",
			data: "subscribe=" + bracelet_name + "&eng=" + lng,
			success: function(data){
				var json = JSON.parse(data);		
				if(json.subscribe == false) {
					$('.sub_inputs').toggle();
				}else {
					if(json.subscribe == 'wahr') $('#show_sub').remove();
					alert(lang['manage_subscription' + json.subscribe]);
				}
			}
		});
	});
});
/*Armband-Name Formular anzeigen*/
$(document).ready(function(){
	$('#edit_name').click(function(){
		$('.name_inputs').toggle();
	});
});

/*Löschen von Kommentaren und Bildern bestätigen*/
function confirmDelete(type, object) {
	var braceName = $(object).attr('data-bracelet');
	var href = $('<a>', { href:$(object).attr("href") } )[0];
	var getVariables = href.search.replace('?', '');
	$.ajax({
		type: "POST",
		url: "/scripts/ajax/ajax_statistics.php",
		data: "braceName=" + encodeURIComponent(braceName) + "&deleterequest=true&eng=" + lng,
		success: function(data){
			var json = JSON.parse(data);
			if(json.flag) {
				var deleteORflag = 'melden';
			}else {
				var deleteORflag = 'loeschen';
			}
			if(lng == 'en') var agree = confirm("Do you really want to " + lang[deleteORflag] + " that " + lang[type] + "?");
			if(lng == 'de') var agree = confirm("Willst du " + lang[type] + " wirklich " + lang[deleteORflag] + "?");
			if(agree) {
				$.ajax({
					type: "POST",
					url: "/scripts/ajax/ajax_statistics.php",
					data: getVariables + "&name=" + braceName + "&eng=" + lng,
					success: function(data){
						console.log("hallo-" + data);
						var json = JSON.parse(data);
						if(json.gemeldet != undefined) {
							if(json.gemeldet == 'Bild') alert(lang['bild_gemeldet']);
							if(json.gemeldet == 'Kommentar') alert(lang['kommentar_gemeldet']);
						}else if(json.location != undefined) {
							window.location.replace("/" + json.location);
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

/*Den Rest vom Bild-Hochladformular anzeigen, wenn man nicht eingeloggt ist.*/
$(document).ready(function(){
	$('#picupload_nologin').click(function() {
		$('#registerpic_upload_inputs').toggle();
		$('.picupload_nologin_text').remove();
	});
});

/*Ajax-Login*/
$(document).ready(function(){
	$("#picupload_login_submit").click(function(){
		username = $("#picupload_login_username").val();
		password = $("#picupload_login_password").val();
		$.ajax({
			type: "POST",
			url: "/scripts/ajax/ajax_login.php",
			data: "login=" + username + "&password=" + password + "&eng=" + lng,
			success: function(html){
				if(html == 'true') {
					$('#registerpic_upload_inputs').toggle();
					$('#picupload_login_loading').toggle();
					$('.picupload_nologin_text').remove();
					$('#login-box').remove();
					$('#headerlogin').html('Logout');
					$('#headerlogin').attr('href', 'login?logout');
					$('#registerprofile').html(lang['meinprofil']);
				}else {
					$('#picupload_login_submit').toggle();
					$('#picupload_login_loading').toggle();
					if(html == 'notsent'){
						$('#picupload_login_errormsg').html(lang['ajax_login']['f0'] + '<br>');
					}else if(html == 'false') {
						$('#picupload_login_errormsg').html(lang['ajax_login']['f1'] + '<br>');
					}else if(html == 'notexisting') {
						$('#picupload_login_errormsg').html(lang['ajax_login']['f2'] + '<br>');
					}else if(html == 'unvalidated'){
						$('#picupload_login_errormsg').html(lang['ajax_login']['f30'] + encodeURIComponent(username)+ lang['ajax_login']['f31'] + '<br>');
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

/*Benachrichtigungen gelesen*/
$(document).ready(function(){
	$("#notific_read").click(function(){
		$.ajax({
			type: "POST",
			url: "/scripts/ajax/ajax_login.php",
			data: "notific_read=true&eng=" + lng,
			success: function(){
				$("#notific_read").remove();
			}
		});
	});
});

/*Zwischen neuesten Bildern und zuletzt geposteten Armbändern wechseln.*/
$(document).on('click', '#pic_br_switch', function() {
	if(pic_br_switch_data == true){ $('#pic_br_switch').data('recent_brid_pics', 'false'); pic_br_switch_data = false;}
		else {$('#pic_br_switch').data('recent_brid_pics', 'true'); pic_br_switch_data = true;}
	reload_q = 3;
	reload_start(0);
});
var receiving = false;
/*Nachricht senden*/
var enter_down = false;
var shift_down = false;
$(document).ready(function() {
	$('#chat_text').keydown(function(e) {
		if(e.which == 13) {
			enter_down = true;
		}else if(e.which == 16) {
			shift_down = true;
		}
	});
	$('#chat_text').keyup(function(e) {
		if(e.which == 13) {
			enter_down = false;
		}else if(e.which == 16) {
			shift_down = false;
		}
	});
	$('#chat_text').keypress(function(e) {
		if(enter_down && shift_down) return true;
		if(e.which == 13) {
			message = $("#chat_text").val().trim();
			$("#chat_text").val('');
			recipient = $("#chat_room").data("recipient");
			console.log(recipient);
			if(message != '') {
				receiving = true;
				$.ajax({
					type: "POST",
					url: "scripts/ajax/ajax_nachrichten.php",
					data: "recipient=" + recipient + "&message=" + message + "&send_msg=true&eng=" + lng + "&senderid=" + $('#chat_room').data('recipient'),
					success: function(html){
						receiving = false;
						$("#chat_text").val("");
						receive_messages($('#chat_room').data('recipient'));
					}
				});
			}
			return false;
		}
	});
});
/*Nach unten scrollen in der Nachrichtenbox*/
var intervalID;
var freqSecs = 5;
var idle = false;
var msg_box = $('#message_box');
function RepeatCall() {
	var inout = (freqSecs * 1000) / 2;
	if(!idle && !receiving) {
		receive_messages();
		messages_read($('#chat_room').data('recipient'));
	}
}
if($('#title').data('title') == 'msgs' && $('#chat_room').data('recipient') != 0 && $("#seen_marker").data("msg_id") != 0 && msg_box[0] != undefined) {
	intervalID = setInterval(RepeatCall, freqSecs * 1000);
	msg_box.scrollTop(msg_box[0].scrollHeight);
}
/*Nachrichten empfangen*/
function receive_messages() {
	receiving = true;
	idle = false;
	recipient = $("#chat_room").data("recipient");
	msg_id = $("#seen_marker").data("msg_id");
	seen_time = $("#seen_marker").html();
	$.ajax({
		type: "POST",
		url: "scripts/ajax/ajax_nachrichten.php",
		data: "receive_msgs=true&msg_id=" + msg_id + "&recipient=" + recipient + "&eng=" + lng + "&senderid=" + $('#chat_room').data('recipient'),
		success: function(html){
			receiving = false;
			$("#seen_marker").remove();
			$("#message_box").append(html);
			if('<p style="color: #999; margin-bottom: 20px;" id="seen_marker" data-msg_id="' + msg_id + '">' + seen_time + '</p>' != html.trim()) msg_box.scrollTop(msg_box[0].scrollHeight);
		}
	});
}
function messages_read(senderid) {
	$.ajax({
		type: "POST",
		url: "scripts/ajax/ajax_nachrichten.php",
		data: "messages_read=true&senderid=" + senderid
	});
}
/*Alle 5 Sekunden auf neue Nachrichten überprüfen*/

/*Nachrichten-Benachrichtigungen entfernen*/
$(document).ready(function() {
	$('.del_msg').click(function() {
		messages_read($('.del_msg').data("del_note"));
		$('#note' + $('.del_msg').data("del_note")).remove();
	});
/*Armbandname ändern*/
	$('#edit_name_submit').click(function(){
		if($('#edit_name_input').val() != '') {
			var new_bracelet_name = $('#edit_name_input').val();
			$.ajax({
				type: "POST",
				url: "/scripts/ajax/ajax_statistics.php",
				data: "change_name=true&eng=" + lng + "&brid=" + $('#edit_name_submit').data('brid') + "&new_name=" + new_bracelet_name,
				success: function(data){
					var json = JSON.parse(data);		
					if(json.change_name == true) {
						$('#disp_bracelet_name').text(json.brace_name);
						$('.name_inputs').toggle();
					}else {
						alert(lang['edit_br_name2']);
					}
					console.log(data);
				},
				beforeSend:function()
				{
						$('#edit_name_input').val('');
				}
			});
		}
	});
});
/*idle Zeug*/
idleTime = 0;
$(document).ready(function () {
    var idleInterval = setInterval(timerIncrement, 1000); /* 1 Sekunde*/
    $(this).mousemove(function (e) {
        idleTime = 0;
		if(idle) {
			receive_messages();
			messages_read($('#chat_room').data('recipient'));
        	idle = false;
		}
    });
    $(this).keypress(function (e) {
        idleTime = 0;
		if(idle) {
			receive_messages();
			messages_read($('#chat_room').data('recipient'));
        	idle = false;
		}
    });
});
function timerIncrement() {
    idleTime = idleTime + 1;
    if(idleTime >= 30) { /* 30 Sekunden*/
        idle = true;
		console.log(idle);
    }
}