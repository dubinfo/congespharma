$(document).ready(function()
{
	$( "#editUser-form" ).dialog(
	{
		autoOpen: false,
		modal: true,
		height: 260,
		width: 500,
		title: "Veuillez encoder vos informations ",
		buttons:
		{
			"Valider": function(){
				//alert($( "#categoriePlat option:selected" ).val());
				var majOK = false;
				var bValid = true;
				var mdp1 = $( "#mdp" ).val();
				var mdp2 = $( "#mdp2" ).val();
				if (mdp1 != mdp2)
				{
					alert("Les mots de passe ne correspondent pas !");
					return false;
				}
				var erreur;
				if ( bValid ) {
					// categorieDuPlat = $( "#categoriePlat option:selected" ).val();
					// alert(categorieDuPlat);
					//----------
					// var idCat = categorieDuPlat;
					// var ajoute = false;
					
					$.post(
						'ajax/majUtilisateur.php',
						{
							"matricule":$( "#matricule_ajout_user" ).val(),
							"login":$( "#login_ajout_user" ).val(),
							"nom":$( "#nom" ).val(),
							"prenom":$( "#prenom" ).val(),
							"email":$( "#email" ).val(),
							'id':-1,'mdp':$( "#mdp" ).val()
						},
						function(data)
						{
							$('#matricule').val($('#matricule_ajout_user').val());
							$('#login').val($('#login_ajout_user').val());
							$('#password').val($('#mdp').val());
       
							alert("Ajout OK, vous pouvez vous connecter au site");
						
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
							// alert('succès');
							location.reload(); 
						}
						else
						{
							alert('erreur : ' + erreur);
						}
					}).error(function(){
						alert('erreur');
					});
					//------
					$( "#editUser-form" ).dialog('close');
				}
			},
			"Retour": function()
			{
				$( "#editUser-form" ).dialog('close');
			}
		}
	});
	
	$(document).on("click", ".modifierUtilisateur", function(e){
		// alert(e.currentTarget.id);
		var id = e.currentTarget.id.substring(1);
		$( "#userId" ).html(id);
		var userInfos;
		$.post(
			'ajax/modifierUtilisateur.php',
			{"id":id},
			function(data){
				//alert(data);
				userInfos = data;
			}
		).success(function(){
			$( "#editUser-form" ).html(userInfos)
			$( "#editUser-form" ).dialog('open');
		}).error(function(){
			alert('erreur');
		});
	});
	
	$(document).on("click", ".supprimerUtilisateur", function(e){
		// alert(e.currentTarget.id);
		var id = e.currentTarget.id.substring(1);
		$( "#userId" ).html(id);
		var userInfos;
		if (confirm("Êtes-vous sur de vouloir supprimer cet utilisateur ?"))
		{
			$.post(
				'ajax/supprimerUtilisateur.php',
				{"id":id},
				function(data){
					//alert(data);
					userInfos = data;
				}
			).success(function(){
				location.reload();
			}).error(function(){
				alert('erreur');
			});
		}
	});
	
	////modification ou suppression du nom d'une des machines
	//$("[id^='edit'],[id^='supp']").on('click',function()
	//{
	//	var tab_id = this.id.split('#');
	//	var action = tab_id['0'];
	//	if (action == 'edit')
	//	{
	//		nouveau_nom = prompt('Please, give a new name to this device : ');
	//	}
	//	else
	//	{
	//		var confirmation = confirm('Are you sure to want to delete this device ? ');
	//		if (!confirmation)
	//		{
	//			return false;
	//		}
	//		else
	//		{
	//			action = 'supp';
	//			nouveau_nom = '';
	//		}
	//	}
	//	
	//	$.ajax
	//	(
	//		{
	//			type: 'POST',
	//			url: 'ajax/ajout_modificaton_machines.php',
	//			dataType: 'text',
	//			data:
	//			{
	//				'action':action, 'nom_machine':nouveau_nom, 'id_machine':tab_id[1]
	//			},
	//			success: function(retour_php)
	//			{
	//				retour_php = parseInt(retour_php);
	//				if (retour_php > 0)
	//				{
	//					$('#ok').html('Successful operation').css('color','green').fadeOut(4000);
	//					location.reload();
	//				}
	//			}
	//		}
	//	);	
	//});
	
	//ajout d'une machine (DD: 2/9/2013)
	//$('#ajoutermachine').on('click',function()
	//{
	//	var nouvelle_machine = prompt("What's the name of the new device ?");
	//	var action = '';
	//	if ($.trim(nouvelle_machine)!= "")
	//	{
	//		action = 'ajout';
	//	}
	//	$.ajax
	//	(
	//		{
	//			type: 'POST',
	//			url: 'ajax/ajout_modificaton_machines.php',
	//			dataType: 'text',
	//			data:
	//			{
	//				'action':action, 'nom_machine':nouvelle_machine
	//			},
	//			success: function(retour_php)
	//			{
	//				retour_php = parseInt(retour_php);
	//				if (retour_php > 0)
	//				{
	//					$('#ok').html('Device correctly inserted in the database').css('color','green').fadeOut(4000);
	//					location.reload();
	//				}
	//			}
	//		}
	//	);	
	//});
	
	//ajout d'un utilisateur
	$(document).on("click", "#ajouterUtilisateur", function(e)
 {
		 //alert(e.currentTarget.id);
		$( "#userId" ).html("-1");
		var userInfos;
		$.post(
			'ajax/modifierUtilisateur.php',
			{"new":true},
			function(data)
			{
				//alert(data);
				userInfos = data;
			}
		).success(function()
		{
			$( "#editUser-form" ).html(userInfos)
			$( "#editUser-form" ).dialog('open');
		}).error(function(){
			alert('erreur');
		});
	});
	
});//fin de jquery