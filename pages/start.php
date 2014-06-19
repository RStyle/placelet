            <div id="connect_leiste">
                <div class="connect_box connect_box_start" id="stats_box">
                    <h1><?php echo $lang->stats->main[$lng.'-title']; ?></h1>
                    <table>
    					<tr>
    						<th><?php echo $lang->stats->main->regarmbänder->$lng; ?></th>
    						<td><?php echo $systemStats['total_registered']?></td>
    					</tr>
    					<tr>
    						<th><?php echo $lang->stats->main->staedte->$lng; ?></th>
    						<td><?php echo $systemStats['city_count']; ?></td>
    					</tr>
    					<tr>
    						<th><?php echo $lang->stats->main->laender->$lng; ?></th>
    						<td><?php echo $systemStats['country_count']; ?></td>
    					</tr>
    					<tr>
    						<th><?php echo $lang->stats->main->beliebtestestadt->$lng; ?></th>
    						<td><?php echo $systemStats['most_popular_city']['city'].' ('.$systemStats['most_popular_city']['number'].')'; ?></td>
    					</tr>
    					<tr>
    						<th><?php echo $lang->stats->main->armband->$lng; ?></th>
    						<td><a href="/<?php echo bracename2ids($systemStats['bracelet_most_cities']['name']); ?>"> <?php echo $systemStats['bracelet_most_cities']['name'].'('.$systemStats['bracelet_most_cities']['number'].')'; ?></a></td>
    					</tr>
    				</table>
                </div>
                <div class="connect_box connect_box_start" id="topusers_box">
                    <h1><?php echo $lang->stats->aktivstebenutzer[$lng.'-title']; ?></h1>
                    <table id="topusers">
    					<tr>
    						<td class="start_topusers_td"><?php echo $lang->stats->aktivstebenutzer->benutzername->$lng; ?></td>
    						<td class="start_topusers_td"><?php echo $lang->stats->aktivstebenutzer->armbänder->$lng; ?></td>
    						<td class="start_topusers_td"><?php echo $lang->stats->aktivstebenutzer->uploads->$lng; ?></td>
    					</tr>
    <?php
    /*for ($i = 0; $i < count($systemStats['user_most_bracelets']['user']); $i++) {
    ?>
    					<tr>
    						<td>
								<a href="/profil?user=<?php echo $systemStats['user_most_bracelets']['user'][$i]; ?>">
									<?php echo $systemStats['user_most_bracelets']['user'][$i]; ?>
								</a>
							</td>
    						<td><?php echo $systemStats['user_most_bracelets']['number'][$i]; ?></td>
    						<td><?php echo $systemStats['user_most_bracelets']['uploads'][$i]; ?></td>
    					</tr>
    <?php
    }*/
	$activ = array();
	$sql = "SELECT user, userid FROM users";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$qusers = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($qusers as $thisuser){
		$sql = "SELECT name FROM bracelets WHERE userid = :userid";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':userid' => $thisuser['userid']));
		$bracelets = $stmt->rowCount(); 
		
		$sql = "SELECT id FROM pictures WHERE userid = :userid";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':userid' => $thisuser['userid']));
		$uploads = $stmt->rowCount(); 
		$activ[$thisuser['user']] = array('br' => $bracelets, 'up' => $uploads, 'userid' => $thisuser['userid']);
	}
	foreach ($activ as $key => $row) {
		$br[$key] = $row['br'];
		$up[$key] = $row['up'];
	}
	array_multisort($up, SORT_DESC, $br, SORT_DESC, $activ);
	$activ = array_slice($activ, 0, 5);
	//var_dump($activ);
	
    foreach($activ as $key => $val) {
    ?>
    					<tr>
    						<td>
    						    <img src="/cache.php?f=<?php echo profile_pic($val['userid']); ?> " width="20" class="border999">&nbsp;
								<a href="/profil?user=<?php echo urlencode($key); ?>">								    
									<?php echo $key; ?>
								</a>
							</td>
    						<td><?php echo $val['br']; ?></td>
    						<td><?php echo $val['up'];/*$systemStats['user_most_bracelets']['uploads'][$key];*/ ?></td>
    					</tr>
    <?php
    }
    ?>
    				</table>
                </div>
                <div class="connect_box connect_box_start" id="submit_box">
                    <h1><?php echo $lang->stats->neuesbild[$lng.'-title']; ?></h1>
                    <div>
						<?php echo $lang->stats->neuesbild->ideingeben->$lng; ?>
						<form action="/login" method="get">
							<input name="postpic" type="text" maxlength="6" size="6" pattern="\w{6}" title="<?php echo $lang->community->sixcharacters->$lng; ?>" placeholder="ID...">
							<input type="submit" value="<?php echo $lang->stats->neuesbild->button->$lng; ?>">
						</form>
					</div>

                    <hr>

                    <h1><?php echo $lang->stats->neuesarmband[$lng.'-title']; ?></h1>
                    <div>
						<?php echo $lang->stats->neuesarmband->ideingeben->$lng; ?>
						<form action="/login" method="get">
							<input name="registerbr" type="text" maxlength="6" size="6" pattern="\w{6}" title="<?php echo $lang->community->sixcharacters->$lng; ?>" placeholder="ID...">
							<input type="submit" value="<?php echo $lang->stats->neuesarmband->button->$lng; ?>">
						</form>
					</div>
                </div>
            </div>
        
<!-- UPLOADS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->        
        
			<article id="recent_pics" class="mainarticles bottom_border_blue">
			<div class="blue_line mainarticleheaders line_header"><h2 id="pic_br_switch" data-recent_brid_pics="false"><?php echo $lang->community->neuestebilder[$lng.'-title']; ?></h2></div>
<?php
			for($i = 1; $i <= count($bracelets_displayed) && $i <= $systemStats['total_posted']; $i++) {
				$braceName = $statistics->brid2name($bracelets_displayed[$i]);
				
				$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid = :brid');
				$stmt->execute(array(':picid' => $stats[$i][$systemStats['recent_picids'][$i]]['picid'], ':brid' => $bracelets_displayed[$i]));
				$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt = $db->prepare('SELECT brid FROM bracelets WHERE userid = :ownerid ORDER BY date ASC');
				$stmt->execute(array(':ownerid' => $statistics->username2id($stats[$i]['owner'])));
				$userfetch = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach($userfetch as $key => $val) {
					if($val['brid'] == $bracelets_displayed[$i]) $stats[$i]['braceletNR'] = $key + 1;
				}
?>
				<div class="width100">
					<div class="div70left">
						<a href="/community?pic_name=<?php echo urlencode($statistics->brid2name($bracelets_displayed[$i]).''); ?>&amp;picid=<?php echo $stats[$i][$systemStats['recent_picids'][$i]]['picid']; ?>&amp;last_pic=last&amp;delete_pic=true" class="delete_button float_right delete_bild mt2" title="<?php echo $lang->pictures->deletepic->$lng; ?>" data-bracelet="<?php echo $braceName; ?>" onclick="confirmDelete('dasBild', this); return false;">X</a>
						<a href="/pictures/bracelets/pic<?php echo '-'.$rowid['id'].'.'.$stats[$i][$systemStats['recent_picids'][$i]]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i][$systemStats['recent_picids'][$i]]['city'].', '.$stats[$i][$systemStats['recent_picids'][$i]]['country']; //onclick="return confirmDelete('dasBild');" ?>" class="thumb_link">
							<img src="/cache.php?f=/img/triangle.png" alt="" class="thumb_triangle">
							<img src="/cache.php?f=/pictures/bracelets/thumb<?php echo '-'.$rowid['id'].'.jpg'; ?>" alt="<?php echo $stats[$i][$systemStats['recent_picids'][$i]]['city'].', '.$stats[$i][$systemStats['recent_picids'][$i]]['country']; ?>" class="thumbnail">
						</a>
						<table class="pic-info">
							<tr>
								<th><?php echo $lang->pictures->datum->$lng; ?></th>
								<td><?php echo date('d.m.Y H:i', $stats[$i][$systemStats['recent_picids'][$i]]['date']). ' '. $lang->misc->uhr->$lng; ?></td>
							</tr>
							<tr>
								<th><?php echo $lang->pictures->ort->$lng; ?></th>
								<td><?php echo $stats[$i][$systemStats['recent_picids'][$i]]['city'].', '.$stats[$i][$systemStats['recent_picids'][$i]]['country']; ?></td>
							</tr>
<?php
				if($stats[$i][$systemStats['recent_picids'][$i]]['user'] != NULL) {
?>
							<tr>
								<th><?php echo $lang->pictures->uploader->$lng; ?></th>
								<td><img src="/cache.php?f=<?php echo profile_pic($stats[$i][$systemStats['recent_picids'][$i]]['userid']); ?> " width="20" class="border999">&nbsp;
                                    <a href="/profil?user=<?php echo urlencode(html_entity_decode($stats[$i][$systemStats['recent_picids'][$i]]['user'])); ?>">								        
                                        <?php echo $stats[$i][$systemStats['recent_picids'][$i]]['user']; ?>
                                    </a></td>
							</tr>
<?php
                 }
?>
						</table>
						<div class="fb-like" data-href="http://placelet.de/<?php echo $stats[$i]['owner'].'/'.$stats[$i]['braceletNR'].'/'.$stats[$i][$systemStats['recent_picids'][$i]]['picid']; ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
						<p class="pic-desc">
							<span class="desc-header"><?php echo $stats[$i][$systemStats['recent_picids'][$i]]['title']; ?></span><br>
							<?php echo $stats[$i][$systemStats['recent_picids'][$i]]['description']; ?>      
							<br><br>
							<span class="pseudo_link toggle_comments" id="toggle_comment<?php echo $i;?>" data-counts="<?php echo count($stats[$i][$systemStats['recent_picids'][$i]])-13; ?>"><?php echo $lang->misc->comments->showcomment->$lng; ?> (<?php echo count($stats[$i][$systemStats['recent_picids'][$i]])-13; ?>)</span>
							
						</p>
					</div>
					<aside class="bracelet-props side_container">
						<table>
							<tr>
								<td><strong><?php echo $lang->pictures->armband->$lng; ?></strong></td>
								<td><strong><?php echo '<a href="/'.$stats[$i]['owner'].'/'.$stats[$i]['braceletNR'].'/'.$stats[$i][$systemStats['recent_picids'][$i]]['picid'].'">'.htmlentities($statistics->brid2name($bracelets_displayed[$i])).'</a>'; ?></strong></td>
							</tr>
							<tr>
								<td><?php echo $lang->pictures->kaeufer->$lng; ?></td>
								<td><a href="/profil?user=<?php echo $stats[$i]['owner']; ?>" class="weiss"><?php echo $stats[$i]['owner']; ?></a></td>
							</tr>
							<tr>
								<td><?php echo $lang->pictures->bilderanzahl->$lng; ?></td>
								<td><?php echo $stats[$i]['pic_anz']; ?></td>
							</tr>
							<tr>
								<td><?php echo $lang->pictures->letzterort->$lng; ?></td>
								<td><?php echo $stats[$i]['lastcity']; ?>,</td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $stats[$i]['lastcountry']; ?></td>
							</tr>
							<tr>
								<td><?php echo $lang->community->neuestebilder->station->$lng; ?></td>
								<td><?php echo $stats[$i][$systemStats['recent_picids'][$i]]['picid']; ?></td>
							</tr>
						</table>
					</aside>
				</div>
				<div class="comments" id="comment<?php echo $i;?>" data-picnr="<?php echo $systemStats['recent_picids'][$i]; ?>">
<?php
				for ($j = 1; $j <= count($stats[$i][$systemStats['recent_picids'][$i]])-13; $j++) {
					//Vergangene Zeit seit dem Kommentar berechnen
					$x_days_ago = days_since($stats[$i][$systemStats['recent_picids'][$i]][$j]['date']);
					//Überprüfen, ob das Kommentar, was man löschen will das letzte ist.
					if(isset($stats[$i][$systemStats['recent_picids'][$i]][$j + 1]['commid'])) {
						$last_comment = 'middle';
					}else {
						$last_comment = 'last';
					}
?>
					<a href="/community?last_comment=<?php echo $last_comment; ?>&amp;commid=<?php echo $stats[$i][$systemStats['recent_picids'][$i]][$j]['commid']; ?>&amp;picid=<?php echo $stats[$i][$systemStats['recent_picids'][$i]][$j]['picid']; ?>&amp;comm_name=<?php echo urlencode($statistics->brid2name($bracelets_displayed[$i])); ?>&amp;delete_comm=true" class="delete_button float_right delete_comment" title="<?php echo $lang->pictures->deletepic->$lng; ?>" data-bracelet="<?php echo $braceName; ?>" onclick="confirmDelete('denKommentar', this); return false;">X</a>
					<img src="/cache.php?f=<?php echo profile_pic($stats[$i][$systemStats['recent_picids'][$i]][$j]['userid']); ?>" width="20" class="border999">&nbsp;
                    <?php if($stats[$i][$systemStats['recent_picids'][$i]][$j]['user'] == NULL) echo '<strong class="comments_name">Anonym</strong>'; else echo '<strong><a class="comments_name" href="/profil?user='.$stats[$i][$systemStats['recent_picids'][$i]][$j]['user'].'">'.$stats[$i][$systemStats['recent_picids'][$i]][$j]['user'].'</a>'; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][$systemStats['recent_picids'][$i]][$j]['date']).')';//onclick="return confirmDelete('denKommentar');" ?>
                    <p><?php echo $stats[$i][$systemStats['recent_picids'][$i]][$j]['comment']; ?></p> 
                    <hr class="border_white">  
<?php 
				}
?>   
					<form name="comment[<?php echo $i; ?>]" class="comment_form" action="/community" method="post">
						<?php echo $lang->misc->comments->kommentarschreiben->$lng; ?><br>
						<label for="comment_content[<?php echo $i; ?>]" class="label_comment_content"><?php echo $lang->misc->comments->deinkommentar->$lng; ?></label><br>
						<textarea name="comment_content[<?php echo $i; ?>]" id="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
						<input type="hidden" name="comment_brace_name[<?php echo $i; ?>]" value="<?php echo html_entity_decode($statistics->brid2name($bracelets_displayed[$i])); ?>">
						<input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i][$systemStats['recent_picids'][$i]]['picid']; ?>">
						<input type="hidden" name="comment_form" value="<?php echo $i; ?>">
						<input type="submit" name ="comment_submit[<?php echo $i; ?>]" value="<?php echo $lang->misc->comments->comment_button->$lng; ?>" class="submit_comment">
					</form>
				</div>
                 
<?php
				if ($i < count($bracelets_displayed)) {
?>
<!--~~~HR~~~~--><hr class="hr_clear">
<?php
				}
			}
			
			$js.="(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = '//connect.facebook.net/de_DE/all.js#xfbml=1';
			fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
			$(document).ajaxComplete(function(){
				try{
					FB.XFBML.parse(); 
				}catch(ex){}
			});";
?>
		</article>
		<div id="fb-root"></div>