<?php
	
	class Calendrier
	{
		private $db; // instance PDO
		
		public function __construct()
		{
			$this->db = PDO2::getInstance()->db;
			
		}

		/**
		* Cette méthode retourne du code html affichant le calendrier généré pour le mois et l'année passés en paramètres.
		* Si rien n'est passé en paramètre, la méthode utilise le mois et l'année courante.
		*
		* @params : $mois (string)
		* @$annee (string)
		* @return : string
		*/
		public function generer($mois = null, $annee = null)
		{
			$html = "";
			// Si le mois et l'année ne sont pas spécifiés, on prend le mois et l'année courante.
			if (!$mois || !$annee)
			{
				$mois = date('m');
				$annee = date('Y');
				// $mois = '04';
				// $annee = '2012';
			}
			// on met à jour le mois et l'année selectionnés dans les champs cachés correspondants
			$html .= "<p id=\"moisCourant\">$mois</p>\n";
			$html .= "\t\t\t<p id=\"anneeCourante\">$annee</p>\n";	
			
			$nombre_jours_mois = (int)date('t', mktime('00', '00', '00', $mois, '01', $annee)); // retourne le nombre de jours du mois à afficher
			$premier_jour_mois = (int)date('N', mktime('00', '00', '00', $mois, '01', $annee)); // retourne le premier jour du mois à afficher (lundi -> dimanche)
			
			$jour = 1;
			$jour_semaine = 1; //lundi
			$noms_jours = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
			$noms_mois = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
			$nom_mois = $noms_mois[(int)$mois - 1]; // on récupère le nom du mois selectionné
			$premiere_semaine = true;
			
			// ouverture de la balise table
			$html .= "\t\t\t<table id=\"calendrier\">\n";
			
			// navigation
			$html .= "\t\t\t\t<tr id=\"navRow\">\n";
			$html .= "\t\t\t\t\t<td>&nbsp;</td>\n";
			$html .= "\t\t\t\t\t<td class=\"center\"><span id=\"prevYear\" class=\"navCal\" title=\"année précédente\"><img src=\"images/nav-first.png\" alt=\"année précédente\" /></span></td>\n"; 	// bouton année précedente
			$html .= "\t\t\t\t\t<td class=\"center\"><span id=\"prevMonth\" class=\"navCal\" title=\"mois précédent\"><img src=\"images/nav-prev.png\" alt=\"mois précédent\" /></span></td>\n"; 		// bouton mois précedent
			$html .= "\t\t\t\t\t<td class=\"nomMois center\">$nom_mois $annee</td>\n"; 									// mois et année actuellement affichés
			$html .= "\t\t\t\t\t<td class=\"center\"><span id=\"nextMonth\" class=\"navCal\" title=\"mois suivant\"><img src=\"images/nav-next.png\" alt=\"mois suivant\" /></span></td>\n"; 		// bouton mois suivant
			$html .= "\t\t\t\t\t<td class=\"center\"><span id=\"nextYear\" class=\"navCal\" title=\"année suivante\"><img src=\"images/nav-last.png\" alt=\"année suivante\" /></span></td>\n"; 	// bouton année suivante
			$html .= "\t\t\t\t\t<td>&nbsp;</td>\n";
			$html .= "\t\t\t\t</tr>\n";
			
			// entetes
			$html .= "\t\t\t\t<tr>\n";
			// $html .= "<tr><td>&nbsp;</td>\n";
			for ($i = 0; $i < sizeof($noms_jours); $i++)
			{
				$html .= "\t\t\t\t\t<td class=\"nomsJours center\">$noms_jours[$i]</td>\n"; // affichage des noms des jours de la semaine comme entete des colonnes
			}
			$html .= "\t\t\t\t</tr>\n";
			
			// contenu
			while ($jour <= $nombre_jours_mois)
			{
				$html .= "\t\t\t\t<tr>\n";
				if ($premiere_semaine)
				{
					$premiere_semaine = false;
					for ($i = 1; $i < $premier_jour_mois; $i++)
					{
						$html .= "\t\t\t\t\t<td>&nbsp;</td>\n";
						$jour_semaine++;
					}
					for ($i = $premier_jour_mois; $i <= 7; $i++)
					{
						$html .= "\t\t\t\t\t<td class=\"conteneurCase\">\n";
						$html .= $this->getReservations($jour, $mois, $annee);
						$html .= "\t\t\t\t\t</td>\n";
						$jour++;
						$jour_semaine++;
					}
				}
				else
				{
					for ($i = 1; $i <= 7; $i++)
					{
						if($jour > $nombre_jours_mois)
						{
							$html .= "\t\t\t\t\t<td>&nbsp;</td>\n";
						}
						else
						{
							$html .= "\t\t\t\t\t<td class=\"conteneurCase\">\n";
							$html .= $this->getReservations($jour, $mois, $annee);
							$html .= "\t\t\t\t\t</td>\n";
							$jour++;
						}
						$jour_semaine++;
					}
				}
				//fin de la semaine;
				$html .= "\t\t\t\t</tr>\n";
				
				// on passe du dimanche au lundi
				$jour_semaine = 1;
			}
			
			// fermeture de la balise table
			$html .= "\t\t\t</table>\n";
			return $html;
		}
		
		/**
		* retourne un tableau html représentant une case du calendrier
		*
		* @params :	int $jour
		*			string $mois
		*			string $annee
		* @return : string
		*/
		private function getReservations($jour, $mois, $annee)
		{
			ob_start();
			$jour = ($jour > 0 && $jour < 10) ? '0' . $jour : '' . $jour;
			// contenu de la case par période
			$am = "&nbsp;";
			$pm = "&nbsp;";
			$nu = "&nbsp;";
			// id de l'utilisateur ayant réservé (-1 si pas réservé)
			$idAm = -1;
			$idPm = -1;
			$idNu = -1;
			// couleur de fond de la case
			$colorAm = "#ffffff";
			$colorPm = "#ffffff";
			$colorNu = "#ffffff";
			// classe de la case (utilisé par jQuery pour afficher ou non le menu contextuel)
			$statutAm = "libre";
			$statutPm = "libre";
			$statutNu = "libre";
			$editable = "_éditable"; // utilisé par jQuery, si _éditable est dans la classe, la case est éditable par l'utilisateur
			$debug = '';
			$id = $annee . '_' . $mois . '_' . $jour . '_%';
			
			$today = ($jour == date('d') && $mois == date('m') && $annee == date('Y')) ? true : false;
			
			//<debug>
			// ob_start();
			// var_dump(array("jour" => $jour, "mois" => $mois, "annee" => $annee));
			// $debug .= ob_get_clean();
			//</debug>
			
			//modification DD 21/07/2013 ajout de la machine dans la requête SQL
			$req = $this->db->prepare("SELECT * FROM reservations WHERE jour LIKE :jour ");
			$req->execute(array(':jour' => $id));
			
			$displayJour = (int)$jour;
			$sql = "SELECT u.nom, u.prenom, sr.couleur, sr.libelle, u.id, r.commentaire, shu.service_id 
													 FROM utilisateurs u
													 LEFT JOIN reservations r
													 ON u.id = r.id_user
													 LEFT JOIN statuts_reservations sr
													 ON r.statut = sr.id
														LEFT JOIN service_has_utilisateurs shu
														ON u.id = shu.utilisateurs_id
													 WHERE r.jour = :jour ";
														//si c'est un admin (ARielle, Monsieur Amighi...), je peux alors afficher les congés de tout le monde pour le bon service
														if($_SESSION['utilisateur']->getRang() == 'utilisateur')
														{
																$sql.= "AND u.id = :user";
														}
														else
														{
																	$sql.= "AND shu.service_id = :service";
														}
			
			$user_req = $this->db->prepare($sql);
														
			$id = substr($id, 0, -1);
			$periodes = array("AM", "PM");
			//$periodes = array("AM", "PM", "NU");
			
			while ($res = $req->fetch())
			{
				foreach ($periodes as $periode)
				{
					$id_res = $id . $periode;
					
					//<debug>
					// ob_start();
					// var_dump($id_res);
					// $debug .= ob_get_clean();
					//</debug>
					
					$user_req->bindParam(':jour', $id_res);
					if($_SESSION['utilisateur']->getRang() == 'utilisateur')
					{
							//permet d'afficher QUE les jours de la personne qui vient de se connecter... pour les utilisateurs
							$user_req->bindParam(':user', $_SESSION['utilisateur']->getId());
					}
					else
					{
							//permet d'afficher les jours de congés du personnel d'un chef de service sans voir les autres membres du personnel... pour les admins
							$user_req->bindParam(':service', $_SESSION['utilisateur']->getServices()[0]);
					}
					
					$user_req->execute();
					$hasComment = false;
					
					//pour les utilisateurs, affichage de leurs jours de congés
					if($_SESSION['utilisateur']->getRang() == 'utilisateur')
					{
										if ($reponse = $user_req->fetch())
										{
											$user = '<div class="contenuCellule">' . substr($reponse['prenom'], 0, 1) . '. ' . $reponse['nom'];
											if (!empty($reponse['commentaire']))
											{
												$hasComment = true;
												$user .= ' <div class="commentImage"><img src="images/commentaire2.gif" id="c' . $id_res . '" alt="" title="' . htmlspecialchars($reponse['commentaire']) . '"/></div>';
											}
											$user .= '</div>';
											
											switch ($periode)
											{
												case "AM":
													$idAm = intval($reponse['id']);
													$am = $user;
													$colorAm = $reponse['couleur'];
													$statutAm = $reponse['libelle'];
													$commentStatutAm = ($hasComment) ? "_commenté" : "";
													break;
												case "PM":
													$idPm = intval($reponse['id']);
													$pm = $user;
													$colorPm = $reponse['couleur'];
													$statutPm = $reponse['libelle'];
													$commentStatutPm = ($hasComment) ? "_commenté" : "";
													break;
												
											}
										}	
					}
					else //pour les admins, il voyent les jours de congés de leur personnel, permet d'afficher toutes les personnes qui ont pris congé dans une même cellule (td) du calendrier
					{
									 
										if ($reponse = $user_req->fetchAll())
										{
												$user = '';
													foreach($reponse as $key=>$value)
													{
																		$user .= '<div class="contenuCellule">' . substr($value['prenom'], 0, 1) . '. ' . $value['nom'];
																		if (!empty($value['commentaire']))
																		{
																					$hasComment = true;
																					$user .= ' <div class="commentImage"><img src="images/commentaire2.gif" id="c' . $id_res . '" alt="" title="' . htmlspecialchars($value['commentaire']) . '"/></div>';
																		}
																		$user .= '</div>';
																		
																		switch ($periode)
																		{
																			case "AM":
																				$idAm = intval($value['id']);
																				$am = $user;
																				$colorAm = $value['couleur'];
																				$statutAm = $value['libelle'];
																				$commentStatutAm = ($hasComment) ? "_commenté" : "";
																				break;
																			case "PM":
																				$idPm = intval($value['id']);
																				$pm = $user;
																				$colorPm = $value['couleur'];
																				$statutPm = $value['libelle'];
																				$commentStatutPm = ($hasComment) ? "_commenté" : "";
																				break;
																			
																		}					
																
													}
											
										}	
					}
					
				}
			}
			
			// Si il s'agit de la date d'aujourd'hui, on entoure la case en rouge à l'aide de la classe 'today'
			if ($today)
			{
				$html =  "\t\t\t\t\t\t<table class=\"today\">\n";
			}
			else
			{
				$html =  "\t\t\t\t\t\t<table class=\"case\">\n";
			}
			// Si la case du calendrier n'est pas une date passée (ou la date d'aujourd'hui), elle sera réservable.
			// if (mktime('00', '00', '00', $mois, $jour, $annee) > time()) 
			// {
				// $editable = "_éditable";
			// }
			
			// Affichage du numéro du jour
			$html .= "\t\t\t\t\t\t\t<tr>\n";
			$html .= "\t\t\t\t\t\t\t\t<td colspan=\"2\" class=\"numeroJour\">$displayJour</td>\n";
			$html .= "\t\t\t\t\t\t\t</tr>\n";
			$samedi_ou_dimanche = self::isweekend($annee,$mois,$jour);
			$jour_ferie = self::getHolidays($mois,$jour);
			
			//si c'est différent de samedi ou dimanche (la méthode isweekend retourne 1 si c'est un samedi ou un dimanche)
			if($samedi_ou_dimanche == 0 && $jour_ferie == 0)
			{
						// A.M.
							$html .= "\t\t\t\t\t\t\t<tr>\n";
							$html .= "\t\t\t\t\t\t\t\t<td class=\"periode\">A.M.</td>\n";
							$html .= "\t\t\t\t\t\t\t\t<td id=\"AM_". $annee . '_' . $mois . '_' . $jour . "\" class=\"disabled statutPeriode $statutAm";
							if ($_SESSION['utilisateur']->getRang() == "administrateur" || $statutAm == "libre" ||
										($statutAm == "proposé" && $idAm == $_SESSION['utilisateur']->getId()))
							{
								if(isset ($commentStatutAm))
								{
									$html .= $editable . $commentStatutAm;
								}
								else
								{
											$html .= $editable;
								}
							}
							else
							{
										$html .= $commentStatutAm;	
							}
							
							$html .= "\" style=\"background-color:$colorAm;\">$am</td>\n";
							$html .= "\t\t\t\t\t\t\t</tr>\n";
							
							// P.M.
							$html .= "\t\t\t\t\t\t\t<tr>\n";
							$html .= "\t\t\t\t\t\t\t\t<td class=\"periode\">P.M.</td>\n";
							$html .= "\t\t\t\t\t\t\t\t<td id=\"PM_". $annee . '_' . $mois . '_' . $jour . "\" class=\"disabled statutPeriode $statutPm";
							if ($_SESSION['utilisateur']->getRang() == "administrateur" || $statutPm == "libre" ||
										($statutPm == "proposé" && $idPm == $_SESSION['utilisateur']->getId()))
							{
										if(isset($commentStatutPm))
										{
												$html .= $editable . $commentStatutPm;	
										}
										else
										{
												$html .= $editable;	
										}
										
							}
							else
							{
											$html .= $commentStatutPm;
							}
							$html .= "\" style=\"background-color:$colorPm;\">$pm</td>\n";
							$html .= "\t\t\t\t\t\t\t</tr>\n";
							
			}
			
			
			
			$html .= "\t\t\t\t\t\t</table>\n";
			
			// affichage des données de debug
			$debug .= ob_get_clean();
			// $html .= "<div class=\"debug\">$debug&nbsp;</div>";
			
			return $html;
		}
		
		//méthode qui vérifie si c'est un samedi ou un dimanche.
		public static function isweekend($year, $month, $day)
		{
				$time = mktime(0, 0, 0, $month, $day, $year);
				$weekday = date('w', $time);
				return ($weekday == 0 || $weekday == 6);
		}
		
		//méthode qui permet de savoir si c'est un jour férié
		//c'est ici que l'on adapte les jours de congés pour l'année en cours
		public static function getHolidays($month, $day)
		{
				$holidays = array("01/01","01/02","04/06","05/01","05/14","05/25","07/21","08/15","09/27","11/1","11/11","11/20","12/24","12/25","12/31");
				if(in_array($month.'/'.$day,$holidays))
				{
							return 1;
				}
				else
				{
							return 	0;
				}
		}
		
	}
	
	
?>