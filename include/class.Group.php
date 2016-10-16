<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

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
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! \PHP_MPM\User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            }
            else if (empty($this->id) && empty($this->name)) {                
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                
                $param = new \PHP_MPM\DatabaseParam();
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
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! \PHP_MPM\User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else if ($this->exists()) {
                throw new \PHP_MPM\MPMAlreadyExistsException(print_r(get_object_vars($this), true));
            } else if (empty($this->name)) {
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
                $param->str(":name", $this->name);
                $params[] = $param;                
                $param = new \PHP_MPM\DatabaseParam();
                if (! empty($this->description)) {
                    $param->str(":description", $this->description);
                } else {
                    $param->null(":description");
                }
                $params[] = $param;
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":creator", \PHP_MPM\User::getSessionUserId());
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
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! \PHP_MPM\User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else if (empty($this->id)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));                                    
            } else if (! $this->exists()) {    
                throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));    
            } else if (empty($this->name)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {                                            
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":name", $this->name);
                $params[] = $param;                
                $param = new \PHP_MPM\DatabaseParam();
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
                $u = new \PHP_MPM\User();
                $u->set($userId, "", "", 0);
                if (! $u->exists()) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    $params = array();
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":group_id", $this->id);
                    $params[] = $param;                            
                    $param = new \PHP_MPM\DatabaseParam();
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
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":group_id", $this->id);
            $params[] = $param;
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":user_id", $userId);
            $params[] = $param;
            \PHP_MPM\Database::execWithoutResult(" DELETE FROM [GROUP_USER] WHERE group_id = :group_id AND user_id = :user_id ", $params);
        }

        /**
        *   remove all users from group
        */
        private function removeAllUsers() {
            $params = array();
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":group_id", $this->id);
            $params[] = $param;                                
            \PHP_MPM\Database::execWithoutResult(" DELETE FROM [GROUP_USER] WHERE group_id = :group_id ", $params);
        }        
        
        /**
        *   search (list) groups
        */
        public static function search($page, $resultsPage) {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException("");
            } else {
                $data = new \PHP_MPM\SearchResults();
                if ($resultsPage > 0) {
                    $totalResults = \PHP_MPM\Database::execScalar(" SELECT COUNT(G.id) FROM [GROUP] G ", array());
                    $data->setPager($totalResults, $page, $resultsPage);
                    $params = array();
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->int(":start", (($page - 1) * $resultsPage));
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->int(":results_page", $resultsPage);
                    $params[] = $param;                    
                    $data->setResults(\PHP_MPM\Database::execWithResult(" SELECT G.id, G.name, G.description, U.id AS creatorId, U.name AS creatorName, datetime(G.created, 'localtime') AS creationDate FROM [GROUP] G LEFT JOIN [USER] U ON U.id = G.creator ORDER BY G.name LIMIT :start, :results_page ", $params));
                } else {
                    $data->setPager(0, 1, 0);
                    $data->setResults(\PHP_MPM\Database::execWithResult(" SELECT G.id, G.name, G.description, U.id AS creatorId, U.name AS creatorName, datetime(G.created, 'localtime') AS creationDate FROM [GROUP] G LEFT JOIN [USER] U ON U.id = G.creator ORDER BY G.name ", array()));
                }                                
                return($data);
            }
        }

        /**
        *   delete group (and all contained user references)
        */
        public function delete() {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! \PHP_MPM\User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                // TODO: transaction support
                $this->removeAllUsers();
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                                
                \PHP_MPM\Database::execWithoutResult(" DELETE FROM [GROUP] WHERE id = :id ", $params);
            }
        }

        /**
        *   get group users
        */
        private function getUsers() {
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":group_id", $this->id);                
            return(\PHP_MPM\Database::execWithResult(" SELECT GU.user_id AS id, U.email FROM [GROUP_USER] GU LEFT JOIN USER U ON U.id = GU.user_id WHERE GU.group_id = :group_id ", array($param)));
        }

        /**
        *   get group (metadata & users)
        */
        public function get() {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! \PHP_MPM\User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else if (empty($this->id)) {                
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $param = new \PHP_MPM\DatabaseParam();
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