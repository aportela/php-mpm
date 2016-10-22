<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   permission class
    */
    class Permission {

        public $group;
        public $flags;

		public function __construct () {
            $this->group = new \PHP_MPM\Group();
            $this->flags = new \stdClass();
            $this->flags->allowCreate = false;
            $this->flags->allowView = false;
            $this->flags->allowUpdate = false;
            $this->flags->allowDelete = false;
        }

        public function __destruct() { }

        public function set($group, $allowCreate = false, $allowView = false, $allowUpdate = false, $allowDelete = false) {
            $this->group = $group;
            $this->flags->allowCreate = boolval($allowCreate);
            $this->flags->allowView = boolval($allowView);
            $this->flags->allowUpdate = boolval($allowUpdate);
            $this->flags->allowDelete = boolval($allowDelete);
        }
    }

