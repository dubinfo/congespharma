<?php
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', dirname($url));
	define('BASE_PATH', dirname(realpath('.')));
	//Les classes doivent être définies AVANT le session_start(), sinon PHP ne peux pas charger/sauvegarder les objets de page en page
	require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
	session_start();
	
	//if (!isset($_SESSION['utilisateur']) || !($_SESSION['utilisateur'] instanceof Administrateur))
	//{
	//	echo '<p class="validateTips">Vous ne disposez pas des privilèges nécessaires pour accéder à cette page.</p>';
	//	exit;
	//}
	$db = PDO2::getInstance()->db;
	// $_POST['id'] = "19";
	
	// Si pas d'id spécifié, c'est un ajout d'utilisateur
	if (empty($_POST['id']))
	{
		$newUser = true;
	}
	// var_dump($_SERVER);
	else
	{
		$newUser = false; 
		$user = $db->prepare("SELECT matricule, login, nom, prenom, email, rang, statut FROM utilisateurs WHERE id = :id");
		$user->bindParam(":id", $_POST['id'], PDO::PARAM_INT);
		if ($user->execute() && $infos = $user->fetch())
		{
			
		}
		else
		{
			echo '<p class="validateTips">Erreur lors de la récupération des données dans la base de données.</p>';
			exit;
		}
	}
?>
	<form action="#">
		<fieldset>
			<table>
				<tr>
					<td><label for="matricule">Matricule : </label></td>
					<td><input type="text" name="matricule" id="matricule_ajout_user" value="<?php echo ($newUser) ? "" : $infos['matricule']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="login">Login : </label></td>
					<td><input type="text" name="login" id="login_ajout_user" value="<?php echo ($newUser) ? "" : $infos['login']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="nom">Nom : </label></td>
					<td><input type="text" name="nom" id="nom" value="<?php echo ($newUser) ? "" : $infos['nom']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="prénom">Prénom : </label></td>
					<td><input type="text" name="prenom" id="prenom" value="<?php echo ($newUser) ? "" : $infos['prenom']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="mdp">Choisissez un mot de passe : </label></td>
					<td><input type="password" name="mdp" id="mdp" value="<?php echo ($newUser) ? "" : $infos['mdp']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="mdp2">Confirmez votre mot de passe : </label></td>
					<td><input type="password" name="mdp2" id="mdp2" value="<?php echo ($newUser) ? "" : $infos['mdp2']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="email">Email : </label></td>
					<td><input type="text" name="email" id="email" value="<?php echo ($newUser) ? "" : $infos['email']; ?>" /></td>
				</tr>
				
			</table>
		</fieldset>
	</form>
