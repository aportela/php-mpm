<?php
    namespace PHP_MPM;

    /**
    *   invalid parameters exception
    */
    class MPMInvalidParamsException extends \Exception {
        public function __construct($message = "", $code = 0, Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }

        public function __toString() {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        }
    }

    /**
    *   already exists exception
    */
    class MPMAlreadyExistsException extends \Exception {
        public function __construct($message = "", $code = 0, Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }

        public function __toString() {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        }
    }

    /**
    *   not found exception
    */
    class MPMNotFoundException extends \Exception {
        public function __construct($message = "", $code = 0, Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }

        public function __toString() {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        }
    }

    /**
    *   authenticated session required exception
    */
    class MPMAuthSessionRequiredException extends \Exception {
        public function __construct($message = "", $code = 0, Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }

        public function __toString() {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        }
    }

    /**
    *   administration privileges required exception
    */
    class MPMAdminPrivilegesRequiredException extends \Exception {
        public function __construct($message = "", $code = 0, Exception $previous = null) {
            parent::__construct($message, $code, $previous);
        }

        public function __toString() {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        }
    }

?>