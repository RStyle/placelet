<?php
session_start(); //Session starten
include_once('../connection.php');
include_once('../functions.php');
include_once('../user.php');
if(isset($_SESSION['user'])){
	$user = new User($_SESSION['user'], $db);
	$checklogin = $user->logged;
}else{
	$user = new User(false, $db);
	$checklogin = false;
}
$statistics = new Statistics($db, $user);
$braceName = urldecode($_GET['braceName']);
$braceID = $statistics->name2brid($braceName);
if ($braceID != NULL) {
	$bracelet_stats = $statistics->bracelet_stats($braceID, $db);
	if (isset($bracelet_stats['owners'])) {
		$picture_details = $statistics->picture_details($braceID, $db);
		$stats = array_merge($bracelet_stats, $picture_details);
	} else {
		$bracelet_stats['owners'] = 0;
		$stats = $bracelet_stats;
	}
?>
<!--HR über dem 1. nachgeladenen Bild<hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;">-->
<?php
	for ($i = $_GET['q'] - 3; $i < $_GET['q']; $i++) {
		if(!isset($stats[$i])) break;
			if($i < $_GET['q']) {
?>
<!--~~~HR~~~~--><hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;">
<?php
	}
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
	}
}