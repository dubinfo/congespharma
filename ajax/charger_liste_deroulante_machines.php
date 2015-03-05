<?php
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', dirname($url));
	define('BASE_PATH', dirname(realpath('.')));
	// On dŽtermine le chemin vers le fichier autoload.php
	require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
	session_start();
	// rŽcuperation de l'instance de PDO
	$db = PDO2::getInstance()->db;
   //print_r($db);
  
   $sql = $db->query("SELECT ID, nom_machine FROM machines");
   $machines = $sql->fetchAll();
   echo json_encode(array('liste_machines'=>$machines, 'machine_actuelle'=>$_SESSION['machine']));
   
   
?>   