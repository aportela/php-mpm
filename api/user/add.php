<?php
    /**
    *   /api/user/add.php
    *   description: add new user
    *
    *   request method: POST
    *
    *   @param string id 
    *   @param string email
    *   @param string password
    */
    namespace PHP_MPM;

    require_once "../../include/configuration.php";
    require_once "../../include/class.User.php";
    require_once "../../include/class.CustomExceptions.php";

    ob_start();    
    $result = array("success" => false);
    try {
        $u = new User();         
        $u->set(
            isset($_POST["id"]) ? $_POST["id"]: "", 
            isset($_POST["email"]) ? $_POST["email"]: "", 
            isset($_POST["password"]) ? $_POST["password"]: ""
        );
        $u->add();
        $result["success"] = true;
        ob_clean();
    } catch (MPMInvalidParamsException $e) {
        ob_clean();
        header("HTTP/1.1 400 Bad Request", 400, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }
    } catch (MPMAlreadyExistsException $e) {
        ob_clean();
        header("HTTP/1.1 409 Conflict", 409, true);
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