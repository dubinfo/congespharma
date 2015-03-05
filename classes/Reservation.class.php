<?php
	class Reservation
	{
		private $id;
		private $date;
		private $periode;
		private $commentaire;
		private $order;
		
		public function __construct($jour, $commentaire = null)
		{
			$this->id = $jour;
			if (empty($commentaire))
			{
				$this->commentaire = "&lt;pas de commentaire&gt;";
			}
			else
			{
				$this->commentaire = $commentaire;
			}
			$tmp = explode("_", $jour);
			$this->date = $tmp[2] . "/" . $tmp[1] . "/" . $tmp[0];
			switch ($tmp[3])
			{
				case "AM" :
					$this->periode = "a.m.";
					$this->order = $tmp[0] * 100000 + $tmp[1] * 1000 + $tmp[2] * 10 + 0;
					break;
				case "PM" :
					$this->periode = "p.m.";
					$this->order = $tmp[0] * 100000 + $tmp[1] * 1000 + $tmp[2] * 10 + 1;
					break;
				case "NU" :
					$this->periode = "nuit";
					$this->order = $tmp[0] * 100000 + $tmp[1] * 1000 + $tmp[2] * 10 + 2;
					break;
			}
		}
		
		public function getId()
		{
			return $this->id;
		}
		
		public function getDate()
		{
			return $this->date;
		}
		
		public function getPeriode()
		{
			return $this->periode;
		}
		
		public function getCommentaire()
		{
			return $this->commentaire;
		}
		
		public function getOrder()
		{
			return $this->order;
		}
	}
?>