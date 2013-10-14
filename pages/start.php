<?php
	$armband[1] = 111;
	$stats = array_merge($user->bracelet_stats($armband[1]), $user->picture_details($armband[1]));
?>
		<article id="placelet_stats" class="mainarticles">
        	<table>
            	<tr>
                	<td>registrierte Armbänder</td>
                	<td>###</td>
                </tr>
                <tr>
                    <td>verschiedene Städte</td>
                    <td>###</td>
                </tr>
                <tr>
                    <td>beliebtester Ort</td>
                    <td>###Frankfurt, Deutschland###</td>
                </tr>
                <tr>
                    <td>Benutzer mit den meisten Armbändern</td>
                    <td>###JohnZoidberg###</td>
                </tr> 
                <tr>
                	<td>am weitesten gereistes Armband</td>
                    <td>###<a href="armband?id=516515">516515</a>###</td>
                </tr>
            </table>
        </article>
        
<!-- UPLOADS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->        
        
        <div class="blue_line mainarticleheaders line_header"><h2>Neueste Bilder</h2></div>
        <article id="recent_pics" class="mainarticles bottom_border_blue">
        	<div style="width: 100%;">
                <div style="width: 73%; float: left;">

                    <a href="pictures/bracelets/image-1.jpg" data-lightbox="pictures" title="Sydney, Australia" >
                        <img src="pictures/bracelets/thumb-1.jpg" alt="Sydney, Australia" class="start-picture">
                        <img src="img/triangle.png" alt="" style="margin-top: 10px; float: left">
                    </a>
                    
                    <table class="start-info">
                   		<tr>
                            <th>Uploader:</th>
                            <td><?php echo $stats[0][1]['user']; ?></td>
                        </tr>
                    	<tr>
                            <th>Datum</th>
                            <td><?php echo date('d.m.Y', $stats[0]['date']); ?></td>
                        </tr>
                    	<tr>
                            <th>Ort:</th>
                            <td><?php echo $stats[0]['city'].', '.$stats[0]['country']; ?></td>
                        </tr>
                    </table> 
                    
                    <p class="start-desc">
                        <span class="desc-header">Beschreibung:</span><br>
                        <?php echo $stats[0]['description']; ?>      
                        <br><br>
                        <span class="pseudo_link toggle_comments" id="toggle_comment1">Kommentare zeigen</span>
                    </p>
                    
                </div>
                <aside class="bracelet-props side_container">
                    <h1>Statistik</h1>
                    <table style="width: 100%;">
                        <tr>
                            <td><strong>Armband ID</strong></td>
                            <td><strong><?php echo '<a href="armband?id='.$armband[1].'">'.$armband[1].'</a>'; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Käufer</td>
                            <td><?php echo $stats['owner']; ?></td>
                        </tr>
                        <tr>
                            <td>Anzahl Besitzer</td>
                            <td><?php echo $stats['owners']; ?></td>
                        </tr>
                        <tr>
                            <td>Letzter Ort</td>
							<td><?php echo $stats[0]['city'].', '.$stats[0]['country']; ?></td>
                        </tr>
                    </table>
                </aside>
			</div>
            <div class="comments" id="comment1">
                <strong><?php echo $stats[0][1]['user']; ?></strong>, <?php echo 'vor x Tagen ('.date('H:i d.m.Y', $stats[0][1]['date']).')'; ?>
                <p><?php echo $stats[0][1]['comment']; ?></p>
            </div>
            
			<hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;">
<!-- NÄCHSTER UPLOAD ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->			
			
            <div style="width: 100%;">
                <div style="width: 73%; float: left;">
                    
                    <a href="pictures/bracelets/image-2.jpg" data-lightbox="pictures" title="Sydney, Australia" >
                        <img src="pictures/bracelets/thumb-2.jpg" alt="London, England" class="start-picture">
                        <img src="img/triangle.png" alt="" style="margin-top: 10px; float: left">
                    </a>
                    
                    <table class="start-info">
                   		<tr>
                       		<th>Uploader:</th>
							<td>John L.</td>
                        </tr>
                    	<tr>
                        	<th>Datum</th>
                            <td>13.10.2013</td>
                        </tr>
                    	<tr>
                            <th>Ort:</th>
                            <td>London, England</td>
                        </tr>
                    </table> 

                    <p class="start-desc">
                        <span class="desc-header">Beschreibung:</span><br/>
                        Das Reisearmband kannst du an nette Freunde oder Reisende verschenken.
                        Das Ziel ist es, das Armband so weit wie möglich reisen zu lassen. Wo ein Armband

                        Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore
                        et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
                        <br/> <br/>
                        <span class="pseudo_link toggle_comments" id="toggle_comment2">Kommentare zeigen</span>
                    </p>
                </div>
                <aside class="bracelet-props side_container">
                    <h1>Statistik</h1>
                    <table style="width: 100%;">
                        <tr>
                            <td><strong>Armband ID</strong></td>
                            <td><strong><a href="armband?id=3141592653">3141592653</a></strong></td>
                        </tr>
                        <tr>
                            <td>Käufer</td>
                            <td>Daniel Schäfer</td>
                        </tr>
                        <tr>
                            <td>Anzahl Besitzer</td>
                            <td>12</td>
                        </tr>
                        <tr>
                            <td>Letzter Standort</td>
                            <td>Berlin, Deutschland</td>
                        </tr>
                    </table>
                </aside>
			</div>
            <div class="comments" id="comment2">
                <strong>John Zoidberg</strong>, vor drei Tagen (18:00 05.10.2013)
                <p>Das Reisearmband kannst du an nette Freunde oder Reisende verschenken.
                Das Ziel ist es, das Armband so weit wie möglich reisen zu lassen. Wo ein Armband
                
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore
                et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
                Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.      <br/> <br/>
                Das Reisearmband kannst du an nette Freunde oder Reisende verschenken.
                Das Ziel ist es, das Armband so weit wie möglich reisen zu lassen. Wo ein Armband
                
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore
                et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
                Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
            </div>
            <div style="clear: both; height: 0px;">
            	&nbsp;
            </div>
		</article>