<?php
    namespace PHP_MPM;

    require_once "class.Database.php";

    /**
    *   error class
    */
    class Error {
        /**
        *   add error (exception) into database
        */
        public static function save($e) {
            $params = array();
            $param = new DatabaseParam();
            $param->str(":class", get_class($e));
            $params[] = $param;
            $param = new DatabaseParam();
            $param->int(":line", $e->getLine());
            $params[] = $param;
            $param = new DatabaseParam();
            $param->str(":filename", $e->getFile());
            $params[] = $param;
            $param = new DatabaseParam();
            $param->int(":code", intval($e->getCode()));
            $params[] = $param;
            $param = new DatabaseParam();
            $param->str(":trace", print_r($e->getTrace(), true));
            $params[] = $param;
            try {
                Database::execWithoutResult(" INSERT INTO ERROR (created, class, line, filename, code, trace) VALUES (CURRENT_TIMESTAMP, :class, :line, :filename, :code, :trace) ", $params);
            } catch (\Throwable $e) {
                // we do not want to throw (again) on error 
            }
        }
    }
?>