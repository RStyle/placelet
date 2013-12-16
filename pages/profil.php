			<article id="profil" class="mainarticles bottom_border_green">
<?php
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
?>
							<li><a href="armband?name=<?php echo urlencode($statistics->brid2name($val['brid'])); ?>"><?php echo $statistics->brid2name($val['brid']); ?></a></li>
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
				if(!isset($armbaender['picture_count'][$armbaender['brid'][$i]]['picid'])) $armbaender['picture_count'][$armbaender['brid'][$i]]['picid'] = 0;
?>
						<tr>
							<td><a href="armband?name=<?php echo urlencode($statistics->brid2name($armbaender['brid'][$i])); ?>"><?php echo $statistics->brid2name($armbaender['brid'][$i]); ?></a></td>
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
                Armbänder: <?php echo count($userdetails['brid']); ?>, Uploads: <?php echo count($userdetails['pics']); ?></p>
            </div>
<?php
	if(isset($userdetails['brid'])) {
?>
				<div class="showcases">
                    <ul class="tabs">
                        <li class="pseudo_link">Armbänder</li>
                        <li class="pseudo_link">Abonnements</li>
                        <li class="pseudo_link">Uploads</li>
                    </ul>
<?php
		foreach($userdetails['picture_count'] as $key => $val) {
			if($val['picid'] == NULL) $val['picid'] = 0;
?>
                    <a class="previews" href="armband?name=<?php echo urlencode($statistics->brid2name($key)); ?>">
<?php
			if($val['picid'] != 0) {
?>
                        <img class="preview_pic" alt="latest pic" src="pictures/bracelets/thumb<?php echo '-'.$key.'-'.$val['picid'].'.jpg'; ?>"><br>
<?php
			}
?>
                        <p class="preview_text"><?php echo htmlentities($statistics->brid2name($key)); ?>
                        <span style="float:right;">Bilder: <?php echo $val['picid']; ?></span></p>                        
                    </a>
<?php
		}
?>
      
    			<!--	<table border="1">
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
			        </table>       -->
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