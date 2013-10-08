<?php
try {
	$db = new PDO(
		'mysql:host=localhost;dbname=juniorprojekt1',
		'jp',
		'juniorprojekt1'
	);
	//$db = new PDO('mysql:host=localhost;dbname=juniorprojekt','root','test');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo 'ERROR: ' . $e->getMessage();
}
?>