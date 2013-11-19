			<article id="kontakt" class="mainarticles bottom_border_green">
<?php
foreach($_GET as $key => $val) {
	$_GET[$key] = clean_input($val);
}
if($user->login) {
	$username = $user->login;
}
//Passwortlink überprüfen
if(isset($_GET['passwordCode'])) {
	$recover_code = $user->check_recover_code($_GET['passwordCode']);
}
if(isset($_POST['submit'])) {
	switch($_POST['submit']) {
		//Link zum Passwort wiederherstellen senden
		case 'Neues Passwort zuschicken':
			$password_reset = $user->reset_password($_POST['recover_email'], $_POST['recover_email']);
			break;
		//Userdetails ändern
		case 'Änderungen speichern':
			if($user->login) {
				$change_details = $user->change_details($_POST['change_firstname'], $_POST['change_lastname'], $_POST['change_email'], $_POST['change_old_pwd'], $_POST['change_new_pwd'], $user->login);
				$js .= 'alert("'.$change_details.'");';
			}
			break;
		case 'Passwort ändern':
			if($recover_code) {
				$new_password = $user->new_password($recover_code, $_POST['new_pwd']);
				$js .= 'alert("'.$new_password.'");';
			}
			break;
	}
}
//Userdetails abrufen
if(isset($username) && Statistics::userexists($username)) {
	$userdetails = $statistics->userdetails($username);
	$armbaender = profile_stats($userdetails);
}
if ($user->login) {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Deine Accounteinstellungen, <?php echo $user->login ?></h1></div>
				<div>
					Deine Accountdetails:
					<form name="change" action="account" method="post">
						<table border="0">
							<tr>
								<th>Vorname:</th>
								<td><?php echo $userdetails['first_name']; ?></td>
								<td><input type="text" name="change_firstname" placeholder="Vorname"></td>
							</tr>
							<tr>
								<th>Nachname:</th>
								<td><?php echo $userdetails['last_name']; ?></td>
								<td><input type="text" name="change_lastname" placeholder="Nachname"></td>
							</tr>
							<tr>
								<th>E-Mail Adresse</th>
								<td><?php echo $userdetails['email']; ?></td>
								<td><input type="text" name="change_email" placeholder="E-Mail Adresse"></td>
							</tr>
							<tr>
								<th>Passwort</th>
								<td>&nbsp;</td>
								<td><input type="password" name="change_old_pwd" placeholder="altes Password"></td>
							</tr>
							<tr>
								<th>&nbsp;</th>
								<td>&nbsp;</td>
								<td><input type="password" name="change_new_pwd" placeholder="neues Passwort"></td>
							</tr>
							<tr>
								<th>&nbsp;</th>
								<td>&nbsp;</td>
								<td><input type="submit" name="submit" value="Änderungen speichern"></td>
							</tr>
						</table>
					</form>
				</div>
<?php 
} else {
	if(isset($_GET['recoverPassword'])) {
		if($_GET['recoverPassword'] == 'yes') {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Passwort vergessen?</h1></div>
				<p>
					Für den Fall, dass Sie Ihr Passwort vergessen haben sollten, tragen Sie bitte in das nachfolgende Eingabefeld die E-Mailadresse ein, auf welche Ihr Account bei placelet.de registriert ist.<br>
					In Kürze werden Sie eine E-Mail erhalten in der ein Link ist, mit dem Sie Ihr Passwort zurücksetzen können.
				</p>

				<form name="recover_password" action"account" method="post">
					E-Mail Adresse <input type="text" name="recover_email" placeholder="E-Mail Adresse"><br>
					<input type="submit" name="submit" value="Neues Passwort zuschicken">
				</form>
<?php
		}
	}elseif(isset($recover_code)) {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Neuese Passwort eingeben</h1></div>
<?php
		if($recover_code) {
?>
				<form name="change" action="account" method="post">
					<input type="password" name="new_pwd" placeholder="neues Passwort">
					<input type="submit" name="submit" value="Passwort ändern">
				</form>
<?php
		}else {
?>
				Dies ist kein gültiger Code, um dein Passwort zurückzusetzen.
<?php
		}
	}else {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Account</h1></div>
					Du kannst deine Accounteinstellungen nur ändern, wenn du eingeloggt bist.<br>
					Bitte logge dich ein:
					<form name="login" action="account" method="post">
						<table style="border: 1px solid black">
							<tr>
								<td><label for="login">Benutzername&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
								<td><input type="text" name="login" id="login" size="20" maxlength="15" placeholder="Benutzername" pattern=".{4,15}" title="Min.4 - Max.15" required></td>
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
					</form>
<?php
	}
}
?>
			</article>