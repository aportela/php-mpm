<?php

    declare(strict_types=1);

    ob_start();

    require __DIR__ . '/../vendor/autoload.php';

    session_start();

    $app = (new \PHP_MPM\App())->get();

    $app->run();

?>