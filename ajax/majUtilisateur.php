<?php
	define('EMAIL_FROM', 'congespharma@ulb.ac.be');
	define('NAME_FROM', 'Congespharma');
	define('EMAIL_WEBMASTER', 'ddubois2@gmail.com');
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'];
	define('URL', $url . '/op_utilisateur.php');
	
	session_start();
	if(!empty($_POST['id'])
		&& !empty($_POST['matricule'])
		&& !empty($_POST['login'])
		&& !empty($_POST['nom'])
		&& !empty($_POST['prenom'])
		&& !empty($_POST['mdp'])
		&& !empty($_POST['email'])
		&& !empty($_POST['service1'])
		)
	{
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
		$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
		define('BASE_URL', dirname($url));
		define('BASE_PATH', dirname(realpath('.')));
		require_once('../mailer/class.phpmailer.php');
		// On détermine le chemin vers le fichier autoload.php
		require_once(dirname(dirname(__file__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
		
		$db = PDO2::getInstance()->db;
		$code = sha1($_POST['email']) . sha1(time());
		if (!$erreur = EmailAndMatriculeNotExit($db))
		{
			$request = $db->prepare("INSERT INTO utilisateurs (matricule, login, nom, prenom, email, statut, code, password)
							VALUES (:matricule, :login, :nom, :prenom, :email, :statut, :code, :password)");

			if($request->execute(array(':matricule' => $_POST['matricule'],
						   ':login' => $_POST['login'],
						   ':nom' => $_POST['nom'],
						   ':prenom' => $_POST['prenom'],
						   ':email' => $_POST['email'],
						   ':statut' => 1,
						   ':code' => $code,
						   ':password' => sha1($_POST['mdp']))))
			{
				$id_utilisateur = $db->lastInsertId();
				$request = $db->prepare("INSERT INTO service_has_utilisateurs (service_id, utilisateurs_id) VALUES (:service, :utilisateur)");
				$request->execute(array(':service' => $_POST['service1'], ':utilisateur' => $id_utilisateur));
				
				if(!empty($_POST['service2']))
				{
					$request = $db->prepare("INSERT INTO service_has_utilisateurs (service_id, utilisateurs_id) VALUES (:service, :utilisateur)");
					$request->execute(array(':service' => $_POST['service2'], ':utilisateur' => $id_utilisateur));	
				}
				
				if($_POST['isAdmin'] == 1)
				{
					sendEmailForAdmin($id_utilisateur);
				}
				
				echo json_encode(array("retour"=>true));
			}
			else
			{
				echo json_encode(array("retour"=>false, "erreur"=>"Erreur Lors de l'enregistrement."));
			}
		}
		else
		{
			echo json_encode(array("retour"=>false, "erreur"=>$erreur));
		}
	}
	else
	{
		echo json_encode(array("retour"=>false, "erreur"=>"Il manque des données."));
	}
	
	function EmailAndMatriculeNotExit($db){
		$request = $db->prepare("SELECT id FROM utilisateurs WHERE matricule = :matricule");
		$request->execute(array(':matricule'=>$_POST['matricule']));
		if($request->fetchAll())
		{
			return "Ce matricule est déjà utilisé sur le site.";
		}
		
		$request = $db->prepare("SELECT id FROM utilisateurs WHERE email = :email");
		$request->execute(array(':email'=>$_POST['email']));
		if($request->fetchAll())
		{
			return "Cette adresse email est déjà utilisée sur le site.";
		}
		
		return false;	
	}
	
	function sendEmailForAdmin($id)
	{
		//si on ne coche pas la checkbox admin, on n'envoi pas de mail
		
		$mail = new PHPMailer();
                
		$mail->CharSet = 'UTF-8';
		$mail->From = EMAIL_FROM;
		$mail->FromName = NAME_FROM;
		$mail->Subject = "Congespharma - Inscription d'un administrateur";
		
		$email = $_POST['email'];
		$key = new Encryption([$id, $email]);
		$body ='<div align="center"><img src="http://s23.postimg.org/b9c7bk0x7/ulb.jpg"/></div>
			<p>'  . $_POST['prenom'] . ' ' . $_POST['nom'] . ' s\'est inscrit sur le site
			de congespharma et a demandé(e) des droits d\'administrations.
			</p>
			<p>
			   Cliquez sur le lien ci-dessous pour voir plus d\'information sur la demande de droits
			   d\'administrations et pour la valider ou non.
			</p>
			<p><a href="'. URL .'?email='. $email . '&key=' . $key->getTextHasher(). '&ip=' . $_SERVER['REMOTE_ADDR'].
			'">Lien pour voir la demande</a></p>
			<p style="font-style: italic;">Merci de ne pas répondre à ce message!</p>';
		
		$mail->MsgHTML($body);
		$mail->AltBody = "Ce message est au format HTML, votre messagerie n'accepte pas ce format.";
	    
		$mail->addAddress(EMAIL_WEBMASTER);
		
		$mail->send();
	}
?>