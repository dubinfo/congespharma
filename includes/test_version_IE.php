<script>
    navigator.sayswho= (function()
    {
        var N= navigator.appName, ua= navigator.userAgent, tem;
        var M= ua.match(/(opera|chrome|safari|firefox|msie)\/?\s*(\.?\d+(\.\d+)*)/i);
        if(M && (tem= ua.match(/version\/([\.\d]+)/i))!= null) M[2]= tem[1];
        M= M? [M[1], M[2]]: [N, navigator.appVersion,'-?'];
        return M;
    })();
         
         
    if(navigator.sayswho[0].toUpperCase() == 'MSIE' && navigator.sayswho[1] < 10)
    {
        //si le navigateur est trop ancien, je n'affiche pas le formulaire de connexion ou de création de compte
       
       document.write('<div id="navigateur_pas_ok">');
       document.write('Votre navigateur web est '+ navigator.sayswho+'\n');
       document.write('Il est trop ancien !!! Merci d\'utiliser un autre navigateur plus récent ! ');
       document.write('</div>');
       $('#btn_login').attr("disabled",true).after('Votre Navigateur est trop ancien, merci d\'en utiliser un autre plus récent').css('color','red');
    }
    else if(navigator.sayswho[0].toUpperCase() == 'FIREFOX' && navigator.sayswho[1] < 20)
    {
        //si le navigateur est trop ancien, je n'affiche pas le formulaire de connexion ou de création de compte
       
       document.write('<div id="navigateur_pas_ok">'); 
       document.write('Votre navigateur web est '+ navigator.sayswho+'\n');
       document.write(' Il est trop ancien !!! Merci d\'utiliser un autre navigateur plus récent ! ');
       document.write('</div>');
       $('#btn_login').attr("disabled",true).after('Votre Navigateur est trop ancien, merci d\'en utiliser un autre plus récent').css('c','red');
    }
    //else
    //{
    //    document.write('<div id="navigateur_ok">Votre navigateur web est '+ navigator.sayswho+'</div>');
    //}
</script>