$(function() {
    $("#nouvel_utilisateur").hide();
    
    $("#ajouterUtilisateur").click(function() {
        clearInscription();
        $("#nouvel_utilisateur").show();
    });
    
    $("#close_inscription").click(function(){
        $("#nouvel_utilisateur").hide();
    });
    
    $("#btn_annuler").click(function(){
        $("#nouvel_utilisateur").hide();
    });
    
    $("#service1").change(function(){
        var service = $("#service1 option:selected").val();
        if ($("#service2").length) {
            if (service == "") {
                var service2 = $("#service2 option:selected").val();
                if (service2 != "") {
                    $("#service1").removeAttr("selected");
                    $("#service1 option['" + service2 + "']").attr("selected", "selected");
                }
                $("#line_service2").remove();
            }
        }
        else
        {
            $.ajax({
                url : 'ajax/load_select_service.php',
                type : 'POST',
                data : 'service1=' + service,
                dataType : 'json',
                success : function(data, statut){
                     if (data.retour)
                     {
                         $("#line_service1").after(data.html);
                     }
                     else
                     {
                         alert("erreur: " + retour.erreur);
                     }
                }
             });
        }
    });
    
    $("#btn_inscription").click(function()
    {
        //alert($( "#categoriePlat option:selected" ).val());
        var majOK = false;
        var bValid = true;
        var mdp1 = $( "#mdp" ).val();
        var mdp2 = $( "#mdp2" ).val();
        var service = $("#service1 option:selected").val();
        var service2 = $("#service2 option:selected").val();
        if (mdp1 != mdp2)
        {
            alert("Les mots de passe ne correspondent pas !");
            return false;
        }

        if (!emailIsCorrect())
        {
            alert("Format de l'email incorrect !");
            return false;
        }
        
        if (service == "")
        {
            alert("Veuillez choisir votre service !");
            return false;
        }
        
        if (service == service2)
        {
            alert("Vous avez choisi deux fois le même service!");
            return false;
        }
        var erreur;
        
        if ( bValid )
        {
        
                if ($('#admin_rank').is(':checked'))
                {
                    isAdmin = 1;
                }
                else
                {
                    isAdmin = 0;
                }
                $.post(
                        'ajax/majUtilisateur.php',
                        {
                                "matricule":$( "#matricule_ajout_user" ).val(),
                                "login":$( "#login_ajout_user" ).val(),
                                "nom":$( "#nom" ).val(),
                                "prenom":$( "#prenom" ).val(),
                                "email":$( "#email" ).val(),
                                'id':-1,
                                'mdp': mdp1,
                                'service1' : service,
                                'service2' : service2,
                                'isAdmin' : isAdmin
                        },
                        function(data)
                        {
                                $('#matricule').val($('#matricule_ajout_user').val());
                                $('#login').val($('#login_ajout_user').val());
                                $('#password').val($('#mdp').val());
                        
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
                            retour = "Ajout OK, vous pouvez vous connecter au site";
                            if (isAdmin)
                            {
                                retour += "\nVotre rang d'administrateur doit être validé!"
                            }
                            alert(retour);
                            $("#nouvel_utilisateur").hide();
                            clearInscription();
                        }
                        else
                        {
                                alert('erreur : ' + erreur);
                        }
                }).error(function(){
                        alert('erreur');
                });
        }
        return true;
    })
    
    function clearInscription() {
        $('#matricule_ajout_user').val("");
        $('#login_ajout_user').val("");
        $('#nom').val("");
        $('#prenom').val("");
        $('#mdp').val("");
        $('#mdp2').val("");
        $('#email').val("");
    }
    
    function emailIsCorrect(field) {
        field = field || "email";
        var email = $("#" + field).val();
        var pattern_email = /^[a-zA-Z0-9._-]{2,}@[a-z0-9._-]{2,}\.[a-z]{2,4}$/;
        return pattern_email.test(email);
    }
    
    //mot de passe oublié
    $("#password_forgot_request").hide();
    $("#password_forgot").click(function(){
        $("#password_forgot_request").toggle();
    });
    
    $("#btn_password_forgot_confirm").click(function()
    {
        
        if (emailIsCorrect("password_forgot_email")) {
            email = $("#password_forgot_email").val();
            $.ajax({
                url : './ajax/password_forgot_send_email.php',
                type : 'POST',
                data : 'email=' + email,
                dataType : 'json',
                success : function(retour, statut){
                    console.log(retour);
                    if (retour.retour)
                    {
                        $("#password_forgot_request").html('<p id="top_forgot_password">Email correcte. Un mail vous a &eacute;t&eacute; envoy&eacute; pour r&eacute;initialiser votre mot de passe!</p>');
                        $("#password_forgot_request").addClass('small_size');
                    }
                    else
                    {
                        $("#error_forgot_password").text(retour.erreur);
                    }
                }
            });
        }
        else
        {
            $("#error_forgot_password").text("Format de l'email incorrect !");
            //alert("Format de l'email incorrect !");
        }
    });
    
    $("#btn_password_forgot_cancel").click(function(){
        $("#password_forgot_request").hide();
    });
});