<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   search template class
    */
    class SearchTemplate {

        public $id;
        public $name;
        public $description;
        public $permissions;
        public $sql;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $name = "", string $description = "", $permissions = array(), string $sql = "") {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->permissions = $permissions;
            $this->sql = $sql;
        }

        /**
        *   check search template existence
        *
        *   only "id" set => search existence by id
        *   only "name" set => search existence by name
        *   "id" & "name" set => search existence by id        
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
                $sql = null;                
                $params = array();
                if (empty($this->id)) {
                    // search by name
                    $sql = " SELECT * FROM [SEARCH_TEMPLATE] WHERE name = :name ";
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":name", $this->name);
                    $params[] = $param;                
                } else {
                    // search by id
                    $sql = " SELECT * FROM [SEARCH_TEMPLATE] WHERE id = :id ";
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":id", $this->id);
                    $params[] = $param;                
                }
                $db = \PHP_MPM\Database::getHandler();
                $rows = $db->execWithResult($sql, $params);
                return(count($rows) > 0);                
            }            
        }

        /**
        *   add new search template
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
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":sql", $this->sql);
                $params[] = $param;
                $db = \PHP_MPM\Database::getHandler(true);
                $db->execWithoutResult(" INSERT INTO [SEARCH_TEMPLATE] (id, name, description, created, creator, sql) VALUES (:id, :name, :description, CURRENT_TIMESTAMP, :creator, :sql) ", $params);
            }
        }
    }
?>