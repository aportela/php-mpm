<?php

    declare(strict_types=1);

    namespace PHP_MPM;

    class User {

        public $id;
        public $email;
        public $name;
        public $password;
        public $passwordHash;
        public $accountType;

	    public function __construct ($obj = null) {
            if ($obj) {
                $this->id = isset($obj["id"]) ? $obj["id"]: null;
                $this->email = isset($obj["email"]) ? $obj["email"]: null;
                $this->name = isset($obj["name"]) ? $obj["name"]: null;
                $this->password = isset($obj["password"]) ? $obj["password"]: null;
                $this->accountType = isset($obj["accountType"]) ? $obj["accountType"]: false;
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
                $results = $dbh->query(" SELECT id, email, password_hash AS passwordHash, name, account_type AS accountType FROM USER WHERE id = :id AND DELETED IS NULL ", array(
                    (new \PHP_MPM\Database\DBParam())->str(":id", $this->id)
                ));
            } else if (! empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $results = $dbh->query(" SELECT id, email, password_hash AS passwordHash, name, account_type AS accountType FROM USER WHERE email = :email AND DELETED IS NULL  ", array(
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
                $this->accountType = $results[0]->accountType;
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
                    \PHP_MPM\UserSession::set($this->id, $this->email, $this->name, $this->accountType);
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
        public static function signOut(): void {
            \PHP_MPM\UserSession::clear();
        }

        private function validate(): void {
        }

        /**
         * save new user
         */
        public function add(\PHP_MPM\Database\DB $dbh): bool {
            $this->validate();
            if (! empty($this->password)) {
                try {
                    // get user data with this email
                    $u = new \PHP_MPM\User();
                    $u->email = $this->email;
                    $u->get($dbh);
                    // another registered user has this email -> deny creation
                    throw new \PHP_MPM\Exception\ElementAlreadyExistsException("email");
                } catch (\PHP_MPM\Exception\NotFoundException $e) {
                    // no user found with this email -> allow create
                }
                $params = array(
                    (new \PHP_MPM\Database\DBParam())->str(":id", $this->id),
                    (new \PHP_MPM\Database\DBParam())->str(":email", mb_strtolower($this->email)),
                    (new \PHP_MPM\Database\DBParam())->str(":password_hash", $this->passwordHash($this->password)),
                    (new \PHP_MPM\Database\DBParam())->str(":name", $this->name),
                    (new \PHP_MPM\Database\DBParam())->str(":account_type", $this->accountType),
                    (new \PHP_MPM\Database\DBParam())->str(":creator", \PHP_MPM\UserSession::getUserId())
                );
                $success = false;
                try {
                    $success = $dbh->execute(" INSERT INTO USER (id, email, password_hash, name, account_type, creator, created, deleted) VALUES(:id, :email, :password_hash, :name, :account_type, :creator, UTC_TIMESTAMP(3), NULL) ", $params);
                } catch (\PDOException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        throw new \PHP_MPM\Exception\ElementAlreadyExistsException("email");
                    } else {
                        throw $e;
                    }
                }
                return($success);
            } else {
                throw new \PHP_MPM\Exception\InvalidParamsException("password");
            }
        }

        /**
         * save existent user
         */
        public function update(\PHP_MPM\Database\DB $dbh): void {
            $this->validate();
            try {
                // get user data with this email
                $u = new \PHP_MPM\User();
                $u->email = $this->email;
                $u->get($dbh);
                // if same user, allow update (email not changed)
                if ($u->id != $this->id) {
                    throw new \PHP_MPM\Exception\ElementAlreadyExistsException("email");
                }
            } catch (\PHP_MPM\Exception\NotFoundException $e) {
                // no user found with this (changed) email -> allow update
            }
            $params = array(
                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id),
                (new \PHP_MPM\Database\DBParam())->str(":email", mb_strtolower($this->email)),
                (new \PHP_MPM\Database\DBParam())->str(":name", $this->name),
                (new \PHP_MPM\Database\DBParam())->str(":account_type", $this->accountType)
            );
            $query = " UPDATE USER SET email = :email, name = :name, account_type = :account_type WHERE id = :id ";
            if (! empty($email)) {
                $params[] = (new \PHP_MPM\Database\DBParam())->str(":password_hash", $this->passwordHash($this->password));
                $query = " UPDATE USER SET email = :email, name = :name, account_type = :account_type, password_hash = :password_hash WHERE id = :id ";
            }
            try {
                $dbh->execute($query, $params);
            } catch (\PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    throw new \PHP_MPM\Exception\ElementAlreadyExistsException("email");
                } else {
                    throw $e;
                }
            }
        }

        /**
         * delete (set deleted flag) user
         */
        public function delete(\PHP_MPM\Database\DB $dbh): void {
            // check existence
            $this->get($dbh);
            $params = array(
                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id)
            );
            $dbh->execute(" UPDATE USER SET deleted = UTC_TIMESTAMP(3) WHERE id = :id ", $params);
        }

        /**
         * search (list) users
         */
        public static function search(\PHP_MPM\Database\DB $dbh, int $page = 1, int $resultsPage = 16, array $filter = array(), string $sortBy = "", string $sortOrder = "ASC") {
            $params = array();
            $whereCondition = "";
            if (isset($filter)) {
                $conditions = array();
                if (isset($filter["accountType"]) && ! empty($filter["accountType"])) {
                    $conditions[] = " U.account_type = :account_type ";
                    $params[] = (new \PHP_MPM\Database\DBParam())->str(":account_type", $filter["accountType"]);
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
            $data = new \PHP_MPM\SearchResult($page, $resultsPage, intval($result[0]->total));
            if (! empty($sortBy)) {
                switch($sortBy) {
                    case "accountType":
                        $sqlOrder = " ORDER BY U.account_type ";
                    break;
                    case "email":
                        $sqlOrder = " ORDER BY U.email ";
                    break;
                    case "created":
                        $sqlOrder = " ORDER BY U.created ";
                    break;
                    case "name":
                    default:
                        $sqlOrder = " ORDER BY U.name ";
                    break;
                }
            } else {
                $sqlOrder = " ORDER BY U.name ";
            }
            $query = sprintf('
                SELECT
                    U.id AS id,
                    U.email AS email,
                    U.name AS name,
                    U.account_type AS accountType,
                    DATE_FORMAT(CONVERT_TZ(U.created, @@session.time_zone, "+00:00"), "%s") AS created
                FROM USER U
                WHERE U.deleted IS NULL
                %s
                %s
                %s
                %s
                ',
                \PHP_MPM\Database\DB::JSON_UTC_DATETIME_FORMAT,
                $whereCondition,
                $sqlOrder,
                $sortOrder == "DESC" ? "DESC": "ASC",
                $data->isPaginationEnabled() ? sprintf("LIMIT %d OFFSET %d", $data->resultsPage, $data->getSQLPageOffset()) : null
            );
            $data->results = $dbh->query($query, $params);
            return($data);
        }
    }

?>