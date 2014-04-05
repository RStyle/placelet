			<article id="profil" class="mainarticles bottom_border_green">
<?php
if(!isset($_GET['user']) && !$user->login) {
?>
								<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->profil[$lng.'-title']; ?></h1></div>
                                <div style="float: left; margin-right: 2em;">
                                        <?php echo $lang->profil->notlogged->$lng; ?>
                                        <form name="login" action="profil" method="post">
                                                <table style="border: 1px solid black">
                                                        <tr>
                                                                <td><label for="profile_login"><?php echo $lang->form->benutzername->$lng; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
                                                                <td><input type="text" name="login" id="profile_login" size="20" maxlength="15" placeholder="<?php echo $lang->form->benutzername->$lng; ?>" pattern=".{4,15}" title="Min.4 - Max.15" required></td>
                                                        </tr>
                                                        <tr>
                                                                <td><label for="profile_password"><?php echo $lang->form->passwort->$lng; ?></label></td>
                                                                <td><input type="password" name="password" id="profile_password" class="password"  size="20" maxlength="30" pattern=".{6,30}" title="Min.6 - Max.30" value="!§%$$%\/%§$" required></td>
                                                        </tr>
                                                        <tr>
                                                                <td><input type="submit" value="Login"></td>
                                                                <td>&nbsp;</td>
                                                        </tr>
                                                </table>
                                        </form>
                                </div>
                                <div>
                                        <?php echo $lang->profil->searchprofil->$lng; ?>
                                        <form action="profil" method="get">
                                                <table style="border: 1px solid black; overflow: auto;">
                                                        <tr>
                                                                <td><input type="text" name="user" size="20" maxlength="15" placeholder="<?php echo $lang->form->benutzername->$lng; ?>" pattern=".{4,15}" title="Min.4 - Max.15" required></td>
                                                                <td><input type="submit" value="<?php echo $lang->form->suchen->$lng; ?>"></td>
                                                        </tr>
                                                </table>
                                        </form>
                                </div>
                                <div style="clear: both;">
                                        &nbsp;
                                </div>
<?php	
}elseif($user->login || Statistics::userexists($username)) {
?>
				<div class="green_line mainarticleheaders line_header"><h1><?php if($user->login == $username) echo $lang->profil->deinprofil->$lng.', '.htmlentities($user->login); else echo 'Profil von '.htmlentities($username); ?></h1></div>
				<div class="user_info">
					<img class="profile_pic" src="/img/profil_pic_small.png" alt="<?php echo $lang->profil->profilpic->$lng; ?>">           
					<h1><?php echo $userdetails['user']; ?></h1>
					<p>
						<?php echo $lang->profil->registered_since->$lng; ?>: <?php echo date('d.m.Y', $userdetails['registered']); ?><br>
						Status: <?php if($userdetails['status'] == 2) echo 'Admin'; else echo 'User'; ?>
					</p>
				</div>
<?php 
    if($user->login == $username) {
?>        
                <div class="logged_info">
                    <p>
						<?php echo $lang->profil->youremail->$lng; ?>: <?php echo $userdetails['email']; ?>
						<ul class="list_style_none" style="padding: 0;">
							<li><a href="/account?details"><?php echo $lang->profil->change_accountdetails->$lng; ?></a></li>
							<li><a href="/account?notifications"><?php echo $lang->account->notifications->$lng; ?></a></li>
							<li><a href="/account?privacy"><?php echo $lang->account->privacy->$lng; ?></a></li>
						</ul>
					</p>
                </div>      
<?php        
    }
?>
	            <div style="clear: both;">
<?php
	if($user->login == $username) {
		if($notifications['pic_owns'] == NULL && $notifications['comm_owns'] == NULL && $notifications['comm_pics'] == NULL && $notifications['pic_subs'] == NULL) $notifications['new'] = false;
			else $notifications['new'] = true;
?>
	<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Benachrichtigungen ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
				<p class="tabs pseudo_link" id="tab_1"><span class="tab_arrow1 arrow_down"></span>&nbsp;<?php echo $lang->profil->notifications->$lng; ?> (<?php if($notifications['new'])
				echo (count($notifications['pic_owns']) + count($notifications['comm_owns']) + count($notifications['comm_pics']) + count($notifications['pic_subs'])); else echo '0'; ?>)</p>
				<hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
					<div class="showcases" id="showcase_1">
<?php
		if($notifications['new']) {
			if($notifications['pic_owns'] != NULL) {
?>
						<div class="pic_owns notifications">
							<p><?php echo $lang->account->pic_owns->$lng; ?>:<br>&nbsp;</p>
<?php
				foreach($notifications['pic_owns'] as $pic) {
					$pic['name'] = $statistics->brid2name($pic['brid']);
					
					$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid=:brid');
					$stmt->execute(array('picid' => $pic['picid'], 'brid' => $pic['brid']));
					$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
?>
							<div class="previews">
								<a href="/armband?name=<?php echo urlencode($pic['name']); ?>" title="<?php echo urlencode($pic['brid']); ?>">
									<img alt="latest pic" class="previewpic" src="/pictures/bracelets/thumb<?php echo '-'.$rowid['id'].'.jpg'; ?>"><br>
									<p class="preview_text">
										<?php echo htmlentities($pic['name'])."\n"; ?>
										<span style="float:right;">Bilder: <?php echo $pic['picid']; ?></span>
									</p>
								</a>
							</div>
<?php
				}
?>
						</div>
<?php
			}
			if($notifications['pic_subs'] != NULL) {
?>
						<div class="pic_subs notifications">
							<p><?php echo $lang->account->pic_subs->$lng; ?>:</p>
<?php
				foreach($notifications['pic_subs'] as $pic) {
					$pic['name'] = $statistics->brid2name($pic['brid']);
					
					$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid=:brid');
					$stmt->execute(array('picid' => $pic['picid'], 'brid' => $pic['brid']));
					$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
?>
							<div class="previews">
								<a href="/armband?name=<?php echo urlencode($pic['name']); ?>" title="<?php echo urlencode($pic['brid']); ?>">
									<img alt="latest pic" class="previewpic" src="/pictures/bracelets/thumb<?php echo '-'.$rowid['id'].'.jpg'; ?>"><br>
									<p class="preview_text">
										<?php echo htmlentities($pic['name'])."\n"; ?>
										<span style="float:right;">Bilder: <?php echo $pic['picid']; ?></span>
									</p>
								</a>
							</div>
<?php
				}
?>
						</div>
<?php
			}
			if($notifications['comm_owns'] != NULL) {
?>
						<div class="comm_owns notifications">
							<p><?php echo $lang->account->comm_owns->$lng; ?>:</p>
<?php
				foreach($notifications['comm_owns'] as $comm) {
					$comm['user'] = Statistics::id2username($comm['userid']);
?>
							<div class="previews comments" style="display: block;">
								<strong><?php if($comm['userid'] == false) echo 'Anonym'; else echo $comm['user']; ?></strong>, <?php echo days_since($comm['date']).'('.date('H:i d.m.Y', $comm['date']).')'; ?>
								<p><?php echo $comm['comment']; ?></p> 
							</div>
<?php
				}
?>
						</div>
<?php
			}
			if($notifications['comm_pics'] != NULL) {
?>
						<div class="comm_owns notifications">
							<p><?php echo $lang->account->comm_pics->$lng; ?>:</p>
<?php
				foreach($notifications['comm_pics'] as $comm) {
					$comm['user'] = Statistics::id2username($comm['userid']);
?>
							<div class="previews comments" style="display: block;">
								<strong><?php if($comm['user'] == false) echo 'Anonym'; else echo $comm['user']; ?></strong>, <?php echo days_since($comm['date']).'('.date('H:i d.m.Y', $comm['date']).')'; ?>
								<p><?php echo $comm['comment']; ?></p> 
							</div>
<?php
				}
?>
						</div>
<?php
			}
?>
						<span id="notific_read" class="pseudo_link" style="clear: both;"><?php echo $lang->profil->notific_read->$lng; ?></span>
<?php
		}else echo '<p>'.$lang->profil->nonew_notific->$lng.'.</p>';
?>
					</div>
<?php
	}
?>					
	<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Armbänder ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
				<p class="tabs pseudo_link" id="tab_2"><span class="tab_arrow2 arrow_down"></span>&nbsp;<?php echo $lang->profil->bracelets->$lng; ?> (<?php if(isset($userdetails['brid'])) echo count($userdetails['brid']); else echo '0'; ?>)</p>
				<hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
					<div class="showcases" id="showcase_2">
<?php
		if(isset($userdetails['brid'])) {
			foreach($userdetails['picture_count'] as $key => $val) {
				$key_name = $statistics->brid2name($key);
				if($val['picid'] == NULL) $val['picid'] = 0;
?>
						<div class="previews">
<?php
				if($val['picid'] != 0) {
					if($user->login == $username) {
					
						$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid=:brid');
						$stmt->execute(array('picid' => $val['picid'], 'brid' => $key));
						$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
?>
							<a href="/armband?name=<?php echo urlencode($key_name); ?>" title="<?php echo urlencode($key); ?>">
									<img alt="latest pic" class="previewpic" src="/pictures/bracelets/thumb<?php echo '-'.$rowid['id'].'.jpg'; ?>"><br>
<?php	
					}else {
					
						$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid=:brid');
						$stmt->execute(array('picid' => $val['picid'], 'brid' => $key));
						$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
?>
							<a href="/armband?name=<?php echo urlencode($key_name); ?>">
									<img alt="latest pic" class="previewpic" src="/pictures/bracelets/thumb<?php echo '-'.$rowid['id'].'.jpg'; ?>"><br>
<?php
					}
				}elseif($user->login == $username) {
?>
							<a href="/login?postpic=<?php echo urlencode($key); ?>" title="<?php echo urlencode($key); ?>">
                                    <img alt="no picture available" class="previewpic" src="/img/no_pic2.png"><br>
<?php
				}else {
?>
							<a href="/armband?name=<?php echo urlencode($key_name); ?>">
                                    <img alt="no picture available" class="previewpic" src="/img/no_pic2.png"><br>
<?php
				}
?>

								<p class="preview_text">
									<?php echo htmlentities($key_name)."\n"; ?>
									<span style="float:right;"><?php echo $lang->profil->pictures->$lng; ?>: <?php echo $val['picid']; ?></span>
								</p>
							</a>
						</div>
<?php
			}
		}elseif($user->login == $username) echo '<p>'.$lang->profil->nobracelet->$lng.'.</p>';
		else echo '<p>'.$lang->profil->nobracelet_other->$lng.'.</p>';
?>
					</div>
	<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Abos ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
				<p class="tabs pseudo_link" id="tab_3"><span class="tab_arrow3 arrow_right"></span>&nbsp;<?php echo $lang->profil->abos->$lng; ?> (<?php echo count($userdetails['subscriptions']); ?>)</p>
				<hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
					<div class="showcases" id="showcase_3">
<?php
		if($userdetails['subscriptions'] != NULL) {
			foreach($userdetails['subscriptions'] as $key => $val) {
				$val['name'] = $statistics->brid2name($key);
				if(!isset($val['picid'])) $val['picid'] = 0;
?>
						<div class="previews">
							<a href="/armband?name=<?php echo urlencode($val['name']); ?>">
<?php
				if($val['picid'] != 0) {
				
					$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid=:brid');
					$stmt->execute(array('picid' => $val['picid'], 'brid' => $key));
					$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
?>
								<img alt="latest pic" class="previewpic" src="/pictures/bracelets/thumb<?php echo '-'.$rowid['id'].'.jpg'; ?>"><br>
<?php
				}else {
?>
                                <img alt="no picture available" class="previewpic" src="/img/no_pic2.png"><br>
<?php
				}
?>
								<p class="preview_text">
									<?php echo htmlentities($val['name']."\n"); ?>
									<span style="float:right;"><?php echo $lang->profil->pictures->$lng; ?>: <?php echo $val['picid']; ?></span>
								</p>
							</a>
							<?php if($user->login == $username) { ?><br><a href="/armband?name=<?php echo urlencode($val['name']).'&sub=false&sub_code='.urlencode(PassHash::hash($userdetails['email'])); ?>" class="preview_text"><?php echo $lang->profil->unsubscribe->$lng; ?></a><?php } ?>
						</div>
<?php
			}
		}elseif($user->login == $username) echo '<p>'.$lang->profil->noabos->$lng.'.</p>';
		else echo '<p>'.$lang->profil->noabos_other->$lng.'.</p>';
?>
					</div>
	<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Uploads ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->            
				<p class="tabs pseudo_link" id="tab_4"><span class="tab_arrow4 arrow_right"></span>&nbsp;<?php echo $lang->profil->uploads->$lng; ?> (<?php echo count($userdetails['pics']); ?>)</p>
				<hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
					<div class="showcases" id="showcase_4">
<?php
		if($userdetails['pics'] != NULL) {
			foreach($userdetails['pics'] as $key => $val) {
				$val['name'] = $statistics->brid2name($val['brid']);
				if($val['picid'] == NULL) $val['picid'] = 0;
?>
						<div class="previews">
<?php
				if($val['picid'] != 0) {
					
					$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid=:brid');
					$stmt->execute(array('picid' => $val['picid'], 'brid' => $val['brid']));
					$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
					
					if($user->login == $username) {
?>
							<a href="/armband?name=<?php echo urlencode($val['name']); ?>" title="<?php echo $val['brid']; ?>">
								<img alt="latest pic" class="previewpic" src="/pictures/bracelets/thumb<?php echo '-'.$rowid['id'].'.jpg'; ?>"><br>
<?php
					}else {
?>
							<a href="/armband?name=<?php echo urlencode($val['name']); ?>">
								<img alt="latest pic" class="previewpic" src="/pictures/bracelets/thumb<?php echo '-'.$rowid['id'].'.jpg'; ?>"><br>
<?php
					}
				}else { 
?>
							<a href="/armband?name=<?php echo urlencode($val['name']); ?>">
                                <img alt="no picture available" class="previewpic" src="/img/no_pic2.png"><br>
<?php
				}//Nicht wirklich nötig.
?>
								<p class="preview_text">
									<?php echo htmlentities($val['name'])."\n"; ?>
									<span style="float:right;"><?php echo $lang->profil->station->$lng; ?> <?php echo $val['picid']; ?> <?php echo $lang->profil->pictures->$lng; ?>: <?php echo $val['picCount']; ?></span>
								</p>
							</a>
						</div>
<?php
			}
		}elseif($user->login == $username) echo '<p>'.$lang->profil->noupload->$lng.'.</p>';
			else echo '<p>'.$lang->profil->noupload_other->$lng.'.</p>';
?>
					</div>
				</div>
<?php
}else {
?>
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->profil->notexists->$lng; ?></h1></div>
				<p><?php echo $lang->profil->this_notexists->$lng; ?>.</p>
<?php
}
?>
			</article>