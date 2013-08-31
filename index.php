<?php
include_once('./other/functions.php'); //Einbinden einer Datei, welche verschiedene PHP-Funktionen bereitstellt, wie z.B. eine Überprüfung, ob die hochgeladene Datei wirklich ein Bild ist
//Hier werden Cookies überprüft gesetzt usw.

//--//
$title = 'Pracelet';  // Kann von anderen Dateien geändert werden, z.B. in "Pracelet Shop"



//Ich denke, dass der Head immer gleich sein wird, auf Wunsch kann das aber geändert werden
echo'<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<meta name="description" content="Placelet shop and image service">
<meta name="keywords" content="Placelet, Placelet Shop, Global Bracelet, Travel & Connect, Global Bracelet. Travel & Connect, Travel and Connect, Global Bracelet. Travel and Connect">
<meta name="author" content="Roman S., Danial S., Julian Z.">
<title>'.$title.'</title>
</head>
<body>
';
echo $page; //zu Testzwecken
include_once('./pages/'.$page.'.php');

echo'</body>
</html>';
?>