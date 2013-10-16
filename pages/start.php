<?php
	
//Kommentare schreiben
if (isset($_POST['comment_submit'])) {
	$write_comment = User::write_comment ($_POST['comment_brid'][$_POST['comment_form']],
						 $_POST['comment_user'][$_POST['comment_form']],
						 $_POST['comment_content'][$_POST['comment_form']],
						 $_POST['comment_picid'][$_POST['comment_form']],
						 $db,
						 $user);
}
if (isset($write_comment)) {
	echo '<script type="text/javascript">
			//$(document).ready(function(){
				alert("'.$write_comment.'");
			//});
		  </script>';
}

$systemStats = User::systemStats($db);
//hier werden die Armbänder bestimmt, die angezeigt werden
$bracelets_displayed[1] = 111;
$stats[1] = array_merge($user->bracelet_stats($bracelets_displayed[1]), $user->picture_details($bracelets_displayed[1]));

$bracelets_displayed[2] = 222;
$stats[2] = array_merge($user->bracelet_stats($bracelets_displayed[2]), $user->picture_details($bracelets_displayed[2]));
?>
		<article id="placelet_stats">
		    <h1 style="clear: both">Community-Statistiken</h1>  
            <table>
            	<tr>
                	<th>registrierte Armbänder</th>
                	<td><?php echo $systemStats['total_registered'].' von '.$systemStats['total']; ?></td>
                </tr>
                <tr>
                    <th>verschiedene Städte</th>
                    <td><?php echo $systemStats['city_count']; ?></td>
                </tr>
                <tr>
                    <th>beliebteste Stadt</th>
                    <td><?php echo $systemStats['most_popular_city']['city'].' ('.$systemStats['most_popular_city']['number'].')'; ?></td>
                </tr>
                <!--<tr>
                    <th>Benutzer mit den meisten Armbändern</th>
                    <td><?php echo $systemStats['user_most_bracelets']['user'].' ('.$systemStats['user_most_bracelets']['number'].')'; ?></td>
                </tr>  -->
                <tr>
                	<th>Armband mit den meisten Bildern</th>
                    <td><a href="armband?id=<?php echo $systemStats['bracelet_most_cities']['brid']; ?>"><?php echo $systemStats['bracelet_most_cities']['brid'].' ('.$systemStats['bracelet_most_cities']['number'].')'; ?></a></td>
                </tr>
            </table>
            <table>
            	<tr>
                    <th rowspan="4">Topusers</th>
                    <td style="border-bottom: 1px solid #000;">Benutzername |</td>
                    <td style="border-bottom: 1px solid #000;">Armbänder |</td>
                    <td style="border-bottom: 1px solid #000;">Uploads</td>
                </tr>
                <tr>
                    <td>Rstyle</td>
                    <td>3</td>
                    <td>10</td>
                </tr>
                <tr>
                    <td>Derp</td>
                    <td>4</td>
                    <td>13</td>
                </tr>
                <tr>
                    <td>test1</td>
                    <td>3</td>
                    <td>10</td>
                </tr>
            </table> 
            <hr style="border: none; height: 0px; border-bottom: 0; clear: both;">
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
                            <th>Uploader</th>
                            <td><?php echo $stats[$i][0][1]['user']; ?></td>
                        </tr>
                    	<tr>
                            <th>Datum</th>
                            <td><?php echo date('d.m.Y', $stats[$i][0]['date']); ?></td>
                        </tr>
                    	<tr>
                            <th>Ort</th>
                            <td><?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; ?></td>
                        </tr>
                    	<tr>
                            <th>Station Nr.</th>
                            <td><?php echo $stats[$i][0]['picid']; ?></td>
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
                            <td><strong><?php echo '<a href="armband?id='.$bracelets_displayed[$i].'">'.$bracelets_displayed[$i].'</a>'; ?></strong></td>
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
<?php
				for ($j = 1; $j <= count($stats[$i][0])-7; $j++) {
?>
                    <strong><?php echo $stats[$i][0][$j]['user']; ?></strong>, <?php echo 'vor x Tagen ('.date('H:i d.m.Y', $stats[$i][0][$j]['date']).')'; ?>
                    <p><?php echo $stats[$i][0][$j]['comment']; ?></p> 
                    <hr style="border: 1px solid white;">  
<?php 
				}
?>   
                <form name="comment[<?php echo $i; ?>]" class="comment_form" action="<?php echo $friendly_self; ?>" method="post">
                    <span style="font-family: Verdana, Times"><strong style="color: #000;">Kommentar</strong> schreiben</span><br><br>
                    <label for="comment_user[<?php echo $i; ?>]" class="label_comment_user">Name: </label>
                    <input type="text" name="comment_user[<?php echo $i; ?>]" class="comment_user" size="20" maxlength="15"<?php if (isset($user->login)){echo ' value="'.$user->login.'" ';} ?>placeholder="Name" required><br>  
                    <label for="comment_content[<?php echo $i; ?>]" class="label_comment_content">Dein Kommentar:</label><br>
                    <textarea name="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
                    <input type="hidden" name="comment_brid[<?php echo $i; ?>]" value="<?php echo $bracelets_displayed[$i];?>">
                    <input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i][0]['picid']; ?>">
                    <input type="hidden" name="comment_form" value="<?php echo $i; ?>">
                    <input type="submit" name ="comment_submit[<?php echo $i; ?>]" value="Kommentar abschicken" class="submit_comment">
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