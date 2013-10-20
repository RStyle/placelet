$(document).ready(function() {
	// starte wenn DOM geladen ist
	
var Input_password = $('input[name=password]');
var default_reg_password_value = $('input[name=reg_password]').val();
var default_password_value = Input_password.val();
if (default_password_value == '')
	default_password_value = default_reg_password_value;
var show_login = false;
var login_return = true;

$("#headerlogin").click(function(){
	show_login = !show_login;
	if(show_login == true)
		$("#login-box").show();
	else
		$("#login-box").hide();
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
			this.setCustomValidity("Die Passwörter passen nicht zueinander.") //Errormeldung bei beiden Inputelementen - browserspezifisch, Chrome erkennt nur das erste, Firefox & IE10 beide
			
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



//Neuste Bilder Nachladen
$( "#start_reload" ).click(function() {
var nachlad = $.ajax( "./scripts/ajax/ajax_start.php" )
  .done(function( data ) {
  $("#start_reload").remove();
  htmlcode = $("#recent_pics").html();
  $("#recent_pics").html( htmlcode + data + '<div class="pseudo_link" id="start_reload" style="clear: both;" >Mehr anzeigen</div>');
  })
  .fail(function() {
    alert( "error" );
  });
  
});
//---

//Kommentare Ein/Ausblenden
$('body').on('click', '.toggle_comments', function (){
	number = $(this).attr('id');
	number = number.replace('toggle_comment','');
	$("#comment" + number).toggle(); 
});
//---


function check_width(){   
    
    if(window.innerWidth < 1480 && window.innerWidth > 1230){
		//$("#logo"). =
		$("#logo").attr("src", 'img/logo.svg');
		$("#logo").attr("width", '93');
	} else {
		$("#logo").attr("src", 'img/logo_extended.svg');
		$("#logo").attr("width", '206');
	}
	
	if(window.innerWidth > 1230){
		$("#logo").attr("style", 'top: 3em;');
	}   
	else {
        $("#logo").attr("style", 'top: 11px;'); 
    }
	
	if(window.innerWidth < 1038){
		$("#login-box").css({ 'left' : 540 });
	}   
	else {
		if(window.innerWidth > 1530) {
			$("#login-box").css({ 'right' : '30%' });
		}
		else {
			
			$("#login-box").css({ 'left' : '53%' });
		}
	}
	
}

window.onresize = check_width;
check_width();


});