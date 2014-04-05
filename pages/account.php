			<article id="kontakt" class="mainarticles bottom_border_green">
<?php
if($user->login) {
	if($category == 'details') {
?>
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->account->titel->$lng; ?>, <?php echo $user->login ?></h1></div>
				<div>
					<?php echo $lang->account->deine->$lng.' '.$lang->account->details->$lng; ?>:
					<form name="change" action="account?details" method="post">
						<table border="0">
							<tr>
								<th><?php echo $lang->form->benutzername->$lng; ?></th>
								<td><?php echo $userdetails['user']; ?></td>
								<td><input type="text" name="change_new_name" size="20" maxlength="15" placeholder="<?php echo $lang->form->benutzername->$lng; ?>"></td>
							</tr>
							<tr>
								<th><?php echo $lang->form->email->$lng; ?></th>
								<td><?php echo $userdetails['email']; ?></td>
								<td><input type="email" name="change_email" size="20" maxlength="100" placeholder="<?php echo $lang->form->email->$lng; ?>"></td>
							</tr>
							<tr>
								<th><?php echo $lang->form->passwort->$lng; ?></th>
								<td>&nbsp;</td>
								<td><input type="password" name="change_old_pwd" size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" placeholder="<?php echo $lang->form->oldpass->$lng; ?>"></td>
							</tr>
							<tr>
								<th>&nbsp;</th>
								<td>&nbsp;</td>
								<td><input type="password" name="change_new_pwd" size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" placeholder="<?php echo $lang->form->newpass->$lng; ?>"></td>
							</tr>
							<tr>
								<th>&nbsp;</th>
								<td>&nbsp;</td>
								<td><input type="submit" name="submit" value="<?php echo $lang->form->speichern->$lng; ?>"></td>
							</tr>
						</table>
					</form>
				</div>
<?php 
	}elseif($category == 'notifications') {
?>
			<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->account->notifications->$lng; ?> (<?php echo $user->login ?>)</h1></div>
				<div>
					<form action="account?notifications" method="post">
						<table border="0">
							<tr>
								<td>&nbsp;</td>
								<td>online</td>
								<td>E-Mail</td>
							</tr>
							<tr>
								<th><?php echo $lang->account->pic_owns->$lng; ?></th>
								<td><input type="checkbox" name="pic_own_online"<?php if($userdetails['notifications']['pic_own_online']) echo ' checked';?>></td>
								<td><input type="checkbox" name="pic_own_email"<?php if($userdetails['notifications']['pic_own_email']) echo ' checked';?>></td>
							</tr>
							<tr>
								<th><?php echo $lang->account->comm_owns->$lng; ?></th>
								<td><input type="checkbox" name="comm_own_online"<?php if($userdetails['notifications']['comm_own_online']) echo ' checked';?>></td>
								<td><input type="checkbox" name="comm_own_email"<?php if($userdetails['notifications']['comm_own_email']) echo ' checked';?>></td>
							</tr>
							<tr>
								<th><?php echo $lang->account->comm_pics->$lng; ?></th>
								<td><input type="checkbox" name="comm_pic_online"<?php if($userdetails['notifications']['comm_pic_online']) echo ' checked';?>></td>
								<td><input type="checkbox" name="comm_pic_email"<?php if($userdetails['notifications']['comm_pic_email']) echo ' checked';?>></td>
							</tr>
							<tr>
								<th><?php echo $lang->account->pic_subs->$lng; ?></th>
								<td><input type="checkbox" name="pic_subs_online"<?php if($userdetails['notifications']['pic_subs_online']) echo ' checked';?>></td>
								<td><input type="checkbox" name="pic_subs_email"<?php if($userdetails['notifications']['pic_subs_email']) echo ' checked';?>></td>
							</tr>
							<tr>
								<td><?php if(isset($_POST['notification_change'])) echo $lang->form->saved->$lng; else echo '&nbsp;';?></td>
								<td><input type="submit" name="submit" value="<?php echo $lang->form->speichern->$lng; ?>"></td>
								<td><input type="hidden" name="notification_change"></td>
							</tr>
						</table>
					</form>
				</div>
<?php
	}elseif($category == 'privacy') {
?>
			<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->account->privacy->$lng; ?> (<?php echo $user->login ?>)</h1></div>
			<p><?php echo $lang->account->privacy_availability->$lng; ?></p>
<?php	
	}else {
?>
			<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->account->nocategory->$lng; ?> (<?php echo $user->login ?>)</h1></div>
			<p>
				<?php echo $lang->account->category->$lng; ?>:<br>
				<ul class="list_style_none" style="padding: 0;">
					<li><a href="/account?details"><?php echo $lang->account->details->$lng; ?></a></li>
                	<li><a href="/account?notifications"><?php echo $lang->account->notifications->$lng; ?></a></li>
                	<li><a href="/account?privacy"><?php echo $lang->account->privacy->$lng; ?></a></li>
                	<!--<li><a href="/account?delete"><?php echo $lang->account->delete->$lng; ?></a></li>-->
				</ul>
			</p>
<?php
	}
}else {
	if(isset($_GET['recoverPassword'])) {
		if($_GET['recoverPassword'] == 'yes') {
?>
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->form->passwort_vergessen->$lng; ?></h1></div>
				<p>
					<?php echo $lang->account->lostpasswort->$lng; ?>
				</p>

				<form name="recover_password" action"account" method="post">
					<?php echo $lang->form->email->$lng; ?>: <input type="email" name="recover_email" size="20" maxlength="100" placeholder="<?php echo $lang->form->email->$lng; ?>"><br>
					<input type="submit" name="submit" value="<?php echo $lang->form->newpass->$lng; ?>">
				</form>
<?php
		}
	}elseif(isset($recover_code)) {
?>
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->account->neues_passwort->$lng; ?></h1></div>
<?php
			if($recover_code) {
?>
				<form name="change" action="account" method="post">
					<input type="password" name="new_pwd" size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" placeholder="<?php echo $lang->form->newpass->$lng; ?>">
					<input type="hidden" name="new_username" value="<?php echo $recover_code; ?>">
					<input type="submit" name="submit" value="<?php echo $lang->form->changepass->$lng; ?>">
				</form>
<?php
		}else {
?>
				<?php echo $lang->account->invalid_code->$lng; ?>
<?php
		}
	}else {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Account</h1></div>
					<?php echo $lang->account->ausgeloggt->$lng; ?>
					<form name="login" action="account" method="post">
						<table style="border: 1px solid black">
							<tr>
								<td><label for="acc_login"><?php echo $lang->form->benutzername->$lng; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
								<td><input type="text" name="login" id="acc_login" size="20" maxlength="15" placeholder="<?php echo $lang->form->benutzername->$lng; ?>" pattern=".{4,15}" title="Min.4 - Max.15" required></td>
							</tr>
							<tr>
								<td><label for="acc_password"><?php echo $lang->form->passwort->$lng; ?></label></td>
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