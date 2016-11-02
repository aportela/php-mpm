<?php
    /**
    *   /api/attribute/update.php
    *   description: update attribute
    *
    *   request method: POST
    *   format: json
    *
    *   @param string id 
    *   @param string name
    *   @param string description
    *   @param array options (optional)
    */
    namespace PHP_MPM;

    require_once "../../include/configuration.php";
    require_once "../../include/class.User.php";
    require_once "../../include/class.Attribute.php";
    require_once "../../include/class.CustomExceptions.php";
    require_once "../../include/class.Error.php";

    ob_start();    

    session_start();
    
    $result = array("success" => false);
    try {
        $params = \PHP_MPM\Utils::getRequestParamsFromJSON();
        $a = new \PHP_MPM\Attribute();         
        $a->set(
            isset($params["id"]) ? $params["id"]: "", 
            isset($params["name"]) ? $params["name"]: "", 
            isset($params["description"]) ? $params["description"]: "",
            \PHP_MPM\AttributeType::NONE
        );
        if (isset($params["options"])) {            
            if (is_array($params["options"])) {
                $options = array();
                $t = count($params["attributes"]);
                for ($i = 0; $i < $t; $i++) {
                    $ao = new \PHP_MPM\AttributeOption();
                    $ao->set(
                        isset($params["options"][$i]["id"]) ? $params["options"][$i]["id"]: "",
                        isset($params["options"][$i]["name"]) ? $params["options"][$i]["name"]: "",
                        isset($params["options"][$i]["idx"]) ? intval($params["options"][$i]["idx"]): 0
                    );
                    $options[] = $ao;
                }
                $a->options = $options;
            } else {
                throw new \PHP_MPM\MPMInvalidParamsException("options");
            }            
        }
        $a->update();
        $result["success"] = true;
        ob_clean();
    } catch (\PHP_MPM\MPMInvalidParamsException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.1 400 Bad Request", 400, true);
        if (ENVIRONMENT_DEV && DEBUG) {
            $result["exception"] = print_r($e, true);
        }
    } catch (\PHP_MPM\MPMNotFoundException $e) {
        \PHP_MPM\Error::save($e);
        ob_clean();
        header("HTTP/1.0 404 Not Found", 404, true);
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