<?php
$page = 'armband';
if (isset($_GET['id'])) {
	$braceID = $_GET['id'];
}
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>