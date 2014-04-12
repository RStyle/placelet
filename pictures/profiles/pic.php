<?php
if(isset($_GET['user'])) {
	header('Content-Type: image/jpeg');
	if(@readfile('../../pictures/profiles/'.$_GET['user'].'.jpg') === false)
	readfile('../../img/profil_pic_small.png');
}
?>