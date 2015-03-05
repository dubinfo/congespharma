<?php
    require_once('../includes/connexion_bd.php');

    if(isset($_POST['service1']))
    {
        $html = '<tr id="line_service2">
                    <td>
                        <label for="service2">Second service (facultatif)</label>
                    </td>
                    <td>
                        <select id="service2">
                            <option value="">Aucun</option>';
        $request = $db->prepare('SELECT * FROM service s WHERE id <> :id_service1 ORDER BY libelle');
        $request->bindParam(':id_service1', $_POST['service1']);
        $request->execute();
        $result = $request->fetchAll(PDO::FETCH_OBJ);
        
        foreach($result as $value)
        {
            $html .='<option value="' . $value->id . '">' . $value->libelle. '</option>';
        }
        
        echo json_encode(array("retour"=>true, "html"=>$html));
    }
    else
    {
        echo json_encode(array("retour"=>false, "erreur"=>"Il manque des données."));
    }
?>