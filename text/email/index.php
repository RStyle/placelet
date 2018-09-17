<?php
require_once('.././website/classes/tmfcolorparser.inc.php');
$TMFColorPraser= new TMFColorParser();
//$co->toHTML($text);
$java="";
$body="";
$head="";
$number=0;
$_GET["praser"]=strtolower(trim($_GET["praser"]));
if(file_exists("./functions.php"))include('./functions.php');
function website_exists($a){
$inhalt=@file_get_contents($a);
if($inhalt===false)
return false;
else
return true;
}

$mysql= new mysqli("localhost" , "web10" , "dertyp1", "usr_web10_1");
$aus="SELECT * FROM playerpage ORDER BY name";
$myless=$mysql->query($aus);
$manialinks=array();
while($infos=$myless->fetch_array()){
$manialinks[$infos["name"]]=array(
"name" => $infos["name"],
"link" => $infos["link"],
"type" => $infos["type"]
);
}
function ml_adresse($eingabe,$zahl=1){
global $manialinks;

$eingabe=trim($eingabe);
if($eingabe!=""){
$c=htmlspecialchars(utf8_decode($eingabe));
$b=str_replace("?",'&amp;',$c);
$a=explode('?',$c);
$a=$a[0];
//$a=strtolower($b[0]);
if($manialinks[$a]["link"]!=""){
if($zahl==1)
return 'https://manialinks.de.vu?prase='.$manialinks[$a]["link"];
else
return $manialinks[$a]["link"];
}else{
return 'tmtp:///:'.$eingabe;
}
}
}
function url_adresse($a){
$a=trim($a);
if($a!=""){
$a=strtolower(htmlspecialchars(utf8_decode($a)));
if($a[0]=="h" && $a[1]=="t" && $a[2]=="t" && $a[3]=="p"){
return $a;
}else{
return "https://".$a;
}
}
}
if(trim($_GET["praser"])==""){$_GET["praser"]="test";}
$datei=ml_adresse($_GET["praser"],0);
$datei='https://rsty.keksml.de/rsty22.php';
$xml=simplexml_load_file($datei);
$order='https://rsty.keksml.de/';
//echo $datei;
//---praser
if($xml->redirect!="" && !isset($_GET["error"])){
if($xml->redirect=="rsty")$xml->redirect=$xml->redirect."?error";
header("Location: $xml->redirect");
}
if($xml->music["data"] !=""){
$mfile=$xml->music["data"];
if(preg_match("/\.ogg$/",$mfile) or preg_match("/\.mux$/",$mfile)){
$no=array(".ogg",".mux");
$mfile=str_replace('.ogg','.mp3',$mfile);
if(!website_exists($mfile)) {
$bbcc=1;
echo $mfile;
}
}
if($bbcc!=1)$body.='<embed src="'.$mfile.'" autostart="true" loop="true" hidden="true" name="mysound" width=0 height=0 />
';
}
foreach($xml->quad as $quads){
quad($quads);
}
































echo'<html>
<head>
<style type="text/css">
.Kiste {
position: absolute;
top: 0px;
left: 0px;
bottom: 0px;
right: 0px;
overflow: hidden;
background-image:url(background.png)
}
a.menublink { text-decoration:blink; }
</style>
'.$head.'
</head>
<body>
<div class="Kiste" >
'.$body.'
</div>
<script type="text/javascript">
<!--
function func1(){
'.$java.'
}
window.setInterval("func1()", 100);
func1();
// -->
</script>
</body>
';
$mysql->close();
?>