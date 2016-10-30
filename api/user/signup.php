<?php
    /**
    *   /api/user/signup.php
    *   description: register new user
    *
    *   request method: POST
    *   format: json
    *
    *   @param string email
    *   @param string password
    *   @param string name
    */
    namespace PHP_MPM;

    require_once "../../include/configuration.php";
    require_once "../../include/class.User.php";
    require_once "../../include/class.CustomExceptions.php";
    require_once "../../include/class.Error.php";

    ob_start();
      
    session_start();

    $result = array("success" => false);
    try {
        $params = \PHP_MPM\Utils::getRequestParamsFromJSON();
        $u = new \PHP_MPM\User();
        $u->email = isset($params["email"]) ? $params["email"]: "";         
        $u->password = isset($params["password"]) ? $params["password"]: "";                 
        $u->name = isset($params["name"]) ? $params["name"]: "";
        $u->signup();
        $result["success"] = true;
        ob_clean();
    } catch (\PHP_MPM\MPMInvalidParamsException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.1 400 Bad Request", 400, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }
    } catch (\PHP_MPM\MPMAlreadyExistsException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.1 409 Conflict", 409, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }
    } catch (\PHP_MPM\MPMAccessDeniedException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.1 403 Forbidden", 403, true);
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