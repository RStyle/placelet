		    <div id="connect_leiste">
                <div class="connect_box" id="stats_box">
                    <h1>Community-Statistiken</h1>
                    <table>
    					<tr>
    						<th>Registrierte Armbänder</th>
    						<td><?php echo $systemStats['total_registered']?></td>
    					</tr>
    					<tr>
    						<th>Verschiedene Städte</th>
    						<td><?php echo $systemStats['city_count']; ?></td>
    					</tr>
    					<tr>
    						<th>Beliebteste Stadt</th>
    						<td><?php echo $systemStats['most_popular_city']['city'].' ('.$systemStats['most_popular_city']['number'].')'; ?></td>
    					</tr>
    					<tr>
    						<th>Armband mit den meisten Bildern</th>
    						<td><a href="armband?name=<?php echo urlencode($systemStats['bracelet_most_cities']['name']); ?>"> <?php echo $systemStats['bracelet_most_cities']['name'].'('.$systemStats['bracelet_most_cities']['number'].')'; ?></a></td>
    					</tr>
    				</table>
                </div>
                <div class="connect_box" id="topusers_box">
                    <h1>Aktivste Benutzer</h1>
                    <table id="topusers">
    					<tr>
    						<td style="border-bottom: 1px solid #000;">Benutzername</td>
    						<td style="border-bottom: 1px solid #000;">Armbänder</td>
    						<td style="border-bottom: 1px solid #000;">Uploads</td>
    					</tr>
    <?php
    for ($i = 0; $i < $user_anz; $i++) {
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
                <div class="connect_box" id="submit_box">
                    <h1>+1 bild</h1>
                    <p>
						Gib deine <span>Armband-ID</span> an:
						<form action="login" method="get">
							<input name="postpic" type="text" maxlength="6" size="6" pattern="[0-9]{6}" title="6 Zahlen" placeholder="ID...">
							<input type="submit" value="Zum Upload">
						</form>
					</p>

                    <hr>

                    <h1>neues armband</h1>
                    <p>
						Gib deine <span>Armband-ID</span> an:
						<form action="login" method="get">
							<input name="registerbr" type="text" maxlength="6" size="6" pattern="[0-9]{6}" title="6 Zahlen" placeholder="ID...">
							<input type="submit" value="Armband registrieren">
						</form>
					</p>
                </div>
            </div>    
        
<!-- UPLOADS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->        
        
			<article id="recent_pics" class="mainarticles bottom_border_blue">
			<div class="blue_line mainarticleheaders line_header"><h2>Neueste Bilder</h2></div>
<?php
			for ($i = 1; $i <= count($bracelets_displayed); $i++) {
?>
				<div style="width: 100%; overflow: auto;">
					<div style="width: 70%; float: left;">
						<a href="start?pic_name=<?php echo urlencode($statistics->brid2name($bracelets_displayed[$i])); ?>&picid=<?php echo $stats[$i][0]['picid']; ?>&last_pic=last&delete_pic=true" class="delete_button float_right delete_bild" style="margin-top: 2em;" title="Bild löschen/melden" >X</a>
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
					<a href="start?last_comment=<?php echo $last_comment; ?>&commid=<?php echo $stats[$i][0][$j]['commid']; ?>&picid=<?php echo $stats[$i][0][$j]['picid']; ?>&comm_name=<?php echo urlencode($statistics->brid2name($bracelets_displayed[$i])); ?>&delete_comm=true" class="delete_button float_right delete_comment" title="Kommentar löschen/melden" >X</a>
					<strong><?php echo $stats[$i][0][$j]['user']; ?></strong>, <?php echo $x_days_ago.' ('.date('H:i d.m.Y', $stats[$i][0][$j]['date']).')';//onclick="return confirmDelete('den Kommentar');" ?>
                    <p><?php echo $stats[$i][0][$j]['comment']; ?></p> 
                    <hr style="border: 1px solid white;">  
<?php 
				}
?>   
					<form name="comment[<?php echo $i; ?>]" class="comment_form" action="start" method="post">
						<span style="font-family: Verdana, Times"><strong style="color: #000;">Kommentar</strong> schreiben</span><br><br>
						<label for="comment_user[<?php echo $i; ?>]" class="label_comment_user">Name: </label>
						<input type="text" name="comment_user[<?php echo $i; ?>]" id="comment_user[<?php echo $i; ?>]" class="comment_user" size="20" maxlength="15" <?php if (isset($user->login)){echo 'value="'.$user->login.'" ';} ?>placeholder="Name" pattern=".{4,15}" title="Min.4 - Max.15" required><br>  
						<label for="comment_content[<?php echo $i; ?>]" class="label_comment_content">Dein Kommentar:</label><br>
						<textarea name="comment_content[<?php echo $i; ?>]" id="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
						<input type="hidden" name="comment_brid[<?php echo $i; ?>]" value="<?php echo $bracelets_displayed[$i];?>">
						<input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i][0]['picid']; ?>">
						<input type="hidden" name="comment_form" value="<?php echo $i; ?>">
						<input type="submit" name ="comment_submit[<?php echo $i; ?>]" value="Kommentar abschicken" class="submit_comment">
					</form>
					</div>
                 
<?php
					if ($i < count($bracelets_displayed)) {
?>
<!--~~~HR~~~~--><hr style="clear: both;">
<?php	
					}
				}
?>
			<div class="pseudo_link" id="start_reload" onClick="reload_start(-3);"  style="clear: both;" >Vorherige Seite</div>
			<div class="pseudo_link" id="start_reload" onClick="reload_start(3);"  style="clear: both;" >Nächste Seite</div>
		</article>