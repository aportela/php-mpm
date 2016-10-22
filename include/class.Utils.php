<?php
    namespace PHP_MPM;

    /**
    *   commom functions class
    */
    class Utils {
        /**
        *   generate uuid
        *
        *   (Andrew Moore) http://stackoverflow.com/a/2040279
        */
        public static function uuid() {
            return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

                // 16 bits for "time_mid"
                mt_rand( 0, 0xffff ),

                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand( 0, 0x0fff ) | 0x4000,

                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand( 0, 0x3fff ) | 0x8000,

                // 48 bits for "node"
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
            );
        }

        /**
        *   get remote user ip address
        *
        *   (Tim Kennedy) http://stackoverflow.com/a/55790
        */
        public static function getRemoteIpAddress() {
            $ip = null;
            if (isset($_SERVER)) {
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
            }
            return($ip);            
        }

        /**
        *   get remote user browser user agent
        */
        public static function getBrowserUserAgent() {
            return(isset($_SERVER ['HTTP_USER_AGENT']) ? $_SERVER ['HTTP_USER_AGENT']: null);
        }

        /**
        *   transform/decode json request value into params array
        */
        public static function getRequestParamsFromJSON() {
            $json = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new \PHP_MPM\MPMInvalidParamsException();
            } else {
                return($json);
            }            
        }
    }
?>