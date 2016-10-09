<?php
    namespace PHP_MPM;

    require_once "class.CustomExceptions.php";
    require_once "class.Database.php";

    /**
    *   user class
    */
    class User {

        private $id;
        private $email;
        private $password;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $email = "", string $password = "") {
            $this->id = $id;
            $this->email = $email;
            $this->password = $password;
        }

        public function exists() {
            if (empty($this->email)) {
                throw new MPMInvalidParamsException(print_r($this));
            } else {
                $param = new DatabaseParam();
                $param->str(":email", $this->email);                
                $rows = Database::execWithResult(" SELECT * FROM USER WHERE email = :email ", array($param));
                return(count($rows) > 0);                
            }            
        }        
    }
?>