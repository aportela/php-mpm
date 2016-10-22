<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   permission class
    */
    class TemplateAttribute {

        public $id;
        public $attribute;
        public $required;
        public $defaultValue;

		public function __construct () {
            $this->attribute = new \PHP_MPM\Attribute(); 
        }

        public function __destruct() { }

        public function set($id, $attribute, $required = false, $defaultValue = null) {
            $this->id = $id;
            $this->attribute = $attribute;
            $this->required = $required;
            $this->defaultValue = $defaultValue;
        }
    }

