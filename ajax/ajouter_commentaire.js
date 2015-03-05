$(document).ready(function(){
	$( "#commentaire-dialog" ).dialog({
		autoOpen: false,
		modal: true,
		height: 260,
		width: 500,
		title: "Modification des données d'un utilisateur",
		buttons: {
			"Valider": function(){
				// alert($( "#categoriePlat option:selected" ).val());
				var majOK = false;
				var erreur;
				$.post(
					'ajax/majReservation.php',
					{"id":$( "#userId" ).html(), "matricule":$( "#matricule" ).val(), "login":$( "#login" ).val(), "nom":$( "#nom" ).val(), "prenom":$( "#prenom" ).val(), "email":$( "#email" ).val(), "rang":$( "#rang" ).val(), "statut":$( "#statut" ).val()},
					function(data){
						alert(data);
						data = jQuery.parseJSON(data);
						if (data.retour)
						{
							majOK = true;
						}
						else
						{
							erreur = data.erreur;
						}
					}
				).success(function(){
					if (majOK)
					{
						alert('succès');
						location.reload(); 
					}
					else
					{
						alert('erreur : ' + erreur);
					}
				}).error(function(){
					alert('erreur');
				});
				//------
				$( "#editUser-form" ).dialog('close');
			},
			"Retour": function(){
				$( "#editUser-form" ).dialog('close');
			}
		}
	});
});