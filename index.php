<?php
    namespace PHP_MPM;

    define("INCLUDE_PATH", __DIR__ . DIRECTORY_SEPARATOR . "include");

    require_once INCLUDE_PATH. "/configuration.php";
    require_once INCLUDE_PATH . "/class.Template.php";

    ob_start();

    $t = new Template("layout.php");

    $t->render(array("name" => "john doe"));
    ob_flush();
?>