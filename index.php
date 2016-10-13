<?php
    namespace PHP_MPM;
   
    require_once __DIR__ . DIRECTORY_SEPARATOR . "include" . "/configuration.php";

    ob_start();
    session_start();

    $t = new \PHP_MPM\Template("layout.php");

    $t->render(array("is_logged" => User::isAuthenticated(), "name" => \PHP_MPM\User::isAuthenticated() ? $_SESSION["user_name"] : "john doe"));
    ob_flush();
?>