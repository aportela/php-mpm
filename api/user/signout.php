<?php
    /**
    *   /api/user/signout.php
    *   description: finish (and destroy) authenticated session
    *
    *   request method: POST
    *
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
        $u = new User();                 
        $u->signout();
        $result["success"] = true; 
        ob_clean();
        header("HTTP/1.1 200 OK", 200, true);
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