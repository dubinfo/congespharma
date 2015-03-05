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
		<p><input type="button" id="ajoutermachine" value="Add a device" style="background-color:#003873;color:#ffffff;" /></p>
      <span id="ok"></span>
		</div>
		<table>
			<thead>
				<tr>
					<th>Name of the device</th>
               <th>Actions</th>
				</tr>
			</thead>
			<tbody>
<?php
	$stmt = PDO2::getInstance()->db->prepare("SELECT ID, nom_machine FROM machines");
	$stmt->execute();
	// echo "<table>";
	while ($res = $stmt->fetch())
	{
		
			echo "<tr>\n<td>$res[nom_machine]</td>";
			echo "<td><a id=\"edit#$res[ID]\" class=\"modifier\">Edit</a> | <a id=\"supp#$res[ID]\" class=\"supprimer\">Delete</a></td>\n";
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