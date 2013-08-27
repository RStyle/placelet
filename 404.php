<?php
$pages = array('home' => '1', 'shop' => '1', 'aboutus' => '1', 'impressum' => '1', 'connect' => '1', 'agb' => '1', 'profil' => '1');
//Auslesen der Seite per $_GET
$page = 'noone';
foreach ( $_GET as $key => $value ) {
                if(isset($pages[$key]))
				$page = $key;
				break;
        }
echo $page;
?>