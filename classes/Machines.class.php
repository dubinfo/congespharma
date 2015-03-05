<?php
   class Machines
   {
      private $_db;
      
      public function __construct()
      {
         $this->_db = PDO2::getInstance()->db;
      }
      public function ajouter_machine()
      {
         $q = $this->_db->prepare('INSERT INTO machines(nom_machine) VALUES (:nom_machine)');
         $data = array(':nom_machine'=>$_POST['nom_machine']);
         $q->execute($data);
         return $id_insere = $this->_db->lastInsertId();
      }

      public function modifier_machine($id)
      {
         $q = $this->_db->prepare('UPDATE machines SET nom_machine = :nouveau_nom WHERE ID = :id');
         $data = array(':nouveau_nom'=>$_POST['nom_machine'],':id'=>$_POST['id_machine']);
         $q->execute($data);
         //je retourne le nombre de lignes affectées par l'update
         return $q->rowCount();
      }
      
      public function supprimer_machine($id)
      {
         $q = $this->_db->prepare('DELETE FROM machines WHERE ID = :id');
         $data = array(':id'=>$_POST['id_machine']);
         $q->execute($data);
         //je retourne le nombre de lignes affectées par le delete
         return $q->rowCount();
      }     
}
?>