<!--CONNECT-LEISTE-->
			<div id="connect_leiste">
                <div class="connect_box" id="uploads_box">
                    <h1 class="headers pseudo_link" id="header_1"><span class="header_arrow1 arrow_right"></span>&nbsp;<?php echo $lang->stats->neuesterupload[$lng."-title"]; ?></h1>
                        <div id="connectbox_1">
<?php
if($systemStats['total_posted'] > 1) {
?>
                            <div class="changepic pseudo_link float_right" onClick="return false" onMouseDown="javascript:change_pic('+', '1');"><img src="img/next.png" alt="next"></div>	
<?php
}
?>
                            <div id="central_newest_pic">
                            	<div class="more_imgs">
                            	    <img class="fake_img pseudo_link" src="#" alt="-"><br>
                                    <img class="fake_img pseudo_link" src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[1].'-'.$stats[1][0]['picid'].'.jpg'; ?>" alt="-"><br>
                                    <img class="fake_img pseudo_link" src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[2].'-'.$stats[2][0]['picid'].'.jpg'; ?>" alt="-" onMouseDown="javascript:change_pic('+', 1);">
        					   </div>
                                                           			
        						<a href="pictures/bracelets/pic<?php echo '-'.$bracelets_displayed[1].'-'.$stats[1][0]['picid'].'.'.$stats[1][0]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[1][0]['city'].', '.$stats[1][0]['country']; ?>" class="connect_thumb_link">							
        						<img src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[1].'-'.$stats[1][0]['picid'].'.jpg'; ?>" alt="<?php echo $stats[1][0]['city'].', '.$stats[1][0]['country']; ?>" class="connect_thumbnail" style="max-height: 175px;">
        						</a>
        							<table class="connect_pic-info">
        								<tr>
        									<th><?php echo $lang->pictures->armband->$lng; ?></th>
        									<td><strong><?php echo '<a href="armband?name='.urlencode($statistics->brid2name($bracelets_displayed[1])).'">'.htmlentities($statistics->brid2name($bracelets_displayed[1])).'</a>'; ?></strong></td>
        								</tr>
        								<tr>
        									<th><?php echo $lang->pictures->datum->$lng; ?></th>
        									<td><?php echo date('d.m.Y H:i', $stats[1][0]['date'])." ".$lang->misc->uhr->$lng; ?></td>
        								</tr>
        								<tr>
        									<th><?php echo $lang->pictures->ort->$lng; ?></th>
        									<td><?php echo $stats[1][0]['city'].', '.$stats[1][0]['country']; ?></td>
        								</tr>
                                    	<?php
                                    				if($stats[1][0]['user'] != NULL) {
                                    	?>
        								<tr>
        									<th><?php echo $lang->pictures->uploader->$lng; ?></th>
        									<td><a href="profil?user=<?php echo urlencode(html_entity_decode($stats[1][0]['user'])); ?>"><?php echo $stats[1][0]['user']; ?></a></td>
        								</tr>
                                    	<?php
                                    				 }
                                    	?>
        							</table>
    					    </div>	
    						<!--<img src="img/loading.gif" id="loading" alt="loading..." style="display: block; margin: 0 auto; display: none; right: 25.5%;">    -->
    					</div>
                </div>
                <div class="connect_box" id="submit_box">
                    <h1 class="headers pseudo_link" id="header_2"><span class="header_arrow2 arrow_right"></span>&nbsp;<?php echo $lang->stats->neuesbild[$lng."-title"]; ?></h1>
                    <div id="connectbox_2">
                        <form action="login" method="get">
                            <p>
        						<?php echo $lang->stats->neuesbild->ideingeben->$lng; ?><br>						
    							<input name="postpic" type="text" maxlength="6" size="6" pattern="[0-9]{6}" title="6 Ziffern" placeholder="ID...">
    							<input type="submit" value="<?php echo $lang->stats->neuesbild->button->$lng; ?>">    						
        					</p>
    					</form>
    					
                        <hr>
                        
                        <h1><?php echo $lang->stats->neuesarmband[$lng."-title"]; ?></h1>
                        <form action="login" method="get">
                        <p>
    						<?php echo $lang->stats->neuesarmband->ideingeben->$lng; ?><br>
    						<input name="registerbr" type="text" maxlength="6" size="6" pattern="[0-9]{6}" title="6 Ziffern" placeholder="ID...">
    						<input type="submit" value="<?php echo $lang->stats->neuesarmband->button->$lng; ?>">						 
    					</p>
    					</form>
    				</div>
                </div>
            </div>

<!--ERSTER ARTIKEL-->
			<article id="reisearmband" class="mainarticles bottom_border_blue">
				<div class="mainarticleheaders line_header blue_line"><h1><?php echo $lang->home->artikel1[$lng."-title"]; ?></h1></div>
				 <?php if(!isset($_GET['rund'])) echo '<a href="pictures/armband2.jpg" data-lightbox="armbaender" title="Armband"><img src="/pictures/thumb-armband2.jpg" alt="Armband" style="width: 100%;"></a>'; else echo '<div class="round_image" style="margin-bottom: 0.5em; background: url(/pictures/thumb-armband2.jpg)"></div>';?>				
				<p>
                    <?php echo $lang->home->artikel1->paragraph[0]->$lng; ?>
				</p>
				<p>
                    <?php echo $lang->home->artikel1->paragraph[1]->$lng; ?>
                     
				</p>
			</article>                                                                                                        
<!--ZWEITER ARTIKEL-->
			<article id="kollektion" class="mainarticles bottom_border_green">
				<div class=" mainarticleheaders line_header green_line"><h1><?php echo $lang->home->artikel2[$lng."-title"]; ?></h1></div>
				<!--<?php if(!isset($_GET['rund'])) echo '<a href="pictures/armband.jpg" data-lightbox="armbaender" title="Armband"><img src="/pictures/thumb-armband.jpg" style="width: 100%;" alt="Armband"></a>'; else echo '<div class="round_image" style="margin-bottom: 0.5em; background: url(/pictures/thumb-armband.jpg)"></div>';?>-->
				<div class="responsive_16-9" style="margin-bottom: 1em;"><iframe width="560" height="315" src="//www.youtube.com/embed/xtnbzTK2G8I" style="border: none"></iframe></div>
<?php
for($i = 0; $i < 6; $i++){
?>
				<p>
					<span class="highlighted kollektion_numbers"><?php echo $i+1; ?></span><?php echo $lang->home->artikel2->paragraph[$i]->$lng; ?>
				</p>
				<span class="arrow highlighted">&#11015;</span>
<?php
}
?>
				<br><?php echo $lang->home->artikel2->articlefooter->$lng; ?>
			</article>
<!--SIDEBAR-->
			<aside class="side_container">
				<!--<h1>JUNIOR</h1>-->
				<img src="img/JUNIOR_Logo.png" alt="JUNIOR" style="width: 80%; height: auto;">
				<p>Das Unternehmen Placelet entstand durch das Projekt JUNIOR der Institut der deutschen Wirtschaft Köln JUNIOR gGmbH. JUNIOR wird 
				auf Bundesebene durch das Bundesministerium für Wirtschaft und Technologie, die KfW Mittelstandsbank, Gesamtmetall, dem Handelsblatt, Danfoss, Deloitte, der AXA Versicherung und Fed 
				Ex gefördert.</p>
			</aside>
			<aside class="side_container" style="margin-top: 20px;">
				<h1>facebook</h1>
				<div id="fb_plugin" class="fb-like-box" data-href="http://www.facebook.com/Placelet" data-width="200" data-height="190" data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="false" data-show-border="true"></div>
			</aside>
			<aside id="map_home" class="side_container" style="margin-top: 20px;">
				Bitte aktivieren sie Javascript um die Weltkarte mit allen Orten sehen zu können.
			</aside>