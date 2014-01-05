<?php
$page = 'search';
require_once('./init.php');
/*---------------------------------------------------------*/
if(isset($_GET['squery']) && !isset($_POST['squery']))
	$_POST['squery'] = $_GET['squery'];	//Für Browsernachladen
if(isset($_POST['squery'])) {
	$js .= 'window.history.replaceState( {}, "Placelet - Suchergebnis", "/search?squery='.$_POST['squery'].'");';
	$squery = $_POST['squery'];
	if(strlen($squery) <= 18) {
		$braceID = $statistics->name2brid($squery);
		if(Statistics::userexists($_POST['squery'])) {
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
	$squery = htmlentities($squery);
	}
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>