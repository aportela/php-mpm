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
    }

    /**
    *   attribute class
    */
    class Attribute {
        public $id;
        public $name;
        public $description;
        public $type;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $name = "", string $description = "", int $type = AttributeType::NONE) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->type = $type;            
        }

        /**
        *   check attribute (by id/name) existence
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
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":name", $this->name);
                $params[] = $param;                
                $rows = \PHP_MPM\Database::execWithResult(" SELECT * FROM [ATTRIBUTE] WHERE id = :id OR name = :name ", $params);
                return(count($rows) > 0);                
            }            
        }

        /**
        *   search (list) groups
        */
        public static function search($page, $resultsPage) {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException("");
            } else {
                // TODO: pagination & filtering
                // TODO: type is returned as string (not integer)
                return(\PHP_MPM\Database::execWithResult(" SELECT A.id, A.name, A.description, A.type, U.id AS creatorId, U.name AS creatorName, A.created AS creationDate FROM [ATTRIBUTE] A LEFT JOIN [USER] U ON U.id = A.creator ORDER BY A.name ", array()));
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
                        \PHP_MPM\Database::execWithoutResult(" INSERT INTO [ATTRIBUTE] (id, name, description, type, created, creator) VALUES (:id, :name, :description, :type, CURRENT_TIMESTAMP, :creator) ", $params);
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
                    \PHP_MPM\Database::execWithoutResult(" UPDATE [ATTRIBUTE] SET name = :name, description = :description WHERE id = :id ", $params);
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
                \PHP_MPM\Database::execWithoutResult(" DELETE FROM [ATTRIBUTE] WHERE id = :id ", $params);
            }
        }
    }
?>