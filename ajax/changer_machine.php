<?php
	include('../includes/connexion_bd.php');
   $mois = $_POST['mois'];
   $annee = $_POST['annee'];
   $machine = $_POST['machine'];
   
   //DD je change la valeur de la variable de session machine
   $_SESSION['machine'] = $machine;
   
   $cal = new Calendrier;
	$retour = $cal->generer();
	echo $retour;
   //echo json_encode(array('retour' => $retour, 'debug' => $debug));
   
?>   