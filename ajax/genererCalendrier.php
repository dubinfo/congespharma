<?php
	include('../includes/connexion_bd.php');
	
	
 if (!empty($_POST['action']))
	{
		if(isset($_POST['periode'])) $infos = explode('_', $_POST['periode']);
		
		switch ($_POST['action'])
		{
			case 'proposer' :
			case 'réserver' :
				$debug = $_SESSION['utilisateur']->reserver($infos[3], $infos[2], $infos[1], $infos[0], (empty($_POST['commentaire']) || $_POST['commentaire'] == 'null') ? null : $_POST['commentaire']);
				
			break;
			
			case 'reserver_plage':
				$_SESSION['utilisateur']->reserver_plage($_POST['debut'], $_POST['fin'], (empty($_POST['commentaire']) || $_POST['commentaire'] == 'null') ? null : $_POST['commentaire']);
				$_POST['mois'] = substr($_POST['debut'], 4,1);
				$_POST['annee'] = substr($_POST['debut'],6);
			break;
			
			case 'supprimer' :
			case 'retirer' :
				$_SESSION['motif'] = (isset($_POST['motif']))? $_POST['motif'] : "";
				$_SESSION['utilisateur']->annuler($infos[1] .  '_' . $infos[2] .  '_' . $infos[3] .  '_' . $infos[0], (empty($_POST['commentaire']) || $_POST['commentaire'] == 'null') ? null : $_POST['commentaire']);
				
			break;
		
			case 'supprimer_plage':
				$_SESSION['utilisateur']->supprimer_plage($_POST['debut'], $_POST['fin'], (empty($_POST['commentaire']) || $_POST['commentaire'] == 'null') ? null : $_POST['commentaire']);
				$_POST['mois'] = substr($_POST['debut'], 4,1);
				$_POST['annee'] = substr($_POST['debut'],6);
			break;
		
			case 'occuper' :
				$_SESSION['utilisateur']->occuper($infos[3], $infos[2], $infos[1], $infos[0], (empty($_POST['commentaire']) || $_POST['commentaire'] == 'null') ? null : $_POST['commentaire']);
			break;
			
			case 'valider' :
				$_SESSION['utilisateur']->valider($infos[3], $infos[2], $infos[1], $infos[0], (empty($_POST['commentaire']) || $_POST['commentaire'] == 'null') ? null : $_POST['commentaire']);
			break;
			
			case 'refuser' :
				$_SESSION['utilisateur']->refuser($infos[3], $infos[2], $infos[1], $infos[0], (empty($_POST['commentaire']) || $_POST['commentaire'] == 'null') ? null : $_POST['commentaire']);
			break;
			
			case 'add_comment' :
			case 'update_comment' :
				$stmt = $db->prepare("UPDATE reservations
							SET commentaire = :commentaire
							WHERE jour = :jour
							AND id_user = :user"); //maj DD 21/7/13
				if (empty($_POST['commentaire']) || $_POST['commentaire'] == 'null')
				{
					$stmt->bindValue(":commentaire", null, PDO::PARAM_NULL);
				}
				else
				{
					$stmt->bindParam(":commentaire", $_POST['commentaire'], PDO::PARAM_STR);
				}
				$stmt->bindParam(':user', $_SESSION['utilisateur']->getId());
				$stmt->bindValue(":jour", $infos[1] .  '_' . $infos[2] .  '_' . $infos[3] .  '_' . $infos[0], PDO::PARAM_STR);
				//$stmt->bindParam(":machine", $_SESSION['machine']); //maj DD 21/7/13
				$stmt->execute();
			break;
		
			case 'delete_comment' :
				$stmt = $db->prepare("UPDATE reservations
							SET commentaire = :commentaire
							WHERE jour = :jour 
							AND id_user = :user"); //maj DD 21/7/13
				$stmt->bindValue(":commentaire", null, PDO::PARAM_NULL);
				$stmt->bindParam(':user', $_SESSION['utilisateur']->getId());
				//$stmt->bindValue(":machine", $_SESSION['machine']); //maj DD 21/7/13
				$stmt->bindValue(":jour", $infos[1] .  '_' . $infos[2] .  '_' . $infos[3] .  '_' . $infos[0], PDO::PARAM_STR);
				$stmt->execute();
			break;
		}
	}
	
	// genération du calendrier pour le mois choisi
	if (empty($_POST['mois']) || empty($_POST['annee']))
	{
		$mois = date('m');
		$annee = date('Y');
	}
	else
	{
		$mois = ($_POST['mois'] > 0 && $_POST['mois'] < 10) ? '0' . $_POST['mois'] : '' . $_POST['mois'];
		$annee = (string)$_POST['annee'];
	}
	//ré-affiche du calendrier avec les changements
	$cal = new Calendrier;
	$retour = $cal->generer($mois, $annee);
	echo json_encode(array('retour' => $retour, 'debug' => $debug));
?>