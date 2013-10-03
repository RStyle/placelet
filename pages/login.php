      <article id="kontakt" class="mainarticles">
        <div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename['login'];?></h1></div><?php
        if($checklogin == true)echo "eingeloggt"; else echo "nicht eingeloggt";
//Wenn nicht eingeloggt
if(isset($user)){if($user->logged == true){echo "-EINGELOGGT-"; echo '<a href="?logout">LOGOUT</a>';} else echo "falsches Login";}
if(isset($_SESSION['user']))echo'- USERISTDA';
if($_SESSION['login']==false){
echo'
<form name="login" id="form_login" action="'.$_SERVER['PHP_SELF'].'" method="post">
  <table style="border: 1px solid black">
	  <tr>
	    <td><label for="login">Benutzername</label></td>
		<td><input type="text" name="login" id="login" size="20" maxlength="30" placeholder="Username" required></td>
      </tr>
	  <tr>
	    <td><label for="password">Passwort</label></td>
		<td><input type="password" name="password" id="password" class="password"  size="20" maxlength="30"  value="!§%&$%&/%§$" required></td>
      </tr>
	  <tr>
        <td><input type="submit" value="Login"></td>
		<td>&nbsp;</td>
	  </tr>
<!--	</table>-->
</form>
<form name="reg" id="form_reg" action="'.$_SERVER['PHP_SELF'].'" method="post">
<!--  <table>-->
    <tr>
	  <td><label for="reg_login">Benutzername</label></td>
	  <td><input type="text" name="reg_login" id="reg_login" class="input_text" size="20" maxlength="30" placeholder="Username" required></td>
	</tr>
	<tr>
	  <td><label for="reg_name">Nachname</label></td>
	  <td><input type="text" name="reg_name" id="reg_name" class="input_text" size="20" maxlength="30" placeholder="Name" required></td>
	</tr>
	<tr>
	  <td><label for="reg_first_name">Vorname</label>
	  <td><input type="text" name="reg_first_name" id="reg_first_name" class="input_text" size="20" maxlength="30" placeholder="First name" required></td>
	</tr>
	<tr>
	  <td><label for="reg_email">Email-Adresse</label>
	  <td><input type="email" name="reg_email" id="reg_email" class="input_text" size="20" maxlength="30" placeholder="Email" required></td>
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