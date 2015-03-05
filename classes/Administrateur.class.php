<?php
	class Administrateur extends Utilisateur
	{
		// Redéfinissez l'exception ainsi le message n'est pas facultatif
		public function __construct($id, $matricule, $nom, $prenom, $email, $rang='administrateur') 
		{
			parent::__construct($id, $matricule, $nom, $prenom, $email, $rang);
		}
		
		################################################################################
		#                           Gestion des réservations                           #
		################################################################################
		
		/**
		* Effectue une réservation pour l'administrateur (directement en vert donc)
		*
		* @name: 		Utilisateur::reserver(matricule, jour, mois, annee, periode, commentaire = null)
		* @access:		public
		*
		* @params:		jour (int)
		*				mois (int)
		*				annee (int)
		*				periode (string)
		*				commentaire (string ou null)
		*
		* @return:		bool
		*/
		public function reserver($jour, $mois, $annee, $periode, $commentaire = null)
		{
			//echo "dans admin";
			$id_jour = $annee . '_' . $mois . '_' . $jour . '_' . $periode;
			//$occupation = '#ffcc00';
			$nom_complet = $this->getNomComplet();
			
			$requete = $this->db->prepare('INSERT INTO reservations(jour, statut, id_user, commentaire) 
										 VALUES(:id_jour, :statut, :utilisateur, :commentaire)');
			$requete->bindParam(':id_jour', $id_jour);
			$requete->bindValue(':statut', 1);
			$requete->bindParam(':utilisateur', $this->id);
			//$requete->bindParam(':machine', $_SESSION['machine']);
			
			// Si il n'y a pas de commentaire
			if (!$commentaire)
			{
				$requete->bindValue(':commentaire', null, PDO::PARAM_NULL);
			}
			else
			{
				$requete->bindParam(':commentaire', $commentaire);
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
			return true;
		}
		
		/**
		* Occupe la période (en rouge)
		*
		* @name: 		Utilisateur::reserver(matricule, jour, mois, annee, periode, commentaire = null)
		* @access:		public
		*
		* @params:		jour (int)
		*				mois (int)
		*				annee (int)
		*				periode (string)
		*				commentaire (string ou null)
		*
		* @return:		bool
		*/
		public function occuper($jour, $mois, $annee, $periode, $commentaire = null)
		{
			$id_jour = $annee . '_' . $mois . '_' . $jour . '_' . $periode;
			//$occupation = '#ffcc00';
			$nom_complet = $this->getNomComplet();
			
			$requete = $this->db->prepare('INSERT INTO reservations(jour, statut, id_user, commentaire, machine) 
										 VALUES(:id_jour, :statut, :utilisateur, :commentaire, :machine)');
			$requete->bindParam(':id_jour', $id_jour);
			$requete->bindValue(':statut', 2);
			$requete->bindParam(':utilisateur', $this->id);
			$requete->bindParam(':machine', $_SESSION['machine']);
			
			// Si il n'y a pas de commentaire
			if (!$commentaire)
			{
				$requete->bindValue(':commentaire', null, PDO::PARAM_NULL);
			}
			else
			{
				$requete->bindParam(':commentaire', $commentaire);
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
			return true;
		}

		/**
		* Valide une proposition de réservation faite par un utilisateur
		*
		* @name: 		Administrateur::valider(jour, mois, annee, periode, commentaire = null)
		* @access:		public
		*
		* @params:		jour (int)
		*				mois (int)
		*				annee (int)
		*				periode (string)
		*				commentaire (string ou null)
		*
		* @return:		bool
		*/
		public function valider($jour, $mois, $annee, $periode, $commentaire = null)
		{
			$id_jour = $annee . '_' . $mois . '_' . $jour . '_' . $periode;
			//$occupation = '#ffcc00';
			// $nom_complet = $this->getNomComplet();
			
			$requete = $this->db->prepare('UPDATE reservations
													SET statut = :statut
													WHERE jour = :id_jour
													AND machine = :machine');
			$requete->bindParam(':id_jour', $id_jour);
			$requete->bindValue(':statut', 1);
			$requete->bindParam(':machine', $_SESSION['machine']);
			
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
			return true;
		}
		
		/**
		* Refuse une proposition de réservation faite par un utilisateur
		*
		* @name: 		Administrateur::valider(jour, mois, annee, periode, commentaire = null)
		* @access:		public
		*
		* @params:		jour (int)
		*				mois (int)
		*				annee (int)
		*				periode (string)
		*				commentaire (string ou null)
		*
		* @return:		bool
		*/
		public function refuser($jour, $mois, $annee, $periode, $commentaire = null)
		{
			$id_jour = $annee . '_' . $mois . '_' . $jour . '_' . $periode;
			//$occupation = '#ffcc00';
			// $nom_complet = $this->getNomComplet();
			
			$requete = $this->db->prepare('UPDATE reservations
													SET statut = :statut
													WHERE jour = :id_jour
													AND machine = :machine');
			$requete->bindParam(':id_jour', $id_jour);
			$requete->bindValue(':statut', 2);
			$requete->bindParam(':machine', $_SESSION['machine']);
			
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
			return true;
		}
		
		################################################################################
		#                           Gestion des utilisateurs                           #
		################################################################################
		
		public function ajouterUtilisateur(array $infos)
		{
			// insertion d'un nouvel utilisateur dans la base de données.
		}
		
		public function supprimerUtilisateur($id_utilisateur)
		{
			// suppression d'un utilisateur dans la base de données.
		}
		
		public function modifierUtilisateur(array $infos)
		{
			// mise à jour des informations d'un utilisateur.
		}
	}
?>