<?php
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', dirname($url));
	define('BASE_PATH', dirname(realpath('.')));
	//Les classes doivent Ítre dÈfinies AVANT le session_start(), sinon PHP ne peux pas charger/sauvegarder les objets de page en page
	require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
	session_start();
	
	$stmt = PDO2::getInstance()->db->prepare("SELECT * FROM utilisateurs ORDER BY nom");
	$stmt->execute();
	
 $arr_users = array();
 
	//résultat à obtenir =>"fold1-key1": {"name": "Caroline Noyon"},
 $retour = "";
	$i = 1;
	$retour.= '	{';
	while ($res = $stmt->fetch())
	{
		 $retour.= '"fold1-key'.$res['id'].'":{"name":"'.$res['nom'].' '.$res['prenom'].'"},';
   //$arr_users[] =  $res['id'];
   //$arr_users[] = $res['prenom'].' '.$res['nom'];
	}
	$retour = substr($retour, 0, -1);
	$retour.= '}';
	
	echo $retour;
?>