<?php
    /**
    *   /api/attribute/search.php
    *   description: search (list) attributes
    *
    *   request method: POST
    *
    *   @param int page 
    *   @param int resultsPage 
    */
    namespace PHP_MPM;

    require_once "../../include/configuration.php";
    require_once "../../include/class.Attribute.php";
    require_once "../../include/class.CustomExceptions.php";
    require_once "../../include/class.Error.php";

    ob_start();

    session_start();

    $result = array("success" => false, "results" => array());
    try {
        $result["results"] = Attribute::search(1, 16);
        $result["success"] = true;
        ob_clean();
        header("HTTP/1.1 200 OK", 200, true);
    } catch (MPMInvalidParamsException $e) {
        Error::save($e);
        ob_clean();
        header("HTTP/1.1 400 Bad Request", 400, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }
    } catch (MPMAuthSessionRequiredException $e) {
        Error::save($e);
        ob_clean();
        header("HTTP/1.0 403 Forbidden", 403, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }
    } catch (\PDOException $e) {
        ob_clean();
        header("HTTP/1.1 500 Internal Server Error", 500, true);        
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }    
    } catch (\Throwable $e) {
        Error::save($e);
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