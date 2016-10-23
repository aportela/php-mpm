<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   template attribute class
    */
    class TemplateAttributeDefinition extends \PHP_MPM\AttributeDefinition {

        public $label;
        public $required;
        public $defaultValue;

		public function __construct () {
            parent::__construct();
        }

        public function __destruct() { }

        public function set($id, $attribute, $label = "", $required = false, $defaultValue = null) {
            $this->id = $id;
            $this->attribute = $attribute;
            $this->label = $label;
            $this->required = boolval($required);
            $this->defaultValue = $defaultValue;
        }

        /**
        *   add new attribute definition to template
        */
        public function add($templateId) {
            if (empty($templateId) || empty($this->attribute->id) || empty($this->label)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $a = new \PHP_MPM\Attribute();
                $a->id = $this->attribute->id;
                if (! $a->exists()) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    if (empty($this->id)) {
                        $this->id = \PHP_MPM\Utils::uuid(); 
                    }
                    $params = array();
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":id", $this->id);
                    $params[] = $param;                            
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":template_id", $templateId);
                    $params[] = $param;                            
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":attribute_id", $this->attribute->id);
                    $params[] = $param;                                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":label", $this->label);
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->bool(":required", $this->required);
                    $params[] = $param;
                    \PHP_MPM\Database::execWithoutResult(" INSERT INTO [TEMPLATE_ATTRIBUTE] (id, template_id, attribute_id, label, required) VALUES (:id, :template_id, :attribute_id, :label, :required) ", $params);
                }
            }                        
        }

        /**
        *   update template attribute definition
        */
        public function update() {
            if (empty($this->id) || empty($this->label)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                            
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":label", $this->label);
                $params[] = $param;
                $param = new \PHP_MPM\DatabaseParam();
                $param->bool(":required", $this->required);
                $params[] = $param;
                \PHP_MPM\Database::execWithoutResult(" UPDATE [TEMPLATE_ATTRIBUTE] SET label = :label, required = :required WHERE id = :id ", $params);
            }                                    
        }

        /**
        *   delete template attribute definition
        */
        public function delete() {
            if (empty($this->id)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":id", $this->id);
                $params[] = $param;                            
                \PHP_MPM\Database::execWithoutResult(" DELETE FROM [TEMPLATE_ATTRIBUTE] WHERE id = :id ", $params);
            }                                    
        }

        /**
        *   get template attribute definitions
        */
        public static function search($templateId) {
            if (empty($templateId)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $attributes = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":template_id", $templateId);
                $results = \PHP_MPM\Database::execWithResult(" SELECT TA.id AS id, A.id AS attributeId, A.name AS attributeName, A.description AS attributeDescription, A.type AS attributeType, TA.label, TA.required FROM [TEMPLATE_ATTRIBUTE] TA LEFT JOIN [ATTRIBUTE] A ON A.id = TA.attribute_id WHERE TA.template_id = :template_id ", array($param));
                foreach($results as $result) {
                    $templateAttribute = new \PHP_MPM\TemplateAttributeDefinition();
                    $a = new \PHP_MPM\Attribute();
                    $a->id = $result->attributeId;
                    $a->name = $result->attributeName;
                    $a->description = $result->attributeDescription;
                    $a->type = $result->attributeType;
                    $templateAttribute->set($result->id, $a, $result->label, $result->required, "");
                    $attributes[] = $templateAttribute;
                }                 
                return($attributes);
            }
        }

        /**
        *   remove all template attribute definitions
        */
        public static function deleteAll($templateId) {
            if (empty($templateId)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":template_id", $templateId);
                $params[] = $param;                            
                \PHP_MPM\Database::execWithoutResult(" DELETE FROM [TEMPLATE_ATTRIBUTE] WHERE template_id = :template_id ", $params);
            }                                                
        }
    }

?>