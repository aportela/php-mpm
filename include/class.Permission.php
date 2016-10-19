<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   permission class
    */
    class Permission {

        public $group;
        public $allowCreate;
        public $allowView;
        public $allowUpdate;
        public $allowDelete;

		public function __construct () {
            $this->group = new \PHP_MPM\Group(); 
            $this->allowCreate = false;
            $this->allowView = false;
            $this->allowUpdate = false;
            $this->allowDelete = false;
        }

        public function __destruct() { }

        public function set($group, $allowCreate = false, $allowView = false, $allowUpdate = false, $allowDelete = false) {
            $this->group = $group;
            $this->allowCreate = $allowCreate;
            $this->allowView = $allowView;
            $this->allowUpdate = $allowUpdate;
            $this->allowDelete = $allowDelete;
        }
    }

