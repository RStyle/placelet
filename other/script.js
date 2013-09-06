$(document).ready(function() {
	// starte wenn DOM geladen ist
var Input_password = $('input[name=password]');
var default_password_value = Input_password.val();

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
});

$(".input_text").onblur(function(){
		if(this.value != $.trim(this.value)) this.value = $.trim(this.value);
    }
);


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

});