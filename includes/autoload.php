<?php
	require_once(dirname(dirname(__file__)) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Config.class.php');
    function chargerClasse ($classe)
    {
        require(BASE_PATH . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $classe . '.class.php'); // On inclut la classe correspondante au paramètre passé
    }
	
	spl_autoload_register('chargerClasse'); // On enregistre la fonction en autoload pour qu'elle soit appelée dès qu'on instanciera une classe non déclarée
?>