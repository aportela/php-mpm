<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   attribute type definitions
    */
    abstract class AttributeType {
        const NONE = 0;             // default (_DON'T USE THIS_)
        const TEXT_SHORT = 1;       // short string (0-255 chars)
        const TEXT_LONG = 2;        // long string (memo)
        const NUMBER_INTEGER = 3;   // integer number
        const NUMBER_DECIMAL = 4;   // decimal number
        const DATE = 5;             // date 
        const TIME = 6;             // time
        const DATETIME = 7;         // date & time
        const BOOLEAN = 8;          // boolean (true/false)
        const SELECT = 9;           // select (multiple options) 
        const USER = 10;            // user
        const GROUP = 11;           // group
    }

    /**
    *   attribute class
    */
    class Attribute {
        public $id;
        public $name;
        public $description;
        public $type;
        public $options;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $name = "", $description = "", int $type = \PHP_MPM\AttributeType::NONE) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->type = $type;            
        }

        /**
        *   check attribute existence
        *
        *   only "id" set => search existence by id
        *   only "name" set => search existence by email
        *   "id" & "name" set => search existence by id        
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
                $sql = null;
                $params = array();
                if (empty($this->id)) {
                    // search by name
                    $sql = " SELECT * FROM [ATTRIBUTE] WHERE name = :name ";
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":name", $this->name);
                    $params[] = $param;                
                } else {
                    // search by id
                    $sql = " SELECT * FROM [ATTRIBUTE] WHERE id = :id ";
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
        *   search (list) groups
        */
        public static function search($page, $resultsPage, $searchByText) {
            if (! User::isAuthenticated()) {
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
                        $sql = " SELECT COUNT(A.id) FROM [ATTRIBUTE] A WHERE (A.name LIKE :text OR A.description LIKE :text) ";
                    } else {
                        $sql = " SELECT COUNT(A.id) FROM [ATTRIBUTE] A ";
                    }
                    $db = \PHP_MPM\Database::getHandler();
                    $totalResults = $db->execScalar($sql, $params);
                    $data->setPager($totalResults, $page, $resultsPage);
                    if ($totalResults > 0) {
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->int(":start", (($page - 1) * $resultsPage));
                        $params[] = $param;
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->int(":results_page", $resultsPage);
                        $params[] = $param;
                        if (! empty($searchByText)) {
                            $sql = " SELECT A.id, A.name, A.description, A.type, U.id AS creatorId, U.name AS creatorName, datetime(A.created, 'localtime') AS creationDate FROM [ATTRIBUTE] A LEFT JOIN [USER] U ON U.id = A.creator WHERE (A.name LIKE :text OR A.description LIKE :text) ORDER BY A.name COLLATE NOCASE ASC LIMIT :start, :results_page ";
                        } else {
                            $sql = " SELECT A.id, A.name, A.description, A.type, U.id AS creatorId, U.name AS creatorName, datetime(A.created, 'localtime') AS creationDate FROM [ATTRIBUTE] A LEFT JOIN [USER] U ON U.id = A.creator ORDER BY A.name COLLATE NOCASE ASC LIMIT :start, :results_page ";
                        }                    
                        $data->setResults($db->execWithResult($sql, $params));
                    }
                } else {
                    $data->setPager(0, 1, 0);
                    if (! empty($searchByText)) {
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->str(":text", "%" . $searchByText . "%");
                        $params[] = $param;                        
                        $sql = " SELECT A.id, A.name, A.description, A.type, U.id AS creatorId, U.name AS creatorName, datetime(A.created, 'localtime') AS creationDate FROM [ATTRIBUTE] A LEFT JOIN [USER] U ON U.id = A.creator WHERE (A.name LIKE :text OR A.description LIKE :text) ORDER BY A.name COLLATE NOCASE ASC ";
                    } else {
                        $sql = " SELECT A.id, A.name, A.description, A.type, U.id AS creatorId, U.name AS creatorName, datetime(A.created, 'localtime') AS creationDate FROM [ATTRIBUTE] A LEFT JOIN [USER] U ON U.id = A.creator ORDER BY A.name COLLATE NOCASE ASC ";
                    }
                    $db = \PHP_MPM\Database::getHandler();
                    $data->setResults($db->execWithResult($sql, $params));
                }                                
                return($data);
            }
        }

        /**
        *   add new attribute
        */
        public function add() {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                if ($this->exists()) {
                    throw new \PHP_MPM\MPMAlreadyExistsException(print_r(get_object_vars($this), true));
                } else {
                    if (empty($this->name)) {
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
                        $param->int(":type", $this->type);
                        $params[] = $param;                
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->str(":creator", User::getSessionUserId());
                        $params[] = $param;
                        $db = \PHP_MPM\Database::getHandler(true);                
                        $db->execWithoutResult(" INSERT INTO [ATTRIBUTE] (id, name, description, type, created, creator) VALUES (:id, :name, :description, :type, CURRENT_TIMESTAMP, :creator) ", $params);
                        if ($this->type == \PHP_MPM\AttributeType::SELECT) {
                            if ($this->options) {
                                $t = count($this->options);
                                for ($i = 0; $i < $t; $i++) {
                                    $this->options[$i]->add($this->id);
                                }
                            }
                        }
                    }
                }
            }
        }        

        /**
        *   update attribute
        */
        public function update() {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                if (empty($this->id) || empty($this->name)) {
                    throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
                } else if (! $this->exists()) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
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
                    $db = \PHP_MPM\Database::getHandler(true);
                    $db->execWithoutResult(" UPDATE [ATTRIBUTE] SET name = :name, description = :description WHERE id = :id ", $params);
                    if ($this->type == \PHP_MPM\AttributeType::SELECT) {
                        \PHP_MPM\AttributeOption::deleteAll($this->id);
                        if ($this->options) {
                            $t = count($this->options);
                            for ($i = 0; $i < $t; $i++) {
                                $this->options[$i].add($this->id);
                            }
                        }
                    }
                }
            }
        }

        /**
        *   delete attribute
        */
        public function delete() {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else if (empty($this->id)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;
                $db = \PHP_MPM\Database::getHandler(true);                                
                $db->execWithoutResult(" DELETE FROM [ATTRIBUTE] WHERE id = :id ", $params);
                \PHP_MPM\AttributeOption::deleteAll($this->id);
            }
        }

        /**
        *   get attribute (metadata / options)
        */
        public function get() {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (empty($this->id)) {                
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $db = \PHP_MPM\Database::getHandler();                
                $rows = $db->execWithResult(" SELECT name, description, type FROM [ATTRIBUTE] WHERE id = :id ", array($param));
                if (count($rows) != 1) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    $this->set($this->id, $rows[0]->name, $rows[0]->description, $rows[0]->type);
                    if ($this->type == \PHP_MPM\AttributeType::SELECT) {
                        $this->options = \PHP_MPM\AttributeOption::search($this->id);
                    }                    
                    return(get_object_vars($this));
                }
            }
        }        
    }
?>