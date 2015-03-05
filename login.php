<?php
	session_start();
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', $url);
	define('BASE_PATH', realpath('.'));
	include("includes/test_version_IE.php");
	require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
	
	$db = PDO2::getInstance()->db;
	
	if(!empty($_POST['matricule']) || !empty($_POST['login']) || !empty($_POST['password']))
	{
		
		// préparation des variables
		$matricule = $_POST['matricule'];
		$login = $_POST['login'];
		$password = sha1($_POST['password']);
		
		// préparation et execution de la requête
		$verifLogin = $db->prepare('SELECT u.id,
					           u.matricule,
						   u.nom,
						   u.prenom,
						   u.email,
						   r.libelle,
						   u.statut,
						   u.rang,
						   shu.service_id
						FROM utilisateurs u
						LEFT JOIN statuts_utilisateurs su
						ON u.statut = su.id
						LEFT JOIN rangs r ON u.rang = r.id
						JOIN service_has_utilisateurs shu
						ON shu.utilisateurs_id = u.id
						WHERE su.libelle <> :statut
						AND u.matricule = :matricule
						AND login = :login
						AND password = :password');
		$verifLogin->bindValue(':statut', 'supprimé');
		$verifLogin->bindParam(':matricule', $matricule);
		$verifLogin->bindParam(':login', $login);
		$verifLogin->bindParam(':password', $password);
		$verifLogin->execute();
		
		// Si le serveur retourne un résultat, l'utilisateur s'est correctement connecté
		if ($connecte = $verifLogin->fetch())
		{
			switch ($connecte['statut'])
			{
				case 1:
					// On instancie un objet Utilisateur auquel on relie les informations de l'utilisateur
					if ((int)$connecte['rang'] == 1)
					{
						$_SESSION['utilisateur'] = new Administrateur($connecte['id'], $connecte['matricule'], $connecte['nom'], $connecte['prenom'], $connecte['email'], $connecte[5]);
					}
					else
					{
						$_SESSION['utilisateur'] = new Utilisateur($connecte['id'], $connecte['matricule'], $connecte['nom'], $connecte['prenom'], $connecte['email'], $connecte[5]);
					}
					$_SESSION['utilisateur']->addService($connecte['service_id']);
					
					if($connecte = $verifLogin->fetch())
					{
						$_SESSION['utilisateur']->addService($connecte['service_id']);
					}
					//echo '<pre>';
					//print_r($_SESSION);
					//echo '</pre>';
					header('Location: index.php');
					break;
				case 3:
					// Si le compte est desactivé, on affiche un message pour en informer l'utilisateur
					if (isset($_SESSION['accountProblem'])) unset($_SESSION['accountProblem']);
					$_SESSION['accountProblem'] = array('desactive'=>'Your account was suspended by the administrator. Please contact Pierre Van Antwerpen (pvantwer@ulb.ac.be) or Cédric Delporte (cedric.delporte@ulb.ac.be) for more informations.');
					break;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-FR" lang="fr-FR">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Faculté de Pharmacie - congespharma : Authentification</title>
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="ajax/checkIdentifiants.js"></script>
		<script type="text/javascript" src="ajax/jquery_login.js"></script>
		<script type="text/javascript" src="js/jquery-ui.js"></script>
		<script type="text/javascript" src="js/jquery.ui.position.js"></script>
		<script type="text/javascript" src="js/login.js"></script>
		<script type="text/javascript" src="js/documentation.js"></script>
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<link rel="stylesheet" type="text/css" href="css/login.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css" />
	</head>
	<body>
		<header>
			<img src="images/footer.jpg"/> <h1>Calendrier des congés – Faculté de Pharmacie</h1>	
		</header>
		
		<div id="debug"><?php //if(isset($_SESSION['utilisateur'])) print_r($_SESSION['utilisateur']); ?></div>
		<div id="body_login">
			<div id="contenu" class="bloc_login">
				<form action="login.php" method="post" id="form1">
					<p>
						<img src="images/user-lock-icon.jpg" alt="S'identifier" width="48" height="48" /><span class="Style6"><strong>Identification</strong></span>
					</p>
					<p>
						<input name="matricule" type="text" id="matricule" tabindex="1" />
						<strong>Matricule</strong>
					</p>
					<p>
						<input name="login" type="text" id="login" tabindex="2" />
						<strong>NetID</strong>
					</p>
					<p>
						<input name="password" type="password" id="password" tabindex="3" />
						<strong>Mot de passe</strong>
					</p>
					<p>
						<input type="submit" name="btn_identification" value="Connexion" tabindex="8" />
					</p>
					<p>
						<a id="password_forgot" href="#password_forgot_request">Mot de passe oublié ?</a>
					</p>
					<p>
						Pas encore inscrit ? -->
						<img style="cursor: pointer" src="images/ajouterUtilisateur.png" id="ajouterUtilisateur" title="S'inscire"/>
					</p>
					<p>
						<span id="td_gif_identifiants_nok">&nbsp;</span>
						<span id="td_texte_identifiants_nok">&nbsp;</span>
					</p>
				</form>
			</div>
			<div id="nouvel_utilisateur" class="bloc_login">
				<form action="#">
					<h2 class="Style6">Veuillez encoder vos informations <a id="close_inscription" href="#">[fermer]</a></h2>
					<fieldset>
						<table id="table_inscription">
							<tr>
								<td><label for="matricule">Matricule : </label></td>
								<td><input type="text" name="matricule" id="matricule_ajout_user"/></td>
							</tr>
							<tr>
								<td><label for="login">NetID : </label></td>
								<td><input type="text" name="login" id="login_ajout_user"/></td>
							</tr>
							<tr>
								<td><label for="nom">Nom : </label></td>
								<td><input type="text" name="nom" id="nom"/></td>
							</tr>
							<tr>
								<td><label for="prénom">Prénom : </label></td>
								<td><input type="text" name="prenom" id="prenom"/></td>
							</tr>
							<tr>
								<td><label for="mdp">Choisissez un mot de passe : </label></td>
								<td><input type="password" name="mdp" id="mdp"/></td>
							</tr>
							<tr>
								<td><label for="mdp2">Confirmez votre mot de passe : </label></td>
								<td><input type="password" name="mdp2" id="mdp2"/></td>
							</tr>
							<tr>
								<td><label for="email">Email : </label></td>
								<td><input type="text" name="email" id="email"/></td>
							</tr>
							<tr id="line_service1">
								<td><label for="service1">Service : </label></td>
								<td>
									<select id="service1">
										<option value="">Choisissez votre service</option>
									<?php
										$request = $db->prepare('SELECT * FROM service s ORDER BY libelle');
										$request->execute();
										$result = $request->fetchAll(PDO::FETCH_OBJ);
										foreach($result as $value)
										{
											echo '<option value="' . $value->id . '">' . $value->libelle. '</option>';
										}
									?>
									</select>
								</td>
							</tr>
							<tr>
								<td><label for="admin_rank">Etes-vous le chef hiérarchique du service ? </label></td>
								<td>
									<input type="checkbox" id="admin_rank" name="admin_rank" value="admin_rank">
								</td>
							</tr>
						</table>
					</fieldset>
					<input type="button" name="btn_inscription" id="btn_inscription" value="Valider"/>
					<input type="reset" name="btn_annuler" id="btn_annuler" value="annuler"/>
				</form>
			</div>
		</div>
		<div id="password_forgot_request" title="test">
			<p id="top_forgot_password">Veuillez entrer l'adresse e-mail utilisée lors de votre inscription sur le site congespharma.</p>
			<form id="password_forgot_form">
				<label for="email">E-mail:</label>
				<input name="password_forgot_email" type="text" id="password_forgot_email"/>
				<input type="button" name="btn_password_forgot_confirm" id="btn_password_forgot_confirm" value="Valider"/>
				<input type="button" name="btn_password_forgot_cancel" id="btn_password_forgot_cancel" value="Annuler"/>
			</form>
			<p id="error_forgot_password" class="error_message"></p>
		</div>
		<a id="show_documentation"><img src="./images/help-icon.png" alt="Afficher l'aide pour l'utilisation du calendrier"/></a>
		<?php require_once "includes/documentation.php"; ?>
		<footer>
			<p style="text-align: center">Développé par David Dubois en décembre 2014</p>
			<p style="color: red;text-align: center"><strong>Dernière mise à jour : 20 février 2015</strong></p>
		</footer>
	</body>
</html>