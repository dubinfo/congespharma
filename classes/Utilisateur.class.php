<?php
	/**
	* Cette classe gère les interactions des utilisateurs (reservation, annulation d'une reservation) ainsi que leurs données personnelles 
	* (email, matricule, nom, prénom, ...)
	*
	* @Author : David Dubois
	*
	* @Param 
	*/
	class Utilisateur
	{
		const EMAIL_FROM = "congespharma@ulb.ac.be";
		const NAME_FROM = "Congespharma";
		
		// Attributs
		protected $id;
		protected $nom;
		protected $prenom;
		protected $email;
		protected $matricule;
		protected $rang;
		protected $db;
		protected $reservations = array(); // réservations non encore envoyées par email
		protected $services = array();
		
		// Constructeur
		public function __construct($id, $matricule, $nom, $prenom, $email, $rang)
		{
			$this->id = $id;
			$this->matricule = $matricule;
			$this->nom = $nom;
			$this->prenom = $prenom;
			$this->email = $email;
			$this->rang = $rang;
			$this->db = PDO2::getInstance()->db;
			//$this->initializeReservations();
		}
		
		/**
		* Fonction "magique" utilisée par PHP pour serialiser un objet, on détermine quels attributs doivent être serialisés.
		* ici j'exclus l'attribut db car un objet PDO n'est pas serialisable.
		*/
		public function __sleep()
		{
			return array('id', 'nom', 'prenom', 'email', 'matricule', 'rang', 'reservations', 'services');
		}
		
		/**
		* Fonction "magique" utilisée par PHP pour déserialiser un objet.
		* ici je récupère l'instance de la connexion PDO car elle n'avait pas pu être sérialisée.
		*/
		public function __wakeup()
		{
			$this->db = PDO2::getInstance()->db;
		}
		
		// Accesseurs
		public function getNomComplet()
		{
			return substr($this->prenom, 0, 1) . '. ' . $this->nom;
		}
		
		public function getFullName()
		{
			return $this->prenom . " " . $this->nom;
		}
		
		public function getMatricule()
		{
			return $this->matricule;
		}
		
		public function getEmail()
		{
			return $this->email;
		}
		
		public function getRang()
		{
			return $this->rang;
		}
		
		public function getId()
		{
			return $this->id;
		}
		
		public function getReservations()
		{
			return $this->reservations;
		}
		
		public function getServices()
		{
			return $this->services;
		}
		
		public function getService($i)
		{
			return $this->services[$i];
		}
		
		public function addService($service)
		{
			$this->services[] = $service;
		}
		
		// Méthodes
		
		/**
		* Effectue une proposition de réservation pour l'utilisateur
		*
		* @name: 		Utilisateur::reserver(matricule, jour, mois, annee, periode, commentaire = null)
		* @access:		public
		*
		* @params:		matricule (string)
		*				jour (int)
		*				mois (int)
		*				annee (int)
		*				periode (string)
		*				commentaire (string ou null)
		*
		* @return:		bool
		*/
		public function reserver($jour, $mois, $annee, $periode, $commentaire = null, $too_book_for = null)
		{
			$id_jour = $annee . '_' . $mois . '_' . $jour . '_' . $periode;
			//$occupation = '#ffcc00';
			$nom_complet = $this->getNomComplet();
			
			$requete = $this->db->prepare('INSERT INTO reservations(jour, statut, id_user, commentaire) 
													VALUES(:id_jour, :statut, :utilisateur, :commentaire)'); //ajout par DD 21/07/2013
			$requete->bindParam(':id_jour', $id_jour);
			$requete->bindValue(':statut', 1);
			$requete->bindParam(':utilisateur', $this->id);
			
			
			// Si il n'y a pas de commentaire, on entre NULL dans la base de données
			if (!$commentaire)
			{
				$requete->bindValue(':commentaire', null, PDO::PARAM_NULL);
			}
			else
			{
				$requete->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
			}
			
			// On vérifie que l'enregistrement a été effectué grace à la valeur de retour de PDOStatement::execute()
			if (!$requete->execute())
			{
				$arr = $requete->errorInfo();
				if (Config::read('debug'))
				{
					$_SESSION['erreurs']['reservation'] = 'Erreur : [SQLSTATE:' . $arr[0] . '][' . $arr[1] . '] ' . $arr[2];
				}
				else
				{
					if (substr($arr[2], 0, 15) == 'Duplicate entry')
					{
						$_SESSION['erreurs']['reservation'] = 'Réservation impossible, la salle est déjà réservée pour cette période.';
					}
					else
					{
						$_SESSION['erreurs']['reservation'] = 'Réservation non effectuée, une erreur s\'est produite';
					}
				}
				return false;
			}
			$this->reservations[$id_jour] = new Reservation($id_jour, $commentaire);
			$this->trierReservations();
			
			//envoie de mail
			return 
			$this->sendEmail('reserver', "J'ai réservé un jour de congé !",
				  array('jour' => $jour,
					'mois' => $mois,
					'annee' => $annee,
					'periode' => $periode,
					'commentaire'=>$commentaire));
			
			return true;
		}
		//réserver une plage de dates au lieu d'une période à la fois.
		public function reserver_plage($debut,$fin, $commentaire = null)
		{
			//echo $debut.''.$fin;
			$arr_debut = explode("-",$debut);
			$arr_fin = explode("-",$fin);
			
			// Start date
			$date = $arr_debut[0].'-'.$arr_debut[1].'-'.$arr_debut[2];
			$date_debut = $arr_debut[0].'-'.$arr_debut[1].'-'.$arr_debut[2];
			// End date
			$end_date = $arr_fin[0].'-'.$arr_fin[1].'-'.$arr_fin[2];
			//il faut laisser ceci, autrement, le premier jour n'est pas enregistré correctement dans la BD et le jour ne s'affiche pas en vert...
			$date = date ("Y-m-d", strtotime("+0 day", strtotime($date)));
			while (strtotime($date) <= strtotime($end_date))
			{
				//1 vérifier si date pas WE ou jour férié
				
				//2 Vérifier si date pas déjà encodée
				//echo "$date\n";
				$arr_date_debut_check = explode('-',$date);
				$arr_date_fin_check = explode('-',$end_date);
				//je vérifie si c'est un samedi ou un dimanche
				$pasok_we = Calendrier::isweekend($arr_date_debut_check[0],$arr_date_debut_check[1],$arr_date_debut_check[2]);
				//je vérifie si ce n'est pas un jour de congé officiel à l'ULB
				$pasok_ferie = Calendrier::getHolidays($arr_date_debut_check[1],$arr_date_debut_check[2]);
				
				//echo $pasok_ferie.'---'.$pasok_we;
				if($pasok_ferie == 0 && $pasok_we == 0)
				{
					for($i = 0; $i < 2;$i++)
					{
						if($i == 0)
						{
							$periode = '_AM';	
						}
						else
						{
							$periode = '_PM';	
						}
						
						$stmt = $this->db->prepare('SELECT COUNT(*) FROM reservations WHERE jour = :jour AND id_user = :id_user');
						$data = array(':jour'=>$arr_date_debut_check[0].'_'.$arr_date_debut_check[1].'_'.$arr_date_debut_check[2].$periode,':id_user'=>$_SESSION['utilisateur']->getId());
						$stmt->execute($data);
						if ($stmt->fetchColumn() < 1)
						{
							$stmt = $this->db->prepare('INSERT INTO reservations (jour, statut, id_user,commentaire) VALUES(:jour, :statut, :id_user, :commentaire)');
							$data = array(':jour'=>$arr_date_debut_check[0].'_'.$arr_date_debut_check[1].'_'.$arr_date_debut_check[2].$periode,':statut'=>1,':id_user'=>$_SESSION['utilisateur']->getId(),'commentaire'=>$_POST['commentaire']);																
							//echo str_replace(array_keys($data), array_values($data), $stmt->queryString);
							$stmt->execute($data);	
						}
					}
				}
				//echo $pasok;
				$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
			}//fin du while
			
			//envoie de mail
			if($date_debut == $end_date)
			{
				$subject = "J'ai reservé un jour de congé !";
			}
			else
			{
				$subject = "J'ai reservé des jours de congé !";
			}
			
			$this->sendEmail('reserver_plage', $subject,
				  array('debut' => $date_debut,
					'fin' => $end_date,
					'commentaire'=>$commentaire));
		}
		
		public function annuler($id_jour, $commentaire)
		{
			$requete = $this->db->prepare('DELETE FROM reservations
							WHERE jour = :id_jour
							AND id_user = :user');
			$requete->bindParam(':id_jour', $id_jour);
			$requete->bindParam(':user', $_SESSION['utilisateur']->getId());
			
			$requete->execute();
			$this->supprimerReservation($id_jour);
			
			$this->sendEmail('supprimer', "J'ai annulé un jour de congé !", array('jour' => $id_jour, 'commentaire'=>$commentaire));
		}
		
		public function supprimer_plage($debut,$fin, $commentaire)
		{
			$data = explode("-", $debut);
			$date_debut = $data['2'] . "_" . $data['1'] . "_" . $data['0'];
			
			$data = explode("-", $fin);
			$date_fin = $data['2'] . "_" . $data['1'] . "_" . $data['0'] . "_" . "ZZ";
			
			$requete = $this->db->prepare('DELETE FROM reservations
							WHERE jour >= :jour_deb
							AND jour <= :jour_fin
							AND id_user = :user');
			$requete->bindParam(':jour_deb', $date_debut);
			$requete->bindParam(':jour_fin', $date_fin);
			$requete->bindParam(':user', $_SESSION['utilisateur']->getId());
			$requete->execute();
			
			//envoie de mail
			if($debut == $fin)
			{
				$this->sendEmail('supprimer_plage', "J'ai annulé un jour de congé !",
					  array('debut' => $debut,
						'fin' => $fin,
						'commentaire'=>$commentaire));
			}
			else
			{
				$this->sendEmail('supprimer_plage', "J'ai annulé des jours de congé !",
					  array('debut' => $debut,
						'fin' => $fin,
						'commentaire'=>$commentaire));
			}
		}
		
		protected function initializeReservations()
		{
			$requete = "SELECT jour, commentaire 
						FROM reservations 
						WHERE id_user = :id
						AND statut = 3
						AND email_envoye = 0
						"; //modifé par DD 21/7/13
			$stmt = $this->db->prepare($requete);
			$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
			//$stmt->bindParam(":machine", $_SESSION['machine'], PDO::PARAM_INT); //modifé par DD 21/7/13
			$stmt->execute();
			
			while ($res = $stmt->fetch())
			{
				$this->reservations[$res['jour']] = new Reservation($res['jour'], $res['commentaire']);
			}
			
			$this->trierReservations();
		}
		
		public function trierReservations()
		{
			function cmp(Reservation $a, Reservation $b)
			{
				if ($a->getOrder() == $b->getOrder()) {
					return 0;
				}
				return ($a->getOrder() < $b->getOrder()) ? -1 : 1;
			}

			uasort($this->reservations, "cmp");
		}
		
		public function supprimerReservation($jour)
		{
			if (isset($this->reservations[$jour])) unset($this->reservations[$jour]);
		}
		
		public function sendEmail($action, $subject, $data)
		{
			require('../mailer/class.phpmailer.php');
			$sql = 'SELECT u.nom, u.prenom, u.email
				FROM utilisateurs u
				JOIN service_has_utilisateurs shu
				ON shu.utilisateurs_id = u.id
				WHERE u.rang = 1
				AND (shu.service_id = ' . $this->getService(0);
				
			if(count($this->getServices()) > 1)
			{
				$sql .= ' OR shu.service_id = ' . $this->getService(1);
			}
			
			$sql .= ')';
			$requete = $this->db->prepare($sql);
			$requete->execute();
			
			$mail = new PHPMailer();
			$mail->From = $_SESSION['utilisateur']->getEmail();
			$mail->FromName = $_SESSION['utilisateur']->getNomComplet();
			$mail->CharSet = 'UTF-8';
	
			// Définition du sujet/objet
			$mail->Subject = "congespharma : " . $subject;
			
			$firstMail = true;
			foreach($requete->fetchAll(PDO::FETCH_OBJ) as $admin)
			{
				$body = "<div align='center'><img src='http://s23.postimg.org/b9c7bk0x7/ulb.jpg'  /></div>";
				$body.= '<p>Bonjour '
				     . substr($admin->prenom, 0, 1) . '. ' . $admin->nom
				     . ',<br/></p>';
				$reponse = false;
				switch($action)
				{
					case 'reserver':
						$body.= '<p>Je vous informe que j\' ai pris congé le '.$data['jour'].'/'.$data['mois'].'/'.$data['annee'].' (' .$data['periode'].') via le site congespharma.ulb.ac.be<br/><p/>';
						$reponse = true;
					break;
					
					case 'reserver_plage':
						if($data['debut'] == $data['fin'])
						{
							$body.= '<p>Je vous informe que j\' ai pris congé le '.$data['debut'] . ' via le site congespharma.ulb.ac.be<br/><p/>';
						}
						else
						{
							$body.= '<p>Je vous informe que j\' ai pris congé du '.$data['debut'].' au '.$data['fin'] . ' via le site congespharma.ulb.ac.be<br/><p/>';
						}
						$reponse = true;
					break;
				
					case 'supprimer':
						$body.= '<p>Je vous informe que j\' ai annulé mon jour de congé du ' . $data['jour'] . ' via le site congespharma.ulb.ac.be<br/><p/>';
					break;
				
					case 'supprimer_plage':
						if($data['debut'] == $data['fin'])
						{
							$body.= '<p>Je vous informe que j\' ai annulé mon congé du '.$data['debut'] . ' via le site congespharma.ulb.ac.be<br/><p/>';
						}
						else
						{
							$body.= '<p>Je vous informe que j\' ai annulé des jours de congé entre le ' . $data['debut'] . 'et le ' . $data['fin'] . ' via le site congespharma.ulb.ac.be<br/><p/>';
						}
					break;
				}
				
				if($data['commentaire'] != null)
				{
					$body.= '<p>Motif : '. $data['commentaire'] . '<p/>';
				}
				
				if($reponse)
				{
					$body.= '<p>Veuillez bien vouloir répondre à ce mail pour me faire savoir si vous acceptez ou refusez cette demande de congé.</p>';
				}
				$body.= '<br/><p>Cordialement,<p/><p>';
				$body.= $_SESSION['utilisateur']->getNomComplet();
				$body.= '</p>';
		
				// On définit le contenu de cette page comme message
				$mail->MsgHTML($body);
				
				//grande priorité
				$mail->Priority = 1;
				$mail->AddCustomHeader("X-MSMail-Priority: High");
				$mail->AddCustomHeader("Importance: High");
		
				// On pourra définir un message alternatif pour les boîtes de
				// messagerie n'acceptant pas le html
				$mail->AltBody = "Ce message est au format HTML, votre messagerie n'accepte pas ce format.";
		
				// Il reste encore à ajouter au moins un destinataire
				$mail->AddAddress($admin->email);
				if($firstMail)
				{
					$mail->AddBCC("ddubois2@gmail.com");
					$firstMail = false;
				}

				// Pour finir, on envoi l'e-mail
				$mail->send();
			}
		}
	}
?>