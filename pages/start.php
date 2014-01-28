		    <div id="connect_leiste">
                <div class="connect_box connect_box_start" id="stats_box">
                    <h1><?php echo $lang->stats->main[$lng.'-title']; ?></h1>
                    <table>
    					<tr>
    						<th><?php echo $lang->stats->main->regarmbänder->$lng; ?></th>
    						<td><?php echo $systemStats['total_registered']?></td>
    					</tr>
    					<tr>
    						<th><?php echo $lang->stats->main->städte->$lng; ?></th>
    						<td><?php echo $systemStats['city_count']; ?></td>
    					</tr>
    					<tr>
    						<th><?php echo $lang->stats->main->beliebtestestadt->$lng; ?></th>
    						<td><?php echo $systemStats['most_popular_city']['city'].' ('.$systemStats['most_popular_city']['number'].')'; ?></td>
    					</tr>
    					<tr>
    						<th><?php echo $lang->stats->main->armband->$lng; ?></th>
    						<td><a href="armband?name=<?php echo urlencode($systemStats['bracelet_most_cities']['name']); ?>"> <?php echo $systemStats['bracelet_most_cities']['name'].'('.$systemStats['bracelet_most_cities']['number'].')'; ?></a></td>
    					</tr>
    				</table>
                </div>
                <div class="connect_box connect_box_start" id="topusers_box">
                    <h1><?php echo $lang->stats->aktivstebenutzer[$lng.'-title']; ?></h1>
                    <table id="topusers">
    					<tr>
    						<td style="border-bottom: 1px solid #000;"><?php echo $lang->stats->aktivstebenutzer->benutzername->$lng; ?></td>
    						<td style="border-bottom: 1px solid #000;"><?php echo $lang->stats->aktivstebenutzer->armbänder->$lng; ?></td>
    						<td style="border-bottom: 1px solid #000;"><?php echo $lang->stats->aktivstebenutzer->uploads->$lng; ?></td>
    					</tr>
    <?php
    for ($i = 0; $i < count($systemStats['user_most_bracelets']['user']); $i++) {
    ?>
    					<tr>
    						<td>
								<a href="profil?user=<?php echo $systemStats['user_most_bracelets']['user'][$i]; ?>">
									<?php echo $systemStats['user_most_bracelets']['user'][$i]; ?>
								</a>
							</td>
    						<td><?php echo $systemStats['user_most_bracelets']['number'][$i]; ?></td>
    						<td><?php echo $systemStats['user_most_bracelets']['uploads'][$i]; ?></td>
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
						<form action="login" method="get">
							<input name="postpic" type="text" maxlength="6" size="6" pattern="[0-9]{6}" title="6 Ziffern" placeholder="ID...">
							<input type="submit" value="<?php echo $lang->stats->neuesbild->button->$lng; ?>">
						</form>
					</div>

                    <hr>

                    <h1><?php echo $lang->stats->neuesarmband[$lng.'-title']; ?></h1>
                    <div>
						<?php echo $lang->stats->neuesarmband->ideingeben->$lng; ?>
						<form action="login" method="get">
							<input name="registerbr" type="text" maxlength="6" size="6" pattern="[0-9]{6}" title="6 Ziffern" placeholder="ID...">
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
				if(!isset($displayed_picnr)) $displayed_picnr = 0;
?>
				<div style="width: 100%; overflow: auto;">
					<div style="width: 70%; float: left;">
						<a href="start?pic_name=<?php echo urlencode($statistics->brid2name($bracelets_displayed[$i]).''); ?>&amp;picid=<?php echo $stats[$i][$displayed_picnr]['picid']; ?>&amp;last_pic=last&amp;delete_pic=true" class="delete_button float_right delete_bild" style="margin-top: 2em;" title="<?php echo $lang->pictures->deletepic->$lng; ?>" data-bracelet="<?php echo $braceName; ?>" onclick="confirmDelete('dasBild', this); return false;">X</a>
						<a href="pictures/bracelets/pic<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][$displayed_picnr]['picid'].'.'.$stats[$i][$displayed_picnr]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i][$displayed_picnr]['city'].', '.$stats[$i][$displayed_picnr]['country']; //onclick="return confirmDelete('dasBild');" ?>" class="thumb_link">
							<img src="img/triangle.png" alt="" class="thumb_triangle">
							<img src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][$displayed_picnr]['picid'].'.jpg'; ?>" alt="<?php echo $stats[$i][$displayed_picnr]['city'].', '.$stats[$i][$displayed_picnr]['country']; ?>" class="thumbnail">
						</a>
						<table class="pic-info">
							<tr>
								<th><?php echo $lang->pictures->datum->$lng; ?></th>
								<td><?php echo date('d.m.Y H:i', $stats[$i][$displayed_picnr]['date']). ' '. $lang->misc->uhr->$lng; ?></td>
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
							<span class="pseudo_link toggle_comments" id="toggle_comment<?php echo $i;?>" data-counts="<?php echo count($stats[$i][$displayed_picnr])-11; ?>"><?php echo $lang->misc->comments->showcomment->$lng; ?> (<?php echo count($stats[$i][$displayed_picnr])-11; ?>)</span>
						</p>
					</div>
					<aside class="bracelet-props side_container">
						<table>
							<tr>
								<td><strong><?php echo $lang->pictures->armband->$lng; ?></strong></td>
								<td><strong><?php echo '<a href="armband?name='.urlencode($statistics->brid2name($bracelets_displayed[$i])).'">'.htmlentities($statistics->brid2name($bracelets_displayed[$i])).'</a>'; ?></strong></td>
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
								<td><?php echo $stats[$i][$displayed_picnr]['picid']; ?></td>
							</tr>
						</table>
					</aside>
				</div>
				<div class="comments" id="comment<?php echo $i;?>">
<?php
				for ($j = 1; $j <= count($stats[$i][$displayed_picnr])-11; $j++) {
					//Vergangene Zeit seit dem Kommentar berechnen
					$x_days_ago = days_since($stats[$i][$displayed_picnr][$j]['date']);
					//Überprüfen, ob das Kommentar, was man löschen will das letzte ist.
					if(isset($stats[$i][$displayed_picnr][$j + 1]['commid'])) {
						$last_comment = 'middle';
					}else {
						$last_comment = 'last';
					}
?>
					<a href="start?last_comment=<?php echo $last_comment; ?>&amp;commid=<?php echo $stats[$i][$displayed_picnr][$j]['commid']; ?>&amp;picid=<?php echo $stats[$i][$displayed_picnr][$j]['picid']; ?>&amp;comm_name=<?php echo urlencode($statistics->brid2name($bracelets_displayed[$i])); ?>&amp;delete_comm=true" class="delete_button float_right delete_comment" title="<?php echo $lang->pictures->deletepic->$lng; ?>" data-bracelet="<?php echo $braceName; ?>" onclick="confirmDelete('denKommentar', this); return false;">X</a>
					<strong><?php echo $stats[$i][$displayed_picnr][$j]['user']; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][$displayed_picnr][$j]['date']).')';//onclick="return confirmDelete('denKommentar');" ?>
                    <p><?php echo $stats[$i][$displayed_picnr][$j]['comment']; ?></p> 
                    <hr style="border: 1px solid white;">  
<?php 
				}
?>   
					<form name="comment[<?php echo $i; ?>]" class="comment_form" action="start" method="post">
						<?php echo $lang->misc->comments->kommentarschreiben->$lng; ?><br>
						<label <?php if($user->login) echo 'style="display: none; " ';?>for="comment_user[<?php echo $i; ?>]" class="label_comment_user"><?php echo $lang->misc->comments->name->$lng; ?> </label>
						<input <?php if($user->login) echo 'type="hidden"'; else echo 'type="text"';?> name="comment_user[<?php echo $i; ?>]" <?php if($user->login == true) echo ' value="'.$user->login.'" ';?>id="comment_user[<?php echo $i; ?>]" class="comment_user" size="20" maxlength="15" <?php if (isset($user->login)){echo 'value="'.$user->login.'" ';} ?>placeholder="Name" pattern=".{4,15}" title="<?php echo $lang->misc->comments->minmax415->$lng; ?>" required><?php if(!$user->login) echo '<br>'; ?>
						<label for="comment_content[<?php echo $i; ?>]" class="label_comment_content"><?php echo $lang->misc->comments->deinkommentar->$lng; ?></label><br>
						<textarea name="comment_content[<?php echo $i; ?>]" id="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
						<input type="hidden" name="comment_brid[<?php echo $i; ?>]" value="<?php echo $bracelets_displayed[$i];?>">
						<input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i][$displayed_picnr]['picid']; ?>">
						<input type="hidden" name="comment_form" value="<?php echo $i; ?>">
						<input type="submit" name ="comment_submit[<?php echo $i; ?>]" value="<?php echo $lang->misc->comments->comment_button->$lng; ?>" class="submit_comment">
					</form>
				</div>
                 
<?php
					if($displayed_picnr < $displayed_brids[$bracelets_displayed[$i]]) $displayed_picnr++;
						else $displayed_picnr = 0;
					if ($i < count($bracelets_displayed)) {
?>
<!--~~~HR~~~~--><hr style="clear: both;">
<?php
					}
				}
?>
		</article>