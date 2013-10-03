      <article id="kontakt" class="mainarticles">
        <div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename['login'];?></h1></div><?php
        if($checklogin == true)echo "eingeloggt"; else echo "nicht eingeloggt";
//Wenn nicht eingeloggt
if(isset($user)){if($user->logged == true){echo "-EINGELOGGT-"; echo '<a href="?logout">LOGOUT</a>';} else echo "falsches Login";}
if(isset($_SESSION['user']))echo'- USERISTDA';
if($_SESSION['login']==false){
if(isset($_GET['registerbr'])) {//Wenn man eine Armband ID eingegeben hat, kann man sich einloggen
	echo'
	<br>Bitte Logge dich ein oder erstelle dir einen neuen Account<br>Armband Nr. <span style="color: #000; font-style: italic;">'.$_GET['registerbr'].'</span> registrieren:<br>
	<form name="login" id="form_login" action="'.$_SERVER['PHP_SELF'].'" method="post">
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
	</table><br>';
}
echo '</form>
<form name="reg" id="form_reg" action="'.$_SERVER['PHP_SELF'].'" method="post">
  <table style="border: 1px solid black">
    <tr>
	  <td><label for="reg_login">Benutzername</label></td>
	  <td><input type="text" name="reg_login" id="reg_login" class="input_text" size="20" maxlength="30" placeholder="Benutzername" required></td>
	</tr>
	<tr>
	  <td><label for="reg_first_name">Vorname</label>
	  <td><input type="text" name="reg_first_name" id="reg_first_name" class="input_text" size="20" maxlength="30" placeholder="Vorname" required></td>
	</tr>
	<tr>
	  <td><label for="reg_name">Nachname</label></td>
	  <td><input type="text" name="reg_name" id="reg_name" class="input_text" size="20" maxlength="30" placeholder="Nachname" required></td>
	</tr>
	<tr>
	  <td><label for="reg_email">Email-Adresse</label>
	  <td><input type="email" name="reg_email" id="reg_email" class="input_text" size="20" maxlength="30" placeholder="Email-Adresse" required></td>
	</tr>
	<tr>
	  <td><label for="reg_password">Passwort</label>
	  <td><input type="password" name="reg_password" id="reg_password" class="password"  size="20" maxlength="30"  value="!§%&$%&/%§$" required></td>
	</tr>
	<tr>
	  <td><label for="reg_password2">Passwort wiederholen</label></td>
	  <td><input type="password" name="reg_password2" id="reg_password2" class="password" size="20" maxlength="30"  value="!§%&$%&/%§$" required></td>
	</tr>
	<tr>
	  <td><input type="submit" value="Registrieren"></td>
	  <td>&nbsp;</td>
	</tr>
</form>
';}
?>
      </article>