<?php
    /**
    *   /api/error/search.php
    *   description: search (list) errors
    *
    *   request method: POST
    *   format: json
    *
    *   @param int page 
    *   @param int resultsPage 
    */
    namespace PHP_MPM;

    require_once "../../include/configuration.php";
    require_once "../../include/class.CustomExceptions.php";
    require_once "../../include/class.Error.php";

    ob_start();

    session_start();

    $result = array("success" => false, "data" => null);
    try {
        $params = \PHP_MPM\Utils::getRequestParamsFromJSON();
        $result["data"] = \PHP_MPM\Error::search(
            isset($params["page"]) ? $params["page"]: 1,
            isset($params["resultsPage"]) ? $params["resultsPage"]: 16
        );
        $result["success"] = true;
        ob_clean();
        header("HTTP/1.1 200 OK", 200, true);
    } catch (\PHP_MPM\MPMInvalidParamsException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.1 400 Bad Request", 400, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }
    } catch (\PHP_MPM\MPMNotFoundException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.0 404 Not Found", 404, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }
    } catch (\PDOException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.1 500 Internal Server Error", 500, true);        
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }    
    } catch (\Throwable $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.1 500 Internal Server Error", 500, true);        
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }
    } finally {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($result);
        ob_end_flush();
    }
?>