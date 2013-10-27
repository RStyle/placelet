<?php
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors', true);
include_once('./scripts/connection.php');
if(isset($_GET['number'])) {
	$anzahl = $_GET['number'];
	for($i = 0; $i < $anzahl; $i++) {
		$brid = mt_rand(100000, 999999);
		$sql = "SELECT brid FROM bracelets WHERE brid = :brid";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(
				':brid' => $brid));
		$q = $stmt->fetch(PDO::FETCH_ASSOC);
		if($q == NULL) {
			try {
				$sql = "INSERT INTO bracelets (brid) VALUES (:brid)";
				$stmt = $db->prepare($sql);
				$stmt->execute(array(
						':brid' => $brid)
				);
				echo 'Armband Nr. '.$brid.' erfolgreich registriert.<br>';
			} catch(PDOException $e) {
				die('ERROR: ' . $e->getMessage());
			}
		}
	}
}else echo 'Keine Anzahl spezifiziert.';
?>