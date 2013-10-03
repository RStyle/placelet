$(document).ready(function() {
	// starte wenn DOM geladen ist
	
var Input_password = $('input[name=password]');
var default_password_value = Input_password.val();
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
		if(this.value == default_password_value) this.value = '';
    },
    blur:function(){
		if(this.value == '') this.value = default_password_value;
    }
})

$("#form_login").submit(function() {
	if ($("#password").val() == default_password_value) {
	setTimeout(function(){$("#password").select();},10); 
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

/*$(".input_4_20").blur(function(){
		if(this.value != $.trim(this.value)) this.value = $.trim(this.value);  //trimt Formualer - außer Passwörter - direkt per JS
    }
);*/


//Registration
$("#form_reg").submit(function() {
	if ($("#reg_password").val() == default_password_value) {
	setTimeout(function(){$("#reg_password").select();},10); 
		return false;
	}
	if ($("#reg_password2").val() == default_password_value) {
	setTimeout(function(){$("#reg_password2").select();},10); 
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

//window.innerWidth
function check_width(){   
    
    if(window.innerWidth < 1480){
		//$("#logo"). =
		$("#logo").attr("src", 'pictures/logo.svg');
		$("#logo").attr("width", '93');
	} else {
		$("#logo").attr("src", 'pictures/logo_extended.svg');
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