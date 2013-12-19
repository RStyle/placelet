<?php
$page='kontakt';
require_once('./init.php');
/*---------------------------------------------------------*/
if(isset($_POST['submit'])) {
	$send_email = send_email($_POST['sender'], $_POST['subject'], $_POST['content'], $_POST['mailer']);
}
if (isset($send_email)) {
	echo '<script type="text/javascript">
			//$(document).ready(function(){
				alert("'.$send_email.'");
			//});
		  </script>';
}
/*---------------------------------------------------------*/
require_once('./index.php');
//$page zeigt die Seite an und durch das einbinden der index.php Datei wird einfach alles über die index.php Datei geregelt, die GET und POST Variablen bleiben unverändert und werden automatisch übermittelt
?>