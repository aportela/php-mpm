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

        private $id;
        private $name;
        private $description;
        private $users;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $name = "", string $description = "", $users = array()) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->users = $users;
        }

        /**
        *   check user (by email) existence
        */
        public function exists() {
            if (empty($this->id) && empty($this->name)) {                
                throw new MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $param = new DatabaseParam();
                $param->str(":id", $this->id);                
                $param = new DatabaseParam();
                $param->str(":name", $this->name);                
                $rows = Database::execWithResult(" SELECT * FROM [GROUP] WHERE id = :id OR name = :name ", array($param));
                return(count($rows) > 0);                
            }            
        }

        /**
        *   add new group
        */
        public function add() {
            if (! User::isAuthenticated()) {
                throw new MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                if ($this->exists()) {
                    throw new MPMAlreadyExistsException(print_r(get_object_vars($this), true));
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
                    Database::execWithoutResult(" INSERT INTO [GROUP] (id, name, description, created, creator) VALUES (:id, :name, :description, CURRENT_TIMESTAMP, :creator) ", $params);
                    if (count($this->users) > 0) {
                        foreach($users as $user) {
                            $this->addUser($user->id);
                        }
                    }
                }
            }
        }

        /**
        *   update group
        */
        public function update() {
            if (! User::isAuthenticated()) {
                throw new MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                if (empty($this->id)) {
                    throw new MPMInvalidParamsException(print_r(get_object_vars($this), true));                                    
                } else {
                    if (! $this->exists()) {    
                        throw new MPMNotFoundException(print_r(get_object_vars($this), true));    
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
                        Database::execWithoutResult(" UPDATE [GROUP] SET name = :name, description = :description WHERE id = :id ", $params);
                        // TODO: better check user diffs ¿?
                        $this->removeAllUsers();
                        if (count($this->users) > 0) {
                            foreach($users as $user) {
                                $this->addUser($user->id);
                            }
                        }
                    }
                }
            }
        }

        /**
        *   add user to group
        */
        private function addUser($userId) {
            if (empty($userId)) {
                throw new MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new DatabaseParam();
                $param->str(":group_id", $this->id);
                $params[] = $param;                            
                $param = new DatabaseParam();
                $param->str(":user_id", $userId);
                $params[] = $param;                                
                Database::execWithoutResult(" INSERT INTO [GROUP_USER] (group_id, user_id) VALUES (:group_id, :user_id) ", $params);
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
            Database::execWithoutResult(" DELETE FROM [GROUP_USER] WHERE group_id = :group_id AND user_id = :user_id ", $params);
        }

        /**
        *   remove all users from group
        */
        private function removeAllUsers() {
            $params = array();
            $param = new DatabaseParam();
            $param->str(":group_id", $this->id);
            $params[] = $param;                                
            Database::execWithoutResult(" DELETE FROM [GROUP_USER] WHERE group_id = :group_id ", $params);
        }        
        
        /**
        *   search (list) groups
        */
        public static function search($page, $resultsPage) {
            if (! User::isAuthenticated()) {
                throw new MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else {
                return(Database::execWithResult(" SELECT id, name, description FROM [GROUP] ORDER BY name ", array()));
            }
        }

        /**
        *   delete group (and all contained user references)
        */
        public function delete() {
            if (! User::isAuthenticated()) {
                throw new MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                // TODO: transaction support
                $this->removeAllUsers();
                $params = array();
                $param = new DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                                
                Database::execWithoutResult(" DELETE FROM [GROUP] WHERE id = :id ", $params);
            }
        }
    }
?>