jQuery(document).ready(function($){
	$(document).on("click", "#mail", function(e){
		e.preventDefault();
		if (!confirm("Un email va être envoyé à Pierre Van Antwerpen et Cédric Delporte.\nConfirmer votre(vos) réservation(s) ?")) return false;
		var erreur;
		$.post(
			'ajax/mail.php',
			{"action":"mail"},
			function(data){
				// alert(data);
				data = jQuery.parseJSON(data);
				// alert(data.retour);
				// Si PHP a renvoyé quelque chose, on l'affiche
				if (!data.reponse) erreur = data.erreur;
				else erreur = false;
			}
		).success(function(){
			if (!erreur)
			{
				var menuTop;
				$.post(
					'includes/menu_top.php',
					{"update":"update"},
					function(data){
						// alert(data);
						// alert(data.debug);
						menuTop = data;
						// Si PHP a renvoyé quelque chose, on l'affiche
					}
				).success(function(){
					$("#menuTop").html(menuTop);
				}).error(function(){
					$("#menuTop").html("<p>L'application a rencontré une erreur.</p>");
				});
				alert("Email envoyé");
			}
			else alert("Erreur, l'email n'a pas été envoyé");
		}).error(function(){
			alert("Erreur lors de la requête AJAX");
		});
	});
});