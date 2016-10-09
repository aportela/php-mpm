<?php
    namespace PHP_MPM;

    /**
    *   attribute type definitions
    */
    abstract class AttributeType {
        const NONE = 0;             // default (_DON'T USE THIS_)
        const TEXT_SHORT = 1;       // short string (0-255 chars)
        const TEXT_LONG = 2;        // long string (memo)
        const NUMBER_INTEGER = 3;   // integer number
        const NUMBER_DECIMAL = 4;   // decimal number
        const DATE = 5;             // date 
        const TIME = 6;             // time
        const DATETIME = 7;         // date & time
    }

    /**
    *   attribute class
    */
    class Attribute {
        private $id;
        private $name;
        private $description;
        private $type;

		public function __construct () { }

        public function __destruct() { }

        public function set(string $id = "", string $name = "", string $description = "", int $type = AttributeType::NONE) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->type = $type;            
        }

        /**
        *   search (list) groups
        */
        public static function search($page, $resultsPage) {
            if (! User::isAuthenticated()) {
                throw new MPMAuthSessionRequiredException("");
            } else {
                // TODO: pagination & filtering
                // TODO: type is returned as string (not integer)
                return(Database::execWithResult(" SELECT id, name, description, type FROM [ATTRIBUTE] ORDER BY name ", array()));
            }
        }
        
    }
?>