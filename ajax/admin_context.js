chaine_book_for = {};

/**
* le plugin jquery.contextMenu.js vient de http://medialize.github.com/jQuery-contextMenu/
*/

/*
*
* PARTIE ADMINISTRATEURS (partie utilisateurs plus bas)
*
*
*/

jQuery(document).ready(function($)
{
			chaine_book_for = function()
			{
										var tmp;
										$.ajax(
										{
																	method:'POST',
																	dataType: 'json',
																	async: false, //DD 27/07/2014 il faut laisser en asynchrone autrement cela ne fonctionne pas
																	url:'ajax/liste_users.php',
																	success:function(retour_php)
																	{
																								chaine_book_for = retour_php;
																								tmp = retour_php;
																	}
										});
										return tmp;
			}();
			

/**
* Gestion des menus contextuels
*/

// Empêche l'affichage du menu contextuel par défaut lorsqu'on fait un clic droit sur le calendrier.
$(document).on("contextmenu", "#divCalendrier", function(e)
{
			return false;
});

$(function()
{
/**************************************************
	* Context-Menu with Sub-Menu
	**************************************************/
// Menu apparaissant quand la selection par demi-jour n'est pas encore activée
	$.contextMenu({
		selector: '.disabled', 
		
		items:
		{
			"actived_calendar":
			{
				name: "Activer calendrier",
				callback: function(key, options)
				{
					$(".disabled").removeClass("disabled");
					alert("Vous pouvez maintenant prendre ou annulez un demi-jour de congé \n" +
					      "Mais Veuillez privilégier la sélection de plusieurs jours via les plages de sélection!");
				}	
			},
			"sep1": "---------",
			"annuler": {name: "Annuler"}
		}
	});
$.contextMenu(
			{
				selector: '.libre_éditable',
				callback: function(key, options)
				{
							//le code du callback ci-dessous est réservé au to_book_for (réserver pour quelqu'un d'autre) DD 27/07/2014							
							var id = key.substring(9);

								//var m = "clicked: " + key;
								//window.console && console.log(m) || alert(m);
							var commentaire = prompt("Let a comment if you want");
							var mois = parseInt($('#moisCourant').html(), 10);
							var annee = parseInt($('#anneeCourante').html(), 10);
							$("#ajax-loader").show();
							$.post(
											'ajax/genererCalendrier.php',
											{"mois":mois, "annee":annee, "action":"tobookfor", "id":id, "periode":options.$trigger.attr('id'), "commentaire":commentaire},
											function(data)
											{
															// alert(data);
															data = jQuery.parseJSON(data);
															// alert(data.retour);
															// Si PHP a renvoyé quelque chose, on l'affiche
															if (data.retour) calendrier = data.retour;
															else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
											}
							).success(function()
								{
											$("#ajax-loader").hide();
											$('#divCalendrier').html(calendrier);
							}).error(function()
							{		
											$("#ajax-loader").hide();
											$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
							});
				},
										items:
										{
																	"reserver":
																	{
																								name: "Réserver",
																								callback: function(key, options)
																								{
																												var commentaire = prompt("Let a comment if you want");
																												var mois = parseInt($('#moisCourant').html(), 10);
																												var annee = parseInt($('#anneeCourante').html(), 10);
																												$("#ajax-loader").show();
																												$.post(
																													'ajax/genererCalendrier.php',
																													{"mois":mois, "annee":annee, "action":"réserver", "periode":options.$trigger.attr('id'), "commentaire":commentaire},
																													function(data)
																														{
                                                                                                                                                                                                                                                                                                //alert(data);
																																				data = jQuery.parseJSON(data);
																																				// alert(data.retour);
																																				// Si PHP a renvoyé quelque chose, on l'affiche
																																				if (data.retour) calendrier = data.retour;
																																				else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
																														}
																												).success(function()
																													{
																																$("#ajax-loader").hide();
																																$('#divCalendrier').html(calendrier);
																												}).error(function()
																															{
																																						$("#ajax-loader").hide();
																																						$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
																															});
																								}
																	},

										
										"occuper":
										{
												name: "Occuper",
												callback: function(key, options){
																var commentaire = prompt("Let a comment if you want");
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"occuper", "periode":options.$trigger.attr('id'), "commentaire":commentaire},
																				function(data)
																				{
																								// alert(data);
																								data = jQuery.parseJSON(data);
																								// alert(data.retour);
																								// Si PHP a renvoyé quelque chose, on l'affiche
																								if (data.retour) calendrier = data.retour;
																								else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
																				}
																).success(function()
																	{
																				$("#ajax-loader").hide();
																				$('#divCalendrier').html(calendrier);
																}).error(function(){
																				$("#ajax-loader").hide();
																				$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
																});
												}
										},
										

								"fold1":
										{
																	"name": "To book for ",
																	"items": chaine_book_for,
																	callback: function(key, options)
																	{
																								//alert("ici");
																								var commentaire = prompt("Let a comment if you want");
																								var mois = parseInt($('#moisCourant').html(), 10);
																								var annee = parseInt($('#anneeCourante').html(), 10);
																								$("#ajax-loader").show();
																								$.post(
																												'ajax/genererCalendrier.php',
																												{"mois":mois, "annee":annee, "action":"réserver", "periode":options.$trigger.attr('id'), "commentaire":commentaire},
																												function(data)
																												{
																																// alert(data);
																																data = jQuery.parseJSON(data);
																																// alert(data.retour);
																																// Si PHP a renvoyé quelque chose, on l'affiche
																																if (data.retour) calendrier = data.retour;
																																else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
																												}
																								).success(function()
																									{
																												alert("ok");
																												$("#ajax-loader").hide();
																												$('#divCalendrier').html(calendrier);
																								}).error(function()
																								{
																												alert("pas ok");			
																												$("#ajax-loader").hide();
																												$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
																								});
																	}
										},
																	"sep1": "---------",
																	"annuler": {name: "Annuler"},
							}//fin de items

							}); //fin de $.contextMenu(
});

/*
*
* PARTIE UTILISATEURS (partie administrateurs plus haut)
*
*
*/

// Menu apparaissant pour annuler une proposition de résérvation par les utilisateurs normaux (clic droit sur une case reservée par l'utilisateur)
$.contextMenu({
				selector: '.proposé_éditable',
				callback: function(key, options) {

								var m = "clicked '" + key + "' on element with id '#" + options.$trigger.attr('id') + "'";
								// window.console && console.log(m) || alert(m);
				},
				items: {
								"valider": {
												name: "Valider",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"valider", "periode":options.$trigger.attr('id')},
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
								"refuser": {
												name: "Refuser",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"refuser", "periode":options.$trigger.attr('id')},
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
								"supprimer": {
												name: "Supprimer",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"supprimer", "periode":options.$trigger.attr('id')},
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
								"sep1": "---------",
								"ajouter_commentaire": {
												name: "Ajouter un commentaire",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																var commentaire = prompt("commentaire :");
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"add_comment", "periode":options.$trigger.attr('id'), "commentaire":commentaire},
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

// Menu apparaissant pour annuler une proposition de résérvation par les utilisateurs normaux (clic droit sur une case reservée par l'utilisateur)
$.contextMenu({
				selector: '.occupé_éditable',
				callback: function(key, options) {

								var m = "clicked '" + key + "' on element with id '#" + options.$trigger.attr('id') + "'";
								// window.console && console.log(m) || alert(m);
				},
				items: {
								"valider": {
												name: "Valider",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"valider", "periode":options.$trigger.attr('id')},
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
								"supprimer": {
												name: "Supprimer",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"supprimer", "periode":options.$trigger.attr('id')},
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
								"sep1": "---------",
								"ajouter_commentaire": {
												name: "Ajouter un commentaire",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																var commentaire = prompt("commentaire :");
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"add_comment", "periode":options.$trigger.attr('id'), "commentaire":commentaire},
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

// Menu apparaissant pour annuler une proposition de résérvation par les utilisateurs normaux (clic droit sur une case reservée par l'utilisateur)
$.contextMenu({
				selector: '.accepté_éditable',
				callback: function(key, options) {

								var m = "clicked '" + key + "' on element with id '#" + options.$trigger.attr('id') + "'";
								// window.console && console.log(m) || alert(m);
				},
				items: {
								"refuser": {
												name: "Refuser",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"refuser", "periode":options.$trigger.attr('id')},
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
								"supprimer": {
												name: "Supprimer",
												callback: function(key, options){
                                                                                                                        var commentaire = prompt("Vous pouvez laisser un motif (facultatif)","");
															var mois = parseInt($('#moisCourant').html(), 10);
															var annee = parseInt($('#anneeCourante').html(), 10);
															$("#ajax-loader").show();
															$.post(
																			'ajax/genererCalendrier.php',
																			{"mois":mois, "annee":annee, "action":"supprimer", "periode":options.$trigger.attr('id'), 'commentaire': commentaire},
																			function(data){
																							 //alert(data);
																							data = jQuery.parseJSON(data);
																							 //alert(data.retour);
																							 //Si PHP a renvoyé quelque chose, on l'affiche
																							if (data.retour)
																							{
																											calendrier = data.retour;
																											 debug = data.debug;
																											 //alert(data.debug);
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
								"sep1": "---------",
								"ajouter_commentaire": {
												name: "Ajouter un commentaire",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																var commentaire = prompt("commentaire :");
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"add_comment", "periode":options.$trigger.attr('id'), "commentaire":commentaire},
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

// Menu apparaissant lors d'un clic droit sur une case proposée avec commentaire
$.contextMenu({
				selector: '.proposé_éditable_commenté',
				callback: function(key, options) {

								var m = "clicked '" + key + "' on element with id '#" + options.$trigger.attr('id') + "'";
								// window.console && console.log(m) || alert(m);
				},
				items: {
								"valider": {
												name: "Valider",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"valider", "periode":options.$trigger.attr('id')},
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
								"refuser": {
												name: "Refuser",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"refuser", "periode":options.$trigger.attr('id')},
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
								"supprimer": {
												name: "Supprimer",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"supprimer", "periode":options.$trigger.attr('id')},
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
																// alert(new_commentaire);
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

// Menu apparaissant lors d'un clic droit sur une case occupée avec commentaire
$.contextMenu({
				selector: '.occupé_éditable_commenté',
				callback: function(key, options) {

								var m = "clicked '" + key + "' on element with id '#" + options.$trigger.attr('id') + "'";
								// window.console && console.log(m) || alert(m);
				},
				items: {
								"valider": {
												name: "Valider",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"valider", "periode":options.$trigger.attr('id')},
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
								"supprimer": {
												name: "Supprimer",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"supprimer", "periode":options.$trigger.attr('id')},
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

// Menu apparaissant lors d'un clic droit sur une case acceptée avec commentaire
$.contextMenu({
				selector: '.accepté_éditable_commenté',
				callback: function(key, options) {

								var m = "clicked '" + key + "' on element with id '#" + options.$trigger.attr('id') + "'";
								// window.console && console.log(m) || alert(m);
				},
				items: {
								"refuser": {
												name: "Refuser",
												callback: function(key, options){
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"refuser", "periode":options.$trigger.attr('id')},
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
								"supprimer": {
												name: "Supprimer",
												callback: function(key, options){
                                                                                                                                var commentaire = prompt("Vous pouvez laisser un motif (facultatif)","");
																var mois = parseInt($('#moisCourant').html(), 10);
																var annee = parseInt($('#anneeCourante').html(), 10);
																$("#ajax-loader").show();
																$.post(
																				'ajax/genererCalendrier.php',
																				{"mois":mois, "annee":annee, "action":"supprimer", "periode":options.$trigger.attr('id'), 'commentaire': commentaire},
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

});//fin de jquery