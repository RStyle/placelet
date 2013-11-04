<?php
foreach($_GET as $key => $val) {
	$_GET[$key] = clean_input($val);
}
if(isset($_POST['registerpic_submit'])) {
	$captcha_valid = captcha_valid($_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
	if($captcha_valid) {
		$pic_registered = $statistics->registerpic($statistics->name2brid($_POST['registerpic_name']),
											 $_POST['registerpic_description'],
											 $_POST['registerpic_city'],
											 $_POST['registerpic_country'],
											 $_POST['registerpic_title'],
											 $_FILES['registerpic_file'],
											 $max_file_size);
	}else {
		header('Location: '.$friendly_self.'?postpic='.$_POST['registerpic_name'].'&captcha=false
				&descr='.urlencode(str_replace("\r\n", "ujhztg", $_POST['registerpic_description'])).'
				&city='.urlencode($_POST['registerpic_city']).'
				&country='.urlencode($_POST['registerpic_country']).'
				&title='.urlencode($_POST['registerpic_title']));
	}
}

if(isset($pic_registered)) {
        if($pic_registered == 5) {
                header('Location: armband?name='.$_POST['registerpic_name'].'&picposted='.$pic_registered);
        }elseif(isset($_GET['captcha'])) {
			if($_GET['captcha'] == 'false') {	
                $js .= 'alert("Das Captcha wurde falsch eingegeben.");';
			}
        }
}
if($user->login) {
	$userdetails = $statistics->userdetails($user->login);
	//Armband registrieren
	if (isset($_POST['reg_br']) && $_POST['submit'] == "Armband registrieren") {
			$bracelet_registered = $user->registerbr($_POST['reg_br']);
	}
}
if(isset($_GET['registerbr'])) {
	$bracelet_status = $statistics->bracelet_status($_GET['registerbr']);
}else {
	$bracelet_status = NULL;
}
?>
			<article class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page];?></h1></div>
<?php
if(isset($_GET['loginattempt'])) {
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
}else {
	if(isset($_GET['postpic'])) {
?>
				<form id="registerpic" name="registerpic" class="registerpic" action="<?php echo $friendly_self; ?>" method="post" enctype="multipart/form-data">
					<span style="font-family: Verdana, Times"><strong style="color: #000;">Bild</strong> posten</span><br><br>
					
					<label for="registerpic_name" class="label_registerpic_name">Armband Nr.:</label><br>
					<input type="text" name="registerpic_name" value="<?php if(isset($_GET['postpic'])) echo urldecode($_GET['postpic']);?>"><br>
					
					<label for="registerpic_title" class="label_registerpic_title">Titel:</label><br>
					<input type="text" name="registerpic_title" class="registerpic_title" size="20" maxlength="20" placeholder="Titel"  value="<?php if(isset($_GET['title'])) echo urldecode($_GET['title']);?>"required><br>
					
					<label for="registerpic_city" class="label_registerpic_city">Stadt:</label><br>
					<input type="text" name="registerpic_city" class="registerpic_city" size="20" maxlength="20" placeholder="Stadt" value="<?php if(isset($_GET['city'])) echo urldecode($_GET['city']);?>" required><br>
					
					<label for="registerpic_country" class="label_registerpic_country">Land:</label><br>
					<input type="text" name="registerpic_country" class="registerpic_country" size="20" maxlength="20" placeholder="Land" value="<?php if(isset($_GET['country'])) echo urldecode($_GET['country']);?>" required><br>
					
					<label for="registerpic_description" class="registerpic_description">Beschreibung:</label><br>
					<textarea name="registerpic_description" class="registerpic_description" rows="8" cols="40" maxlength="1000" required><?php if(isset($_GET['descr'])) echo urldecode(str_replace("ujhztg", "\r\n", $_GET['descr']));?></textarea><br>
<?php
		$publickey = "6LfIVekSAAAAAJddojA4s0J4TVf8P_gS2v1zv09P";
		echo recaptcha_get_html($publickey);
?>
					<input type="file" name="registerpic_file" id="registerpic_file" maxlength="<?php echo $max_file_size; ?>" required><br>
					<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>">
					<input type="submit" name="registerpic_submit" value="Bild posten"><br>
					<img id="image_preview" src="./img/placeholder.png" style="background-repeat: no-repeat;background-position: center;max-height:0px">
				</form>
			</article>
<?php
	}elseif(!$user->login) {
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
							<td><input type="password" name="password" id="password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required></td>
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
							<td><label for="reg_email">Email-Adresse</label></td>
							<td><input type="email" name="reg_email" id="reg_email" class="input_text" size="20" maxlength="30" placeholder="Email-Adresse" required></td>
						</tr>
						<tr>
							<td><label for="reg_password">Passwort</label></td>
							<td><input type="password" name="reg_password" id="reg_password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required></td>
						</tr>
						<tr>
							<td><label for="reg_password2">Passwort wiederholen</label></td>
							<td><input type="password" name="reg_password2" id="reg_password2" class="password" size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required></td>
						</tr>
						<tr>
							<td><input type="hidden" name="new_register" value="true"><input type="submit" value="Registrieren"></td>
							<td>&nbsp;</td>
						</tr>
					</table>
				<p>Deine E-Mail-Adresse wird nicht an Dritte weitergegeben. Wir benötigen sie zum Beispiel, um dir auf Anfrage dein Passwort senden zu können.</p>
				</form>
<?php
	}else {
		if($bracelet_status == 0) {
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
				}
				echo '<br><br>';
			}
		}
?>
				<form name="registerbr" action="<?php echo $friendly_self; ?>" method="post">
					<label for="reg_br">Armband registrieren</label>
					<input type="text" name="reg_br" id="reg_br" class="input_text" size="20" maxlength="10" placeholder="Armband ID" value="<?php if(isset($_GET["registerbr"])) {echo $_GET["registerbr"];}// else {echo "Armband ID";}?>">
					<input type="submit" name="submit" value="Armband registrieren">
				</form>
<?php
	}
}
?>
		</article>