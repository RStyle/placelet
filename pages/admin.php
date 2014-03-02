<?php
if($user->admin && $checklogin) {
?>
			<article id="kontakt" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page];?></h1></div>
				<ul>
					<li><a href="admin?comments">Kommentare verwalten</a></li>
					<li><a href="admin?pictures">Bilder verwalten</a></li>

				</ul>
<?php
	if(isset($admin_stats)) {
		if(isset($_GET['comments'])) {
			if(count($admin_stats['spam_comments']) != 0) {
?>
				<table border="1">
					<tr>
						<th>Benutzername</th>
						<th>Armband-Name<br>
							(ID bei Mouseover)</th>
						<th>Kommentar</th>
						<th>Spam</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
<?php
				for($i = 0; $i < count($admin_stats['spam_comments']); $i++) {
					$admin_stats['spam_comments'][$i]['name'] = $statistics->brid2name($admin_stats['spam_comments'][$i]['brid']);

?>
					<tr>
						<td><?php echo htmlentities($admin_stats['spam_comments'][$i]['user']); ?></td>
						<td><a href="armband?name=<?php echo urlencode($admin_stats['spam_comments'][$i]['name']); ?>" title="<?php echo $admin_stats['spam_comments'][$i]['brid']; ?>"><?php echo htmlentities($admin_stats['spam_comments'][$i]['name']); ?></a></td>
						<td><?php echo $admin_stats['spam_comments'][$i]['comment']; ?></td>
						<td><?php echo $admin_stats['spam_comments'][$i]['spam']; ?>x</td>
						<td><a href="admin?comments&delete_comm=true&commid=<?php echo $admin_stats['spam_comments'][$i]['commid']; ?>&picid=<?php echo $admin_stats['spam_comments'][$i]['picid']; ?>&name=<?php echo urlencode($admin_stats['spam_comments'][$i]['name']); ?>">Kommentar löschen</a></td>
						<td><a href="admin?comments&nospam=true&commid=<?php echo $admin_stats['spam_comments'][$i]['commid']; ?>&picid=<?php echo $admin_stats['spam_comments'][$i]['picid']; ?>&name=<?php echo urlencode($admin_stats['spam_comments'][$i]['name']); ?>">Kein Spam</a></td>
					</tr>
<?php
				}
?>
				</table>
<?php
			}else {
?>
				<p>Es gibt keine als Spam markierten Kommentare.</p>
<?php
			}
		}elseif(isset($_GET['pictures'])) {
			if(count($admin_stats['spam_pics']) != 0) {
?>
				<table border="1">
					<tr>
						<th>Benutzername</th>
						<th>Armband-Name<br>
							(ID bei Mouseover)</th>
						<th>Titel</th>
						<th>Bild</th>
						<th>Spam</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
<?php
				for($i = 0; $i < count($admin_stats['spam_pics']); $i++) {
					$admin_stats['spam_pics'][$i]['name'] = $statistics->brid2name($admin_stats['spam_pics'][$i]['brid']);
?>
					<tr>
						<td><?php echo htmlentities($admin_stats['spam_pics'][$i]['user']); ?></td>
						<td><a href="armband?name=<?php echo urlencode($admin_stats['spam_pics'][$i]['name']); ?>" title="<?php echo $admin_stats['spam_pics'][$i]['brid']; ?>"><?php echo htmlentities($admin_stats['spam_pics'][$i]['name']); ?></a></td>
						<td><?php echo htmlentities($admin_stats['spam_pics'][$i]['title']); ?></td>
						<td>
							<a href="pictures/bracelets/pic<?php echo '-'.$admin_stats['spam_pics'][$i]['brid'].'-'.$stats[$i]['picid'].'.'.$admin_stats['spam_pics'][$i]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $admin_stats['spam_pics'][$i]['city'].', '.$admin_stats['spam_pics'][$i]['country']; ?>" class="thumb_link">
								<img src="pictures/bracelets/thumb<?php echo '-'.$admin_stats['spam_pics'][$i]['brid'].'-'.$admin_stats['spam_pics'][$i]['picid'].'.jpg'; ?>" alt="<?php echo $admin_stats['spam_pics'][$i]['city'].', '.$admin_stats['spam_pics'][$i]['country']; ?>" class="thumbnail">
							</a>
						</td>
						<td><?php echo htmlentities($admin_stats['spam_pics'][$i]['spam']); ?>x</td>
						<td><a href="admin?pictures&delete_pic=true&picid=<?php echo $admin_stats['spam_pics'][$i]['picid']; ?>&name=<?php echo urlencode($admin_stats['spam_pics'][$i]['name']); ?>">Bild löschen</a></td>
						<td><a href="admin?pictures&nospam=true&commid=0&picid=<?php echo $admin_stats['spam_pics'][$i]['picid']; ?>&name=<?php echo urlencode($admin_stats['spam_pics'][$i]['name']); ?>">Kein Spam</a></td>
					</tr>
<?php
				}
?>
				</table>
<?php
			}else {
?>
				<p>Es gibt keine als Spam markierten Bilder.</p>
<?php
			}
		}
	}
?>
			</article>
<?php
}else {
?>
			<article id="kontakt" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page];?></h1></div>
				<p>Die Admin-Seite kann nur von einem Admin aufgerufen werden.</p>
			</article>
<?php
}
?>