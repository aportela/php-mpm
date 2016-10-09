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
        private $type;

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

        /**
        *   get (by id/email) user data
        */
        private function get() {
            if (empty($this->id) && empty($this->email)) {
                throw new MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                
                $param = new DatabaseParam();
                $param->str(":email", $this->email);
                $params[] = $param;                
                $rows = Database::execWithResult(" SELECT id, email, password, type FROM USER WHERE id = :id OR email = :email ", $params);
                if (count($rows) != 1) {
                    throw new MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    $this->password = $rows[0]->password;
                    $this->type = $rows[0]->type;
                }                                                
            }            
        }

        /**
        *   sign in with specified password
        */
        public function login(): bool {
            $userPassword = $this->password;
            $this->get();
            if (! password_verify($userPassword, $this->password)) {
                return(false);
            } else {
                $_SESSION["user_id"] = $this->id;
                $_SESSION["user_email"] = $this->email;
                $_SESSION["user_type"] = $this->type;
                return(true);
            }
        }

        /**
        *   sign out
        *
        *   (Frxstrem) http://stackoverflow.com/a/3512570
        */
        public function logout() {
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }
            session_destroy();
        }

        /**
        *   check if user has authenticated session
        */
        public static function isAuthenticated(): bool {
            return(isset($_SESSION["user_id"]));
        }

        /**
        *   check if user is isAuthenticated as administrator
        */
        public static function isAuthenticatedAsAdmin(): bool {
            return(isset($_SESSION["user_type"]) && intval($_SESSION["user_type"]) == 1);
        }

        /**
        *   return session user id
        */
        public static function getSessionUserId(): bool {
            return(isset($_SESSION["user_id"]) ? $_SESSION["user_id"]: null); 
        }

        /**
        *   generate recover account token
        */
            $this->get();
            $params = array();
            $param = new DatabaseParam();
            $param->str(":user_id", $this->id);
            $params[] = $param;
            // TODO: transaction support
            Database::execWithoutResult(" DELETE FROM RECOVER_ACCOUNT_REQUEST WHERE user_id = :user_id ", $params);                
            $param = new DatabaseParam();
            $token = password_hash((sha1(uniqid()) . sha1(uniqid)), PASSWORD_BCRYPT, array("cost" => 12));
            $param->str(":token", $token);
            $params[] = $param;                
            Database::execWithoutResult(" INSERT OR REPLACE INTO RECOVER_ACCOUNT_REQUEST (created, token, user_id) VALUES (CURRENT_TIMESTAMP, :token, :user_id) ", $params);
            return($token);
        }
    }
?>