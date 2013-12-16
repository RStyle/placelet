<?php
if (isset($braceName)) {
	$braceID = $statistics->name2brid($braceName);
}
foreach($_GET as $key => $val) {
	$_GET[$key] = clean_input($val);
}
if ($braceName != NULL) {
	//Kommentare schreiben
	if(isset($_POST['comment_submit'])) {
		$write_comment = $statistics->write_comment ($braceID,
							 $_POST['comment_user'][$_POST['comment_form']],
							 $_POST['comment_content'][$_POST['comment_form']],
							 $_POST['comment_picid'][$_POST['comment_form']]);
	}
	if(isset($write_comment)) {
		$js .= 'alert("'.$write_comment.'");';
	}
	//Kommentar löschen
	if(isset($_GET['last_comment']) && isset($_GET['delete_comm']) && isset($_GET['commid']) && isset($_GET['picid']) && isset($_GET['name'])) {
		$comment_deleted = $statistics->manage_comment($user->admin, $_GET['last_comment'], $_GET['commid'], $_GET['picid'], $braceID);
		if($comment_deleted === true ) {
			header('Location: armband?name='.urlencode($braceName).'&comment_deleted=true');
		}elseif($comment_deleted == 2) {
			$js .= 'alert("Kommentar gemeldet.");';
		}
		if($_GET['comment_deleted'] == 'true') {
			$js .= 'alert("Kommentar erfolgreich gelöscht.");';	
		}
	}
	//Bild löschen
	if(isset($_GET['last_pic']) && isset($_GET['delete_pic']) && isset($_GET['picid']) && isset($_GET['name'])) {
		$pic_deleted = $statistics->manage_pic($user->admin, $_GET['last_pic'], $_GET['picid'], $braceID);
		if($pic_deleted === true ) {
			header('Location: armband?name='.urlencode($braceName).'&pic_deleted=true');
		}elseif($pic_deleted == 2) {
			$js .= 'alert("Bild gemeldet.");';
		}elseif ($pic_deleted == false) {
			$js .= 'Es ist ein Fehler beim Löschen des Bildes aufgetreten<br>Bitte informiere den Support.';
		}else echo $pic_deleted;
	}
	if(isset($_GET['pic_deleted'])) {
		if($_GET['pic_deleted'] == 'true') {
			$js .= 'alert("Bild erfolgreich gelöscht.");';	
		}
	}
	//Armband Name ändern
	$owner = false;
	if(isset($_POST['edit_submit'])) {
		$change_name = $user->edit_br_name($braceID, $_POST['edit_name']);
		if($change_name == 1) {
			header('Location: armband?name='.urlencode($_POST['edit_name']).'&name_edited='.$change_name);
		}elseif($change_name == 2) {
			$js .= 'alert("Es gibt schon ein Armband mit diesem Namen.");';
		}
	}
	if(isset($_GET['name_edited'])) {
		$js .= 'alert("Name erfolgreich geändert.");';
	}
	if($user->login) {
		//Überprüfen, ob man das Armband gekauft hat.
		$userdetails = $statistics->userdetails($user->login);
		$armbaender = profile_stats($userdetails);
		if($armbaender['brid'] != NULL) {
			if(in_array($braceID, $armbaender['brid'])) {
				$owner = true;
			}
		}
	}
	$bracelet_stats = $statistics->bracelet_stats($braceID, $db);
	if (isset($bracelet_stats['owners'])) {
		$picture_details = $statistics->picture_details($braceID, $db);
		$stats = array_merge($bracelet_stats, $picture_details);
	} else {
		$bracelet_stats['owners'] = 0;
		$stats = $bracelet_stats;
	}
	if(isset($_GET['sub']) && isset($_GET['sub_email'])) {
		$sub_added = $statistics->manage_subscription($_GET['sub'], $braceID, $_GET['sub_email']);
		if(isset($sub_added)) {
			if($sub_added === true) $js .= 'alert("Abonnement erfolgreich hinzugefügt.");';
				elseif($sub_added == 2) $js .= 'alert("Dieses Armband wurde schon mit der eingegebenen E-Mail abonniert.");';
				elseif($sub_added == 3) $js .= 'alert("Dieses Armband wurde nicht mit dieser E-Mail abonniert.");';
				elseif($sub_added === false) $js .= 'alert("Du hast das Abonnement erfolgreich beendet.");';
		}
	}
?>
			<article id="armband" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1>Armband <?php echo $braceName; ?></h1></div>
				<span class="pseudo_link float_right" id="show_sub">Armband abbonieren</span>
				<a href="<?php echo 'login?postpic=true'; ?>">Ein neues Bild zu diesem Armband posten</a>
				<form method="get" action="armband">
					<input type="submit" name="sub_submit" value="Abonnieren" class="float_right sub_inputs" style="display: none;">
					<input name="sub_email" type="email"  size="20" maxlength="254" placeholder="E-Mail Adresse" class="float_right sub_inputs" style="display: none;" required>
					<input type="hidden" name="sub" value="true">
					<input type="hidden" name="name" value="<?php echo urlencode($braceName); ?>">
				</form>
<?php
					for ($i = 0; $i < count($stats) - 4 && $i < 3; $i++) {
						if($i == 0) $last_pic = 'last';
							else $last_pic = 'middle';
?>
				<div style="width: 100%; overflow: auto;">
				<a href="armband?name=<?php echo urlencode($braceName); ?>&picid=<?php echo $stats[$i]['picid']; ?>&last_pic=<?php echo $last_pic; ?>&delete_pic=true" class="delete_button float_right" style="margin-top: 2em;" title="Bild löschen/melden" onclick="return confirmDelete('das Bild');">X</a>
					<h3><?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?></h3>
					<a href="pictures/bracelets/pic<?php echo '-'.$braceID.'-'.$stats[$i]['picid'].'.'.$stats[$i]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?>" class="thumb_link">
						<img src="img/triangle.png" alt="" class="thumb_triangle">
						<img src="pictures/bracelets/thumb<?php echo '-'.$braceID.'-'.$stats[$i]['picid'].'.jpg'; ?>" alt="<?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?>" class="thumbnail">
					</a>
					<table class="pic-info">
						<tr>
							<th>Datum</th>
							<td><?php echo date('d.m.Y H:i', $stats[$i]['date']); ?> Uhr</td>
						</tr>
						<tr>
							<th>Ort</th>
							<td><?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?></td>
						</tr>
<?php
				if($stats[$i]['user'] != NULL) {
?>
						<tr>
							<th>Uploader</th>
							<td><?php echo $stats[$i]['user']; ?></td>
						</tr>
<?php
                 }
?>
					</table>
						
					<p class="pic-desc">
						<span class="desc-header"><?php echo $stats[$i]['title']; ?></span><br>
						<?php echo $stats[$i]['description']; ?>      
						<br><br>
						<span class="pseudo_link toggle_comments" id="toggle_comment<?php echo $i;?>">Kommentare zeigen</span>
					</p>
                    
					<div class="comments" id="comment<?php echo $i;?>">
<?php
					for ($j = 1; $j <= count($stats[$i])-11; $j++) {
					//Vergangene Zeit seit dem Kommentar berechnen
					$x_days_ago = ceil((strtotime("00:00") - $stats[$i][$j]['date']) / 86400);
					switch($x_days_ago) {
						case 0:
							$x_days_ago = 'heute';
							break;
						case 1:
							$x_days_ago = 'gestern';
							break;
						default:
							$x_days_ago = 'vor '.$x_days_ago.' Tagen';
					}
					//Überprüfen, ob das Kommentar, was man löschen will das letzte ist.
					if(isset($stats[$i][$j + 1]['commid'])) {
						$last_comment = 'middle';
					}else {
						$last_comment = 'last';
					}
?>
							<a href="armband?name=<?php echo urlencode($braceName); ?>&last_comment=<?php echo $last_comment; ?>&commid=<?php echo $stats[$i][$j]['commid']; ?>&picid=<?php echo $stats[$i][$j]['picid']; ?>&delete_comm=true" class="delete_button float_right" title="Kommentar löschen/melden" onclick="return confirmDelete('den Kommentar');">X</a>
                            <strong><?php echo $stats[$i][$j]['user']; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][$j]['date']).')'; ?>
                            <p><?php echo $stats[$i][$j]['comment']; ?></p> 
                            <hr style="border: 1px solid white;">  
<?php 
					}
?>   
						<form name="comment[<?php echo $i; ?>]" class="comment_form" action="armband?name=<?php echo urlencode($braceName); ?>" method="post">
							<span style="font-family: Verdana, Times"><strong style="color: #000;">Kommentar</strong> schreiben</span><br><br>
							<label for="comment_user[<?php echo $i; ?>]" class="label_comment_user">Name: </label>
							<input type="text" name="comment_user[<?php echo $i; ?>]" class="comment_user" size="20" maxlength="15" <?php if (isset($user->login))echo 'value="'.$user->login.'" '; ?>placeholder="Name" pattern=".{4,15}" title="Min.4 - Max.15" required><br>  
							<label for="comment_content[<?php echo $i; ?>]" class="label_comment_content">Dein Kommentar:</label><br>
							<textarea name="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
							<input type="hidden" name="comment_brid[<?php echo $i; ?>]" value="<?php echo $braceID;?>">
							<input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i]['picid']; ?>">
							<input type="hidden" name="comment_form" value="<?php echo $i; ?>">
							<input type="submit" name="comment_submit[<?php echo $i; ?>]" value="Kommentar abschicken" class="submit_comment">
						</form>
					</div>
				</div>
<?php
						if ($i < count($stats) - 5 && $i < 2) {
?>
<!--~~~HR~~~~--><hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;">
<?php	
						}
					}
?>
<?php
		if ($bracelet_stats['owners'] == 0 ) {
			echo '<p>Zu diesem Armband wurde noch kein Bild gepostet.</p>';
		}else {
?>
			<div class="pseudo_link" id="armband_reload" onClick="reload_armband('<?php echo urlencode($braceName); ?>');"  style="clear: both;" >Mehr anzeigen</div>
<?php
		}
?>
			</article>
			<aside class="side_container" id="bracelet_props">
				<h1>Statistik</h1>
				<table style="width: 100%;">
					<tr>
						<th>Name</th>
						<td><strong><?php echo $stats['name']; if($owner) {?> </strong> <img src="img/edit.png" id="edit_name" class="pseudo_link"></td><?php } ?>
					</tr>
<?php
		if($owner) {
?> 
					<form method="post" action="armband?name=<?php echo urlencode($braceName); ?>">
						<tr>
							<td><input type="text" name="edit_name" placeholder="Neuer Name" class="name_inputs" style="display: none;" size="20" maxlength="18" pattern=".{4,18}" title="Min.4 - Max.18" required></td>
							<td><input type="submit" value="Ändern" class="name_inputs" name="edit_submit" style="display: none;"></td>
						</tr>
					</form>
<?php
		}
?>
					<tr>
						<td>Käufer</td>
						<td><?php echo $stats['owner']; ?></td>
					</tr>
					<tr>
						<td>Registriert am</td>
						<td><?php echo date('d.m.Y', $stats['date']); ?></td>
					</tr>
					<tr>
						<td>Anzahl Besitzer</td>
						<td><?php echo $stats['owners']; ?></td>
					</tr>
<?php
		if ($bracelet_stats['owners'] != 0 ) {
?>
					<tr>
						<td>Letzter Ort</td>
						<td><?php echo $stats[0]['city']; ?>,</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><?php echo $stats[0]['country']; ?></td>
					</tr>
<?php
		}
?>
				</table>
			</aside>
<?php
} else {
?>
			<article id="armband" class="mainarticles bottom_border_green" style="width: 100%;">
				<div class="green_line mainarticleheaders line_header"><h1>Falsche Seite</h1></div>
				<p>Du solltest nicht hier sein. Gehe einfach eine Seite zurück.</p>
			</article>
<?php
}
?>