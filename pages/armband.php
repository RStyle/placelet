<?php
	//$stats = $user->bracelet_details($_GET['id']);
	$stats = array_merge($user->bracelet_stats($_GET['id']), $user->picture_details($_GET['id']));
?>
        <article id="armband" class="mainarticles bottom_border_green">
			<div class="green_line mainarticleheaders line_header"><h1>Armband <?php echo $braceID; ?></h1></div>
			<?php //print_r ($stats); ?>
            <div>
                <h3><?php echo $stats[0]['city'].', '.$stats[0]['country']; ?></h3>
                <a href="pictures/bracelets/image-1.jpg" data-lightbox="pictures" title="Sydney, Australia">
                    <img src="pictures/bracelets/thumb-1.jpg" alt="Sydney, Australia" style="width: 40%; height: 300px; float: left; margin-right: 1em; margin-bottom: 1em;">
                </a>
                <?php echo date('d.m.Y', $stats[0]['date']); ?>
                <h4><?php echo $stats[0]['title']; ?></h4>
                <p><?php echo $stats[0]['description']; ?></p>
                <span class="toggle_comments pseudo_link" id="toggle_comment1">Kommentare zeigen</span>
            </div>
            <div style="clear: both; display: none; color: black;" id="comment1">
                <strong><?php echo $stats[0][1]['user']; ?></strong>, <?php echo 'vor x Tagen ('.date('H:i d.m.Y', $stats[0][1]['date']).')'; ?>
                <p><?php echo $stats[0][1]['comment']; ?></p>
            </div>
            
<!----HR---><hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;">
            <div style="float: left;">
            
                <h3><?php echo $stats[1]['city'].', '.$stats[1]['country']; ?></h3>
                <a href="pictures/bracelets/image-2.jpg" data-lightbox="pictures" title="London, England">
                    <img src="pictures/bracelets/thumb-2.jpg" alt="London, England" style="width: 40%; height: 300px; float: left; margin-right: 1em; margin-bottom: 1em;">
                </a>
               <?php echo date('d.m.Y', $stats[1]['date']); ?>
                <h4><?php echo $stats[1]['title']; ?></h4>
                <p><?php echo $stats[1]['description']; ?></p>
                <span class="toggle_comments pseudo_link" id="toggle_comment2">Kommentare zeigen</span>
            </div>
            <div style="clear: both; display: none; color: black;" id="comment2">
                <strong><?php echo $stats[1][1]['user']; ?></strong>, <?php echo 'vor x Tagen ('.date('H:i d.m.Y', $stats[1][1]['date']).')'; ?>
                <p><?php echo $stats[1][1]['comment']; ?></p>
            </div>
            
<!----HR---><hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;">
            <div style="float: left;">
            
                <h3><?php echo $stats[1]['city'].', '.$stats[1]['country']; ?></h3>
                <a href="pictures/bracelets/image-2.jpg" data-lightbox="pictures" title="London, England">
                    <img src="pictures/bracelets/thumb-2.jpg" alt="London, England" style="width: 40%; height: 300px; float: left; margin-right: 1em; margin-bottom: 1em;">
                </a>
               <?php echo date('d.m.Y', $stats[1]['date']); ?>
                <h4><?php echo $stats[1]['title']; ?></h4>
                <p><?php echo $stats[1]['description']; ?></p>
                <span class="toggle_comments pseudo_link" id="toggle_comment3">Kommentare zeigen</span>
            </div>
            <div style="clear: both; display: none; color: black;" id="comment3">
                <strong><?php echo $stats[2][1]['user']; ?></strong>, <?php echo 'vor x Tagen ('.date('H:i d.m.Y', $stats[2][1]['date']).')'; ?>
                <p><?php echo $stats[2][1]['comment']; ?></p>
            </div>
		</article>
        <aside class="side_container" id="bracelet_props">
            <h1>Statistik</h1>
            <table style="width: 100%;">
                <tr>
                    <td><strong>Armband ID</strong></td>
                    <td><strong><?php echo $braceID; ?></strong></td>
                </tr>
                <tr>
                    <td>Käufer</td>
                    <td><?php echo $stats['owner']; ?></td>
                </tr>
                <tr>
                    <td>Registriert am</td>
                    <td><?php echo date('d.m.Y', $stats['date']); ?></td>
                </tr>
                <tr>
                    <td>Anzahl Besitzer</td>
                    <td><?php echo $stats['owners']; ?></td>
                </tr>
                <tr>
                    <td>Letzter Ort</td>
                    <td>Sydney, Australia</td>
                </tr>
            </table>
        </aside>