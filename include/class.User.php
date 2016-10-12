<?php
    namespace PHP_MPM;

    require_once "class.CustomExceptions.php";
    require_once "class.Database.php";
    require_once "class.Utils.php";

    /**
    *   user type definitions
    */
    abstract class UserType {
        const DEFAULT = 0;          // normal (default) user
        const ADMINISTRATOR = 1;    // administrator user (all privileges)
    }

    /**
    *   user class
    */
    class User {

        public $id;
        public $email;
        public $password;
        public $type;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $email = "", string $password = "", int $type = 0) {
            $this->id = $id;
            $this->email = $email;
            $this->password = $password;
            $this->type = $type;
        }

        /**
        *   check user (by email) existence
        */
        public function exists() {
            if (empty($this->email) && empty($this->id)) {                
                throw new MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                
                $param = new DatabaseParam();
                $param->str(":email", $this->email);
                $params[] = $param;                
                $rows = Database::execWithResult(" SELECT * FROM USER WHERE email = :email OR id = :id ", $params);
                return(count($rows) > 0);                
            }            
        }

        /**
        *   register new user
        */
        public function signup() {
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
                    $param = new DatabaseParam();
                    $param->int(":type", UserType::DEFAULT);
                    $params[] = $param;                                
                    $param = new DatabaseParam();
                    $param->str(":creator", User::isAuthenticated() ? User::getSessionUserId(): $this->id);
                    $params[] = $param;                                
                    Database::execWithoutResult(" INSERT INTO USER (id, email, password, type, created, creator) VALUES (:id, :email, :password, :type, CURRENT_TIMESTAMP, :creator) ", $params);
                }
            }
        }

        /**
        *   add new user (admin privileges required)
        */
        public function add() {
            if (! User::isAuthenticated()) {
                throw new MPMAuthSessionRequiredException();
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new MPMAdminPrivilegesRequiredException();
            }
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
                    $param = new DatabaseParam();
                    $param->int(":type", $this->type);
                    $params[] = $param;                                
                    $param = new DatabaseParam();
                    $param->str(":creator", User::getSessionUserId());
                    $params[] = $param;                                
                    Database::execWithoutResult(" INSERT INTO USER (id, email, password, type, created, creator) VALUES (:id, :email, :password, :type, CURRENT_TIMESTAMP, :creator) ", $params);
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
        public static function getSessionUserId() {
            return(isset($_SESSION["user_id"]) ? $_SESSION["user_id"]: null); 
        }

        /**
        *   generate recover account token
        */
        public function generateRecoverAccountToken(): string {
            // TODO: NOT WORKING ¿?
            $this->get();
            $params = array();
            $param = new DatabaseParam();
            $param->str(":user_id", $this->id);
            $params[] = $param;
            // TODO: transaction support
            Database::execWithoutResult(" DELETE FROM RECOVER_ACCOUNT_REQUEST WHERE user_id = :user_id ", $params);                
            $param = new DatabaseParam();
            $token = password_hash((sha1(uniqid()) . sha1(uniqid())), PASSWORD_BCRYPT, array("cost" => 12));
            $param->str(":token", $token);
            $params[] = $param;                
            Database::execWithoutResult(" INSERT OR REPLACE INTO RECOVER_ACCOUNT_REQUEST (created, token, user_id) VALUES (CURRENT_TIMESTAMP, :token, :user_id) ", $params);
            return($token);
        }

        /**
        *   get user metadata from recover account token
        */
        public static function getUserFromRecoverAccountToken(string $token) {
            // TODO: NOT WORKING ¿?
            if (empty($token)) {
                throw new MPMInvalidParamsException("");
            } else {
                $params = array();
                $param = new DatabaseParam();
                $param->str(":token", $token);
                $params[] = $param;
                // tokens are valid only for 60 minutes 
                $rows = Database::execWithResult(" SELECT RAR.user_id AS id, U.email, U.type FROM RECOVER_ACCOUNT_REQUEST RAR LEFT JOIN USER U ON U.id = RAR.user_id WHERE RAR.token = :token AND ((strftime('%s', CURRENT_TIMESTAMP) - strftime('%s', RAR.created)) / 60) < 60 ", $params);
                if (count($rows) != 1) {
                    throw new MPMNotFoundException($token);
                } else {
                    $user = new User();
                    $user->set(
                        isset($rows[0]->id) ? $rows[0]->id: "", 
                        isset($rows[0]->email) ? $rows[0]->email: "", 
                        "", 
                        isset($rows[0]->type) ? $rows[0]->type: 0
                    );
                    return(get_object_vars($user));
                }                                                
            }
        }

        /**
        *   search (list) users
        */
        public static function search($page, $resultsPage) {
            if (! User::isAuthenticated()) {
                throw new MPMAuthSessionRequiredException();
            } else {
                // TODO: pagination & filtering
                return(Database::execWithResult(" SELECT id, email, type FROM [USER] ORDER BY email ", array()));
            }
        }
        
    }
?>