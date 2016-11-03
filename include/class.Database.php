<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   database (PDO) param wrapper class
    */
	class DatabaseParam {
		public $name;
		public $value;
		public $type;

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
            $this->type = \PDO::PARAM_NULL;
		}

        /**
        *   set BOOL param
        */
		public function bool(string $name, bool $value) {
            $this->name = $name;
            $this->value = $value;
            $this->type = \PDO::PARAM_BOOL; 
		}

        /**
        *   set INTEGER param
        */        
		public function int(string $name, int $value) {
            $this->name = $name;
            $this->value = $value;
            $this->type = \PDO::PARAM_INT;
		}

        /**
        *   set STRING param
        */
		public function str(string $name, $value) {
            $this->name = $name;
            $this->value = $value;
            $this->type = \PDO::PARAM_STR;
		}
	}

    /**
    * database (PDO) wrapper class
    */    
	class Database {

		private $dbh;
		private $transaction;
		private $errors;

		private static $handler;

		public static function getHandler(bool $transaction = false) {
			if (self::$handler == null) {
				self::$handler = new Database($transaction);
			}
			return(self::$handler);
		}

		public function __construct (bool $transaction = false) {
			$this->errors = false;
			$this->transaction = false;
			$this->dbh = new \PDO(PDO_CONNECTION_STRING, DATABASE_USERNAME, DATABASE_PASSWORD, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
			if ($transaction) {
				$this->beginTrans();
			}
		} 

		public function __destruct() {
			if ($this->transaction) {
				if ($this->errors) {
					$this->dbh->exec("ROLLBACK;");
				} else {
					$this->dbh->exec("COMMIT;");
				} 
			}
			$this->dbh = null;
		}

		public function beginTrans() {
			$this->dbh->exec("BEGIN;");
			$this->transaction = true;
		}

		public function endTrans() {
			if ($this->errors) {
				$this->dbh->exec("ROLLBACK;");
			} else {
				$this->dbh->exec("COMMIT;");
			}
			$this->transaction = false; 			
		} 

		public function execWithoutResult(string $sql, $params = array()) {
			$stmt = null;
			try {
				$stmt = $this->dbh->prepare($sql);
				$total_params = count($params);
				if ($total_params > 0) {
					for ($i = 0; $i < $total_params; $i++) {						
						$stmt->bindValue($params[$i]->name, $params[$i]->value, $params[$i]->type);
					}
				}				
				$stmt->execute();
			} finally {
				$stmt = null;
			}		
		}

		public function execWithResult($sql, $params = array()): array {
			$rows = array();
			$stmt = null;
			try {
				$stmt = $this->dbh->prepare($sql);
				$total_params = count($params);
				if ($total_params > 0) {
					for ($i = 0; $i < $total_params; $i++) {
						$stmt->bindValue($params[$i]->name, $params[$i]->value, $params[$i]->type);
					}
				}				
				if ($stmt->execute()) {
					while ($row = $stmt->fetchObject()) {
						$rows[] = $row;
					}
				}
                $stmt->closeCursor();
			} finally {
				$stmt = null;
			}		
			return($rows);
		}

		public function execScalar($sql, $params = array()): int {
			$result = null;
			$stmt = null;
			try {
				$stmt = $this->dbh->prepare($sql);
				$total_params = count($params);
				if ($total_params > 0) {
					for ($i = 0; $i < $total_params; $i++) {
						$stmt->bindValue($params[$i]->name, $params[$i]->value, $params[$i]->type);
					}
				}				
				if ($stmt->execute()) {
					if ($row = $stmt->fetch()) {
						$result = $row[0];
					}
				}
                $stmt->closeCursor();
			} finally {
				$stmt = null;
			}		
			return($result);
		}
	}

?>