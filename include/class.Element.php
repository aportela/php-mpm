<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   element class
    */
    class Element {

        public $id;
        public $templateId;
        public $description;
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

        public function add() {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));                
            } else if (empty($this->templateId) || empty($this->description)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $t = new \PHP_MPM\Template();
                $t->id = $this->templateId;
                if (! $t->allowElementCreation()) {
                    throw new \PHP_MPM\MPMAccessDeniedException(print_r(get_object_vars($this), true));
                } else {
                    if (empty($this->id)) {
                        $this->id = \PHP_MPM\Utils::uuid();
                    }
                    $params = array();
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":id", $this->id);
                    $params[] = $param;                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":template_id", $this->templateId);
                    $params[] = $param;                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":description", $this->description);
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":creator", User::getSessionUserId());
                    $params[] = $param;
                    $db = \PHP_MPM\Database::getHandler(true);
                    $db->execWithoutResult(" INSERT INTO [ELEMENT] (id, template_id, description, created, creator) VALUES (:id, :template_id, :description, CURRENT_TIMESTAMP, :creator) ", $params);
                }
            }
        }
    }
?>