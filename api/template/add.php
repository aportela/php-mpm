<?php
    /**
    *   /api/template/add.php
    *   description: add new template
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
    require_once "../../include/class.User.php";
    require_once "../../include/class.Template.php";
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
                $permission = new \PHP_MPM\Permission();
                $g = new Group(); 
                $g->id = $_POST["group_permissions"][$i];
                $permission->set(
                    $g, 
                    $_POST["create_flag_permissions"][$i] == "1",
                    $_POST["view_flag_permissions"][$i] == "1",
                    $_POST["update_flag_permissions"][$i] == "1",
                    $_POST["delete_flag_permissions"][$i] == "1"
                );
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
        $t->add();
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