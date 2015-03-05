/**
* le plugin jquery.contextMenu.js vient de http://medialize.github.com/jQuery-contextMenu/
*/
	
jQuery(document).ready(function($){
	
	/**
	* Gestion des menus contextuels
	*/
	
	// Empêche l'affichage du menu contextuel par défaut lorsqu'on fait un clic droit sur le calendrier.
	$(document).on("contextmenu", "#divCalendrier", function(e){
		return false;
	});
	
	// Menu apparaissant quand la selection par demi-jour n'est pas encore activée
	$.contextMenu({
		selector: '.disabled', 
		
		items:
		{
			
			"sep1": "---------",
			"annuler": {name: "Opération non autorisée"}
		}
	});
	// Menu apparaissant pour une proposition de résérvation par les utilisateurs normaux (clic droit sur une case libre)
	$.contextMenu({
		selector: '.libre_éditable', 
		callback: function(key, options) {
			
			var m = "clicked '" + key + "' on element with id '#" + options.$trigger.attr('id') + "'";
			// window.console && console.log(m) || alert(m);
		},
		items:
		{
			"proposer":
			{
				name: "Prendre congé",
				callback: function(key, options)
				{
							var commentaire = prompt("Vous pouvez laisser un motif (facultatif)");
							// alert('"'+commentaire+'"');
							$("#ajax-loader").show();
							var mois = parseInt($('#moisCourant').html(), 10);
							var annee = parseInt($('#anneeCourante').html(), 10);
							var debug = "ok";
					$.post(
						'ajax/genererCalendrier.php',
						{"mois":mois, "annee":annee, "action":"réserver", "periode":options.$trigger.attr('id'), "commentaire":commentaire},
						function(data)
						{
							 //alert(data);
							data = jQuery.parseJSON(data);
							// alert(data.debug);
							debug = data.debug;
							// Si PHP a renvoyé quelque chose, on l'affiche
							if (data.retour) calendrier = data.retour;
							else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
						}
					).success(function()
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
						).success(function()
						{
							$("#menuTop").html(menuTop);
							//DD => 7/2/2015: je redéclare les datepickers, autrement Bug, les datespickers ne s'affichent plus
							$(".datepicker").datepicker(
										{
											changeMonth: true,
											changeYear: true,
											dateFormat: 'dd-mm-yy',
											onSelect: function(date)
											{
												
												var date1 = $('.datepicker').datepicker('getDate');           
												var date = new Date( Date.parse( date1 ) ); 
												date.setDate( date.getDate() );        
												var newDate = date.toDateString(); 
												newDate = new Date( Date.parse( newDate ) );                      
												$('.datepicker_fin').datepicker("option","minDate",newDate);            
											}
										});
										
										$( ".datepicker_fin" ).datepicker(
										{
											changeMonth: true,
											changeYear: true,
											dateFormat: 'dd-mm-yy'
										}
										);
							
							
						}).error(function(){
							$("#menuTop").html("<p>L'application a rencontré une erreur.</p>");
						});
						$("#ajax-loader").hide();
						$('#divCalendrier').html(calendrier + "<p>" + debug + "</p>");
					}).error(function(){
						$("#ajax-loader").hide();
						$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
					});
				}
			},
			"sep1": "---------",
			"annuler": {name: "Annuler"}
		}
	});
	
	// Menu apparaissant pour annuler un congé 
	$.contextMenu({
        selector: '.accepté, .accepté_commenté', 
        callback: function(key, options) {
			
            var m = "clicked '" + key + "' on element with id '#" + options.$trigger.attr('id') + "'";
			// window.console && console.log(m) || alert(m);
        },
        items:
								{
            "retirer": {
				name: "Annuler la demande de congé",
				callback: function(key, options)
				{
					var commentaire = prompt("Vous pouvez laisser un motif (facultatif)","");
					var mois = parseInt($('#moisCourant').html(), 10);
					var annee = parseInt($('#anneeCourante').html(), 10);
					//pour résoudre le probleme d'IE qui met par défault undefined
					//$_SESSION['motif'] = prompt("Vous pouvez laisser un motif (facultatif)","");
					$("#ajax-loader").show();
					$.post(
						'ajax/genererCalendrier.php',
						{"mois":mois, "annee":annee, "action":"retirer", "periode":options.$trigger.attr('id'), 'commentaire': commentaire},
						function(data)
						{
							// alert(data);
							data = jQuery.parseJSON(data);
							// alert(data.retour);
							// Si PHP a renvoyé quelque chose, on l'affiche
							if (data.retour)
							{
								calendrier = data.retour;
								// debug = data.debug;
								// alert(data.debug);
							}
							else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
						}
					).success(function()
							{
						$("#ajax-loader").hide();
						$('#divCalendrier').html(calendrier);
						$('#debug-div').html(debug);
					}).error(function(){
						$("#ajax-loader").hide();
						$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
					});
				}
			},
            "sep1": "---------",
			"modifier_commentaire": {
				name: "Modifier le commentaire",
				callback: function(key, options){
					var mois = parseInt($('#moisCourant').html(), 10);
					var annee = parseInt($('#anneeCourante').html(), 10);
					// alert(options.$trigger.attr('id'));
					var commentaire = $("#" + options.$trigger.attr('id') + " div div img").attr("title");
					var new_commentaire = prompt("commentaire : ", commentaire);
					if (new_commentaire == null) new_commentaire = commentaire;
					$("#ajax-loader").show();
					$.post(
						'ajax/genererCalendrier.php',
						{"mois":mois, "annee":annee, "action":"update_comment", "periode":options.$trigger.attr('id'), "commentaire":new_commentaire},
						function(data){
							// alert(data);
							data = jQuery.parseJSON(data);
							// alert(data.retour);
							// Si PHP a renvoyé quelque chose, on l'affiche
							if (data.retour)
							{
								calendrier = data.retour;
								// debug = data.debug;
								// alert(data.debug);
							}
							else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
						}
					).success(function(){
						$("#ajax-loader").hide();
						$('#divCalendrier').html(calendrier);
						$('#debug-div').html(debug);
					}).error(function(){
						$("#ajax-loader").hide();
						$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
					});
				}
			},
            "supprimer_commentaire": {
				name: "Supprimer le commentaire",
				callback: function(key, options){
					var mois = parseInt($('#moisCourant').html(), 10);
					var annee = parseInt($('#anneeCourante').html(), 10);
					$("#ajax-loader").show();
					$.post(
						'ajax/genererCalendrier.php',
						{"mois":mois, "annee":annee, "action":"delete_comment", "periode":options.$trigger.attr('id')},
						function(data){
							// alert(data);
							data = jQuery.parseJSON(data);
							// alert(data.retour);
							// Si PHP a renvoyé quelque chose, on l'affiche
							if (data.retour)
							{
								calendrier = data.retour;
								// debug = data.debug;
								// alert(data.debug);
							}
							else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
						}
					).success(function(){
						$("#ajax-loader").hide();
						$('#divCalendrier').html(calendrier);
						$('#debug-div').html(debug);
					}).error(function(){
						$("#ajax-loader").hide();
						$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
					});
				}
			},
			"sep2": "---------",
            "annuler": {name: "Annuler"}
        }
    });
});