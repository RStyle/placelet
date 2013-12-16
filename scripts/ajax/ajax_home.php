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
$systemStats = $statistics->systemStats(0, $startVal+3);
$bracelets_displayed = $systemStats['recent_brids'];
foreach($bracelets_displayed as $key => $val) {
	$stats[$key] = array_merge($statistics->bracelet_stats($val), $statistics->picture_details($val));
}
$i = $startVal;
if($i != 1) {
?>
						<div class="pseudo_link float_left" onClick="return false" onMouseDown="javascript:change_pic('-', <?php echo $startVal; ?>);"><img src="img/prev.png" alt="prev"></div>
<?php
}
if(isset($stats[$i + 1][0]['picid'])) {
?>
					    <div class="pseudo_link float_right" onClick="return false" onMouseDown="javascript:change_pic('+', <?php echo $startVal; ?>);"><img src="img/next.png" alt="next"></div> 
<?php
}
?>
						<div id="central_newest_pic">
                            <div class="more_imgs">
                                    <?php if(isset($stats[$i+1][0]['picid'])){?><img class="fake_img pseudo_link" src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[$i+1].'-'.$stats[$i+1][0]['picid'].'.jpg'; ?>" alt="-" onMouseDown="javascript:change_pic('+', <?php echo $startVal;?>);"><?php } ?>     <br>
                                    <?php if(isset($stats[$i+2][0]['picid'])){?><img class="fake_img pseudo_link" src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[$i+2].'-'.$stats[$i+2][0]['picid'].'.jpg'; ?>" alt="-" onMouseDown="javascript:change_pic('+', <?php echo $startVal;?>+1);"><?php } ?>     <br>
                                    <?php if(isset($stats[$i+3][0]['picid'])){?><img class="fake_img pseudo_link" src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[$i+3].'-'.$stats[$i+3][0]['picid'].'.jpg'; ?>" alt="-" onMouseDown="javascript:change_pic('+', <?php echo $startVal;?>+2);"><?php } ?>
    					    </div>
                            <a href="pictures/bracelets/pic<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][0]['picid'].'.'.$stats[$i][0]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; ?>" class="connect_thumb_link">
    							<img src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][0]['picid'].'.jpg'; ?>" alt="<?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; ?>" class="connect_thumbnail" style="max-height: 175px;">
    						</a>
    						
							<table class="connect_pic-info">
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
									<td><a href="profil?user=<?php echo $stats[$i][0]['user']; ?>"><?php echo $stats[$i][0]['user']; ?></a></td>
								</tr>
	<?php
				 }
	?>
							</table> 
							<!--<p class="pic-desc">
								<span class="desc-header"><?php echo $stats[$i][0]['title']; ?></span><br>
								<?php echo $stats[$i][0]['description']; ?>
							</p>              -->
						</div>
						
						<!--<img src="img/loading.gif" id="loading" alt="loading..." style="display: block; margin: 0 auto; display: none; position: relative; right: 25.5%;">     -->