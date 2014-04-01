<?php
$page = 'home';
//-------
	$eecho = '';
	$data = getlnlt();
	$size = '295x400';
	$central = '0, 0';
	$max = array(false, false, false, false, 0);
	$i = 0;
	foreach($data as $pos){ 
		if($pos['latitude'] < $max[0] || $max[0] == false)
			$max[0] = $pos['latitude'];
		if($pos['latitude'] > $max[1] || $max[1] == false)
			$max[1] = $pos['latitude'];
		if($pos['longitude'] < $max[2] || $max[2] == false)
			$max[2] = $pos['longitude'];
		if($pos['longitude'] > $max[3] || $max[3] == false)
			$max[3] = $pos['longitude'];
		$eecho.= '|'.substr($pos['latitude'], 5).'|'.substr($pos['longitude'], 5); 
	}
	
	$central = ($max[0]+($max[1]-$max[0])/2) . ', ' . ($max[2]+($max[3]-$max[2])/2);
	$max[4] = ($max[1]-$max[0]);
	if(($max[3]-$max[2]) > $max[4])
		$max[4] = ($max[3]-$max[2]);
		
	$zoom = 1;
	if($max[4] < 0.02)
		$zoom = 14;
	else if($max[4] < 0.0625)
		$zoom = 12;
	else if($max[4] < 0.125)
		$zoom = 11;
	else if($max[4] < 0.25)
		$zoom = 10;
	else if($max[4] < 0.5)
		$zoom = 9;
	else if($max[4] < 1)
		$zoom = 8;
	else if($max[4] < 2)
		$zoom = 7;
	else if($max[4] < 5)
		$zoom = 6;
	else if($max[4] < 6.5)
		$zoom = 5;
	else if($max[4] < 18)
		$zoom = 4;
	else if($max[4] < 40)
		$zoom = 3;
	else if($max[4] < 80)
		$zoom = 2;

	$googlemapsurl = 'http://maps.googleapis.com/maps/api/staticmap?center='.$central.'&zoom='.$zoom.'&size='.$size.'&markers='.$eecho.'&sensor=false;';
//---------
require_once('./init.php');
/*---------------------------------------------------------*/
if(isset($_GET['regstatuschange']) && isset($_GET['regstatuschange_user'])){
	$regstatus_change = $user->regstatuschange($_GET['regstatuschange'], $_GET['regstatuschange_user']);
	if($regstatus_change) {
		$js .= 'alert("'.$lang->php->regstatuschange->wahr->$lng.'");';
	}elseif(!$regstatus_change) {
		$js .= 'alert("'.$lang->php->regstatuschange->falsch->$lng.'");';
	}
}
$systemStats = $statistics->systemStats(0, 3);
//hier werden die Armbänder bestimmt, die angezeigt werden
$bracelets_displayed = $systemStats['recent_brids'];
foreach($bracelets_displayed as $key => $val) {
	$stats[$key] = array_merge($statistics->bracelet_stats($val), $statistics->picture_details($val));
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>