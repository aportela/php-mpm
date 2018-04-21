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
    }

?>