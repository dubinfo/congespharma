<?php
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', $url);
	define('BASE_PATH', realpath('.'));
	//Les classes doivent être définies AVANT le session_start(), sinon PHP ne peux pas charger/sauvegarder les objets de page en page
	require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
	session_start();
	$header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-FR" lang="fr-FR">
	<head>
		<title>Calendrier APA</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="js/jquery.js"></script>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery.contextMenu.css" />
	</head>
	<body>';
	
	if (!empty($_POST['id']))
	{
		$db = PDO2::getInstance()->db;
		$stmt = $db->prepare("UPDATE utilisateurs SET password = SHA1(:password), statut = 1, code = :null WHERE id = :id");
		$stmt->bindParam(":password", $_POST['password'], PDO::PARAM_STR);
		$stmt->bindParam(":id", $_POST['id'], PDO::PARAM_INT);
		$stmt->bindValue(":null", null, PDO::PARAM_NULL);
		if ($stmt->execute())
		{
			// TODO : envoyer email
			echo $header;
			echo "<p>Votre compte a bien été activé, vous pouvez dès à présent vous <a href=\"login.php\">connecter</a></p>";
			echo "</body></html>";
			exit;
		}
		else
		{
			die("Erreur lors de la mise à jour du mot de passe.");
		}
		
	}
	ob_start();
	echo $header;
?>

	<?php
		if (empty($_GET['ref']))
		{
			header("Location: index.php");
			exit;
		}
		else
		{
			$message = "";
			$db = PDO2::getInstance()->db;
			
			$stmt = $db->prepare("SELECT * FROM utilisateurs WHERE code = :code");
			$stmt->bindValue(":code", $_GET['ref'], PDO::PARAM_STR);
			if ($stmt->execute())
			{
				while ($reponse = $stmt->fetch())
				{
					$id = $reponse['id'];
					$message = "<form action=\"activer_compte.php\" method=\"post\">\n" .
					"<input type=\"hidden\" value=\"$id\" id=\"userid\" name=\"id\"/>\n";
					break;
				}
				if (empty($message))
				{
					echo "<p>Erreur : lien invalide.</p>";
				}
				else
				{
					$message .= "<p>Bienvenue $reponse[prenom] $reponse[nom], veuillez choisir un mot de passe</p>\n" .
					"<table>\n<tr>\n<td>mot de passe : </td>\n<td>" .
					"<input type=\"password\" name=\"password\" id=\"password\" />\n" .
					"</td>\n</tr>\n<tr>\n<td>confirmation de votre mot de passe : </td>\n<td>" .
					"<input type=\"password\" name=\"confirmation\" id=\"confirmation\" />\n" .
					"</td>\n</tr>\n<tr>\n<td colspan=\"2\">" .
					"<input type=\"submit\" id=\"valider\" name=\"valider\" value=\"valider\" />\n" .
					"</td>\n</tr>\n</table>\n</form>\n";
					echo $message;
				}
			}
			else
			{
				echo "<p>Erreur lors de la requête SQL</p>";
			}
		}
	?>
	</body>
</html>
<?php
	echo ob_get_clean();
?>