<?php
    namespace PHP_MPM;

    require_once "class.CustomExceptions.php";
    require_once "class.Database.php";
    require_once "class.Utils.php";

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

        /**
        *   check user (by email) existence
        */
        public function exists() {
            if (empty($this->email)) {                
                throw new MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $param = new DatabaseParam();
                $param->str(":email", $this->email);                
                $rows = Database::execWithResult(" SELECT * FROM USER WHERE email = :email ", array($param));
                return(count($rows) > 0);                
            }            
        }

        /**
        *   add new user
        */
        public function add() {            
            if ($this->exists()) {
                throw new MPMAlreadyExistsException(print_r(get_object_vars($this), true));
            } else {
                if (empty($this->email) || empty($this->password)) {
                    throw new MPMInvalidParamsException(print_r(get_object_vars($this), true));
                } else {
                    if (empty($this->id)) {
                        $this->id = Utils::uuid();
                    }
                    $params = array();
                    $param = new DatabaseParam();
                    $param->str(":id", $this->id);
                    $params[] = $param;                
                    $param = new DatabaseParam();
                    $param->str(":email", $this->email);
                    $params[] = $param;                
                    $param = new DatabaseParam();
                    $param->str(":password", password_hash($this->password, PASSWORD_BCRYPT, array("cost" => 12)));
                    $params[] = $param;                                
                    Database::execWithoutResult(" INSERT INTO USER (id, email, password, created) VALUES (:id, :email, :password, CURRENT_TIMESTAMP) ", $params);
                }
            }
        }        
    }
?>