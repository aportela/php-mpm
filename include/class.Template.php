<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   simple (temporal ?) templating class
    */
    class Template {
        private $path;
        public $name;

		public function __construct (string $name = "") {
            $this->path = TEMPLATES_PATH . DIRECTORY_SEPARATOR . DEFAULT_TEMPLATE_THEME . DIRECTORY_SEPARATOR . $name;             
            if (! file_exists($this->path)) {
                throw new \PHP_MPM\MPMCustomTemplatingException("template not found on path " . $this->path);
            } else {
                $this->name = $name;
            }
        }

        public function __destruct() { }

        public function render($params) {
            global $_TEMPLATE;
            if (! isset($_TEMPLATE)) {
                $_TEMPLATE = array();
            }            
            foreach ($params as $key => $value) {
                $_TEMPLATE[$key] = $value;
            }
            include($this->path);
        }        
    }
?>