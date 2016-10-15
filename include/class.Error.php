<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   error class
    */
    class Error {
        /**
        *   add error (exception) into database
        */
        public static function save($e) {
            $params = array();
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":class", get_class($e));
            $params[] = $param;
            $param = new \PHP_MPM\DatabaseParam();
            $param->int(":line", $e->getLine());
            $params[] = $param;
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":filename", $e->getFile());
            $params[] = $param;
            $param = new \PHP_MPM\DatabaseParam();
            $param->int(":code", intval($e->getCode()));
            $params[] = $param;
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":trace", print_r($e->getTrace(), true));
            $params[] = $param;
            try {
                \PHP_MPM\Database::execWithoutResult(" INSERT INTO ERROR (created, class, line, filename, code, trace) VALUES (CURRENT_TIMESTAMP, :class, :line, :filename, :code, :trace) ", $params);
            } catch (\Throwable $e) {
                // we do not want to throw (again) on error 
            }
        }

        /**
        *   search (list) errors
        */
        public static function search($page, $resultsPage) {
            if (! User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! User::isAuthenticatedAsAdmin()) {
                throw new \PHP_MPM\MPMAdminPrivilegesRequiredException(print_r(get_object_vars($this), true));
            } else {
                $data = new \PHP_MPM\SearchResults();
                if ($resultsPage > 0) {
                    $totalResults = \PHP_MPM\Database::execScalar(" SELECT COUNT(E.created) FROM [ERROR] E ", array());
                    $data->setPager($totalResults, $page, $resultsPage);
                    $params = array();
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->int(":start", (($page - 1) * $resultsPage));
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->int(":results_page", $resultsPage);
                    $params[] = $param;                    
                    $data->setResults(\PHP_MPM\Database::execWithResult(" SELECT datetime(created, 'localtime') AS creationDate, class, line, filename, code, trace FROM [ERROR] ORDER BY created DESC LIMIT :start, :results_page ", $params));
                } else {
                    $data->setPager(0, 1, 0);
                    $data->setResults(\PHP_MPM\Database::execWithResult(" SELECT datetime(created, 'localtime') AS creationDate, class, line, filename, code, trace FROM [ERROR] ORDER BY created DESC ", array()));
                }                                
                return($data);                
            }
        }        
    }
?>