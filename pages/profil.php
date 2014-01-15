			<article id="profil" class="mainarticles bottom_border_green">
<?php
if(!isset($_GET['user']) && !$user->login) {
?>
								<div class="green_line mainarticleheaders line_header"><h1>Profil</h1></div>
                                <div style="float: left; margin-right: 2em;">
                                        Dein Profil kann nur angezeigt werden, wenn du eingeloggt bist.<br>
                                        Bitte logge dich ein:
                                        <form name="login" action="profil" method="post">
                                                <table style="border: 1px solid black">
                                                        <tr>
                                                                <td><label for="profile_login">Benutzername&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
                                                                <td><input type="text" name="login" id="profile_login" size="20" maxlength="15" placeholder="Benutzername" pattern=".{4,15}" title="Min.4 - Max.15" required></td>
                                                        </tr>
                                                        <tr>
                                                                <td><label for="profile_password">Passwort</label></td>
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
                                        Oder suche nach dem Profil von jemand anderem:
                                        <form action="profil" method="get">
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
}elseif($user->login || Statistics::userexists($username)) {
?>
				<div class="green_line mainarticleheaders line_header"><h1><?php if($user->login == $username) echo 'Dein Profil, '.htmlentities($user->login); else echo 'Profil von '.htmlentities($username); ?></h1></div>
				<div class="user_info">
					<img class="profile_pic" src="img/profil_pic_small.png" alt="Profilbild">           
					<h1><?php echo $userdetails['user']; ?></h1>
					<p>
						Registriert seit: <?php echo date('d.m.Y', $userdetails['registered']); ?><br>
						Status: <?php if($userdetails['status'] == 2) echo 'Admin'; else echo 'User'; ?>
					</p>
				</div>
<?php 
    if($user->login == $username) {
?>        
                <div class="logged_info">
                    <p>
						Deine E-Mail-Adresse: <?php echo $userdetails['email']; ?>
						<ul class="list_style_none" style="padding: 0;">
							<li><a href="account?details">Accountdetails ändern</a></li>
							<li><a href="account?notifications">Benachrichtigungseinstellungen</a></li>
							<li><a href="account?privacy">Privatsphäreeinstellungen</a></li>
						</ul>
					</p>
                </div>      
<?php        
    }
?>
	            <div style="clear: both;">
<?php
	if($user->login == $username) {
?>
	<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Benachrichtigungen ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
				<p class="tabs pseudo_link" id="tab_4"><span class="tab_arrow4 arrow_down"></span>&nbsp;Benachrichtigungen (<?php if(isset($userdetails['brid'])){ echo count($userdetails['brid']); } else { echo '0';} ?>)</p>
				<hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
					<div class="showcases" id="showcase_4" style="display: none;">
					
					</div>
<?php
	}
?>					
	<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Armbänder ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
				<p class="tabs pseudo_link" id="tab_1"><span class="tab_arrow1 arrow_down"></span>&nbsp;Armbänder (<?php if(isset($userdetails['brid'])){ echo count($userdetails['brid']); } else { echo '0';} ?>)</p>
				<hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
					<div class="showcases" id="showcase_1">
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
?>
							<a href="armband?name=<?php echo urlencode($key_name); ?>" title="<?php echo urlencode($key); ?>">
									<img alt="latest pic" class="previewpic" src="pictures/bracelets/thumb<?php echo '-'.$key.'-'.$val['picid'].'.jpg'; ?>"><br>
<?php	
					}else {
?>
							<a href="armband?name=<?php echo urlencode($key_name); ?>">
									<img alt="latest pic" class="previewpic" src="pictures/bracelets/thumb<?php echo '-'.$key.'-'.$val['picid'].'.jpg'; ?>"><br>
<?php
					}
				}elseif($user->login == $username) {
?>
							<a href="login?postpic=<?php echo urlencode($key); ?>" title="<?php echo urlencode($key); ?>">
                                    <img alt="no picture available" class="previewpic" src="img/no_pic2.png"><br>
<?php
				}else {
?>
							<a href="armband?name=<?php echo urlencode($key_name); ?>">
                                    <img alt="no picture available" class="previewpic" src="img/no_pic2.png"><br>
<?php
				}
?>

								<p class="preview_text">
									<?php echo htmlentities($key_name)."\n"; ?>
									<span style="float:right;">Bilder: <?php echo $val['picid']; ?></span>
								</p>
							</a>
						</div>
<?php
			}
		}elseif($user->login == $username) echo 'Du besitzt noch kein Armband.';
		else echo 'Dieser Benutzer besitzt noch kein Armband.';
?>
					</div>
	<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Abos ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
				<p class="tabs pseudo_link" id="tab_2"><span class="tab_arrow2 arrow_right"></span>&nbsp;Abonnements (<?php echo count($userdetails['subscriptions']); ?>)</p>
				<hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
					<div class="showcases" id="showcase_2">
<?php
		if($userdetails['subscriptions'] != NULL) {
			foreach($userdetails['subscriptions'] as $key => $val) {
				$val['name'] = $statistics->brid2name($key);
				if(!isset($val['picid'])) $val['picid'] = 0;
?>
						<div class="previews">
							<a href="armband?name=<?php echo urlencode($val['name']); ?>">
<?php
				if($val['picid'] != 0) {
?>
								<img alt="latest pic" class="previewpic" src="pictures/bracelets/thumb<?php echo '-'.$key.'-'.$val['picid'].'.jpg'; ?>"><br>
<?php
				}else {
?>
                                <img alt="no picture available" class="previewpic" src="img/no_pic2.png"><br>
<?php
				}
?>
								<p class="preview_text">
									<?php echo htmlentities($val['name']."\n"); ?>
									<span style="float:right;">Bilder: <?php echo $val['picid']; ?></span>
								</p>
							</a>
							<?php if($user->login == $username) {?><br><a href="armband?name=<?php echo urlencode($val['name']).'&sub=false&sub_code='.urlencode(PassHash::hash($userdetails['email'])); ?>" class="preview_text">Deabonnieren</a><?php } ?>
						</div>
<?php
			}
		}elseif($user->login == $username) echo 'Du hast noch kein Armband abonniert.';
		else echo 'Dieser Benutzer hat noch kein Armband abonniert.';//Entfernen, wenn es nicht gewünscht ist fremde Abos anzuzeigen
?>
					</div>
	<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Uploads ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->            
				<p class="tabs pseudo_link" id="tab_3"><span class="tab_arrow3 arrow_right"></span>&nbsp;Uploads (<?php echo count($userdetails['pics']); ?>)</p>
				<hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
					<div class="showcases" id="showcase_3">
<?php
		if($userdetails['pics'] != NULL) {
			foreach($userdetails['pics'] as $key => $val) {
				$val['name'] = $statistics->brid2name($val['brid']);
				if($val['picid'] == NULL) $val['picid'] = 0;
?>
						<div class="previews">
<?php
				if($val['picid'] != 0) {
					if($user->login == $username) {
?>
							<a href="armband?name=<?php echo urlencode($val['name']); ?>" title="<?php echo $val['brid']; ?>">
								<img alt="latest pic" class="previewpic" src="pictures/bracelets/thumb<?php echo '-'.$val['brid'].'-'.$val['picid'].'.jpg'; ?>"><br>
<?php
					}else {
?>
							<a href="armband?name=<?php echo urlencode($val['name']); ?>">
								<img alt="latest pic" class="previewpic" src="pictures/bracelets/thumb<?php echo '-'.$val['brid'].'-'.$val['picid'].'.jpg'; ?>"><br>
<?php
					}
				}else { 
?>
							<a href="armband?name=<?php echo urlencode($val['name']); ?>">
                                <img alt="no picture available" class="previewpic" src="img/no_pic2.png"><br>
<?php
				}//Nicht wirklich nötig.
?>
								<p class="preview_text">
									<?php echo htmlentities($val['name'])."\n"; ?>
									<span style="float:right;">Station Nr.: <?php echo $val['picid']; ?> Bilder: <?php echo $val['picCount']; ?></span>
								</p>
							</a>
						</div>
<?php
			}
		}elseif($user->login == $username) echo 'Du hast noch kein Bild hochgeladen';
		else echo 'Dieser Benutzer hat noch kein Bild hochgeladen.';
?>
					</div>
				</div>
<?php
}else {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Benutzer existiert nicht</h1></div>
				<p>Dieser Benutzer existiert nicht.</p>
<?php
}

?>
			</article>