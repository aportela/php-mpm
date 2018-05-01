<?php

    declare(strict_types=1);

    namespace PHP_MPM;

    class Group {

        public $id;
        public $name;
        public $description;
        public $users;

	    public function __construct ($obj = null) {
            $this->users = array();
            if ($obj) {
                $this->id = isset($obj["id"]) ? $obj["id"]: null;
                $this->name = isset($obj["name"]) ? $obj["name"]: null;
                $this->description = isset($obj["description"]) ? $obj["description"]: null;
                if (isset($obj["users"])) {
                    foreach($obj["users"] as $user) {
                        array_push($this->users, new \PHP_MPM\User($user));
                    }
                }
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
                $results = $dbh->query("
                    SELECT G.id, G.name, G.description
                    FROM `GROUP` G
                    WHERE G.id = :id AND G.deleted IS NULL ",
                    array(
                    (new \PHP_MPM\Database\DBParam())->str(":id", $this->id)
                    )
                );
                if (count($results) == 1) {
                    $this->id = $results[0]->id;
                    $this->name = $results[0]->name;
                    $this->description = $results[0]->description;
                    $this->getUsers($dbh);
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
         * save group users
         */
        private function setUsers(\PHP_MPM\Database\DB $dbh) {
            $dbh->execute(
                " DELETE FROM USER_GROUP WHERE group_id = :group_id ",
                array(
                    (new \PHP_MPM\Database\DBParam())->str(":group_id", $this->id),
                )
            );
            foreach($this->users as $user) {
                $dbh->execute(
                    " INSERT INTO USER_GROUP (user_id, group_id) VALUES (:user_id, :group_id) ",
                    array(
                        (new \PHP_MPM\Database\DBParam())->str(":user_id", $user->id),
                        (new \PHP_MPM\Database\DBParam())->str(":group_id", $this->id)
                    )
                );
            }
        }

        /**
         * get group users
         */
        private function getUsers(\PHP_MPM\Database\DB $dbh) {
            $this->users = $dbh->query(
                "
                    SELECT U.id, U.name
                    FROM USER_GROUP UG
                    LEFT JOIN USER U ON U.id = UG.user_id
                    WHERE group_id = :group_id
                    AND U.deleted IS NULL
                    ORDER BY U.name
                ",
                array(
                    (new \PHP_MPM\Database\DBParam())->str(":group_id", $this->id)
                )
            );
        }

        /**
         * save new group
         */
        public function add(\PHP_MPM\Database\DB $dbh): void {
            $this->validate();
            $params = array(
                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id),
                (new \PHP_MPM\Database\DBParam())->str(":name", $this->name),
                (new \PHP_MPM\Database\DBParam())->str(":description", $this->description),
                (new \PHP_MPM\Database\DBParam())->str(":creator", \PHP_MPM\UserSession::getUserId())
            );
            try {
                if ($dbh->execute(" INSERT INTO `GROUP` (id, name, description, creator, created, deleted) VALUES(:id, :name, :description, :creator, UTC_TIMESTAMP(3), NULL) ", $params)) {
                    $this->setUsers($dbh);
                }
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    throw new \PHP_MPM\Exception\ElementAlreadyExistsException("name");
                } else {
                    throw $e;
                }
            }
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
                $this->setUsers($dbh);
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
                    COALESCE(TMP.userCount, 0) AS userCount,
                    DATE_FORMAT(CONVERT_TZ(G.created, @@session.time_zone, "+00:00"), "%s") AS created
                FROM `GROUP` G
                LEFT JOIN (
                    SELECT COUNT(user_id) AS userCount, group_id
                    FROM USER_GROUP
                    GROUP BY group_id
                ) TMP ON TMP.group_id = G.id
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