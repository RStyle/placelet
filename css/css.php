<?php
header('Content-type: text/css');
header ("cache-control: must-revalidate; max-age: 2592000");
header ("expires: " . gmdate ("D, d M Y H:i:s", time() + 2592000) . " GMT");
ob_start("ob_gzhandler");

//ty http://phpperformance.de/performancegewinn-durch-virtuelles-javascript-file/ + http://www.phpgangsta.de/externe-javascript-dateien-zusammenfassen

$str_ouptput;
$str_output = file_get_contents('main.css');
$str_output .= file_get_contents('lightbox.css');

// Remove single line comments
//$str_output = preg_replace('#//.*#', '', $str_output);
 
// Remove line breaks and indents
//$str_output = preg_replace('#//.*#', '', $str_output);
//$str_output = preg_replace('#\n|\r\n|\r|\t#', '', $str_output);
//$str_output = str_replace("\n", '', $str_output);
$str_output = preg_replace("/\s+/", " ", $str_output);
$str_output = str_replace(array("\r\n", "\r", "\n"), '', $str_output);
$str_output = str_replace('; ', ';', $str_output);
$str_output = str_replace('*/ ', '*/', $str_output);
$str_output = str_replace(' /* ', '/*', $str_output);
//$str_output = preg_replace('#\n|\r\n|\r|\t#', '', $output);*/

echo $str_output;
?>