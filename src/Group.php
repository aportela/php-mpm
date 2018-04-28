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