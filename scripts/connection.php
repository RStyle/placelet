<?php
if(!isset($_SESSION['testserver'])){
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
}elseif($_SESSION['testserver'] == false){
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
}else{
	try {
		$db = new PDO(
			'mysql:host=localhost;dbname=testjuniorprojekt1',
			'jp',
			'juniorprojekt1'
		);
		//$db = new PDO('mysql:host=localhost;dbname=juniorprojekt','root','test');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		echo 'ERROR: ' . $e->getMessage();
	}
}
?>