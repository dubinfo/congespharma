$(function() {
    $("#documentation").hide();
    $(".tuto_utilisation").hide();
    
    $("#show_documentation").click(function() {
       $("#documentation").toggle("slow");
       $("#tuto_utilisation_right_click").hide();
       $("#tuto_utilisation__multiple_days").hide();
       $("#tuto_actived_calendar").hide();
    });
    
    $("#hide_documentation").click(function() {
        $("#documentation").hide("slow");
        $("#tuto_utilisation_right_click").hide();
        $("#tuto_utilisation__multiple_days").hide();
        $("#tuto_actived_calendar").hide();
    });
    
    $("#right_click_show").click(function() {
        $("#tuto_utilisation_right_click").toggle();
    });
    
    $("#multiple_days_show").click(function() {
       $("#tuto_utilisation__multiple_days").toggle(); 
    });
    
    $("#actived_calendar_show").click(function() {
       $("#tuto_actived_calendar").toggle();
    });
});