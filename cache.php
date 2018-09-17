<?php
/* Kein Input? */
if (!isset($_GET['file']) && !isset($_GET['f'])) {
  exit();
}
if(!isset($_GET['file']))
	$_GET['file'] = $_GET['f'];
	
function set_eTagHeaders($file, $timestamp) { //dank an: https://blog.franky.ws/php-und-das-caching-via-http-header-etag/
    $gmt_mTime = gmdate('r', $timestamp); 
 
    header('Cache-Control: public');
    header('ETag: "' . md5($timestamp . $file) . '"');
    header('Last-Modified: ' . $gmt_mTime);
 
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
        if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5($timestamp . $file)) {
            header('HTTP/1.1 304 Not Modified');
            exit();
        }
    }
}

if($_GET['file'][0] != '/')
	$_GET['file'][0] = '/'.$_GET['file'][0];

$filename = dirname($_SERVER['SCRIPT_FILENAME']);
$svg = false;

if(file_exists('.'.$_GET['file'])){
	$end = '';
	$all = explode('.', $_GET['file']);
	$thisend = $all[count($all)-1];
	$thisend = str_replace('jpeg', 'jpg', $thisend);
	if($thisend == 'png' || $thisend == 'jpg' || $thisend == 'gif')
		$mime = 'image/'.$thisend;
	elseif($thisend == 'ico')
		$mime = 'image/x-icon';
	elseif($thisend == 'svg'){
		$mime = 'image/svg+xml';
		$svg = true;
	}
	
	set_eTagHeaders('.'.$_GET['file'], filemtime('.'.$_GET['file']));
	
}else{
	if(file_exists('.'.$_GET['file'].'.png')){
		$end = '.png';
		$mime = 'image/png';
	}elseif(file_exists('.'.$_GET['file'].'.jpg')){
		$end = '.jpg';
		$mime = 'image/jpeg';
	}elseif(file_exists('.'.$_GET['file'].'.jpeg')){
		$end = '.jpeg';
		$mime = 'image/jpeg';
	}elseif(file_exists('.'.$_GET['file'].'.gif')){
		$end = '.gif';
		$mime = 'image/gif';
	}elseif(file_exists('.'.$_GET['file'].'.ico')){
		$end = '.ico';
		$mime = 'image/x-icon';
	}elseif(file_exists('.'.$_GET['file'].'.svg')){
		$end = '.svg';
		$mime = 'image/svg+xml';
		$svg = true;
	}else
		exit();
		
	set_eTagHeaders('.'.$_GET['file'].$end, filemtime('.'.$_GET['file'].$end));
}

if(file_exists('.'.$_GET['file'].$end)){
header('Pragma: public');
header('Cache-Control: max-age=31536000');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
header('Content-Type: '.$mime);	

if($_GET['file'] == '/img/logo_extended.svg'){
	//header('Vary: Accept-Encoding');
	ob_start("ob_gzhandler"); //Kompression bei dieser großer Datei (Rat von Google, man spart so 49% Traffik bei dieser Datei)
}

echo file_get_contents('.'.$_GET['file'].$end);
}

?>