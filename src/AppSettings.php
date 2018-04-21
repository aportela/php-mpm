<?php
    declare(strict_types=1);

    return [
        'settings' => [
            'displayErrorDetails' => true, // set to false in production
            'addContentLengthHeader' => false, // Allow the web server to send the content-length header
            'twigParams' => [
                'production' => false,
                'localVendorAssets' => true // use local vendor assets (vs remote cdn)
            ],
            'phpRequiredExtensions' => array('pdo_sqlite', 'mbstring', 'curl'),
            // database settings
            'database' => [
                'type' => "PDO_SQLITE", // supported types: PDO_SQLITE | PDO_MARIADB
                'name' => "php-mpm",
                'username' => '',
                'password' => '',
                'host' => 'localhost',
                'port' => 3306,
            ],
            // Renderer settings
            'renderer' => [
                'template_path' => __DIR__ . '/../templates',
            ],
            // Monolog settings
            'logger' => [
                'name' => 'phpmpm-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/default.log',
                'level' => \Monolog\Logger::DEBUG
            ],
            'databaseLogger' => [
                'name' => 'phpmpm-db',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/database.log',
                'level' => \Monolog\Logger::DEBUG
            ],
            'apiLogger' => [
                'name' => 'phpmpm-api',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/api.log',
                'level' => \Monolog\Logger::DEBUG
            ],
            'common' => [
                'allowSignUp' => true
            ]
        ],
    ];
?>