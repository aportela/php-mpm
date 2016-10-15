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

    $t->render(array("is_logged" => User::isAuthenticated(), "name" => \PHP_MPM\User::isAuthenticated() ? $_SESSION["user_name"] : "john doe"));
    ob_flush();
?>