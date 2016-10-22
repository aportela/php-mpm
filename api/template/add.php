<?php
    /**
    *   /api/template/add.php
    *   description: add new template
    *
    *   request method: POST
    *   format: json
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
        $params = \PHP_MPM\Utils::getRequestParamsFromJSON();
        $permissions = array();
        if (isset($params["permissions"])) {
            if (is_array($params["permissions"])) {
                $t = count($params["permissions"]);
                for ($i = 0; $i < $t; $i++) {
                    if (isset($params["permissions"][$i]["group"]) && isset($params["permissions"][$i]["flags"])) {
                        $permission = new \PHP_MPM\TemplatePermission();
                        $g = new \PHP_MPM\Group(); 
                        $g->id = isset($params["permissions"][$i]["group"]["id"]) ? $params["permissions"][$i]["group"]["id"]: "";
                        $permission->set(
                            $g, 
                            isset($params["permissions"][$i]["flags"]["allowCreate"]) ? boolval($params["permissions"][$i]["flags"]["allowCreate"]): false,
                            isset($params["permissions"][$i]["flags"]["allowView"]) ? boolval($params["permissions"][$i]["flags"]["allowView"]): false,
                            isset($params["permissions"][$i]["flags"]["allowUpdate"]) ? boolval($params["permissions"][$i]["flags"]["allowUpdate"]): false,
                            isset($params["permissions"][$i]["flags"]["allowDelete"]) ? boolval($params["permissions"][$i]["flags"]["allowDelete"]): false
                        );
                        $permissions[] = $permission;
                    } else {
                        throw new \PHP_MPM\MPMInvalidParamsException("permissions");
                    }
                }
            } else {
                throw new \PHP_MPM\MPMInvalidParamsException("permissions");
            }
        }
        $attributes = array();
        if (isset($params["attributes"])) {
            if (is_array($params["attributes"])) {
                $t = count($params["attributes"]);
                for ($i = 0; $i < $t; $i++) {
                    if (isset($params["attributes"][$i]["attribute"])) {
                        $templateAttribute = new \PHP_MPM\TemplateAttribute();
                        $a = new \PHP_MPM\Attribute(); 
                        $a->id = isset($params["attributes"][$i]["attribute"]["id"]) ? $params["attributes"][$i]["attribute"]["id"]: "";
                        $templateAttribute->set(
                            "",
                            $a,
                            isset($params["attributes"][$i]["label"]) ? $params["attributes"][$i]["label"]: "",
                            isset($params["attributes"][$i]["required"]) ? boolval($params["attributes"][$i]["required"]): false,
                            ""
                        );
                        $attributes[] = $templateAttribute;
                    } else {
                        throw new \PHP_MPM\MPMInvalidParamsException("attributes");        
                    }
                }
            } else {
                throw new \PHP_MPM\MPMInvalidParamsException("attributes");
            }
        }        
        $t = new \PHP_MPM\Template();
        $t->set(
            isset($params["id"]) ? $params["id"]: "", 
            isset($params["name"]) ? $params["name"]: "", 
            isset($params["description"]) ? $params["description"]: "",
            $permissions,
            $attributes,
            isset($params["htmlForm"]) ? $params["htmlForm"]: ""
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