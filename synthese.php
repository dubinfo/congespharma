<?php

// Désactiver le rapport d'erreurs
 error_reporting(0);
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', $url);
	define('BASE_PATH', realpath('.'));
	//Les classes doivent être définies AVANT le session_start(), sinon PHP ne peux pas charger/sauvegarder les objets de page en page
	require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
	session_start();
	include('includes/connexion_bd.php');
 
	// Si l'utilisateur se déconnecte, on supprime sa variable de session
	if (isset($_GET['deconnexion']))
	{
			unset($_SESSION['utilisateur']);
	}
	
	// On vérifie si l'utilisateur est connecté, si ce n'est pas le cas => redirection vers l'écran de login
	if (!isset($_SESSION['utilisateur']))
	{
		header('Location: login.php');
	}
	define("ADMIN_RANK", "administrateur");
	define("BEFORE_YEAR", 1);
	define("AFTER_YEAR", 1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-FR" lang="fr-FR">
	<head>
		<title>Faculté de Pharmacie : Synthèse des congés</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery-ui.js"></script>
		<script type="text/javascript" src="js/jquery.ui.position.js"></script>
		<script type="text/javascript" src="js/jquery.contextMenu.js"></script>
		<script type="text/javascript" src="ajax/genererCalendrier.js"></script>
		<script type="text/javascript" src="ajax/mail.js"></script>
		<script type="text/javascript" src="ajax/jquery_index.js"></script>
		<script type="text/javascript" src="ajax/supprimer_jours.js"></script>
		<script type="text/javascript" src="ajax/refresh_synthese.js"></script>
		<link rel="stylesheet" type="text/css" href="css/style_synthese.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery.contextMenu.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css" />
	</head>
	<body>
		<?php
		$rang = $_SESSION['utilisateur']->getRang();
		//DD 7/2/2015 => Si c'est un admin, on affiche l'icone pour générer le fichier excel
		if($rang == ADMIN_RANK)
		{
			$code = new Encryption([$_SESSION['utilisateur']->getId(), $_SESSION['utilisateur']->getEmail()]);
		?>
		<a href="./classes/Classes/synthese_conge.php?id=<?php echo $_SESSION['utilisateur']->getId() . "&code=" . $code->getTextHasher(); ?>">
			<div id="export_from_excel">
				<p>Exporter en Excel</p>
				<img src="images/excel.png" />
			</div>
		</a>
		<?php
		}
		?>
		
		<div id="filters">
			<div>
				<span>Filtrer la liste </span>
				<select id="month_filter" class="synthese_filter">
					<option value="">par Mois</option>
					<?php
						//creation filtre mois
						$months = ["Janvier","Février","Mars","Avril","Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
						for($i=0; $i < count($months); $i++)
						{
							echo '<option value="' . (($i<9)?'0':'') . ($i+1) .'">' . $months[$i].'</option>';   
						}
					?>
				</select>
			</div>
			<div>
				<span>et/ou</span>
				<select id="year_filter" class="synthese_filter">
					<option value="">par année</option>
					<?php
						//creation filtre année
						for($i=date('Y')-BEFORE_YEAR; $i <= date('Y')+AFTER_YEAR; $i++)
						{
							echo '<option value="' . $i . '">' . $i . '</option>';
						}
					?>
				</select>
			</div>
			<?php
				if($rang == ADMIN_RANK)
				{
			?>
			<div>
				<span> et/ou</span>
				<select id="person_filter" class="synthese_filter">
					<option value="">par Personne</option>
					<?php 
						$sqlRequest = "SELECT
									u.id,
									u.nom,
									u.prenom
								FROM utilisateurs u
								JOIN reservations r
								ON r.id_user = u.id
								JOIN service_has_utilisateurs shs
								ON shs.utilisateurs_id = u.id
								JOIN service s
								ON shs.service_id = s.id
								WHERE s.id = " . $_SESSION['utilisateur']->getService(0);
						if($_SESSION['utilisateur']->getService(1))
						{
							$sqlRequest .= " OR s.id = " . $_SESSION['utilisateur']->getService(1);
						}
						
						$sqlRequest .= " GROUP BY r.id_user
								ORDER BY u.prenom, u.nom";
						$sql = $db->prepare($sqlRequest);
						$sql->execute();
						$res = $sql->fetchAll(PDO::FETCH_OBJ);
						foreach($res as $value)
						{
							echo '<option value="' . $value->id . '">' . $value->prenom . ' ' . $value->nom . '</option>';
						}
					?>
				</select>
			</div>	
				<?php
						$sql = $db->prepare("SELECT id, libelle
								     FROM service_has_utilisateurs shs
								     JOIN service s
								     ON shs.service_id = s.id
								     WHERE utilisateurs_id = :id");
						$sql->execute(array(':id' => $_SESSION['utilisateur']->getId()));
						$res = $sql->fetchAll(PDO::FETCH_OBJ);
						if(count($res) > 1)
						{
				?>
				<div>
			<span> et/ou</span>
				<select id="service_filter" class="synthese_filter">
					<option value="">Par service</option>
				<?php
							foreach($res as $service)
							{
								echo '<option value="' . $service->id . '">' . $service->libelle . '</option>';		
							}
						}
				?>
				</select>
			</div>
			<?php
				}
			?>
		</div>
		<div id="table_synthese">
		<?php
			$synthese = new Synthese();
			echo $synthese->getSynthese();
		?>		
		</div>
	</body>
</html>
