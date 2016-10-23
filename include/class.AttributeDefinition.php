<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   attribute definition class
    */
    class AttributeDefinition {

        public $id;
        public $attribute;

		public function __construct () {
            $this->attribute = new \PHP_MPM\Attribute(); 
        }

        public function __destruct() { }
    }
?>