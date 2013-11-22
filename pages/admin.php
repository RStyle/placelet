<?php
if($user->admin && $checklogin) {
	//Kommentar löschen
	if(isset($_GET['delete_comm']) && isset($_GET['commid']) && isset($_GET['picid']) && isset($_GET['name'])) {
		$comment_deleted = $statistics->manage_comment($user->admin, 'middle', $_GET['commid'], $_GET['picid'], $statistics->name2brid(urldecode($_GET['name'])));
		if($comment_deleted === true) {
			$js .= 'alert("Kommentar erfolgreich gelöscht.");';
		}
	}
	$admin_stats = $statistics->admin_stats();
?>
			<article id="kontakt" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page];?></h1></div>
				<table border="1">
<?php
	if(isset($admin_stats)) {
		if(count($admin_stats['spam_comments']) != 0) {
?>
					<tr>
						<th>Benutzername</th>
						<th>Armband-ID</th>
						<th>Kommentar</th>
						<th>&nbsp;</th>
					</tr>
<?php
			for($i = 0; $i < count($admin_stats['spam_comments']); $i++) {
?>
					<tr>
						<td><?php echo $admin_stats['spam_comments'][$i]['user']; ?></td>
						<td><a href="armband?name=<?php echo urlencode($statistics->brid2name($admin_stats['spam_comments'][$i]['brid'])); ?>" title="<?php echo $admin_stats['spam_comments'][$i]['brid']; ?>"><?php echo $statistics->brid2name($admin_stats['spam_comments'][$i]['brid']); ?></a></td>
						<td><?php echo $admin_stats['spam_comments'][$i]['comment']; ?></td>
						<td><a href="admin?delete_comm=true&commid=<?php echo $admin_stats['spam_comments'][$i]['commid']; ?>&picid=<?php echo $admin_stats['spam_comments'][$i]['picid']; ?>&name=<?php echo urlencode($statistics->brid2name($admin_stats['spam_comments'][$i]['brid'])); ?>">Kommentar löschen</a></td>
					</tr>
<?php
			}	
		}else {
?>
					<tr>
						<td>Es gibt keine als Spam markierten Kommentare.</td>
					</tr>
<?php
		}
	}
?>
				</table>
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