<?php
    /**
    *   /api/group/add.php
    *   description: add new group
    *
    *   request method: POST
    *   format: json
    *
    *   @param string id
    *   @param string name
    *   @param string description
    *   @param array users
    */
    namespace PHP_MPM;

    require_once "../../include/configuration.php";
    require_once "../../include/class.User.php";
    require_once "../../include/class.Group.php";
    require_once "../../include/class.CustomExceptions.php";
    require_once "../../include/class.Error.php";

    ob_start();
        
    session_start();

    $result = array("success" => false);
    try {
        $params = \PHP_MPM\Utils::getRequestParamsFromJSON();
        $users = array();
        if (isset($params["users"])) {
            if (is_array($params["users"])) {
                foreach($params["users"] as $user) {
                    $u = new \PHP_MPM\User();
                    $u->id = isset($user["id"]) ? $user["id"]: "";
                    $users[] = $u;
                }
            } else {
                throw new \PHP_MPM\MPMInvalidParamsException("users");
            }
        }
        $g = new \PHP_MPM\Group();
        $g->set(
            isset($params["id"]) ? $params["id"]: "", 
            isset($params["name"]) ? $params["name"]: "", 
            isset($params["description"]) ? $params["description"]: "",
            $users
        );
        $g->add();
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
    } catch (\PHP_MPM\MPMAuthSessionRequiredException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.0 403 Forbidden", 403, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }        
    } catch (\PHP_MPM\MPMAdminPrivilegesRequiredException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.0 403 Forbidden", 403, true);
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