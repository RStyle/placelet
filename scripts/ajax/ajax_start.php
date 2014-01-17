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
$systemStats = $statistics->systemStats(3, $_GET['q']);
//hier werden die Armbänder bestimmt, die angezeigt werden
$bracelets_displayed = $systemStats['recent_brids'];
foreach($bracelets_displayed as $key => $val) {
	$stats[$key] = array_merge($statistics->bracelet_stats($val), $statistics->picture_details($val));
}

if(isset($stats[$_GET['q'] - 2]))
			for ($i = $_GET['q'] - 2; $i <= $_GET['q']; $i++) {
				if(!isset($stats[$i])) break;
				$braceName = $statistics->brid2name($bracelets_displayed[$i]);
				if($i == $_GET['q'] - 2)
					echo '<hr style="clear: both;">';
?>
				<div style="width: 100%; overflow: auto;">
					<div style="width: 70%; float: left;">
						<a href="start?pic_name=<?php echo urlencode($statistics->brid2name($bracelets_displayed[$i])); ?>&picid=<?php echo $stats[$i][0]['picid']; ?>&last_pic=last&delete_pic=true" class="delete_button float_right delete_bild" style="margin-top: 2em;" title="Bild löschen/melden" data-bracelet="<?php echo $braceName; ?>" onclick="confirmDelete('das Bild', this); return false;">X</a>
						<a href="pictures/bracelets/pic<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][0]['picid'].'.'.$stats[$i][0]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; //onclick="return confirmDelete('das Bild');" ?>" class="thumb_link">
							<img src="img/triangle.png" alt="" class="thumb_triangle">
							<img src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][0]['picid'].'.jpg'; ?>" alt="<?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; ?>" class="thumbnail">
						</a>
						<table class="pic-info">
							<tr>
								<th>Datum</th>
								<td><?php echo date('d.m.Y H:i', $stats[$i][0]['date']); ?> Uhr</td>
							</tr>
							<tr>
								<th>Ort</th>
								<td><?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; ?></td>
							</tr>
<?php
				if($stats[$i][0]['user'] != NULL) {
?>
							<tr>
								<th>Uploader</th>
								<td><a href="profil?user=<?php echo urlencode(html_entity_decode($stats[$i][0]['user'])); ?>"><?php echo $stats[$i][0]['user']; ?></a></td>
							</tr>
<?php
                 }
?>
						</table> 
						<p class="pic-desc">
							<span class="desc-header"><?php echo $stats[$i][0]['title']; ?></span><br>
							<?php echo $stats[$i][0]['description']; ?>      
							<br><br>
							<span class="pseudo_link toggle_comments" id="toggle_comment<?php echo $i;?>">Kommentare zeigen</span>
						</p>
					</div>
					<aside class="bracelet-props side_container">
						<table>
							<tr>
								<td><strong>Armband</strong></td>
								<td><strong><?php echo '<a href="armband?name='.urlencode($statistics->brid2name($bracelets_displayed[$i])).'">'.htmlentities($statistics->brid2name($bracelets_displayed[$i])).'</a>'; ?></strong></td>
							</tr>
							<tr>
								<td>Käufer</td>
								<td><a href="profil?user=<?php echo urlencode(html_entity_decode($stats[$i]['owner'])); ?>" style="color: #fff;"><?php echo $stats[$i]['owner']; ?></a></td>
							</tr>
							<tr>
								<td>Anzahl Besitzer</td>
								<td><?php echo $stats[$i]['owners']; ?></td>
							</tr>
							<tr>
								<td>Letzter Ort</td>
								<td><?php echo $stats[$i][0]['city']; ?>,</td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $stats[$i][0]['country']; ?></td>
							</tr>
							<tr>
								<td>Station Nr.</td>
								<td><?php echo $stats[$i][0]['picid']; ?></td>
							</tr>
						</table>
					</aside>
				</div>
				<div class="comments" id="comment<?php echo $i;?>">
<?php
				for ($j = 1; $j <= count($stats[$i][0])-11; $j++) {
					//Vergangene Zeit seit dem Kommentar berechnen
					$x_days_ago = ceil((strtotime("00:00") - $stats[$i][0][$j]['date']) / 86400);
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
				if(isset($stats[$i][0][$j + 1]['commid'])) {
					$last_comment = 'middle';
				}else {
					$last_comment = 'last';
				}
?>
					<a href="start?last_comment=<?php echo $last_comment; ?>&commid=<?php echo $stats[$i][0][$j]['commid']; ?>&picid=<?php echo $stats[$i][0][$j]['picid']; ?>&comm_name=<?php echo urlencode($statistics->brid2name($bracelets_displayed[$i])); ?>&delete_comm=true" class="delete_button float_right delete_comment" title="Kommentar löschen/melden" data-bracelet="<?php echo $braceName; ?>" onclick="confirmDelete('den Kommentar', this); return false;">X</a>
					<strong><?php echo $stats[$i][0][$j]['user']; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][0][$j]['date']).')';//onclick="return confirmDelete('den Kommentar');" ?>
                    <p><?php echo $stats[$i][0][$j]['comment']; ?></p> 
                    <hr style="border: 1px solid white;">  
<?php 
				}
?>   
					<form name="comment[<?php echo $i; ?>]" class="comment_form" action="start" method="post">
						<span style="font-family: Verdana, Times"><strong style="color: #000;">Kommentar</strong> schreiben</span><br><br>
						<label <?php if($user->login) echo 'style="display: none; " ';?>for="comment_user[<?php echo $i; ?>]" class="label_comment_user">Name: </label>
						<input <?php if($user->login) echo 'type="hidden" '; else echo 'type="text" ';?>type="text" name="comment_user[<?php echo $i; ?>]" <?php if($user->login == true) echo ' value="'.$user->login.'" ';?>id="comment_user[<?php echo $i; ?>]" class="comment_user" size="20" maxlength="15" <?php if (isset($user->login)){echo 'value="'.$user->login.'" ';} ?>placeholder="Name" pattern=".{4,15}" title="Min.4 - Max.15" required><?php if(!$user->login) echo '<br>'; ?>
						<label for="comment_content[<?php echo $i; ?>]" class="label_comment_content">Dein Kommentar:</label><br>
						<textarea name="comment_content[<?php echo $i; ?>]" id="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
						<input type="hidden" name="comment_brid[<?php echo $i; ?>]" value="<?php echo $bracelets_displayed[$i];?>">
						<input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i][0]['picid']; ?>">
						<input type="hidden" name="comment_form" value="<?php echo $i; ?>">
						<input type="submit" name ="comment_submit[<?php echo $i; ?>]" value="Kommentar abschicken" class="submit_comment">
					</form>
				</div>
                 
<?php
					if (isset($stats[$i+1])) {
?>
<!--~~~HR~~~~--><hr style="clear: both;">
<?php	
					}
				}
?>
