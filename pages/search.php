<?php
if(isset($_POST['squery'])) {
	if(strlen($squery) <= 18) {
		$squery_href = urlencode($squery);
?>
			<article id="kontakt" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page]; ?></h1></div>
				<ul>
<?php
		switch($squery_result['user']) {
			case 0:
?>
					<li>Es gibt einen Benutzer mit dem Namen <a href="profil?user=<?php echo $squery_href;?>"><strong><?php echo $squery;?></strong></a></li>
<?php
				break;
			case 1:
?>
					<li>Es gibt keinen Benutzer mit dem Namen <strong><?php echo $squery;?></strong></li>
<?php
				break;
		}
		switch($squery_result['bracelet_name']) {
			case 0:
?>
					<li>Es gibt kein Armband mit dem Namen <strong><?php echo $squery;?></strong></li>
<?php
				break;
			case 2:
?>
					<li>Armband <a href="armband?name=<?php echo $squery_href;?>"><strong><?php echo $squery;?></strong></a> ist auf <a href="profil?user=<?php echo $braceOwner['owner']; ?>"><strong><?php echo $braceOwner['owner']; ?></strong></a> registriert.</li>
<?php
				break;
		}
		switch($squery_result['bracelet_id']) {
			case 0:
?>
					<li>Es gibt kein Armband mit der ID <strong><?php echo $squery;?></strong></li>
<?php
				break;
			case 1:
?>
					<li>Armband mit der ID <strong><?php echo $squery;?></strong> ist noch nicht registriert worden. <a href="login?registerbr=<?php echo $squery;?>">Hier registrieren</a></li>
	<?php
				break;
			case 2:
?>
					<li>
						Armband <a href="armband?name=<?php echo urlencode($statistics->brid2name($squery));?>"><strong><?php echo $squery;?></strong></a> ist auf <a href="profil?user=<?php echo $braceOwner['owner']; ?>"><strong><?php echo $braceOwner['owner']; ?></strong></a> registriert.
						Poste <a href="login?postpic=<?php echo $squery_href;?>">hier</a> ein Bild 
					</li>
<?php
				break;
		}
	}else {
?>
			<article id="kontakt" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page]; ?></h1></div>
				<ul>
					<li>Es gibt keine Armbänder oder Benutzernamen, die länger als 18 Zeichen sind.</li>
<?php
	}
?>
				</ul>
			</article>
<?php
}
?>