<?php
    include('../includes/connexion_bd.php');
    require_once('../includes/autoload.php');
    //vérifier si présence des variables post
    if(allVarPostIsset())
    {
        $synthese = new Synthese($_POST['personId'], $_POST['month'], $_POST['year'], $_POST['service']);
        echo json_encode(array("retour"=>true, "html"=> $synthese->getSynthese()));
    }
    else
    {
        echo json_encode(array("retour"=>false, "erreur"=>"Il manque des données."));
    }
    
    function allVarPostIsset()
    {
        return isset($_POST['month']) && isset($_POST['personId'])
            && isset($_POST['year']) && isset($_POST['service']);
    }
?>