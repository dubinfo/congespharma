<?php
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', dirname($url));
	define('BASE_PATH', dirname(realpath('.')));
	// On détermine le chemin vers le fichier autoload.php
	require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');

	session_start();
	if (!isset($_SESSION['utilisateur']))
	{
		echo json_encode(array("erreur" => "accès non autorisé"));
		exit;
	}
	$erreur = array();
	ob_start();
	
	$db = PDO2::getInstance()->db;
	
	
	
	$to      = 	"Pierre Van Antwerpen <pvantwer@ulb.ac.be>, Cedric Delporte <cedric.delporte@ulb.ac.be>";
	// $to      = 	"Denis Bastin <bastin.denis@gmail.com>, David Dubois <ddubois2@gmail.com>";
	//$to      = 	"David Dubois <ddubois2@gmail.com>";
	
	$subject = 	"APA - Nouvelle(s) réservation(s)";
	$message = 	"Bonjour,<br /><br />" . $_SESSION['utilisateur']->getNomComplet() . " propose de nouvelles réservations, en voici le détail :<br /><br />" .
				"<table><tr><td><i><b>date</b></i></td><td><i><b>période</b></i></td><td><i><b>commentaire</b></i></td></tr>";
	foreach ($_SESSION['utilisateur']->getReservations() as $reservation)
	{
		$message .= "<tr><td>" . $reservation->getDate() . "</td><td>" . $reservation->getPeriode() . "</td><td>" . $reservation->getCommentaire() . "</td></tr>";
	}
	$message .= "</table><br /><br />Veuillez vous rendre à cette adresse pour accepter ou refuser les réservations citées ci-dessus : " .
				"<a href=\"" . BASE_URL . DIRECTORY_SEPARATOR . "\" target=\"blank\">Accès direct au site</a><br /><br />" .
				"Cordialement,<br /><br />APA.";
				
	utf8_decode($message);			
 	
 	$headers = "From:APA <noreply@ulb.ac.be>". "\n" ."Reply-To:APA <noreply@ulb.ac.be>". "\n" ."MIME-Version: 1.0". "\n" ."Content-type: text/html; charset=UTF8_FR";
	
/*	$headers  = "From:APA <noreply@ulb.ac.be>". "\r\n" ;
  	$headers .=	"Reply-To:APA <noreply@ulb.ac.be>". "\r\n" ;
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF8' . "\r\n";*/		
	

	if(!mail($to, $subject, $message, $headers)) 
	// if(false) 
	{
		$erreur['mail'] = "échec de l'envoi du mail";
	}
	else
	{
		$stmt = $db->prepare("UPDATE reservations SET email_envoye = 1 WHERE jour = :jour");
		foreach ($_SESSION['utilisateur']->getReservations() as $reservation)
		{
			$stmt->bindParam(":jour", $reservation->getId(), PDO::PARAM_STR);
			if ($stmt->execute())
			{
				$_SESSION['utilisateur']->supprimerReservation($reservation->getId());
			}
			else
			{
				$erreur['database'] = "Une erreur est survenue lors de la mise à jour dans la base de données";
			}
		}
	}
	
	$scriptErrors = ob_get_clean();
	if (!empty($scriptErrors))
	{
		$erreur['script'] = $scriptErrors;
	}
	if (empty($erreur)) $reponse = true;
	else $reponse = false;
	echo json_encode(array('erreur' => $erreur, 'reponse' => $reponse));
?>