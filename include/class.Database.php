<?php

	require_once "configuration.php";

    /**
    *   database (PDO) param wrapper class
    */
	class DatabaseParam {
		private $name;
		private $value;
		private $type;

		public function __construct () { }

        public function __destruct() { }

        /**
        *   set param properties
        */
        public function set($name, $value, $type) {
			$this->name = $name;
			$this->value = $value;
			$this->type = $type;
		}

        /**
        *   set NULL param
        */
		public function null(string $name) {
            $this->name = $name;
            $this->value = null;
            $this->type = PDO::PARAM_NULL;
		}

        /**
        *   set BOOL param
        */
		public function bool(string $name, bool $value) {
            $this->name = $name;
            $this->value = $value;
            $this->type = PDO::PARAM_BOOL; 
		}

        /**
        *   set INTEGER param
        */        
		public function int(string $name, int $value) {
            $this->name = $name;
            $this->value = $value;
            $this->type = PDO::PARAM_INT;
		}

        /**
        *   set STRING param
        */
		public function str(string $name, $value) {
            $this->name = $name;
            $this->value = $value;
            $this->type = PDO::PARAM_STR;
		}
	}

    /**
    * database (PDO) wrapper class
    */    
	class Database {

        /**
        *   
        */		
		public static function execWithoutResult(string $sql, $params = array()) {
			$dbh = null;
			$stmt = null;
			try {
				$dbh = new PDO(PDO_CONNECTION_STRING, DATABASE_USERNAME, DATABASE_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
				$stmt = $dbh->prepare($sql);
				$total_params = count($params);
				if ($total_params > 0) {
					for ($i = 0; $i < $total_params; $i++) {						
						$stmt->bindValue($params[$i]->name, $params[$i]->value, $params[$i]->type);
					}
				}				
				$stmt->execute();
			} finally {
				$stmt = null;
				$dbh = NULL;
			}		
		}

		public static function execWithResult($sql, $params = array()): array {
			$rows = array();
			try {
				$dbh = new PDO(PDO_CONNECTION_STRING, DATABASE_USERNAME, DATABASE_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
				$stmt = $dbh->prepare($sql);
				$total_params = count($params);
				if ($total_params > 0) {
					for ($i = 0; $i < $total_params; $i++) {
						$stmt->bindValue($params[$i]->name, $params[$i]->value, $params[$i]->type);
					}
				}				
				if ($stmt->execute()) {
					while ($row = $stmt->fetch()) {
						$rows[] = $row;
					}
				}
                $stmt->closeCursor();
				$dbh = NULL;
			} finally {
				$stmt = null;
				$dbh = NULL;
			}		
			return($rows);
		}
	}

?>