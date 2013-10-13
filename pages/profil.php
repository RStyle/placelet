<?php
if (isset($_SESSION['user'])) {
	$userdetails = $user->userdetails();
?>
        <article id="kontakt" class="mainarticles bottom_border_green">
            <div class="green_line mainarticleheaders line_header"><h1>Profil, <?php echo $_SESSION['user'] ?></h1></div>
            <div style="float: left;">
            	Dein Account:
                <table border="0">
                	<?php /*
						foreach ($userdetails as $key => $val) {
							echo '
							<tr>
								<th>'.$key.'</th>
								<td>'.$val.'</td>
							</tr>
							';
						}*/
						//print_r ($userdetails);
					?>
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
            </div>
            <div style="float: left; margin-left: 2em;">
                Deine Armbänder:
                <table border="1">
                    <tr>
                        <th>Armband ID</th>
                        <th>registriert am</th>
                        <th>Anzahl Besitzer</th>
                    </tr>
                    <?php
						foreach ($userdetails['brid'] as $val => $key) {
							$armbaender['id'][$val] = $key;
						}
						foreach ($userdetails['date'] as $val => $key) {
							$armbaender['date'][$val] = $key;
						}
						for ($i = 0; $i < count($armbaender['id']); $i++) {
							echo '
							<tr>
								<td><a href="armband?id='.$armbaender['id'][$i].'">Armband '.$armbaender['id'][$i].'</a></td>
								<td>'.$armbaender['date'][$i].'</td>
								<td>noch nicht implementiert</td>
							</tr>';
						}
					?>
                </table>
            </div>
            <div style="clear: both;">
            	&nbsp;
            </div>
        </article>
<?php 
} else {
	?>
        <article id="kontakt" class="mainarticles bottom_border_green">
            <div class="green_line mainarticleheaders line_header"><h1>Profil</h1></div>
            Dein Profil kann nur angezeigt werden, wenn du eingeloggt bist.<br />
            Bitte logge dich ein:
            <form name="login" id="form_login" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <table style="border: 1px solid black">
                    <tr>
                        <td><label for="login">Benutzername&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
                        <td><input type="text" name="login" id="login" size="20" maxlength="30" placeholder="Benutzername" required></td>
                    </tr>
                    <tr>
                        <td><label for="password">Passwort</label></td>
                        <td><input type="password" name="password" id="password" class="password"  size="20" maxlength="30"  value="!§%&$%&/%§$" required></td>
                    </tr>
                    <tr>
                        <td><input type="submit" value="Login"></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </form>
        </article>
<?php
}
?>