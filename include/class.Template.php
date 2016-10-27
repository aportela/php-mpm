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
        public $htmlForm;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $name = "", string $description = "", $permissions = array(), $attributes = array(), $htmlForm = "") {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->permissions = $permissions;
            $this->attributes = $attributes;
            $this->htmlForm = $htmlForm;
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
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":html_form", $this->htmlForm);
                $params[] = $param;
                // TODO: transaction support
                \PHP_MPM\Database::execWithoutResult(" INSERT INTO [TEMPLATE] (id, name, description, created, creator, html_form) VALUES (:id, :name, :description, CURRENT_TIMESTAMP, :creator, :html_form) ", $params);
                foreach($this->permissions as $permission) {
                    $permission->add($this->id);
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
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":html_form", $this->htmlForm);
                $params[] = $param;                
                // TODO: transaction support
                \PHP_MPM\Database::execWithoutResult(" UPDATE [TEMPLATE] SET name = :name, description = :description, html_form = :html_form WHERE id = :id ", $params);
                // TODO: better check user diffs ¿?
                \PHP_MPM\TemplatePermission::deleteAll($this->id);
                foreach($this->permissions as $permission) {
                    $permission->add($this->id);
                }
                \PHP_MPM\TemplateAttributeDefinition::deleteAll($this->id);
                foreach($this->attributes as $templateAttribute) {
                    if (empty($templateAttribute->id)) {
                        $templateAttribute->id = \PHP_MPM\Utils::uuid();
                    }
                    $templateAttribute->add($this->id);
                }
            }
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
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                                
                \PHP_MPM\Database::execWithoutResult(" DELETE FROM [TEMPLATE] WHERE id = :id ", $params);
                \PHP_MPM\TemplateAttributeDefinition::deleteAll($this->id);
                \PHP_MPM\TemplatePermission::deleteAll($this->id);
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
                $rows = \PHP_MPM\Database::execWithResult(" SELECT name, description, html_form AS htmlForm FROM [TEMPLATE] WHERE id = :id ", array($param));
                if (count($rows) != 1) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    $this->name = $rows[0]->name;
                    $this->description = $rows[0]->description;
                    $this->htmlForm = $rows[0]->htmlForm;
                    $this->attributes = \PHP_MPM\TemplateAttributeDefinition::search($this->id);
                    $this->permissions = \PHP_MPM\TemplatePermission::search($this->id);
                    return(get_object_vars($this));
                }
            }
        }
    }
?>