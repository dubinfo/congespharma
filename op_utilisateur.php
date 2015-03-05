<?php
    define('BASE_PATH', realpath('.'));
    define('ADMIN_RANK', 1);
    require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
    if(allParamExist())
    {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-FR" lang="fr-FR">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Faculté de Pharmacie - congespharma : réinitialisation mot de passe</title>
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery-ui.js"></script>
		<script type="text/javascript" src="js/jquery.ui.position.js"></script>
		<script type="text/javascript" src="js/op_utilisateur.js"></script>
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
    		<link rel="stylesheet" type="text/css" href="css/login.css" />
		<link rel="stylesheet" type="text/css" href="css/op_utilisateur.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css" />
	</head>
        <body>
		<header>
			<img src="images/footer.jpg"/> <h1>Calendrier des congés – Faculté de Pharmacie</h1>	
		</header>
<?php
        $email = $_GET['email'];
        $key = $_GET['key'];
        
        $db = PDO2::getInstance()->db;
        $sql = $db->prepare('SELECT id, matricule, login, nom, prenom, email, rang FROM utilisateurs WHERE email = :email');
        $sql->execute(array(':email' => $email)) or
                        die('<div><p class="error">Erreur lors de la lecture de la Base de données!</p></div>');

        if($result = $sql->fetch())
        {
            $keyCompute = new Encryption([$result['id'], $result['email']]);
            if($key == $keyCompute->getTextHasher())
            {
?>
            <div id="info_utilisateur">
                <h2>Demande de droits administrateurs</h2>
		<p>
		    <span class="label">Ip :</span>
                    <span class="data"><?php echo $_GET['ip'];?></span>
		</p>
                <p>
                    <span class="label">Matricule :</span>
                    <span class="data"><?php echo $result['matricule'];?></span>
                </p>
                <p>
                    <span class="label">NetID :</span>
                    <span class="data"><?php echo $result['login'];?></span>
                </p>
                <p>
                    <span class="label">Prénom :</span>
                    <span class="data"><?php echo $result['prenom'];?></span>
                </p>
                <p>
                    <span class="label">Nom :</span>
                    <span class="data"><?php echo $result['nom'];?></span>
                </p>
                <p>
                    <span class="label">Email :</span>
                    <span class="data"><?php echo $result['email'];?></span>
                </p>
                <p>
                    <span class="label">Service :</span>
<?php
                $sql = $db->prepare('SELECT libelle
                                     FROM service ser,
                                          service_has_utilisateurs shu
                                     WHERE ser.id = shu.service_id 
                                     AND shu.utilisateurs_id = :user_id
                                     ORDER BY libelle');
                $sql->execute(array(':user_id' => $result['id'])) or
                    die('<div><p class="error">Erreur lors de la lecture de la Base de données!</p></div>');
                $services = $sql->fetchAll();
                foreach($services as $service)
                {
                    echo '<span class="data"> - ' . $service['libelle'] . '</span></p><p>';
                }
?>
                    <form id="give_admin_rank">
			<input type="hidden" id="id_user" value="<?php echo $result['id']; ?>"/>
                        <input id="btn_give_admin_rank" value="Accept<?php echo ($result['rang'] == ADMIN_RANK)? 'é" class="isAdmin"':'er"'; ?> type="button"/>
			<input id="btn_decline_admin_rank" value="Refuser" <?php echo ($result['rang'] == ADMIN_RANK)? 'class="hidden"':''; ?> type="button"/>
                    </form>
                </p>
            </div>
<?php  
            }
            else
            {
                header('Location: index.php');
            }
        }
        else
        {
            echo '<div><p class="error">Adresse email inexistante sur le site congespharma!</p></div>';
        }
    }
    else
    {
        header('Location: index.php');
    }
    
    function allParamExist(){
        return (isset($_GET['email']) && !empty($_GET['email'])) &&
                    (isset($_GET['key']) && !empty($_GET['key'])) &&
		    (isset($_GET['ip']) && !empty($_GET['ip']));
    }
?>