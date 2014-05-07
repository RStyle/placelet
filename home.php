<?php
$page = 'home';
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
	$stats[$key] = $statistics->bracelet_stats($val, true);
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>