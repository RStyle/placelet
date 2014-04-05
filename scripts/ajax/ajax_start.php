<?php
session_start(); //Session starten
include_once('../connection.php');
include_once('../functions.php');
require_once('../user.php');
$lang = simplexml_load_file('../../text/translations.xml');
if(isset($_GET['eng'])) $lng = $_GET['eng'];
if(isset($_SESSION['user'])){
	$user = new User($_SESSION['user'], $db);
	$checklogin = $user->logged;
}else{
	$user = new User(false, $db);
	$checklogin = false;
}
$statistics = new Statistics($db, $user);

$recent_brid_pics = false;//Zur Absicherung, war bei diesem Syntax ziemlich verwirrt...
if($_GET['q'] == 3) {
	if($_GET['recent_brid_pics'] == 'false') $recent_brid_pics = true;
		else $recent_brid_pics = false;
}elseif($_GET['q'] > 3) {
	if($_GET['recent_brid_pics'] == 'false') $recent_brid_pics = false;
		else $recent_brid_pics = true;
}
$user_anz = 5;
$systemStats = $statistics->systemStats($user_anz, $_GET['q'], $recent_brid_pics);
//hier werden die Armbänder bestimmt, die angezeigt werden
$bracelets_displayed = $systemStats['recent_brids'];
foreach($bracelets_displayed as $key => $val) {
	$stats[$key] = array_merge($statistics->bracelet_stats($val), $statistics->picture_details($val));
}
/*echo 'Q = '.$_GET['q'].'<br>recent_brid_pics = ';
if($recent_brid_pics) echo 'true'; else echo 'false';*/
/*echo 'bracelets_displayed:<br>';
print_r($bracelets_displayed);
echo '<br><br><hr><hr><br><br>stats:<br>';
foreach($stats as $key => $val) {
	print_r($key);
	echo '<br>';
	print_r($val);
	echo '<br><br>';
}*/
if($_GET['q'] == 3) {
	if($recent_brid_pics) {
?>
				<div class="blue_line mainarticleheaders line_header"><h2 id="pic_br_switch" data-recent_brid_pics="true"><?php echo $lang->community->neuestearmbaender[$lng.'-title']; ?></h2></div>
<?php
	}else {
?>
				<div class="blue_line mainarticleheaders line_header"><h2 id="pic_br_switch" data-recent_brid_pics="false"><?php echo $lang->community->neuestebilder[$lng.'-title']; ?></h2></div>
<?php	
	}
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			for($i = $_GET['q'] - 2; $i <= $_GET['q']; $i++) {
				if($stats[$i] != array('name' => NULL)) {
				if($_GET['q'] > 3 || ($i > $_GET['q'] - 2 && $_GET['q'] == 3)) {
?>
<!--~~~HR~~~~--><hr style="clear: both;">
<?php	
}
				$braceName = $statistics->brid2name($bracelets_displayed[$i]);
				
				$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid=:brid');
				$stmt->execute(array('picid' => $stats[$i][$systemStats['recent_picids'][$i]-1]['picid'], 'brid' => $bracelets_displayed[$i]));
				$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
?>
				<div style="width: 100%; overflow: auto;">
					<div style="width: 70%; float: left;">
						<a href="community?pic_name=<?php echo urlencode($statistics->brid2name($bracelets_displayed[$i]).''); ?>&amp;picid=<?php echo $stats[$i][$systemStats['recent_picids'][$i]-1]['picid']; ?>&amp;last_pic=last&amp;delete_pic=true" class="delete_button float_right delete_bild" style="margin-top: 2em;" title="<?php echo $lang->pictures->deletepic->$lng; ?>" data-bracelet="<?php echo $braceName; ?>" onclick="confirmDelete('dasBild', this); return false;">X</a>
						<a href="pictures/bracelets/pic<?php echo '-'.$rowid['id'].'.'.$stats[$i][$systemStats['recent_picids'][$i]-1]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i][$systemStats['recent_picids'][$i]-1]['city'].', '.$stats[$i][$systemStats['recent_picids'][$i]-1]['country']; //onclick="return confirmDelete('dasBild');" ?>" class="thumb_link">
							<img src="img/triangle.png" alt="" class="thumb_triangle">
							<img src="pictures/bracelets/thumb<?php echo '-'.$rowid['id'].'.jpg'; ?>" alt="<?php echo $stats[$i][$systemStats['recent_picids'][$i]-1]['city'].', '.$stats[$i][$systemStats['recent_picids'][$i]-1]['country']; ?>" class="thumbnail">
						</a>
						<table class="pic-info">
							<tr>
								<th><?php echo $lang->pictures->datum->$lng; ?></th>
								<td><?php echo date('d.m.Y H:i', $stats[$i][$systemStats['recent_picids'][$i]-1]['date']). ' '. $lang->misc->uhr->$lng; ?></td>
							</tr>
							<tr>
								<th><?php echo $lang->pictures->ort->$lng; ?></th>
								<td><?php echo $stats[$i][$systemStats['recent_picids'][$i]-1]['city'].', '.$stats[$i][$systemStats['recent_picids'][$i]-1]['country']; ?></td>
							</tr>
<?php
				if($stats[$i][$systemStats['recent_picids'][$i]-1]['user'] != NULL) {
?>
							<tr>
								<th><?php echo $lang->pictures->uploader->$lng; ?></th>
								<td><a href="profil?user=<?php echo urlencode(html_entity_decode($stats[$i][$systemStats['recent_picids'][$i]-1]['user'])); ?>"><?php echo $stats[$i][$systemStats['recent_picids'][$i]-1]['user']; ?></a></td>
							</tr>
<?php
                 }
?>
						</table> 
						<p class="pic-desc">
							<span class="desc-header"><?php echo $stats[$i][$systemStats['recent_picids'][$i]-1]['title']; ?></span><br>
							<?php echo $stats[$i][$systemStats['recent_picids'][$i]-1]['description']; ?>      
							<br><br>
							<span class="pseudo_link toggle_comments" id="toggle_comment<?php echo $i;?>" data-counts="<?php echo count($stats[$i][$systemStats['recent_picids'][$i]-1])-11; ?>" onClick="show_comments(this);"><?php echo $lang->misc->comments->showcomment->$lng; ?> (<?php echo count($stats[$i][$systemStats['recent_picids'][$i]-1])-11; ?>)</span>
						</p>
					</div>
					<aside class="bracelet-props side_container">
						<table>
							<tr>
								<td><strong><?php echo $lang->pictures->armband->$lng; ?></strong></td>
								<td><strong><?php echo '<a href="'.urlencode($statistics->brid2name($bracelets_displayed[$i])).'?pic='.$stats[$i][$systemStats['recent_picids'][$i]-1]['picid'].'">'.htmlentities($statistics->brid2name($bracelets_displayed[$i])).'</a>'; ?></strong></td>
							</tr>
							<tr>
								<td><?php echo $lang->pictures->kaeufer->$lng; ?></td>
								<td><a href="profil?user=<?php echo urlencode(html_entity_decode($stats[$i]['owner'])); ?>" style="color: #fff;"><?php echo $stats[$i]['owner']; ?></a></td>
							</tr>
							<tr>
								<td><?php echo $lang->pictures->besitzer->$lng; ?></td>
								<td><?php echo $stats[$i]['owners']; ?></td>
							</tr>
							<tr>
								<td><?php echo $lang->pictures->letzterort->$lng; ?></td>
								<td><?php echo $stats[$i][0]['city']; ?>,</td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $stats[$i][0]['country']; ?></td>
							</tr>
							<tr>
								<td><?php echo $lang->community->neuestebilder->station->$lng; ?></td>
								<td><?php echo $stats[$i][$systemStats['recent_picids'][$i]-1]['picid']; ?></td>
							</tr>
						</table>
					</aside>
				</div>
				<div class="comments" id="comment<?php echo $i;?>" data-picnr="<?php echo $systemStats['recent_picids'][$i]-1+1; ?>">
<?php
				for ($j = 1; $j <= count($stats[$i][$systemStats['recent_picids'][$i]-1])-11; $j++) {
					//Vergangene Zeit seit dem Kommentar berechnen
					$x_days_ago = days_since($stats[$i][$systemStats['recent_picids'][$i]-1][$j]['date']);
					//Überprüfen, ob das Kommentar, was man löschen will das letzte ist.
					if(isset($stats[$i][$systemStats['recent_picids'][$i]-1][$j + 1]['commid'])) {
						$last_comment = 'middle';
					}else {
						$last_comment = 'last';
					}
?>
					<a href="community?last_comment=<?php echo $last_comment; ?>&amp;commid=<?php echo $stats[$i][$systemStats['recent_picids'][$i]-1][$j]['commid']; ?>&amp;picid=<?php echo $stats[$i][$systemStats['recent_picids'][$i]-1][$j]['picid']; ?>&amp;comm_name=<?php echo urlencode($statistics->brid2name($bracelets_displayed[$i])); ?>&amp;delete_comm=true" class="delete_button float_right delete_comment" title="<?php echo $lang->pictures->deletepic->$lng; ?>" data-bracelet="<?php echo $braceName; ?>" onclick="confirmDelete('denKommentar', this); return false;">X</a>
					<strong><?php if($stats[$i][$systemStats['recent_picids'][$i]-1][$j]['user'] == NULL) echo 'Anonym'; else echo $stats[$i][$systemStats['recent_picids'][$i]-1][$j]['user']; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][$systemStats['recent_picids'][$i]-1][$j]['date']).')';//onclick="return confirmDelete('denKommentar');" ?>
                    <p><?php echo $stats[$i][$systemStats['recent_picids'][$i]-1][$j]['comment']; ?></p> 
                    <hr style="border: 1px solid white;">  
<?php 
				}
?>   
					<form name="comment[<?php echo $i; ?>]" class="comment_form" action="community" method="post">
						<?php echo $lang->misc->comments->kommentarschreiben->$lng; ?><br>
						<label for="comment_content[<?php echo $i; ?>]" class="label_comment_content"><?php echo $lang->misc->comments->deinkommentar->$lng; ?></label><br>
						<textarea name="comment_content[<?php echo $i; ?>]" id="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
						<input type="hidden" name="comment_brid[<?php echo $i; ?>]" value="<?php echo $bracelets_displayed[$i];?>">
						<input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i][$systemStats['recent_picids'][$i]-1]['picid']; ?>">
						<input type="hidden" name="comment_form" value="<?php echo $i; ?>">
						<input type="submit" name ="comment_submit[<?php echo $i; ?>]" value="<?php echo $lang->misc->comments->comment_button->$lng; ?>" class="submit_comment">
					</form>
				</div>
                 
<?php
				}
			}
?>