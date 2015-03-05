<?php
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', dirname($url));
	define('BASE_PATH', dirname(realpath('.')));
	//Les classes doivent être définies AVANT le session_start(), sinon PHP ne peux pas charger/sauvegarder les objets de page en page
	require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
	session_start();
	
	ob_start();
	$stmt = PDO2::getInstance()->db->prepare("SELECT matricule, login, nom, prenom, email FROM utilisateurs");
	$stmt->execute();
	echo "<table>";
	while ($res = $stmt->fetch())
	{
		echo "<tr>\n<td>$res[matricule]</td>\n<td>$res[login]</td>\n<td>$res[nom]</td>\n<td>$res[prenom]</td>\n<td>$res[email]</td>\n</tr>\n";
	}
	echo "</table>";
	$retour = ob_get_clean();
	echo json_encode(array("retour"=>$retour));
?>