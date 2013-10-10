<?php
	$user->userdetails($_SESSION['user'], $db);
	$user_details =  $user->userdetails($_SESSION['user'], $db);
	$user_details['users'] = $user_details[0][0];
	$user_details['addresses'] = $user_details[1][0];
	$brids = array();
	foreach ($user_details[2] as $key => $val) {
		$brids = array_merge_recursive($val, $brids);
	}
	$user_details['bracelets'] = $brids;
	$userdetails = array_merge($user_details['users'], $user_details['addresses'], $user_details['bracelets']);
?>
        <article id="kontakt" class="mainarticles bottom_border_green">
            <div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename['login'];?></h1></div>
<?php
//if($checklogin == true)echo "EINGELOGGT"; else echo "PECH";
//Wenn nicht eingeloggt
//if(isset($user)){if($user->logged == true){echo "2EINGELOGGT2"; echo '<a href="?logout">LOGOUT</a>';} else echo "2PECH2";}
//if(isset($_SESSION['user']))echo'- USERISTDA';
if($checklogin == false){
	if(isset($_GET['registerbr'])) {//Wenn man eine Armband ID eingegeben hat, kann man sich einloggen?>
        Bitte Logge dich ein oder erstelle dir einen neuen Account, um dein Armband zu registrieren<br>Armband Nr. <span style="color: #000; font-style: italic;"><?php echo $_GET['registerbr']; ?></span> registrieren:<br>
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
        </form><br>
<?php
	}
?>
        <form name="reg" id="form_reg" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
          <table style="border: 1px solid black">
            <tr>
              <td><label for="reg_login">Benutzername</label></td>
              <td><input type="text" name="reg_login" id="reg_login" class="input_text" size="20" maxlength="30" placeholder="Benutzername" required></td>
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
              <td><input type="password" name="reg_password" id="reg_password" class="password"  size="20" maxlength="30"  value="!§%&$%&/%§$" required></td>
            </tr>
            <tr>
              <td><label for="reg_password2">Passwort wiederholen</label></td>
              <td><input type="password" name="reg_password2" id="reg_password2" class="password" size="20" maxlength="30"  value="!§%&$%&/%§$" required></td>
            </tr>
            <tr>
              <td><input type="hidden" name="new_register" value="true"><input type="submit" value="Registrieren"></td>
              <td>&nbsp;</td>
            </tr>
        </form>
<?php
}
else {
?>
            <form name="registerbr" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <label for="reg_br">Armband registrieren</label>
                <input type="text" name="reg_br" id="reg_br" class="input_text" size="20" maxlength="10" placeholder="Armband ID" value="<?php if(isset($_GET["registerbr"])) {echo $_GET["registerbr"];}// else {echo "Armband ID";}?>">
                <input type="submit" name="submit" value="Armband registrieren">
            </form>
            <?php
			if (isset($_POST['reg_br'])) {
				if ($_POST['reg_br'] == $userdetails['brid']) {
					echo 'Armband '.$_POST['reg_br'].' erfolgreich registriert';
				}
				echo $_POST['reg_br'];
				echo $userdetails['brid'];
			}
			echo $userdetails['brid'];
			print_r ($userdetails);
}
?>
      </article>