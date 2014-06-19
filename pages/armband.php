<?php
$works = false;
if($braceName !== NULL) {
	if(isset($stats['name'])) {
		$eecho = '';
		$data = getlnlt($stats['name']);
		$central = '0, 0';
		$max = array(false, false, false, false, 0);
		$i = 0;
		$weg = "var flightPlanCoordinates = [";
		$count_pos = 1;
		
		foreach($data as $pos){
		
			if($pos['latitude'] < $max[0] || $max[0] == false)
				$max[0] = $pos['latitude'];
			if($pos['latitude'] > $max[1] || $max[1] == false)
				$max[1] = $pos['latitude'];
			if($pos['longitude'] < $max[2] || $max[2] == false)
				$max[2] = $pos['longitude'];
			if($pos['longitude'] > $max[3] || $max[3] == false)
				$max[3] = $pos['longitude'];

			$weg.= "\n".'new google.maps.LatLng('.$pos['latitude'].', '.$pos['longitude'].')';
			if($count_pos < count($data)) $weg.= ',';
			$count_pos++;
			$js.= '
			var latlng'.$i.' = new google.maps.LatLng('.$pos['latitude'].', '.$pos['longitude'].');';
			$eecho .= '
			var marker'.$i.' = new google.maps.Marker({
				position: latlng'.$i.',
				map: map
			});';
			$i++;
		}
		  $weg.= "\n];\n".'var flightPath = new google.maps.Polyline({
				path: flightPlanCoordinates,
				strokeColor: "#FE2E2E",
				strokeOpacity: 0.5,
				strokeWeight: 2
			  });
			    flightPath.setMap(map);'."\n";
		
		$central = ($max[0]+($max[1]-$max[0])/2) . ', ' . ($max[2]+($max[3]-$max[2])/2);
		$max[4] = ($max[1]-$max[0]);
		if(($max[3]-$max[2]) > $max[4])
			$max[4] = ($max[3]-$max[2]);
			
		$zoom = 1;
		if($max[4] < 0.02)
			$zoom = 14;
		else if($max[4] < 0.0625)
			$zoom = 12;
		else if($max[4] < 0.125)
			$zoom = 11;
		else if($max[4] < 0.25)
			$zoom = 10;
		else if($max[4] < 0.5)
			$zoom = 9;
		else if($max[4] < 1)
			$zoom = 8;
		else if($max[4] < 2)
			$zoom = 7;
		else if($max[4] < 5)
			$zoom = 6;
		else if($max[4] < 6.5)
			$zoom = 5;
		else if($max[4] < 18)
			$zoom = 4;
		else if($max[4] < 40)
			$zoom = 3;
		else if($max[4] < 80)
			$zoom = 2;
			
		if(count($data) <= 1) $weg = '';
		 $js.='
		function initialize() {
		  var mapOptions = {
			zoom: '.$zoom.',
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: new google.maps.LatLng('.$central.')
		  }
		var map = new google.maps.Map(document.getElementById("map_home"), mapOptions);
		'.$weg.$eecho.'
		}';
		$js.= "\n".'google.maps.event.addDomListener(window, "load", initialize);';
		$showPics = $stats['pic_anz'] - $startPicid + 1;
		if($showPics < 3) $showPics = 3;
		if($defaultPicid) $showPics = 3;
?>
			<article id="armband" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1 id="bracelet" data-pics="<?php echo $showPics; ?>"><?php echo $lang->pictures->armband->$lng; ?> <?php echo htmlentities($braceName); ?></h1></div>
				<?php if(!$user_subscribed) echo '<span class="pseudo_link float_right" id="show_sub">'.$lang->armband->abonnieren->$lng.'</span>'; ?>
				<a href="/<?php echo 'login?postpic'; if($user->login && ($user->admin == true || $user->login == @$stats[$stats['pic_anz'] - 1]['user'] || @$user->login == $stats['owner'])) echo '='.$braceID.'" title="'.$braceID.'"';?>"><?php echo $lang->armband->bildposten->$lng; ?></a>
<?php
		if(!$user_subscribed) {
?>
				<form method="get" action="/armband">
					<input type="submit" name="sub_submit" value="<?php echo $lang->pictures->armband->$lng; ?>" class="float_right sub_inputs" style="display: none;">
					<input name="sub_code" type="email"  size="20" maxlength="100" placeholder="<?php echo $lang->form->email->$lng; ?>" class="float_right sub_inputs" style="display: none;" required>
					<input type="hidden" name="sub" value="email">
					<input type="hidden" name="name" value="<?php echo urlencode($braceName); ?>" id="bracelet_name">
				</form>
<?php
		}
		for($i = 0; $i < $showPics; $i++) {
			if(isset($stats[$i])) {
				$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid = :brid');
				$stmt->execute(array('picid' => $stats[$i]['picid'], 'brid' => $braceID));
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
					
				if($i == 0) $last_pic = 'last';
					else $last_pic = 'middle';
	?>
					<div style="width: 100%; overflow: auto;">
					<a href="/armband?name=<?php echo urlencode($braceName); ?>&amp;picid=<?php echo $stats[$i]['picid']; ?>&amp;last_pic=<?php echo $last_pic; ?>&amp;delete_pic=true" class="delete_button float_right" style="margin-top: 2em;" data-bracelet="<?php echo $braceName; ?>" title="<?php echo $lang->pictures->deletepic->$lng; ?>" onclick="confirmDelete('dasBild', this); return false;">X</a>
						<h3 id="pic-<?php echo $stats[$i]['picid']; ?>"><?php if(!$defaultPicid && $startPicid == $stats[$i]['picid']) echo '<img alt="" src="/cache.php?f=/img/pfeil_small.png" width="30" height="20" style="position: relative; top: 2px;"> '; echo $stats[$i]['city'].', '.$stats[$i]['country']; ?></h3>
						<a href="/pictures/bracelets/pic<?php echo '-'.$row['id'].'.'.$stats[$i]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?>" class="thumb_link">
							<img src="/cache.php?f=/img/triangle.png" alt="" class="thumb_triangle">
							<img src="/cache.php?f=/pictures/bracelets/thumb-<?php echo $row['id']; ?>.jpg" alt="<?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?>" class="thumbnail">
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
								<td><img src="/cache.php?f=<?php echo profile_pic($stats[$i]['userid']); ?>" width="20" style="border: 1px #999 solid;">&nbsp;
                                    <a href="/profil?user=<?php echo $stats[$i]['user']; ?>"><?php echo $stats[$i]['user']; ?></a></td>
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
							<span class="pseudo_link toggle_comments" id="toggle_comment<?php echo $i;?>" data-counts="<?php echo count($stats[$i])-13 ?>"><?php echo $lang->misc->comments->showcomment->$lng; ?> (<?php echo count($stats[$i])-13; ?>)</span>
						</p>
						
						<div class="comments" id="comment<?php echo $i;?>">
	<?php
				for ($j = 1; $j <= count($stats[$i])-13; $j++) {
					//Vergangene Zeit seit dem Kommentar berechnen
					$x_days_ago = days_since($stats[$i][$j]['date']);
					//Überprüfen, ob das Kommentar, was man löschen will das letzte ist.
					if(isset($stats[$i][$j + 1]['commid'])) {
						$last_comment = 'middle';
					}else {
						$last_comment = 'last';
					}
	?>
								<a href="/armband?name=<?php echo urlencode($braceName); ?>&amp;last_comment=<?php echo $last_comment; ?>&amp;commid=<?php echo $stats[$i][$j]['commid']; ?>&amp;picid=<?php echo $stats[$i][$j]['picid']; ?>&amp;delete_comm=true" class="delete_button float_right" data-bracelet="<?php echo $braceName; ?>" title="<?php echo $lang->pictures->deletecomment->$lng; ?>" onclick="confirmDelete('denKommentar', this); return false;">X</a>
								<img src="/cache.php?f=<?php echo profile_pic($stats[$i][$j]['userid']); ?> " width="20" style="border: 1px #999 solid;">&nbsp;
                                <strong><?php if($stats[$i][$j]['user'] == NULL) echo 'Anonym'; else echo $stats[$i][$j]['user']; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][$j]['date']).')'; ?>
								<p><?php echo $stats[$i][$j]['comment']; ?></p> 
								<hr style="border: 1px solid white;">  
	<?php 
				}
	?>   
							<form name="comment[<?php echo $i; ?>]" class="comment_form" action="/<?php echo bracename2ids($braceName); ?>" method="post">
								<?php echo $lang->misc->comments->kommentarschreiben->$lng; ?><br>
								<label for="comment_content[<?php echo $i; ?>]" class="label_comment_content"><?php echo $lang->misc->comments->deinkommentar->$lng; ?>:</label><br>
								<textarea name="comment_content[<?php echo $i; ?>]" id="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
								<input type="hidden" name="comment_brace_name[<?php echo $i; ?>]" value="<?php echo html_entity_decode($braceName); ?>">
								<input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i]['picid']; ?>">
								<input type="hidden" name="comment_form" value="<?php echo $i; ?>">
								<input type="submit" name="comment_submit[<?php echo $i; ?>]" value="<?php echo $lang->misc->comments->comment_button->$lng; ?>" class="submit_comment">
							</form>
						</div>
					</div>
	<?php
				if(isset($stats[$i + 1]) && $i < $showPics - 1) {
	?>
	<!--~~~HR~~~~--><hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;">
	<?php	
				}
			}
		}
?>
<?php
		if ($bracelet_stats['pic_anz'] == 0 ) {
			echo '<p>'.$lang->armband->nopics->$lng.'</p>';
		}
?>
			</article>
			<aside class="side_container" id="bracelet_props">
				<h1><?php echo $lang->armband->statistik->$lng; ?></h1>
				<table style="width: 100%;">
					<tr>
						<th><?php echo $lang->misc->comments->name->$lng; ?></th>
						<td><?php echo '<strong id="disp_bracelet_name" data-url_bracename="'.urlencode($stats['name']).'">'.htmlentities($stats['name']).'</strong>'; if($owner) {?>  <img src="/cache.php?f=/img/edit.png" id="edit_name" class="pseudo_link"></td><?php } ?>
					</tr>
<?php
		if($owner) {
?> 
						<tr>
							<td><input type="text" name="edit_name" id="edit_name_input" placeholder="<?php echo $lang->armband->neuername->$lng; ?>" class="name_inputs display_none" size="20" maxlength="18" pattern=".{4,18}" title="Min.4 - Max.18" required></td>
							<td><input type="submit" id="edit_name_submit" data-brid="<?php echo $braceID; ?>" value="<?php echo $lang->armband->aendern->$lng; ?>" class="name_inputs display_none" name="edit_submit"></td>
						</tr>
<?php
		}
?>
					<tr>
						<td><?php echo $lang->pictures->kaeufer->$lng; ?></td>
						<td><a href="/profil?user=<?php echo $stats['owner']; ?>"><?php echo $stats['owner']; ?></a></td>
					</tr>
					<tr>
						<td><?php echo $lang->armband->registriert_am->$lng; ?></td>
						<td><?php echo date('d.m.Y', $stats['date']); ?></td>
					</tr>
					<tr>
						<td><?php echo $lang->pictures->bilderanzahl->$lng; ?></td>
						<td><?php echo $stats['pic_anz']; ?></td>
					</tr>
<?php
		if($bracelet_stats['pic_anz'] != 0) {
?>
					<tr>
						<td><?php echo $lang->pictures->letzterort->$lng; ?></td>
						<td><?php echo $stats['lastcity']; ?>,</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><?php echo $stats['lastcountry']; ?></td>
					</tr>
<?php
		}
		if($stats['pic_anz'] > 1){
			$i = 0;
			$distance = 0;
			$data2 = $data;
			
			foreach($data2 as $l){
				if($i > 0){
				$p1 = array('latitude' => $data2[$i-1]['latitude'], 'longitude' => $data2[$i-1]['longitude']);
				$p2 = array('latitude' => $data2[$i]['latitude'], 'longitude' => $data2[$i]['longitude']);
					$distance = $distance + getDistance($data2[$i-1], $l);
				}
				$i++;
			}
			$distance = round($distance)/1000;
?>
			<tr>
						<td><?php echo $lang->armband->distanz->$lng;?></td>
						<td><?php echo $distance; ?>km</td>
					</tr>
<?php
	}
?>
				</table>
			</aside>
<?php
		if($bracelet_stats['pic_anz'] != 0) {
?>
			<aside id="map_home" class="side_container" style="margin-top: 20px;"><?php echo $lang->misc->activate_js->$lng; ?></aside>
<?php
		}
		$works = true;
	}
	/*}else {
?>
			<article id="armband" class="mainarticles bottom_border_green" style="width: 100%;">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->armband->falschesarmband->$lng; ?></h1></div>
				<p>
					<?php echo $lang->armband->gibtsnicht->$lng; ?>
					<form action="/armband">
						<input type="search" name="name" placeholder="<?php echo $lang->armband->armbandname->$lng; ?>" size="20" maxlength="18" pattern=".{4,18}" title="Min.4 - Max.18" value="<?php if(isset($_GET['name'])) echo $_GET['name']?>" required>
					</form>
					<br><?php echo $lang->armband->odersuchen->no->$lng; ?>:
					<form action="/search" method="get">
						<input name="squery" type="search" placeholder="<?php echo $lang->form->suchen->$lng; ?>..." size="20" maxlength="18" value="<?php if(isset($_GET['name'])) echo $_GET['name']?>" required>
					</form>
				</p>
			</article>
<?php
	}*/
}if(!$works) {
?>
			<article id="armband" class="mainarticles bottom_border_green" style="width: 100%;">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->armband->falscheseite->$lng; ?></h1></div>
				<p><?php echo $lang->armband->gehweg->$lng; ?></p>
				<?php echo $lang->armband->odersuchen->armband->$lng; ?>:
				<form action="/search" method="get">
					<input name="squery" type="search" placeholder="<?php echo $lang->form->suchen->$lng; ?>..." size="20" maxlength="18" value="<?php if(isset($_GET['name'])) echo $_GET['name']?>" required>
				</form>
			</article>
<?php
}
?>