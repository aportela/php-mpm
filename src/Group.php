<?php

    declare(strict_types=1);

    namespace PHP_MPM;

    class Group {

        public $id;
        public $name;
        public $description;

	    public function __construct ($obj = null) {
            if ($obj) {
                $this->id = isset($obj["id"]) ? $obj["id"]: null;
                $this->name = isset($obj["name"]) ? $obj["name"]: null;
                $this->description = isset($obj["description"]) ? $obj["description"]: null;
            }
        }

        public function __destruct() { }

        /**
         * get group data
         * id must be set
         *
         * @param \PHP_MPM\Database\DB $dbh database handler
         */
        public function get(\PHP_MPM\Database\DB $dbh) {
            $results = null;
            if (! empty($this->id)) {
                $results = $dbh->query(" SELECT id, name, description FROM `GROUP` WHERE id = :id AND DELETED IS NULL ", array(
                    (new \PHP_MPM\Database\DBParam())->str(":id", $this->id)
                ));
                if (count($results) == 1) {
                    $this->id = $results[0]->id;
                    $this->name = $results[0]->name;
                    $this->description = $results[0]->description;
                    $this->userCount = $results[0]->userCount;
                } else {
                    throw new \PHP_MPM\Exception\NotFoundException("");
                }
            } else {
                throw new \PHP_MPM\Exception\InvalidParamsException("id");
            }
        }

        private function validate(): void {
        }

        /**
         * save new group
         */
        public function add(\PHP_MPM\Database\DB $dbh): bool {
            $this->validate();
            $params = array(
                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id),
                (new \PHP_MPM\Database\DBParam())->str(":name", $this->name),
                (new \PHP_MPM\Database\DBParam())->str(":description", $this->description),
                (new \PHP_MPM\Database\DBParam())->str(":creator", \PHP_MPM\UserSession::getUserId())
            );
            $success = false;
            try {
                $success = $dbh->execute(" INSERT INTO `GROUP` (id, name, description, creator, created, deleted) VALUES(:id, :name, :description, :creator, UTC_TIMESTAMP(3), NULL) ", $params);
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    throw new \PHP_MPM\Exception\ElementAlreadyExistsException("name");
                } else {
                    throw $e;
                }
            }
            return($success);
        }

        /**
         * save existent group
         */
        public function update(\PHP_MPM\Database\DB $dbh): void {
            $this->validate();
            $params = array(
                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id),
                (new \PHP_MPM\Database\DBParam())->str(":name", $this->name),
                (new \PHP_MPM\Database\DBParam())->str(":description", $this->description)
            );
            $query = " UPDATE `GROUP` SET name = :name, description = :description WHERE id = :id ";
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
         * delete (set deleted flag) group
         */
        public function delete(\PHP_MPM\Database\DB $dbh): void {
            // check existence
            $this->get($dbh);
            $params = array(
                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id)
            );
            $dbh->execute(" UPDATE `GROUP` SET deleted = UTC_TIMESTAMP(3) WHERE id = :id ", $params);
        }

        /**
         * search (list) groups
         */
        public static function search(\PHP_MPM\Database\DB $dbh, int $page = 1, int $resultsPage = 16, array $filter = array(), string $sortBy = "", string $sortOrder = "ASC") {
            $params = array();
            $whereCondition = "";
            if (isset($filter)) {
                $conditions = array();
                if (isset($filter["name"]) && ! empty($filter["name"])) {
                    $conditions[] = " G.name LIKE :name ";
                    $params[] = (new \PHP_MPM\Database\DBParam())->str(":name", "%" . $filter["name"] . "%");
                }
                if (isset($filter["description"]) && ! empty($filter["description"])) {
                    $conditions[] = " G.description LIKE :description ";
                    $params[] = (new \PHP_MPM\Database\DBParam())->str(":description", "%" . $filter["description"] . "%");
                }
                $whereCondition = count($conditions) > 0 ? " AND " .  implode(" AND ", $conditions) : "";
            }
            $queryCount = '
                SELECT
                    COUNT(G.id) AS total
                FROM `GROUP` G
                WHERE G.deleted IS NULL
                ' . $whereCondition . '
            ';
            $result = $dbh->query($queryCount, $params);
            $data = new \stdClass();
            $data->actualPage = $page;
            $data->resultsPage = $resultsPage;
            $data->totalResults = $result[0]->total;
            if ($resultsPage > 0) {
                $data->totalPages = ceil($data->totalResults / $resultsPage);
            } else {
                $data->totalPages = $data->totalResults > 0 ? 1: 0;
                $resultsPage = $data->totalResults;
            }
            if (! empty($sortBy)) {
                switch($sortBy) {
                    case "description":
                        $sqlOrder = " ORDER BY G.description ";
                    break;
                    case "created":
                        $sqlOrder = " ORDER BY G.created ";
                    break;
                    case "name":
                    default:
                        $sqlOrder = " ORDER BY G.name ";
                    break;
                }
            } else {
                $sqlOrder = " ORDER BY G.name ";
            }
            $query = sprintf('
                SELECT
                    G.id AS id,
                    G.name AS name,
                    G.description AS description,
                    0 AS userCount,
                    DATE_FORMAT(CONVERT_TZ(G.created, @@session.time_zone, "+00:00"), "%s") AS created
                FROM `GROUP` G
                WHERE G.deleted IS NULL
                %s
                %s
                %s
                LIMIT %d OFFSET %d
                ',
                \PHP_MPM\Database\DB::JSON_UTC_DATETIME_FORMAT,
                $whereCondition,
                $sqlOrder,
                $sortOrder == "DESC" ? "DESC": "ASC",
                $resultsPage,
                $resultsPage * ($page - 1)
            );
            $data->results = $dbh->query($query, $params);
            return($data);
        }
    }

?>