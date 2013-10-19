<?php
if (isset($_SESSION['user'])) {
	$userdetails = $user->userdetails();
	//Armband registrieren
	if (isset($_POST['reg_br']) && $_POST['submit'] == "Armband registrieren") {
			$bracelet_registered = $user->registerbr($_POST['reg_br']);
	}
}
if(isset($_GET['registerbr'])) {
	$bracelet_status = User::bracelet_status($_GET['registerbr'], $db);
}
?>
<?php
if(!isset($_GET['loginattempt'])) {
	$captcha = 'captcha';
?>
        <article id="register_pic" class="mainarticles bottom_border_blue">
            <div class="blue_line mainarticleheaders line_header"><h1>Bild posten</h1></div>
            <form id="registerpic" name="registerpic" class="registerpic" action="<?php echo 'armband'; ?>" method="post" enctype="multipart/form-data">
                <span style="font-family: Verdana, Times"><strong style="color: #000;">Bild</strong> posten</span><br><br>
                
                <label for="registerpic_brid" class="label_registerpic_brid">Armband Nr.:</label><br>
                <input type="text" name="registerpic_brid" value="<?php if(isset($_GET['registerbr'])) echo $_GET['registerbr'];?>"><br>
                
                <label for="registerpic_title" class="label_registerpic_title">Titel:</label><br>
                <input type="text" name="registerpic_title" class="registerpic_title" size="20" maxlength="20" placeholder="Titel" required><br>
                
                <label for="registerpic_city" class="label_registerpic_city">Stadt:</label><br>
                <input type="text" name="registerpic_city" class="registerpic_city" size="20" maxlength="20" placeholder="Stadt" required><br>
                
                <label for="registerpic_country" class="label_registerpic_country">Land:</label><br>
                <input type="text" name="registerpic_country" class="registerpic_country" size="20" maxlength="20" placeholder="Land" required><br>
                
                <label for="registerpic_description" class="registerpic_description">Beschreibung:</label><br>
                <textarea name="registerpic_description" class="registerpic_description" rows="8" cols="40" maxlength="1000" required></textarea><br> 
                
                <label for="registerpic_captcha" class="label_registerpic_captcha">Captcha:</label><br>
                <input type="text" name="registerpic_captcha" class="registerpic_captcha" size="20" maxlength="5" placeholder="Captcha" value="<?php echo $captcha;?>" required><br>
                
                <input type="file" name="registerpic_file" id="registerpic_file" maxlength="<?php echo $max_file_size; ?>" required><br>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>">
                <input type="submit" name="registerpic_submit" value="Bild posten"><br>
                <img id="image_preview" src="./img/placeholder.png" style="background-repeat: no-repeat;background-position: center;max-height:0px">
            </form>
        </article>
        <article id="register_br" class="mainarticles bottom_border_green">
            <div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page];?></h1></div>
<?php
	if($checklogin == false){
		if(isset($_GET['registerbr'])) {//Wenn man eine Armband ID eingegeben hat, kann man sich einloggen
?>
			Bitte Logge dich ein oder erstelle dir einen neuen Account,<br>
            um dein Armband Nr. <span style="color: #000; font-style: italic;"><?php echo $_GET['registerbr']; ?></span> registrieren:<br>
			<form name="login" id="form_login" action="<?php echo $friendly_self.'?registerbr='.$_GET['registerbr']; ?>" method="post">
			  <table style="border: 1px solid black">
				<tr>
				  <td><label for="login">Benutzername&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
				  <td><input type="text" name="login" id="login" size="20" maxlength="30" placeholder="Benutzername" pattern=".{6,30}" title="Min.4 - Max.15" required></td>
				</tr>
				<tr>
				  <td><label for="password">Passwort</label></td>
				  <td><input type="password" name="password" id="password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%&$%&/%§$" required></td>
				</tr>
				<tr>
				  <td><input type="submit" value="Login"></td>
				  <td>&nbsp;</td>
				</tr>
			  </table>
			</form><br>
<?php
		}
?>
			<form name="reg" id="form_reg" action="<?php echo $friendly_self; ?>" method="post">
			  <table style="border: 1px solid black">
				<tr>
				  <td><label for="reg_login">Benutzername</label></td>
				  <td><input type="text" name="reg_login" id="reg_login" class="input_text" size="20" maxlength="30" placeholder="Benutzername" pattern=".{4,15}" title="Min.4 - Max.15" required></td>
				</tr>
				<tr>
				  <td><label for="reg_first_name">Vorname</label></td>
				  <td><input type="text" name="reg_first_name" id="reg_first_name" class="input_text" size="20" maxlength="30" placeholder="Vorname" required></td>
				</tr>
				<tr>
				  <td><label for="reg_name">Nachname</label></td>
				  <td><input type="text" name="reg_name" id="reg_name" class="input_text" size="20" maxlength="30" placeholder="Nachname" required></td>
				</tr>
				<tr>
				  <td><label for="reg_email">Email-Adresse</label></td>
				  <td><input type="email" name="reg_email" id="reg_email" class="input_text" size="20" maxlength="30" placeholder="Email-Adresse" required></td>
				</tr>
				<tr>
				  <td><label for="reg_password">Passwort</label></td>
				  <td><input type="password" name="reg_password" id="reg_password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%&$%&/%§$" required></td>
				</tr>
				<tr>
				  <td><label for="reg_password2">Passwort wiederholen</label></td>
				  <td><input type="password" name="reg_password2" id="reg_password2" class="password" size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%&$%&/%§$" required></td>
				</tr>
				<tr>
				  <td><input type="hidden" name="new_register" value="true"><input type="submit" value="Registrieren"></td>
				  <td>&nbsp;</td>
				</tr>
			</form>
<?php
	}else {
		if (isset($_POST['reg_br'])) {
			switch ($bracelet_registered) {
				case '0':
					echo 'Dieses Armband gibt es nicht!';
					break;
				case 1:
					echo 'Armband '.$_POST['reg_br'].' erfolgreich registriert.';
					break;
				case 2:
					echo 'Armband '.$_POST['reg_br'].' wurde schon auf dich registriert.';
					break;
				case 3:
					echo 'Dieses Armband wurde schon auf einen anderen Benutzer registriert.';
					break;
				default:
					echo '$bracelet_registered hat einen unbekannten Wert<br>
					bitte melde diesen Fall dem <a href="mailto:support@placelet.de">Support</a>.';
			}
			echo '<br><br>';
		}
?>
				<form name="registerbr" action="<?php echo $friendly_self; ?>" method="POST">
					<label for="reg_br">Armband registrieren</label>
					<input type="text" name="reg_br" id="reg_br" class="input_text" size="20" maxlength="10" placeholder="Armband ID" value="<?php if(isset($_GET["registerbr"])) {echo $_GET["registerbr"];}// else {echo "Armband ID";}?>">
					<input type="submit" name="submit" value="Armband registrieren">
				</form>
	<?php
	}
} else {
?>
			Der eingegebene Benutzername oder das Passwort waren falsch.<br><br>
			<form name="login" id="form_login" action="<?php echo $friendly_self; ?>" method="post">
			  <table style="border: 1px solid black">
				<tr>
				  <td><label for="login">Benutzername&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
				  <td><input type="text" name="login" id="login" size="20" maxlength="30" pattern=".{4,15}" title="Min.4 - Max.15" placeholder="Benutzername" required></td>
				</tr>
				<tr>
				  <td><label for="password">Passwort</label></td>
				  <td><input type="password" name="password" id="password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%&$%&/%§$" required></td>
				</tr>
				<tr>
				  <td><input type="submit" value="Login"></td>
				  <td>&nbsp;</td>
				</tr>
			  </table>
			</form><br>
<?php
}
?>
      </article>