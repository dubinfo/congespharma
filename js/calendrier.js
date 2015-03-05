$(function(){
    $("#actived_half_day").click(function(){
        $(".disabled").removeClass("disabled");
        alert("Vous pouvez maintenant prendre ou annuler un demi-jour de congé.\n" +
              "Mais Veuillez privilégier la sélection de plusieurs jours via les plages de sélection!");
    });
});