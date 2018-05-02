<?php

    declare(strict_types=1);

    namespace PHP_MPM;

    class Attribute {

        public $id;
        public $name;
        public $description;
        public $type;

	    public function __construct ($obj = null) {
            if ($obj) {
                $this->id = isset($obj["id"]) ? $obj["id"]: null;
                $this->name = isset($obj["name"]) ? $obj["name"]: null;
                $this->description = isset($obj["description"]) ? $obj["description"]: null;
                $this->type = isset($obj["type"]) ? intval($obj["type"]): 0;
            }
        }

        public function __destruct() { }

        private function validate(): void {
        }

        /**
         * get attribute data
         * id must be set
         *
         * @param \PHP_MPM\Database\DB $dbh database handler
         */
        public function get(\PHP_MPM\Database\DB $dbh) {
            $results = null;
            if (! empty($this->id)) {
                $results = $dbh->query("
                    SELECT A.id, A.name, A.description, A.type
                    FROM ATTRIBUTE A
                    WHERE A.id = :id AND A.deleted IS NULL ",
                    array(
                        (new \PHP_MPM\Database\DBParam())->str(":id", $this->id)
                    )
                );
                if (count($results) == 1) {
                    $this->id = $results[0]->id;
                    $this->name = $results[0]->name;
                    $this->description = $results[0]->description;
                    $this->type = intval($results[0]->type);
                } else {
                    throw new \PHP_MPM\Exception\NotFoundException("");
                }
            } else {
                throw new \PHP_MPM\Exception\InvalidParamsException("id");
            }
        }

        /**
         * save new attribute
         */
        public function add(\PHP_MPM\Database\DB $dbh): void {
            $this->validate();
            $params = array(
                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id),
                (new \PHP_MPM\Database\DBParam())->str(":name", $this->name),
                (new \PHP_MPM\Database\DBParam())->str(":description", $this->description),
                (new \PHP_MPM\Database\DBParam())->int(":type", $this->type),
                (new \PHP_MPM\Database\DBParam())->str(":creator", \PHP_MPM\UserSession::getUserId())
            );
            try {
                $dbh->execute(" INSERT INTO ATTRIBUTE (id, name, description, type, creator, created, deleted) VALUES(:id, :name, :description, :type, :creator, UTC_TIMESTAMP(3), NULL) ", $params);
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    throw new \PHP_MPM\Exception\ElementAlreadyExistsException("name");
                } else {
                    throw $e;
                }
            }
        }

        /**
         * save existent attribute
         */
        public function update(\PHP_MPM\Database\DB $dbh): void {
            $this->validate();
            $params = array(
                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id),
                (new \PHP_MPM\Database\DBParam())->str(":name", $this->name),
                (new \PHP_MPM\Database\DBParam())->str(":description", $this->description)
            );
            $query = " UPDATE ATTRIBUTE SET name = :name, description = :description WHERE id = :id ";
            try {
                $dbh->execute($query, $params);
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    throw new \PHP_MPM\Exception\ElementAlreadyExistsException("name");
                } else {
                    throw $e;
                }
            }
        }

        /**
         * delete (set deleted flag) attribute
         */
        public function delete(\PHP_MPM\Database\DB $dbh): void {
            // check existence
            $this->get($dbh);
            $params = array(
                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id)
            );
            $dbh->execute(" UPDATE ATTRIBUTE SET deleted = UTC_TIMESTAMP(3) WHERE id = :id ", $params);
        }

        /**
         * search (list) attributes
         */
        public static function search(\PHP_MPM\Database\DB $dbh, int $page = 1, int $resultsPage = 16, array $filter = array(), string $sortBy = "", string $sortOrder = "ASC") {
            $params = array();
            $whereCondition = "";
            if (isset($filter)) {
                $conditions = array();
                if (isset($filter["name"]) && ! empty($filter["name"])) {
                    $conditions[] = " A.name LIKE :name ";
                    $params[] = (new \PHP_MPM\Database\DBParam())->str(":name", "%" . $filter["name"] . "%");
                }
                if (isset($filter["description"]) && ! empty($filter["description"])) {
                    $conditions[] = " A.description LIKE :description ";
                    $params[] = (new \PHP_MPM\Database\DBParam())->str(":description", "%" . $filter["description"] . "%");
                }
                if (isset($filter["typeId"]) && ! empty($filter["typeId"])) {
                    $conditions[] = " A.type = :type_id ";
                    $params[] = (new \PHP_MPM\Database\DBParam())->int(":type_id", intval($filter["typeId"]));
                }
                if (isset($filter["typeName"]) && ! empty($filter["typeName"])) {
                    $conditions[] = " AT.name = :type_name ";
                    $params[] = (new \PHP_MPM\Database\DBParam())->str(":type_name", $filter["typeName"]);
                }
                $whereCondition = count($conditions) > 0 ? " AND " .  implode(" AND ", $conditions) : "";
            }
            $queryCount = '
                SELECT
                    COUNT(A.id) AS total
                FROM ATTRIBUTE A
                WHERE A.deleted IS NULL
                ' . $whereCondition . '
            ';
            $result = $dbh->query($queryCount, $params);
            $data = new \PHP_MPM\SearchResult($page, $resultsPage, intval($result[0]->total));
            if (! empty($sortBy)) {
                switch($sortBy) {
                    case "description":
                        $sqlOrder = " ORDER BY A.description ";
                    break;
                    case "created":
                        $sqlOrder = " ORDER BY A.created ";
                    break;
                    case "typeId":
                        $sqlOrder = " ORDER BY A.type ";
                    break;
                    case "typeName":
                        $sqlOrder = " ORDER BY AT.name ";
                    break;
                    case "name":
                    default:
                        $sqlOrder = " ORDER BY A.name ";
                    break;
                }
            } else {
                $sqlOrder = " ORDER BY A.name ";
            }
            $query = sprintf('
                SELECT
                    A.id AS id,
                    A.name AS name,
                    A.description AS description,
                    A.type AS typeId,
                    AT.name AS typeName,
                    DATE_FORMAT(CONVERT_TZ(A.created, @@session.time_zone, "+00:00"), "%s") AS created
                FROM ATTRIBUTE A
                LEFT JOIN ATTRIBUTE_TYPE AT ON AT.id = A.type
                WHERE A.deleted IS NULL
                %s
                %s
                %s
                LIMIT %d OFFSET %d
                ',
                \PHP_MPM\Database\DB::JSON_UTC_DATETIME_FORMAT,
                $whereCondition,
                $sqlOrder,
                $sortOrder == "DESC" ? "DESC": "ASC",
                $data->resultsPage,
                $data->getSQLPageOffset()
            );
            $data->results = $dbh->query($query, $params);
            return($data);
        }

        /**
         * return attribute types collection (id => name)
         */
        public static function getTypes(\PHP_MPM\Database\DB $dbh) {
            return($dbh->query("
                SELECT
                    id,
                    name
                FROM ATTRIBUTE_TYPE
                ORDER BY name
                "
                ,
                array()
            ));
        }
    }
?>