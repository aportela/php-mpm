<?php
    namespace PHP_MPM;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "configuration.php";

    /**
    *   permission class
    */
    class TemplatePermission extends Permission {

        /**
        *   add template permission
        */
        public function add($templateId) {
            if (empty($templateId) || empty($this->group->id)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $g = new \PHP_MPM\Group();
                $g->id = $this->group->id;
                if (! $g->exists()) {
                    throw new \PHP_MPM\MPMNotFoundException(print_r(get_object_vars($this), true));
                } else {
                    $params = array();
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":template_id", $templateId);
                    $params[] = $param;                            
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->str(":group_id", $this->group->id);
                    $params[] = $param;                                
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->bool(":allow_create", $this->flags->allowCreate);
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->bool(":allow_view", $this->flags->allowView);
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->bool(":allow_update", $this->flags->allowUpdate);
                    $params[] = $param;
                    $param = new \PHP_MPM\DatabaseParam();
                    $param->bool(":allow_delete", $this->flags->allowDelete);
                    $params[] = $param;
                    \PHP_MPM\Database::execWithoutResult(" INSERT INTO [TEMPLATE_PERMISSION] (template_id, group_id, allow_create, allow_view, allow_update, allow_delete) VALUES (:template_id, :group_id, :allow_create, :allow_view, :allow_update, :allow_delete) ", $params);
                }
            }                        
        }

        /**
        *   update template permission
        */
        public function update($templateId) {
            if (empty($templateId) || empty($this->group->id)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->bool(":allow_create", $this->flags->allowCreate);
                $params[] = $param;
                $param = new \PHP_MPM\DatabaseParam();
                $param->bool(":allow_view", $this->flags->allowView);
                $params[] = $param;
                $param = new \PHP_MPM\DatabaseParam();
                $param->bool(":allow_update", $this->flags->allowUpdate);
                $params[] = $param;
                $param = new \PHP_MPM\DatabaseParam();
                $param->bool(":allow_delete", $this->flags->allowDelete);
                $params[] = $param;                
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":template_id", $templateId);
                $params[] = $param;                            
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":group_id", $this->group->id);
                $params[] = $param;                                
                \PHP_MPM\Database::execWithoutResult(" UPDATE [TEMPLATE_PERMISSION] SET allow_create = :allow_create, allow_view = :allow_view, allow_update = :allow_update, allow_delete = :allow_delete WHERE template_id = :template_id AND group_id = :group_id ", $params);
            }                                    
        }

        /**
        *   delete template permission
        */
        public function delete($templateId) {
            if (empty($templateId) || empty($this->group->id)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":template_id", $templateId);
                $params[] = $param;                            
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":group_id", $this->group->id);
                $params[] = $param;                                
                \PHP_MPM\Database::execWithoutResult(" DELETE FROM [TEMPLATE_PERMISSION] WHERE template_id = :template_id AND group_id = :group_id ", $params);
            }                                    
        }

        /**
        *   delete all template permissions
        */
        public static function deleteAll($templateId) {
            if (empty($templateId)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $params = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":template_id", $templateId);
                $params[] = $param;                            
                \PHP_MPM\Database::execWithoutResult(" DELETE FROM [TEMPLATE_PERMISSION] WHERE template_id = :template_id ", $params);
            }                                    
        } 

        /**
        *   get template permissions
        */
        public static function getPermissions($templateId) {
            if (empty($templateId)) {
                throw new \PHP_MPM\MPMInvalidParamsException(print_r(get_object_vars($this), true));
            } else {
                $permissions = array();
                $param = new \PHP_MPM\DatabaseParam();
                $param->str(":template_id", $templateId);
                $results = \PHP_MPM\Database::execWithResult(" SELECT TP.group_id AS groupId, G.name AS groupName, G.description AS groupDescription, TP.allow_create AS allowCreate, TP.allow_update AS allowUpdate, TP.allow_view AS allowView, TP.allow_delete AS allowDelete FROM [TEMPLATE_PERMISSION] TP LEFT JOIN [GROUP] G ON G.id = TP.group_id WHERE TP.template_id = :template_id ", array($param));
                foreach($results as $result) {
                    $permission = new \PHP_MPM\Permission();
                    $g = new \PHP_MPM\Group();
                    $g->id = $result->groupId;
                    $g->name = $result->groupName;
                    $g->description = $result->groupDescription;
                    $permission->set(
                        $g,
                        $result->allowCreate,
                        $result->allowView,
                        $result->allowUpdate,
                        $result->allowDelete
                    );
                    $permissions[] = $permission;
                }                 
                return($permissions);
            }
        }
    }

