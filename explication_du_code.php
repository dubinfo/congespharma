<?php
   /* Explicaiton du code APA 
    * @author David Dubois (david.dubois@ulb.ac.be)
    *@date : 21/07/2013
    *
    *Login :
    *
    *Chargement du calendrier aprs le login :
    *
    *C'est la mŽthode generer() de la classe Calendrier qui s'en occupe
    *Elle est instanciŽe dans la page index.php (en bas de la page)
    *voir aussi public function generer($mois = null, $annee = null) de
    *la page Calendrier.class.php
    *
    * getReservations permet de rŽcupŽrer les rŽservations pour les jours ET la machine
    * j'ai modifiŽ ce code sql afin qu'il tienne compte de la macihe :
    *
    * $req = $this->db->prepare("SELECT * FROM reservations WHERE jour LIKE :jour AND machine = :machine");
		$req->execute(array(':jour' => $id,':machine'=>$_SESSION['machine']));
			
et cette chaine sql :

$user_req = $this->db->prepare("SELECT u.nom, u.prenom, sr.couleur, sr.libelle, u.id, r.commentaire
													 FROM utilisateurs u
													 LEFT JOIN reservations r
													 ON u.id = r.id_user
													 LEFT JOIN statuts_reservations sr
													 ON r.statut = sr.id
													 WHERE r.jour = :jour
													 AND r.machine = :machine");

Ajout de la machine lors d'une rŽservation :

Voir le code se trouvant dans Utilisateur.class.php ==> public function reserver($jour, $mois, $annee, $periode, $commentaire = null)

?>