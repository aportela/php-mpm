<?php

    declare(strict_types=1);

    namespace PHP_MPM\Middleware;

    class APIAdminPrivilegesRequired {

        private $container;

        public function __construct($container) {
            $this->container = $container;
        }

        /**
         * Example middleware invokable class
         *
         * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
         * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
         * @param  callable                                 $next     Next middleware
         *
         * @return \Psr\Http\Message\ResponseInterface
         */
        public function __invoke($request, $response, $next)
        {
            if (\PHP_MPM\UserSession::isAdmin()) {
                $response = $next($request, $response);
                return $response;
            } else {
                $this->container["apiLogger"]->info("Request api method without administration privileges: " . $request->getOriginalMethod() . " " . $request->getUri()->getPath());
                return $response->withJson([], 403);
            }
        }
    }

?>