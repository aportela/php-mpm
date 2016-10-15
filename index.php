<?php
    namespace PHP_MPM;
   
    require_once __DIR__ . DIRECTORY_SEPARATOR . "include" . "/configuration.php";

    ob_start();

    header('Expires: Thu, 19 Nov 1981 00:00:00 GMT');
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");    

    session_start();

    $t = new \PHP_MPM\Template("layout.php");

    $t->render(array(
        "session_user_is_logged" => User::isAuthenticated(), 
        "session_user_id" => \PHP_MPM\User::getSessionUserId(),
        "session_user_name" => \PHP_MPM\User::getSessionUserName(),
        "session_user_is_admin" => \PHP_MPM\User::isAuthenticatedAsAdmin() ? 1 : 0
    ));
    ob_flush();
?>