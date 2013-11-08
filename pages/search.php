<?php
foreach($_GET as $key => $val) {
	$_GET[$key] = clean_input($val);
}
if(isset($_GET['squery'])) {
	$squery = $_GET['squery'];
	$braceID = $statistics->name2brid($squery);
	if(Statistics::userexists($_GET['squery'])) {
		$squery_result['user'] = 0;
	}else {
		$squery_result['user'] = 1;
	}
	switch ($statistics->bracelet_status($braceID)) {
		case '0':
			$squery_result['bracelet_name'] = 0;
			break;
		case 1:
			$squery_result['bracelet_name'] = 1;
			break;
		case 2:
			$braceOwner = $statistics->bracelet_stats($braceID);
			$squery_result['bracelet_name'] = 2;
			break;
	}
	switch ($statistics->bracelet_status($squery)) {
		case '0':
			$squery_result['bracelet_id'] = 0;
			break;
		case 1:
			$squery_result['bracelet_id'] = 1;
			break;
		case 2:
			$braceOwner = $statistics->bracelet_stats($squery);
			$squery_result['bracelet_id'] = 2;
			break;
	}
?>
			<article id="kontakt" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page]; ?></h1></div>
				<ul>
<?php
	switch($squery_result['user']) {
		case 0:
?>
					<li>Es gibt einen Benutzer mit dem Namen <a href="profil?user=<?php echo $squery;?>"><strong><?php echo $squery;?></strong></a></li>
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
					<li>Armband <a href="armband?name=<?php echo $squery;?>"><strong><?php echo $squery;?></strong></a> ist auf <a href="profil?user=<?php echo $braceOwner['owner']; ?>"><strong><?php echo $braceOwner['owner']; ?></strong></a> registriert.</li>
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
						Poste <a href="login?postpic=<?php echo $squery;?>">hier</a> ein Bild 
					</li>
<?php
			break;
	}
?>
				</ul>
			</article>
<?php
}
?>