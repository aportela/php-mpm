<?php
    /**
    *   /api/user/exists.php
    *   description: check user (email) existence
    *
    *   request method: POST
    *
    *   @param string email 
    */
    namespace PHP_MPM;

    require_once "../../include/configuration.php";
    require_once "../../include/class.User.php";
    require_once "../../include/class.CustomExceptions.php";
    require_once "../../include/class.Error.php";

    ob_start();
        
    session_start();

    $result = array("success" => false, "exists" => false);
    try {
        $u = new \PHP_MPM\User();         
        $u->set("", isset($_POST["email"]) ? $_POST["email"]: "", "", 0);
        $result["exists"] = $u->exists();
        ob_clean();
        if ($result["exists"]) {
            header("HTTP/1.1 200 OK", 200, true);
        } else {
            header("HTTP/1.0 404 Not Found", 404, true);
        }
        $result["success"] = true;
    } catch (\PHP_MPM\MPMInvalidParamsException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.1 400 Bad Request", 400, true);
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