<?php
session_start();
if(!isset($_SESSION['testserver'])){
	$_SESSION['testserver'] = true;
	echo 'Du bist ab jetzt unterwegs im Testmodus!';
}elseif($_SESSION['testserver'] === false){
	$_SESSION['testserver'] = true;
	echo 'Du bist ab jetzt unterwegs im Testmodus!';
}else{
	$_SESSION['testserver'] = false;
	echo 'Du bist jetzt nicht mehr unterwegs im Testmodus!';
}
?>
<br><a href="/home">Zur&uuml;ck zu Placelet</a>