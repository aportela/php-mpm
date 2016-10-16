<?php
    /**
    *   /api/user/search.php
    *   description: search (list) users
    *
    *   request method: POST
    *
    *   @param int page 
    *   @param int resultsPage
    *   @param string text 
    */
    namespace PHP_MPM;

    require_once "../../include/configuration.php";
    require_once "../../include/class.User.php";
    require_once "../../include/class.CustomExceptions.php";
    require_once "../../include/class.Error.php";

    ob_start();

    session_start();

    $result = array("success" => false, "data" => null);
    try {
        $result["data"] = \PHP_MPM\User::search(
            isset($_POST["page"]) ? $_POST["page"]: 1,
            isset($_POST["resultsPage"]) ? $_POST["resultsPage"]: 16,
            isset($_POST["text"]) ? $_POST["text"]: ""
        );
        $result["success"] = true;
        ob_clean();
        header("HTTP/1.1 200 OK", 200, true);
    } catch (\PHP_MPM\MPMInvalidParamsException $e) {
        Error::save($e);
        ob_clean();
        header("HTTP/1.1 400 Bad Request", 400, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }
    } catch (\PHP_MPM\MPMNotFoundException $e) {
        Error::save($e);
        ob_clean();
        header("HTTP/1.0 404 Not Found", 404, true);
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