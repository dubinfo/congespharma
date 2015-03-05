<?php
    define('EMAIL_FROM', 'congespharma@ulb.ac.be');
    define('NAME_FROM', 'Congespharma');
    define('BASE_PATH', dirname(realpath('.')));
    require_once('../mailer/class.phpmailer.php');
    require_once(dirname(dirname(__file__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
    
    if(allParamExist())
    {
        $db = PDO2::getInstance()->db;
        $id = $_POST['id_user'];
        if($_POST['action'] == 'accept')
        {
            $sql = $db->prepare('UPDATE utilisateurs set rang=1 WHERE id = :id');
            $sql->execute(array(':id' => $id)) or
                            die(json_encode(array("retour"=>false, "erreur"=>"Erreur Lors de la modification de la base de données.")));
                            
            $sql = $db->prepare("SELECT email FROM utilisateurs WHERE id = :id");
            $sql->execute(array(':id' => $id)) or
                            die(json_encode(array("retour"=>false, "erreur"=>"Erreur Lors de la lecture de la base de données.")));
            $result = $sql->fetch();
            sendEmail('acceptée', $result['email']);
            
            echo json_encode(array("retour"=>true));
        }
        elseif($_POST['action'] == 'decline')
        {
            $sql = $db->prepare("SELECT email FROM utilisateurs WHERE id = :id");
            $sql->execute(array(':id' => $id)) or
                            die(json_encode(array("retour"=>false, "erreur"=>"Erreur Lors de la lecture de la base de données.")));
            $result = $sql->fetch();
            sendEmail('refusée', $result['email']);
            
            echo json_encode(array("retour"=>true));
        }
        else
        {
            echo json_encode(array("retour"=>false, "erreur"=>"Action inconnue."));
        }
    }
    else
    {
        echo json_encode(array("retour"=>false, "erreur"=>"Il manque des paramètres."));
    }
    
    function sendEmail($reponse, $email){
        $mail = new PHPMailer();
        
        $mail->CharSet = 'UTF-8';
        $mail->From = EMAIL_FROM;
        $mail->FromName = NAME_FROM;
        $mail->Subject = "Congespharma - demande de rang administrateur " . $reponse;
        
        $body ='<div align="center"><img src="http://s23.postimg.org/b9c7bk0x7/ulb.jpg"/></div>
                <p>Votre demande pour avoir des droits d\'administrations sur le site congespharma a été '.
                $reponse . ' par l\'administrateur du site</p>
                <p style="font-style: italic;">Merci de ne pas répondre à ce message!</p>';
        
        $mail->MsgHTML($body);
        $mail->AltBody = "Ce message est au format HTML, votre messagerie n'accepte pas ce format.";
    
        $mail->addAddress($email);
        $mail->send();
    }
    
    function allParamExist(){
        return (isset($_POST['action']) && !empty($_POST['action'])) &&
                    (isset($_POST['id_user']) && !empty($_POST['id_user']));
    }
?>