<?php

    declare(strict_types=1);

    namespace PHP_MPM;

    class UserSession {

        public static function set(string $userId = "", string $email = "", string $name = "", string $accountType = "U") {
            $_SESSION["userId"] = $userId;
            $_SESSION["email"] = $email;
            $_SESSION["name"] = $name;
            $_SESSION["accounType"] = $accountType;
            $_SESSION["isAdmin"] = $accountType == "A";
        }

        /**
         * clear current user session
         */
        public static function clear() {
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                if (PHP_SAPI != 'cli') {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
                }
            }
            if (session_status() != PHP_SESSION_NONE) {
                session_destroy();
            }
        }

        /**
         * check if user is logged
         */
        public static function isLogged() {
            return(isset($_SESSION["userId"]));
        }

        /**
         * return logged user id
         *
         * @return string userId || null
         */
        public static function getUserId() {
            return(isset($_SESSION["userId"]) ? $_SESSION["userId"]: null);
        }

        /**
         * return logged user email
         *
         * @return string email || null
         */
        public static function getEmail() {
            return(isset($_SESSION["email"]) ? $_SESSION["email"]: null);
        }

        /**
         * return logged user name
         *
         * @return string nick || null
         */
        public static function getName() {
            return(isset($_SESSION["name"]) ? $_SESSION["name"]: null);
        }

        /**
         * return logged user account type
         */
        public static function isAdmin() {
            return(isset($_SESSION["isAdmin"]) ? $_SESSION["isAdmin"]: false);
        }

    }

?>