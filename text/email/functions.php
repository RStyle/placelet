<?php
function quad($quad){
global $java;
global $body;
global $number;
global $order;

$quad["image"]=str_replace('./',$order,$quad["image"]);
$quad["imagefocus"]=str_replace('./',$order,$quad["imagefocus"]);


if(!empty($quad["manialink"])){
$quad["link"]=ml_adresse($quad["manialink"]);
}
elseif(!empty($quad["url"])){
$quad["link"]=url_adresse($quad["url"]);
}
//if(preg_match("/\.bik$/",$quad["image"])
$quad["image"]=str_replace(".bik",".gif",$quad["image"]);
$quad["imagefocus"]=str_replace(".bik",".gif",$quad["imagefocus"]);

if(!empty($quad["posn"])){
$p=explode(" ",$quad["posn"]);
$p1=$p[0]+0;
$p2=$p[1]+0;
$p3=$p[2]+48;
if($p3>96) $p3=96;
if($p3<0)  $p3=0;

$quad["index"]=$p3;
$quad["left"]=($p1+64)/128*100;
$quad["top"]=(-$p2+48)/96*100;
}
elseif(!empty($quad["pos"])){
$p=explode(" ",$quad["pos"]);
$p1=$p[0]+0;
$p2=$p[1]+0;
$p3=$p[2]+0;
$p3=-$p3+0.75;
if($p3>1.5) $p3=1.5;
if($p3<0)  $p3=0;

$quad["index"]=$p3/1.5*96;
$quad["left"]=(-$p1+1)/2*100;
$quad["top"]=(-$p2+0.75)/1.5*100;
}else{
$quad["index"]=1;
$quad["left"]=50;
$quad["top"]=50;
}

if(!empty($quad["sizen"])){
$s=explode(" ",$quad["sizen"]);
$quad["width"]=$s[0]/128;
$quad["height"]=$s[1]/96;
if($quad["halign"]=="center"){ $quad["left"]-=$quad["width"]*100/2; }
if($quad["halign"]=="right"){ $quad["left"]-=$quad["width"]*100; }
if($quad["valign"]=="center"){ $quad["top"]-=$quad["height"]*100/2; }
if($quad["valign"]=="bottom"){ $quad["top"]-=$quad["height"]*100; }
}
$position='position:absolute;Left:'.($quad["left"]).'%; top:'.($quad["top"]).'%;';
//$position2='height='.$quad["height"].'% width='.$quad["width"].'%';
//$position2='height="sizeheight('.$quad["height"].')px" width="sizewidth('.$quad["width"].')px"';
//$position2='width = '.$quad["width"].'/100*screen.availWidth';
$java.='document.getElementById(\'object'.$number.'\').width = '.$quad["width"].'*window.innerWidth;
document.getElementById(\'object'.$number.'\').height = '.$quad["height"].'*window.innerHeight;
';

$body.='<div style="'.$position.' z-index:'.($quad["index"]).'">';

if($quad["imagefocus"]!="" or $quad["link"]!=""){
if(!empty($quad["link"]))
$body.= '
<a href="'.utf8_decode($quad["link"]).'" ';
else
$body.= '
<a ';
if($quad["imagefocus"]!="")$body.='onmouseover="document.getElementById(\'object'.$number.'\').src = \''.$quad["imagefocus"].'\';" onmouseout="document.getElementById(\'object'.$number.'\').src = \''.$quad["image"].'\';" ';
$body.='>';
 }
if($quad["style"]==""){
$body.='
<img id="object'.$number.'" border="0" src="'.$quad["image"].' " '.$position2.' >
';
}elseif(file_exists("./styles/".$quad["style"].'/'.$quad["substyle"].'.gif')){
$body.='
<img id="object'.$number.'" border="0" src="./styles/'.$quad["style"].'/'.$quad["substyle"].'.gif" '.$position2.' >
';
}elseif(file_exists("./styles/".$quad["style"].'/'.$quad["substyle"].'.png')){
$body.='
<img id="object'.$number.'" border="0" src="./styles/'.$quad["style"].'/'.$quad["substyle"].'.png" '.$position2.' >
';
}
if($quad["imagefocus"]!="" or $quad["link"]!="")  $body.= '</a>';
$body.='</div>
';
$number++;

//$quad[""]

}

?>