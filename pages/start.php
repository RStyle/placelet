<?php
	$systemStats = User::systemStats($db);
//hier werden die Armbänder bestimmt, die angezeigt werden
	$bracelets_displayed[1] = 111;
	$stats[1] = array_merge($user->bracelet_stats($bracelets_displayed[1]), $user->picture_details($bracelets_displayed[1]));
	
	$bracelets_displayed[2] = 111;
	$stats[2] = array_merge($user->bracelet_stats($bracelets_displayed[1]), $user->picture_details($bracelets_displayed[1]));
?>
		<article id="placelet_stats" class="mainarticles">
        	<table>
            	<tr>
                	<td>registrierte Armbänder</td>
                	<td><?php echo $systemStats['total_registered'].' von '.$systemStats['total']; ?></td>
                </tr>
                <tr>
                    <td>verschiedene Städte</td>
                    <td><?php echo $systemStats['city_count']; ?></td>
                </tr>
                <tr>
                    <td>beliebteste Stadt</td>
                    <td><?php echo $systemStats['most_popular_city']['city'].' ('.$systemStats['most_popular_city']['number'].')'; ?></td>
                </tr>
                <tr>
                    <td>Benutzer mit den meisten Armbändern</td>
                    <td><?php echo $systemStats['user_most_bracelets']['user'].' ('.$systemStats['user_most_bracelets']['number'].')'; ?></td>
                </tr> 
                <tr>
                	<td>Armband mit den meisten Bildern</td>
                    <td><a href="armband?id=<?php echo $systemStats['bracelet_most_cities']['brid']; ?>"><?php echo $systemStats['bracelet_most_cities']['brid'].' ('.$systemStats['bracelet_most_cities']['number'].')'; ?></a></td>
                </tr>
            </table>
        </article>
        
<!-- UPLOADS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->        
        
        <div class="blue_line mainarticleheaders line_header"><h2>Neueste Bilder</h2></div>
        <article id="recent_pics" class="mainarticles bottom_border_blue">
        <?php
			for ($i = 1; $i <= count($bracelets_displayed); $i++) {
		?>
        	<div style="width: 100%;">
                <div style="width: 73%; float: left;">

                    <a href="pictures/bracelets/image-1.jpg" data-lightbox="pictures" title="Sydney, Australia" >
                        <img src="pictures/bracelets/thumb-1.jpg" alt="Sydney, Australia" class="start-picture">
                        <img src="img/triangle.png" alt="" style="margin-top: 10px; float: left">
                    </a>
                    
                    <table class="start-info">
                   		<tr>
                            <th>Uploader:</th>
                            <td><?php echo $stats[$i][0][1]['user']; ?></td>
                        </tr>
                    	<tr>
                            <th>Datum</th>
                            <td><?php echo date('d.m.Y', $stats[$i][0]['date']); ?></td>
                        </tr>
                    	<tr>
                            <th>Ort:</th>
                            <td><?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; ?></td>
                        </tr>
                    </table> 
                    
                    <p class="start-desc">
                        <span class="desc-header">Beschreibung:</span><br>
                        <?php echo $stats[$i][0]['description']; ?>      
                        <br><br>
                        <span class="pseudo_link toggle_comments" id="toggle_comment<?php echo $i;?>">Kommentare zeigen</span>
                    </p>
                    
                </div>
                <aside class="bracelet-props side_container">
                    <h1>Statistik</h1>
                    <table style="width: 100%;">
                        <tr>
                            <td><strong>Armband ID</strong></td>
                            <td><strong><?php echo '<a href="armband?id='.$bracelets_displayed[1].'">'.$bracelets_displayed[1].'</a>'; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Käufer</td>
                            <td><?php echo $stats[$i]['owner']; ?></td>
                        </tr>
                        <tr>
                            <td>Anzahl Besitzer</td>
                            <td><?php echo $stats[$i]['owners']; ?></td>
                        </tr>
                        <tr>
                            <td>Letzter Ort</td>
							<td><?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; ?></td>
                        </tr>
                    </table>
                </aside>
			</div>
            <div class="comments" id="comment<?php echo $i;?>">
                <strong><?php echo $stats[$i][0][1]['user']; ?></strong>, <?php echo 'vor x Tagen ('.date('H:i d.m.Y', $stats[$i][0][1]['date']).')'; ?>
                <p><?php echo $stats[$i][0][1]['comment']; ?></p>      
                      <form name="comment_form" class="comment_form" action="start" method="post">
                        <label style="font-family: Verdana, Times"><strong style="color: #000;">Kommentar</strong> schreiben</label><br><br>
        				  <label for="comment_name" id="label_comment_name">Name: </label>
        				  <input type="text" name="comment_name" id="comment_name" size="20" maxlength="15" placeholder="Name"><br>  
        				  <label for="comment_text" id="label_comment_text">Dein Kommentar:</label><br>
        				  <textarea name="comment_text" class="comment_text" rows="6" cols="130" maxlength="1000"></textarea><br><br>
        				  <input type="submit" value="Kommentar abschicken" id="submit_comment">
        		   	  </form> 
                    
            </div>
                 
            <?php
					if ($i < count($bracelets_displayed)) {
			?><!----HR----><hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;"><?php	
					}
				}
			?>

            <div style="clear: both; height: 0px;">
            	&nbsp;
            </div>
		</article>