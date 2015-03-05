<?php
	if (!empty($_POST['id']))
	{
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
		$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
		define('BASE_URL', dirname($url));
		define('BASE_PATH', dirname(realpath('.')));
		// On détermine le chemin vers le fichier autoload.php
		require_once(dirname(dirname(__file__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
		session_start();
		if (!isset($_SESSION['utilisateur']) || !($_SESSION['utilisateur'] instanceof Administrateur))
		{
			echo json_encode(array("retour"=>false, "erreur"=>"Vous n'avez pas les privilèges requis."));
		}
		else
		{
			$db = PDO2::getInstance()->db;
			
			$stmt = $db->prepare("UPDATE utilisateurs 
								  SET statut = 4
								  WHERE id = :id");
			$stmt->bindParam(":id", $_POST['id'], PDO::PARAM_INT);
			if($stmt->execute())
			{
				echo json_encode(array("retour"=>true));
			}
			else
			{
				echo json_encode(array("retour"=>false, "erreur"=>"Erreur lors de la suppression."));
			}
		}
	}
	else
	{
		echo json_encode(array("retour"=>false, "erreur"=>"Il manque des données."));
	}
?>