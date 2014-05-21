			<article class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page];?></h1></div>
<?php
if(isset($loginattempt) || isset($_GET['notexisting'])) {
	if(isset($loginattempt)) echo '
				'.$lang->php->loginattempt->$lng.'<br><br>';
	if(isset($_GET['notexisting'])) echo '
				'.$lang->php->notexisting->$lng.'<br><br>';
?>
				<form name="login" id="form_login" action="login" method="post">
					<table class="border_black">
						<tr>
							<td><label for="login"><?php echo $lang->form->benutzername->$lng; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
							<td><input type="text" name="login" id="login" size="20" maxlength="30" pattern="\w{4,15}" title="Min.4 - Max.15" placeholder="<?php echo $lang->form->benutzername->$lng; ?>" required></td>
						</tr>
						<tr>
							<td><label for="password"><?php echo $lang->form->passwort->$lng; ?></label></td>
							<td><input type="password" name="password" id="password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%&$%&/%§$" required></td>
						</tr>
						<tr>
							<td><input type="submit" value="Login"></td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</form><br>
				<a href="/account?recoverPassword=yes"><?php echo $lang->form->passwort_vergessen->$lng; ?></a>
<?php
}elseif(isset($postpic)) {
	$postpic_id = array(false);
	if($postpic != "") 
		preg_match("/^\w{".$ziffern."}$/", $postpic, $postpic_id);
?>
				<div id="register_pic">
					<form name="registerpic" action="/login?postpic=<?php echo $postpic; ?>" method="post" enctype="multipart/form-data">
						<span class="verdana_times"><?php echo $lang->login->bildposten->$lng; ?></span><br><br>
						
						<label for="registerpic_brid" class="label_registerpic_brid"><?php echo $lang->login->armbandid->$lng; ?>:</label><br>
						<input type="text" name="registerpic_brid" maxlength="<?php echo $ziffern; ?>" size="6" pattern="\w{<?php echo $ziffern; ?>}" title="<?php echo $lang->community->sixcharacters->$lng; ?>" id="registerpic_brid" value="<?php if(count($postpic_id) == 1) echo $postpic_id[0]; else if(isset($postpic)) if($postpic != 'true') echo @$_POST['registerpic_brid']; ?>" required><br>
						
						<label for="registerpic_title" class="label_registerpic_title"><?php echo $lang->login->title->$lng; ?>:</label><br>
						<input type="text" name="registerpic_title" class="registerpic_title" size="20" maxlength="30" pattern=".{4,30}" id="registerpic_title" title="Min. 4 - Max. 30" placeholder="<?php echo $lang->login->title->$lng; ?>" value="<?php if(isset($postpic)) if($postpic != 'true') echo @$_POST['registerpic_title'];?>" required><br>
						
						<label for="registerpic_city" class="label_registerpic_city"><?php echo $lang->login->city->$lng; ?>:</label><br>
						<input type="text" name="registerpic_city" class="registerpic_city" id="registerpic_city" size="20" placeholder="<?php echo $lang->login->city->$lng; ?>" value="<?php if(isset($postpic)) if($postpic != 'true') echo @$_POST['registerpic_city'];?>" required><br>
						
						<label for="registerpic_country" class="label_registerpic_country"><?php echo $lang->login->country->$lng; ?>:</label><br>
						<input type="text" name="registerpic_country" class="registerpic_country" id="registerpic_country" size="20" placeholder="<?php echo $lang->login->country->$lng; ?>" value="<?php if(isset($postpic)) if($postpic != 'true') echo @$_POST['registerpic_country'];?>" required><br>
						
						<label for="registerpic_state" class="label_registerpic_state"><?php echo $lang->login->state->$lng; ?>:</label><br>
						<input type="text" name="registerpic_state" class="registerpic_state" id="registerpic_state" size="20" placeholder="<?php echo $lang->login->state->$lng; ?>" value="<?php if(isset($postpic)) if($postpic != 'true') echo @$_POST['registerpic_state'];?>"><br>
						
						<div id="map">
							<div id="pos"><?php echo $lang->login->map_placeholder->$lng; ?></div>
							<p><?php echo $lang->login->mapinfo->$lng; ?></p>
						</div>
						
						<input type="hidden" name="registerpic_latitude" id="latitude" value="<?php if(isset($postpic)) if($postpic != 'true') echo @$_POST['registerpic_latitude'];?>">
						<input type="hidden" name="registerpic_longitude" id="longitude" value="<?php if(isset($postpic)) if($postpic != 'true') echo @$_POST['registerpic_longitude'];?>">
						
						<label for="registerpic_description" class="registerpic_description"><?php echo $lang->login->description->$lng; ?>:</label><br>
						<textarea name="registerpic_description" id="registerpic_description" class="registerpic_description" rows="8" cols="40" maxlength="1000" required><?php if($postpic != 'true') echo @$_POST['registerpic_description'];?></textarea><br>
<?php
		//$publickey = "6LfIVekSAAAAAJddojA4s0J4TVf8P_gS2v1zv09P";
		//echo recaptcha_get_html($publickey);
?>
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>">
						<input type="hidden" name="registerpic_date" id="registerpic_date" value="default">
						<input type="hidden" name="" value="">
						<div id="registerpic_upload_inputs"<?php if($user->login == false) echo ' class="display_none"'; ?>>
							<input type="file" name="registerpic_file" accept="image/*" id="upload_pic"><br>
							<input type="submit" name="registerpic_submit" id="registerpic_submit" value="<?php echo $lang->login->bildupload->$lng; ?>"><br>
							<?php echo $lang->login->preview->$lng; ?>:<br>
							<img id="image_preview" src="/cache.php?f=/img/placeholder.png" alt="preview">
						</div>
					</form>
					
<?php
	if($user->login == false) {
?>
						<p class="picupload_nologin_text"><?php echo $lang->login->notlogged_pic->$lng; ?></p>
						<?php /* <!--<form action="./" method="post">--> */ ?>
							<span id="picupload_login_errormsg"></span>
							<input type="text" size="20" name="picupload_login_username" maxlength="15" placeholder="<?php echo $lang->form->benutzername->$lng; ?>" pattern="\w{4,15}" title="Min.4 - Max.15" class="picupload_nologin_text" id="picupload_login_username"><br>
							<input type="password" size="20" name="picupload_login_password" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" class="picupload_nologin_text password" id="picupload_login_password"><br>
							<input type="submit" value="Login" class="picupload_nologin_text display_none" id="picupload_login_submit"><img src="/cache.php?f=/img/loading.gif" alt="Laden..." id="picupload_login_loading">
						<?php /* <!--</form>--> */ ?>
<?php
	}
?>
				</div>
<?php
}elseif(isset($registerbr)) {
	if($user->login) {
		if(isset($bracelet_registered)) {
			switch($bracelet_registered) {
					case 0:
						echo $lang->php->bracelet_registered->f0->$lng;
						break;
					case 2:
						echo $lang->php->bracelet_registered->f2->$lng;
						break;
					default:
						if($bracelet_registered[0] == 3)
							echo $lang->php->bracelet_registered->f3->first->$lng.' <a href="/profil?user='.urlencode($bracelet_registered[1]).'">'.$bracelet_registered[1].'</a>'.$lang->php->bracelet_registered->f3->last->$lng;
			}
		}
?>
				<form name="registerbr" action="/login?registerbr" method="post">
					<label for="reg_br"><?php echo $lang->login->armband_registrieren->$lng; ?></label>
					<input type="text" name="reg_br" id="reg_br" class="input_text" maxlength="<?php echo $ziffern; ?>" size="20" pattern="\w{<?php echo $ziffern; ?>}" title="<?php echo $lang->community->sixcharacters->$lng; ?>" placeholder="<?php echo $lang->login->armbandid->$lng; ?>" value="<?php if(isset($_GET["registerbr"])) {echo $_GET["registerbr"];}?>" autofocus required>
					<input type="submit" name="registerbr_submit" value="<?php echo $lang->form->register->$lng; ?>">
				</form>
<?php
	}else {//Wenn man eine Armband ID eingegeben hat, kann man sich einloggen
?>
				<form name="login" id="form_login" action="/login" method="post" class="float_left">
				<?php echo $lang->login->notlogged_armband1->$lng; if($registerbr != '') echo 'Nr. <span class="italic000">'.$registerbr.'</span> '.$lang->login->notlogged_armband2->$lng; ?>
					<table class="border_black">
						<tr>
							<td><label for="log_login"><?php echo $lang->form->benutzername->$lng; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
							<td><input type="text" name="login" id="log_login" size="20" maxlength="15" placeholder="<?php echo $lang->form->benutzername->$lng; ?>" pattern="\w{4,15}" title="Min.4 - Max.15" required></td>
						</tr>
						<tr>
							<td><label for="log_password"><?php echo $lang->form->passwort->$lng; ?></label></td>
							<td><input type="password" name="password" id="log_password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required></td>
						</tr>
						<tr>
							<td><input type="submit" value="Login"></td>
							<td><input type="hidden" name="login_location" value="login?registerbr=<?php echo $registerbr; ?>"></td>
						</tr>
					</table>
				</form>
				<form name="reg" id="form_reg" action="/login" method="post" class="float_right"><?php /*<!--DIESES FORMULAR MUSS IMMER GLEICHZEITIG MIT DEM UNTEN AKTUALISIERT WERDEN!!! -->*/ ?>				
					<table class="border_black">
						<tr>
							<td><label for="reg_login"><?php echo $lang->form->benutzername->$lng; ?></label></td>
							<td><input type="text" name="reg_login" id="reg_login" class="input_text" size="20" maxlength="30" placeholder="<?php echo $lang->form->benutzername->$lng; ?>" pattern="\w{4,15}" title="Min.4 - Max.15" required></td>
						</tr>
						<tr>
							<td><label for="reg_email"><?php echo $lang->form->email->$lng; ?></label></td>
							<td><input type="email" name="reg_email" id="reg_email" class="input_text" size="20" maxlength="100" placeholder="<?php echo $lang->form->email->$lng; ?>" required></td>
						</tr>
						<tr>
							<td><label for="reg_password"><?php echo $lang->form->passwort->$lng; ?></label></td>
							<td><input type="password" name="reg_password" id="reg_password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required></td>
						</tr>
						<tr>
							<td><label for="reg_password2"><?php echo $lang->form->repeat_passwort->$lng; ?></label></td>
							<td><input type="password" name="reg_password2" id="reg_password2" class="password" size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required></td>
						</tr>
						<tr>
							<td><input type="hidden" name="new_register" value="true"><input type="submit" value="<?php echo $lang->form->register->$lng; ?>"></td>
							<td><input type="hidden" name="lng" id="longitude" value="<?php echo $lng;?>"></td>
						</tr>
					</table>
					<?php echo $lang->login->disclaimer->$lng; ?>
				</form>
<?php
	}
}elseif(isset($unvalidated) || @$user_registered === true || isset($revalidation)) {
	if(isset($user_registered)) {
?>
				<p><?php echo $lang->php->successfully_registered->$lng; ?></p>
<?php
	}elseif(isset($revalidation)) {
		if($revalidation === true){
?>
				<p><?php echo $lang->php->revalidation->wahr->$lng; ?></p>
<?php
		}else {
			echo $revalidation;
		}
	}else {
?>
				<p><?php echo $lang->php->revalidation->falsch->$lng; ?></p>
<?php
	}
?>
				<form method="post" action="/login">
					<input type="text" name="revalidate_user" size="20" maxlength="15" placeholder="<?php echo $lang->form->benutzername->$lng; ?>" pattern="\w{4,15}" <?php if(isset($unvalidated)) echo 'value="'.$unvalidated.'" '; ?>title="Min.4 - Max.15" required>
					<input type="email" name="revalidate_email" size="20" maxlength="100" placeholder="<?php echo $lang->form->email->$lng; ?>" required>
					<input type="submit" name="revalidate_submit" value="<?php echo $lang->login->change_email->$lng; ?>">
				</form>
<?php
}elseif(!$user->login) {
	if(isset($user_registered)) echo '<span style="font-style: italic; font-weight: bold;">'.$user_registered.'</span>';
?>
                <div id="register_pics">
                    <div id="reg_pic3" class="register_pic" style="margin-top: 25px; border-color: #D340FF;">
                        <div class="overlay"></div>
                        <p><?php echo $lang->login->reg_pic3_text->$lng; ?></p>
                    </div>
                    <div id="reg_pic1" class="register_pic" style="width: 300px; height: 300px;">
                        <div class="overlay"></div>
                        <p style="width: 200px; top: 85px;"><?php echo $lang->login->reg_pic1_text->$lng; ?></p>
                    </div>
                    <div id="reg_pic2" class="register_pic" style="margin-right: 0; margin-top: 25px; border-color: #05EB80;">
                        <div class="overlay"></div>
                        <p style="left: 40px;"><?php echo $lang->login->reg_pic2_text->$lng; ?></p>
                    </div>
                </div>
                <p id="reg_text"><?php echo $lang->login->register_now->$lng; ?><br>
                <span style="float:left; width: 110px;">&nbsp;</span><span class="arrow_down" style="border-top-color: #FFF;" ></span></p>
                <hr>
                
				<form name="reg" id="form_reg" action="/login" method="post" class="hr_clear"><?php /*<!--DIESES FORMULAR MUSS IMMER GLEICHZEITIG MIT DEM OBEN AKTUALISIERT WERDEN!!! -->*/ ?>					
					<table id="reg_table">
						<tr>
							<td><label for="reg_login"><?php echo $lang->form->benutzername->$lng; ?></label></td>
							<td><input type="text" name="reg_login" id="reg_login" class="input_text" size="20" maxlength="30" placeholder="<?php echo $lang->form->benutzername->$lng; ?>" pattern="\w{4,15}" title="Min.4 - Max.15" required></td>
						</tr>
						<tr>
							<td><label for="reg_email"><?php echo $lang->form->email->$lng; ?></label></td>
							<td><input type="email" name="reg_email" id="reg_email" class="input_text" size="20" maxlength="100" placeholder="<?php echo $lang->form->email->$lng; ?>" required></td>
						</tr>
						<tr>
							<td><label for="reg_password"><?php echo $lang->form->passwort->$lng; ?></label></td>
							<td><input type="password" name="reg_password" id="reg_password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required></td>
						</tr>
						<tr>
							<td><label for="reg_password2"><?php echo $lang->form->repeat_passwort->$lng; ?></label></td>
							<td><input type="password" name="reg_password2" id="reg_password2" class="password" size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required></td>
						</tr>
						<tr>
							<td><input type="hidden" name="new_register" value="true"><input type="submit" value="<?php echo $lang->form->register->$lng; ?>"></td>
							<td><input type="hidden" name="lng" id="longitude" value="<?php echo $lng;?>"></td>
						</tr>
					</table>
					<p id="disclaimer"><?php echo $lang->login->disclaimer->$lng; ?></p>
				</form>
<?php
}else {
?>
				<p><?php echo $lang->misc->nope->$lng; ?></p>
<?php
}
?>
			</article>