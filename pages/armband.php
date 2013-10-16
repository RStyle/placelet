<?php

//Kommentare schreiben
if (isset($_POST['comment_submit'])) {
	$write_comment = User::write_comment ($_GET['id'],
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

$stats = array_merge($user->bracelet_stats($_GET['id']), $user->picture_details($_GET['id']));
?>
        <article id="armband" class="mainarticles bottom_border_green">
			<div class="green_line mainarticleheaders line_header"><h1>Armband <?php echo $braceID; ?></h1></div>
<?php
				for ($i = 0; $i < count($stats)-4; $i++) {
?>
            <div style="float: left;">
                <h3><?php echo $stats[$i]['city'].', '.$stats[$i]['country']; ?></h3>
                <a href="pictures/bracelets/image-1.jpg" data-lightbox="pictures" title="Sydney, Australia">
                    <img src="pictures/bracelets/thumb-1.jpg" alt="Sydney, Australia" style="width: 40%; height: 300px; float: left; margin-right: 1em; margin-bottom: 1em;">
                </a>
                <?php echo date('d.m.Y', $stats[0]['date']); ?>
                <h4><?php echo $stats[$i]['title']; ?></h4>
                <p><?php echo $stats[$i]['description']; ?></p>
                <span class="toggle_comments pseudo_link" id="toggle_comment<?php echo $i;?>">Kommentare zeigen</span>
            </div>
            <div class="comments" id="comment<?php echo $i;?>">
<?php
				for ($j = 1; $j <= count($stats[$i])-7; $j++) {
?>
                    <strong><?php echo $stats[$i][$j]['user']; ?></strong>, <?php echo 'vor x Tagen ('.date('H:i d.m.Y', $stats[$i][$j]['date']).')'; ?>
                    <p><?php echo $stats[$i][$j]['comment']; ?></p> 
                    <hr style="border: 1px solid white;">  
<?php 
				}
?>   
                <form name="comment[<?php echo $i; ?>]" class="comment_form" action="<?php echo $friendly_self.'?id='.$_GET['id']; ?>" method="post">
                    <span style="font-family: Verdana, Times"><strong style="color: #000;">Kommentar</strong> schreiben</span><br><br>
                    <label for="comment_user[<?php echo $i; ?>]" class="label_comment_user">Name: </label>
                    <input type="text" name="comment_user[<?php echo $i; ?>]" class="comment_user" size="20" maxlength="15"<?php if (isset($user->login)){echo ' value="'.$user->login.'" ';} ?>placeholder="Name" required><br>  
                    <label for="comment_content[<?php echo $i; ?>]" class="label_comment_content">Dein Kommentar:</label><br>
                    <textarea name="comment_content[<?php echo $i; ?>]" class="comment_content" rows="6" maxlength="1000" required></textarea><br><br>
                    <input type="hidden" name="comment_brid[<?php echo $i; ?>]" value="<?php echo $_GET['id'];?>">
                    <input type="hidden" name="comment_picid[<?php echo $i; ?>]" value="<?php echo $stats[$i]['picid']; ?>">
                    <input type="hidden" name="comment_form" value="<?php echo $i; ?>">
                    <input type="submit" name ="comment_submit[<?php echo $i; ?>]" value="Kommentar abschicken" class="submit_comment">
                </form>
            </div>
<?php
					if ($i < count($stats)-5) {
?><!----HR----><hr style="border-style: solid; height: 0px; border-bottom: 0; clear: both;"><?php	
					}
				}
?>

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