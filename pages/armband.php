<?php
if (isset($braceName)) {
	$braceID = $statistics->name2brid($braceName);
}
foreach($_GET as $key => $val) {
	$_GET[$key] = clean_input($val);
}
if (isset($_POST['registerpic_submit'])) {
	$captcha_valid = captcha_valid($_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
	if($captcha_valid) {
		$pic_registered = $statistics->registerpic($braceID,
												$_POST['registerpic_description'],
												$_POST['registerpic_city'],
												$_POST['registerpic_country'],
												$_POST['registerpic_title'],
												$_FILES['registerpic_file'],
												$max_file_size);
	} else {
		header('Location: '.$friendly_self.'?name='.$braceName.'&registerpic=1&captcha=false
				&descr='.str_replace("\r\n", "ujhztg", $_POST['registerpic_description']).'
				&city='.$_POST['registerpic_city'].'
				&country='.$_POST['registerpic_country'].'
				&title='.$_POST['registerpic_title']);
	}
}
if(isset($_GET['captcha'])) {
	if($_GET['captcha'] == 'false') {
			$pic_registered = 'Das Captcha wurde falsch eingegeben.';	
	}
}
if (isset($pic_registered)) {
	if($pic_registered == 'Bild erfolgreich gepostet.') {
		header('Location: armband?name='.$braceName);
	}
	$js .=  'alert("'.$pic_registered.'");';
}
if ($braceName != NULL && isset($_GET['registerpic'])) {
	if($_GET['registerpic'] == 1) {
?>
				<article style="float: left; width: 100%;" class="mainarticles bottom_border_green">
					<div class="green_line mainarticleheaders line_header"><h1>Bild zu Armband <?php echo $braceName; ?> posten</h1></div>
					<form id="registerpic" name="registerpic" class="registerpic" action="<?php echo $friendly_self.'?name='.$braceName; ?>" method="post" enctype="multipart/form-data">
						<span style="font-family: Verdana, Times"><strong style="color: #000;">Bild</strong> posten</span><br><br>
                
						<label for="registerpic_title" class="label_registerpic_title">Titel:</label><br>
						<input type="text" name="registerpic_title" class="registerpic_title" size="20" maxlength="20" placeholder="Titel"  value="<?php if(isset($_GET['title'])) echo $_GET['title'];?>"required><br>
						
						<label for="registerpic_city" class="label_registerpic_city">Stadt:</label><br>
						<input type="text" name="registerpic_city" class="registerpic_city" size="20" maxlength="20" placeholder="Stadt" value="<?php if(isset($_GET['city'])) echo $_GET['city'];?>" required><br>
						
						<label for="registerpic_country" class="label_registerpic_country">Land:</label><br>
						<input type="text" name="registerpic_country" class="registerpic_country" size="20" maxlength="20" placeholder="Land" value="<?php if(isset($_GET['country'])) echo $_GET['country'];?>" required><br>
						
						<label for="registerpic_description" class="registerpic_description">Beschreibung:</label><br>
						<textarea name="registerpic_description" class="registerpic_description" rows="8" cols="40" maxlength="1000" required><?php if(isset($_GET['descr'])) echo str_replace("ujhztg", "\r\n", $_GET['descr']);?></textarea><br> 
                        
                        <input type="file" name="registerpic_file" id="registerpic_file" maxlength="<?php echo $max_file_size; ?>" required><br>
<?php
						$publickey = "6LfIVekSAAAAAJddojA4s0J4TVf8P_gS2v1zv09P";
						echo recaptcha_get_html($publickey);
?>
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>">
						<input type="submit" name="registerpic_submit" value="Bild posten"><br>
						<img id="image_preview" src="./img/placeholder.png" style="background-repeat: no-repeat;background-position: center;max-height:0px">
					</form>
				</article>
<?php
	}
} elseif ($braceName != NULL) {
	//Kommentare schreiben
	if (isset($_POST['comment_submit'])) {
		$write_comment = $statistics->write_comment ($braceID,
							 $_POST['comment_user'][$_POST['comment_form']],
							 $_POST['comment_content'][$_POST['comment_form']],
							 $_POST['comment_picid'][$_POST['comment_form']],
							 $user,
							 $db);
	}
	if (isset($write_comment)) {
		echo '<script type="text/javascript">
				//$(document).ready(function(){
					alert("'.$write_comment.'");
				//});
			  </script>';
	}
	
	$bracelet_stats = $statistics->bracelet_stats($braceID, $db);
	if (isset($bracelet_stats['owners'])) {
		$picture_details = $statistics->picture_details($braceID, $db);
		$stats = array_merge($bracelet_stats, $picture_details);
	} else {
		$bracelet_stats['owners'] = 0;
		$stats = $bracelet_stats;
	}
?>
			<article id="armband" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1>Armband <?php echo $braceName; ?></h1></div>
				<a href="<?php echo $friendly_self.'?name='.urlencode($braceName).'&amp;registerpic=1'; ?>" style="clear: both;">Ein neues Bild zu diesem Armband posten</a>
<?php
					for ($i = 0; $i < count($stats)-4; $i++) {
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
?>
                            <strong><?php echo $stats[$i][$j]['user']; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][$j]['date']).')'; ?>
                            <p><?php echo $stats[$i][$j]['comment']; ?></p> 
                            <hr style="border: 1px solid white;">  
<?php 
					}
?>   
						<form name="comment[<?php echo $i; ?>]" class="comment_form" action="<?php echo $friendly_self.'?name='.$braceName; ?>" method="post">
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
						if ($i < count($stats)-5) {
?>
<!--~~~HR~~~~--><hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;">
<?php	
						}
					}
?>
				<div class="pseudo_link" style="clear: both;" >Mehr anzeigen</div>
<?php
		if ($bracelet_stats['owners'] == 0 ) {
			echo '<p>Zu diesem Armband wurde noch kein Bild gepostet.</p>';
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
						<td><?php echo $stats[0]['city'].', '.$stats[0]['country']; ?></td>
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