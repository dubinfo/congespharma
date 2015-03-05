<?php
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', $url);
	define('BASE_PATH', realpath('.'));
	//Les classes doivent être définies AVANT le session_start(), sinon PHP ne peux pas charger/sauvegarder les objets de page en page
	require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
	session_start();
	
	ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-FR" lang="fr-FR">
	<head>
		<title>Calendrier APA</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery-ui.js"></script>
		<script type="text/javascript" src="js/jquery.ui.position.js"></script>
		<script type="text/javascript" src="js/jquery.contextMenu.js"></script>
		<script type="text/javascript" src="ajax/genererCalendrier.js"></script>
		<script type="text/javascript" src="ajax/modifierUtilisateur.js"></script>
		<!--<script type="text/javascript" src="ajax/gestion.js"></script>-->
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery.contextMenu.css" />
	</head>
	<body>
		<div id="menuTop" style="width:90%;margin-left:auto;margin-right:auto;">
		<?php require_once "includes/menu_top.php"; ?>
		</div>
		<div style="clear:both;width:90%;margin-left:auto;margin-right:auto;">
		<p><input type="button" id="ajouterUtilisateur" value="Ajouter un utilisateur" style="background-color:#003873;color:#ffffff;" /></p>
		</div>
		<table>
			<thead>
				<tr>
					<td>matricule</td>
					<td>login</td>
					<td>nom</td>
					<td>prénom</td>
					<td>email</td>
					<td>rang</td>
					<td>statut</td>
					<td>actions</td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td>matricule</td>
					<td>login</td>
					<td>nom</td>
					<td>prénom</td>
					<td>email</td>
					<td>rang</td>
					<td>statut</td>
					<td>actions</td>
				</tr>
			</tfoot>
			<tbody>
<?php
	$stmt = PDO2::getInstance()->db->prepare("SELECT u.id, u.matricule, u.login, u.nom, u.prenom, u.email, r.libelle AS rang, s.libelle AS statut FROM utilisateurs u LEFT JOIN rangs r ON u.rang = r.id LEFT JOIN statuts_utilisateurs s ON u.statut = s.id");
	$stmt->execute();
	// echo "<table>";
	while ($res = $stmt->fetch())
	{
		if ($res['statut'] != 'supprimé')
		{
			echo "<tr>\n<td>$res[matricule]</td>\n<td>$res[login]</td>\n<td>$res[nom]</td>\n<td>$res[prenom]</td>\n<td>$res[email]</td>\n";
			echo "<td>$res[rang]</td>\n<td>$res[statut]</td>\n<td><a id=\"u$res[id]\" class=\"modifierUtilisateur\">éditer</a> | <a id=\"s$res[id]\" class=\"supprimerUtilisateur\">supprimer</a></td>\n</tr>\n";
		}
	}
	// echo "</table>";
?>
			</tbody>
		</table>
		<div id="editUser-form" title="Modifier les informations d'un utilisateur"></div>
		<p id="userId" style="display:none;"></p>
	</body>
</html>
<?php
	echo ob_get_clean();
?>