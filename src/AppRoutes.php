<?php
    declare(strict_types=1);

    use Slim\Http\Request;
    use Slim\Http\Response;

    $this->app->get('/', function (Request $request, Response $response, array $args) {
        $this->logger->info($request->getOriginalMethod() . " " . $request->getUri()->getPath());
        $v = new \PHP_MPM\Database\Version(new \PHP_MPM\Database\DB($this), $this->get('settings')['database']['type']);
        return $this->view->render($response, 'index.html.twig', array(
            'settings' => $this->settings["twigParams"],
            'initialState' => json_encode(
                array(
                    'upgradeAvailable' => $v->hasUpgradeAvailable(),
                    'logged' => \PHP_MPM\UserSession::isLogged(),
                    'allowSignUp' => $this->get('settings')['common']['allowSignUp']
                )
            )
        ));
    });

    $this->app->group("/api", function($request) {

        /**
         * user api start
         */
        $this->group("/user", function() {

            $this->post('/signin', function (Request $request, Response $response, array $args) {
                $user = new \PHP_MPM\User($request->getParam("user", null));
                if ($user->signIn(new \PHP_MPM\Database\DB($this))) {
                    return $response->withJson(['logged' => true], 200);
                } else {
                    return $response->withJson(['logged' => false], 401);
                }
            });

            $this->get('/signout', function (Request $request, Response $response, array $args) {
                \PHP_MPM\User::signOut();
                return $response->withJson(['logged' => false], 200);
            });

        });
        /**
         * user api end
         */


    })->add(new \PHP_MPM\Middleware\APIExceptionCatcher($this->app->getContainer()));

    $this->app->get('/api/poll', function (Request $request, Response $response, array $args) {
        return $response->withJson(['success' => true], 200);
    })->add(new \PHP_MPM\Middleware\APIExceptionCatcher($this->app->getContainer()));

?>