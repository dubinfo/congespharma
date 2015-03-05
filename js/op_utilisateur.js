$(function(){
    $('input[type="button"]').removeAttr("disabled");
    $(".isAdmin").attr("disabled", "disabled");
    
    $("#btn_give_admin_rank").click(function(){
        id_user = $("#id_user").val();
        $.ajax({
            url : 'ajax/op_user.php',
            type : 'POST',
            data : 'action=accept' + '&id_user=' + id_user,
            dataType : 'json',
            success : function(retour, statut){
                if (retour.retour)
                {
                    $('#btn_give_admin_rank').addClass('isAdmin');
                    $("#btn_give_admin_rank").val("Accepté");
                    $("#btn_give_admin_rank").attr("disabled", "disabled");
                    $("#btn_decline_admin_rank").addClass('hidden');
                }
                else
                {
                    alert("erreur: " + retour.erreur);
                }
            }
        });
    });
    
    $("#btn_decline_admin_rank").click(function(){
        id_user = $("#id_user").val();
        $.ajax({
            url : 'ajax/op_user.php',
            type : 'POST',
            data : 'action=decline' + '&id_user=' + id_user,
            dataType : 'json',
            success : function(retour, statut){
                if (retour.retour)
                {
                    $('#btn_give_admin_rank').addClass('hidden');
                    $("#btn_decline_admin_rank").val("Refusé");
                    $("#btn_decline_admin_rank").attr("disabled", "disabled");
                    alert("Un email a été envoyé à l'utilisateur pour l'informer de votre refus.");
                }
                else
                {
                    alert("erreur: " + retour.erreur);
                }
            }
        });
    });
});