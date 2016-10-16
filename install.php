<?php
    namespace PHP_MPM;

    ob_start();

    require_once "include/configuration.php";
    require_once "include/class.Database.php";

    /**
    *   create a bootstrap alert container
    */
    function putAlert(string $type, string $text): string {
        return("<div class=\"alert alert-" . $type . "\" role=\"alert\"><strong>[-]</strong> " . $text . "</div>");                
    }

    function putCollapsible(string $header, string $message) {
        $html = '
        <div class="card card-block">
        <div id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                     <h5 class="card-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        ' . $header . '
                        </a>
                    </h5>
                </div>
                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                    <pre><small>
                    ' . htmlentities($message) . '
                    </code></small>                    
                </div>
            </div>
        </div>
        </div>
        ';
        return($html);
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>php-mpm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css" integrity="2hfp1SzUoho7/TsGGGDaFdsuuDL0LX2hnUp6VkX3CUQ2K4K+xjboZdsXyp4oUHZj" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
        <h1>php-mpm installer</h1>
        <hr />
        <?php
            $errors = false;

            if (PHP_VERSION >= MIN_PHP_VERSION) {
                echo putAlert("success", sprintf("checking php version (>=%0.1f)... ok!", MIN_PHP_VERSION));
            } else {
                echo putAlert("danger", sprintf("error: required php version >= %0.1f", MIN_PHP_VERSION));
                $errors = true;
            }

            if (extension_loaded('pdo_sqlite')) {
                echo putAlert("success", "required sqlite3 extension (pdo_sqlite) found");
            } else {
                echo putAlert("danger", "error: required sqlite3 extension (pdo_sqlite) not found");
                $errors = true;
            }

            if (is_writable(__DIR__)) {
                echo putAlert("success", sprintf("checking write permissions on dir (%s)... ok!", __DIR__));
            } else {
                echo putAlert("danger", sprintf("error: no write permissions on dir (%s)", __DIR__));
                $errors = true;
            }

            if (! file_exists(SQLITE_DATABASE_PATH)) {
                if (! $errors) {
                    $queries = array(
                        " CREATE TABLE [USER] ([id] VARCHAR(36) UNIQUE NOT NULL PRIMARY KEY, [email] VARCHAR(254) UNIQUE NOT NULL, [password] VARCHAR(255) NOT NULL, [name] VARCHAR(32) NOT NULL, [created] TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, [creator] VARCHAR(36) NOT NULL, [type] BOOLEAN DEFAULT '0' NOT NULL, [deleted] TIMESTAMP); ",
                        " CREATE TABLE [GROUP] ([id] VARCHAR(36) NOT NULL PRIMARY KEY, [name] VARCHAR(32) UNIQUE NOT NULL, [description] VARCHAR(128) NULL, [created] TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, [creator] VARCHAR(36) NOT NULL); ",
                        " CREATE TABLE [GROUP_USER] ([group_id] VARCHAR(36) NOT NULL, [user_id] VARCHAR(36) NOT NULL, PRIMARY KEY([group_id], [user_id])); ",
                        " CREATE TABLE [ERROR] ([created] TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL PRIMARY KEY, [class] VARCHAR(64) NOT NULL, [line] INTEGER NOT NULL, [filename] VARCHAR(512) NOT NULL, [code] INTEGER NOT NULL, [message] TEXT, [trace] TEXT, [user_id] VARCHAR(36), [user_agent] TEXT, [user_remote_address] VARCHAR(15)) ",
                        " CREATE TABLE [RECOVER_ACCOUNT] ([created] TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL PRIMARY KEY, [class] VARCHAR(64) NOT NULL, [line] INTEGER NOT NULL, [filename] VARCHAR(512)  NOT NULL, [code] INTEGER  NOT NULL, [trace] VARCHAR(16384) NOT NULL); ",
                        " CREATE TABLE [RECOVER_ACCOUNT_REQUEST] ([created] TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, [token] VARCHAR(255) UNIQUE NOT NULL, [user_id] VARCHAR(36) NOT NULL,PRIMARY KEY ([token],[user_id])) ",
                        " CREATE TABLE [ATTRIBUTE] ([id] VARCHAR(36) UNIQUE NOT NULL PRIMARY KEY, [name] VARCHAR(32) UNIQUE NOT NULL, [description] VARCHAR(128) NULL, [type] INTEGER DEFAULT '0' NOT NULL, [created] TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, [creator] VARCHAR(36) NOT NULL) ",
                        " INSERT INTO [USER] (id, email, password, name, created, creator, type, deleted) VALUES (\"00000000-0000-0000-0000-000000000000\", \"admin@localhost\", \"" . password_hash("password", PASSWORD_BCRYPT, array("cost" => 12)) . "\", \"administrator\", CURRENT_TIMESTAMP, \"00000000-0000-0000-0000-000000000000\", '1', NULL); ",
                        " INSERT INTO [GROUP] (id, name, description, created, creator) VALUES (\"1111111-1111-1111-1111-111111111111\", \"Public\", \"Public (default) common group\", CURRENT_TIMESTAMP, \"00000000-0000-0000-0000-000000000000\"); ",
                        " INSERT INTO [GROUP_USER] (group_id, user_id) VALUES (\"1111111-1111-1111-1111-111111111111\", \"00000000-0000-0000-0000-000000000000\"); ",
                        " INSERT INTO ATTRIBUTE (id, name, description, type, created, creator) VALUES(\"1111111-1111-1111-0000-111111111111\", \"Name\", \"For short (0-255 chars) texts\", 1, CURRENT_TIMESTAMP, \"00000000-0000-0000-0000-000000000000\" ); ",
                        " INSERT INTO ATTRIBUTE (id, name, description, type, created, creator) VALUES(\"1111111-1111-1111-0000-222222222222\", \"Description\", \"For long (memo) texts\", 2, CURRENT_TIMESTAMP, \"00000000-0000-0000-0000-000000000000\" ); ",
                        " INSERT INTO ATTRIBUTE (id, name, description, type, created, creator) VALUES(\"1111111-1111-1111-0000-333333333333\", \"Age\", \"Integer values\", 3, CURRENT_TIMESTAMP, \"00000000-0000-0000-0000-000000000000\" ); ",
                        " INSERT INTO ATTRIBUTE (id, name, description, type, created, creator) VALUES(\"1111111-1111-1111-0000-444444444444\", \"Amount\", \"Decimal values\", 4, CURRENT_TIMESTAMP, \"00000000-0000-0000-0000-000000000000\" ); ",
                        " INSERT INTO ATTRIBUTE (id, name, description, type, created, creator) VALUES(\"1111111-1111-1111-0000-555555555555\", \"Start date\", \"Date values\", 5, CURRENT_TIMESTAMP, \"00000000-0000-0000-0000-000000000000\" ); ",
                        " INSERT INTO ATTRIBUTE (id, name, description, type, created, creator) VALUES(\"1111111-1111-1111-0000-666666666666\", \"Hour\", \"Time values\", 6, CURRENT_TIMESTAMP, \"00000000-0000-0000-0000-000000000000\" ); ",
                        " INSERT INTO ATTRIBUTE (id, name, description, type, created, creator) VALUES(\"1111111-1111-1111-0000-777777777777\", \"Registered on\", \"Date & Time\", 7, CURRENT_TIMESTAMP, \"00000000-0000-0000-0000-000000000000\" ); "                        
                    );
                    $exception = null;                
                    try {
                        foreach($queries as $query) {
                            \PHP_MPM\Database::execWithoutResult($query, array());
                        }
                    } catch (\PDOException $e) {
                        $exception = $e;
                        $errors = true;
                    } catch (\Throwable $e) {
                        $exception = $e;
                        $errors = true;
                    } finally {
                        if (! $errors) {
                            echo putAlert("success", "database creation... ok!");
                        } else {
                            echo putAlert("danger", "error creating database");
                            echo putCollapsible("Exception details", print_r($exception, true));
                        }
                    }
                }
            } else {
                echo putAlert("warning", sprintf("database already created (%s)", SQLITE_DATABASE_PATH));
                $errors = true;
            }
            if (! $errors) {
                echo putAlert("success", "installation successfully");
            } else {
                echo putAlert("danger", "php-mpm was not installed correctly");
            }
        ?> 
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
  </body>
</html>