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
                    'defaultResultsPage' => $this->get('settings')['common']['defaultResultsPage'],
                    'session' => array(
                        "logged" => \PHP_MPM\UserSession::isLogged(),
                        "user" => \PHP_MPM\UserSession::isLogged() ? array(
                            "id" => \PHP_MPM\UserSession::getUserId(),
                            "name" => \PHP_MPM\UserSession::getName(),
                            "email" => \PHP_MPM\UserSession::getEmail(),
                            "accountType" => \PHP_MPM\UserSession::getAccountType(),
                            "isAdmin" => \PHP_MPM\UserSession::isAdmin()
                        ): array()
                    )
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
                    return $response->withJson(
                        [
                            'session' => array(
                                "logged" => \PHP_MPM\UserSession::isLogged(),
                                "user" => \PHP_MPM\UserSession::isLogged() ? array(
                                    "id" => \PHP_MPM\UserSession::getUserId(),
                                    "name" => \PHP_MPM\UserSession::getName(),
                                    "email" => \PHP_MPM\UserSession::getEmail(),
                                    "accountType" => \PHP_MPM\UserSession::getAccountType(),
                                    "isAdmin" => \PHP_MPM\UserSession::isAdmin()
                                ): array()
                            )
                        ]
                    , 200);
                } else {
                    return $response->withJson(
                        [
                            'session' => array(
                                "logged" => false
                            )
                        ]
                    , 401);
                }
            });

            $this->get('/signout', function (Request $request, Response $response, array $args) {
                \PHP_MPM\User::signOut();
                return $response->withJson(
                    [
                        'session' => array(
                            "logged" => false
                        )
                    ]
                , 200);
            });
        });

        $this->group("/users", function() {

            $this->post('/', function (Request $request, Response $response, array $args) {
                $data = \PHP_MPM\User::search(
                    new \PHP_MPM\Database\DB($this),
                    $request->getParam("actualPage", 1),
                    $request->getParam("resultsPage", $this->get('settings')['common']['defaultResultsPage']),
                    array(
                        "accountType" => $request->getParam("accountType", ""),
                        "email" => $request->getParam("email", ""),
                        "name" => $request->getParam("name", ""),
                    ),
                    $request->getParam("sortBy", ""),
                    $request->getParam("sortOrder", "ASC")
                );
                return $response->withJson([
                    'users' => $data->results,
                    "pagination" => array(
                        'totalResults' => $data->totalResults,
                        'actualPage' => $data->actualPage,
                        'resultsPage' => $data->resultsPage,
                        'totalPages' => $data->totalPages
                    )
                ], 200);
            });

            $this->post('/{id}', function (Request $request, Response $response, array $args) {
                $user = new \PHP_MPM\User($request->getParam("user", null));
                $user->add(new \PHP_MPM\Database\DB($this));
                return $response->withJson([], 200);
            });

            $this->put('/{id}', function (Request $request, Response $response, array $args) {
                $user = new \PHP_MPM\User($request->getParam("user", null));
                $user->update(new \PHP_MPM\Database\DB($this));
                return $response->withJson([], 200);
            });

            $this->delete('/{id}', function (Request $request, Response $response, array $args) {
                $route = $request->getAttribute('route');
                $user = new \PHP_MPM\User();
                $user->id = $route->getArgument("id");
                $user->delete(new \PHP_MPM\Database\DB($this));
                return $response->withJson([], 200);
            });

            $this->get('/{id}', function (Request $request, Response $response, array $args) {
                $route = $request->getAttribute('route');
                $user = new \PHP_MPM\User();
                $user->id = $route->getArgument("id");
                $user->get(new \PHP_MPM\Database\DB($this));
                unset($user->password);
                unset($user->passwordHash);
                return $response->withJson([ 'user' => $user ], 200);
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