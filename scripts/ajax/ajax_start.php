<?php
session_start(); //Session starten
include_once('../connection.php');
include_once('../functions.php');
include_once('../user.php');
$lang = simplexml_load_file('../../text/translations.xml');
$lng = 'en';
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
echo '---'.$_GET['recent_brid_pics'].'---';
if($_GET['recent_brid_pics'] == 'true') {
	$displayed_picnr = 0;
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
						<a href="start?&picid=<?php echo $stats[$i][$displayed_picnr]['picid']; ?>&last_pic=last&delete_pic=true" class="delete_button float_right delete_bild" style="margin-top: 2em;" title="<?php echo $lang->pictures->deletepic->$lng; ?>" onclick="confirmDelete('dasBild', this); return false;">X</a>
						<a href="pictures/bracelets/pic<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][$displayed_picnr]['picid'].'.'.$stats[$i][$displayed_picnr]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i][$displayed_picnr]['city'].', '.$stats[$i][$displayed_picnr]['country']; ?>" class="thumb_link" data-bracelet="<?php echo $braceName; ?>">
							<img src="img/triangle.png" alt="" class="thumb_triangle">
							<img src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][$displayed_picnr]['picid'].'.jpg'; ?>" alt="<?php echo $stats[$i][$displayed_picnr]['city'].', '.$stats[$i][$displayed_picnr]['country']; ?>" class="thumbnail">
						</a>
						<table class="pic-info">
							<tr>
								<th><?php echo $lang->pictures->datum->$lng; ?></th>
								<td><?php echo date('d.m.Y H:i', $stats[$i][$displayed_picnr]['date']); ?> Uhr</td>
							</tr>
							<tr>
								<th><?php echo $lang->pictures->ort->$lng; ?></th>
								<td><?php echo $stats[$i][$displayed_picnr]['city'].', '.$stats[$i][$displayed_picnr]['country']; ?></td>
							</tr>
<?php
				if($stats[$i][$displayed_picnr]['user'] != NULL) {
?>
							<tr>
								<th><?php echo $lang->pictures->uploader->$lng; ?></th>
								<td><a href="profil?user=<?php echo urlencode(html_entity_decode($stats[$i][$displayed_picnr]['user'])); ?>"><?php echo $stats[$i][$displayed_picnr]['user']; ?></a></td>
							</tr>
<?php
                 }
?>
						</table> 
						<p class="pic-desc">
							<span class="desc-header"><?php echo $stats[$i][$displayed_picnr]['title']; ?></span><br>
							<?php echo $stats[$i][$displayed_picnr]['description']; ?>      
							<br><br>
							<span class="pseudo_link toggle_comments" id="toggle_comment<?php echo $i;?>" onClick="show_comments(this);"  data-counts="<?php echo count($stats[$i][$displayed_picnr])-11; ?>"><?php echo $lang->misc->comments->showcomment->$lng; ?> (<?php echo count($stats[$i][$displayed_picnr])-11; ?>)</span>
						</p>
					</div>
					<aside class="bracelet-props side_container">
						<table>
							<tr>
								<td><strong><?php echo $lang->pictures->armband->$lng; ?></strong></td>
								<td><strong><?php echo '<a href="armband?name='.urlencode($braceName).'">'.htmlentities($braceName).'</a>'; ?></strong></td>
							</tr>
							<tr>
								<td><?php echo $lang->pictures->kaufer->$lng; ?></td>
								<td><a href="profil?user=<?php echo urlencode($stats[$i]['owner']); ?>" style="color: #fff;"><?php echo htmlentities($stats[$i]['owner']); ?></a></td>
							</tr>
							<tr>
								<td><?php echo $lang->pictures->besitzer->$lng; ?></td>
								<td><?php echo $stats[$i]['owners']; ?></td>
							</tr>
							<tr>
								<td><?php echo $lang->pictures->letzterort->$lng; ?></td>
								<td><?php echo $stats[$i][0]['city']; ?>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><?php echo $stats[$i][0]['country']; ?></td>
							</tr>
							<tr>
								<td><?php echo $lang->community->neuestebilder->station->$lng; ?></td>
								<td><?php echo $stats[$i][$displayed_picnr]['picid']; ?></td>
							</tr>
						</table>
					</aside>
				</div>
				<div class="comments" id="comment<?php echo $i;?>">
<?php
				for ($j = 1; $j <= count($stats[$i][$displayed_picnr])-11; $j++) {
					//Vergangene Zeit seit dem Kommentar berechnen
					$x_days_ago = floor((time() - ($stats[$i][$displayed_picnr][$j]['date'] - (time() - strtotime("00:00")))) / 86400);
					switch($x_days_ago) {
						case 0:
							$x_days_ago = $lang->misc->comments->heute->$lng;
							break;
						case 1:
							$x_days_ago = $lang->misc->comments->gestern->$lng;
							break;
						default:
							$x_days_ago = $lang->misc->comments->tagenstart->$lng.' '.$x_days_ago.' '.$lang->misc->comments->tagenend->$lng;
					}
				//Überprüfen, ob das Kommentar, was man löschen will das letzte ist.
				if(isset($stats[$i][$displayed_picnr][$j + 1]['commid'])) {
					$last_comment = 'middle';
				}else {
					$last_comment = 'last';
				}
?>
					<a href="start?last_comment=<?php echo $last_comment; ?>&commid=<?php echo $stats[$i][$displayed_picnr][$j]['commid']; ?>&picid=<?php echo $stats[$i][$displayed_picnr][$j]['picid']; ?>&delete_comm=true" class="delete_button float_right" data-bracelet="<?php echo $braceName; ?>" onclick="confirmDelete('denKommentar', this); return false;">X</a>
                    <strong><?php echo $stats[$i][$displayed_picnr][$j]['user']; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][$displayed_picnr][$j]['date']).')'; ?>
                    <p><?php echo $stats[$i][$displayed_picnr][$j]['comment']; ?></p> 
                    <hr style="border: 1px solid white;">  
<?php 
				}
?>   
					<form name="comment[<?php echo $i; ?>]" class="comment_form" action="start" method="post">
						<?php echo $lang->misc->comments->kommentarschreiben->$lng; ?><br><br>
						<label <?php if($user->login) echo 'style="display: none; " ';?>for="comment_user[<?php echo $i; ?>]" class="label_comment_user"><?php echo $lang->misc->comments->name->$lng; ?></label>
							<input <?php if($user->login) echo 'type="hidden" '; else echo 'type="text" ';?>name="comment_user[<?php echo $i; ?>]" <?php if($user->login == true) echo ' value="'.$user->login.'" ';?>class="comment_user" size="20" maxlength="15" placeholder="Name" pattern=".{4,15}" title="<?php $lang->misc->comments->minmax415->$lng; ?>" required><?php if(!$user->login) echo '<br>'; ?>
						<label for="comment_content[<?php echo $i; ?>]" class="label_comment_content"><?php echo $lang->misc->comments->deinkommentar->$lng; ?></label><br>
						<textarea name="comment_content[<?php echo $i; ?>]" id="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
						<input type="hidden" name="comment_brid[<?php echo $i; ?>]" value="<?php echo $bracelets_displayed[$i];?>">
						<input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i][$displayed_picnr]['picid']; ?>">
						<input type="hidden" name="comment_form" value="<?php echo $i; ?>">
						<input type="submit" name ="comment_submit[<?php echo $i; ?>]" value="<?php echo $lang->misc->comments->comment_button->$lng; ?>" class="submit_comment">
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
