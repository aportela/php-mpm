<?php
    namespace PHP_MPM;

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
                throw new MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            }
            else if (empty($this->id) && empty($this->name)) {                
                throw new MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                
                $param = new DatabaseParam();
                $param->str(":name", $this->name);
                $params[] = $param;                
                $rows = Database::execWithResult(" SELECT * FROM [ATTRIBUTE] WHERE id = :id OR name = :name ", $params);
                return(count($rows) > 0);                
            }            
        }

        /**
        *   search (list) groups
        */
        public static function search($page, $resultsPage) {
            if (! User::isAuthenticated()) {
                throw new MPMAuthSessionRequiredException("");
            } else {
                // TODO: pagination & filtering
                // TODO: type is returned as string (not integer)
                return(Database::execWithResult(" SELECT id, name, description, type FROM [ATTRIBUTE] ORDER BY name ", array()));
            }
        }

        /**
        *   add new attribute
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
                    if (empty($this->name)) {
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
                        $param->str(":name", $this->name);
                        $params[] = $param;                
                        $param = new DatabaseParam();
                        if (! empty($this->description)) {
                            $param->str(":description", $this->description);
                        } else {
                            $param->null(":description");
                        }
                        $params[] = $param;
                        $param = new DatabaseParam();
                        $param->int(":type", $this->type);
                        $params[] = $param;                
                        $param = new DatabaseParam();
                        $param->str(":creator", User::getSessionUserId());
                        $params[] = $param;                
                        Database::execWithoutResult(" INSERT INTO [ATTRIBUTE] (id, name, description, type, created, creator) VALUES (:id, :name, :description, :type, CURRENT_TIMESTAMP, :creator) ", $params);
                    }
                }
            }
        }        

        /**
        *   update attribute
        */
        public function update() {
            if (! User::isAuthenticated()) {
                throw new MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                if (empty($this->id) || empty($this->name)) {
                    throw new MPMInvalidParamsException(print_r(get_object_vars($this), true));
                } else if (! $this->exists()) {
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
                    Database::execWithoutResult(" UPDATE [ATTRIBUTE] SET name = :name, description = :description WHERE id = :id ", $params);
                }
            }
        }

        /**
        *   delete attribute
        */
        public function delete() {
            if (! User::isAuthenticated()) {
                throw new MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                                
                Database::execWithoutResult(" DELETE FROM [ATTRIBUTE] WHERE id = :id ", $params);
            }
        }
    }
?>