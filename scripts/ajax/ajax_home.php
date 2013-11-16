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
$cv = $_POST['contentVar'];
$startVal = $_POST['startVal'];
switch($cv){
	case '+':
		$startVal++;
		break;
	case '-':
		$startVal--;
		break;
}
$systemStats = $statistics->systemStats(0, $startVal + 1);
$bracelets_displayed = $systemStats['recent_brids'];
foreach($bracelets_displayed as $key => $val) {
	$stats[$key] = array_merge($statistics->bracelet_stats($val), $statistics->picture_details($val));
}
$i = $startVal;
if($i != 1) {
?>
						<div class="pseudo_link float_left" onClick="return false" onMouseDown="javascript:change_pic('-', <?php echo $startVal; ?>);">&lt;</div>
<?php
}
if(isset($stats[$i + 1][0]['picid'])) {
?>
						<div class="pseudo_link float_right" onClick="return false" onMouseDown="javascript:change_pic('+', <?php echo $startVal; ?>);">&gt;</div>
<?php
}
?>
						<a href="pictures/bracelets/pic<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][0]['picid'].'.'.$stats[$i][0]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; ?>" class="thumb_link">
							<img src="img/triangle.png" alt="" class="thumb_triangle">
							<img src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][0]['picid'].'.jpg'; ?>" alt="<?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; ?>" class="thumbnail" style="max-height: 175px;">
						</a>
						<div>
							<table class="pic-info">
								<tr>
									<th>Armband</th>
									<td><strong><?php echo '<a href="armband?name='.urlencode($statistics->brid2name($bracelets_displayed[$i])).'">'.$statistics->brid2name($bracelets_displayed[$i]).'</a>'; ?></strong></td>
								</tr>
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
									<td><?php echo $stats[$i][0]['user']; ?></td>
								</tr>
	<?php
				 }
	?>
							</table> 
							<p class="pic-desc">
								<span class="desc-header"><?php echo $stats[$i][0]['title']; ?></span><br>
								<?php echo $stats[$i][0]['description']; ?>
							</p>
						</div>
						<img src="img/loading.gif" id="loading" alt="loading..." style="display: block; margin: 0 auto; display: none; position: relative; right: 25.5%;">