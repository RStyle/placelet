			<article id="kontakt" class="mainarticles bottom_border_green">
<?php
if($user->login) {
	if($category == 'details') {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Deine Accounteinstellungen, <?php echo $user->login ?></h1></div>
				<div>
					Deine Accountdetails:
					<form name="change" action="account?details" method="post">
						<table border="0">
							<tr>
								<th>E-Mail Adresse</th>
								<td><?php echo $userdetails['email']; ?></td>
								<td><input type="email" name="change_email" size="20" maxlength="254" placeholder="E-Mail Adresse"></td>
							</tr>
							<tr>
								<th>Passwort</th>
								<td>&nbsp;</td>
								<td><input type="password" name="change_old_pwd" size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" placeholder="altes Password"></td>
							</tr>
							<tr>
								<th>&nbsp;</th>
								<td>&nbsp;</td>
								<td><input type="password" name="change_new_pwd" size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" placeholder="neues Passwort"></td>
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
	}elseif($category == 'notifications') {
?>
			<div class="green_line mainarticleheaders line_header"><h1>Benachrichtigungseinstellungen (<?php echo $user->login ?>)</h1></div>
				<div>
					<form action="account?notifications" method="post">
						<table border="0">
							<tr>
								<td>&nbsp;</td>
								<td>online</td>
								<td>E-Mail</td>
							</tr>
							<tr>
								<th>Neue Bilder von deinen Armbändern</th>
								<td><input type="checkbox" name="pic_own_online"<?php if($userdetails['notifications']['pic_own_online']) echo ' checked';?>></td>
								<td><input type="checkbox" name="pic_own_email"<?php if($userdetails['notifications']['pic_own_email']) echo ' checked';?>></td>
							</tr>
							<tr>
								<th>Neue Kommentare zu Bildern von deinen Armbändern</th>
								<td><input type="checkbox" name="comm_own_online"<?php if($userdetails['notifications']['comm_own_online']) echo ' checked';?>></td>
								<td><input type="checkbox" name="comm_own_email"<?php if($userdetails['notifications']['comm_own_email']) echo ' checked';?>></td>
							</tr>
							<tr>
								<th>Neue Kommentare zu deinen Bildern</th>
								<td><input type="checkbox" name="comm_pic_online"<?php if($userdetails['notifications']['comm_pic_online']) echo ' checked';?>></td>
								<td><input type="checkbox" name="comm_pic_email"<?php if($userdetails['notifications']['comm_pic_email']) echo ' checked';?>></td>
							</tr>
							<tr>
								<th>Neue Bilder zu deinen abonnierten Armbändern</th>
								<td><input type="checkbox" name="pic_subs_online"<?php if($userdetails['notifications']['pic_subs_online']) echo ' checked';?>></td>
								<td><input type="checkbox" name="pic_subs_email"<?php if($userdetails['notifications']['pic_subs_email']) echo ' checked';?>></td>
							</tr>
							<tr>
								<td><?php if(isset($_POST['notification_change'])) echo 'Erfolgreich geändert.'; else echo '&nbsp;';?></td>
								<td><input type="submit" name="submit" value="Änderungen speichern"></td>
								<td><input type="hidden" name="notification_change"></td>
							</tr>
						</table>
					</form>
				</div>
<?php
	}elseif($category == 'privacy') {
?>
			<div class="green_line mainarticleheaders line_header"><h1>Privatsphäreeinstellungen (<?php echo $user->login ?>)</h1></div>
			<p>Es sind noch keine Privatsphäreeinstellungen verfügbar, es wäre nett, wenn du uns mitteilen würdest, welche du gerne bei uns sehen würdest.</p>
<?php	
	}else {
?>
			<div class="green_line mainarticleheaders line_header"><h1>Keine Kategorie ausgewählt (<?php echo $user->login ?>)</h1></div>
			<p>
				Bitte wähle eine Kategorie aus:<br>
				<ul class="list_style_none" style="padding: 0;">
					<li><a href="account?details">Accountdetails ändern</a></li>
                	<li><a href="account?notifications">Benachrichtigungseinstellungen</a></li>
                	<li><a href="account?privacy">Privatsphäreeinstellungen</a></li>
				</ul>
			</p>
<?php
	}
}else {
	if(isset($_GET['recoverPassword'])) {
		if($_GET['recoverPassword'] == 'yes') {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Passwort vergessen?</h1></div>
				<p>
					Für den Fall, dass Sie Ihr Passwort vergessen haben sollten, tragen Sie bitte in das nachfolgende Eingabefeld die E-Mailadresse ein, auf welche Ihr Account bei placelet.de registriert ist.<br>
					In Kürze werden Sie eine E-Mail erhalten in der ein Link ist, mit dem Sie Ihr Passwort zurücksetzen können.
				</p>

				<form name="recover_password" action"account" method="post">
					E-Mail Adresse <input type="email" name="recover_email" size="20" maxlength="254" placeholder="E-Mail Adresse"><br>
					<input type="submit" name="submit" value="Neues Passwort zuschicken">
				</form>
<?php
		}
	}elseif(isset($recover_code)) {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Neues Passwort eingeben</h1></div>
<?php
			if($recover_code) {
?>
				<form name="change" action="account" method="post">
					<input type="password" name="new_pwd" size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" placeholder="neues Passwort">
					<input type="hidden" name="new_username" value="<?php echo $recover_code; ?>">
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
								<td><label for="acc_login">Benutzername&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
								<td><input type="text" name="login" id="acc_login" size="20" maxlength="15" placeholder="Benutzername" pattern=".{4,15}" title="Min.4 - Max.15" required></td>
							</tr>
							<tr>
								<td><label for="acc_password">Passwort</label></td>
								<td><input type="password" name="password" id="acc_password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required></td>
							</tr>
							<tr>
								<td><input type="submit" value="Login"></td>
								<td><input type="hidden" name="login_location" value="account?<?php echo $category; ?>"></td>
							</tr>
						</table>
					</form>
<?php
	}
}
?>
			</article>