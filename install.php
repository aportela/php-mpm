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
                        " CREATE TABLE [USER] ([id] VARCHAR(36)  UNIQUE NOT NULL PRIMARY KEY, [email] VARCHAR(254)  UNIQUE NOT NULL, [password] VARCHAR(255)  NOT NULL, [created] TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL)"
                    );
                    $exception = null;                
                    try {
                        foreach($queries as $query) {
                            Database::execWithoutResult($query, array());
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