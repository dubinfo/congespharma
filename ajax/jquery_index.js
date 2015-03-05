$(document).ready(function()
{
   //$.ajax(
   //{
   //   type: "POST",
   //   url: "ajax/charger_liste_deroulante_machines.php",
   //   dataType: "json",
   //   success: function(retour)
   //   {
   //      //alert(retour);
   //      $.each(retour.liste_machines,function(idx,cont)
   //      {
   //         $("#sel_choix_machines").append($('<option/>').val(cont.ID).html(cont.nom_machine));
   //      });
   //      //alert(retour.machine_actuelle);
   //      $("#sel_choix_machines option[value='" + retour.machine_actuelle + "']").attr("selected","selected");
   //   },
   //   error: function(retour)
   //   {
   //      alert("erreur");
   //   }
   //}
   //) //fin de $.ajax
   
//   $('#sel_choix_machines').change(function()
//   {
//      var mois = parseInt($('#moisCourant').html(), 10);
//		var annee = parseInt($('#anneeCourante').html(), 10);
//      var machine = this.value;
//      $.ajax(
//      {
//         type: 'POST',
//         url: 'ajax/changer_machine.php',
//         data:{'mois':mois, 'annee':annee, 'machine':machine},
//         dataType: 'text',
//         success: function(retour)
//         {
//            
//               //data = jQuery.parseJSON(data);
//               //calendrier = data.retour;
//               
//               $('#divCalendrier').html('');
//               $('#divCalendrier').html(retour);
//
//            
//         },
//         error: function()
//         {
//            alert("dans erreur");
//         }
//      });
//   });
   
}); //fin de jquery