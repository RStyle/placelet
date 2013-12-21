			<article id="profil" class="mainarticles bottom_border_green">
<?php
if(!isset($_GET['user'])) {
	if ($user->login) {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Dein Profil, <?php echo htmlentities($user->login); ?></h1></div>
				<div style="float: left; margin-right: 2em;">
					Dein Account:
					<table border="0">
						<tr>
							<th>Benutzername:</th>
							<td><?php echo $userdetails['user']; ?></td>
						</tr>
						<tr>
							<th>E-Mail Adresse</th>
							<td><?php echo $userdetails['email']; ?></td>
						</tr>
<?php
		if($userdetails['status'] == 2) {
?>
						<tr>
							<th>Status</th>
							<td>Admin</td>
						</tr>
<?php
		}
?>
					</table>
					<p><a href="account">Accountdetails ändern</a></p>
					Abonnierte Armbänder
					<ul>
<?php
		foreach($userdetails['subscriptions'] as $key => $val) {
			$val['name'] = $statistics->brid2name($val['brid']);
?>
							<li><a href="armband?name=<?php echo urlencode($val['name']); ?>"><?php echo htmlentities($val['name']); ?></a></li>
<?php
		}
?>
					</ul>
				</div>
				<div style="float: left;">
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
				$armbaender['name'][$i] = $statistics->brid2name($armbaender['brid'][$i]);
				if(!isset($armbaender['picture_count'][$armbaender['brid'][$i]]['picid'])) $armbaender['picture_count'][$armbaender['brid'][$i]]['picid'] = 0;
?>
						<tr>
							<td><a href="armband?name=<?php echo urlencode($armbaender['name'][$i]); ?>"><?php echo $armbaender['name'][$i]; ?></a></td>
							<td><?php echo $armbaender['brid'][$i]; ?></a></td>
							<td><?php echo date('d.m.Y', $armbaender['date'][$i]); ?></td>
							<td><?php echo $armbaender['picture_count'][$armbaender['brid'][$i]]['picid']; ?></td>
<?php
					if($armbaender['picture_count'][$armbaender['brid'][$i]]['picid'] == 0) {
?>
							<td><a href="login?postpic=<?php echo $armbaender['brid'][$i]; ?>">Bild posten</a></td>
<?php
					}
?>
						</tr>
<?php
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
	}
} elseif(Statistics::userexists($username)){
?>
            <div class="green_line mainarticleheaders line_header"><h1>Profil von <?php echo htmlentities($username); ?></h1></div>
            <div class="user_info">
                <img class="profile_pic" src="img/profil_pic_small.png">           
                <h1><?php echo $userdetails['user']; ?></h1>
                <p>Registriert seit: <?php echo date('H:i d.m.Y', $userdetails['registered']); ?><br>
                Armbänder: <?php if (isset($userdetails['brid'])){ echo count($userdetails['brid']); } else { echo '0';} ?>, Uploads: <?php echo count($userdetails['pics']); ?></p>
            </div>

<!-- ------------------------------------------ Armbänder ---------------------------------------------------- -->
            <p class="tabs pseudo_link" id="tab_1"><strong class="arrow_down"></strong>&nbsp;Armbänder</p>
            <hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
				<div class="showcases" id="showcase_1">
<?php
    if(isset($userdetails['brid'])) {
		foreach($userdetails['picture_count'] as $key => $val) {
			$key_name = $statistics->brid2name($key);
			if($val['picid'] == NULL) $val['picid'] = 0;
?>
                    <a class="previews" href="armband?name=<?php echo urlencode($key_name); ?>">
<?php
			if($val['picid'] != 0) {
?>
                        <img alt="latest pic" src="pictures/bracelets/thumb<?php echo '-'.$key.'-'.$val['picid'].'.jpg'; ?>"><br>
<?php
			}
?>
                        <p class="preview_text"><?php echo htmlentities($key_name); ?>
                        <span style="float:right;">Bilder: <?php echo $val['picid']; ?></span></p>                        
                    </a>
<?php
		}
	} else echo 'Dieser Benutzer besitzt noch kein Armband.'
?>
			    </div>
<!-- ------------------------------------------ Abos ---------------------------------------------------- -->
            <p class="tabs pseudo_link" id="tab_2"><strong class="arrow_right"></strong>&nbsp;Abonnements</p>
            <hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
				<div class="showcases" id="showcase_2">
<?php
    if($userdetails['subscriptions']!=NULL) {
		foreach($userdetails['picture_count'] as $key => $val) {
			$key_name = $statistics->brid2name($key);
			if($val['picid'] == NULL) $val['picid'] = 0;
?>
                    <a class="previews" href="armband?name=<?php echo urlencode($key_name); ?>">
<?php
			if($val['picid'] != 0) {
?>
                        <img alt="latest pic" src="pictures/bracelets/thumb<?php echo '-'.$key.'-'.$val['picid'].'.jpg'; ?>"><br>
<?php
			}
?>
                        <p class="preview_text"><?php echo htmlentities($key_name); ?>
                        <span style="float:right;">Bilder: <?php echo $val['picid']; ?></span></p>
                    </a>
<?php
		}
	} else echo 'Dieser Benutzer hat noch kein Armband abonniert.';
?>
			    </div>
<!-- ------------------------------------------ Uploads ---------------------------------------------------- -->            
            <p class="tabs pseudo_link" id="tab_3"><strong class="arrow_right"></strong>&nbsp;Uploads</p>
            <hr style="margin-top: 0; height: 3px; background-color: #ddd; border: none;">
				<div class="showcases" id="showcase_3">
<?php
    if ($userdetails['pics'] != NULL) { 
		foreach($userdetails['picture_count'] as $key => $val) {
			$key_name = $statistics->brid2name($key);
			if($val['picid'] == NULL) $val['picid'] = 0;
?>
                    <a class="previews" href="armband?name=<?php echo urlencode($key_name); ?>">
<?php
			if($val['picid'] != 0) {
?>
                        <img alt="latest pic" src="pictures/bracelets/thumb<?php echo '-'.$key.'-'.$val['picid'].'.jpg'; ?>"><br>
<?php
			}
?>
                        <p class="preview_text"><?php echo htmlentities($key_name); ?>
                        <span style="float:right;">Bilder: <?php echo $val['picid']; ?></span></p>
                    </a>
<?php
		}
	} else echo 'Dieser Benutzer hat noch kein Bild hochgeladen.';
?>
			    </div>    
<?php
} else {
?>
				<div class="green_line mainarticleheaders line_header"><h1>Benutzer existiert nicht</h1></div>
				<p>Dieser Benutzer existiert nicht.</p>
<?php
}

?>
			</article>