<?php
	class PDO2
	{
		public $db; // handle of the db connexion
		private static $instance;

		private function __construct()
		{
			// building data source name from config
			$dsn = 'mysql:host=' . Config::read('db.host') .
				   ';dbname='    . Config::read('db.basename').
				   ';connect_timeout=15';
			// getting DB user from config                
			$user = Config::read('db.user');
			// getting DB password from config                
			$password = Config::read('db.password');

			$this->db = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		}

		public static function getInstance()
		{
			if (!isset(self::$instance))
			{
				$object = __CLASS__;
				self::$instance = new $object;
			}
			return self::$instance;
		}

		// others global functions
	}
?>