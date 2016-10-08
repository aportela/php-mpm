<?php
    namespace PHP_MPM;

    define('VERSION', 0.1);

    define("ENVIRONMENT_DEV", true);
    // WARNING: with DEBUG enabled, PDO exceptions include _complete_ database configuration values (host/user/password...) so enable this only on environment devs with caution
    define("DEBUG", true);

    define("DATABASE_HOST", "localhost");
    define("DATABASE_PORT", 3006);
    define("DATABASE_USERNAME", "");
    define("DATABASE_PASSWORD", "");
    define("DATABASE_NAME", "php-mpm");   
    define("PDO_CONNECTION_STRING", 'mysql:host=' . DATABASE_HOST . ';port=' . DATABASE_PORT . ';dbname=' . DATABASE_NAME);
	define("DATABASE_ENCODING", "utf8_unicode");

    define("CACHE", false);

    define("APP_ROOT_LOCAL_PATH", basename(__DIR__));

    if (ENVIRONMENT_DEV) {
		ini_set('display_errors', 'On');
        ini_set('display_startup_errors', 'On');
        ini_set('log_errors', 'On');
		error_reporting(E_ALL);	
	} else {
		ini_set('display_errors', 'Off');
        ini_set('display_startup_errors', 'Off');
        ini_set('log_errors', 'On');
		error_reporting(E_ALL);	        
    }
?>