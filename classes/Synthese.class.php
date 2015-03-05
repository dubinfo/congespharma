<?php
	
	class Synthese
	{
		const ADMIN_RANK = "administrateur";
		const USER_RANK = "utilisateur";
		private $db; // instance PDO
		private $month;
		private $person;
		private $year;
		private $service;
		
		public function __construct()
		{
			$this->db = PDO2::getInstance()->db;
			$nb_args = func_num_args();
			//echo $nb_args;
			switch($nb_args)
			{
				case 1:
					$args = func_get_args();
					$this->construct($args[0]);
				break;
			
				case 2:
					$args = func_get_args();
					$this->construct($args[0], $args[1]);
				break;
			
				case 3:
					$args = func_get_args();
					$this->construct($args[0], $args[1], $args[2]);
				break;
			
				case 4:
					$args = func_get_args();
					$this->construct($args[0], $args[1], $args[2], $args[3]);
				break;
			
				default:
					$this->construct();
				break;
			}
		}
		
		private function construct($person = "", $month = "", $year="", $service="")
		{
			$this->month = $month;
			$this->person = $person;
			$this->year = $year;
			$this->service = $service;
		}
		
		//méthode qui permet d'afficher la synthèse des congés
		public function getSynthese()
		{
			$sqlRequest = "SELECT
					     reservations.jour,
					     reservations.id_user,
					     reservations.commentaire,
					     utilisateurs.nom,
					     utilisateurs.prenom,
					     utilisateurs.email
					FROM reservations
					LEFT JOIN utilisateurs
					ON reservations.id_user = utilisateurs.id";
			$rang = $_SESSION['utilisateur']->getRang();
			$addRequest = "";
			
			if(!empty($this->person))
			{
				$sqlRequest .= " WHERE utilisateurs.id = " . $this->person;
			}
			else if($rang == SELF::USER_RANK)
			{
				$sqlRequest .= " WHERE utilisateurs.id = " . $_SESSION['utilisateur']->getId();
			}
			else
			{
				$sqlRequest .= " LEFT JOIN service_has_utilisateurs shs
						ON reservations.id_user = shs.utilisateurs_id";

				if(!empty($this->service))
				{
					$sqlRequest .= " WHERE shs.service_id = " . $this->service;
				}
				else
				{
					$services = $this->getServices();
					$sqlRequest .= " WHERE(shs.service_id = " . $services[0];
				
					if(isset($services[1]))
					{
						$sqlRequest .= " OR shs.service_id = " . $services[1];
						$addRequest = " GROUP BY reservations.id";
					}
					$sqlRequest .= ')';
				}
			}
			
			if(!empty($this->month))
			{
				$sqlRequest .= " AND jour LIKE '%" . $this->month . "_____M'";
				$first_condition = false;
			}
			
			if(!empty($this->year))
			{
				$sqlRequest .= " AND SUBSTR(jour,1,4) = " . $this->year;
			}
			$sqlRequest .= $addRequest . " ORDER BY SUBSTR(jour,1,10), id_user";
			$sql = $this->db->prepare($sqlRequest);
			$sql->execute();
			$res = $sql->fetchAll(PDO::FETCH_OBJ);
			
			$html ="";
			if($rang == SELF::USER_RANK)
			{
				$html = '<div id="count_days"><p>Nombre de jours de congés: ' . (count($res)/2). '</p></div>';
			}
			$html.= '<table id="tab_synthese">';
			$html.= '<tr><td>Jour</td><td>Nom de la personne</td><td>Commentaire</td>';
			if($rang == SELF::ADMIN_RANK)
			{
				$html.= '<td>Supprimer</td>';
			}
			$html.='</tr>';
			
			if(count($res) == 0)
			{
				$nbCol = 3;
				if($rang == SELF::ADMIN_RANK)
				{
					$nbCol++;
				}
				return $html . '<tr><td colspan="' . $nbCol . '">Aucun congé pour ce filtre</td></tr>';
			}
			
			for($i = 0; $i < count($res); $i++)
			{
				$value = $res[$i];
				$tab_jour = explode('_',$value->jour);
				//je vérifie si la date que je traite est la date d'aujourd'hui
				//1) je mets dans une variable la date d'aujourd'hui
				$today = date("Y-m-d");
				//2) la date avec laquelle travaiulle au format de la date d'aujourd'hui
				$la_date = $tab_jour[0].'-'.$tab_jour[1].'-'.$tab_jour[2];
				$datetime = new DateTime($la_date);
				//aujourd'hui
				$datetime_today = new DateTime($today);
				//différence entre aujourd'hui et la date que l'on traite
				$interval = $datetime->diff($datetime_today);
				
				$html.= '<tr';
				//si interval vaut 0 (si on traite la date d'aujourd'hui), j'ajoute une classe qui va mettre le jour en jaune
				if($interval->days == 0)
				{
					$html.= ' class="aujourdhui" ';
				}
				
				$html.= '><td>' . $tab_jour[2].'/'.$tab_jour[1].'/'.$tab_jour[0];

				if($i != count($res)-1 && holidayAllDay($datetime,$value->id_user, $res[$i+1]))
				{
					$i++;
				}
				else
				{
					$html.= ' '.$tab_jour[3];
				}
	
				$html.= '</td><td>';

				$html.= $value->nom.' '.$value->prenom.'</td><td>'.$value->commentaire.'</td>';
					
				if($rang == SELF::ADMIN_RANK)
				{
					$html.= '<td><img src="./images/supprimer.png" class="supprimer_conge" id="'.$value->id_user.'#'.$value->jour.'#'.$value->email.'" /> </td>';
				}
				$html.= '</tr>';
			}
			return $html;
		}
		
		function getServices(){
			$sql = $this->db->prepare("SELECT service_id FROM service_has_utilisateurs WHERE utilisateurs_id = :id");
			$sql->execute(array(':id' => $_SESSION['utilisateur']->getId()));
			
			foreach($sql->fetchAll(PDO::FETCH_OBJ) as $service)
			{
				$services[] = $service->service_id;
			}
			return $services;
		}
	}
	
	function holidayAllDay($date_current_holiday, $id_current_user, $next_holiday) {
		$tab_jour = explode('_',$next_holiday->jour);
		$date_next_holiday = new DateTime($tab_jour[0].'-'.$tab_jour[1].'-'.$tab_jour[2]);
		return ($date_current_holiday->diff($date_next_holiday)->days == 0) && ($next_holiday->id_user == $id_current_user);
	}
?>