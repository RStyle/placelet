<?php
foreach($_GET as $key => $val) {
	$_GET[$key] = clean_input($val);
}
//Kommentare schreiben
if (isset($_POST['comment_submit'])) {
	$write_comment = $statistics->write_comment ($_POST['comment_brid'][$_POST['comment_form']],
						 $_POST['comment_user'][$_POST['comment_form']],
						 $_POST['comment_content'][$_POST['comment_form']],
						 $_POST['comment_picid'][$_POST['comment_form']],
						 $user);
}
if (isset($write_comment)) {
	echo '<script type="text/javascript">
			//$(document).ready(function(){
				alert("'.$write_comment.'");
			//});
		  </script>';
}
$user_anz = 3;
$systemStats = $statistics->systemStats($user_anz, 3);
//hier werden die Armbänder bestimmt, die angezeigt werden
$bracelets_displayed = $systemStats['recent_brids'];
foreach($bracelets_displayed as $key => $val) {
	$stats[$key] = array_merge($statistics->bracelet_stats($val), $statistics->picture_details($val));
}
?>
		<aside id="placelet_stats">
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
                    <td><a href="armband?name=<?php echo urlencode($systemStats['bracelet_most_cities']['name']); ?>"><?php echo $systemStats['bracelet_most_cities']['name'].' ('.$systemStats['bracelet_most_cities']['number'].')'; ?></a></td>
                </tr>
            </table>
            <table id="topusers">
            	<tr>
                    <th rowspan="4">Topusers</th>
                    <td style="border-bottom: 1px solid #000;">Benutzername</td>
                    <td style="border-bottom: 1px solid #000;">Armbänder</td>
                    <td style="border-bottom: 1px solid #000;">Uploads</td>
                </tr>
<?php
for ($i = 1; $i <= $user_anz; $i++) {
?>
                <tr>
                    <td><?php echo $systemStats['user_most_bracelets']['user'][$i]; ?></td>
                    <td><?php echo $systemStats['user_most_bracelets']['number'][$i]; ?></td>
                    <td><?php echo $systemStats['user_most_bracelets']['uploads'][$i]; ?></td>
                </tr>
<?php
}
?>
            </table>
            <hr style="border: none; height: 0px; border-bottom: 0; clear: both;">
        </aside>
        
<!-- UPLOADS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->        
        
        <div class="blue_line mainarticleheaders line_header"><h2>Neueste Bilder</h2></div>
        <article id="recent_pics" class="mainarticles bottom_border_blue">
<?php
			for ($i = 1; $i <= count($bracelets_displayed); $i++) {
?>
        	<div style="width: 100%; overflow: auto;">
                <div style="width: 70%; float: left;">

                    <a href="pictures/bracelets/pic<?php echo '-'.$bracelets_displayed[$i].'-'.$stats[$i][0]['picid'].'.'.$stats[$i][0]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[$i][0]['city'].', '.$stats[$i][0]['country']; ?>" class="thumb_link">
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
                            <td><?php echo $stats[$i][0]['user']; ?></td>
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
                            <td><strong><?php echo '<a href="armband?name='.urlencode($statistics->brid2name($bracelets_displayed[$i])).'">'.$statistics->brid2name($bracelets_displayed[$i]).'</a>'; ?></strong></td>
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
                    	<tr>
                            <td>Station Nr.</td>
                            <td><?php echo $stats[$i][0]['picid']; ?></td>
                        </tr>
                    </table>
                </aside>
			</div>
            <div class="comments" id="comment<?php echo $i;?>">
<?php
				for ($j = 1; $j <= count($stats[$i][0])-8; $j++) {
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
                    <input type="text" name="comment_user[<?php echo $i; ?>]" id="comment_user[<?php echo $i; ?>]" class="comment_user" size="20" maxlength="15"<?php if (isset($user->login)){echo ' value="'.$user->login.'" ';} ?>placeholder="Name" required><br>  
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
?><!--~~HR~~--><hr style="clear: both;"><?php	
					}
				}
?>
			<div class="pseudo_link" id="start_reload" onClick="reload_func();"  style="clear: both;" >Mehr anzeigen</div>
		</article>