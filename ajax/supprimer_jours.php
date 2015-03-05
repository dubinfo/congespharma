<?php
	include('../includes/connexion_bd.php');
	include('../mailer/class.phpmailer.php');
	
	//préparation et execution de la requête
	$supp = $db->prepare('DELETE FROM reservations WHERE id_user = :user  AND jour = :jour');
	$supp->bindParam(':user', $_POST['id_user']);
	$supp->bindParam(':jour', $_POST['jour']);
	$supp->execute();

	$mail = new PHPMailer();
	$mail->From = $_SESSION['utilisateur']->getEmail();
 
	$mail->FromName = $_SESSION['utilisateur']->getFullName();
	$mail->CharSet = 'UTF-8';
       
	// Définition du sujet/objet
	$mail->Subject = "congespharma : J'ai annulé un jour de congé !";
       
	// On lit le contenu d'une page html
	$body = '';
	$body.= "<div align='center'><img src='http://s23.postimg.org/b9c7bk0x7/ulb.jpg'  /></div>";
	$body.= '<p>';
	$body.= 'Bonjour,<br /><br />';
	$body.= 'Je vous informe que j\' ai annulé le jour de congé du '.$_POST['jour'].' via le site congespharma.ulb.ac.be<br /><br />';
	$body.= 'Motif : '.$_POST['motif'].'<br /><br />';
	$body.= 'Cordialement,<br /><br />';
	$body.= $_SESSION['utilisateur']->getFullName();
	
	$body.= '</p>';
	
	$body.= '<p>';

	// On définit le contenu de cette page comme message
	$mail->MsgHTML($body);
	
	//grande priorité
	$mail->Priority = 1;
	$mail->AddCustomHeader("X-MSMail-Priority: High");
	$mail->AddCustomHeader("Importance: High");
       
	// On pourra définir un message alternatif pour les boîtes de
	// messagerie n'acceptant pas le html
	$mail->AltBody = "Ce message est au format HTML, votre messagerie n'accepte pas ce format.";
       
	// Il reste encore à ajouter au moins un destinataire
	$mail->AddAddress($_POST['mail']);
	//copie du message au client
	//$mail->AddAddress($_POST['mail'],$_POST['nom']);
       
	// Pour finir, on envoi l'e-mail
	$mail->send();
?>