<?php
    namespace PHP_MPM;

    require_once "class.CustomExceptions.php";
    require_once "class.Database.php";
    require_once "class.User.php";
    require_once "class.Utils.php";

    /**
    *   group class
    */
    class Group {

        public $id;
        public $name;
        public $description;
        public $users;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $name = "", string $description = "", $users = array()) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->users = $users;
        }

        /**
        *   check group (by id/name) existence
        */
        public function exists() {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            }
            else if (empty($this->id) && empty($this->name)) {                
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                
                $param = new DatabaseParam();
                $param->str(":name", $this->name);
                $params[] = $param;                
                $rows = \PHP_MPM\Database::execWithResult(" SELECT * FROM [GROUP] WHERE id = :id OR name = :name ", $params);
                return(count($rows) > 0);                
            }            
        }

        /**
        *   add new group
        */
        public function add() {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else if ($this->exists()) {
                throw new \PHP_MPM\MPMAlreadyExistsException(print_r(get_object_vars($this), true));
            } else if (empty($this->name)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                if (empty($this->id)) {
                    $this->id = Utils::uuid();
                }
                $params = array();
                $param = new DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                
                $param = new DatabaseParam();
                $param->str(":name", $this->name);
                $params[] = $param;                
                $param = new DatabaseParam();
                if (! empty($this->description)) {
                    $param->str(":description", $this->description);
                } else {
                    $param->null(":description");
                }
                $param = new DatabaseParam();
                $param->str(":creator", User::getSessionUserId());
                $params[] = $param;                
                // TODO: transaction support
                \PHP_MPM\Database::execWithoutResult(" INSERT INTO [GROUP] (id, name, description, created, creator) VALUES (:id, :name, :description, CURRENT_TIMESTAMP, :creator) ", $params);
                if ($this->users && count($this->users) > 0) {
                    foreach($this->users as $user) {
                        $this->addUser($user->id);
                    }
                }
            }
        }

        /**
        *   update group
        */
        public function update() {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else if (empty($this->id)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));                                    
            } else if (! $this->exists()) {    
                throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));    
            } else if (empty($this->name)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {                                            
                $params = array();
                $param = new DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                
                $param = new DatabaseParam();
                $param->str(":name", $this->name);
                $params[] = $param;                
                $param = new DatabaseParam();
                if (! empty($this->description)) {
                    $param->str(":description", $this->description);
                } else {
                    $param->null(":description");
                }
                $params[] = $param;
                // TODO: transaction support
                \PHP_MPM\Database::execWithoutResult(" UPDATE [GROUP] SET name = :name, description = :description WHERE id = :id ", $params);
                // TODO: better check user diffs ¿?
                $this->removeAllUsers();
                if ($this->users && count($this->users) > 0) {
                    foreach($this->users as $user) {
                        $this->addUser($user->id);
                    }
                }
            }
        }

        /**
        *   add user to group
        */
        private function addUser($userId) {
            if (empty($userId)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $u = new User();
                $u->set($userId, "", "", 0);
                if (! $u->exists()) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    $params = array();
                    $param = new DatabaseParam();
                    $param->str(":group_id", $this->id);
                    $params[] = $param;                            
                    $param = new DatabaseParam();
                    $param->str(":user_id", $userId);
                    $params[] = $param;                                
                    \PHP_MPM\Database::execWithoutResult(" INSERT INTO [GROUP_USER] (group_id, user_id) VALUES (:group_id, :user_id) ", $params);
                }
            }            
        }

        /**
        * remove user grom group
        */
        private function removeUser($userId) {
            $params = array();
            $param = new DatabaseParam();
            $param->str(":group_id", $this->id);
            $params[] = $param;
            $param = new DatabaseParam();
            $param->str(":user_id", $userId);
            $params[] = $param;
            \PHP_MPM\Database::execWithoutResult(" DELETE FROM [GROUP_USER] WHERE group_id = :group_id AND user_id = :user_id ", $params);
        }

        /**
        *   remove all users from group
        */
        private function removeAllUsers() {
            $params = array();
            $param = new DatabaseParam();
            $param->str(":group_id", $this->id);
            $params[] = $param;                                
            \PHP_MPM\Database::execWithoutResult(" DELETE FROM [GROUP_USER] WHERE group_id = :group_id ", $params);
        }        
        
        /**
        *   search (list) groups
        */
        public static function search($page, $resultsPage) {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException("");
            } else {
                // TODO: pagination & filtering
                return(\PHP_MPM\Database::execWithResult(" SELECT G.id, G.name, G.description, U.id AS creatorId, U.name AS creatorName, G.created AS creationDate FROM [GROUP] G LEFT JOIN [USER] U ON U.id = G.creator ORDER BY G.name ", array()));
            }
        }

        /**
        *   delete group (and all contained user references)
        */
        public function delete() {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                // TODO: transaction support
                $this->removeAllUsers();
                $params = array();
                $param = new DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                                
                \PHP_MPM\Database::execWithoutResult(" DELETE FROM [GROUP] WHERE id = :id ", $params);
            }
        }

        /**
        *   get group users
        */
        private function getUsers() {
            $param = new DatabaseParam();
            $param->str(":group_id", $this->id);                
            return(\PHP_MPM\Database::execWithResult(" SELECT GU.user_id AS id, U.email FROM [GROUP_USER] GU LEFT JOIN USER U ON U.id = GU.user_id WHERE GU.group_id = :group_id ", array($param)));
        }

        /**
        *   get group (metadata & users)
        */
        public function get() {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else if (empty($this->id)) {                
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $param = new DatabaseParam();
                $param->str(":id", $this->id);                
                $rows = \PHP_MPM\Database::execWithResult(" SELECT name, description FROM [GROUP] WHERE id = :id OR name = :name ", array($param));
                if (count($rows) != 1) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    $this->name = $rows[0]->name;
                    $this->description = $rows[0]->description;
                    $this->users = $this->getUsers();
                    return(get_object_vars($this));
                }
            }
        }
    }
?>