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
            $param->str(":message", $e->getMessage());
            $params[] = $param;            
            $param = new \PHP_MPM\DatabaseParam();
            $trace = print_r($e->getTrace(), true);
            if (ENVIRONMENT_DEV && $trace != print_r(array(), true)) {
                $param->str(":trace", $trace);
            } else {
                $param->null(":trace");
            }
            $params[] = $param;
            $param = new \PHP_MPM\DatabaseParam();
            if (\PHP_MPM\User::isAuthenticated()) {
                $param->str(":user_id", \PHP_MPM\User::getSessionUserId());                
            } else {
                $param->null(":user_id");
            }
            $params[] = $param;
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":user_agent", \PHP_MPM\Utils::getBrowserUserAgent());
            $params[] = $param;            
            $param = new \PHP_MPM\DatabaseParam();
            $param->str(":user_remote_address", \PHP_MPM\Utils::getRemoteIpAddress());
            $params[] = $param;            
            try {
                \PHP_MPM\Database::execWithoutResult(" INSERT INTO ERROR (created, class, line, filename, code, message, trace, user_id, user_agent, user_remote_address) VALUES (CURRENT_TIMESTAMP, :class, :line, :filename, :code, :message, :trace, :user_id, :user_agent, :user_remote_address) ", $params);
            } catch (\Throwable $e) {
                // we do not want to throw (again) on error 
            }
        }

        /**
        *   search (list) errors
        */
        public static function search($page, $resultsPage) {
            if (! \PHP_MPM\User::isAuthenticated()) {
                throw new \PHP_MPM\MPMAuthSessionRequiredException(print_r(get_object_vars($this), true));
            } else if (! \PHP_MPM\User::isAuthenticatedAsAdmin()) {
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
                    $data->setResults(\PHP_MPM\Database::execWithResult(" SELECT datetime(E.created, 'localtime') AS creationDate, E.class, E.line, E.filename, E.code, E.message, E.trace, E.user_agent as userAgent, E.user_remote_address as remoteAddress, U.id AS userId, U.name AS userName FROM [ERROR] E LEFT JOIN [USER] U ON U.id = user_id ORDER BY E.created DESC LIMIT :start, :results_page ", $params));
                } else {
                    $data->setPager(0, 1, 0);
                    $data->setResults(\PHP_MPM\Database::execWithResult(" SELECT datetime(E.created, 'localtime') AS creationDate, E.class, E.line, E.filename, E.code, E.message, E.trace, E.user_agent as userAgent, E.user_remote_address as remoteAddress, U.id AS userId, U.name AS userName FROM [ERROR] E LEFT JOIN [USER] U ON U.id = user_id ORDER BY E.created DESC ", array()));
                }                                
                return($data);                
            }
        }        
    }
?>