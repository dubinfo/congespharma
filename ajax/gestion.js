jQuery(document).ready(function($){
	$(document).on("click", "#users", function(e){
		e.preventDefault();
		var contenu;
		$.post(
			'ajax/gestion.php',
			{"action":"gererUtilisateurs"},
			function(data){
				// alert(data);
				data = jQuery.parseJSON(data);
				// alert(data.retour);
				// Si PHP a renvoyé quelque chose, on l'affiche
				if (data.retour) contenu = data.retour;
				else contenu = '<p class="error">Erreur de chargement des utilisateurs</p>';
			}
		).success(function(){
			$('#divCalendrier').html(contenu);
		}).error(function(){
			$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
		});
	});
	
	$(document).on("click", "#navCalendrier", function(e){
		e.preventDefault();
		var contenu;
		$.post(
			'ajax/genererCalendrier.php',
			{"action":"gererUtilisateurs"},
			function(data){
				// alert(data);
				data = jQuery.parseJSON(data);
				// alert(data.retour);
				// Si PHP a renvoyé quelque chose, on l'affiche
				if (data.retour) contenu = data.retour;
				else contenu = '<p class="error">Erreur de chargement des utilisateurs</p>';
			}
		).success(function(){
			$('#divCalendrier').html(contenu);
		}).error(function(){
			$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
		});
	});
	
});