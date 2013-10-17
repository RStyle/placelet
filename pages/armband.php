<?php
$max_file_size = 100000;
if (isset($_POST['registerpic_submit'])) {
	$pic_registered = $user->registerpic($_POST['registerpic_brid'],
										 $_POST['registerpic_description'],
										 $_POST['registerpic_city'],
										 $_POST['registerpic_country'],
										 $_POST['registerpic_title'],
										 $_FILES['registerpic_file'],
										 $max_file_size);
}
if (isset($pic_registered)) {
	echo '<script type="text/javascript">
			//$(document).ready(function(){
				alert("'.$pic_registered.'");
			//});
		  </script>';
}
if (isset($_GET['id']) && isset($_GET['registerpic']) && isset($_SESSION['user'])) {
	if($_GET['registerpic'] == 1) {
?>
				<article id="armband" class="mainarticles bottom_border_green">
					<div class="green_line mainarticleheaders line_header"><h1>Bild zu Armband <?php echo $braceID; ?> posten</h1></div>
					<form name="registerpic" class="registerpic" action="<?php echo $friendly_self.'?id='.$_GET['id']; ?>" method="post" enctype="multipart/form-data">
						<span style="font-family: Verdana, Times"><strong style="color: #000;">Bild</strong> posten</span><br><br>
                        
                        <label for="registerpic_title" class="label_registerpic_title">Titel:</label><br>
						<input type="text" name="registerpic_title" class="registerpic_title" size="20" maxlength="20" placeholder="Titel" required><br>
                        
                        <label for="registerpic_city" class="label_registerpic_city">Stadt:</label><br>
						<input type="text" name="registerpic_city" class="registerpic_city" size="20" maxlength="20" placeholder="Stadt" required><br>
                        
                        <label for="registerpic_country" class="label_registerpic_country">Land:</label><br>
						<input type="text" name="registerpic_country" class="registerpic_country" size="20" maxlength="20" placeholder="Land" required><br>
                        
                        <label for="registerpic_description" class="registerpic_description">Beschreibung:</label><br>
                        <textarea name="registerpic_description" class="registerpic_description" rows="8" cols="40" maxlength="1000" required></textarea><br> 
                        
                        <input type="file" name="registerpic_file" maxlength="$max_file_size" required><br><br>
                        <input type="hidden" name="MAX_FILE_SIZE" value="$max_file_size">
						<input type="hidden" name="registerpic_brid" value="<?php echo $_GET['id'];?>">
						<input type="submit" name="registerpic_submit" value="Bild posten">
					</form>
				</article>
<?php
	}
} elseif (isset($_GET['id']) && isset($_GET['registerpic']) && !isset($_SESSION['user'])) {
?>
				<article id="armband" class="mainarticles bottom_border_green">
					<div class="green_line mainarticleheaders line_header"><h1>Nicht eingeloggt</h1></div>
                    <p>Bilder kannst du nur posten, wenn du eingeloggt bist.</p>
				</article>	
<?php
} elseif (isset($_GET['id'])) {
	//Kommentare schreiben
	if (isset($_POST['comment_submit'])) {
		$write_comment = User::write_comment ($_GET['id'],
							 $_POST['comment_user'][$_POST['comment_form']],
							 $_POST['comment_content'][$_POST['comment_form']],
							 $_POST['comment_picid'][$_POST['comment_form']],
							 $db,
							 $user);
	}
	if (isset($write_comment)) {
		echo '<script type="text/javascript">
				//$(document).ready(function(){
					alert("'.$write_comment.'");
				//});
			  </script>';
	}
	
	$stats = array_merge($user->bracelet_stats($_GET['id']), $user->picture_details($_GET['id']));
?>
			<article id="armband" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1>Armband <?php echo $braceID; ?></h1></div>
				<div style="float: left;">
<?php
					for ($i = 0; $i < count($stats)-3; $i++) {
?>
                    <div>
                        <h3><?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?></h3>
                        <a href="pictures/bracelets/pic<?php echo '-'.$_GET['id'].'-'.$stats[$i]['picid'].'.'.$stats[$i]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?>">
                            <img src="pictures/bracelets/pic<?php echo '-'.$_GET['id'].'-'.$stats[$i]['picid'].'.'.$stats[$i]['fileext']; ?>" alt="<?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?>" style="width: 40%; float: left; margin-right: 1em; margin-bottom: 1em;">
                        </a>
                        <?php echo date('d.m.Y', $stats[$i]['date']); ?>
                        <h4><?php echo $stats[$i]['title']; ?></h4>
                        <p><?php echo $stats[$i]['description']; ?></p>
                        <span class="toggle_comments pseudo_link" id="toggle_comment<?php echo $i;?>">Kommentare zeigen</span>
                    </div>
                    <div class="comments" id="comment<?php echo $i;?>">
<?php
					for ($j = 1; $j <= count($stats[$i])-8; $j++) {
?>
						<strong><?php echo $stats[$i][$j]['user']; ?></strong>, <?php echo 'vor x Tagen ('.date('H:i d.m.Y', $stats[$i][$j]['date']).')'; ?>
						<p><?php echo $stats[$i][$j]['comment']; ?></p> 
						<hr style="border: 1px solid white;">  
<?php 
					}
?>   
                        <form name="comment[<?php echo $i; ?>]" class="comment_form" action="<?php echo $friendly_self.'?id='.$_GET['id']; ?>" method="post">
                            <span style="font-family: Verdana, Times"><strong style="color: #000;">Kommentar</strong> schreiben</span><br><br>
                            <label for="comment_user[<?php echo $i; ?>]" class="label_comment_user">Name: </label>
                            <input type="text" name="comment_user[<?php echo $i; ?>]" class="comment_user" size="20" maxlength="15"<?php if (isset($user->login)){echo ' value="'.$user->login.'" ';} ?>placeholder="Name" required><br>  
                            <label for="comment_content[<?php echo $i; ?>]" class="label_comment_content">Dein Kommentar:</label><br>
                            <textarea name="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
                            <input type="hidden" name="comment_brid[<?php echo $i; ?>]" value="<?php echo $_GET['id'];?>">
                            <input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i]['picid']; ?>">
                            <input type="hidden" name="comment_form" value="<?php echo $i; ?>">
                            <input type="submit" name="comment_submit[<?php echo $i; ?>]" value="Kommentar abschicken" class="submit_comment">
                        </form>
                    </div>
<?php
						if ($i < count($stats)-4) {
?><!----HR----><hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;"><?php	
						}
					}
?>
				</div>
				<a href="<?php echo $friendly_self.'?id='.$_GET['id'].'&registerpic=1'; ?>">Ein neues Bild zu diesem Armband posten</a>
			</article>
			<aside class="side_container" id="bracelet_props">
				<h1>Statistik</h1>
				<table style="width: 100%;">
					<tr>
						<td><strong>Armband ID</strong></td>
						<td><strong><?php echo $braceID; ?></strong></td>
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
					<tr>
						<td>Letzter Ort</td>
						<td>Sydney, Australia</td>
					</tr>
				</table>
			</aside>
<?php
} else {
?>
				<article id="armband" class="mainarticles bottom_border_green">
					<div class="green_line mainarticleheaders line_header"><h1>Falsche Seite</h1></div>
                    <p>Du solltest nicht hier sein. Gehe einfach eine Seite zurück.</p>
				</article>
<?php
}
?>