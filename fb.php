<?php
error_reporting(E_ALL | E_STRICT); 
ini_set('display_errors', 1);
session_start();
if(!isset($_SESSION['server_SID'])) {
    // Möglichen Session Inhalt löschen
    session_unset();
    // Ganz sicher gehen das alle Inhalte der Session gelöscht sind
    $_SESSION = array();
    // Session zerstören
    session_destroy();
    // Session neu starten
    session_start();
    // Neue Server-generierte Session ID vergeben
    session_regenerate_id();
    // Status festhalten
    $_SESSION['server_SID'] = true;
}
if(!isset($_GET['picid']))
	{echo'No Picture-ID.';exit;}

$picid = trim($_GET['picid']) + 0;
if($picid < 1)
	{echo'Picture-ID to low.';exit;}//https://placelet.de/armband?name='.$rowbrid['name'].'&pic='.$row['picid'].' https: //placelet.de/armband?name=RStyle%231&pic=5

require_once('scripts/connection.php');
$stmt = $db->prepare('SELECT * FROM pictures WHERE id = :picid');
$stmt->execute(array('picid' =>$picid));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $db->prepare('SELECT * FROM bracelets WHERE brid = :brid');
$stmt->execute(array('brid' =>$row['brid']));
$rowbrid = $stmt->fetch(PDO::FETCH_ASSOC);

$link = 'https://placelet.de/armband?name='.urlencode($rowbrid['name']).'&pic='.$row['picid'];
echo '<!DOCTYPE HTML>
<html><body><img src="https://placelet.de/pictures/bracelets/pic-'.$picid.'.jpg" />
    <script language="javascript" type="text/javascript">
    <!-- // JavaScript-Bereich für ältere Browser auskommentieren
    window.location.href = "'.$link.'";
    // -->
    </script></body></html>';
?>