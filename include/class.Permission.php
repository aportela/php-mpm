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

		public function __construct () { }

        public function __destruct() { }

        public function set($group, $allowCreate = false, $allowView = false, $allowUpdate = false, $allowDelete = false) {
            $this->group = $group;
            $this->allowCreate = $allowCreate;
            $this->allowView = $allowView;
            $this->allowUpdate = $allowUpdate;
            $this->allowDelete = $allowDelete;
        }
    }

