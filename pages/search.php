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
			$squery_result['bracelet'] = 0;
			break;
		case 1:
			$squery_result['bracelet'] = 1;
			break;
		case 2:
			$braceOwner = $statistics->bracelet_stats($braceID);
			$squery_result['bracelet'] = 2;
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
					<li>Es gibt einen Benutzer mit dem Namen <a href="profil?user=<?php echo $squery;?>"><?php echo $squery;?></a></li>
<?php
			break;
		case 1:
?>
					<li>Es gibt keinen Benutzer mit dem Namen <?php echo $squery;?></li>
<?php
			break;
	}
?>
<?php
	switch($squery_result['bracelet']) {
		case 0:
?>
					<li>Es gibt kein Armband mit dem Namen <?php echo $squery;?></li>
<?php
			break;
		case 1:
?>
					<li>Armband <?php echo $squery;?> ist noch nicht registriert worden.</li>
<?php
			break;
		case 2:
?>
					<li>Armband <a href="armband?name=<?php echo $squery;?>"><?php echo $squery;?></a> ist auf <a href="profil?user=<?php echo $braceOwner['owner']; ?>"><?php echo $braceOwner['owner']; ?></a> registriert.</li>
<?php
			break;
	}
?>
				</ul>
			</article>
<?php
}
?>