<?php
	include('../includes/connexion_bd.php');
	
	// préparation des variables
	$matricule = (isset($_POST['matricule'])) ? $_POST['matricule'] : 'erreur';
	$login = (isset($_POST['login'])) ? $_POST['login'] : 'erreur';
	$password = (isset($_POST['password'])) ? sha1($_POST['password']) : 'erreur';
	
	// préparation et execution de la requête
	$verifLogin = $db->prepare('SELECT * FROM utilisateurs WHERE matricule = :matricule AND login = :login AND password = :password');
	$verifLogin->bindParam(':matricule', $matricule);
	$verifLogin->bindParam(':login', $login);
	$verifLogin->bindParam(':password', $password);
	$verifLogin->execute();
	
	// Si le serveur retourne un résultat, l'utilisateur s'est correctement connecté
	if ($connecte = $verifLogin->fetch())
	{
		echo json_encode(array('statut' => true));
	}
	else
	{
		echo json_encode(array('statut' => false));
	}
?>