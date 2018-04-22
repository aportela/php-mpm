<?php

    declare(strict_types=1);

    namespace PHP_MPM;

    class User {

        public $id;
        public $email;
        public $name;
        public $password;
        public $passwordHash;
        public $isAdmin;

	    public function __construct ($obj = null) {
            if ($obj) {
                $this->id = isset($obj["id"]) ? $obj["id"]: null;
                $this->email = isset($obj["email"]) ? $obj["email"]: null;
                $this->name = isset($obj["name"]) ? $obj["name"]: null;
                $this->password = isset($obj["password"]) ? $obj["password"]: null;
                $this->isAdmin = isset($obj["isAdmin"]) ? $obj["isAdmin"]: false;
            }
        }

        public function __destruct() { }

        /**
         * helper for hashing password (predefined algorithm)
         *
         * @param string $password string the password to hash
         */
        private function passwordHash(string $password = "") {
            return(password_hash($password, PASSWORD_BCRYPT, array('cost' => 12)));
        }

        /**
         * get user data
         * id || email must be set
         *
         * @param \PHP_MPM\Database\DB $dbh database handler
         */
        public function get(\PHP_MPM\Database\DB $dbh) {
            $results = null;
            if (! empty($this->id)) {
                $results = $dbh->query(" SELECT id, email, password_hash AS passwordHash, name, is_admin AS isAdmin FROM USER WHERE id = :id AND DELETED IS NULL ", array(
                    (new \PHP_MPM\Database\DBParam())->str(":id", $this->id)
                ));
            } else if (! empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $results = $dbh->query(" SELECT id, email, password_hash AS passwordHash, name, is_admin AS isAdmin FROM USER WHERE email = :email AND DELETED IS NULL  ", array(
                    (new \PHP_MPM\Database\DBParam())->str(":email", mb_strtolower($this->email))
                ));
            } else {
                throw new \PHP_MPM\Exception\InvalidParamsException("id,email");
            }
            if (count($results) == 1) {
                $this->id = $results[0]->id;
                $this->email = $results[0]->email;
                $this->passwordHash = $results[0]->passwordHash;
                $this->name = $results[0]->name;
                $this->isAdmin = $results[0]->isAdmin == 1;
            } else {
                throw new \PHP_MPM\Exception\NotFoundException("");
            }
        }

        /**
         * try sign in with specified credentials
         * id || email & password must be set
         *
         * @param \PHP_MPM\Database\DB $dbh database handler
         *
         * @return bool password match (true | false)
         */
        public function signIn(\PHP_MPM\Database\DB $dbh): bool {
            if (! empty($this->password)) {
                $this->get($dbh);
                if (password_verify($this->password, $this->passwordHash)) {
                    \PHP_MPM\UserSession::set($this->id, $this->email, $this->name, $this->isAdmin);
                    return(true);
                } else {
                    return(false);
                }
            } else {
                throw new \PHP_MPM\Exception\InvalidParamsException("password");
            }
        }

        /**
         * sign out
         */
        public static function signOut() {
            \PHP_MPM\UserSession::clear();
        }

        /**
         * search (list) users
         */
        public static function search(\PHP_MPM\Database\DB $dbh, int $page = 1, int $resultsPage = 16, array $filter = array(), string $order = "") {
            $params = array();
            $whereCondition = "";
            if (isset($filter)) {
                $conditions = array();
                if (isset($filter["isAdmin"])) {
                    $conditions[] = " U.is_addmin = :is_admin ";
                    $params[] = (new \PHP_MPM\Database\DBParam())->bool(":is_admin", $filter["isAdmin"]);
                }
                if (isset($filter["email"]) && ! empty($filter["email"])) {
                    $conditions[] = " U.email LIKE :email ";
                    $params[] = (new \PHP_MPM\Database\DBParam())->str(":email", "%" . $filter["email"] . "%");
                }
                if (isset($filter["name"]) && ! empty($filter["name"])) {
                    $conditions[] = " U.name LIKE :name ";
                    $params[] = (new \PHP_MPM\Database\DBParam())->str(":name", "%" . $filter["name"] . "%");
                }
                $whereCondition = count($conditions) > 0 ? " AND " .  implode(" AND ", $conditions) : "";
            }
            $queryCount = '
                SELECT
                    COUNT(U.id) AS total
                FROM USER U
                WHERE U.deleted IS NULL
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
            $sqlOrder = "";
            if (! empty($order) && $order == "random") {
                $sqlOrder = " ORDER BY RAND() ";
            } else {
                $sqlOrder = " ORDER BY U.name ASC ";
            }
            $query = sprintf('
                SELECT
                    U.id AS id,
                    U.email AS email,
                    U.name AS name,
                    U.is_admin AS isAdmin,
                    U.creator AS creator,
                    U.created AS created
                FROM USER U
                WHERE U.deleted IS NULL
                %s
                %s
                LIMIT %d OFFSET %d
                ',
                $whereCondition,
                $sqlOrder,
                $resultsPage,
                $resultsPage * ($page - 1)
            );
            $data->results = $dbh->query($query, $params);
            return($data);
        }
    }

?>