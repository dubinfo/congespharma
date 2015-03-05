$(document).ready(function(){
 
  $('.supprimer_conge').on('click',function()
  {
      var id_decompose = $(this).attr('id').split('#');
      //l'id du user pour lequel on veut suprimer un jour de congé
      var id_user = id_decompose[0];
      //le jour en quetion à supprimer
      var jour = id_decompose[1];
      var mail = id_decompose[2];
      var ok = confirm("Voulez-vous vraiment supprimer ce jour de cong\350 ?");
      if (ok)
      {
        var motif = prompt("Veuillez indiquer une raison qui sera envoy\351e \340 l'utilisateur par mail");
        $.ajax
        (
         {
           type:'POST',
           url: 'ajax/supprimer_jours.php',
           data:
           {
             'id_user':id_user,
             'jour': jour,
             'motif':motif,
             'mail':mail
           },
           success:function(retour)
           {
             location.reload();
           },
           error:function()
           {
             alert("pas ok, erreur");
           }
         }
        )
      }
      
      
  });
});