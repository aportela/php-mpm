<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   attribute option (for attribute type == "select" (9)) class
    */
    class AttributeOption {
        public $id;
        public $name;
        public $idx;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $name = "", int $idx = 0) {
            $this->id = $id;
            $this->name = $name;
            $this->idx = $idx;
        }

        /**
        *   add new attribute
        */
        public function add($attributeId) {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                if (empty($attributeId)) {
                    throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
                } else {
                    if (empty($this->name)) {
                        throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
                    } else {                                        
                        if (empty($this->id)) {
                            $this->id = \PHP_MPM\Utils::uuid();
                        }
                        $params = array();
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->str(":attribute_id", $attributeId);
                        $params[] = $param;                
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->str(":option_id", $this->id);
                        $params[] = $param;                
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->str(":option_value", $this->name);
                        $params[] = $param;                
                        $param = new \PHP_MPM\DatabaseParam();
                        $param->int(":option_index", $this->idx);
                        $params[] = $param;                
                        \PHP_MPM\Database::execWithoutResult(" INSERT INTO [ATTRIBUTE_OPTIONS] (attribute_id, option_id, option_value, option_index) VALUES (:attribute_id, :option_id, :option_value, :option_index) ", $params);
                    }
                }
            }
        }        

        /**
        *   delete all attribute options
        */
        public function deleteAll($attributeId) {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else if (empty($attributeId)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":attribute_id", $attributeId);
                $params[] = $param;                                
                \PHP_MPM\Database::execWithoutResult(" DELETE FROM [ATTRIBUTE_OPTIONS] WHERE attribute_id = :attribute_id ", $params);
            }
        }

        /**
        *   get attribute options
        */
        public static function search($attributeId) {
            if (empty($templateId)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $options = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":attribute_id", $attributeId);
                $results = \PHP_MPM\Database::execWithResult(" SELECT AO.option_id AS id, AO.option_value AS name, AO.option_index AS idx FROM [ATTRIBUTE_OPTIONS] AO WHERE AO.template_id = :attribute_id ", array($param));
                foreach($results as $result) {
                    $option = new \PHP_MPM\AttributeOption();
                    $option->set($result->id, $result->name, $result->idx);
                    $options[] = $option;
                }                 
                return($options);
            }            
        }
    }
?>