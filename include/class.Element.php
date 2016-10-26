<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   element class
    */
    class Element {

        public $id;
        public $templateId;
        public $attributes;
        public $links;
        public $files;
        public $htmlForm;

		public function __construct () {
            $this->attributes = array();
            $this->links = array();
            $this->files = array();
        }

        public function __destruct() { }

        public function create() {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (empty($this->templateId)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));                                    
            } else {                                                            
                $t = new \PHP_MPM\Template();
                $t->id = $this->templateId;
                $t->get();
                // TODO: check flag allowCreate (permissions) for my user groups
                $this->id = \PHP_MPM\Utils::uuid();
                $this->attributes = $t->attributes;
                $this->htmlForm = $t->htmlForm;
            }
        }
    }
?>