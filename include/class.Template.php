<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   template class
    */
    class Template {

        public $id;
        public $name;
        public $description;
        public $permissions;
        public $attributes;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $name = "", string $description = "", $permissions = array(), $attributes = array()) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->permissions = $permissions;
            $this->attributes = $attributes;
        }

        /**
        *   check template existence
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
                    $sql = " SELECT * FROM [TEMPLATE] WHERE name = :name ";
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":name", $this->name);
                    $params[] = $param;                
                } else {
                    // search by id
                    $sql = " SELECT * FROM [TEMPLATE] WHERE id = :id ";
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":id", $this->id);
                    $params[] = $param;                
                }
                $rows = \PHP_MPM\Database::execWithResult($sql, $params);
                return(count($rows) > 0);                
            }            
        }

        /**
        *   add new template
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
                    $param->nulsl(":description");
                }
                $params[] = $param;
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":creator", \PHP_MPM\User::getSessionUserId());
                $params[] = $param;
                // TODO: transaction support
                \PHP_MPM\Database::execWithoutResult(" INSERT INTO [TEMPLATE] (id, name, description, created, creator) VALUES (:id, :name, :description, CURRENT_TIMESTAMP, :creator) ", $params);
                if ($this->permissions && count($this->permissions) > 0) {
                    foreach($this->permissions as $permission) {
                        $this->addPermission($permission->group->id, $permission->allowCreate, $permission->allowView, $permission->allowUpdate, $permission->allowDelete);
                    }
                }
                if ($this->attributes && count($this->attributes) > 0) {
                    foreach($this->attributes as $templateAttribute) {
                        if (empty($templateAttribute->id)) {
                            $templateAttribute->id = \PHP_MPM\Utils::uuid();
                        }
                        $templateAttribute->add($this->id);
                    }
                }
            }
        }

        /**
        *   update template
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
                \PHP_MPM\Database::execWithoutResult(" UPDATE [TEMPLATE] SET name = :name, description = :description WHERE id = :id ", $params);
                // TODO: better check user diffs ¿?
                $this->removeAllPermissions();
                foreach($this->permissions as $permission) {
                    $this->addPermission($permission->group->id, $permission->allowCreate, $permission->allowView, $permission->allowUpdate, $permission->allowDelete);
                }
                \PHP_MPM\TemplateAttribute::deleteAllTemplateAttributes($this->id);
                foreach($this->attributes as $templateAttribute) {
                    if (empty($templateAttribute->id)) {
                        $templateAttribute->id = \PHP_MPM\Utils::uuid();
                    }
                    $templateAttribute->add($this->id);
                }
            }
        }

        /**
        *   add group to template
        */
        private function addPermission($groupId, $allowCreate = true, $allowView = true, $allowUpdate = true, $allowDelete = true) {
            if (empty($groupId)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $g = new \PHP_MPM\Group();
                $g->set($groupId, "", "", 0);
                if (! $g->exists()) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    $params = array();
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":template_id", $this->id);
                    $params[] = $param;                            
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":group_id", $groupId);
                    $params[] = $param;                                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->bool(":allow_create", $allowCreate);
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->bool(":allow_view", $allowView);
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->bool(":allow_update", $allowUpdate);
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->bool(":allow_delete", $allowDelete);
                    $params[] = $param;
                    \PHP_MPM\Database::execWithoutResult(" INSERT INTO [TEMPLATE_PERMISSION] (template_id, group_id, allow_create, allow_view, allow_update, allow_delete) VALUES (:template_id, :group_id, :allow_create, :allow_view, :allow_update, :allow_delete) ", $params);
                }
            }            
        }

        /**
        * remove group from template
        */
        private function removePermission($groupId) {
            $params = array();
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":template_id", $this->id);
            $params[] = $param;
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":group_id", $groupId);
            $params[] = $param;
            \PHP_MPM\Database::execWithoutResult(" DELETE FROM [TEMPLATE_PERMISSION] WHERE template_id = :template_id AND group_id = :group_id ", $params);
        }

        /**
        *   remove all groups from template
        */
        private function removeAllPermissions() {
            $params = array();
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":template_id", $this->id);
            $params[] = $param;                                
            \PHP_MPM\Database::execWithoutResult(" DELETE FROM [TEMPLATE_PERMISSION] WHERE template_id = :template_id ", $params);
        }        
        
        /**
        *   search (list) templates
        */
        public static function search($page, $resultsPage, $searchByText) {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException("");
            } else {
                $data = new \PHP_MPM\SearchResults();
                $sql = null;
                $params = array();
                if ($resultsPage > 0) {
                    if (! empty($searchByText)) {
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->str(":text", "%" . $searchByText . "%");
                        $params[] = $param;
                        $sql = " SELECT COUNT(T.id) FROM [TEMPLATE] T WHERE (T.name LIKE :text OR T.description LIKE :text) ";
                    } else {           
                        $sql = " SELECT COUNT(T.id) FROM [TEMPLATE] T ";
                    }
                    $totalResults = \PHP_MPM\Database::execScalar($sql, $params);
                    $data->setPager($totalResults, $page, $resultsPage);
                    if ($totalResults > 0) {
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->int(":start", (($page - 1) * $resultsPage));
                        $params[] = $param;
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->int(":results_page", $resultsPage);
                        $params[] = $param;
                        if (! empty($searchByText)) {
                            $sql = " SELECT T.id, T.name, T.description, U.id AS creatorId, U.name AS creatorName, datetime(T.created, 'localtime') AS creationDate FROM [TEMPLATE] T LEFT JOIN [USER] U ON U.id = T.creator WHERE (T.name LIKE :text OR T.description LIKE :text) ORDER BY T.name COLLATE NOCASE ASC LIMIT :start, :results_page ";                            
                        } else {
                            $sql = " SELECT T.id, T.name, T.description, U.id AS creatorId, U.name AS creatorName, datetime(T.created, 'localtime') AS creationDate FROM [TEMPLATE] T LEFT JOIN [USER] U ON U.id = T.creator ORDER BY T.name COLLATE NOCASE ASC LIMIT :start, :results_page ";
                        }                 
                        $data->setResults(\PHP_MPM\Database::execWithResult($sql, $params));
                    }
                } else {
                    $data->setPager(0, 1, 0);
                    if (! empty($searchByText)) {
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->str(":text", "%" . $searchByText . "%");
                        $params[] = $param;                        
                        $sql = " SELECT T.id, T.name, T.description, U.id AS creatorId, U.name AS creatorName, datetime(T.created, 'localtime') AS creationDate FROM [TEMPLATE] T LEFT JOIN [USER] U ON U.id = T.creator WHERE (T.name LIKE :text OR T.description LIKE :text) ORDER BY T.name COLLATE NOCASE ASC ";
                    } else {
                        $sql = " SELECT T.id, T.name, T.description, U.id AS creatorId, U.name AS creatorName, datetime(T.created, 'localtime') AS creationDate FROM [TEMPLATE] T LEFT JOIN [USER] U ON U.id = T.creator ORDER BY T.name COLLATE NOCASE ASC ";
                    }
                    $data->setResults(\PHP_MPM\Database::execWithResult($sql, $params));
                }                                
                return($data);
            }
        }

        /**
        *   delete template (and all contained group references)
        */
        public function delete() {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! \PHP_MPM\User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                // TODO: transaction support
                $this->removeAllPermissions();
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                                
                \PHP_MPM\Database::execWithoutResult(" DELETE FROM [TEMPLATE] WHERE id = :id ", $params);
                // TODO: delete template permissions & attributes (references)
            }
        }

        /**
        *   get template permissions
        */
        private function getPermissions() {
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":template_id", $this->id);
            $results = \PHP_MPM\Database::execWithResult(" SELECT TP.group_id AS id, G.name, G.description, TP.allow_create AS allowCreate, TP.allow_update AS allowUpdate, TP.allow_view AS allowView, TP.allow_delete AS allowDelete FROM [TEMPLATE_PERMISSION] TP LEFT JOIN [GROUP] G ON G.id = TP.group_id WHERE TP.template_id = :template_id ", array($param));
            $this->permissions = array();
            foreach($results as $result) {

                $permission = new \stdClass;
                $permission->group = new \stdClass;
                $permission->group->id = $result->id;
                $permission->group->name = $result->name;
                $permission->group->description = $result->description;
                $permission->allowCreate = $result->allowCreate == 1;
                $permission->allowView = $result->allowView == 1;
                $permission->allowUpdate = $result->allowUpdate == 1;
                $permission->allowDelete = $result->allowDelete == 1;
                $this->permissions[] = $permission; 
            }                 
        }

        /**
        *   get template (metadata / permissions / attributes)
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
                $rows = \PHP_MPM\Database::execWithResult(" SELECT name, description FROM [TEMPLATE] WHERE id = :id ", array($param));
                if (count($rows) != 1) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    $this->name = $rows[0]->name;
                    $this->description = $rows[0]->description;
                    $this->getPermissions();
                    $this->attributes = \PHP_MPM\TemplateAttribute::getTemplateAttributes($this->id);
                    return(get_object_vars($this));
                }
            }
        }
    }
?>