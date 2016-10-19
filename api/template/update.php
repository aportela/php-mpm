<?php
    /**
    *   /api/template/update.php
    *   description: update template
    *
    *   request method: POST
    *
    *   @param string id 
    *   @param string name
    *   @param string description
    *   @param array permissions
    */
    namespace PHP_MPM;

    require_once "../../include/configuration.php";
    require_once "../../include/class.Group.php";
    require_once "../../include/class.CustomExceptions.php";
    require_once "../../include/class.Error.php";

    ob_start();
    
    session_start();

    $result = array("success" => false);
    try {
        $permissions = array();
        if (isset($_POST["group_permissions"]) && is_array($_POST["group_permissions"])) {
            $t = count($_POST["group_permissions"]);
            for ($i = 0; $i < $t; $i++) {
                $permission = new \stdClass;
                $permission->group = new Group(); 
                $permission->group->id = $_POST["group_permissions"][$i];
                $permission->allowCreate = $_POST["create_flag_permissions"][$i] == "1";
                $permission->allowView = $_POST["view_flag_permissions"][$i] == "1";
                $permission->allowUpdate = $_POST["update_flag_permissions"][$i] == "1";
                $permission->allowDelete = $_POST["delete_flag_permissions"][$i] == "1";
                $permissions[] = $permission;
            }
        }
        $t = new \PHP_MPM\Template();         
        $t->set(
            isset($_POST["id"]) ? $_POST["id"]: "", 
            isset($_POST["name"]) ? $_POST["name"]: "", 
            isset($_POST["description"]) ? $_POST["description"]: "",
            $permissions
        );
        $t->update();
        $result["success"] = true;
        ob_clean();
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
    } catch (\PHP_MPM\MPMAuthSessionRequiredException $e) {
        Error::save($e);
        ob_clean();
        header("HTTP/1.0 403 Forbidden", 403, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }        
    } catch (\PHP_MPM\MPMAdminPrivilegesRequiredException $e) {
        Error::save($e);
        ob_clean();
        header("HTTP/1.0 403 Forbidden", 403, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }                
    } catch (\PDOException $e) {
        Error::save($e);
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