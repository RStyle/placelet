<?php
if (isset($braceName)) {
	$braceID = $statistics->name2brid($braceName);
}
foreach($_GET as $key => $val) {
	$_GET[$key] = clean_input($val);
}
if ($braceName != NULL) {
	//Kommentare schreiben
	if (isset($_POST['comment_submit'])) {
		$write_comment = $statistics->write_comment ($braceID,
							 $_POST['comment_user'][$_POST['comment_form']],
							 $_POST['comment_content'][$_POST['comment_form']],
							 $_POST['comment_picid'][$_POST['comment_form']]);
	}
	if (isset($write_comment)) {
		$js .= 'alert("'.$write_comment.'");';
	}
	//Kommentar löschen
	if(isset($_GET['last_comment']) && isset($_GET['delete_comm']) && isset($_GET['commid']) && isset($_GET['picid']) && isset($_GET['name'])) {
		$comment_deleted = $statistics->manage_comment($user->admin, $_GET['last_comment'], $_GET['commid'], $_GET['picid'], $braceID);
		if($comment_deleted === true) {
			$js .= 'alert("Kommentar erfolgreich gelöscht.");';
		}elseif($comment_deleted == 2) {
			$js .= 'alert("Kommentar gemeldet.");';
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
					<input name="sub_email" type="text" maxlength="30" placeholder="E-Mail" class="float_right sub_inputs" style="display: none;" required>
					<input type="hidden" name="sub" value="true">
					<input type="hidden" name="name" value="<?php echo urlencode($braceName); ?>">
				</form>
<?php
					for ($i = 0; $i < count($stats) - 4 && $i < 3; $i++) {
?>
				<div style="width: 100%; overflow: auto;">
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
					for ($j = 1; $j <= count($stats[$i])-8; $j++) {
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
							<a href="armband?name=<?php echo urlencode($braceName); ?>&last_comment=<?php echo $last_comment; ?>&commid=<?php echo $stats[$i][$j]['commid']; ?>&picid=<?php echo $stats[$i][$j]['picid']; ?>&delete_comm=true" class="delete_button float_right">X</a>
                            <strong><?php echo $stats[$i][$j]['user']; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][$j]['date']).')'; ?>
                            <p><?php echo $stats[$i][$j]['comment']; ?></p> 
                            <hr style="border: 1px solid white;">  
<?php 
					}
?>   
						<form name="comment[<?php echo $i; ?>]" class="comment_form" action="armband?name=<?php echo urlencode($braceName); ?>" method="post">
							<span style="font-family: Verdana, Times"><strong style="color: #000;">Kommentar</strong> schreiben</span><br><br>
							<label for="comment_user[<?php echo $i; ?>]" class="label_comment_user">Name: </label>
							<input type="text" name="comment_user[<?php echo $i; ?>]" class="comment_user" size="20" maxlength="15"<?php if (isset($user->login)){echo ' value="'.$user->login.'" ';} ?>placeholder="Name" required><br>  
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
						<td><strong>Armband Name</strong></td>
						<td><strong><?php echo $stats['name']; ?></strong></td>
					</tr>
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