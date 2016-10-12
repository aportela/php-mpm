<?php
    namespace PHP_MPM;

    define("INCLUDE_PATH", __DIR__ . DIRECTORY_SEPARATOR . "include");

    require_once INCLUDE_PATH. "/configuration.php";
    require_once INCLUDE_PATH . "/class.Template.php";
    require_once INCLUDE_PATH . "/class.User.php";

    ob_start();
    session_start();

    $t = new Template("layout.php");

    $t->render(array("is_logged" => User::isAuthenticated(), "name" => "john doe"));
    ob_flush();
?>