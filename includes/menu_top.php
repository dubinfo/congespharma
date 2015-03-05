<?php
	if (isset($_POST['update'])) 
	{
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
		$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
		define('BASE_URL', dirname($url));
		define('BASE_PATH', dirname(realpath('.')));
		// On détermine le chemin vers le fichier autoload.php
		require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
		
		session_start();
	}
	define('ADMIN_RANK', 'administrateur');
	$rang = $_SESSION['utilisateur']->getRang();
?>
	<header>
		<h1>
			<img src="images/footer.jpg"/> Calendrier des congés –
<?php
		$db = PDO2::getInstance()->db;
		$sql = $db->prepare("SELECT libelle
				    FROM service
				    JOIN service_has_utilisateurs ON service_id = id
				    WHERE utilisateurs_id = :id");
		$sql->execute(array(':id' => $_SESSION['utilisateur']->getId()));
		$res = $sql->fetchAll(PDO::FETCH_OBJ);
		for($i = 0; $i < count($res); $i++)
		{
			echo (($i > 0)?' et ': ' ') . 'service ' .$res[$i]->libelle;
		}
?>
		</h1>
		<div id="left_header">
			<p>
				Bienvenue
				<?php
					echo $_SESSION['utilisateur']->getNomComplet();
				?>
				- [<a href="index.php?deconnexion=1">Déconnexion</a>]
			</p>
			<fieldset id="chosen_holiday">
				<select id="plage_action" class="inputHoliday">
					<option value="reserver_plage">Réservation</option>
					<option value="supprimer_plage">Suppression</option>
				</select>
				 de jours de congés du :
				<input type="text" id="plage_debut" readonly="readonly" class="datepicker inputHoliday" />
				au :
				<input type="text" readonly="readonly" id="plage_fin" class="datepicker_fin inputHoliday" />
				<input type="button" value="Confirmer" id="btn_plage" />
			</fieldset>
			<a id="show_documentation" href="#"><img src="./images/help-icon.png" alt="Afficher l'aide pour l'utilisation du calendrier"/></a>
			<p>
				Pour activer la selection ou la suppression d'un demi-jour de congé
				<input type="button" value="cliquez ici" id="actived_half_day">
			</p>
		</div>
		<div id="right_header">
		<?php
			//si c'est un admin, il voit en plus de la synthèse le fichier Excel		
			if($rang == ADMIN_RANK)
			{
		?>
			<a target="_blank" href="./classes/Classes/fiches_par_user.php">Afficher le fichier Excel</a> /
		<?php
			}	
		?>
			<a href="synthese.php" target="_blank">Synthèse des jours de congés</a>
		</div>
	</header>		