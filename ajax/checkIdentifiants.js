var identifiantsOK;

jQuery(document).ready(function($){
	$('#form1').submit(function(e){
		// On envoie la requête AJAX (méthode POST)
		$.post(
			'ajax/checkIdentifiants.php',
			$('#form1').serialize(),
			//DD: retour php
			function(data){
				//alert(data);
				data = jQuery.parseJSON(data);
				// Si les identifiants sont incorrects : affichage d'un message d'erreur
				if (!data.statut){
					$('#td_gif_identifiants_nok').html('<img src="images/delete-user-icon.jpg" />');
					$('#td_gif_identifiants_nok').fadeIn();
					$('#td_texte_identifiants_nok').html('Login incorrect !');
					$('#td_texte_identifiants_nok').fadeIn();
					$('#matricule').val('');
					$('#login').val('');
					$('#password').val('');
					identifiantsOK = false;
				}
				else
				{
					identifiantsOK = true;
				}
			}
		).success(function(){
			// Si la requete est réussie (status 200 ok ou 0) on soumet vraiment le formulaire à PHP
			// pour rediriger l'utilisateur vers l'application en elle même
			if (identifiantsOK)
			{
				$('#form1').unbind();
				$('#form1').submit();
			}
		});
		// empeche l'action par défaut (ici la soumission du form)
		e.preventDefault();
	});
	
	$('#matricule').click(function(e){
		$('#td_gif_identifiants_nok').fadeOut();
		$('#td_texte_identifiants_nok').fadeOut();
	});
	
	$('#login').click(function(e){
		$('#td_gif_identifiants_nok').fadeOut();
		$('#td_texte_identifiants_nok').fadeOut();
	});
	
	$('#password').click(function(e){
		$('#td_gif_identifiants_nok').fadeOut();
		$('#td_texte_identifiants_nok').fadeOut();
	});
});