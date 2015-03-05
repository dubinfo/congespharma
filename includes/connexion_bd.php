<?php
   
   $debug = '';
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', dirname($url));
	define('BASE_PATH', dirname(realpath('.')));
	// On dŽtermine le chemin vers le fichier autoload.php
	require_once(dirname(dirname(__file__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
	session_start();
	$db = PDO2::getInstance()->db;
	// $debug .= "commentaire = \"" . $_POST['commentaire'] . "\"";

?>