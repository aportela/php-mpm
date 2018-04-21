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

    $this->app->get('/api/poll', function (Request $request, Response $response, array $args) {
        return $response->withJson(['success' => true], 200);
    })->add(new \PHP_MPM\Middleware\APIExceptionCatcher($this->app->getContainer()));

?>