<?php
    require_once('../includes/connexion_bd.php');
    require_once('../mailer/class.phpmailer.php');
    
    define('EMAIL_FROM', 'congespharma@ulb.ac.be');
    define('NAME_FROM', 'Congespharma');
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
    define('URL', $url . 'renew_password.php');
    
    if(emailIsCorrect()){
        $email = $_POST['email'];
        
        $sql = $db->prepare('SELECT id, password, prenom FROM utilisateurs WHERE email = :email');
	$sql->bindParam(':email', $email);

        if($sql->execute())
        {
            if($result = $sql->fetch())
            {
                try{
                    $mail = new PHPMailer();
                    
		    $mail->CharSet = 'UTF-8';
                    $mail->From = EMAIL_FROM;
                    $mail->FromName = NAME_FROM;
                    $mail->Subject = "Congespharma - Demande de réinitialisation de votre mot de passe";
                    
                    $key = new Encryption([$result['id'], $result['password']]);
                    $body ='<div align="center"><img src="http://s23.postimg.org/b9c7bk0x7/ulb.jpg"/></div>
                            <p>Cher/Chère ' . $result['prenom'] . ',</p>
                            <p>Vous avez demandé la réinitialisation de votre mot de passe.
                               Cliquez sur le lien ci-dessous pour être redirigé(e) vers
                               une page de congespharma dans laquelle vous pourrez changer
                               votre mot de passe</p>
                            <p><a href="'. URL .'?email='. $email . '&key=' . $key->getTextHasher() .
                            '">Réinitialiser le mot de passe</a></p>
                            <p style="font-style: italic;">Si vous n\'avez pas demandé à
                               réinitialiser votre mot de passe ne cliquer pas sur le lien!</p>
                            <p style="font-style: italic;">Merci de ne pas répondre à ce message</p>';
                    
                    $mail->MsgHTML($body);
                    $mail->AltBody = "Ce message est au format HTML, votre messagerie n'accepte pas ce format.";
                
                    $mail->addAddress($email);
                    $mail->send();
		    
                    echo json_encode(array("retour"=>true));
                }
                catch(phpmailerException $e)
                {
                   echo json_encode(array("retour"=>false, "erreur"=>"Erreur lors de l'envoie du mail!"));
                }
            }
            else
            {
                echo json_encode(array("retour"=>false, "erreur"=>"Adresse email inexistante sur le site congespharma!"));
            }
        }
        else
        {
            echo json_encode(array("retour"=>false, "erreur"=>"Erreur lors de la lecture de la Base de données!"));
        }
    }
    else
    {
        echo json_encode(array("retour"=>false, "erreur"=>"Adresse email incorrecte!"));
    }
    
    function emailIsCorrect(){
        return isset($_POST['email']) && preg_match("/^[a-zA-Z0-9._-]{2,}@[a-z0-9._-]{2,}\.[a-z]{2,4}$/",$_POST['email']);
    }
?>