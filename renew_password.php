<?php
    define('BASE_PATH', realpath('.'));
    require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
    if(allParamExist())
    {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-FR" lang="fr-FR">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Calendrier des congés – Faculté de Pharmacie</title>
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery-ui.js"></script>
		<script type="text/javascript" src="js/jquery.ui.position.js"></script>
		<script type="text/javascript" src="js/resetPassword.js"></script>
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
    		<link rel="stylesheet" type="text/css" href="css/login.css" />
		<link rel="stylesheet" type="text/css" href="css/resetPassword.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css" />
	</head>
        <body>
		<header>
			<img src="images/footer.jpg"/> <h1>Calendrier des congés – Laboratoire de Pharmacie Galénique et Secrétariat</h1>	
		</header>
<?php
        $email = $_GET['email'];
        $key = $_GET['key'];
        
        $db = PDO2::getInstance()->db;
        $sql = $db->prepare('SELECT id, password FROM utilisateurs WHERE email = :email');
	$sql->bindParam(':email', $email);
        if($sql->execute())
        {
            if($result = $sql->fetch())
            {
                $keyCompute = new Encryption([$result['id'], $result['password']]);
                if($key == $keyCompute->getTextHasher())
                {
                    //le lien est correcte on verifier si on a déjà reçu le formulaire et si les champs sont correct
                    if(isset($_POST['new_password']) && passwordCorrect())
                    {
                        $password = sha1($_POST['new_password']);
                        $sql = $db->prepare('UPDATE utilisateurs SET password= :password WHERE id = :id');
                        $sql->bindParam(':password',$password);
                        $sql->bindParam(':id', $result['id']);
                        if($sql->execute())
                        {
?>
                <div id="reset_password_success">
                    <h2>Votre mot de passe a été modifié avec succès!</h2>
                    <p>Vous pouvez dès à present vous connecter sur le site <a href="login.php">congespharma</a></p>
                </div>
<?php
                        }
                        else
                        {
                            echo '<div><p class="error">Erreur lors de la modification dans la base de données!</p></div>';
                        }
                    }
                    else
                    {
                    //sinon on affiche le formulaire
?>
            <div>
            <form id="reset_password_form" method="POST">
                <p>
                    <label for="password">Nouveau mot de passe :</label>
                    <input type="password" name="new_password" id="new_password"/>
                </p>
                <p>
                    <label for="confirm_password">Confirmer votre mot de passe :</label>
                    <input type="password" name="confirm_password" id="confirm_password"/>
                </p>
                <p><input type="submit" name="confirm_reset_password" value="Confirmer" id="confirm_reset_password"/></p>
            </form>
            <p id="error_password" class="error_message">
<?php
                        if(isset($_POST['new_password']) && !passwordCorrect()){
                            echo 'Mot de passe vide ou confirmation mot de passe incorrecte!'; 
                        }
                        echo '</p>';
                    }
                }
                else
                {
                    echo '<div><p class="error">Lien de réinitialisation incorrect!</p></div>';
                }
            }
            else
            {
                echo '<div><p class="error">Adresse email inexistante sur le site congespharma!</p></div>';
            }
        }
        else
        {
            echo '<div><p class="error">Erreur lors de la lecture de la Base de données!</p></div>';
        }
    }
    else
    {
        header('Location: login.php');
    }
    
    function allParamExist(){
        return (isset($_GET['email']) && !empty($_GET['email'])) &&
                    (isset($_GET['key']) && !empty($_GET['key']));
    }
    
    function passwordCorrect(){
        return !empty($_POST['new_password']) && !empty($_POST['confirm_password'])
                && $_POST['new_password'] == $_POST['confirm_password'];
    }
?>
</html?