$(function(){
   $("#month_filter").change(function(){
      refreshSynthese();
   });
   
   $("#person_filter").change(function(){
      refreshSynthese();
   });
   
   $("#year_filter").change(function(){
      refreshSynthese();   
   });
   
   $("#service_filter").change(function(){
      refreshSynthese();
   })
   
   //methode pour rafraichir la synthèse
   function refreshSynthese(){
      month = $("#month_filter option:selected").val();
      year = $("#year_filter option:selected").val();
      person = $("#person_filter option:selected").val() || "";
      service = $("#service_filter option:selected").val() || "";
      $.ajax({
         url : 'ajax/refresh_synthese.php',
         type : 'POST',
         data : 'month=' + month + '&personId=' + person + '&year=' + year + '&service=' + service,
         dataType : 'json',
         success : function(retour, statut){
            if (retour.retour)
            {
               $("#table_synthese").html(retour.html);
            }
            else
            {
               alert("erreur: " + retour.erreur);
            }
         }
      });
   }
});