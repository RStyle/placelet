<?php
header('Content-type: text/javascript');
header ("cache-control: must-revalidate; max-age: 2592000");
header ("expires: " . gmdate ("D, d M Y H:i:s", time() + 2592000) . " GMT");
ob_start("ob_gzhandler");

//ty http://phpperformance.de/performancegewinn-durch-virtuelles-javascript-file/ + http://www.phpgangsta.de/externe-javascript-dateien-zusammenfassen
if(isset($_GET['lang']))
	$lang = $_GET['lang'];
else
	$lang = 'de';

$str_ouptput;
$str_output = file_get_contents('exif+jssor+lightbox.js');
$str_output .= file_get_contents('lang'.$lang.'.js');
$s1 = file_get_contents('script.js');
$s = $s1;
$s = str_replace("\t", '', $s);

$s = str_replace("\r\n", "\n", $s); # windows -> linux
$s = str_replace("\r", "\n", $s); # mac -> linux
$s = str_replace('/*1*/', "secccccct", $s); # mac -> linux
$s = str_replace("})\n", "secccccccc", $s); # mac -> linux
$s = str_replace("\n", '', $s); # mac -> linux
$s = str_replace("secccccccc", "})\n", $s); # mac -> linux
$s = str_replace("secccccct", "\n", $s); # mac -> linux

// Remove single line comments
//$str_output = preg_replace('#//.*#', '', $str_output);
 
// Remove line breaks and indents
//$str_output = preg_replace('#//.*#', '', $str_output);
//$str_output = preg_replace('#\n|\r\n|\r|\t#', '', $str_output);
//$str_output = str_replace("\n", '', $str_output);
$str_output = str_replace(array("\r\n", "\r", "\n"), '', $str_output);
//$str_output = preg_replace('#\n|\r\n|\r|\t#', '', $output);*/

echo $str_output.$s;
?>