<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

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
        public $name;
        public $password;
        public $type;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $email = "", string $password = "", string $name = "", int $type = 0) {
            $this->id = $id;
            $this->email = $email;
            $this->password = $password;
            $this->name = $name;
            $this->type = $type;
        }

        /**
        *   check user existence
        *
        *   only "id" set => search existence by id
        *   only "email" set => search existence by email
        *   "id" & "email" set => search existence by id
        */
        public function exists() {
            if (empty($this->email) && empty($this->id)) {                
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $sql = null;
                $params = array();                
                if (empty($this->id)) {
                    // search by email
                    $sql = " SELECT * FROM USER WHERE email = :email ";
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":email", $this->email);
                    $params[] = $param;
                } else {
                    // search by id
                    $sql = " SELECT * FROM USER WHERE id = :id ";                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":id", $this->id);
                    $params[] = $param;
                }
                $rows = \PHP_MPM\Database::execWithResult($sql, $params);
                return(count($rows) > 0);                
            }            
        }

        /**
        *   register new user
        */
        public function signup() {
            if ($this->exists()) {
                throw new \PHP_MPM\MPMAlreadyExistsException(print_r(get_object_vars($this), true));
            } else {
                if (empty($this->email) || empty($this->password) || empty($this->name)) {
                    throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
                } else {
                    if (empty($this->id)) {
                        $this->id = \PHP_MPM\Utils::uuid();
                    }
                    $params = array();
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":id", $this->id);
                    $params[] = $param;                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":email", $this->email);
                    $params[] = $param;                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":password", password_hash($this->password, PASSWORD_BCRYPT, array("cost" => 12)));
                    $params[] = $param;                                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->int(":type", \PHP_MPM\UserType::DEFAULT);
                    $params[] = $param;                                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":name", $this->name);
                    $params[] = $param;                                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":creator", \PHP_MPM\User::isAuthenticated() ? \PHP_MPM\User::getSessionUserId(): $this->id);
                    $params[] = $param;                                
                    \PHP_MPM\Database::execWithoutResult(" INSERT INTO USER (id, email, password, type, name, created, creator, deleted) VALUES (:id, :email, :password, :type, :name, CURRENT_TIMESTAMP, :creator, NULL) ", $params);
                }
            }
        }

        /**
        *   add new user (admin privileges required)
        */
        public function add() {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException();
            } else if (! \PHP_MPM\User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException();
            }
            if ($this->exists()) {
                throw new \PHP_MPM\MPMAlreadyExistsException(print_r(get_object_vars($this), true));
            } else {
                if (empty($this->email) || empty($this->password) || empty($this->name)) {
                    throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
                } else {
                    if (empty($this->id)) {
                        $this->id = \PHP_MPM\Utils::uuid();
                    }
                    $params = array();
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":id", $this->id);
                    $params[] = $param;                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":email", $this->email);
                    $params[] = $param;                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":password", password_hash($this->password, PASSWORD_BCRYPT, array("cost" => 12)));
                    $params[] = $param;                                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->int(":type", $this->type);
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":name", $this->name);
                    $params[] = $param;                                                                                    
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":creator", \PHP_MPM\User::getSessionUserId());
                    $params[] = $param;                                
                    \PHP_MPM\Database::execWithoutResult(" INSERT INTO USER (id, email, password, type, name, created, creator, deleted) VALUES (:id, :email, :password, :type, :name, CURRENT_TIMESTAMP, :creator, NULL) ", $params);
                }
            }
        }

        /**
        *   get (by id/email) user data
        */
        private function get() {
            if (empty($this->id) && empty($this->email)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":email", $this->email);
                $params[] = $param;                
                $rows = \PHP_MPM\Database::execWithResult(" SELECT id, email, password, name, type FROM USER WHERE deleted IS NULL AND (id = :id OR email = :email) ", $params);
                if (count($rows) != 1) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    $this->id = $rows[0]->id;
                    $this->password = $rows[0]->password;
                    $this->name = $rows[0]->name;
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
                $_SESSION["user_name"] = $this->name;
                $_SESSION["user_type"] = $this->type;
                return(true);
            }
        }

        /**
        *   sign out
        *
        *   (Frxstrem) http://stackoverflow.com/a/3512570
        */
        public function signout() {
            
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }
            if (session_status() != PHP_SESSION_NONE) {
                session_destroy();
            }
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
        *   return session user name
        */
        public static function getSessionUserName() {
            return(isset($_SESSION["user_name"]) ? $_SESSION["user_name"]: null);
        }

        /**
        *   return session user email
        */
        public static function getSessionUserEmail() {
            return(isset($_SESSION["user_email"]) ? $_SESSION["user_email"]: null);
        }

        /**
        *   generate recover account token
        */
        public function generateRecoverAccountToken(): string {
            $this->get();
            $params = array();
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":user_id", $this->id);
            $params[] = $param;
            // TODO: transaction support
            \PHP_MPM\Database::execWithoutResult(" DELETE FROM RECOVER_ACCOUNT_REQUEST WHERE user_id = :user_id ", $params);                
            $param = new \PHP_MPM\DatabaseParam();
            $token = password_hash((sha1(uniqid()) . sha1(uniqid())), PASSWORD_BCRYPT, array("cost" => 12));
            $param->str(":token", $token);
            $params[] = $param;                
            \PHP_MPM\Database::execWithoutResult(" INSERT OR REPLACE INTO RECOVER_ACCOUNT_REQUEST (created, token, user_id) VALUES (CURRENT_TIMESTAMP, :token, :user_id) ", $params);
            return($token);
        }

        /**
        *   get user metadata from recover account token
        */
        public static function getUserFromRecoverAccountToken(string $token) {
            // TODO: NOT WORKING ¿?
            if (empty($token)) {
                throw new \PHP_MPM\MPMInvalidParamsException("");
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":token", $token);
                $params[] = $param;
                // tokens are valid only for 60 minutes 
                $rows = \PHP_MPM\Database::execWithResult(" SELECT RAR.user_id AS id, U.email, U.type FROM RECOVER_ACCOUNT_REQUEST RAR LEFT JOIN USER U ON U.id = RAR.user_id WHERE RAR.token = :token AND U.deleted IS NULL AND ((strftime('%s', CURRENT_TIMESTAMP) - strftime('%s', RAR.created)) / 60) < 60 ", $params);
                if (count($rows) != 1) {
                    throw new \PHP_MPM\MPMNotFoundException($token);
                } else {
                    $user = new \PHP_MPM\User();
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
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException();
            } else {
                $data = new \PHP_MPM\SearchResults();
                if ($resultsPage > 0) {
                    $totalResults = \PHP_MPM\Database::execScalar(" SELECT COUNT(U.id) FROM [USER] U WHERE U.deleted IS NULL ", array());
                    $data->setPager($totalResults, $page, $resultsPage);
                    $params = array();
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->int(":start", (($page - 1) * $resultsPage));
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->int(":results_page", $resultsPage);
                    $params[] = $param;
                    $data->setResults(\PHP_MPM\Database::execWithResult(" SELECT U.id, U.email, U.name, U.type, UC.id AS creatorId, UC.name AS creatorName, datetime(U.created, 'localtime') AS creationDate FROM [USER] U LEFT JOIN [USER] UC ON U.creator = UC.id WHERE U.deleted IS NULL ORDER BY U.name ASC, U.email ASC LIMIT :start, :results_page ", $params));
                } else {
                    $data->setPager(0, 1, 0);
                    $data->setResults(\PHP_MPM\Database::execWithResult(" SELECT U.id, U.email, U.name, U.type, UC.id AS creatorId, UC.name AS creatorName, datetime(U.created, 'localtime') AS creationDate FROM [USER] U LEFT JOIN [USER] UC ON U.creator = UC.id WHERE U.deleted IS NULL ORDER BY U.name ASC, U.email ASC ", array()));
                }                                
                return($data);
            }
        }

        /**
        *   delete user
        */
        public function delete() {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! \PHP_MPM\User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else if (empty($this->id)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else if ($this->id == \PHP_MPM\User::getSessionUserId()) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));                
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":email", "deleted_email_on_" . microtime(true));
                $params[] = $param;
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;
                \PHP_MPM\Database::execWithoutResult(" UPDATE [USER] SET email = :email, deleted = CURRENT_TIMESTAMP WHERE id = :id ", $params);
            }
        }

        /**
        *   update user
        */
        public function update() {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (empty($this->id) || empty($this->email) || empty($this->name)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else if ($this->id != User::getSessionUserId() && ! \PHP_MPM\User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else if (! $this->exists()) {    
                throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));                    
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":email", $this->email);
                $params[] = $param;
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":name", $this->name);
                $params[] = $param;
                if (! empty($this->password)) {
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":password", password_hash($this->password, PASSWORD_BCRYPT, array("cost" => 12)));
                    $params[] = $param;                    
                }
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;
                $sql = null;
                if (! empty($this->password)) {
                    $sql = " UPDATE [USER] SET name = :name, email = :email, password = :password WHERE id = :id ";
                } else {
                    $sql = " UPDATE [USER] SET name = :name, email = :email WHERE id = :id ";
                }
                \PHP_MPM\Database::execWithoutResult($sql, $params);
            }
        }
        
    }
?>