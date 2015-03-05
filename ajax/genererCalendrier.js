var ACTION_SUPPRIMER = "supprimer_plage"
var calendrier = '';
var debug = '';

jQuery(document).ready(function($)
{
	/*
	* clic sur 'année précedente'
	*/
	$(document).on("click", "#prevYear", function(e)
	{
		var mois = $('#moisCourant').html();
		var annee = parseInt($('#anneeCourante').html(), 10) - 1;
		// On envoie la requête AJAX (méthode POST)
		// alert('mois : ' + mois + '\nannée : ' + annee);
		$.post(
			'ajax/genererCalendrier.php',
			{"mois":mois, "annee":annee},
			function(data)
   {
				//alert(data);
				data = jQuery.parseJSON(data);
				//alert(data.retour);
				// Si PHP a renvoyé quelque chose, on l'affiche
				if (data.retour) calendrier = data.retour;
				else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
			}
		).success(function()
  {
			$('#divCalendrier').html(calendrier);
		}).error(function()
  {
			$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
		});
	});
	
	/*
	* clic sur 'année suivante'
	*/
	$(document).on("click", "#nextYear", function(e)
 {
		var mois = $('#moisCourant').html();
		var annee = parseInt($('#anneeCourante').html(), 10) + 1;
		// On envoie la requête AJAX (méthode POST)
		// alert('mois : ' + mois + '\nannée : ' + annee);
		$.post(
			'ajax/genererCalendrier.php',
			{"mois":mois, "annee":annee},
			function(data){
				
				data = jQuery.parseJSON(data);
				// alert(data.retour);
				// Si PHP a renvoyé quelque chose, on l'affiche
				if (data.retour) calendrier = data.retour;
				else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
			}
		).success(function(){
			$('#divCalendrier').html(calendrier);
		}).error(function(){
			$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
		});
	});
	
	/*
	* clic sur 'mois précedent'
	*/
	$(document).on("click", "#prevMonth", function(e){
		var mois = parseInt($('#moisCourant').html(), 10) - 1;
		var annee = parseInt($('#anneeCourante').html(), 10);
		if (mois == 0)
		{
			mois = 12;
			annee -= 1;
		}
		// On envoie la requête AJAX (méthode POST)
		// alert('mois : ' + mois + '\nannée : ' + annee);
		$.post(
			'ajax/genererCalendrier.php',
			{"mois":mois, "annee":annee},
			function(data){
				
				data = jQuery.parseJSON(data);
				// alert(data.retour);
				// Si PHP a renvoyé quelque chose, on l'affiche
				if (data.retour) calendrier = data.retour;
				else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
			}
		).success(function(){
			$('#divCalendrier').html(calendrier);
		}).error(function(){
			$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
		});
	});
	
	/*
	* clic sur 'mois suivant'
	*/
	$(document).on("click", "#nextMonth", function(e){
		var mois = parseInt($('#moisCourant').html(), 10) + 1;
		var annee = parseInt($('#anneeCourante').html(), 10);
		if (mois == 13)
		{
			mois = 1;
			annee += 1;
		}
		// On envoie la requête AJAX (méthode POST)
		// alert('mois : ' + mois + '\nannée : ' + annee);
		$.post(
			'ajax/genererCalendrier.php',
			{"mois":mois, "annee":annee},
			function(data){
				// alert(data);
				data = jQuery.parseJSON(data);
				// alert(data.retour);
				// Si PHP a renvoyé quelque chose, on l'affiche
				if (data.retour) calendrier = data.retour;
				else calendrier = '<p class="error">Erreur de chargement du calendrier</p>';
			}
		).success(function(){
			$('#divCalendrier').html(calendrier);
		}).error(function(){
			$('#divCalendrier').html('<p class="error">Erreur lors de la requête AJAX</p>');
		});
	});
 
 //si on ne choisit pas de date dans la première zone de texte (date de début), j'efface la donnée chosiie dans date de fin
 $('#plage_fin').on("change",function()
 {
      if($('#plage_debut').val() == '')
      {
          $(this).val('');
          alert("Veuillez choisir une date de d\351but svp !");
      }
      
 });
	
 //clic sur le bouton btn_plage
 //DD => 7/2/2015: obligé de mettre ceci, autrement Bug, la fonction ne s'enclenche plus après un appel ajax
 $(document).on("click", "#btn_plage", function(e)
 {
	//je vérifie que les deux dates choisies sont valides
	if(($('#plage_debut').val() == '') || ($('#plage_fin').val() == ''))
	{
	  alert("Veuillez choisir une plage de dates valide svp !");
	  return false;
	}
	var action = $("#plage_action option:selected").val();
	var debut = $("#plage_debut").val();
	var fin = $('#plage_fin').val();
	var error = false;
	
	if (action == ACTION_SUPPRIMER && todayOrPreviousDays(debut)) {
		if (todayOrPreviousDays(fin)) {
			alert("Vous ne pouvez pas supprimer des congés ultérieurs au égal à aujourd'hui!");
			error = true;
		}
		else
		{
			var tomorrow = new Date();
			tomorrow.setTime(tomorrow.getTime() + 24 * 3600 * 1000);
			debut = ((tomorrow.getDate() < 10)? "0":"") + tomorrow.getDate() + "-" + ((tomorrow.getMonth() < 9)? "0":"") + (tomorrow.getMonth()+1) + "-" + tomorrow.getFullYear();
			error = !confirm("Vous ne pouvez pas supprimer des congés ultérieurs ou égal à aujourd'hui!\nVoulez vous continuer en partant du " + debut);
		}
	}
	
	
	if (!error) {
		var commentaire = prompt("Vous pouvez laisser un motif (facultatif)","");
		$("#ajax-loader").show();
		$.ajax(
		{
		 
		 type:'POST',
		 url:'ajax/genererCalendrier.php',
		 data:
		 {
		   'debut': debut,
		   'fin': fin,
		   'action': action,
		   'commentaire': commentaire
		 },
		 dataType:'json',
		 success:function(data)
		 {
		   if (data.retour) calendrier = data.retour;
		   $('#divCalendrier').html(calendrier);
		   $("#ajax-loader").hide();
		   if (action == ACTION_SUPPRIMER)
     {
        if (debut == fin)
        {
         alert("Jour de congé supprimé");
        }
        else
        {
         alert("Jour de congés supprimés");
        }
        
          }
          else
          {
        if (debut == fin)
        {
         alert("Jours de congé enregistré");
        }
        else
        {
         alert("Jours de congés enregistrés");
        }
     }
		   $('#plage_action option').prop('selected', false)
				 .filter('[value="reserver_plage"]')
				 .prop('selected', true);
		 },
		 error: function()
		 {
		   $("#ajax-loader").hide();
		   alert("erreur");
		 }
		 
		});
	}
	$('#plage_debut, #plage_fin').val('');
 });
	
	function todayOrPreviousDays(date_string){
		var date= new Date(date_string.substr(6), date_string.substr(3,2)-1, date_string.substr(0,2), 0, 0, 0, 0);
		var today = new Date();
		return today > date;
	}
});