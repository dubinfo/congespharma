<?php
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
	define('BASE_URL', $url);
	define('BASE_PATH', realpath('.'));
	//Les classes doivent être définies AVANT le session_start(), sinon PHP ne peux pas charger/sauvegarder les objets de page en page
	require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php');
	session_start();
	// Si l'utilisateur se déconnecte, on supprime sa variable de session
	if (isset($_GET['deconnexion']))
	{
		unset($_SESSION['utilisateur']);
	}
	
	// On vérifie si l'utilisateur est connecté, si ce n'est pas le cas => redirection vers l'écran de login
	if (!isset($_SESSION['utilisateur']))
	{
		header('Location: login.php');
                
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-FR" lang="fr-FR">
	<head>
		<title>Faculté de Pharmacie : calendrier des congés</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery-ui.js"></script>
		<script type="text/javascript" src="js/jquery.ui.position.js"></script>
		<script type="text/javascript" src="js/jquery.contextMenu.js"></script>
		<script type="text/javascript" src="ajax/genererCalendrier.js"></script>
		<script type="text/javascript" src="ajax/mail.js"></script>
		<script type="text/javascript" src="ajax/jquery_index.js"></script>
		<script type="text/javascript" src="js/documentation.js"></script>
		<script type="text/javascript" src="js/calendrier.js"></script>
		<!--<script type="text/javascript" src="ajax/gestion.js"></script>-->
							
		<?php
			if ($_SESSION['utilisateur']->getRang() == "administrateur")
			{
				echo "<script type=\"text/javascript\" src=\"ajax/admin_context.js\"></script>\n";
			}
			else
			{
				echo "<script type=\"text/javascript\" src=\"ajax/context.js\"></script>\n";
			}
		?>
		<link rel="stylesheet" type="text/css" href="css/calendrier.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery.contextMenu.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css" />
	</head>
	<body>
		<script>
			$(function()
			{
				$(".datepicker").datepicker(
				{
					changeMonth: true,
					changeYear: true,
					dateFormat: 'dd-mm-yy',
					onSelect: function(date)
					{            
						var date1 = $('.datepicker').datepicker('getDate');           
						var date = new Date( Date.parse( date1 ) ); 
						date.setDate( date.getDate() );        
						var newDate = date.toDateString(); 
						newDate = new Date( Date.parse( newDate ) );                      
						$('.datepicker_fin').datepicker("option","minDate",newDate);            
					}
				});
				
				$( ".datepicker_fin" ).datepicker(
				{
					changeMonth: true,
					changeYear: true,
					dateFormat: 'dd-mm-yy'
				}
				);
			});
		</script>
		
		<div id="menuTop" style="width:90%;margin-left:auto;margin-right:auto;">
		<?php require_once "includes/menu_top.php"; ?>
		</div>
		
		<div id="debug-div" style="clear:both;"></div>
		<?php require_once "includes/documentation.php"; ?>	
		
		<div id="divCalendrier" class="disabled">
			<?php
				$calendrier = new Calendrier;
				echo $calendrier->generer();
			?>
		</div>
		<p><img id="ajax-loader" src="images/ajax-loader.gif" alt="loading" style="position:fixed;top:50%;left:50%;margin-top:-XXpx;margin-left:-YYpx;" /></p>
		<script type="text/javascript">
			$("#ajax-loader").hide();
		</script>
		<?php
			// $arr = $_SESSION['utilisateur']->getReservations();
			// var_dump($arr);
		?>

	</body>
</html>
