$(function(){
    $("#confirm_reset_password").attr("disabled", "disabled");
    
    $("#new_password").change(function(){
        checkPassword();
    });
    
    $("#confirm_password").change(function(){
        checkPassword();
    });
    
    function checkPassword(){
        password = $("#new_password").val();
        confirm_password = $("#confirm_password").val();
        if( password == "")
        {
            $("#error_password").text("Veuillez entrez un mot de passe!");
            $("#confirm_reset_password").attr("disabled", "disabled");
        }
        else
        {
            if (confirm_password != "") {
                if(password == confirm_password)
                {
                    $("#error_password").text("");
                    $("#confirm_reset_password").removeAttr("disabled");
                }
                else{
                    $("#error_password").text("La confirmation de mot de passe ne correspond pas au mot de passe!");
                    $("#confirm_reset_password").attr("disabled", "disabled");
                }
            }
            else
            {
                $("#error_password").text("");
                $("#confirm_reset_password").attr("disabled", "disabled");
            }
        }
    }
});