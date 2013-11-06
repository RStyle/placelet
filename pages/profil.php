			<article id="kontakt" class="mainarticles bottom_border_green">
<?php
foreach($_GET as $key => $val) {
	$_GET[$key] = clean_input($val);
}
if(isset($_GET['user'])) {
	$username = $_GET['user'];
}elseif($user->login) {
	$username = $user->login;
}
if(isset($username) && Statistics::userexists($username)) {
	$userdetails = $statistics->userdetails($username);
	$armbaender = profile_stats($userdetails);
}

if(!isset($_GET['user'])) {
	if ($user->login) {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Dein Profil, <?php echo $user->login ?></h1></div>
				<div style="float: left;">
					Dein Account:
					<table border="0">
						<tr>
							<th>Benutzername:</th>
							<td><?php echo $userdetails['user']; ?></td>
						</tr>
						<tr>
							<th>Vorname:</th>
							<td><?php echo $userdetails['first_name']; ?></td>
						</tr>
						<tr>
							<th>Nachname:</th>
							<td><?php echo $userdetails['last_name']; ?></td>
						</tr>
						<tr>
							<th>E-Mail Adresse</th>
							<td><?php echo $userdetails['email']; ?></td>
						</tr>
						<tr>
							<th>Status</th>
							<td><?php echo $userdetails['status']; ?></td>
						</tr>
					</table>
					<p><a href="account">Accounteinstellungen ändern</a></p>
				</div>
				<div style="float: left; margin-left: 2em;">
<?php
							if (isset($userdetails['brid'])) {
?>
					Deine Armbänder:
					<table border="1">
						<tr>
							<th>Armband Name</th>
							<th>Armband ID</th>
							<th>registriert am</th>
							<th>Anzahl Besitzer</th>
						</tr>
<?php
								for ($i = 0; $i < count($armbaender['brid']); $i++) {
									if(!isset($armbaender['picture_count'][$armbaender['brid'][$i]]['picid'])) $armbaender['picture_count'][$armbaender['brid'][$i]]['picid'] = 0;
										echo '						<tr>
							<td><a href="armband?name='.urlencode($statistics->brid2name($armbaender['brid'][$i])).'">'.$statistics->brid2name($armbaender['brid'][$i]).'</a></td>
							<td>'.$armbaender['brid'][$i].'</a></td>
							<td>'.date('d.m.Y', $armbaender['date'][$i]).'</td>
							<td>'.$armbaender['picture_count'][$armbaender['brid'][$i]]['picid'].'</td>
						</tr>
';
								}
							} else {
								echo 'Du besitzt leider noch kein Armband.';
							}
?>
					</table>
				</div>
				<div style="clear: both;">
					&nbsp;
				</div>
<?php 
	} else {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Profil</h1></div>
				<div style="float: left; margin-right: 2em;">
					Dein Profil kann nur angezeigt werden, wenn du eingeloggt bist.<br>
					Bitte logge dich ein:
					<form name="login" action="<?php echo $friendly_self; ?>" method="post">
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
				</div>
				<div>
					Oder suche nach dem Profil von jemand anderem:
					<form action="<?php echo $friendly_self; ?>" method="get">
						<table style="border: 1px solid black; overflow: auto;">
							<tr>
								<td><input type="text" name="user" size="20" maxlength="15" placeholder="Benutzername" pattern=".{4,15}" title="Min.4 - Max.15" required></td>
								<td><input type="submit" value="Suchen"></td>
							</tr>
						</table>
					</form>
				</div>
				<div style="clear: both;">
					&nbsp;
				</div>
<?php
	}
} elseif(Statistics::userexists($username)){
?>
            <div class="green_line mainarticleheaders line_header"><h1>Profil von <?php echo $username; ?></h1></div>
<?php
							if (isset($userdetails['brid'])) {
?>
				Seine Armbänder:
				<table border="1">
					<tr>
						<th>Armband Name</th>
						<th>registriert am</th>
						<th>Anzahl Besitzer</th>
					</tr>
<?php
								for ($i = 0; $i < count($armbaender['brid']); $i++) {
									if(!isset($armbaender['picture_count'][$armbaender['brid'][$i]]['picid'])) $armbaender['picture_count'][$armbaender['brid'][$i]]['picid'] = 0;
									echo '
					<tr>
						<td><a href="armband?name='.urlencode($statistics->brid2name($armbaender['brid'][$i])).'">'.$statistics->brid2name($armbaender['brid'][$i]).'</a></td>
						<td>'.date('d.m.Y', $armbaender['date'][$i]).'</td>
						<td>'.$armbaender['picture_count'][$armbaender['brid'][$i]]['picid'].'</td>
					</tr>';
								}
							} else {
								echo 'Dieser Benutzer besitzt noch kein Armband.';
							}
?>
			</table>
<?php
}else {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Benutzer existiert nicht</h1></div>
				<p>Dieser Benutzer existiert nicht.</p>
<?php
}
?>
			</article>