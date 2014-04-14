<?php
session_start(); //Session starten
include_once('../connection.php');
include_once('../functions.php');
function tprofile_pic($userid) {
	if(file_exists('../../pictures/profiles/'.$userid.'.jpg')) return '/pictures/profiles/'.$userid.'.jpg';
		else return '/img/profil_pic_small.png';
}
require_once('../user.php');
$lang = simplexml_load_file('../../text/translations.xml');
if(isset($_GET['eng'])) $lng = $_GET['eng'];
if(isset($_SESSION['user'])) {
	$user = new User($_SESSION['user'], $db);
	$checklogin = $user->logged;
}else {
	$user = new User(false, $db);
	$checklogin = false;
}
$statistics = new Statistics($db, $user);
$braceName = urldecode($_GET['braceName']);
$braceID = $statistics->name2brid($braceName);
if($braceID != NULL) {
	$bracelet_stats = $statistics->bracelet_stats($braceID, $db);
	if(isset($bracelet_stats['owners'])) {
		$picture_details = $statistics->picture_details($braceID, true);
		$stats = array_merge($bracelet_stats, $picture_details);
	}else {
		$bracelet_stats['owners'] = 0;
		$stats = $bracelet_stats;
	}
	$stmt = $db->prepare('SELECT brid FROM bracelets WHERE userid = :ownerid ORDER BY date ASC');
	$stmt->execute(array(':ownerid' => $statistics->username2id($stats['owner'])));
	$userfetch = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($userfetch as $key => $val) {
		if($val['brid'] == $braceID) $stats['braceletNR'] = $key + 1;
	}
	for ($i = $_GET['q'] - 3; $i < $_GET['q']; $i++) {
		if(!isset($stats[$i])) break;
		if($i < $_GET['q']) {
?>
<!--~~~HR~~~~--><hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;">
<?php
		}
		
		$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid=:brid');
		$stmt->execute(array('picid' => $stats[$i]['picid'], 'brid' => $braceID));
		$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
?>
				<div style="width: 100%; overflow: auto;">
					<a href="/armband?picid=<?php echo $stats[$i]['picid']; ?>&amp;last_pic=middle&amp;delete_pic=true" class="delete_button float_right" style="margin-top: 2em;" title="<?php echo $lang->pictures->deletepic->$lng; ?>" onclick="return confirmDelete('dasBild');">X</a>
					<h3 id="pic-<?php echo $stats[$i]['picid']; ?>"><?php if($startPicid == $stats[$i]['picid']) echo '=> '; echo $stats[$i]['city'].', '.$stats[$i]['country']; ?></h3>
					<a href="/pictures/bracelets/pic<?php echo '-'.$rowid['id'].'.'.$stats[$i]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?>" class="thumb_link" data-bracelet="<?php echo $braceName; ?>">
						<img src="/cache.php?f=/img/triangle.png" alt="" class="thumb_triangle">
						<img src="/cache.php?f=/pictures/bracelets/thumb<?php echo '-'.$rowid['id'].'.jpg'; ?>" alt="<?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?>" class="thumbnail">
					</a>
					<table class="pic-info">
						<tr>
							<th><?php echo $lang->pictures->datum->$lng; ?></th>
							<td><?php echo date('d.m.Y H:i', $stats[$i]['date']); ?> Uhr</td>
						</tr>
<?php
		if($stats[$i]['user'] != NULL) {
?>
						<tr>
							<th><?php echo $lang->pictures->uploader->$lng; ?></th>
							<td><img src="/cache.php?f=<?php echo tprofile_pic($stats[$i]['userid']); ?> " width="20" style="border: 1px #999 solid;">&nbsp;
                                <a href="/profil?user=<?php echo urlencode(html_entity_decode($stats[$i]['user'])); ?>"><?php echo $stats[$i]['user']; ?></a></td>
						</tr>
<?php
		}
?>
					</table>
					<div class="fb-like" data-href="http://placelet.de/<?php echo $stats['owner'].'/'.$stats['braceletNR'].'/'.$stats[$i]['picid']; ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
					<p class="pic-desc">
						<span class="desc-header"><?php echo $stats[$i]['title']; ?></span><br>
						<?php echo $stats[$i]['description']; ?>      
						<br><br>
						<span class="pseudo_link toggle_comments" id="toggle_comment<?php echo $i;?>" onClick="show_comments(this);" data-counts="<?php echo count($stats[$i])-12 ?>"><?php echo $lang->misc->comments->showcomment->$lng; ?> (<?php echo count($stats[$i])-12; ?>)</span>
					</p>
                    
					<div class="comments" id="comment<?php echo $i;?>">
<?php
		for ($j = 1; $j <= count($stats[$i])-12; $j++) {
			//Vergangene Zeit seit dem Kommentar berechnen
			$x_days_ago = days_since($stats[$i][$j]['date']);
			//Überprüfen, ob das Kommentar, was man löschen will das letzte ist.
			if(isset($stats[$i][$j + 1]['commid'])) {
				$last_comment = 'middle';
			}else {
				$last_comment = 'last';
			}
?>
							<a href="/armband?last_comment=<?php echo $last_comment; ?>&amp;commid=<?php echo $stats[$i][$j]['commid']; ?>&amp;picid=<?php echo $stats[$i][$j]['picid']; ?>&amp;delete_comm=true" class="delete_button float_right" title="<?php echo $lang->pictures->deletecomment->$lng; ?>" data-bracelet="<?php echo $braceName; ?>" onclick="confirmDelete('denKommentar', this); return false;">X</a>
                            <img src="/cache.php?f=<?php echo tprofile_pic($stats[$i][$j]['userid']); ?> " width="20" style="border: 1px #999 solid;">&nbsp;
                            <strong><?php if($stats[$i][$j]['user'] == NULL) echo 'Anonym'; else echo $stats[$i][$j]['user']; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][$j]['date']).')'; ?>
                            <p><?php echo $stats[$i][$j]['comment']; ?></p> 
                            <hr style="border: 1px solid white;">  
<?php 
		}
?>   
						<form name="comment[<?php echo $i; ?>]" class="comment_form" action="/<?php echo bridtoids($braceName); ?>" method="post">
							<?php echo $lang->misc->comments->kommentarschreiben->$lng; ?><br>
							<label for="comment_content[<?php echo $i; ?>]" class="label_comment_content"><?php echo $lang->misc->comments->deinkommentar->$lng; ?>:</label><br>
							<textarea name="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
							<input type="hidden" name="comment_brid[<?php echo $i; ?>]" value="<?php echo $braceID;?>">
							<input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i]['picid']; ?>">
							<input type="hidden" name="comment_form" value="<?php echo $i; ?>">
							<input type="submit" name="comment_submit[<?php echo $i; ?>]" value="<?php echo $lang->misc->comments->comment_button->$lng; ?>" class="submit_comment">
						</form>
					</div>
				</div>
<?php
	}
}
?>