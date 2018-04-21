<?php

    declare(strict_types=1);

    namespace PHP_MPM;

    class User {

        public $id;
        public $email;
        public $nick;
        public $password;
        public $passwordHash;
        public $avatarUrl;

        /**
         * user constructor
         *
         * @param string $id
         * @param string $email
         * @param string $password
         */
	    public function __construct (string $id = "", string $email = "", string $password = "", string $nick = "", string $avatarUrl = "") {
            $this->id = $id;
            $this->email = $email;
            $this->password = $password;
            $this->nick = $nick;
            $this->avatarUrl = $avatarUrl;
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
         * add new user
         *
         * @param \PHP_MPM\Database\DB $dbh database handler
         */
        public function add(\PHP_MPM\Database\DB $dbh) {
            if (! empty($this->id) && mb_strlen($this->id) == 36) {
                if (! empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL) && mb_strlen($this->email) <= 255) {
                    if (! empty($this->nick) && mb_strlen($this->nick) <= 255) {
                        if (! empty($this->password)) {
                            $params = array(
                                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id),
                                (new \PHP_MPM\Database\DBParam())->str(":email", mb_strtolower($this->email)),
                                (new \PHP_MPM\Database\DBParam())->str(":nick", mb_strtolower($this->nick)),
                                (new \PHP_MPM\Database\DBParam())->str(":password_hash", $this->passwordHash($this->password)),
                            );
                            if (! empty($this->avatarUrl)) {
                                if (mb_strlen($this->avatarUrl) <= 2048) {
                                    $params[] = (new \PHP_MPM\Database\DBParam())->str(":avatar_url", $this->avatarUrl);
                                } else {
                                    throw new \PHP_MPM\Exception\InvalidParamsException("avatarUrl");
                                }
                            } else {
                                $params[] = (new \PHP_MPM\Database\DBParam())->null(":avatar_url");
                            }
                            return($dbh->execute("INSERT INTO USER (id, email, nick, password_hash, avatar_url) VALUES(:id, :email, :nick, :password_hash, :avatar_url)", $params));
                        } else {
                            throw new \PHP_MPM\Exception\InvalidParamsException("password");
                        }
                    } else {
                        throw new \PHP_MPM\Exception\InvalidParamsException("nick");
                    }
                } else {
                    throw new \PHP_MPM\Exception\InvalidParamsException("email");
                }
            } else {
                throw new \PHP_MPM\Exception\InvalidParamsException("id");
            }
        }

        /**
         * update user (email, nick, avatar url & hashed password fields)
         *
         * @param \PHP_MPM\Database\DB $dbh database handler
         */
        public function update(\PHP_MPM\Database\DB $dbh) {
            if (! empty($this->id) && mb_strlen($this->id) == 36) {
                if (! empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL) && mb_strlen($this->email) <= 255) {
                    if (! empty($this->nick) && mb_strlen($this->nick) <= 255) {
                        if (! empty($this->password)) {
                            $params = array(
                                (new \PHP_MPM\Database\DBParam())->str(":id", $this->id),
                                (new \PHP_MPM\Database\DBParam())->str(":email", mb_strtolower($this->email)),
                                (new \PHP_MPM\Database\DBParam())->str(":nick", mb_strtolower($this->nick)),
                                (new \PHP_MPM\Database\DBParam())->str(":password_hash", $this->passwordHash($this->password))
                            );
                            if (! empty($this->avatarUrl)) {
                                if (mb_strlen($this->avatarUrl) <= 2048) {
                                    $params[] = (new \PHP_MPM\Database\DBParam())->str(":avatar_url", $this->avatarUrl);
                                } else {
                                    throw new \PHP_MPM\Exception\InvalidParamsException("avatarUrl");
                                }
                            } else {
                                $params[] = (new \PHP_MPM\Database\DBParam())->null(":avatar_url");
                            }
                            return($dbh->execute(" UPDATE USER SET email = :email, nick = :nick, password_hash = :password_hash, avatar_url = :avatar_url WHERE id = :id ", $params));
                        } else {
                            throw new \PHP_MPM\Exception\InvalidParamsException("password");
                        }
                    } else {
                        throw new \PHP_MPM\Exception\InvalidParamsException("nick");
                    }
                } else {
                    throw new \PHP_MPM\Exception\InvalidParamsException("email");
                }
            } else {
                throw new \PHP_MPM\Exception\InvalidParamsException("id");
            }
        }

        /**
         * get user data by email
         *
         * @param \PHP_MPM\Database\DB $dbh database handler
         * @param string $email user email
         */
        public static function findByEmail(\PHP_MPM\Database\DB $dbh, $email = "") {
            $u = null;
            try {
                $u = new \PHP_MPM\User();
                $u->email = $email;
                $u->get($dbh);
            } catch (\PHP_MPM\Exception\NotFoundException $e) {
                $u = null;
            }
            return($u);
        }

        /**
         * get user data by nick
         *
         * @param \PHP_MPM\Database\DB $dbh database handler
         * @param string $nick user nick
         */
        public static function findByNick(\PHP_MPM\Database\DB $dbh, string $nick = "") {
            $u = null;
            try {
                $u = new \PHP_MPM\User();
                $u->nick = $nick;
                $u->get($dbh);
            } catch (\PHP_MPM\Exception\NotFoundException $e) {
                $u = null;
            }
            return($u);
        }

        /**
         * get user data (id, email, nick, avatar url & hashed password)
         * id || email must be set
         *
         * @param \PHP_MPM\Database\DB $dbh database handler
         */
        public function get(\PHP_MPM\Database\DB $dbh) {
            $results = null;
            if (! empty($this->id) && mb_strlen($this->id) == 36) {
                $results = $dbh->query(" SELECT id, email, nick, password_hash AS passwordHash, avatar_url AS avatarUrl FROM USER WHERE id = :id ", array(
                    (new \PHP_MPM\Database\DBParam())->str(":id", $this->id)
                ));
            } else if (! empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL) && mb_strlen($this->email) <= 255) {
                $results = $dbh->query(" SELECT id, email, nick, password_hash AS passwordHash, avatar_url AS avatarUrl FROM USER WHERE email = :email ", array(
                    (new \PHP_MPM\Database\DBParam())->str(":email", mb_strtolower($this->email))
                ));
            } else if (! empty($this->nick) && mb_strlen($this->nick) <= 255) {
                $results = $dbh->query(" SELECT id, email, nick, password_hash AS passwordHash, avatar_url AS avatarUrl FROM USER WHERE nick = :nick ", array(
                    (new \PHP_MPM\Database\DBParam())->str(":nick", mb_strtolower($this->nick))
                ));
            } else {
                throw new \PHP_MPM\Exception\InvalidParamsException("id,email,nick");
            }
            if (count($results) == 1) {
                $this->id = $results[0]->id;
                $this->email = $results[0]->email;
                $this->nick = $results[0]->nick;
                $this->passwordHash = $results[0]->passwordHash;
                $this->avatarUrl = $results[0]->avatarUrl;
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
        public function login(\PHP_MPM\Database\DB $dbh): bool {
            if (! empty($this->password)) {
                $this->get($dbh);
                if (password_verify($this->password, $this->passwordHash)) {
                    \PHP_MPM\UserSession::set($this->id, $this->email, $this->nick, $this->avatarUrl);
                    return(true);
                } else {
                    return(false);
                }
            } else {
                throw new \PHP_MPM\Exception\InvalidParamsException("password");
            }
        }

        /**
         * sign out (close session)
         */
        public static function logout(): bool {
            \PHP_MPM\UserSession::clear();
            return(true);
        }

    }

?>